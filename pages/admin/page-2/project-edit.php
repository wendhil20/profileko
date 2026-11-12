<?php
// pages/admin/page-2/project-edit.php — VIEW lang (walang logic)
// Galing sa ProjectController::edit(): $project, $images, $errors, $maxImages, $maxMb
if (!defined('ROOT_PATH')) exit('No direct access');

$removing = input_ids('remove');   // para manatiling naka-check kapag may error
?>
<div class="p-5 lg:p-8">

    <header class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="<?= url('adminprojects') ?>" class="mb-2 inline-flex items-center gap-1.5 text-sm font-medium text-neutral-500 transition hover:text-neutral-900">
                <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
                All projects
            </a>
            <h1 class="text-3xl font-bold">Edit project</h1>
        </div>
        <span class="text-sm text-muted">
            Added <?= e(date('M j, Y', strtotime($project['created_at']))) ?>
            <?php if (!empty($project['updated_at'])): ?> · Updated <?= e(date('M j, Y g:i A', strtotime($project['updated_at']))) ?><?php endif; ?>
        </span>
    </header>

    <!-- Flash messages -->
    <?php foreach (flashes() as $f): ?>
        <div data-flash class="mb-5 flex items-center gap-3 rounded-lg border px-4 py-3 text-sm
                    <?= $f['type'] === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-red-200 bg-red-50 text-red-700' ?>">
            <svg viewBox="0 0 24 24" class="size-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/></svg>
            <span><?= e($f['message']) ?></span>
        </div>
    <?php endforeach; ?>

    <form method="post" action="<?= url('adminprojects/edit') ?>?id=<?= (int) $project['id'] ?>" enctype="multipart/form-data" novalidate
          class="grid gap-6 xl:grid-cols-[400px_1fr] xl:items-start">
        <?= csrf_field() ?>

        <!-- ============ DETAILS ============ -->
        <section class="<?= $cls['box'] ?> p-5">
            <h2 class="mb-4 text-lg font-bold">Details</h2>

            <?php if (!empty($errors['form'])): ?>
                <p class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3.5 py-2.5 text-sm text-red-700"><?= e($errors['form']) ?></p>
            <?php endif; ?>

            <div class="mb-4">
                <label for="name" class="<?= $cls['label'] ?>">Name</label>
                <input id="name" name="name" type="text" maxlength="150" value="<?= e(input('name', $project['name'])) ?>"
                       <?= invalid($errors, 'name') ?> class="<?= $cls['input'] ?>">
                <?= field_error($errors, 'name') ?>
            </div>

            <div class="mb-5">
                <label for="description" class="<?= $cls['label'] ?>">Description <span class="font-normal text-neutral-400">(optional)</span></label>
                <textarea id="description" name="description" rows="6" maxlength="2000"
                          <?= invalid($errors, 'description') ?> class="<?= $cls['input'] ?> resize-y"><?= e(input('description', (string) $project['description'])) ?></textarea>
                <?= field_error($errors, 'description') ?>
            </div>

            <button type="submit" class="<?= $cls['btn'] ?> w-full">
                <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"/></svg>
                Save changes
            </button>
        </section>

        <!-- ============ IMAGES ============ -->
        <section class="<?= $cls['box'] ?> p-5">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold">Images</h2>
                <span class="rounded-full bg-neutral-100 px-2.5 py-0.5 text-sm font-semibold"><?= count($images) ?> / <?= (int) $maxImages ?></span>
            </div>

            <!-- Kasalukuyang mga larawan: i-check para tanggalin -->
            <p class="mb-2 text-sm text-muted">I-click ang larawan para markahan na tatanggalin. Mabubura lang ito kapag pinindot ang <b class="text-neutral-900">Save changes</b>.</p>
            <ul class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                <?php foreach ($images as $i => $img): ?>
                    <li>
                        <label class="group relative block aspect-square cursor-pointer overflow-hidden rounded-lg border-2 border-neutral-200 transition
                                      has-[:checked]:border-red-500">
                            <input type="checkbox" name="remove[]" value="<?= (int) $img['id'] ?>" class="peer sr-only"
                                   <?= in_array((int) $img['id'], $removing, true) ? 'checked' : '' ?>>
                            <img src="<?= img($img['image']) ?>" alt="" loading="lazy"
                                 class="size-full object-cover transition peer-checked:opacity-40 peer-checked:grayscale">

                            <?php if ($i === 0): ?>
                                <span class="absolute left-1.5 top-1.5 rounded bg-neutral-900 px-1.5 py-0.5 text-[11px] font-bold text-white">Cover</span>
                            <?php endif; ?>

                            <!-- hover: "Remove" | naka-check: "Will be removed" -->
                            <span class="absolute inset-x-0 bottom-0 bg-neutral-900/80 py-1 text-center text-xs font-semibold text-white opacity-0 transition
                                         group-hover:opacity-100 peer-checked:hidden">Remove</span>
                            <span class="absolute inset-0 hidden place-items-center peer-checked:grid">
                                <span class="rounded-full bg-red-600 px-2.5 py-1 text-xs font-bold text-white">Will be removed</span>
                            </span>
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Magdagdag ng bago -->
            <span class="<?= $cls['label'] ?>">Add more images</span>
            <?php $uploadError = $errors['images'] ?? ''; include ROOT_PATH . 'pages/admin/page-2/partials/image-uploader.php'; ?>
        </section>
    </form>
</div>

<script>
// Kusang mawawala ang success message
document.querySelectorAll('[data-flash]').forEach(el =>
    setTimeout(() => el.classList.add('hidden'), 5000));
</script>