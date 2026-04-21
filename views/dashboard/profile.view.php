<?php
/**
 * @var string $pageTitle
 * @var string $heading
 * @var string|null $subheading
 * @var string $dashboardNav
 */
require_once base_path('views/dashboard/partials/head.php');
require_once base_path('views/dashboard/partials/sidebar.php');

$pageActions = <<<'HTML'
<button type="button" data-modal-open="modal-profile-add" class="inline-flex items-center justify-center rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
    Upload photo
</button>
<button type="button"
        data-modal-open="modal-profile-view"
        data-profile-name="Alex Rowan"
        data-profile-email="alex@example.com"
        data-profile-role="Owner"
        data-profile-bio="Writes about quiet software, letters, and small tools."
        class="inline-flex items-center justify-center rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
    View public
</button>
<button type="button"
        data-modal-open="modal-profile-edit"
        data-profile-name="Alex Rowan"
        data-profile-email="alex@example.com"
        data-profile-bio="Writes about quiet software, letters, and small tools."
        class="inline-flex items-center justify-center rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
    Edit in modal
</button>
<button type="button"
        data-modal-open="dashboard-confirm-modal"
        data-confirm-title="Save profile?"
        data-confirm-body="Update display name, email, and bio from the page form (demo)."
        data-confirm-label="Save profile"
        data-confirm-variant="primary"
        class="inline-flex items-center justify-center rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
    Save profile
</button>
HTML;
$actionBtn = 'rounded-lg border border-stone-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-stone-800 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
?>

<?php require_once base_path('views/dashboard/partials/page-header.php'); ?>

