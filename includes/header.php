<?php
// includes/header.php — SIDEBAR (kasama sa lahat ng page)
// Desktop: laging kita sa kaliwa.  Mobile: nakatago; lumalabas mula sa kaliwa kapag pinindot ang menu button.
// Laman: mga bagay na kailangan sa BAWAT page (nav, status, CV, socials) — hindi ang laman ng profile.
if (!defined('ROOT_PATH')) exit('No direct access');

// route => [label, icon (SVG path)]
$navLinks = [
    'home'     => ['Overview',  '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0116 0"/>'],
    'resume'   => ['CV',    '<path d="M14 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h6"/>'],
    'projects' => ['Experience Project',      '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>'],
    'contact'  => ['Reach Out', '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>'],
];

// Social links — palitan ng totoong link mo
$socials = [
    ['GitHub',   'https://github.com/' . env('GITHUB_USER', ''), '<path d="M9 19c-4.3 1.4-4.3-2.5-6-3m12 5v-3.5c0-1 .1-1.4-.5-2 2.8-.3 5.5-1.4 5.5-6a4.6 4.6 0 00-1.3-3.2 4.2 4.2 0 00-.1-3.2s-1.1-.3-3.5 1.3a12.3 12.3 0 00-6.2 0C6.5 2.8 5.4 3.1 5.4 3.1a4.2 4.2 0 00-.1 3.2A4.6 4.6 0 004 9.5c0 4.6 2.7 5.7 5.5 6-.6.6-.6 1.2-.5 2V21"/>'],
    ['LinkedIn', 'https://www.linkedin.com/in/username',         '<path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-4 0v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/>'],
    ['Email',    'mailto:wendhil10@gmail.com',                   '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>'],
];

// Class ng link — active state gamit ang data-active="true"
$navCls = 'group relative flex shrink-0 items-center gap-2.5 whitespace-nowrap rounded-lg px-2 py-2 text-[15px] font-semibold text-neutral-500 transition
           hover:bg-neutral-900/5 hover:text-neutral-900
           lg:py-1.5
           data-[active=true]:bg-white data-[active=true]:text-neutral-900
           data-[active=true]:shadow-[0_1px_2px_rgba(0,0,0,.06),0_0_0_1px_rgba(0,0,0,.06)]';
?>
<?php $currentLabel = $navLinks[$path ?? ''][0] ?? ''; ?>

<!-- ============ MOBILE TOP BAR: menu button + logo + CV ============ -->
<div class="sticky top-0 z-30 flex items-center gap-3 border-b border-neutral-900/10 bg-white/80 px-3 py-2 backdrop-blur-md lg:hidden">
    <button type="button" id="sideOpen" aria-label="Open menu" aria-controls="sidebar" aria-expanded="false"
            class="grid size-9 shrink-0 place-items-center rounded-lg border border-neutral-200 bg-white text-neutral-900 transition hover:border-neutral-900 cursor-pointer">
        <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h10"/></svg>
    </button>

    <a href="<?= url('home') ?>" title="Home" class="flex shrink-0 items-center rounded-lg bg-neutral-900 px-2.5 py-1.5">
        <img src="<?= img('logo.png') ?>" alt="Wendhil Himarangan" class="h-6 w-auto max-w-[96px] object-contain">
    </a>

    <!-- pangalan ng kasalukuyang page -->
    <span class="min-w-0 truncate text-sm font-semibold text-neutral-900"><?= e($currentLabel) ?></span>

    <a href="<?= url('resume/download') ?>" aria-label="Download CV"
       class="ml-auto grid size-9 shrink-0 place-items-center rounded-lg bg-neutral-900 text-white">
        <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v3h16v-3"/></svg>
    </a>
</div>

<!-- Madilim na takip sa likod ng sidebar (mobile) — pindutin para isara -->
<div id="sideBackdrop" data-open="false"
     class="fixed inset-0 z-40 bg-neutral-950/50 opacity-0 pointer-events-none transition-opacity duration-300
            data-[open=true]:opacity-100 data-[open=true]:pointer-events-auto lg:hidden"></div>

