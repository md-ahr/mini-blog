<?php
$path = blog_current_path();
$isHome = $path === '/' || $path === '';
$isAbout = $path === '/about';
$isContact = $path === '/contact';
$isBlog = $path === '/blogs' || str_starts_with($path, '/blogs/');
$isLogin = $path === '/login';
$homeUrl = blog_url();
$aboutUrl = blog_url('about');
$contactUrl = blog_url('contact');
$blogUrl = blog_url('blogs');
$loginUrl = blog_url('login');

$linkBase = 'inline-flex items-center rounded-md px-3 py-2 text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50';
$linkIdle = 'text-stone-600 hover:bg-stone-100/80 hover:text-stone-900';
$linkActive = 'bg-stone-200/70 text-stone-900';
?>

<header class="sticky top-0 z-50 border-b border-stone-200/90 bg-stone-50/85 backdrop-blur-md">
    <div class="mx-auto flex h-[4.25rem] max-w-6xl items-center justify-between gap-6 px-4 sm:px-6 lg:px-8">
        <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>"
           class="group flex items-center gap-3 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-stone-900 text-sm font-semibold tracking-tight text-amber-50 shadow-sm ring-1 ring-stone-900/10 transition group-hover:bg-stone-800"
                  aria-hidden="true">M</span>
            <span class="flex flex-col leading-tight">
                <span class="text-sm font-semibold tracking-tight text-stone-900">Mini Blog</span>
                <span class="text-xs font-medium text-stone-500">Notes &amp; long-form</span>
            </span>
        </a>

        <nav class="hidden items-center gap-1 md:flex" aria-label="Primary">
            <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>"
               class="<?= $linkBase ?> <?= $isHome ? $linkActive : $linkIdle ?>"
                    <?= $isHome ? 'aria-current="page"' : '' ?>>
                Home
            </a>
            <a href="<?= htmlspecialchars($blogUrl, ENT_QUOTES, 'UTF-8') ?>"
               class="<?= $linkBase ?> <?= $isBlog ? $linkActive : $linkIdle ?>"
                    <?= $isBlog ? 'aria-current="page"' : '' ?>>
                Blog
            </a>
            <a href="<?= htmlspecialchars($aboutUrl, ENT_QUOTES, 'UTF-8') ?>"
               class="<?= $linkBase ?> <?= $isAbout ? $linkActive : $linkIdle ?>"
                    <?= $isAbout ? 'aria-current="page"' : '' ?>>
                About
            </a>
            <a href="<?= htmlspecialchars($contactUrl, ENT_QUOTES, 'UTF-8') ?>"
               class="<?= $linkBase ?> <?= $isContact ? $linkActive : $linkIdle ?>"
                    <?= $isContact ? 'aria-current="page"' : '' ?>>
                Contact
            </a>
        </nav>

        <div class="relative md:hidden">
            <details class="group">
                <summary
                        class="flex cursor-pointer list-none items-center justify-center rounded-lg border border-stone-200 bg-white p-2.5 text-stone-700 shadow-sm transition hover:border-stone-300 hover:bg-stone-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50 [&::-webkit-details-marker]:hidden"
                        aria-label="Open menu">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" aria-hidden="true">
                        <path d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </summary>
                <div
                        class="absolute right-0 top-full z-50 mt-2 min-w-[12.5rem] overflow-hidden rounded-xl border border-stone-200 bg-white py-1.5 shadow-lg shadow-stone-900/10 ring-1 ring-stone-900/5"
                        role="menu">
                    <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>"
                       class="block px-4 py-2.5 text-sm font-medium <?= $isHome ? 'bg-stone-50 text-stone-900' : 'text-stone-700 hover:bg-stone-50' ?> focus:outline-none focus-visible:bg-stone-50"
                       role="menuitem"
                            <?= $isHome ? 'aria-current="page"' : '' ?>>
                        Home
                    </a>
                    <a href="<?= htmlspecialchars($blogUrl, ENT_QUOTES, 'UTF-8') ?>"
                       class="block px-4 py-2.5 text-sm font-medium <?= $isBlog ? 'bg-stone-50 text-stone-900' : 'text-stone-700 hover:bg-stone-50' ?> focus:outline-none focus-visible:bg-stone-50"
                       role="menuitem"
                            <?= $isBlog ? 'aria-current="page"' : '' ?>>
                        Blog
                    </a>
                    <a href="<?= htmlspecialchars($aboutUrl, ENT_QUOTES, 'UTF-8') ?>"
                       class="block px-4 py-2.5 text-sm font-medium <?= $isAbout ? 'bg-stone-50 text-stone-900' : 'text-stone-700 hover:bg-stone-50' ?> focus:outline-none focus-visible:bg-stone-50"
                       role="menuitem"
                            <?= $isAbout ? 'aria-current="page"' : '' ?>>
                        About
                    </a>
                    <a href="<?= htmlspecialchars($contactUrl, ENT_QUOTES, 'UTF-8') ?>"
                       class="block px-4 py-2.5 text-sm font-medium <?= $isContact ? 'bg-stone-50 text-stone-900' : 'text-stone-700 hover:bg-stone-50' ?> focus:outline-none focus-visible:bg-stone-50"
                       role="menuitem"
                            <?= $isContact ? 'aria-current="page"' : '' ?>>
                        Contact
                    </a>
                </div>
            </details>
        </div>
    </div>
</header>
