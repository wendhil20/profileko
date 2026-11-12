<?php
// pages/404.php
if (!defined('ROOT_PATH')) exit('No direct access');
?>
<section class="max-w-5xl mx-auto px-4 py-24 text-center">
    <p class="text-7xl font-bold text-slate-300">404</p>
    <h1 class="text-2xl font-semibold mt-4 mb-2">Page not found</h1>
    <p class="text-slate-600 mb-6">Walang page na ganito.</p>
    <a href="<?= url('home') ?>" class="px-5 py-2.5 rounded-lg bg-slate-900 text-white hover:bg-slate-700">Back to Home</a>
</section>