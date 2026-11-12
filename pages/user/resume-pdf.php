<?php
// pages/user/resume-pdf.php — HTML for the PDF (rendered by dompdf, not a route)
// Plain inline CSS only: dompdf doesn't run Tailwind. Tables are used for side-by-side layout.
// Variable: $resume (from ResumeController::download)
if (!defined('ROOT_PATH')) exit('No direct access');

$r = $resume;
$h = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');

// Profile photo (same as the home page) — embedded so dompdf doesn't need to fetch a URL
$photo = '';
$photoFile = ROOT_PATH . 'assets/img/profile.png';
if (is_file($photoFile)) {
    $mime  = mime_content_type($photoFile) ?: 'image/png';
    $photo = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($photoFile));
}

$contact = array_filter([
    $r['email'],
    $r['phone'],
    $r['address'],
    $r['linkedin'] ? resume_short_url($r['linkedin']) : '',
    $r['github'] ? resume_short_url($r['github']) : '',
    $r['website'] ? resume_short_url($r['website']) : '',
]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= $h($r['full_name']) ?> — Resume</title>
<style>
    @page { margin: 36px 42px; }
    * { box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; line-height: 1.45; color: #1f1f1f; margin: 0; }

    .name { font-size: 24px; font-weight: bold; color: #0a0a0a; margin: 0; letter-spacing: -0.3px; }
    .role { font-size: 12px; font-weight: bold; color: #525252; margin: 2px 0 0; }
    .photo-cell { width: 82px; padding-right: 14px; vertical-align: middle; }
    .photo { width: 80px; border-radius: 8px; }
    .contact { margin-top: 8px; color: #525252; font-size: 9.5px; }
    .contact span { white-space: normal; }
    .sep { color: #a3a3a3; padding: 0 5px; }

    h2 { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 1.6px; color: #737373;
         margin: 16px 0 8px; padding-bottom: 4px; border-bottom: 1px solid #e5e5e5; }

    table { width: 100%; border-collapse: collapse; }
    td { vertical-align: top; padding: 0; }
    .item { margin-bottom: 10px; page-break-inside: avoid; }
    .item-title { font-size: 11px; font-weight: bold; color: #0a0a0a; }
    .item-sub { font-weight: bold; color: #525252; }
    .item-date { text-align: right; white-space: nowrap; color: #525252; font-weight: bold; width: 120px; }

    .group-title { margin-top: 7px; font-weight: bold; color: #0a0a0a; font-size: 10.5px; }
    ul { margin: 3px 0 0; padding-left: 13px; }
    li { margin-bottom: 2px; }
    .tools { margin-top: 5px; color: #525252; }
    .tools b { color: #1f1f1f; }

    .skills td { padding: 2px 0; }
    .skill-label { width: 130px; font-weight: bold; color: #525252; }
    .summary { color: #333; }
</style>
</head>
<body>

    <!-- Header (photo on the left when assets/img/profile.png exists) -->
    <table>
        <tr>
            <?php if ($photo): ?>
                <td class="photo-cell"><img src="<?= $photo ?>" class="photo" alt=""></td>
            <?php endif; ?>
            <td>
                <p class="name"><?= $h($r['full_name']) ?></p>
                <?php if ($r['title']): ?><p class="role"><?= $h($r['title']) ?></p><?php endif; ?>
                <?php if ($contact): ?>
                    <div class="contact">
                        <?php $first = true; foreach ($contact as $c): ?><?= $first ? '' : '<span class="sep">|</span>' ?><span><?= $h($c) ?></span><?php $first = false; endforeach; ?>
                    </div>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <?php if (trim((string) $r['summary']) !== ''): ?>
        <h2>Summary</h2>
        <div class="summary"><?= nl2br($h($r['summary'])) ?></div>
    <?php endif; ?>

    <!-- Experience -->
    <?php if ($r['experience']): ?>
        <h2>Experience</h2>
        <?php foreach ($r['experience'] as $x): ?>
            <div class="item">
                <table>
                    <tr>
                        <td>
                            <span class="item-title"><?= $h($x['title'] ?? '') ?></span><br>
                            <span class="item-sub"><?= $h($x['company'] ?? '') ?><?= !empty($x['location']) ? ' · ' . $h($x['location']) : '' ?></span>
                        </td>
                        <td class="item-date"><?= $h(resume_period($x)) ?></td>
                    </tr>
                </table>
                <?php foreach (resume_bullet_groups($x['bullets'] ?? []) as $g): ?>
                    <?php if ($g['heading'] !== null): ?><div class="group-title"><?= $h($g['heading']) ?></div><?php endif; ?>
                    <?php if ($g['items']): ?>
                        <ul>
                            <?php foreach ($g['items'] as $b): ?><li><?= $h($b) ?></li><?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if (!empty($x['tools'])): ?>
                    <div class="tools"><b>Tools used:</b> <?= $h($x['tools']) ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Education -->
    <?php if ($r['education']): ?>
        <h2>Education</h2>
        <?php foreach ($r['education'] as $x): ?>
            <div class="item">
                <table>
                    <tr>
                        <td>
                            <span class="item-title"><?= $h($x['degree'] ?? '') ?></span><br>
                            <span class="item-sub"><?= $h($x['school'] ?? '') ?></span>
                            <?php if (!empty($x['location']) || !empty($x['note'])): ?>
                                <br><span><?= $h(implode(' · ', array_filter([$x['location'] ?? '', $x['note'] ?? '']))) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="item-date"><?= $h($x['years'] ?? '') ?></td>
                    </tr>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Skills -->
    <?php if ($r['skills']): ?>
        <h2>Technical Skills</h2>
        <table class="skills">
            <?php foreach ($r['skills'] as $x): ?>
                <tr>
                    <td class="skill-label"><?= $h($x['label'] ?? '') ?></td>
                    <td><?= $h($x['items'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <!-- Certifications -->
    <?php if ($r['certifications']): ?>
        <h2>Certifications</h2>
        <table class="skills">
            <?php foreach ($r['certifications'] as $x): ?>
                <tr>
                    <td><b><?= $h($x['name'] ?? '') ?></b><?= !empty($x['issuer']) ? ' · ' . $h($x['issuer']) : '' ?></td>
                    <td class="item-date"><?= $h($x['year'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

</body>
</html>