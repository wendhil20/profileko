<?php
// pages/admin/auth/loginadmin.php — admin login (route: loginadmin, 'bare' => true)
// Accounts live in the portaccount table (create them with database/create-admin.php).
// session_start() is already called in portfolio.php.
if (!defined('ROOT_PATH')) exit('No direct access');

// Already logged in → go straight to the dashboard
if (is_admin()) {
    redirect(ADMIN_HOME_ROUTE);
}

/* ---------- Failed-attempt limit: 5 tries → 15-minute lock (per IP) ---------- */
const LOGIN_MAX_TRIES = 5;
const LOGIN_LOCK_SECS = 900;

$attemptFile = ROOT_PATH . 'storage/cache/login_attempts.json';
$ipKey       = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? 'unknown');

$loadAttempts = function () use ($attemptFile): array {
    if (!is_file($attemptFile)) return [];
    $data = json_decode((string) file_get_contents($attemptFile), true);
    return is_array($data) ? $data : [];
};
$saveAttempts = function (array $data) use ($attemptFile): void {
    $now = time();
    // drop expired entries
    $data = array_filter($data, fn($a) => ($a['last'] ?? 0) > $now - LOGIN_LOCK_SECS);
    @file_put_contents($attemptFile, json_encode($data), LOCK_EX);
};

$attempts = $loadAttempts();
$mine     = $attempts[$ipKey] ?? ['count' => 0, 'last' => 0];
if ($mine['last'] < time() - LOGIN_LOCK_SECS) {
    $mine = ['count' => 0, 'last' => 0];   // lock has expired
}
$lockedFor = $mine['count'] >= LOGIN_MAX_TRIES ? LOGIN_LOCK_SECS - (time() - $mine['last']) : 0;

$errors   = [];     // per field: ['username' => '...', 'password' => '...']
$formErr  = '';     // general error shown above the form
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!csrf_check()) {
        $formErr = 'This form has expired. Please refresh the page and try again.';
    } elseif ($lockedFor > 0) {
        $formErr = 'Too many failed attempts. Try again in ' . ceil($lockedFor / 60) . ' minute(s).';
    } else {
        // Validation
        if ($username === '') {
            $errors['username'] = 'Please enter your username.';
        } elseif (strlen($username) > 50) {
            $errors['username'] = 'Username is too long.';
        }
        if ($password === '') {
            $errors['password'] = 'Please enter your password.';
        } elseif (strlen($password) > 200) {
            $errors['password'] = 'Password is too long.';
        }

        if (!$errors) {
            $stmt = db()->prepare('SELECT id, name, password_hash, is_active FROM portaccount WHERE username = ? LIMIT 1');
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $acct = $stmt->get_result()->fetch_assoc();

            // Always run password_verify (even with no account) so response time stays the same
            $hash = $acct['password_hash'] ?? '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
            $ok   = password_verify($password, $hash) && $acct && (int) $acct['is_active'] === 1;

            if ($ok) {
                // Rehash if the algorithm is outdated, and record the last login
                if (password_needs_rehash($acct['password_hash'], PASSWORD_DEFAULT)) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $up = db()->prepare('UPDATE portaccount SET password_hash = ? WHERE id = ?');
                    $up->bind_param('si', $newHash, $acct['id']);
                    $up->execute();
                }
                $up = db()->prepare('UPDATE portaccount SET last_login_at = NOW() WHERE id = ?');
                $up->bind_param('i', $acct['id']);
                $up->execute();

                unset($attempts[$ipKey]);
                $saveAttempts($attempts);

                admin_login((int) $acct['id'], $acct['name']);
                redirect(ADMIN_HOME_ROUTE);
            }

            // Wrong credentials — record the attempt
            $mine = ['count' => $mine['count'] + 1, 'last' => time()];
            $attempts[$ipKey] = $mine;
            $saveAttempts($attempts);

            $left = LOGIN_MAX_TRIES - $mine['count'];
            $formErr = $left > 0
                ? 'Incorrect username or password. ' . $left . ' attempt' . ($left === 1 ? '' : 's') . ' left.'
                : 'Too many failed attempts. Login is locked for 15 minutes.';
            if ($left <= 0) $lockedFor = LOGIN_LOCK_SECS;
        }
    }
}


