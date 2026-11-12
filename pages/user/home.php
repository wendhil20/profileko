<?php
// pages/home.php — game-style profile (inspired by ML profile screen)
if (!defined('ROOT_PATH'))
    exit('No direct access');

/* =========================================================
 |  DATA — dito mo papalitan ang laman
 * ========================================================= */
$profile = [
    'name' => 'Wendhil Himarangan',
    'role' => 'Jr. Full Stack Web Developer',
    'photo' => 'profile.png',
];

// Short background — dito mo papalitan ang laman
$about = [
    'summary' => 'Junior full stack web developer building real systems used daily at a construction company. I use AI-assisted development (Claude) as part of my workflow. I plan the features, guide the build, review and test every change, and ship it to production.',
    'builds' => [
        ['title' => 'Accounting systems', 'text' => 'Accounting, budget requests, cash vouchers and purchasing with multi-level approvals and e-signatures.'],
        ['title' => 'CRM & project flow', 'text' => 'Client inquiry, site visit, 2D & quotation, contracts with online client signing, and project tracking.'],
        ['title' => 'E-commerce', 'text' => 'An online store for construction materials with cart, checkout and online payments.'],
    ],
];


// ID card — lalabas kapag pinindot ang "ID" sa switch (itaas-kanan). Dito mo papalitan ang laman.
$idCard = [
    'id_no'  => 'WH-2025-001',
    'email'  => 'wendhil10@gmail.com',
    'github' => 'github.com/' . env('GITHUB_USER', ''),
    'rows'   => [                                   // harap ng ID
        'Course' => 'BS Information Technology',
        'School' => 'Bestlink College of the Philippines',
        'Stack'  => 'PHP · Tailwind · JS · MySQL',
        'Status' => 'Open for opportunities',
    ],
];

// Barcode sa ibaba ng ID — galing sa ID number kaya hindi nagbabago kada refresh
$barcode = [];
foreach (str_split(md5($idCard['id_no'])) as $hex) {
    $barcode[] = (hexdec($hex) % 3) + 1;            // kapal ng bawat guhit: 1 hanggang 3px
}

// Mga logo — iikot lang sa "hero stage" kapag wala pang project sa admin
$techs = [
    ['name' => 'PHP', 'icon' => 'tech/php.svg', 'color' => '#171717', 'filter' => '[filter:drop-shadow(0_0_2px_rgba(0,0,0,.9))_drop-shadow(0_0_2px_rgba(0,0,0,.9))_drop-shadow(0_8px_16px_rgba(0,0,0,.35))]!', 'tile' => 'bg-neutral-900', 'role' => 'Backend'],
    ['name' => 'Tailwind CSS', 'icon' => 'tech/tailwind.svg', 'color' => '#171717', 'filter' => '', 'tile' => 'bg-neutral-50', 'role' => 'Styling'],
    ['name' => 'JavaScript', 'icon' => 'tech/javascript.svg', 'color' => '#171717', 'filter' => '', 'tile' => 'bg-neutral-50', 'role' => 'Frontend'],
];

// Featured projects — galing sa HomeController (pinakabagong projects sa admin)
// Kapag wala pang project, ang mga tech logo sa itaas ang iikot.
$featured     = $featured ?? [];
$showProjects = !empty($featured);

// Tech stack na naka-list — [pangalan, devicon na pangalan, (optional) level gaya ng 'Basic']
// Hanapin ang ibang logo sa https://devicon.dev (hal. 'bootstrap/bootstrap-original')
$stack = [
    'Frontend' => [['HTML', 'html5/html5-original'], ['CSS', 'css3/css3-original'], ['JavaScript', 'javascript/javascript-original'], ['Tailwind CSS', 'tailwindcss/tailwindcss-original']],
    'Backend' => [['PHP', 'php/php-original'], ['Laravel', 'laravel/laravel-original', 'Basic Knowledge']],
    'Database' => [['MySQL', 'mysql/mysql-original'], ['Supabase', 'supabase/supabase-original', 'Realtime']],
    'Tools' => [['Git', 'git/git-original'], ['GitHub', 'github/github-original'], ['VS Code', 'vscode/vscode-original'], ['Claude AI', 'claude/claude-original']],
];
if (!function_exists('devicon')) {
    function devicon(string $name): string
    {
        return 'https://cdn.jsdelivr.net/gh/devicons/devicon@v2.18.0/icons/' . $name . '.svg';
    }
}

