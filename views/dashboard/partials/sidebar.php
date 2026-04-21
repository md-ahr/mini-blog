<?php
/**
 * @var string $dashboardNav Active section key: overview, posts, tags, categories, comments, users, settings, profile
 */
$dashboardNav = $dashboardNav ?? 'overview';
$navItem = 'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
$navIdle = 'text-stone-600 hover:bg-stone-100 hover:text-stone-900';
$navActive = 'bg-stone-900 text-amber-50 shadow-sm ring-1 ring-stone-900/10';

$navItemsAll = [
    ['key' => 'overview', 'label' => 'Overview', 'href' => blog_url('dashboard'), 'icon' => 'home'],
    ['key' => 'posts', 'label' => 'Posts', 'href' => blog_url('dashboard/posts'), 'icon' => 'doc'],
    ['key' => 'tags', 'label' => 'Tags', 'href' => blog_url('dashboard/tags'), 'icon' => 'tag'],
    ['key' => 'categories', 'label' => 'Categories', 'href' => blog_url('dashboard/categories'), 'icon' => 'folder'],
    ['key' => 'comments', 'label' => 'Comments', 'href' => blog_url('dashboard/comments'), 'icon' => 'chat'],
    ['key' => 'users', 'label' => 'Users', 'href' => blog_url('dashboard/users'), 'icon' => 'users'],
    ['key' => 'settings', 'label' => 'Settings', 'href' => blog_url('dashboard/settings'), 'icon' => 'cog'],
    ['key' => 'profile', 'label' => 'Profile', 'href' => blog_url('dashboard/profile'), 'icon' => 'user'],
];

$sessionRole = auth_user();
$items = ($sessionRole !== null && ($sessionRole['role'] ?? '') === 'owner')
    ? $navItemsAll
    : array_values(array_filter($navItemsAll, static fn (array $it): bool => $it['key'] !== 'users'));

$icon = static function (string $name): string {
    return match ($name) {
        'home' => '<svg class="h-5 w-5 shrink-0 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 10v10h14V10"/></svg>',
        'doc' => '<svg class="h-5 w-5 shrink-0 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 3h7l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/><path d="M14 3v5h5"/><path d="M8 13h8M8 17h8"/></svg>',
        'tag' => '<svg class="h-5 w-5 shrink-0 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 5v6a1 1 0 0 0 .293.707l10 10a1 1 0 0 0 1.414 0l6-6a1 1 0 0 0 0-1.414l-10-10A1 1 0 0 0 9 5H3z"/><path d="M7.5 7.5h.01"/></svg>',
        'folder' => '<svg class="h-5 w-5 shrink-0 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>',
        'chat' => '<svg class="h-5 w-5 shrink-0 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7.5 11h9M7.5 15H12"/><path d="M5 18V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H8l-3 3z"/></svg>',
        'users' => '<svg class="h-5 w-5 shrink-0 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        'cog' => '<svg class="h-5 w-5 shrink-0 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9c0 .69.28 1.35.78 1.83.5.48 1.17.75 1.87.76H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/></svg>',
        'user' => '<svg class="h-5 w-5 shrink-0 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
        default => '',
    };
};
?>

