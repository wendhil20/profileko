<?php
// pages/user/resume.php — public resume page
// Variables from controllers/user/ResumeController::index(): $resume
if (!defined('ROOT_PATH')) exit('No direct access');

$r = $resume;
$links = array_filter([
    'Email'    => $r['email'] ? ['mailto:' . $r['email'], $r['email']] : null,
    'Phone'    => $r['phone'] ? ['tel:' . preg_replace('/[^0-9+]/', '', $r['phone']), $r['phone']] : null,
    'LinkedIn' => $r['linkedin'] ? [$r['linkedin'], resume_short_url($r['linkedin'])] : null,
    'GitHub'   => $r['github'] ? [$r['github'], resume_short_url($r['github'])] : null,
    'Website'  => $r['website'] ? [$r['website'], resume_short_url($r['website'])] : null,
]);
$sectionTitle = 'text-[11px] font-semibold uppercase tracking-[.16em] text-neutral-400';
?>
<section class="w-full max-w-4xl mx-auto px-4 sm:px-6 py-10 lg:py-14">

    <?php if ($r['full_name'] === ''): ?>
        <div class="rounded-2xl border border-neutral-900/10 bg-white p-10 text-center">
            <p class="text-lg font-bold">Resume coming soon</p>
            <p class="mt-1 text-sm text-muted">Please check back later.</p>
        </div>
    <?php else: ?>

        <!-- Header -->
        <header class="flex flex-wrap items-start justify-between gap-6 pb-8 border-b border-neutral-900/10">
            <div class="flex min-w-0 items-center gap-4 sm:gap-6">
            <?php if (is_file(ROOT_PATH . 'assets/img/profile.png')): ?>
                <!-- Same photo as the home page (assets/img/profile.png) -->
                <div class="shrink-0 overflow-hidden rounded-2xl p-1 shadow-md bg-white">
                    <img src="<?= img('profile.png') ?>" alt="<?= e($r['full_name']) ?>"
                         class="block w-24 sm:w-32 h-auto rounded-xl bg-neutral-100">
                </div>
            <?php endif; ?>
            <div class="min-w-0">
                <p class="<?= $sectionTitle ?>">Resume</p>
                <h1 class="mt-2 text-4xl sm:text-5xl font-bold tracking-tight"><?= e($r['full_name']) ?></h1>
                <?php if ($r['title']): ?>
                    <p class="mt-2 text-lg font-semibold text-neutral-600"><?= e($r['title']) ?></p>
                <?php endif; ?>
                <?php if ($r['address']): ?>
                    <p class="mt-1 text-sm text-muted"><?= e($r['address']) ?></p>
                <?php endif; ?>
            </div>
            </div>
            <a href="<?= url('resume/download') ?>"
               class="group inline-flex items-center gap-2 rounded-lg bg-neutral-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-neutral-700">
                <svg viewBox="0 0 24 24" class="size-4 transition group-hover:translate-y-0.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v3h16v-3"/></svg>
                Download PDF
            </a>
        </header>

        <?php if ($links): ?>
            <ul class="flex flex-wrap gap-x-6 gap-y-2 py-5 border-b border-neutral-900/10 text-sm">
                <?php foreach ($links as $name => [$href, $text]): ?>
                    <li>
                        <span class="text-neutral-400"><?= e($name) ?></span>
                        <a href="<?= e($href) ?>" <?= str_starts_with($href, 'http') ? 'target="_blank" rel="noopener"' : '' ?>
                           class="ml-1.5 font-semibold text-neutral-900 hover:underline underline-offset-4"><?= e($text) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (trim((string) $r['summary']) !== ''): ?>
            <section class="py-8 border-b border-neutral-900/10">
                <h2 class="<?= $sectionTitle ?>">Summary</h2>
                <p class="mt-3 text-[15px] leading-relaxed text-neutral-700"><?= nl2br(e($r['summary'])) ?></p>
            </section>
        <?php endif; ?>

        <!-- Experience -->
        <?php if ($r['experience']): ?>
            <section class="py-8 border-b border-neutral-900/10">
                <h2 class="<?= $sectionTitle ?>">Experience</h2>
                <div class="mt-5 space-y-8">
                    <?php foreach ($r['experience'] as $x): ?>
                        <article class="grid gap-2 sm:grid-cols-[160px_1fr] sm:gap-6">
                            <p class="text-sm font-semibold text-neutral-500 sm:pt-0.5"><?= e(resume_period($x)) ?></p>
                            <div class="min-w-0">
                                <h3 class="text-lg font-bold leading-snug"><?= e($x['title'] ?? '') ?></h3>
                                <p class="text-sm font-semibold text-neutral-600">
                                    <?= e($x['company'] ?? '') ?><?= !empty($x['location']) ? ' · ' . e($x['location']) : '' ?>
                                </p>
                                <?php foreach (resume_bullet_groups($x['bullets'] ?? []) as $g): ?>
                                    <div class="mt-4">
                                        <?php if ($g['heading'] !== null): ?>
                                            <h4 class="text-[15px] font-bold text-neutral-900"><?= e($g['heading']) ?></h4>
                                        <?php endif; ?>
                                        <?php if ($g['items']): ?>
                                            <ul class="mt-1.5 space-y-1.5 text-[15px] leading-relaxed text-neutral-700">
                                                <?php foreach ($g['items'] as $b): ?>
                                                    <li class="flex gap-2.5"><span class="mt-2.5 size-1.5 shrink-0 rounded-full bg-neutral-400"></span><span><?= e($b) ?></span></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (!empty($x['tools'])): ?>
                                    <div class="mt-4 flex flex-wrap gap-1.5">
                                        <?php foreach (array_filter(array_map('trim', explode(',', $x['tools']))) as $tool): ?>
                                            <span class="rounded-md border border-neutral-900/10 bg-neutral-50 px-2 py-0.5 text-xs font-semibold text-neutral-700"><?= e($tool) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Education -->
        <?php if ($r['education']): ?>
            <section class="py-8 border-b border-neutral-900/10">
                <h2 class="<?= $sectionTitle ?>">Education</h2>
                <div class="mt-5 space-y-6">
                    <?php foreach ($r['education'] as $x): ?>
                        <article class="grid gap-1 sm:grid-cols-[160px_1fr] sm:gap-6">
                            <p class="text-sm font-semibold text-neutral-500"><?= e($x['years'] ?? '') ?></p>
                            <div>
                                <h3 class="text-lg font-bold leading-snug"><?= e($x['degree'] ?? '') ?></h3>
                                <p class="text-sm font-semibold text-neutral-600"><?= e($x['school'] ?? '') ?></p>
                                <?php if (!empty($x['location']) || !empty($x['note'])): ?>
                                    <p class="mt-1 text-sm text-muted">
                                        <?= e(implode(' · ', array_filter([$x['location'] ?? '', $x['note'] ?? '']))) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Skills -->
        <?php if ($r['skills']): ?>
            <section class="py-8 border-b border-neutral-900/10">
                <h2 class="<?= $sectionTitle ?>">Technical Skills</h2>
                <dl class="mt-5 space-y-3">
                    <?php foreach ($r['skills'] as $x): ?>
                        <div class="grid gap-1 sm:grid-cols-[160px_1fr] sm:gap-6">
                            <dt class="text-sm font-semibold text-neutral-500"><?= e($x['label'] ?? '') ?></dt>
                            <dd class="flex flex-wrap gap-1.5">
                                <?php foreach (array_filter(array_map('trim', explode(',', $x['items'] ?? ''))) as $s): ?>
                                    <span class="rounded-md border border-neutral-900/10 px-2 py-0.5 text-sm font-medium text-neutral-800"><?= e($s) ?></span>
                                <?php endforeach; ?>
                            </dd>
                        </div>
                    <?php endforeach; ?>
                </dl>
            </section>
        <?php endif; ?>

        <!-- Certifications -->
        <?php if ($r['certifications']): ?>
            <section class="py-8">
                <h2 class="<?= $sectionTitle ?>">Certifications</h2>
                <ul class="mt-5 space-y-2">
                    <?php foreach ($r['certifications'] as $x): ?>
                        <li class="grid gap-1 sm:grid-cols-[160px_1fr] sm:gap-6">
                            <span class="text-sm font-semibold text-neutral-500"><?= e($x['year'] ?? '') ?></span>
                            <span class="text-[15px]"><b class="font-bold"><?= e($x['name'] ?? '') ?></b><?= !empty($x['issuer']) ? ' · ' . e($x['issuer']) : '' ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

    <?php endif; ?>
</section>