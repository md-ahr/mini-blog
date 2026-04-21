<?php
/**
 * @var string $pageTitle
 * @var string $heading
 * @var string|null $subheading
 * @var string $dashboardNav
 * @var array<string, mixed> $profile
 * @var string $flashSuccess
 * @var string $flashError
 * @var string $csrfToken
 */
require_once base_path('views/dashboard/partials/head.php');
require_once base_path('views/dashboard/partials/sidebar.php');

$h = static fn(string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');

$profileUrl = blog_url('dashboard/profile');
$flashSuccess = $flashSuccess ?? '';
$flashError = $flashError ?? '';
$csrfToken = $csrfToken ?? '';
$p = $profile;

$pageActions = <<<HTML
<button type="submit" form="profile-account-form"
        class="inline-flex items-center justify-center rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
    Save profile
</button>
HTML;

$actionBtn = 'rounded-lg border border-stone-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-stone-800 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
?>

<?php require_once base_path('views/dashboard/partials/page-header.php'); ?>

<?php if ($flashSuccess !== '') : ?>
    <div class="mb-6 rounded-xl border border-emerald-200/90 bg-emerald-50/70 px-4 py-3 text-sm font-medium text-emerald-900 ring-1 ring-emerald-100/80"
         role="status">
        <?= $h($flashSuccess) ?>
    </div>
<?php endif; ?>
<?php if ($flashError !== '') : ?>
    <div class="mb-6 rounded-xl border border-red-200/90 bg-red-50/70 px-4 py-3 text-sm font-medium text-red-900 ring-1 ring-red-100/80"
         role="alert">
        <?= $h($flashError) ?>
    </div>
<?php endif; ?>

<div class="grid gap-6 xl:grid-cols-12">
    <section class="xl:col-span-5" aria-labelledby="profile-card-heading">
        <div class="rounded-2xl border border-stone-200/90 bg-white p-6 shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
            <h2 id="profile-card-heading" class="sr-only">Profile summary</h2>
            <div class="flex flex-col items-center text-center">
                <div class="relative">
                    <?php if (($p['avatar_url'] ?? '') !== '') : ?>
                        <img src="<?= $h((string)$p['avatar_url']) ?>"
                             alt="<?= $h((string)($p['avatar_alt_display'] ?? '')) ?>"
                             width="96"
                             height="96"
                             class="h-24 w-24 rounded-full object-cover ring-4 ring-white shadow-md"/>
                    <?php else : ?>
                        <div class="flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-stone-200 to-stone-400 text-2xl font-semibold text-stone-900 ring-4 ring-white shadow-md"
                             aria-hidden="true"><?= $h((string)($p['initials'] ?? '?')) ?></div>
                    <?php endif; ?>
                    <button type="button" data-modal-open="modal-profile-add"
                            class="absolute -bottom-1 -right-1 rounded-full border border-stone-200 bg-white p-2 text-stone-700 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                            aria-label="Upload profile photo">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                            <circle cx="12" cy="13" r="3"/>
                        </svg>
                    </button>
                </div>
                <p class="mt-4 text-lg font-semibold text-stone-900"><?= $h((string)$p['name']) ?></p>
                <p class="mt-1 text-sm text-stone-600"><?= $h((string)$p['email']) ?></p>
                <p class="mt-3 inline-flex items-center gap-2 rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-800 ring-1 ring-stone-200/80">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                    <?= $h((string)($p['role_display'] ?? '')) ?>
                </p>
                <p class="mt-4 text-xs leading-relaxed text-stone-500">
                    Member since <span
                            class="font-medium text-stone-700"><?= $h((string)($p['member_since_display'] ?? '—')) ?></span>
                    · Last login <span
                            class="font-medium text-stone-700"><?= $h((string)($p['last_login_display'] ?? '—')) ?></span>
                </p>
            </div>
            <div class="mt-6 border-t border-stone-100 pt-6">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-stone-500">Sessions</h3>
                <ul class="mt-3 space-y-2 text-sm text-stone-700" role="list">
                    <li class="flex items-center justify-between gap-3 rounded-xl bg-stone-50 px-3 py-2 ring-1 ring-stone-200/80">
                        <span class="min-w-0 truncate">This browser · current session</span>
                        <span class="shrink-0 text-xs font-semibold text-emerald-800">Current</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <section class="xl:col-span-7 space-y-6" aria-labelledby="profile-details-heading">
        <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
            <h2 id="profile-details-heading" class="text-sm font-semibold text-stone-900">Account</h2>
            <p class="mt-1 text-xs text-stone-500">Display name, email, bio, and photo URL are stored in the
                database.</p>
            <form id="profile-account-form" class="mt-5 grid gap-4 sm:grid-cols-2" method="post"
                  action="<?= $h($profileUrl) ?>">
                <input type="hidden" name="_csrf" value="<?= $h($csrfToken) ?>"/>
                <input type="hidden" name="_action" value="account"/>
                <div class="sm:col-span-2">
                    <label for="profile-name" class="block text-xs font-semibold text-stone-700">Display name</label>
                    <input id="profile-name" name="name" type="text" value="<?= $h((string)$p['name']) ?>"
                           autocomplete="name" required maxlength="191"
                           class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                </div>
                <div class="sm:col-span-2">
                    <label for="profile-email" class="block text-xs font-semibold text-stone-700">Email</label>
                    <input id="profile-email" name="email" type="email" value="<?= $h((string)$p['email']) ?>"
                           autocomplete="email" required maxlength="191"
                           class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                </div>
                <div class="sm:col-span-2">
                    <label for="profile-avatar-url" class="block text-xs font-semibold text-stone-700">Profile photo
                        URL</label>
                    <input id="profile-avatar-url" name="avatar_url" type="url"
                           value="<?= $h((string)($p['avatar_url'] ?? '')) ?>" maxlength="500" placeholder="https://…"
                           class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                    <p class="mt-1 text-xs text-stone-500">Paste an image URL, or use the camera button on your avatar to upload (JPEG, PNG, WebP, or GIF, up to <?= (int) (PROFILE_AVATAR_MAX_BYTES / 1024 / 1024) ?> MB).</p>
                </div>
                <div class="sm:col-span-2">
                    <label for="profile-avatar-alt-page" class="block text-xs font-semibold text-stone-700">Photo alt
                        text</label>
                    <input id="profile-avatar-alt-page" name="avatar_alt" type="text"
                           value="<?= $h((string)($p['avatar_alt'] ?? '')) ?>" maxlength="191" autocomplete="off"
                           class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                </div>
                <div class="sm:col-span-2">
                    <label for="profile-bio" class="block text-xs font-semibold text-stone-700">Bio</label>
                    <textarea id="profile-bio" name="bio" rows="4" maxlength="20000" autocomplete="off"
                              class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"
                              placeholder="Short author bio for bylines."><?= $h((string)($p['bio'] ?? '')) ?></textarea>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
            <h2 class="text-sm font-semibold text-stone-900">Security</h2>
            <p class="mt-1 text-xs text-stone-500">Password changes require your current password.</p>
            <form class="mt-5 grid gap-4 sm:grid-cols-2" method="post" action="<?= $h($profileUrl) ?>">
                <input type="hidden" name="_csrf" value="<?= $h($csrfToken) ?>"/>
                <input type="hidden" name="_action" value="password"/>
                <div class="sm:col-span-2">
                    <label for="profile-current-password" class="block text-xs font-semibold text-stone-700">Current
                        password</label>
                    <input id="profile-current-password" name="current_password" type="password"
                           autocomplete="current-password" required
                           class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                </div>
                <div>
                    <label for="profile-new-password" class="block text-xs font-semibold text-stone-700">New
                        password</label>
                    <input id="profile-new-password" name="new_password" type="password" autocomplete="new-password"
                           required minlength="8"
                           class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                </div>
                <div>
                    <label for="profile-confirm-password"
                           class="block text-xs font-semibold text-stone-700">Confirm</label>
                    <input id="profile-confirm-password" name="confirm_password" type="password"
                           autocomplete="new-password" required minlength="8"
                           class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                </div>
                <div class="sm:col-span-2 flex flex-wrap gap-2 pt-1">
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                        Update password
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>

<?php require_once base_path('views/dashboard/partials/modals/profile.php'); ?>
<?php require_once base_path('views/dashboard/partials/footer.php'); ?>
