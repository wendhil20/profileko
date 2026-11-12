<?php
// pages/user/projects.php — VIEW lang (walang logic) — coverflow carousel ng mga project
// Galing sa PublicProjectController: $projects (bawat isa may 'images' = listahan ng URL)
if (!defined('ROOT_PATH')) exit('No direct access');
?>
<header class="px-5 pt-4 pb-2">
    <h1 class="<?= $cls['title'] ?>">Projects</h1>
</header>

<div class="px-4 pb-6">
    <?php if (!$projects): ?>
        <section class="<?= $cls['panel'] ?> py-16 text-center text-muted">
            Wala pang project na naka-post.
        </section>
    <?php else: ?>
        <?php
            // Itsura ng bawat card depende sa pwesto nito (data-pos) — ang JS ay nagpapalit lang ng data-pos
            $slideCls = 'group relative col-start-1 row-start-1 w-[min(74%,640px)] cursor-pointer overflow-hidden rounded-xl border border-neutral-200 bg-neutral-100
                         transition-all duration-500 ease-[cubic-bezier(.2,.8,.2,1)]
                         data-[pos=center]:z-30 data-[pos=center]:shadow-[0_30px_60px_-20px_rgba(0,0,0,.45)]
                         data-[pos=left]:z-20 data-[pos=left]:-translate-x-[88%] data-[pos=left]:scale-[.72] data-[pos=left]:opacity-60 data-[pos=left]:hover:opacity-90
                         data-[pos=right]:z-20 data-[pos=right]:translate-x-[88%] data-[pos=right]:scale-[.72] data-[pos=right]:opacity-60 data-[pos=right]:hover:opacity-90
                         data-[pos=far-left]:z-10 data-[pos=far-left]:-translate-x-[150%] data-[pos=far-left]:scale-50 data-[pos=far-left]:opacity-25
                         data-[pos=far-right]:z-10 data-[pos=far-right]:translate-x-[150%] data-[pos=far-right]:scale-50 data-[pos=far-right]:opacity-25
                         data-[pos=hidden]:pointer-events-none data-[pos=hidden]:scale-50 data-[pos=hidden]:opacity-0';
            $arrowCls = 'absolute top-1/2 z-40 grid size-10 -translate-y-1/2 place-items-center rounded-full border border-neutral-200 bg-white text-neutral-900 shadow-md
                         transition hover:border-neutral-900 cursor-pointer lg:size-11';
            $many     = count($projects) > 1;
        ?>

        <!-- ============ COVERFLOW: malaki ang nasa gitna, maliit ang nasa gilid ============ -->
        <section id="pcFlow" class="animate-pop" aria-roledescription="carousel" aria-label="Projects">

            <div class="relative">
                <!-- lahat ng card ay nasa iisang grid cell; inuusog lang sila ng data-pos -->
                <div id="pcStage" class="grid place-items-center overflow-hidden px-2 pt-4 pb-20">
                    <?php foreach ($projects as $i => $pr): ?>
                        <?php
                            // Lahat ng kailangan ng info sa ibaba at ng detail modal, naka-JSON sa isang attribute
                            $detail = [
                                'name'        => $pr['name'],
                                'description' => (string) $pr['description'],
                                'excerpt'     => trim(strtok((string) $pr['description'], "\n") ?: ''),
                                'date'        => date('F Y', strtotime($pr['created_at'])),
                                'images'      => $pr['images'],
                            ];
                            $pos = $i === 0 ? 'center' : ($i === 1 ? 'right' : ($i === count($projects) - 1 ? 'left' : 'hidden'));
                        ?>
                        <article data-project="<?= e(json_encode($detail)) ?>" data-pos="<?= $pos ?>"
                                 class="<?= $slideCls ?>">
                            <img src="<?= e($pr['images'][0]) ?>" alt="<?= e($pr['name']) ?>" <?= $i > 2 ? 'loading="lazy"' : '' ?>
                                 class="aspect-[16/10] w-full object-cover object-top">
                            <?php if (count($pr['images']) > 1): ?>
                                <span class="absolute bottom-2.5 right-2.5 inline-flex items-center gap-1 rounded-full bg-neutral-900/85 px-2.5 py-1 text-xs font-semibold text-white">
                                    <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="M21 15l-5-5L5 21"/></svg>
                                    <?= count($pr['images']) ?>
                                </span>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if ($many): ?>
                    <button type="button" data-flow-prev aria-label="Previous project" class="<?= $arrowCls ?> left-1 -mt-8 lg:left-3">
                        <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                    </button>
                    <button type="button" data-flow-next aria-label="Next project" class="<?= $arrowCls ?> right-1 -mt-8 lg:right-3">
                        <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                    </button>
                <?php endif; ?>
            </div>

            <!-- Info ng project na nasa gitna -->
            <?php $first = $projects[0]; ?>
            <div id="pcInfo" class="mx-auto -mt-12 max-w-2xl px-2 text-center">
                <?php if ($many): ?>
                    <!-- tuldok: isa bawat project -->
                    <div id="pcDots" class="mb-3 flex justify-center gap-1.5">
                        <?php foreach ($projects as $i => $pr): ?>
                            <button type="button" data-flow-go="<?= $i ?>" data-active="<?= $i === 0 ? 'true' : 'false' ?>"
                                    aria-label="<?= e($pr['name']) ?>"
                                    class="h-1.5 w-1.5 rounded-full bg-neutral-300 transition-all cursor-pointer hover:bg-neutral-500
                                           data-[active=true]:w-6 data-[active=true]:bg-neutral-900"></button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <h2 id="pcName" class="text-2xl font-bold leading-tight lg:text-3xl"><?= e($first['name']) ?></h2>
                <p id="pcExcerpt" class="mx-auto mt-2 line-clamp-2 max-w-xl text-muted"><?= e(trim(strtok((string) $first['description'], "\n") ?: '')) ?></p>

                <div class="mt-4 flex items-center justify-center gap-4">
                    <span id="pcDate" class="text-sm text-neutral-400"><?= e(date('F Y', strtotime($first['created_at']))) ?></span>
                    <button type="button" id="pcOpen"
                            class="inline-flex items-center gap-2 rounded-lg bg-neutral-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-neutral-700 cursor-pointer">
                        View details
                        <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </button>
                </div>
            </div>
        </section>
    <?php endif; ?>
