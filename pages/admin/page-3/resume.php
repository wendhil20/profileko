<?php
// pages/admin/page-3/resume.php — edit the resume
// Variables from ResumeController::index(): $resume, $errors, $saved
if (!defined('ROOT_PATH')) exit('No direct access');

$input = 'w-full h-10 rounded-lg border border-neutral-300 bg-white px-3 text-sm text-neutral-900 placeholder:text-neutral-400
          focus:outline-none focus:border-neutral-900 focus:ring-2 focus:ring-neutral-900/10';
$area  = 'w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 placeholder:text-neutral-400
          focus:outline-none focus:border-neutral-900 focus:ring-2 focus:ring-neutral-900/10';
$label = 'block text-xs font-semibold text-neutral-700 mb-1.5';
$card  = 'rounded-xl border border-neutral-200 bg-white';

$err = fn(string $key) => !empty($errors[$key])
    ? '<p class="mt-1 text-xs font-medium text-red-600">' . e($errors[$key]) . '</p>'
    : '';

// Empty rows so a new resume still shows one of each
$experience     = $resume['experience']     ?: [[]];
$education      = $resume['education']      ?: [[]];
$skills         = $resume['skills']         ?: [[]];
$certifications = $resume['certifications'];
?>
<div class="p-5 lg:p-8 max-w-5xl">

    <!-- Header -->
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold">Resume</h1>
            <p class="mt-1 text-sm text-muted">Everything here appears on your public resume page and in the PDF.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= url('resume') ?>" target="_blank" rel="noopener"
               class="h-10 px-4 inline-flex items-center gap-2 rounded-lg border border-neutral-300 bg-white text-sm font-semibold hover:bg-neutral-50">
                View page
            </a>
            <a href="<?= url('resume/download') ?>"
               class="h-10 px-4 inline-flex items-center gap-2 rounded-lg border border-neutral-300 bg-white text-sm font-semibold hover:bg-neutral-50">
                Download PDF
            </a>
        </div>
    </div>

    <?php if ($saved && empty($errors)): ?>
        <div class="mt-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            Resume saved.
        </div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="mt-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <?= e($errors['form'] ?? 'Please fix the highlighted fields below.') ?>
            <?php if (!empty($errors['exp'])): ?><br><?= e($errors['exp']) ?><?php endif; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('adminresume') ?>" class="mt-6 space-y-6" id="resumeForm">
        <?= csrf_field() ?>

        <!-- ===== Personal info ===== -->
        <section class="<?= $card ?>">
            <header class="px-5 py-4 border-b border-neutral-100">
                <h2 class="text-base font-bold">Personal info</h2>
            </header>
            <div class="p-5 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="<?= $label ?>" for="full_name">Full name <span class="text-red-500">*</span></label>
                    <input id="full_name" name="full_name" class="<?= $input ?>" value="<?= e($resume['full_name']) ?>" required>
                    <?= $err('full_name') ?>
                </div>
                <div>
                    <label class="<?= $label ?>" for="title">Job title</label>
                    <input id="title" name="title" class="<?= $input ?>" value="<?= e($resume['title']) ?>" placeholder="Junior Web Developer">
                </div>
                <div>
                    <label class="<?= $label ?>" for="email">Email</label>
                    <input id="email" name="email" type="email" class="<?= $input ?>" value="<?= e($resume['email']) ?>">
                    <?= $err('email') ?>
                </div>
                <div>
                    <label class="<?= $label ?>" for="phone">Phone</label>
                    <input id="phone" name="phone" class="<?= $input ?>" value="<?= e($resume['phone']) ?>">
                </div>
                <div class="sm:col-span-2">
                    <label class="<?= $label ?>" for="address">Address</label>
                    <input id="address" name="address" class="<?= $input ?>" value="<?= e($resume['address']) ?>" placeholder="City, Philippines">
                    <p class="mt-1 text-[11px] text-muted">Tip: city only is enough on a public resume.</p>
                </div>
                <div>
                    <label class="<?= $label ?>" for="linkedin">LinkedIn URL</label>
                    <input id="linkedin" name="linkedin" class="<?= $input ?>" value="<?= e($resume['linkedin']) ?>" placeholder="https://www.linkedin.com/in/…">
                    <?= $err('linkedin') ?>
                </div>
                <div>
                    <label class="<?= $label ?>" for="github">GitHub URL</label>
                    <input id="github" name="github" class="<?= $input ?>" value="<?= e($resume['github']) ?>" placeholder="https://github.com/…">
                    <?= $err('github') ?>
                </div>
                <div class="sm:col-span-2">
                    <label class="<?= $label ?>" for="website">Website / Portfolio URL</label>
                    <input id="website" name="website" class="<?= $input ?>" value="<?= e($resume['website']) ?>" placeholder="https://…">
                    <?= $err('website') ?>
                </div>
                <div class="sm:col-span-2">
                    <label class="<?= $label ?>" for="summary">Summary <span class="font-normal text-neutral-400">(optional — leave empty to hide)</span></label>
                    <textarea id="summary" name="summary" rows="3" class="<?= $area ?>"><?= e($resume['summary']) ?></textarea>
                </div>
            </div>
        </section>

        <!-- ===== Experience ===== -->
        <section class="<?= $card ?>">
            <header class="px-5 py-4 border-b border-neutral-100 flex items-center justify-between">
                <h2 class="text-base font-bold">Experience</h2>
                <button type="button" data-add="exp" class="h-8 px-3 rounded-lg border border-neutral-300 text-xs font-semibold hover:bg-neutral-50">+ Add experience</button>
            </header>
            <div class="p-5 space-y-4" data-list="exp">
                <?php foreach ($experience as $i => $x): ?>
                    <div class="rs-row rounded-lg border border-neutral-200 p-4" data-row>
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-semibold text-neutral-500">Experience</p>
                            <button type="button" data-remove class="text-xs font-semibold text-neutral-400 hover:text-red-600">Remove</button>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div><label class="<?= $label ?>">Job title *</label><input name="exp[<?= $i ?>][title]" class="<?= $input ?>" value="<?= e($x['title'] ?? '') ?>"></div>
                            <div><label class="<?= $label ?>">Company *</label><input name="exp[<?= $i ?>][company]" class="<?= $input ?>" value="<?= e($x['company'] ?? '') ?>"></div>
                            <div><label class="<?= $label ?>">Location</label><input name="exp[<?= $i ?>][location]" class="<?= $input ?>" value="<?= e($x['location'] ?? '') ?>"></div>
                            <div class="grid grid-cols-2 gap-3">
                                <div><label class="<?= $label ?>">Start</label><input name="exp[<?= $i ?>][start]" class="<?= $input ?>" value="<?= e($x['start'] ?? '') ?>" placeholder="June 2025"></div>
                                <div><label class="<?= $label ?>">End</label><input name="exp[<?= $i ?>][end]" class="<?= $input ?>" value="<?= e($x['end'] ?? '') ?>" placeholder="Dec 2026"></div>
                            </div>
                            <label class="sm:col-span-2 inline-flex items-center gap-2 text-sm text-neutral-700">
                                <input type="checkbox" name="exp[<?= $i ?>][current]" value="1" class="size-4 accent-neutral-900" <?= !empty($x['current']) ? 'checked' : '' ?>>
                                I currently work here (end shows "Present")
                            </label>
                            <div class="sm:col-span-2"><label class="<?= $label ?>">Tools used</label><input name="exp[<?= $i ?>][tools]" class="<?= $input ?>" value="<?= e($x['tools'] ?? '') ?>" placeholder="PHP, MySQL, JavaScript, Tailwind CSS"></div>
                            <div class="sm:col-span-2">
                                <label class="<?= $label ?>">What you did <span class="font-normal text-neutral-400">(one per line · start a line with # for a heading, e.g. <code># CRM System</code>)</span></label>
                                <textarea name="exp[<?= $i ?>][bullets]" rows="12" class="<?= $area ?>"><?= e(implode("\n", $x['bullets'] ?? [])) ?></textarea>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- ===== Education ===== -->
        <section class="<?= $card ?>">
            <header class="px-5 py-4 border-b border-neutral-100 flex items-center justify-between">
                <h2 class="text-base font-bold">Education</h2>
                <button type="button" data-add="edu" class="h-8 px-3 rounded-lg border border-neutral-300 text-xs font-semibold hover:bg-neutral-50">+ Add education</button>
            </header>
            <div class="p-5 space-y-4" data-list="edu">
                <?php foreach ($education as $i => $x): ?>
                    <div class="rs-row rounded-lg border border-neutral-200 p-4" data-row>
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-semibold text-neutral-500">Education</p>
                            <button type="button" data-remove class="text-xs font-semibold text-neutral-400 hover:text-red-600">Remove</button>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="sm:col-span-2"><label class="<?= $label ?>">Degree</label><input name="edu[<?= $i ?>][degree]" class="<?= $input ?>" value="<?= e($x['degree'] ?? '') ?>" placeholder="Bachelor of Science in Information Technology"></div>
                            <div><label class="<?= $label ?>">School</label><input name="edu[<?= $i ?>][school]" class="<?= $input ?>" value="<?= e($x['school'] ?? '') ?>"></div>
                            <div><label class="<?= $label ?>">Years</label><input name="edu[<?= $i ?>][years]" class="<?= $input ?>" value="<?= e($x['years'] ?? '') ?>" placeholder="2021 – 2025"></div>
                            <div><label class="<?= $label ?>">Location</label><input name="edu[<?= $i ?>][location]" class="<?= $input ?>" value="<?= e($x['location'] ?? '') ?>"></div>
                            <div><label class="<?= $label ?>">Note</label><input name="edu[<?= $i ?>][note]" class="<?= $input ?>" value="<?= e($x['note'] ?? '') ?>" placeholder="Graduated / Cum Laude"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- ===== Skills ===== -->
        <section class="<?= $card ?>">
            <header class="px-5 py-4 border-b border-neutral-100 flex items-center justify-between">
                <h2 class="text-base font-bold">Skills</h2>
                <button type="button" data-add="skill" class="h-8 px-3 rounded-lg border border-neutral-300 text-xs font-semibold hover:bg-neutral-50">+ Add group</button>
            </header>
            <div class="p-5 space-y-3" data-list="skill">
                <?php foreach ($skills as $i => $x): ?>
                    <div class="rs-row grid gap-3 sm:grid-cols-[200px_1fr_auto] items-end" data-row>
                        <div><label class="<?= $label ?>">Group</label><input name="skill[<?= $i ?>][label]" class="<?= $input ?>" value="<?= e($x['label'] ?? '') ?>" placeholder="Languages"></div>
                        <div><label class="<?= $label ?>">Items <span class="font-normal text-neutral-400">(comma-separated)</span></label><input name="skill[<?= $i ?>][items]" class="<?= $input ?>" value="<?= e($x['items'] ?? '') ?>" placeholder="PHP, JavaScript, SQL"></div>
                        <button type="button" data-remove class="h-10 px-3 text-xs font-semibold text-neutral-400 hover:text-red-600">Remove</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- ===== Certifications ===== -->
        <section class="<?= $card ?>">
            <header class="px-5 py-4 border-b border-neutral-100 flex items-center justify-between">
                <h2 class="text-base font-bold">Certifications <span class="text-xs font-normal text-neutral-400">(optional)</span></h2>
                <button type="button" data-add="cert" class="h-8 px-3 rounded-lg border border-neutral-300 text-xs font-semibold hover:bg-neutral-50">+ Add certification</button>
            </header>
            <div class="p-5 space-y-3" data-list="cert">
                <?php foreach ($certifications as $i => $x): ?>
                    <div class="rs-row grid gap-3 sm:grid-cols-[1fr_200px_100px_auto] items-end" data-row>
                        <div><label class="<?= $label ?>">Name</label><input name="cert[<?= $i ?>][name]" class="<?= $input ?>" value="<?= e($x['name'] ?? '') ?>"></div>
                        <div><label class="<?= $label ?>">Issuer</label><input name="cert[<?= $i ?>][issuer]" class="<?= $input ?>" value="<?= e($x['issuer'] ?? '') ?>"></div>
                        <div><label class="<?= $label ?>">Year</label><input name="cert[<?= $i ?>][year]" class="<?= $input ?>" value="<?= e($x['year'] ?? '') ?>"></div>
                        <button type="button" data-remove class="h-10 px-3 text-xs font-semibold text-neutral-400 hover:text-red-600">Remove</button>
                    </div>
                <?php endforeach; ?>
                <p data-empty class="text-sm text-muted <?= $certifications ? 'hidden' : '' ?>">No certifications yet.</p>
            </div>
        </section>

        <!-- Save bar -->
        <div class="sticky bottom-0 -mx-5 lg:-mx-8 px-5 lg:px-8 py-3 bg-neutral-50/90 backdrop-blur border-t border-neutral-200 flex justify-end">
            <button type="submit" class="h-11 px-6 rounded-lg bg-neutral-900 text-white text-sm font-semibold hover:bg-neutral-700">
                Save resume
            </button>
        </div>
    </form>
</div>

<!-- Templates for new rows (__i__ is replaced with a unique number) -->
<template id="tpl-exp">
    <div class="rs-row rounded-lg border border-neutral-200 p-4" data-row>
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-semibold text-neutral-500">Experience</p>
            <button type="button" data-remove class="text-xs font-semibold text-neutral-400 hover:text-red-600">Remove</button>
        </div>
        <div class="grid gap-3 sm:grid-cols-2">
            <div><label class="<?= $label ?>">Job title *</label><input name="exp[__i__][title]" class="<?= $input ?>"></div>
            <div><label class="<?= $label ?>">Company *</label><input name="exp[__i__][company]" class="<?= $input ?>"></div>
            <div><label class="<?= $label ?>">Location</label><input name="exp[__i__][location]" class="<?= $input ?>"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="<?= $label ?>">Start</label><input name="exp[__i__][start]" class="<?= $input ?>" placeholder="June 2025"></div>
                <div><label class="<?= $label ?>">End</label><input name="exp[__i__][end]" class="<?= $input ?>" placeholder="Dec 2026"></div>
            </div>
            <label class="sm:col-span-2 inline-flex items-center gap-2 text-sm text-neutral-700">
                <input type="checkbox" name="exp[__i__][current]" value="1" class="size-4 accent-neutral-900"> I currently work here (end shows "Present")
            </label>
            <div class="sm:col-span-2"><label class="<?= $label ?>">Tools used</label><input name="exp[__i__][tools]" class="<?= $input ?>"></div>
            <div class="sm:col-span-2"><label class="<?= $label ?>">What you did <span class="font-normal text-neutral-400">(one per line · start a line with # for a heading, e.g. <code># CRM System</code>)</span></label><textarea name="exp[__i__][bullets]" rows="12" class="<?= $area ?>"></textarea></div>
        </div>
    </div>
</template>
<template id="tpl-edu">
    <div class="rs-row rounded-lg border border-neutral-200 p-4" data-row>
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-semibold text-neutral-500">Education</p>
            <button type="button" data-remove class="text-xs font-semibold text-neutral-400 hover:text-red-600">Remove</button>
        </div>
        <div class="grid gap-3 sm:grid-cols-2">
            <div class="sm:col-span-2"><label class="<?= $label ?>">Degree</label><input name="edu[__i__][degree]" class="<?= $input ?>"></div>
            <div><label class="<?= $label ?>">School</label><input name="edu[__i__][school]" class="<?= $input ?>"></div>
            <div><label class="<?= $label ?>">Years</label><input name="edu[__i__][years]" class="<?= $input ?>" placeholder="2021 – 2025"></div>
            <div><label class="<?= $label ?>">Location</label><input name="edu[__i__][location]" class="<?= $input ?>"></div>
            <div><label class="<?= $label ?>">Note</label><input name="edu[__i__][note]" class="<?= $input ?>"></div>
        </div>
    </div>
</template>
<template id="tpl-skill">
    <div class="rs-row grid gap-3 sm:grid-cols-[200px_1fr_auto] items-end" data-row>
        <div><label class="<?= $label ?>">Group</label><input name="skill[__i__][label]" class="<?= $input ?>" placeholder="Tools"></div>
        <div><label class="<?= $label ?>">Items <span class="font-normal text-neutral-400">(comma-separated)</span></label><input name="skill[__i__][items]" class="<?= $input ?>"></div>
        <button type="button" data-remove class="h-10 px-3 text-xs font-semibold text-neutral-400 hover:text-red-600">Remove</button>
    </div>
</template>
<template id="tpl-cert">
    <div class="rs-row grid gap-3 sm:grid-cols-[1fr_200px_100px_auto] items-end" data-row>
        <div><label class="<?= $label ?>">Name</label><input name="cert[__i__][name]" class="<?= $input ?>"></div>
        <div><label class="<?= $label ?>">Issuer</label><input name="cert[__i__][issuer]" class="<?= $input ?>"></div>
        <div><label class="<?= $label ?>">Year</label><input name="cert[__i__][year]" class="<?= $input ?>"></div>
        <button type="button" data-remove class="h-10 px-3 text-xs font-semibold text-neutral-400 hover:text-red-600">Remove</button>
    </div>
</template>

<script>
    (function () {
        const form = document.getElementById('resumeForm');
        let next = 1000; // new rows get unique indexes so they never collide with saved ones

        form.addEventListener('click', e => {
            const add = e.target.closest('[data-add]');
            if (add) {
                const type = add.dataset.add;
                const list = form.querySelector(`[data-list="${type}"]`);
                const html = document.getElementById('tpl-' + type).innerHTML.replaceAll('__i__', String(next++));
                list.insertAdjacentHTML('beforeend', html);
                list.querySelector('[data-empty]')?.classList.add('hidden');
                list.lastElementChild.querySelector('input, textarea')?.focus();
                return;
            }
            const remove = e.target.closest('[data-remove]');
            if (remove) {
                const row = remove.closest('[data-row]');
                const hasText = [...row.querySelectorAll('input:not([type=checkbox]), textarea')].some(i => i.value.trim() !== '');
                if (hasText && !confirm('Remove this item?')) return;
                const list = row.parentElement;
                row.remove();
                if (!list.querySelector('[data-row]')) list.querySelector('[data-empty]')?.classList.remove('hidden');
            }
        });

        // Warn before leaving with unsaved changes
        let dirty = false;
        form.addEventListener('input', () => dirty = true);
        form.addEventListener('submit', () => dirty = false);
        window.addEventListener('beforeunload', e => { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
    })();
</script>