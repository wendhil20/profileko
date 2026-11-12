<?php
// includes/ui.php — paulit-ulit na Tailwind classes (gamit ng lahat ng page)
if (!defined('ROOT_PATH')) exit('No direct access');

$cls = [
    // layout ng bawat page
    'title'   => 'text-3xl font-bold tracking-widest uppercase bg-linear-to-b from-neutral-900 to-neutral-500 bg-clip-text text-transparent',
    'panel'   => 'p-5 rounded bg-white border border-neutral-200 shadow-sm animate-pane',

    // mga piraso
    'section' => 'text-2xl font-bold text-neutral-900 tracking-wide mb-4',
    'card'    => 'p-4 rounded-md bg-white border border-neutral-200 transition hover:-translate-y-0.5 hover:border-neutral-400 hover:shadow-sm',
    'chip'    => 'inline-block px-2.5 py-0.5 rounded-full font-bold text-neutral-900 bg-neutral-100 border border-neutral-300',
    'hr'      => 'my-4 h-px border-0 bg-linear-to-r from-transparent via-neutral-300 to-transparent',
    'bar'     => 'h-2 rounded-full bg-neutral-200 overflow-hidden',
    'fill'    => 'block h-full rounded-full bg-linear-to-r from-neutral-900 to-neutral-500 transition-[width] duration-700',

    // forms (admin)
    'label'   => 'mb-1.5 block text-sm font-semibold text-neutral-700',
    'input'   => 'w-full rounded-lg border border-neutral-200 bg-white px-3.5 py-2.5 text-[15px] text-neutral-900 outline-none transition
                  placeholder:text-neutral-400 hover:border-neutral-300 focus:border-neutral-900 focus:ring-4 focus:ring-neutral-900/5
                  aria-[invalid=true]:border-red-400 aria-[invalid=true]:bg-red-50/40',
    'btn'     => 'inline-flex items-center justify-center gap-2 rounded-lg bg-neutral-900 px-4 py-2.5 font-semibold text-white transition
                  hover:bg-neutral-700 active:scale-[.98] disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer',
    'box'     => 'rounded-xl border border-neutral-200 bg-white',
];