</div>

<!-- ============ PROJECT DETAIL (larawan + buong description) ============ -->
<dialog id="projectModal" aria-labelledby="pmTitle"
        class="m-auto h-[min(92vh,760px)] w-[min(96vw,1200px)] max-w-none overflow-hidden rounded-xl bg-white p-0 shadow-2xl
               backdrop:bg-neutral-950/80 backdrop:backdrop-blur-sm open:grid grid-rows-[45%_minmax(0,1fr)]
               lg:grid-rows-none lg:grid-cols-[minmax(0,1fr)_380px]">

    <!-- Kaliwa: larawan -->
    <div class="relative flex min-h-0 flex-col bg-neutral-950">
        <div class="relative flex min-h-0 flex-1 items-center justify-center">
            <img id="pmImg" alt="" class="max-h-full w-full object-contain">

            <button type="button" data-prev aria-label="Previous image"
                    class="absolute left-3 top-1/2 grid size-10 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-neutral-900 shadow transition hover:bg-white cursor-pointer">
                <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            </button>
            <button type="button" data-next aria-label="Next image"
                    class="absolute right-3 top-1/2 grid size-10 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-neutral-900 shadow transition hover:bg-white cursor-pointer">
                <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
            </button>
            <span id="pmCount" class="absolute bottom-3 left-3 rounded-full bg-neutral-900/80 px-2.5 py-1 text-xs font-semibold text-white tabular-nums"></span>
        </div>

        <!-- thumbnails -->
        <div id="pmThumbs" class="flex shrink-0 gap-2 overflow-x-auto border-t border-white/10 p-3"></div>
    </div>

    <!-- Kanan: buong detalye -->
    <div class="flex min-h-0 flex-col">
        <div class="flex items-start justify-between gap-4 border-b border-neutral-200 p-5">
            <div class="min-w-0">
                <h2 id="pmTitle" class="text-2xl font-bold leading-tight"></h2>
                <p id="pmDate" class="mt-1 text-sm text-neutral-400"></p>
            </div>
            <button type="button" data-close aria-label="Close"
                    class="grid size-9 shrink-0 place-items-center rounded-full border border-neutral-200 transition hover:border-neutral-900 cursor-pointer">
                <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>

        <!-- buong description — naka-scroll kapag mahaba -->
        <div class="min-h-0 flex-1 overflow-y-auto p-5 [scrollbar-width:thin]">
            <div id="pmDesc"></div>
        </div>
    </div>
