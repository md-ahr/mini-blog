<?php
$navBase = defined('BLOG_BASE_PATH') ? rtrim(BLOG_BASE_PATH, '/') . '/' : '';
$homeUrl = $navBase . 'index.php';
$aboutUrl = $navBase . 'about.php';
?>

<header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/90 backdrop-blur-md">
    <nav class="mx-auto flex h-14 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6" aria-label="Primary">
        <a
                href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>"
                class="text-sm font-semibold tracking-tight text-slate-900 transition hover:text-slate-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 focus-visible:ring-offset-white rounded-sm"
        >
            Mini Blog
        </a>

        <ul class="hidden list-none items-center gap-8 md:flex">
            <li>
                <a
                        href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>"
                        class="text-sm font-medium text-slate-600 transition hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 focus-visible:ring-offset-white rounded-sm"
                >
                    Home
                </a>
                <a
                        href="<?= htmlspecialchars($aboutUrl, ENT_QUOTES, 'UTF-8') ?>"
                        class="text-sm font-medium text-slate-600 transition hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 focus-visible:ring-offset-white rounded-sm"
                >
                    About Us</a>
            </li>
        </ul>

        <div class="relative md:hidden">
            <details class="group">
                <summary
                        class="flex cursor-pointer list-none items-center justify-center rounded-lg p-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 focus-visible:ring-offset-white [&::-webkit-details-marker]:hidden"
                        aria-label="Open menu"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" aria-hidden="true">
                        <path d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </summary>
                <div
                        class="absolute right-0 top-full mt-2 min-w-[12rem] rounded-xl border border-slate-200 bg-white py-1 shadow-lg shadow-slate-900/5"
                        role="menu"
                >
                    <a
                            href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>"
                            class="block px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:bg-slate-50"
                            role="menuitem"
                    >
                        Home
                    </a>
                </div>
            </details>
        </div>
    </nav>
</header>