<div class="flex min-h-screen">
    <aside
            class="sticky top-0 hidden h-screen w-64 shrink-0 flex-col border-r border-stone-200/90 bg-white shadow-sm shadow-stone-900/5 lg:flex"
            aria-label="Dashboard">
        <div class="flex h-[4.25rem] items-center gap-3 border-b border-stone-100 px-5">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-stone-900 text-sm font-semibold tracking-tight text-amber-50 shadow-sm ring-1 ring-stone-900/10"
                  aria-hidden="true">M</span>
            <div class="min-w-0 leading-tight">
                <p class="truncate text-sm font-semibold text-stone-900">Mini Blog</p>
                <p class="truncate text-xs font-medium text-stone-500">Dashboard</p>
            </div>
        </div>
        <?php $sessionUserVariant = 'aside'; require base_path('views/dashboard/partials/session-user.php'); ?>
        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4" aria-label="Dashboard sections">
            <?php foreach ($items as $item) : ?>
                <?php
                $isActive = $dashboardNav === $item['key'];
                ?>
                <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                   class="<?= $navItem ?> <?= $isActive ? $navActive : $navIdle ?>"
                    <?= $isActive ? 'aria-current="page"' : '' ?>>
                    <?= $icon($item['icon']) ?>
                    <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="border-t border-stone-100 p-3 space-y-2">
            <a href="<?= htmlspecialchars(blog_url(), ENT_QUOTES, 'UTF-8') ?>"
               class="flex items-center justify-center gap-2 rounded-lg border border-stone-200 bg-stone-50 px-3 py-2.5 text-xs font-semibold text-stone-700 transition hover:border-stone-300 hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" aria-hidden="true">
                    <path d="M15 18 9 12l6-6"/>
                </svg>
                View site
            </a>
            <button type="button"
                    data-modal-open="dashboard-confirm-modal"
                    data-confirm-title="Sign out?"
                    data-confirm-body="You will be signed out and returned to the sign-in page."
                    data-confirm-label="Log out"
                    data-confirm-variant="primary"
                    data-confirm-redirect="<?= htmlspecialchars(blog_url('logout'), ENT_QUOTES, 'UTF-8') ?>"
                    class="flex w-full items-center justify-center gap-2 rounded-lg border border-stone-200 bg-white px-3 py-2.5 text-xs font-semibold text-stone-800 transition hover:border-stone-300 hover:bg-stone-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M10 17H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h5"/>
                    <path d="M15 12H9"/>
                    <path d="m17 8 4 4-4 4"/>
                </svg>
                Log out
            </button>
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-40 border-b border-stone-200/90 bg-stone-100/90 backdrop-blur-md lg:hidden">
            <div class="flex h-14 items-center justify-between gap-2 px-4">
                <a href="<?= htmlspecialchars(blog_url('dashboard'), ENT_QUOTES, 'UTF-8') ?>"
                   class="flex min-w-0 items-center gap-2 font-semibold text-stone-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-100 rounded-md">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-stone-900 text-xs font-semibold text-amber-50"
                          aria-hidden="true">M</span>
                    <span class="truncate">Dashboard</span>
                </a>
                <div class="flex shrink-0 items-center gap-1.5">
                <?php $sessionUserVariant = 'toolbar'; require base_path('views/dashboard/partials/session-user.php'); ?>
                <details class="relative group">
                    <summary
                            class="flex cursor-pointer list-none items-center justify-center rounded-lg border border-stone-200 bg-white p-2 text-stone-700 shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-100 [&::-webkit-details-marker]:hidden"
                            aria-label="Open dashboard menu">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" aria-hidden="true">
                            <path d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </summary>
                    <div
                            class="absolute right-0 top-full z-50 mt-2 w-[min(18rem,calc(100vw-2rem))] overflow-hidden rounded-xl border border-stone-200 bg-white py-1.5 shadow-lg shadow-stone-900/10 ring-1 ring-stone-900/5"
                            role="menu">
                        <?php foreach ($items as $item) : ?>
                            <?php $isActive = $dashboardNav === $item['key']; ?>
                            <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                               class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium <?= $isActive ? 'bg-stone-50 text-stone-900' : 'text-stone-700 hover:bg-stone-50' ?> focus:outline-none focus-visible:bg-stone-50"
                               role="menuitem"
                                <?= $isActive ? 'aria-current="page"' : '' ?>>
                                <?= $icon($item['icon']) ?>
                                <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php endforeach; ?>
                        <div class="my-1 border-t border-stone-100"></div>
                        <a href="<?= htmlspecialchars(blog_url(), ENT_QUOTES, 'UTF-8') ?>"
                           class="block px-4 py-2.5 text-sm font-medium text-stone-700 hover:bg-stone-50 focus:outline-none focus-visible:bg-stone-50"
                           role="menuitem">
                            View site
                        </a>
                        <button type="button"
                                role="menuitem"
                                data-modal-open="dashboard-confirm-modal"
                                data-confirm-title="Sign out?"
                                data-confirm-body="You will be signed out and returned to the sign-in page."
                                data-confirm-label="Log out"
                                data-confirm-variant="primary"
                                data-confirm-redirect="<?= htmlspecialchars(blog_url('logout'), ENT_QUOTES, 'UTF-8') ?>"
                                class="flex w-full items-center gap-2 px-4 py-2.5 text-left text-sm font-medium text-stone-700 hover:bg-stone-50 focus:outline-none focus-visible:bg-stone-50">
                            <svg class="h-5 w-5 shrink-0 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M10 17H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h5"/>
                                <path d="M15 12H9"/>
                                <path d="m17 8 4 4-4 4"/>
                            </svg>
                            Log out
                        </button>
                    </div>
                </details>
                </div>
            </div>
        </header>

        <main id="main-content" tabindex="-1" class="min-w-0 flex-1 px-4 py-8 sm:px-6 lg:px-10">
