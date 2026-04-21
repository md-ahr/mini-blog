<?php
/**
 * Top bar user menu: avatar, name, email, dropdown (Profile, View site, Log out).
 *
 * @var string $dashboardNav
 */
$dashboardNav = $dashboardNav ?? 'overview';
$u = auth_user();
if ($u === null) {
    return;
}
$email = htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8');
$name = htmlspecialchars((string)($u['name'] ?? ''), ENT_QUOTES, 'UTF-8');
$displayName = $name !== '' ? $name : 'Account';
$src = htmlspecialchars(auth_user_avatar_src($u), ENT_QUOTES, 'UTF-8');
$alt = htmlspecialchars(auth_user_avatar_alt($u), ENT_QUOTES, 'UTF-8');
$profileUrl = htmlspecialchars(blog_url('dashboard/profile'), ENT_QUOTES, 'UTF-8');
$siteUrl = htmlspecialchars(blog_url(), ENT_QUOTES, 'UTF-8');
$logoutUrl = htmlspecialchars(blog_url('logout'), ENT_QUOTES, 'UTF-8');
$menuLabel = 'Account menu for ' . ($name !== '' ? $name : $email);
?>
<details class="group relative">
    <summary
            class="flex cursor-pointer list-none items-center gap-2 rounded-xl border border-stone-200 bg-white py-1.5 pl-1.5 pr-2.5 text-left shadow-sm transition hover:border-stone-300 hover:bg-stone-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white [&::-webkit-details-marker]:hidden"
            aria-label="<?= htmlspecialchars($menuLabel, ENT_QUOTES, 'UTF-8') ?>">
        <img src="<?= $src ?>"
             alt=""
             width="36"
             height="36"
             class="h-9 w-9 shrink-0 rounded-full bg-stone-200 object-cover ring-2 ring-stone-200/80"
             loading="lazy"
             decoding="async"/>
        <div class="hidden min-w-0 max-w-[12rem] text-left sm:block">
            <p class="truncate text-sm font-semibold text-stone-900"><?= $displayName ?></p>
            <p class="truncate text-xs text-stone-500" title="<?= $email ?>"><?= $email ?></p>
        </div>
        <svg class="h-4 w-4 shrink-0 text-stone-400 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="m6 9 6 6 6-6"/>
        </svg>
    </summary>
    <div
            class="absolute right-0 top-full z-50 mt-2 w-[min(17rem,calc(100vw-2rem))] overflow-hidden rounded-xl border border-stone-200 bg-white py-1.5 shadow-lg shadow-stone-900/10 ring-1 ring-stone-900/5"
            role="menu">
        <div class="border-b border-stone-100 px-3 py-2.5 sm:hidden">
            <p class="truncate text-sm font-semibold text-stone-900"><?= $displayName ?></p>
            <p class="truncate text-xs text-stone-500" title="<?= $email ?>"><?= $email ?></p>
        </div>
        <a href="<?= $profileUrl ?>"
           class="flex items-center gap-2 px-3 py-2.5 text-sm font-medium text-stone-800 hover:bg-stone-50 focus:outline-none focus-visible:bg-stone-50 <?= $dashboardNav === 'profile' ? 'bg-stone-50' : '' ?>"
           role="menuitem" <?= $dashboardNav === 'profile' ? 'aria-current="page"' : '' ?>>
            <svg class="h-5 w-5 shrink-0 text-stone-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.75" aria-hidden="true">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
            Profile
        </a>
        <a href="<?= $siteUrl ?>"
           target="_blank"
           class="flex items-center gap-2 px-3 py-2.5 text-sm font-medium text-stone-800 hover:bg-stone-50 focus:outline-none focus-visible:bg-stone-50"
           role="menuitem">
            <svg class="h-5 w-5 shrink-0 text-stone-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.75" aria-hidden="true">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            View site
        </a>
        <div class="my-1 border-t border-stone-100"></div>
        <button type="button"
                role="menuitem"
                data-modal-open="dashboard-confirm-modal"
                data-confirm-title="Sign out?"
                data-confirm-body="You will be signed out and returned to the sign-in page."
                data-confirm-label="Log out"
                data-confirm-variant="primary"
                data-confirm-redirect="<?= $logoutUrl ?>"
                class="flex w-full items-center gap-2 px-3 py-2.5 text-left text-sm font-medium text-stone-800 hover:bg-stone-50 focus:outline-none focus-visible:bg-stone-50">
            <svg class="h-5 w-5 shrink-0 text-stone-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M10 17H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h5"/>
                <path d="M15 12H9"/>
                <path d="m17 8 4 4-4 4"/>
            </svg>
            Log out
        </button>
    </div>
</details>
