<?php
// pages/admin/dashboard.php — panimulang admin page
if (!defined('ROOT_PATH')) exit('No direct access');
?>
<div class="p-5 lg:p-8">
    <h1 class="text-3xl font-bold">Dashboard</h1>
    <p class="mt-1 text-muted">Welcome, <?= e(admin_name()) ?>.</p>

    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <a href="<?= url('admin/projects') ?>" class="block p-5 rounded-lg border border-neutral-200 bg-white transition hover:border-neutral-900">
            <b class="block text-lg">Projects</b>
            <span class="text-sm text-muted">Magdagdag o mag-edit ng projects</span>
        </a>
    </div>
</div>