// GitHub dashboard — galing sa config/github.php (naka-cache ng 1 oras)
$github = github_stats();

// Kulay ng heatmap boxes (level 0 hanggang 4) — kapareho ng GitHub green
$heat = ['bg-[#ebedf0]', 'bg-[#9be9a8]', 'bg-[#40c463]', 'bg-[#30a14e]', 'bg-[#216e39]'];

// Kulay ng bawat language — kapareho ng ginagamit ng GitHub
$langColors = [
    'PHP' => '#777bb4',
    'JavaScript' => '#f1e05a',
    'TypeScript' => '#3178c6',
    'CSS' => '#563d7c',
    'HTML' => '#e34c26',
    'Blade' => '#f7523f',
    'Hack' => '#878787',
    'Python' => '#3572a5',
    'Java' => '#b07219',
    'C#' => '#178600',
    'Vue' => '#41b883',
    'Shell' => '#89e051',
];

?>

<!-- ================= BACKGROUND ================= -->
<div class="fixed inset-0 -z-10 overflow-hidden
            bg-[radial-gradient(ellipse_at_78%_55%,rgba(0,0,0,.05),transparent_50%),linear-gradient(180deg,#ffffff,#f5f5f5)]"
    aria-hidden="true">
    <!-- grid lines -->
    <div class="absolute inset-0 bg-[linear-gradient(rgba(0,0,0,.05)_1px,transparent_1px),linear-gradient(90deg,rgba(0,0,0,.05)_1px,transparent_1px)] bg-size-[56px_56px]
                [mask-image:radial-gradient(ellipse_at_70%_60%,#000_20%,transparent_70%)]"></div>
    <!-- particles -->
    <?php for ($p = 0; $p < 24; $p++): ?>
        <span class="absolute -bottom-2 size-0.5 rounded-full bg-neutral-300 opacity-70 animate-rise"
            style="left:<?= rand(0, 100) ?>%; animation-delay:-<?= rand(0, 120) / 10 ?>s; animation-duration:<?= rand(80, 160) / 10 ?>s"></span>
    <?php endfor; ?>
</div>

<!-- ================= TOAST ================= -->
<div id="mlToast" data-show="false" class="fixed top-18 lg:top-4 left-1/2 z-50 flex items-center gap-2 px-5 py-2 whitespace-nowrap text-lg font-semibold text-white
            bg-neutral-900 border border-neutral-900 pointer-events-none
            -translate-x-1/2 -translate-y-[300%] data-[show=true]:translate-y-0 transition duration-500">
    <span class="size-2 rounded-full bg-white animate-pulse"></span>
    <span id="mlToastText">Open for opportunities</span>
</div>

<!-- Top bar -->
<header class="flex items-center justify-between gap-3 px-4 pt-3 pb-1 lg:px-5 lg:pt-4 lg:pb-2">
    <h1 id="mlTitle"
        class="text-xl lg:text-3xl font-bold tracking-[.25em] lg:tracking-widest bg-linear-to-b from-neutral-900 to-neutral-500 bg-clip-text text-transparent">
        PROFILE</h1>

    <!-- Switch: Profile (normal) / ID (itsurang ID card) -->
    <?php
    $switchBtn = 'flex items-center gap-1.5 rounded-full px-3 py-1.5 text-sm font-semibold text-neutral-500 transition cursor-pointer
                  hover:text-neutral-900 data-[active=true]:bg-neutral-900 data-[active=true]:text-white';
    ?>
    <div class="flex shrink-0 items-center gap-1 rounded-full border border-neutral-200 bg-white p-1 shadow-sm" role="group"
        aria-label="View">
        <button type="button" data-view-btn="profile" data-active="true" class="<?= $switchBtn ?>">
            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="8" r="4" />
                <path d="M4 21a8 8 0 0116 0" />
            </svg>
            Profile
        </button>
        <button type="button" data-view-btn="id" data-active="false" class="<?= $switchBtn ?>">
            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="4" width="18" height="16" rx="2" />
                <circle cx="9" cy="10" r="2" />
                <path d="M5.5 16a3.5 3.5 0 017 0M15 9h3M15 13h3" />
            </svg>
            ID
        </button>
    </div>
</header>

<!-- data-view="profile" (normal) o "id" (ID card) — pinapalitan ng switch sa itaas -->
<div id="mlView" data-view="profile"
    class="group/view flex-1 min-h-0 grid grid-cols-1 gap-3 px-3 pb-4 sm:px-4 lg:gap-4 lg:grid-cols-[minmax(0,1.15fr)_minmax(0,.85fr)]">

    <!-- ============ LEFT: PANELS ============ -->
    <!-- sa ID mode: sakop ang buong taas at may tuldok-tuldok na background -->
    <div class="order-2 lg:order-1 lg:self-start lg:max-h-full overflow-y-auto p-4 sm:p-5 rounded
                    bg-white
                    [scrollbar-width:thin] [scrollbar-color:rgba(0,0,0,.25)_transparent]
                    lg:group-data-[view=id]/view:self-stretch
                    group-data-[view=id]/view:bg-[radial-gradient(circle,rgba(0,0,0,.07)_1px,transparent_1.5px),linear-gradient(#fff,#fff)]
                    group-data-[view=id]/view:bg-size-[18px_18px]">

        <!-- ============ ID CARD (kita lang kapag naka-"ID" ang switch) ============ -->
        <section class="hidden flex-col items-center -mt-4 pb-2 sm:-mt-5 lg:min-h-[calc(100%+1.25rem)] group-data-[view=id]/view:flex animate-pane">

            <!-- lanyard: nakasabit mula sa itaas ng panel hanggang sa card -->
            <div class="flex min-h-12 flex-col items-center lg:flex-1" aria-hidden="true">
                <span class="w-6 flex-1 bg-neutral-900"></span>
                <span class="h-4 w-9 shrink-0 rounded-b-md border-2 border-t-0 border-neutral-400 bg-neutral-200"></span>
                <span class="h-3 w-1 shrink-0 bg-neutral-400"></span>
            </div>

            <!-- card: pindutin para baliktarin (data-flipped) -->
            <div id="idCard" data-flipped="false" role="button" tabindex="0" aria-label="Flip ID card"
                class="group/card -mt-1 w-64 shrink-0 cursor-pointer perspective-[1400px] sm:w-72 lg:w-80">
                <div class="relative aspect-[54/86] transition-transform duration-700 ease-[cubic-bezier(.2,.8,.2,1)] transform-3d
                            group-data-[flipped=true]/card:rotate-y-180">

                    <!-- ===== HARAP ===== -->
                    <div class="absolute inset-0 flex flex-col overflow-hidden rounded-2xl border border-neutral-200 bg-white backface-hidden
                                shadow-[0_24px_48px_-16px_rgba(0,0,0,.3)]">

                        <!-- itim na ulo: butas ng lanyard + logo -->
                        <div class="relative flex flex-col items-center bg-neutral-900 px-4 pt-7 pb-12 text-white">
                            <span class="absolute top-2.5 left-1/2 h-1.5 w-10 -translate-x-1/2 rounded-full bg-white/25"></span>
                            <img src="<?= img('logo.png') ?>" alt="" class="h-7 w-auto max-w-[120px] object-contain lg:h-8">
                            <span class="mt-1.5 text-[10px] font-semibold tracking-[.35em] uppercase text-neutral-400">Developer
                                ID</span>
                        </div>

                        <!-- picture -->
                        <img src="<?= img($profile['photo']) ?>" alt="<?= e($profile['name']) ?>"
                            class="relative mx-auto -mt-9 size-20 rounded-xl border-4 border-white bg-neutral-100 object-cover object-top shadow-md sm:size-24 lg:size-28">

                        <!-- pangalan -->
                        <div class="mt-2 px-4 text-center">
                            <b class="block text-lg font-bold leading-tight text-neutral-900 lg:text-xl"><?= e($profile['name']) ?></b>
                            <span
                                class="mt-1 inline-block rounded-full bg-neutral-900 px-2.5 py-0.5 text-[10px] font-semibold tracking-wider uppercase text-white"><?= e($profile['role']) ?></span>
                        </div>

                        <!-- detalye -->
                        <dl class="mx-4 mt-3 divide-y divide-neutral-200 border-y border-neutral-200 text-[11px] leading-tight sm:text-xs lg:mx-5 lg:text-[13px]">
                            <?php foreach ($idCard['rows'] as $label => $value): ?>
                                <div class="grid grid-cols-[52px_1fr] gap-2 py-1.5 lg:grid-cols-[60px_1fr] lg:py-2">
                                    <dt class="font-semibold tracking-wider uppercase text-muted"><?= e($label) ?></dt>
                                    <dd class="font-semibold text-neutral-900"><?= e($value) ?></dd>
                                </div>
                            <?php endforeach; ?>
                        </dl>

                        <!-- barcode + ID number -->
                        <div class="mt-auto px-4 pb-3 pt-2 text-center">
                            <div class="flex h-7 items-stretch justify-center gap-[2px]" aria-hidden="true">
                                <?php foreach ($barcode as $w): ?>
                                    <span class="bg-neutral-900" style="width: <?= $w ?>px"></span>
                                <?php endforeach; ?>
                            </div>
                            <span
                                class="mt-1 block text-[10px] font-semibold tracking-[.3em] text-neutral-500"><?= e($idCard['id_no']) ?></span>
                        </div>
                    </div>

                    <!-- ===== LIKOD ===== -->
                    <div class="absolute inset-0 flex flex-col overflow-hidden rounded-2xl bg-neutral-900 text-white backface-hidden rotate-y-180
                                shadow-[0_24px_48px_-16px_rgba(0,0,0,.3)]">
                        <span class="mx-auto mt-2.5 h-1.5 w-10 rounded-full bg-white/25"></span>
                        <span class="mt-4 block h-8 bg-black"></span>

                        <div class="flex flex-1 flex-col px-5 pt-4 pb-4">
                            <p class="text-[10px] font-semibold tracking-[.3em] uppercase text-neutral-400">What I build</p>
                            <ul class="mt-2 space-y-1.5 text-sm font-semibold">
                                <?php foreach ($about['builds'] as $b): ?>
                                    <li class="flex items-center gap-2">
                                        <span class="size-1.5 shrink-0 rounded-full bg-white"></span><?= e($b['title']) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <p class="mt-4 text-[10px] font-semibold tracking-[.3em] uppercase text-neutral-400">Contact</p>
                            <p class="mt-1.5 text-xs leading-relaxed break-all text-neutral-200">
                                <?= e($idCard['email']) ?><br><?= e($idCard['github']) ?>
                            </p>

                            <!-- pirma -->
                            <div class="mt-auto">
                                <span class="block text-xl italic font-semibold leading-none"><?= e($profile['name']) ?></span>
                                <span
                                    class="mt-1.5 block border-t border-white/30 pt-1 text-[10px] font-semibold tracking-[.3em] uppercase text-neutral-400">Signature</span>
                            </div>

                            <div class="mt-3 flex items-center justify-between">
                                <img src="<?= img('logo.png') ?>" alt="" class="h-5 w-auto max-w-[90px] object-contain">
                                <span class="text-[10px] font-semibold tracking-[.3em] text-neutral-400"><?= e($idCard['id_no']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <p class="mt-4 flex items-center gap-1.5 text-xs font-semibold text-muted">
                <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 12a9 9 0 0115.5-6.2L21 8M21 3v5h-5M21 12a9 9 0 01-15.5 6.2L3 16m0 5v-5h5" />
                </svg>
                Click the card to flip
            </p>

            <!-- espasyo sa ibaba para nasa gitna ang ID (desktop) -->
            <div class="hidden lg:block lg:flex-1"></div>
        </section>

        <!-- PROFILE (normal na itsura) -->
        <section class="animate-pane group-data-[view=id]/view:hidden">
            <div class="flex items-center gap-4 sm:items-start sm:gap-5">
                <!-- Avatar — lapad lang ang naka-set; ang taas ay susunod sa totoong sukat ng picture -->
                <div class="relative shrink-0 overflow-hidden rounded-2xl p-1 shadow-md">
                    <img src="<?= img($profile['photo']) ?>" alt="<?= e($profile['name']) ?>"
                        class="relative block w-20 sm:w-28 h-auto rounded-xl bg-neutral-100">
                </div>

                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl sm:text-3xl font-bold leading-tight text-neutral-900"><?= e($profile['name']) ?>
                    </h2>
                    <p class="text-base sm:text-lg text-muted"><?= e($profile['role']) ?></p>
                </div>
            </div>

            <hr class="<?= $cls['hr'] ?>">

            <!-- ============ BACKGROUND ============ -->
            <div class="animate-pop mb-2">
                <h3 class="flex items-center gap-2 mb-3 text-xl font-bold">
                    <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M16 18l6-6-6-6M8 6l-6 6 6 6" />
                    </svg>
                    Background
                </h3>

                <p class="text-neutral-700 leading-relaxed"><?= e($about['summary']) ?></p>

                <!-- What I build -->
                <p class="mt-4 mb-2 text-sm font-semibold">What I build</p>
                <ul class="grid gap-3 sm:grid-cols-3">
                    <?php foreach ($about['builds'] as $b): ?>
                        <li class="<?= $cls['card'] ?> py-3">
                            <b class="block font-bold text-neutral-900"><?= e($b['title']) ?></b>
                            <span class="block mt-1 text-sm text-muted leading-snug"><?= e($b['text']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- ============ GITHUB DASHBOARD ============ -->
            <div class="animate-pop">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="flex items-center gap-2 text-xl font-bold">
                        <svg viewBox="0 0 24 24" class="size-5" fill="currentColor" aria-hidden="true">
                            <path
                                d="M12 .5a11.5 11.5 0 00-3.64 22.41c.58.1.79-.25.79-.56v-2c-3.2.7-3.88-1.37-3.88-1.37-.53-1.33-1.280-1.69-1.28-1.69-1.05-.71.08-.7.08-.7 1.16.08 1.77 1.19 1.77 1.19 1.03 1.77 2.71 1.26 3.37.96.1-.75.4-1.26.73-1.55-2.55-.29-5.24-1.28-5.24-5.69 0-1.26.45-2.29 1.19-3.1-.12-.29-.52-1.46.11-3.05 0 0 .97-.31 3.17 1.18a11 11 0 015.77 0c2.2-1.49 3.17-1.18 3.17-1.18.63 1.59.23 2.76.11 3.05.74.81 1.19 1.84 1.19 3.1 0 4.42-2.7 5.4-5.26 5.68.41.36.78 1.06.78 2.14v3.17c0 .31.21.67.8.56A11.5 11.5 0 0012 .5z" />
                        </svg>
                        GitHub Activity
                    </h3>
                 
                </div>

                <?php if (!$github): ?>
                    <p class="<?= $cls['card'] ?> text-center text-muted">
                        Hindi makuha ang GitHub data. Ilagay ang <code class="font-bold text-neutral-900">GITHUB_USER</code>
                        sa <code>.env</code>.
                    </p>
                <?php else: ?>


                    <!-- Contribution graph -->
                    <?php if (!empty($github['weeks'])): ?>
                        <div class="<?= $cls['card'] ?> hover:translate-y-0 mb-4">
                            <div id="ghGraph" class="overflow-x-auto [scrollbar-width:thin] pb-1">
                                <div class="flex gap-[2px] w-max">
                                    <!-- araw ng linggo -->
                                    <div class="flex flex-col gap-[2px] pr-1 text-[10px] leading-[10px] text-muted">
                                        <span class="h-3"></span>
                                        <?php foreach (['', 'Mon', '', 'Wed', '', 'Fri', ''] as $dayLabel): ?>
                                            <span class="h-[10px]"><?= $dayLabel ?></span>
                                        <?php endforeach; ?>
                                    </div>

                                    <?php $prevMonth = null;
                                    foreach ($github['weeks'] as $w => $week): ?>
                                        <?php
                                        $month = date('M', strtotime($week[0]['date']));
                                        $showMonth = $month !== $prevMonth && ($w > 0 || $week[0]['weekday'] === 0);
                                        $prevMonth = $month;
                                        $padTop = $w === 0 ? $week[0]['weekday'] : 0;   // unang linggo: puwedeng hindi nagsimula sa Linggo
                                        ?>
                                        <div class="flex flex-col gap-[2px]">
                                            <span
                                                class="h-3 text-[10px] leading-3 text-muted whitespace-nowrap"><?= $showMonth ? $month : '' ?></span>
                                            <?php for ($i = 0; $i < $padTop; $i++): ?>
                                                <span class="size-[10px]"></span>
                                            <?php endfor; ?>
                                            <?php foreach ($week as $day): ?>
                                                <span class="size-[10px] rounded-[2px] <?= $heat[$day['level']] ?>"
                                                    title="<?= (int) $day['count'] ?> contribution<?= $day['count'] == 1 ? '' : 's' ?> — <?= date('M j, Y', strtotime($day['date'])) ?>"></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-2 text-xs text-muted">
                                <span><?= number_format($github['contributions']) ?> contributions in the last year</span>
                                <span class="flex items-center gap-1">
                                    Less
                                    <?php foreach ($heat as $h): ?><span
                                            class="size-[10px] rounded-[2px] <?= $h ?>"></span><?php endforeach; ?>
                                    More
                                </span>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="<?= $cls['card'] ?> hover:translate-y-0 mb-4 text-sm text-center text-muted">
                            Lalabas ang contribution graph kapag may <code
                                class="font-bold text-neutral-900">GITHUB_TOKEN</code> sa <code>.env</code>.
                        </p>
                    <?php endif; ?>

                    <!-- Top languages -->
                    <?php if (!empty($github['languages'])): ?>
                        <p class="mb-2 text-sm font-semibold">Top Languages</p>
                        <div class="flex h-2.5 rounded-full overflow-hidden bg-neutral-100">
                            <?php foreach ($github['languages'] as $i => $lang): ?>
                                <span
                                    style="width: <?= $lang['percent'] ?>%; background: <?= $langColors[$lang['name']] ?? '#a3a3a3' ?>"
                                    title="<?= e($lang['name']) ?> <?= $lang['percent'] ?>%"></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2 text-sm">
                            <?php foreach ($github['languages'] as $i => $lang): ?>
                                <span class="flex items-center gap-1.5">
                                    <span class="size-2.5 rounded-full"
                                        style="background: <?= $langColors[$lang['name']] ?? '#a3a3a3' ?>"></span>
                                    <?= e($lang['name']) ?> <span class="text-muted"><?= $lang['percent'] ?>%</span>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>
            </div>

            <hr class="<?= $cls['hr'] ?>">

            <!-- ============ TECH STACK (list) ============ -->
            <div class="animate-pop">
                <h3 class="flex items-center gap-2 mb-3 text-xl font-bold">
                    <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 2l9 5-9 5-9-5 9-5zM3 12l9 5 9-5M3 17l9 5 9-5" />
                    </svg>
                    Tech Stack
                </h3>

                <dl class="divide-y divide-neutral-200 border-y border-neutral-200">
                    <?php foreach ($stack as $group => $items): ?>
                        <div class="grid gap-2 py-3 sm:grid-cols-[120px_1fr] sm:items-center">
                            <dt class="text-sm font-semibold text-muted"><?= e($group) ?></dt>
                            <dd class="flex flex-wrap gap-2">
                                <?php foreach ($items as $t): ?>
                                    <span
                                        class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-md border border-neutral-200 bg-white text-sm font-medium text-neutral-800 transition hover:border-neutral-900">
                                        <img src="<?= e(devicon($t[1])) ?>" alt="" width="18" height="18" loading="lazy"
                                            class="size-[18px] object-contain">
                                        <?= e($t[0]) ?>
                                        <?php if (!empty($t[2])): ?>
                                            <span
                                                class="px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider text-neutral-500 bg-neutral-100"><?= e($t[2]) ?></span>
                                        <?php endif; ?>
                                    </span>
                                <?php endforeach; ?>
                            </dd>
                        </div>
                    <?php endforeach; ?>
                </dl>
            </div>
        </section>

    </div>

    <!-- ============ RIGHT: HERO STAGE ============ -->
    <div id="mlStage"
        class="order-1 lg:order-2 relative flex items-center gap-4 px-1 py-2 lg:flex-col lg:justify-center lg:gap-0 lg:px-0 lg:min-h-0 lg:py-6">

        <?php
        // Galaw ng bawat slide (project man o logo) — ang JS ay nagpapalit lang ng data-state
        $slideCls = 'ml-tech absolute inset-0 m-0 pointer-events-none opacity-0 translate-y-4 blur-sm
                     transition-all duration-700 ease-[cubic-bezier(.2,.8,.2,1)]
                     data-[state=active]:pointer-events-auto data-[state=active]:opacity-100 data-[state=active]:translate-y-0 data-[state=active]:scale-100 data-[state=active]:blur-none
                     data-[state=leaving]:-translate-y-4';
        ?>

        <?php if ($showProjects): ?>
            <!-- ===== FEATURED PROJECTS: nagpapalitang screenshot ng mga project ===== -->
            <div class="relative grid place-items-center w-36 shrink-0 sm:w-56 lg:w-[min(72%,440px)]">

                <!-- spotlight + umiikot na rings sa likod (desktop) -->
                <div class="pointer-events-none absolute left-1/2 top-1/2 hidden size-[400px] -translate-x-1/2 -translate-y-1/2 lg:block"
                    aria-hidden="true">
                    <div
                        class="absolute inset-0 rounded-full bg-[radial-gradient(circle,#ffffff_0%,#f4f4f5_55%,transparent_72%)]">
                    </div>
                    <div
                        class="absolute inset-0 rounded-full border border-dashed border-neutral-300 animate-[spin_40s_linear_infinite]">
                        <span class="absolute -top-1 left-1/2 -translate-x-1/2 size-2 rounded-full bg-neutral-900"></span>
                    </div>
                    <div
                        class="absolute inset-[12%] rounded-full border border-neutral-200 animate-[spin_24s_linear_infinite_reverse]">
                        <span
                            class="absolute top-1/2 -right-[3px] -translate-y-1/2 size-1.5 rounded-full bg-neutral-400"></span>
                    </div>
                </div>

                <!-- mga screenshot (naka-frame na parang browser window) -->
                <div class="relative z-10 w-full aspect-[16/10]">
                    <?php foreach ($featured as $i => $p): ?>
                        <figure data-state="<?= $i === 0 ? 'active' : 'idle' ?>" data-name="<?= e($p['name']) ?>"
                            data-role="Featured Project"
                            class="<?= $slideCls ?> scale-95 data-[state=leaving]:scale-105">
                            <a href="<?= url('projects') ?>" title="<?= e($p['name']) ?>"
                                class="flex h-full flex-col overflow-hidden rounded-lg border border-neutral-200 bg-white
                                       shadow-[0_20px_40px_-12px_rgba(0,0,0,.25)] transition hover:border-neutral-400 animate-float">
                                <!-- title bar ng browser -->
                                <span
                                    class="flex shrink-0 items-center gap-1 border-b border-neutral-200 bg-neutral-100 px-2 py-1 lg:gap-1.5 lg:px-3 lg:py-2">
                                    <span class="size-1.5 rounded-full bg-neutral-300 lg:size-2"></span>
                                    <span class="size-1.5 rounded-full bg-neutral-300 lg:size-2"></span>
                                    <span class="size-1.5 rounded-full bg-neutral-300 lg:size-2"></span>
                                </span>
                                <img src="<?= img($p['image']) ?>" alt="<?= e($p['name']) ?>"
                                    class="min-h-0 w-full flex-1 object-cover object-top">
                            </a>
                        </figure>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php else: ?>
            <!-- ===== Wala pang project: mga tech logo ang iikot ===== -->
            <div class="relative grid place-items-center size-32 shrink-0 sm:size-40 lg:size-[min(78vw,340px)]">
                <div
                    class="absolute inset-0 rounded-full bg-[radial-gradient(circle,#ffffff_0%,#f4f4f5_55%,transparent_72%)]">
                </div>
                <div class="absolute inset-[9%] rounded-full border border-neutral-200"></div>

                <!-- umiikot na rings na may tuldok -->
                <div
                    class="absolute inset-0 rounded-full border border-dashed border-neutral-300 animate-[spin_40s_linear_infinite]">
                    <span class="absolute -top-1 left-1/2 -translate-x-1/2 size-2 rounded-full bg-neutral-900"></span>
                </div>
                <div
                    class="absolute inset-[22%] rounded-full border border-neutral-200 animate-[spin_24s_linear_infinite_reverse]">
                    <span
                        class="absolute top-1/2 -right-[3px] -translate-y-1/2 size-1.5 rounded-full bg-neutral-400"></span>
                </div>

                <!-- mga logo -->
                <div class="relative z-10 size-[44%]">
                    <?php foreach ($techs as $i => $t): ?>
                        <figure data-state="<?= $i === 0 ? 'active' : 'idle' ?>" data-name="<?= e($t['name']) ?>"
                            data-role="<?= e($t['role']) ?>"
                            class="<?= $slideCls ?> grid place-items-center scale-75 data-[state=leaving]:scale-110">
                            <img src="<?= img($t['icon']) ?>" alt="<?= e($t['name']) ?>"
                                class="w-full h-full object-contain animate-float <?= e($t['filter']) ?> drop-shadow-[0_12px_20px_rgba(0,0,0,.12)]">
                        </figure>
                    <?php endforeach; ?>
                </div>

                <!-- anino sa ilalim ng logo -->
                <div class="absolute bottom-[19%] left-1/2 -translate-x-1/2 w-[28%] h-3 rounded-[50%] bg-black/10 blur-md">
                </div>
            </div>
        <?php endif; ?>

        <!-- Info: pangalan ng project (o ng tech) -->
        <?php
        $slides    = $showProjects ? $featured : $techs;
        $firstRole = $showProjects ? 'Featured Project' : $techs[0]['role'];
        $nameCls   = $showProjects ? 'text-base leading-snug lg:text-2xl line-clamp-2' : 'text-xl lg:text-4xl';
        ?>
        <div id="techInfo"
            class="relative z-10 flex-1 min-w-0 lg:flex-none lg:mt-6 lg:w-full lg:max-w-sm lg:text-center">
            <span id="techRole"
                class="inline-block whitespace-nowrap px-2 py-0.5 lg:px-3 lg:py-1 rounded-full border border-neutral-200 bg-white text-[10px] lg:text-xs font-semibold tracking-[.2em] uppercase text-muted">
                <?= e($firstRole) ?>
            </span>
            <h3 id="techName" class="mt-1.5 lg:mt-3 font-bold tracking-tight text-neutral-900 <?= $nameCls ?>">
                <?= e($slides[0]['name']) ?></h3>

            <!-- Pager: napupuno habang naghihintay ng susunod -->
            <div class="mt-1 lg:mt-4 flex gap-1.5 lg:justify-center lg:gap-2">
                <?php foreach ($slides as $i => $s): ?>
                    <button type="button" data-go="<?= $i ?>" aria-label="<?= e($s['name']) ?>" class="py-2 cursor-pointer">
                        <span class="relative block h-1 w-6 lg:w-10 rounded-full bg-neutral-200 overflow-hidden">
                            <span class="ml-progress absolute inset-y-0 left-0 w-0 rounded-full bg-neutral-900"></span>
                        </span>
                    </button>
                <?php endforeach; ?>
            </div>

            <?php if ($showProjects): ?>
                <a href="<?= url('projects') ?>"
                    class="inline-flex items-center gap-1 text-xs font-semibold text-neutral-500 transition hover:text-neutral-900 lg:mt-2 lg:text-sm">
                    View all projects
                    <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14m-6-6l6 6-6 6" />
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>