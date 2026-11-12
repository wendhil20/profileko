// assets/js/app.js
// Binabago lang ng JS ang data-* attributes (data-active, data-state, data-show).
// Ang itsura ay naka-Tailwind sa HTML: hal. data-[active=true]:block

document.addEventListener('DOMContentLoaded', () => {
    countUp(document);
    initTechStage();
    initToast();
    initShare();
    initGithubGraph();
    initIdSwitch();
});

/* =========================================================
   1. COUNT UP — mga numerong umaakyat mula 0 (data-count)
   ========================================================= */
function countUp(scope) {
    scope.querySelectorAll('[data-count]').forEach(el => {
        const target = parseInt(el.dataset.count, 10) || 0;
        const start  = performance.now();

        function step(now) {
            const progress = Math.min((now - start) / 1200, 1);
            const eased    = 1 - Math.pow(1 - progress, 3);   // ease-out
            el.textContent = Math.round(target * eased).toLocaleString();
            if (progress < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    });
}

/* =========================================================
   2. HERO STAGE — nagpapalitan ang mga project (o PHP / Tailwind / JS)
   ========================================================= */
function initTechStage() {
    const stage = document.getElementById('mlStage');
    if (!stage) return;

    const items = stage.querySelectorAll('.ml-tech');
    const dots  = stage.querySelectorAll('[data-go]');
    const bars  = stage.querySelectorAll('.ml-progress');
    const info  = document.getElementById('techInfo');
    const name  = document.getElementById('techName');
    const role  = document.getElementById('techRole');

    let current = 0;
    let timer   = null;
    const DELAY = 3500; // ms bawat palit

    // Pinupuno ang pager ng kasalukuyang logo habang naghihintay
    function runProgress() {
        bars.forEach((b, i) => {
            b.style.transition = 'none';
            b.style.width = '0%';
            if (i === current) {
                void b.offsetWidth;                         // restart
                b.style.transition = `width ${DELAY}ms linear`;
                b.style.width = '100%';
            }
        });
    }

    function freezeProgress() {
        const b = bars[current];
        if (!b) return;
        b.style.width = getComputedStyle(b).width;
        b.style.transition = 'none';
    }

    function show(index) {
        if (index === current) return;
        const prev = items[current];
        const next = items[index];

        // lumang logo → lalabas paitaas, tapos babalik sa "idle"
        prev.dataset.state = 'leaving';
        setTimeout(() => { if (prev.dataset.state === 'leaving') prev.dataset.state = 'idle'; }, 700);

        // bagong logo → papasok
        next.dataset.state = 'active';

        // pangalan at role
        name.textContent  = next.dataset.name;
        role.textContent  = next.dataset.role;

        // i-replay ang pop animation ng text
        info.classList.remove('animate-pop');
        void info.offsetWidth;
        info.classList.add('animate-pop');

        current = index;
    }

    function autoplay() {
        clearInterval(timer);
        runProgress();
        timer = setInterval(() => {
            show((current + 1) % items.length);
            runProgress();
        }, DELAY);
    }

    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            show(parseInt(dot.dataset.go, 10));
            autoplay(); // i-reset ang timer pagkatapos mag-click
        });
    });

    // I-pause kapag naka-hover sa stage
    stage.addEventListener('mouseenter', () => { clearInterval(timer); freezeProgress(); });
    stage.addEventListener('mouseleave', autoplay);

    autoplay();
}

/* =========================================================
   3. TOAST — "Open for opportunities"
   ========================================================= */
function initToast() {
    const toast = document.getElementById('mlToast');
    if (!toast) return;
    setTimeout(() => toast.dataset.show = true, 800);
    setTimeout(() => toast.dataset.show = false, 4500);
}

function flash(text) {
    const toast = document.getElementById('mlToast');
    if (!toast) return;
    document.getElementById('mlToastText').textContent = text;
    toast.dataset.show = true;
    setTimeout(() => toast.dataset.show = false, 2000);
}

/* =========================================================
   4. SHARE BUTTON
   ========================================================= */
function initShare() {
    const btn = document.getElementById('mlShare');
    if (!btn) return;

    btn.addEventListener('click', async () => {
        const data = { title: document.title, url: location.href.split('#')[0] };
        try {
            if (navigator.share) {
                await navigator.share(data);          // phone: native share sheet
            } else {
                await navigator.clipboard.writeText(data.url);
                flash('Link copied!');
            }
        } catch (e) { /* kinansela ng user */ }
    });
}

/* =========================================================
   5. GITHUB GRAPH — i-scroll sa pinakabagong linggo (para sa mobile)
   ========================================================= */
function initGithubGraph() {
    const graph = document.getElementById('ghGraph');
    if (graph) graph.scrollLeft = graph.scrollWidth;
}

/* =========================================================
   6. ID SWITCH — Profile (normal) / ID (itsurang ID card)
   ========================================================= */
function initIdSwitch() {
    const view = document.getElementById('mlView');
    if (!view) return;

    const buttons = document.querySelectorAll('[data-view-btn]');
    const title   = document.getElementById('mlTitle');
    const card    = document.getElementById('idCard');

    // Palitan ang view: 'profile' o 'id'
    function setView(name) {
        view.dataset.view = name;
        buttons.forEach(b => b.dataset.active = String(b.dataset.viewBtn === name));
        if (title) title.textContent = name === 'id' ? 'ID CARD' : 'PROFILE';
        if (card) card.dataset.flipped = 'false';        // laging harap ang unang kita
    }

    buttons.forEach(b => b.addEventListener('click', () => setView(b.dataset.viewBtn)));

    // Baliktarin ang ID kapag pinindot (o Enter / Space sa keyboard)
    if (card) {
        const flip = () => card.dataset.flipped = String(card.dataset.flipped !== 'true');
        card.addEventListener('click', flip);
        card.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); flip(); }
        });
    }
}