</dialog>

<script>
// Coverflow ng mga project + detail modal: pindutin ang card sa gitna → larawan (next/prev/thumbnails/arrow keys) + buong description
(() => {
    const dlg    = document.getElementById('projectModal');
    const imgEl  = document.getElementById('pmImg');
    const count  = document.getElementById('pmCount');
    const thumbs = document.getElementById('pmThumbs');
    const prev   = dlg.querySelector('[data-prev]');
    const next   = dlg.querySelector('[data-next]');
    let images = [], index = 0;

    function show(i) {
        index = (i + images.length) % images.length;
        imgEl.src = images[index];
        count.textContent = `${index + 1} / ${images.length}`;
        thumbs.querySelectorAll('button').forEach((b, n) => {
            b.classList.toggle('ring-2', n === index);
            b.classList.toggle('opacity-50', n !== index);
        });
    }

    /*
     * Ginagawang maayos ang description na tinype sa admin:
     *   "Label: paliwanag"  → listahan ng features (bold ang label)
     *   "Features:"         → maliit na heading
     *   "- item"            → bullet
     *   ibang linya         → talata
     * textContent lang ang gamit (hindi innerHTML), kaya ligtas kahit may <script> sa tinype.
     */
    function renderDescription(text, box) {
        box.replaceChildren();
        if (!text || !text.trim()) {
            box.append(make('p', 'text-muted', 'Walang description.'));
            return;
        }

        let para = [], list = null;
        const flushPara = () => {
            if (para.length) box.append(make('p', 'mb-4 text-[17px] leading-relaxed text-neutral-700', para.join(' ')));
            para = [];
        };

        for (const raw of text.replace(/\r/g, '').split('\n')) {
            const line = raw.trim();
            if (!line) { flushPara(); list = null; continue; }

            const heading = line.match(/^([^:]{2,60}):$/);                 // "Features:"
            const bullet  = line.match(/^[-•*]\s+(.+)$/);                  // "- item"
            const body    = bullet ? bullet[1] : line;
            const pair    = body.match(/^([^:]{2,40}):\s+(.+)$/);          // "Label: paliwanag"

            if (heading) {
                flushPara(); list = null;
                box.append(make('h3', 'mb-2 mt-5 text-xs font-bold uppercase tracking-[.18em] text-neutral-400 first:mt-0', heading[1]));
                continue;
            }
            if (pair || bullet) {
                flushPara();
                if (!list) {
                    list = make('ul', 'mb-4 divide-y divide-neutral-100 overflow-hidden rounded-lg border border-neutral-200');
                    box.append(list);
                }
                const li = make('li', 'flex gap-3 px-3.5 py-3');
                li.append(make('span', 'mt-2 size-1.5 shrink-0 rounded-full bg-neutral-900'));
                const content = make('div', 'min-w-0');
                if (pair) {
                    content.append(make('b', 'block font-bold leading-snug text-neutral-900', pair[1]));
                    content.append(make('span', 'mt-0.5 block text-[15px] leading-relaxed text-neutral-600', pair[2]));
                } else {
                    content.append(make('span', 'block text-[15px] leading-relaxed text-neutral-700', body));
                }
                li.append(content);
                list.append(li);
                continue;
            }
            list = null;
            para.push(line);
        }
        flushPara();
    }

    function make(tag, className, text) {
        const el = document.createElement(tag);
        el.className = className;
        if (text !== undefined) el.textContent = text;
        return el;
    }

    function open(data) {
        images = data.images;
        document.getElementById('pmTitle').textContent = data.name;
        document.getElementById('pmDate').textContent  = data.date;
        renderDescription(data.description, document.getElementById('pmDesc'));

        const many = images.length > 1;
        prev.hidden = next.hidden = count.hidden = thumbs.hidden = !many;
        thumbs.innerHTML = '';
        images.forEach((src, n) => {
            const t = document.createElement('button');
            t.type = 'button';
            t.setAttribute('aria-label', `Image ${n + 1}`);
            t.className = 'shrink-0 overflow-hidden rounded-md ring-white transition hover:opacity-100 cursor-pointer';
            t.innerHTML = `<img src="${src}" alt="" class="h-14 w-20 object-cover">`;
            t.addEventListener('click', () => show(n));
            thumbs.appendChild(t);
        });

        show(0);
        dlg.showModal();
        dlg.querySelector('.overflow-y-auto').scrollTop = 0;
    }

    /* ---------- COVERFLOW ---------- */
    const flow = document.getElementById('pcFlow');
    if (flow) {
        const cards   = [...flow.querySelectorAll('[data-project]')];
        const dots    = [...flow.querySelectorAll('[data-flow-go]')];
        const info    = document.getElementById('pcInfo');
        const total   = cards.length;
        let current   = 0;

        const dataOf = i => JSON.parse(cards[i].dataset.project);

        // Pwesto ng bawat card kumpara sa nasa gitna: 0 = gitna, -1 = kaliwa, 1 = kanan, ...
        const POS = { '0': 'center', '-1': 'left', '1': 'right', '-2': 'far-left', '2': 'far-right' };

        function go(i) {
            current = (i + total) % total;

            cards.forEach((card, n) => {
                let offset = (n - current + total) % total;          // 0 .. total-1
                if (offset > total / 2) offset -= total;             // pinakamalapit na direksyon (umiikot)
                card.dataset.pos = POS[offset] ?? 'hidden';
            });
            dots.forEach((d, n) => d.dataset.active = String(n === current));

            // info sa ibaba
            const data = dataOf(current);
            document.getElementById('pcName').textContent    = data.name;
            document.getElementById('pcExcerpt').textContent = data.excerpt;
            document.getElementById('pcDate').textContent    = data.date;
            info.classList.remove('animate-pop');
            void info.offsetWidth;                                   // i-replay ang pop animation
            info.classList.add('animate-pop');
        }

        // Card sa gitna → buksan ang detalye; card sa gilid → dalhin sa gitna
        cards.forEach((card, n) => card.addEventListener('click', () => {
            if (n === current) open(dataOf(n)); else go(n);
        }));
        document.getElementById('pcOpen').addEventListener('click', () => open(dataOf(current)));

        flow.querySelector('[data-flow-prev]')?.addEventListener('click', () => go(current - 1));
        flow.querySelector('[data-flow-next]')?.addEventListener('click', () => go(current + 1));
        dots.forEach((d, n) => d.addEventListener('click', () => go(n)));

        // Arrow keys (kapag sarado ang modal)
        document.addEventListener('keydown', e => {
            if (dlg.open || total < 2) return;
            if (e.key === 'ArrowLeft')  go(current - 1);
            if (e.key === 'ArrowRight') go(current + 1);
        });

        // Swipe sa phone
        let startX = null;
        const stage = document.getElementById('pcStage');
        stage.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
        stage.addEventListener('touchend', e => {
            if (startX === null || total < 2) return;
            const moved = e.changedTouches[0].clientX - startX;
            if (Math.abs(moved) > 40) go(current + (moved < 0 ? 1 : -1));
            startX = null;
        }, { passive: true });
    }

    prev.addEventListener('click', () => show(index - 1));
    next.addEventListener('click', () => show(index + 1));
    dlg.querySelector('[data-close]').addEventListener('click', () => dlg.close());
    dlg.addEventListener('click', e => { if (e.target === dlg) dlg.close(); });   // click sa labas = close
    document.addEventListener('keydown', e => {
        if (!dlg.open || images.length < 2) return;
        if (e.key === 'ArrowLeft')  show(index - 1);
        if (e.key === 'ArrowRight') show(index + 1);
    });
})();
</script>