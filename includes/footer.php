<?php
// includes/footer.php — footer ng mga public page (hindi kasama sa home at admin)
if (!defined('ROOT_PATH')) exit('No direct access');

$owner = 'Wendhil Himarangan';
?>
<footer class="mt-auto border-t border-neutral-200 bg-white">
    <div class="flex flex-col items-center justify-between gap-2 px-5 py-4 text-sm text-neutral-500 sm:flex-row">

        <!-- Kaliwa: copyright -->
        <p>
            &copy; <?= date('Y') ?>
            <span class="font-semibold text-neutral-900"><?= e($owner) ?></span>. All rights reserved.
        </p>

        <!-- Kanan: gamit sa paggawa + balik sa itaas -->
        <div class="flex items-center gap-4">
            <p class="flex items-center gap-1.5">
                Built with
                <span class="font-semibold text-neutral-900">PHP</span>
                <span class="size-1 rounded-full bg-neutral-300"></span>
                <span class="font-semibold text-neutral-900">Tailwind CSS</span>
                <span class="size-1 rounded-full bg-neutral-300"></span>
                <span class="font-semibold text-neutral-900">JavaScript</span>
            </p>

       
        </div>
    </div>
</footer>