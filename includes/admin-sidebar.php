<?php
// includes/admin-sidebar.php — SIDEBAR ng admin (madilim para kita agad na admin side)
if (!defined('ROOT_PATH')) exit('No direct access');

// route => [label, icon (SVG path)]
$adminLinks = [
    'admindashboard' => ['Dashboard', '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>'],
    'adminprojects'  => ['Projects',  '<path d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>'],
    'adminresume'    => ['Resume',    '<path d="M14 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h6"/>'],
];

$adminCls = 'group flex shrink-0 items-center gap-3 whitespace-nowrap rounded-lg px-3 py-2 text-sm font-semibold text-neutral-400 transition
             hover:bg-white/10 hover:text-white
             lg:py-2.5 lg:text-[15px]
             data-[active=true]:bg-white data-[active=true]:text-neutral-900';

$adminName = $_SESSION['admin_name'] ?? 'Admin';
?>
<aside class="sticky top-0 z-30 flex items-center gap-3 px-3 py-2 bg-neutral-950 text-white
              lg:relative lg:w-60 lg:shrink-0 lg:self-stretch lg:flex-col lg:items-stretch lg:gap-0 lg:p-4">

    <!-- Logo -->
    <a href="<?= url('admindashboard') ?>" class="flex shrink-0 items-center gap-3 lg:px-1 lg:pb-5 lg:mb-4 lg:border-b lg:border-white/10">
        <span class="grid place-items-center size-9 lg:size-10 rounded-lg bg-white text-sm lg:text-base font-bold text-neutral-900">WH</span>
        <span class="hidden lg:block leading-tight">
            <b class="block text-sm font-bold">Admin Panel</b>
            <span class="block text-xs text-neutral-400">Portfolio</span>
        </span>
    </a>

    <!-- Links -->
    <nav class="flex min-w-0 flex-1 gap-1 overflow-x-auto [scrollbar-width:none] lg:flex-none lg:flex-col lg:overflow-visible">
        <p class="hidden lg:block px-3 mb-1 text-[11px] font-semibold uppercase tracking-[.15em] text-neutral-500">Manage</p>
        <?php foreach ($adminLinks as $navRoute => [$label, $icon]): ?>
            <?php $active = ($path ?? '') === $navRoute; ?>
            <a href="<?= url($navRoute) ?>" data-active="<?= $active ? 'true' : 'false' ?>"
               title="<?= e($label) ?>" <?= $active ? 'aria-current="page"' : '' ?> class="<?= $adminCls ?>">
                <svg viewBox="0 0 24 24" class="size-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $icon ?></svg>
                <span class="hidden group-data-[active=true]:inline lg:inline"><?= e($label) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Ibaba: tingnan ang site + user + logout -->
    <div class="flex shrink-0 items-center gap-1 lg:mt-auto lg:flex-col lg:items-stretch lg:gap-2 lg:pt-4 lg:border-t lg:border-white/10">
        <a href="<?= url('home') ?>" target="_blank" rel="noopener" title="View site" class="<?= $adminCls ?>">
            <svg viewBox="0 0 24 24" class="size-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 01-1 1H5a1 1 0 01-1-1V7a1 1 0 011-1h5"/></svg>
            <span class="hidden lg:inline">View site</span>
        </a>

        <div class="hidden lg:flex items-center gap-3 px-3 py-2">
            <span class="grid place-items-center size-8 rounded-full bg-white/10 text-xs font-bold"><?= e(strtoupper(substr($adminName, 0, 1))) ?></span>
            <span class="min-w-0 flex-1 truncate text-sm font-semibold"><?= e($adminName) ?></span>
        </div>

        <a href="<?= url('admin/logout') ?>" title="Logout"
           class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold text-red-400 transition hover:bg-red-500/10 hover:text-red-300">
            <svg viewBox="0 0 24 24" class="size-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 17l5-5-5-5M20 12H9M12 20H5a1 1 0 01-1-1V5a1 1 0 011-1h7"/></svg>
            <span class="hidden lg:inline">Logout</span>
        </a>
    </div>
</aside>