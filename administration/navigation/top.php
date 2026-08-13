<?php
// client/navigation/top.php - Tailwind Control-Center nav
$currentUrl = $_SERVER['REQUEST_URI'];

$role = $_SESSION['role'] ?? '';
$position = $_SESSION['position'] ?? POSITION_STAFF;
$isHead = $position === POSITION_HEAD;
$isStaff = $position === POSITION_STAFF;


?>

<div id="ccNavOverlay"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 z-[9997]">
</div>

<div id="ccNavPanel" class="fixed top-0 left-1/2 -translate-x-1/2 w-[90%] max-w-sm z-[9998] ease-out"
    style="transform: translateY(-9999px); transition: none;">

    <!-- Menu content -->
    <div id="ccNavContent" class="bg-neutral-900 rounded-b-3xl ring-1 ring-white/10 p-5 pb-6">
        <div class="grid grid-cols-2 gap-3 text-sm text-white">

            <?php if (in_array($role, [ROLE_HR])): ?>
                 <?php if ($position === 'staff'): ?>
                    <a href="/"
                        class="flex flex-col items-center justify-center gap-1.5 bg-white/5 hover:bg-white/10 active:bg-white/15 active:scale-95 rounded-2xl px-3 py-4 transition-all duration-150">
                        <span class="text-xl">🏠</span>
                        <span class="font-medium">Home</span>
                    </a>
                    <a href="/about"
                        class="flex flex-col items-center justify-center gap-1.5 bg-white/5 hover:bg-white/10 active:bg-white/15 active:scale-95 rounded-2xl px-3 py-4 transition-all duration-150">
                        <span class="text-xl">ℹ️</span>
                        <span class="font-medium">About</span>
                    </a>
                    <a href="/services"
                        class="flex flex-col items-center justify-center gap-1.5 bg-white/5 hover:bg-white/10 active:bg-white/15 active:scale-95 rounded-2xl px-3 py-4 transition-all duration-150">
                        <span class="text-xl">🛠️</span>
                        <span class="font-medium">Services</span>
                    </a>
                    <a href="/contact"
                        class="flex flex-col items-center justify-center gap-1.5 bg-white/5 hover:bg-white/10 active:bg-white/15 active:scale-95 rounded-2xl px-3 py-4 transition-all duration-150">
                        <span class="text-xl">✉️</span>
                        <span class="font-medium">Contact</span>
                    </a>

                <?php endif; ?>
            <?php endif; ?>


        </div>
    </div>

    <!-- Handle -->
    <div id="ccNavHandle"
        class="w-16 h-6 bg-neutral-900 rounded-b-2xl shadow-lg shadow-black/40 mx-auto flex items-center justify-center cursor-pointer hover:bg-neutral-800 transition-colors">
        <span class="w-8 h-1 bg-white/40 rounded-full"></span>
    </div>
</div>

<script>
    (() => {
        const handle = document.getElementById('ccNavHandle');
        const panel = document.getElementById('ccNavPanel');
        const content = document.getElementById('ccNavContent');
        const overlay = document.getElementById('ccNavOverlay');
        let isOpen = false;
        let startY = 0;

        const applyClosedPosition = () => {
            const contentHeight = content.offsetHeight;
            panel.style.transform = `translateY(-${contentHeight}px)`;
        };

        const toggle = (open) => {
            if (open) {
                panel.style.transform = 'translateY(0)';
            } else {
                applyClosedPosition();
            }
            overlay.classList.toggle('opacity-0', !open);
            overlay.classList.toggle('pointer-events-none', !open);
            isOpen = open;
        };

        // 1. Set the real closed position instantly, with NO transition
        applyClosedPosition();

        // 2. Only enable the transition AFTER the initial position is set,
        //    so it doesn't animate on page load
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                panel.style.transition = 'transform 300ms ease-out';
            });
        });

        handle.onclick = () => toggle(!isOpen);
        overlay.onclick = () => toggle(false);

        handle.addEventListener('touchstart', e => startY = e.touches[0].clientY, { passive: true });
        handle.addEventListener('touchmove', e => {
            if (e.touches[0].clientY - startY > 40) toggle(true);
        }, { passive: true });

        panel.addEventListener('touchstart', e => startY = e.touches[0].clientY, { passive: true });
        panel.addEventListener('touchend', e => {
            if (startY - e.changedTouches[0].clientY > 40) toggle(false);
        });

        window.addEventListener('resize', () => { if (!isOpen) applyClosedPosition(); });
    })();
</script>