<?php
// pages/admin/page-2/partials/image-uploader.php — pang-upload ng maraming larawan (Add at Edit)
//
// Gamit:
//   <?php $uploadError = $errors['images'] ?? ''; include ROOT_PATH . 'pages/admin/page-2/partials/image-uploader.php'; ? >
//
// Kailangan: $maxImages, $maxMb (galing sa controller), $uploadError
if (!defined('ROOT_PATH')) exit('No direct access');
?>
<div data-uploader data-max="<?= (int) $maxImages ?>" data-max-mb="<?= (int) $maxMb ?>">
    <label for="images"
           class="group relative flex min-h-36 cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed px-4 py-6 text-center text-sm text-neutral-500 transition
                  <?= $uploadError ? 'border-red-400 bg-red-50/40' : 'border-neutral-300 bg-neutral-50 hover:border-neutral-900' ?>">
        <svg viewBox="0 0 24 24" class="size-8 text-neutral-400 transition group-hover:text-neutral-900" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="M21 15l-5-5L5 21"/></svg>
        <span><b class="text-neutral-900">Click to upload</b> o i-drag dito</span>
        <span class="text-xs text-neutral-400">JPG, PNG, WEBP o GIF · max <?= (int) $maxMb ?> MB bawat isa · hanggang <?= (int) $maxImages ?></span>
    </label>
    <input id="images" name="images[]" type="file" multiple accept="image/jpeg,image/png,image/webp,image/gif" class="sr-only">

    <!-- Preview ng mga napili -->
    <ul data-preview class="mt-3 grid grid-cols-3 gap-2 empty:hidden sm:grid-cols-4"></ul>
    <p data-note class="mt-1.5 hidden text-sm text-amber-600"></p>

    <?php if ($uploadError): ?>
        <p class="mt-1.5 text-sm text-red-600"><?= e($uploadError) ?></p>
    <?php endif; ?>
</div>

<script>
// Pumili / i-drag ng maraming larawan, may preview at tanggal-button bawat isa
(() => {
    const box     = document.currentScript.previousElementSibling;
    const input   = box.querySelector('input[type=file]');
    const label   = box.querySelector('label');
    const list    = box.querySelector('[data-preview]');
    const note    = box.querySelector('[data-note]');
    const max     = +box.dataset.max;
    const maxSize = +box.dataset.maxMb * 1024 * 1024;
    let picked    = [];

    const say = msg => { note.textContent = msg; note.classList.toggle('hidden', !msg); };

    function add(files) {
        let skipped = '';
        for (const f of files) {
            if (!f.type.startsWith('image/'))                         { skipped = `${f.name}: hindi larawan.`; continue; }
            if (f.size > maxSize)                                      { skipped = `${f.name}: lampas ${box.dataset.maxMb} MB.`; continue; }
            if (picked.some(p => p.name === f.name && p.size === f.size)) continue;
            if (picked.length >= max)                                  { skipped = `Hanggang ${max} larawan lang.`; break; }
            picked.push(f);
        }
        say(skipped);
        sync();
    }

    function sync() {
        const dt = new DataTransfer();
        picked.forEach(f => dt.items.add(f));
        input.files = dt.files;

        list.innerHTML = '';
        picked.forEach((f, i) => {
            const li = document.createElement('li');
            li.className = 'group/item relative aspect-square overflow-hidden rounded-md border border-neutral-200 bg-neutral-100';
            li.innerHTML = `
                <img src="${URL.createObjectURL(f)}" alt="" class="size-full object-cover">
                <button type="button" aria-label="Remove"
                        class="absolute right-1 top-1 grid size-6 place-items-center rounded-full bg-neutral-900/80 text-white opacity-0 transition group-hover/item:opacity-100 focus:opacity-100 cursor-pointer">
                    <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>`;
            li.querySelector('button').addEventListener('click', () => { picked.splice(i, 1); say(''); sync(); });
            list.appendChild(li);
        });
    }

    input.addEventListener('change', () => add([...input.files]));

    ['dragenter', 'dragover'].forEach(ev => label.addEventListener(ev, e => { e.preventDefault(); label.classList.add('border-neutral-900'); }));
    ['dragleave', 'drop'].forEach(ev => label.addEventListener(ev, e => { e.preventDefault(); label.classList.remove('border-neutral-900'); }));
    label.addEventListener('drop', e => add([...e.dataTransfer.files]));
})();
</script>