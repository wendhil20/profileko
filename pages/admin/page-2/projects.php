<?php
// pages/admin/page-2/projects.php — VIEW lang (walang logic)
// Galing sa ProjectController: $projects, $errors, $maxImages, $maxMb
if (!defined('ROOT_PATH')) exit('No direct access');
?>
<div class="p-5 lg:p-8">

    <header class="mb-6">
        <h1 class="text-3xl font-bold">Projects</h1>
        <p class="mt-1 text-muted">Magdagdag ng project na lalabas sa portfolio.</p>
    </header>

    <!-- Flash messages (galing sa redirect) -->
    <?php foreach (flashes() as $f): ?>
        <div data-flash class="mb-5 flex items-center gap-3 rounded-lg border px-4 py-3 text-sm
                    <?= $f['type'] === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-red-200 bg-red-50 text-red-700' ?>">
            <svg viewBox="0 0 24 24" class="size-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/></svg>
            <span><?= e($f['message']) ?></span>
        </div>
    <?php endforeach; ?>

    <div class="grid gap-6 xl:grid-cols-[400px_1fr] xl:items-start">

        <!-- ============ FORM ============ -->
        <form method="post" action="<?= url('adminprojects') ?>" enctype="multipart/form-data" novalidate
              class="<?= $cls['box'] ?> p-5">
            <?= csrf_field() ?>
            <h2 class="mb-4 text-lg font-bold">Add project</h2>

            <?php if (!empty($errors['form'])): ?>
                <p class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3.5 py-2.5 text-sm text-red-700"><?= e($errors['form']) ?></p>
            <?php endif; ?>

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="<?= $cls['label'] ?>">Name</label>
                <input id="name" name="name" type="text" maxlength="150" value="<?= e(input('name')) ?>"
                       placeholder="e.g. NobleHome Accounting System" <?= invalid($errors, 'name') ?> class="<?= $cls['input'] ?>">
                <?= field_error($errors, 'name') ?>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label for="description" class="<?= $cls['label'] ?>">Description <span class="font-normal text-neutral-400">(optional)</span></label>
                <textarea id="description" name="description" rows="4" maxlength="2000"
                          placeholder="Ano ang ginagawa ng project, at anong tech ang gamit."
                          <?= invalid($errors, 'description') ?> class="<?= $cls['input'] ?> resize-y"><?= e(input('description')) ?></textarea>
                <?= field_error($errors, 'description') ?>
            </div>

            <!-- Images (marami) -->
            <div class="mb-5">
                <span class="<?= $cls['label'] ?>">Images</span>
                <?php $uploadError = $errors['images'] ?? ''; include ROOT_PATH . 'pages/admin/page-2/partials/image-uploader.php'; ?>
                <p class="mt-1.5 text-xs text-neutral-400">Ang unang larawan ang magiging cover.</p>
            </div>

            <button type="submit" class="<?= $cls['btn'] ?> w-full">
                <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Add project
            </button>
        </form>

        <!-- ============ LIST ============ -->
        <section class="<?= $cls['box'] ?> overflow-hidden">
            <div class="flex items-center justify-between border-b border-neutral-200 px-5 py-3.5">
                <h2 class="text-lg font-bold">All projects</h2>
                <span class="rounded-full bg-neutral-100 px-2.5 py-0.5 text-sm font-semibold"><?= count($projects) ?></span>
            </div>

            <?php if (!$projects): ?>
                <p class="px-5 py-12 text-center text-muted">Wala pang project. Magdagdag gamit ang form.</p>
            <?php else: ?>
                <ul class="divide-y divide-neutral-200">
                    <?php foreach ($projects as $p): ?>
                        <li class="flex items-center gap-4 px-5 py-3.5">
                            <div class="relative shrink-0">
                                <img src="<?= img($p['image']) ?>" alt="" loading="lazy" class="h-14 w-22 rounded-md border border-neutral-200 object-cover">
                                <span class="absolute -bottom-1.5 -right-1.5 rounded-full bg-neutral-900 px-1.5 text-[11px] font-bold leading-5 text-white"
                                      title="<?= (int) $p['image_count'] ?> image(s)"><?= (int) $p['image_count'] ?></span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <b class="block truncate"><?= e($p['name']) ?></b>
                                <p class="truncate text-sm text-muted"><?= e($p['description'] ?: '—') ?></p>
                            </div>
                            <time class="hidden shrink-0 text-sm text-neutral-400 md:block" datetime="<?= e($p['created_at']) ?>">
                                <?= e(date('M j, Y', strtotime($p['created_at']))) ?>
                            </time>
                            <a href="<?= url('adminprojects/edit') ?>?id=<?= (int) $p['id'] ?>"
                               class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-neutral-200 px-3 py-1.5 text-sm font-semibold transition hover:border-neutral-900">
                                <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z"/></svg>
                                Edit
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </div>
</div>

<script>
// Kusang mawawala ang success message
document.querySelectorAll('[data-flash]').forEach(el =>
    setTimeout(() => el.classList.add('hidden'), 5000));
</script>