<!-- ============ SIDEBAR ============ -->
<!-- Mobile: fixed sa kaliwa, nakatago (-translate-x-full) hanggang data-open="true" -->
<aside id="sidebar" data-open="false"
       class="fixed inset-y-0 left-0 z-50 flex w-[264px] max-w-[85vw] flex-col overflow-y-auto bg-white p-3 shadow-2xl
              invisible -translate-x-full transition-all duration-300 ease-out
              data-[open=true]:visible data-[open=true]:translate-x-0
              lg:visible lg:relative lg:inset-auto lg:z-30 lg:m-3 lg:mr-0 lg:w-[232px] lg:max-w-none lg:shrink-0 lg:translate-x-0 lg:self-stretch lg:overflow-visible lg:transition-none
              lg:rounded-2xl lg:border lg:border-neutral-900/10 lg:bg-white/60 lg:backdrop-blur-xl
              lg:shadow-[0_10px_40px_-12px_rgba(0,0,0,.12)]">

    <!-- Logo (assets/img/logo.png) sa itim na background + close button (mobile) -->
    <div class="mb-4 flex shrink-0 items-center gap-2">
    <a href="<?= url('home') ?>" title="Home" class="flex min-w-0 flex-1 items-center">
        <span class="relative flex w-full items-center justify-center overflow-hidden rounded-xl bg-neutral-900 px-4 py-3 transition hover:bg-neutral-800
                     lg:py-4">
            <!-- manipis na grid sa likod ng logo -->
            <span class="pointer-events-none absolute inset-0 bg-[linear-gradient(rgba(255,255,255,.07)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,.07)_1px,transparent_1px)] bg-size-[16px_16px]
                         [mask-image:radial-gradient(ellipse_at_center,#000_10%,transparent_75%)]" aria-hidden="true"></span>
            <img src="<?= img('logo.png') ?>" alt="Wendhil Himarangan"
                 class="relative h-8 w-auto max-w-[120px] object-contain lg:h-10 lg:max-w-[160px]">
        </span>
    </a>
        <button type="button" id="sideClose" aria-label="Close menu"
                class="grid size-10 shrink-0 place-items-center rounded-lg border border-neutral-200 text-neutral-900 transition hover:border-neutral-900 cursor-pointer lg:hidden">
            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
        </button>
    </div>

    <!-- Links -->
    <nav id="sideNav" class="flex flex-col gap-1">
        <p class="px-2 mb-1 text-[11px] font-semibold uppercase tracking-[.16em] text-neutral-400">Navigate</p>
        <?php foreach ($navLinks as $navRoute => [$label, $icon]): ?>
            <?php $active = ($path ?? '') === $navRoute; ?>
            <a href="<?= url($navRoute) ?>" data-active="<?= $active ? 'true' : 'false' ?>"
               <?= $active ? 'aria-current="page"' : '' ?> class="<?= $navCls ?>">
                <!-- icon sa maliit na tile -->
                <span class="grid size-7 shrink-0 place-items-center rounded-md transition
                             group-data-[active=true]:bg-neutral-900 group-data-[active=true]:text-white">
                    <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $icon ?></svg>
                </span>
                <span><?= e($label) ?></span>
                <!-- tuldok sa kanan ng naka-active na link -->
                <span class="ml-auto mr-1.5 hidden size-1.5 rounded-full bg-neutral-900 group-data-[active=true]:block"></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Ibaba: status card, CV, socials -->
    <div class="mt-auto flex flex-col gap-2.5 pt-4">

        <!-- Status card -->
        <div class="rounded-xl border border-neutral-900/10 bg-white/70 p-3">
            <p class="flex items-center gap-2 text-sm font-semibold text-neutral-900">
                <span class="relative flex size-2 shrink-0">
                    <span class="absolute inset-0 rounded-full bg-emerald-500 animate-ping opacity-60"></span>
                    <span class="relative size-2 rounded-full bg-emerald-500"></span>
                </span>
                Open for opportunities
            </p>

            <!-- Download CV -->
            <a href="<?= url('resume/download') ?>"
               class="group/cv mt-3 flex items-center justify-center gap-2 rounded-lg bg-neutral-900 px-3 py-2 text-sm font-semibold text-white transition hover:bg-neutral-700">
                <svg viewBox="0 0 24 24" class="size-4 transition group-hover/cv:translate-y-0.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v3h16v-3"/></svg>
                Download CV
            </a>
        </div>

        <!-- Socials -->
        <div class="flex items-center justify-between px-1">
            <span class="text-[11px] text-neutral-400">© <?= date('Y') ?></span>
            <div class="flex items-center gap-0.5">
                <?php foreach ($socials as [$name, $href, $icon]): ?>
                    <a href="<?= e($href) ?>" <?= str_starts_with($href, 'http') ? 'target="_blank" rel="noopener"' : '' ?>
                       aria-label="<?= e($name) ?>" title="<?= e($name) ?>"
                       class="grid size-8 place-items-center rounded-lg text-neutral-400 transition hover:bg-neutral-900/5 hover:text-neutral-900">
                        <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $icon ?></svg>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</aside>

<script>
// Mobile sidebar: menu button → buksan; X, takip, o Esc → isara (data-open lang ang binabago)
(() => {
    const side     = document.getElementById('sidebar');
    const backdrop = document.getElementById('sideBackdrop');
    const openBtn  = document.getElementById('sideOpen');
    if (!side || !openBtn) return;

    function setOpen(open) {
        side.dataset.open = backdrop.dataset.open = String(open);
        openBtn.setAttribute('aria-expanded', String(open));
        document.body.classList.toggle('overflow-hidden', open);   // huwag mag-scroll ang page sa likod
    }

    openBtn.addEventListener('click', () => setOpen(true));
    document.getElementById('sideClose').addEventListener('click', () => setOpen(false));
    backdrop.addEventListener('click', () => setOpen(false));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') setOpen(false); });

    // Kapag pinalaki ang screen habang bukas, isara para hindi maiwan ang body na naka-lock
    matchMedia('(min-width: 1024px)').addEventListener('change', e => { if (e.matches) setOpen(false); });
})();
</script>