// Still locked (even on a fresh page load) → show the message right away
if ($lockedFor > 0 && $formErr === '') {
    $formErr = 'Too many failed attempts. Try again in ' . ceil($lockedFor / 60) . ' minute(s).';
}

$fieldCls = 'peer h-12 w-full rounded-xl border border-neutral-200 bg-neutral-50 pl-11 pr-4 text-[15px] text-neutral-900 outline-none transition
             placeholder:text-neutral-400 hover:border-neutral-300
             focus:border-neutral-900 focus:bg-white focus:ring-4 focus:ring-neutral-900/5
             aria-[invalid=true]:border-red-400 aria-[invalid=true]:bg-red-50/40 aria-[invalid=true]:focus:ring-red-500/10';
$iconCls  = 'pointer-events-none absolute left-4 top-1/2 size-[18px] -translate-y-1/2 text-neutral-400 transition peer-focus:text-neutral-900';
?>

<div class="grid flex-1 lg:grid-cols-[1.05fr_1fr]">

    <!-- ============ LEFT: branding (desktop only) ============ -->
    <aside class="relative hidden overflow-hidden bg-neutral-950 text-white lg:flex lg:flex-col lg:justify-between lg:p-12">
        <!-- grid lines + glow -->
        <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(rgba(255,255,255,.06)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,.06)_1px,transparent_1px)] bg-size-[48px_48px]
                    [mask-image:radial-gradient(ellipse_at_30%_40%,#000_10%,transparent_70%)]" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-40 -right-40 size-[480px] rounded-full bg-white/5 blur-3xl" aria-hidden="true"></div>

        <a href="<?= url('home') ?>" class="relative flex items-center gap-3">
            <span class="grid size-10 place-items-center rounded-lg bg-white text-base font-bold text-neutral-900">WH</span>
            <span class="leading-tight">
                <b class="block font-bold">Wendhil Himarangan</b>
                <span class="block text-sm text-neutral-400">Portfolio · Admin</span>
            </span>
        </a>

        <div class="relative max-w-md">
            <p class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-[.2em] text-neutral-300">
                <span class="size-1.5 rounded-full bg-emerald-400"></span> Restricted area
            </p>
            <h2 class="text-5xl font-bold leading-[1.05] tracking-tight">Manage your<br>portfolio.</h2>
            <p class="mt-4 text-lg text-neutral-400">Projects, profile and everything else on the site — all in one place.</p>

            <!-- small terminal -->
            <div class="mt-10 rounded-xl border border-white/10 bg-white/[.03] p-4 font-mono text-[13px] leading-relaxed backdrop-blur">
                <div class="mb-3 flex gap-1.5" aria-hidden="true">
                    <span class="size-2.5 rounded-full bg-white/20"></span><span class="size-2.5 rounded-full bg-white/20"></span><span class="size-2.5 rounded-full bg-white/20"></span>
                </div>
                <p><span class="text-neutral-500">$</span> sudo portfolio <span class="text-neutral-500">--admin</span></p>
                <p class="text-neutral-400">› Waiting for authentication<span class="animate-pulse">_</span></p>
            </div>
        </div>

        <p class="relative text-sm text-neutral-500">© <?= date('Y') ?> Wendhil Himarangan</p>
    </aside>

    <!-- ============ RIGHT: form ============ -->
    <section class="flex items-center justify-center bg-white px-5 py-12 sm:px-8">
        <div class="w-full max-w-[380px]">

            <!-- Logo (mobile only) -->
            <a href="<?= url('home') ?>" class="mb-10 inline-flex items-center gap-3 lg:hidden">
                <span class="grid size-10 place-items-center rounded-lg bg-neutral-900 text-base font-bold text-white">WH</span>
                <span class="text-sm font-semibold text-neutral-500">Portfolio · Admin</span>
            </a>

            <h1 class="text-3xl font-bold tracking-tight">Welcome back</h1>
            <p class="mt-1 text-neutral-500">Sign in to continue.</p>

            <form id="loginForm" method="post" action="<?= url(ADMIN_LOGIN_ROUTE) ?>" novalidate class="mt-8">
                <?= csrf_field() ?>

                <!-- General error (no alert) -->
                <div id="formErr" role="alert" data-locked="<?= (int) max(0, $lockedFor) ?>"
                     class="<?= $formErr ? 'flex' : 'hidden' ?> mb-6 items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-3.5 text-sm text-red-700">
                    <span class="grid size-7 shrink-0 place-items-center rounded-lg bg-red-100">
                        <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.2"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 8v5M12 16.5h.01"/><circle cx="12" cy="12" r="9"/></svg>
                    </span>
                    <span id="formErrText" class="pt-1 leading-snug"><?= e($formErr) ?></span>
                </div>

                <!-- Username -->
                <div class="mb-5">
                    <label for="username" class="mb-2 block text-sm font-semibold text-neutral-700">Username</label>
                    <div class="relative">
                        <input id="username" name="username" type="text" autocomplete="username" maxlength="50"
                               value="<?= e($username) ?>" placeholder="e.g. wendhil"
                               aria-invalid="<?= isset($errors['username']) ? 'true' : 'false' ?>" aria-describedby="usernameErr"
                               <?= $username === '' ? 'autofocus' : '' ?> class="<?= $fieldCls ?>">
                        <svg viewBox="0 0 24 24" class="<?= $iconCls ?>" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0116 0"/></svg>
                    </div>
                    <p id="usernameErr" class="mt-2 flex items-center gap-1.5 text-sm text-red-600 <?= isset($errors['username']) ? '' : 'hidden' ?>"><?= e($errors['username'] ?? '') ?></p>
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="mb-2 block text-sm font-semibold text-neutral-700">Password</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" autocomplete="current-password" maxlength="200"
                               placeholder="••••••••"
                               aria-invalid="<?= isset($errors['password']) ? 'true' : 'false' ?>" aria-describedby="passwordErr"
                               <?= $username !== '' ? 'autofocus' : '' ?> class="<?= $fieldCls ?> pr-12">
                        <svg viewBox="0 0 24 24" class="<?= $iconCls ?>" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 018 0v3"/></svg>
                        <button type="button" id="togglePw" aria-label="Show password" aria-pressed="false"
                                class="absolute right-1.5 top-1/2 grid size-9 -translate-y-1/2 place-items-center rounded-lg text-neutral-400 transition hover:bg-neutral-100 hover:text-neutral-900 cursor-pointer">
                            <svg id="eyeOn" viewBox="0 0 24 24" class="size-[18px]" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg id="eyeOff" viewBox="0 0 24 24" class="hidden size-[18px]" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3l18 18M10.6 5.1A10 10 0 0112 5c6.5 0 10 7 10 7a17 17 0 01-3.2 4.2M6.6 6.6C3.8 8.4 2 12 2 12s3.5 7 10 7a9.7 9.7 0 005.4-1.6M9.9 9.9a3 3 0 004.2 4.2"/></svg>
                        </button>
                    </div>
                    <p id="passwordErr" class="mt-2 text-sm text-red-600 <?= isset($errors['password']) ? '' : 'hidden' ?>"><?= e($errors['password'] ?? '') ?></p>
                    <p id="capsWarn" class="mt-2 hidden items-center gap-1.5 text-sm font-medium text-amber-600">
                        <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4l8 9h-4v7H8v-7H4z"/></svg>
                        Caps Lock is on
                    </p>
                </div>

                <button type="submit" id="loginBtn" <?= $lockedFor > 0 ? 'disabled' : '' ?>
                        class="group flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-neutral-900 px-4 font-semibold text-white shadow-lg shadow-neutral-900/10 transition
                               hover:bg-neutral-800 active:scale-[.99] disabled:cursor-not-allowed disabled:opacity-50 disabled:shadow-none cursor-pointer">
                    <svg id="loginSpin" viewBox="0 0 24 24" class="hidden size-4 animate-spin" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity=".25"/>
                        <path d="M21 12a9 9 0 00-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                    <span id="loginText">Sign in</span>
                    <svg id="loginArrow" viewBox="0 0 24 24" class="size-4 transition group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </button>
            </form>

            <div class="mt-8 flex items-center gap-3 text-sm text-neutral-400">
                <span class="h-px flex-1 bg-neutral-200"></span>
                <a href="<?= url('home') ?>" class="inline-flex items-center gap-1.5 font-medium text-neutral-500 transition hover:text-neutral-900">
                    <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
                    Back to portfolio
                </a>
                <span class="h-px flex-1 bg-neutral-200"></span>
            </div>
        </div>
    </section>
</div>

<script>
(() => {
    const form = document.getElementById('loginForm');
    const fields = {
        username: { el: document.getElementById('username'), err: document.getElementById('usernameErr'),
                    check: v => v.trim() === '' ? 'Please enter your username.' : '' },
        password: { el: document.getElementById('password'), err: document.getElementById('passwordErr'),
                    check: v => v === '' ? 'Please enter your password.' : '' },
    };

    // Show / hide the error under a field (no alert)
    const setError = (f, msg) => {
        f.err.textContent = msg;
        f.err.classList.toggle('hidden', !msg);
        f.el.setAttribute('aria-invalid', msg ? 'true' : 'false');
    };

    // While typing: clear the error once it's fixed
    Object.values(fields).forEach(f => {
        f.el.addEventListener('input', () => {
            if (f.el.getAttribute('aria-invalid') === 'true') setError(f, f.check(f.el.value));
        });
        f.el.addEventListener('blur', () => { if (f.el.value !== '') setError(f, f.check(f.el.value)); });
    });

    form.addEventListener('submit', e => {
        let first = null;
        Object.values(fields).forEach(f => {
            const msg = f.check(f.el.value);
            setError(f, msg);
            if (msg && !first) first = f.el;
        });
        if (first) { e.preventDefault(); first.focus(); return; }

        // Loading state — prevents double submit
        document.getElementById('loginBtn').disabled = true;
        document.getElementById('loginSpin').classList.remove('hidden');
        document.getElementById('loginArrow').classList.add('hidden');
        document.getElementById('loginText').textContent = 'Signing in…';
    });

    // Show / hide password
    const pw = fields.password.el, toggle = document.getElementById('togglePw');
    toggle.addEventListener('click', () => {
        const show = pw.type === 'password';
        pw.type = show ? 'text' : 'password';
        toggle.setAttribute('aria-pressed', String(show));
        toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        document.getElementById('eyeOn').classList.toggle('hidden', show);
        document.getElementById('eyeOff').classList.toggle('hidden', !show);
        pw.focus();
    });

    // Caps Lock warning
    const caps = document.getElementById('capsWarn');
    const capsShow = on => { caps.classList.toggle('hidden', !on); caps.classList.toggle('flex', on); };
    pw.addEventListener('keyup', e => capsShow(!!e.getModifierState?.('CapsLock')));
    pw.addEventListener('blur', () => capsShow(false));

    // Locked: live countdown, then re-enable the button
    const banner = document.getElementById('formErr');
    let left = +banner.dataset.locked || 0;
    if (left > 0) {
        const btn = document.getElementById('loginBtn'), text = document.getElementById('formErrText');
        const tick = () => {
            if (left <= 0) {
                btn.disabled = false;
                banner.classList.replace('flex', 'hidden');
                return;
            }
            const m = Math.floor(left / 60), s = String(left % 60).padStart(2, '0');
            text.textContent = `Too many failed attempts. Try again in ${m}:${s}.`;
            left--;
            setTimeout(tick, 1000);
        };
        tick();
    }
})();
</script>