<div class="grid gap-6 xl:grid-cols-12">
    <section class="xl:col-span-5" aria-labelledby="profile-card-heading">
        <div class="rounded-2xl border border-stone-200/90 bg-white p-6 shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
            <h2 id="profile-card-heading" class="sr-only">Profile summary</h2>
            <div class="flex flex-col items-center text-center">
                <div class="relative">
                    <div class="flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-stone-200 to-stone-400 text-3xl font-semibold text-stone-900 ring-4 ring-white shadow-md"
                         aria-hidden="true">A</div>
                    <button type="button" data-modal-open="modal-profile-add"
                            class="absolute -bottom-1 -right-1 rounded-full border border-stone-200 bg-white p-2 text-stone-700 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                            aria-label="Upload profile photo">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                            <circle cx="12" cy="13" r="3"/>
                        </svg>
                    </button>
                </div>
                <p class="mt-4 text-lg font-semibold text-stone-900">Alex Rowan</p>
                <p class="mt-1 text-sm text-stone-600">alex@example.com</p>
                <p class="mt-3 inline-flex items-center gap-2 rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-800 ring-1 ring-stone-200/80">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                    Owner
                </p>
                <p class="mt-4 text-xs leading-relaxed text-stone-500">
                    Member since <span class="font-medium text-stone-700">Jan 2025</span> · Last login <span class="font-medium text-stone-700">today</span>
                </p>
                <div class="mt-5 flex flex-wrap items-center justify-center gap-2">
                    <button type="button" class="<?= $actionBtn ?>"
                            data-modal-open="modal-profile-view"
                            data-profile-name="Alex Rowan"
                            data-profile-email="alex@example.com"
                            data-profile-role="Owner"
                            data-profile-bio="Writes about quiet software, letters, and small tools.">View</button>
                    <button type="button" class="<?= $actionBtn ?>"
                            data-modal-open="modal-profile-edit"
                            data-profile-name="Alex Rowan"
                            data-profile-email="alex@example.com"
                            data-profile-bio="Writes about quiet software, letters, and small tools.">Edit</button>
                    <button type="button"
                            class="rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-800 shadow-sm hover:border-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/70 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                            data-modal-open="dashboard-confirm-modal"
                            data-confirm-title="Delete your account?"
                            data-confirm-body="This will remove your user, posts attribution, and sessions (demo)."
                            data-confirm-label="Delete account">Delete</button>
                </div>
            </div>
            <div class="mt-6 border-t border-stone-100 pt-6">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-stone-500">Sessions</h3>
                <ul class="mt-3 space-y-2 text-sm text-stone-700" role="list">
                    <li class="flex items-center justify-between gap-3 rounded-xl bg-stone-50 px-3 py-2 ring-1 ring-stone-200/80">
                        <span class="min-w-0 truncate">This device · Safari</span>
                        <span class="shrink-0 text-xs font-semibold text-emerald-800">Current</span>
                    </li>
                    <li class="flex items-center justify-between gap-3 rounded-xl bg-white px-3 py-2 ring-1 ring-stone-200/80">
                        <span class="min-w-0 truncate">Phone · Chrome</span>
                        <button type="button"
                                data-modal-open="dashboard-confirm-modal"
                                data-confirm-title="Revoke session?"
                                data-confirm-body="Sign out the Phone · Chrome session."
                                data-confirm-label="Revoke"
                                class="shrink-0 text-xs font-semibold text-amber-900 underline decoration-amber-300/80 underline-offset-4 hover:text-amber-950 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white rounded-sm">Revoke</button>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <section class="xl:col-span-7 space-y-6" aria-labelledby="profile-details-heading">
        <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
            <h2 id="profile-details-heading" class="text-sm font-semibold text-stone-900">Account</h2>
            <p class="mt-1 text-xs text-stone-500">Update fields when persistence is connected.</p>
            <form class="mt-5 grid gap-4 sm:grid-cols-2" action="#" method="get" onsubmit="return false;">
                <div class="sm:col-span-2">
                    <label for="profile-name" class="block text-xs font-semibold text-stone-700">Display name</label>
                    <input id="profile-name" type="text" value="Alex Rowan" autocomplete="name"
                           class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                </div>
                <div class="sm:col-span-2">
                    <label for="profile-email" class="block text-xs font-semibold text-stone-700">Email</label>
                    <input id="profile-email" type="email" value="alex@example.com" autocomplete="email"
                           class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                </div>
                <div class="sm:col-span-2">
                    <label for="profile-bio" class="block text-xs font-semibold text-stone-700">Bio</label>
                    <textarea id="profile-bio" rows="4" autocomplete="off"
                              class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"
                              placeholder="Short author bio for bylines."></textarea>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
            <h2 class="text-sm font-semibold text-stone-900">Security</h2>
            <p class="mt-1 text-xs text-stone-500">Password changes should require the current password server-side.</p>
            <form class="mt-5 grid gap-4 sm:grid-cols-2" action="#" method="get" onsubmit="return false;">
                <div class="sm:col-span-2">
                    <label for="profile-current-password" class="block text-xs font-semibold text-stone-700">Current password</label>
                    <input id="profile-current-password" type="password" autocomplete="current-password"
                           class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                </div>
                <div>
                    <label for="profile-new-password" class="block text-xs font-semibold text-stone-700">New password</label>
                    <input id="profile-new-password" type="password" autocomplete="new-password"
                           class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                </div>
                <div>
                    <label for="profile-confirm-password" class="block text-xs font-semibold text-stone-700">Confirm</label>
                    <input id="profile-confirm-password" type="password" autocomplete="new-password"
                           class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                </div>
                <div class="sm:col-span-2 flex flex-wrap gap-2 pt-1">
                    <button type="button"
                            data-modal-open="dashboard-confirm-modal"
                            data-confirm-title="Update password?"
                            data-confirm-body="Validate the current password on the server before applying a new one (demo)."
                            data-confirm-label="Update password"
                            data-confirm-variant="primary"
                            class="inline-flex items-center justify-center rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                        Update password
                    </button>
                    <button type="button"
                            data-modal-open="dashboard-confirm-modal"
                            data-confirm-title="Sign out everywhere?"
                            data-confirm-body="Revoke all active sessions except this browser (demo)."
                            data-confirm-label="Sign out"
                            class="inline-flex items-center justify-center rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                        Sign out everywhere
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>

<?php require_once base_path('views/dashboard/partials/modals/profile.php'); ?>
<?php require_once base_path('views/dashboard/partials/footer.php'); ?>
