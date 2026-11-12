<?php
// pages/user/contact.php — Contact page (walang form; diretsong email at mga link)
if (!defined('ROOT_PATH')) exit('No direct access');

/* =========================================================
 |  DATA — dito mo papalitan ang laman
 * ========================================================= */
$email      = 'wendhil10@gmail.com';
$githubUser = env('GITHUB_USER', '');

// Mga paraan para makontak ka — [pangalan, nakasulat, link, icon (SVG path)]
$channels = [
    ['Email',    $email,                       'mailto:' . $email,                     '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>'],
    ['LinkedIn', 'linkedin.com/in/username',   'https://www.linkedin.com/in/username', '<path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-4 0v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/>'],
    ['GitHub',   'github.com/' . $githubUser,  'https://github.com/' . $githubUser,    '<path d="M9 19c-4.3 1.4-4.3-2.5-6-3m12 5v-3.5c0-1 .1-1.4-.5-2 2.8-.3 5.5-1.4 5.5-6a4.6 4.6 0 00-1.3-3.2 4.2 4.2 0 00-.1-3.2s-1.1-.3-3.5 1.3a12.3 12.3 0 00-6.2 0C6.5 2.8 5.4 3.1 5.4 3.1a4.2 4.2 0 00-.1 3.2A4.6 4.6 0 004 9.5c0 4.6 2.7 5.7 5.5 6-.6.6-.6 1.2-.5 2V21"/>'],
    ['Resume',   'Download my CV',             url('resume'),                          '<path d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v3h16v-3"/>'],
];
?>
<header class="px-5 pt-4 pb-2">
    <h1 class="<?= $cls['title'] ?>">Contact</h1>
</header>

<div class="px-4 pb-6">
    <section class="<?= $cls['panel'] ?> grid max-w-5xl gap-8 p-6 sm:p-8 lg:grid-cols-[minmax(0,1.1fr)_minmax(0,.9fr)] lg:gap-10">

        <!-- ============ KALIWA: paanyaya + email ============ -->
        <div class="flex flex-col">
            <!-- Status -->
            <p class="inline-flex items-center gap-2 self-start rounded-full border border-neutral-200 bg-white px-3 py-1 text-sm font-semibold text-neutral-900">
                <span class="relative flex size-2 shrink-0">
                    <span class="absolute inset-0 rounded-full bg-emerald-500 opacity-60 animate-ping"></span>
                    <span class="relative size-2 rounded-full bg-emerald-500"></span>
                </span>
                Open for opportunities
            </p>

            <h2 class="mt-5 text-4xl font-bold leading-[1.05] tracking-tight text-neutral-900 sm:text-5xl">
                Let&rsquo;s work<br>together.
            </h2>
            <p class="mt-4 max-w-md text-lg leading-relaxed text-muted">
                Have a role, a project, or a question about my work? Send me an email and I&rsquo;ll get back to you.
            </p>

            <!-- Email + Copy -->
            <div class="mt-8">
                <p class="mb-1.5 text-xs font-semibold tracking-[.2em] uppercase text-neutral-400">Email me at</p>
                <div class="flex items-center gap-2 rounded-xl border border-neutral-200 bg-neutral-50 p-1.5 pl-4">
                    <span class="min-w-0 flex-1 truncate text-base font-semibold text-neutral-900 sm:text-lg"><?= e($email) ?></span>
                    <button type="button" id="copyEmail" data-email="<?= e($email) ?>" data-copied="false"
                            class="group inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm font-semibold text-neutral-900 transition
                                   hover:border-neutral-900 cursor-pointer data-[copied=true]:border-neutral-900 data-[copied=true]:bg-neutral-900 data-[copied=true]:text-white">
                        <!-- icon: copy / check -->
                        <svg viewBox="0 0 24 24" class="size-4 group-data-[copied=true]:hidden" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15V5a2 2 0 012-2h10"/></svg>
                        <svg viewBox="0 0 24 24" class="hidden size-4 group-data-[copied=true]:block" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5L20 7"/></svg>
                        <span class="group-data-[copied=true]:hidden">Copy</span>
                        <span class="hidden group-data-[copied=true]:inline">Copied</span>
                    </button>
                </div>

                <a href="mailto:<?= e($email) ?>" class="<?= $cls['btn'] ?> mt-3 w-full sm:w-auto">
                    <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                    Send an email
                </a>
            </div>
        </div>

        <!-- ============ KANAN: mga link ============ -->
        <div>
            <p class="mb-3 text-xs font-semibold tracking-[.2em] uppercase text-neutral-400">Find me on</p>
            <ul class="divide-y divide-neutral-200 overflow-hidden rounded-xl border border-neutral-200">
                <?php foreach ($channels as [$name, $text, $href, $icon]): ?>
                    <li>
                        <a href="<?= e($href) ?>" <?= str_starts_with($href, 'https://') ? 'target="_blank" rel="noopener"' : '' ?>
                           class="group flex items-center gap-4 bg-white px-4 py-4 transition hover:bg-neutral-50">
                            <span class="grid size-11 shrink-0 place-items-center rounded-lg bg-neutral-100 text-neutral-900 transition group-hover:bg-neutral-900 group-hover:text-white">
                                <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $icon ?></svg>
                            </span>
                            <span class="min-w-0 flex-1">
                                <b class="block font-bold leading-tight text-neutral-900"><?= e($name) ?></b>
                                <span class="block truncate text-sm text-muted"><?= e($text) ?></span>
                            </span>
                            <svg viewBox="0 0 24 24" class="size-4 shrink-0 text-neutral-400 transition group-hover:translate-x-1 group-hover:text-neutral-900" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
</div>

<script>
// Copy button: kinokopya ang email, tapos "Copied" ng 2 segundo (data-copied lang ang binabago)
(() => {
    const btn = document.getElementById('copyEmail');
    if (!btn) return;

    btn.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(btn.dataset.email);
        } catch (e) {
            // lumang browser o walang pahintulot: piliin na lang ang text para makopya nang mano-mano
            const range = document.createRange();
            range.selectNodeContents(btn.previousElementSibling);
            getSelection().removeAllRanges();
            getSelection().addRange(range);
            return;
        }
        btn.dataset.copied = 'true';
        setTimeout(() => btn.dataset.copied = 'false', 2000);
    });
})();
</script>