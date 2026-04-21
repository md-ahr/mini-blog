<?php
/**
 * @var string $pageTitle
 * @var string $heading
 * @var string|null $subheading
 * @var string $dashboardNav
 * @var array<string, string> $settings
 * @var bool $canEditSettings
 * @var string $flashSuccess
 * @var string $flashError
 * @var string $csrfToken
 * @var string $settingsUrl
 */
require_once base_path('views/dashboard/partials/head.php');
require_once base_path('views/dashboard/partials/sidebar.php');

$settings = $settings ?? blog_settings_defaults();
$canEditSettings = $canEditSettings ?? false;
$settingsUrl = $settingsUrl ?? blog_url('dashboard/settings');
$csrfToken = $csrfToken ?? auth_csrf_token();
$flashSuccess = $flashSuccess ?? '';
$flashError = $flashError ?? '';

$s = static function (string $key) use ($settings): string {
  return htmlspecialchars($settings[$key] ?? '', ENT_QUOTES, 'UTF-8');
};

$homeMode = (string) ($settings['homepage_display'] ?? 'latest_posts');
$isStaticHome = $homeMode === 'static_page';
$rssOn = ($settings['rss_enabled'] ?? '1') === '1';
$rssLabel = $rssOn ? 'Enabled' : 'Disabled';
$commentsOn = ($settings['comments_enabled'] ?? '1') === '1';
$modOn = ($settings['comments_require_moderation'] ?? '1') === '1';
$close30 = ($settings['comments_close_after_30_days'] ?? '0') === '1';

$disabledAttr = $canEditSettings ? '' : ' disabled';

$viewSnapAttrs = sprintf(
  'data-site-title="%s" data-tagline="%s" data-posts-per-page="%s" data-date-format="%s" data-rss="%s"',
  $s('site_title'),
  $s('site_tagline'),
  $s('posts_per_page'),
  $s('date_format'),
  htmlspecialchars($rssLabel, ENT_QUOTES, 'UTF-8')
);
$pageActions = <<<HTML
<button type="button"
        data-modal-open="modal-setting-view"
        {$viewSnapAttrs}
        class="inline-flex items-center justify-center rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
    View snapshot
</button>
HTML;
?>

<?php require_once base_path('views/dashboard/partials/page-header.php'); ?>

<?php if ($flashSuccess !== '') : ?>
    <div class="mb-6 rounded-xl border border-emerald-200/90 bg-emerald-50/90 px-4 py-3 text-sm font-medium text-emerald-950 ring-1 ring-emerald-100"
         role="status">
        <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>
<?php if ($flashError !== '') : ?>
    <div class="mb-6 rounded-xl border border-red-200/90 bg-red-50/90 px-4 py-3 text-sm font-medium text-red-900 ring-1 ring-red-100"
         role="alert">
        <?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if (!$canEditSettings) : ?>
    <p class="mb-6 rounded-xl border border-amber-200/90 bg-amber-50/80 px-4 py-3 text-sm text-amber-950 ring-1 ring-amber-100">
        Only <strong>owners</strong> can edit site settings. You can still review values and use your account options below.
    </p>
<?php endif; ?>

<form method="post" action="<?= htmlspecialchars($settingsUrl, ENT_QUOTES, 'UTF-8') ?>" class="space-y-6">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"/>
    <input type="hidden" name="_action" value="save_settings"/>

    <div class="grid gap-6 lg:grid-cols-12">
        <div class="lg:col-span-7 space-y-6">
            <section class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80" aria-labelledby="site-settings-heading">
                <h2 id="site-settings-heading" class="text-sm font-semibold text-stone-900">Site</h2>
                <p class="mt-1 text-xs text-stone-500">Public-facing strings and listing defaults.</p>
                <div class="mt-5 space-y-4">
                    <div>
                        <label for="site-title" class="block text-xs font-semibold text-stone-700">Site title</label>
                        <input id="site-title" name="site_title" type="text" required maxlength="191" value="<?= $s('site_title') ?>"
                               class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"<?= $disabledAttr ?>/>
                    </div>
                    <div>
                        <label for="site-tagline" class="block text-xs font-semibold text-stone-700">Tagline</label>
                        <input id="site-tagline" name="site_tagline" type="text" maxlength="500" value="<?= $s('site_tagline') ?>"
                               class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"<?= $disabledAttr ?>/>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="posts-per-page" class="block text-xs font-semibold text-stone-700">Posts per page</label>
                            <input id="posts-per-page" name="posts_per_page" type="number" min="1" max="100" value="<?= $s('posts_per_page') ?>"
                                   class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30 tabular-nums"<?= $disabledAttr ?>/>
                        </div>
                        <div>
                            <label for="date-format" class="block text-xs font-semibold text-stone-700">Date format</label>
                            <select id="date-format" name="date_format"
                                    class="<?= htmlspecialchars($dashboardSelect, ENT_QUOTES, 'UTF-8') ?> mt-1.5"<?= $disabledAttr ?>>
                                <option value="M j, Y" <?= ($settings['date_format'] ?? '') === 'M j, Y' ? 'selected' : '' ?>>M j, Y</option>
                                <option value="Y-m-d" <?= ($settings['date_format'] ?? '') === 'Y-m-d' ? 'selected' : '' ?>>Y-m-d</option>
                                <option value="relative" <?= ($settings['date_format'] ?? '') === 'relative' ? 'selected' : '' ?>>Relative</option>
                            </select>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80" aria-labelledby="reading-settings-heading">
                <h2 id="reading-settings-heading" class="text-sm font-semibold text-stone-900">Reading &amp; syndication</h2>
                <p class="mt-1 text-xs text-stone-500">Homepage mode and RSS.</p>
                <div class="mt-5 space-y-4">
                    <fieldset class="<?= $canEditSettings ? '' : 'opacity-70' ?>">
                        <legend class="text-xs font-semibold text-stone-700">Homepage displays</legend>
                        <div class="mt-2 space-y-2">
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-stone-200 bg-stone-50/40 p-3 hover:bg-stone-50/80">
                                <input type="radio" name="homepage_display" value="latest_posts" class="mt-1" <?= !$isStaticHome ? 'checked' : '' ?><?= $disabledAttr ?>/>
                                <span>
                                    <span class="block text-sm font-semibold text-stone-900">Latest posts</span>
                                    <span class="block text-xs text-stone-600">Use the blog index as the home experience.</span>
                                </span>
                            </label>
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-stone-200 bg-white p-3 hover:bg-stone-50/60">
                                <input type="radio" name="homepage_display" value="static_page" class="mt-1" <?= $isStaticHome ? 'checked' : '' ?><?= $disabledAttr ?>/>
                                <span>
                                    <span class="block text-sm font-semibold text-stone-900">Static page slug</span>
                                    <span class="block text-xs text-stone-600">Placeholder for future routing — store a slug for now.</span>
                                </span>
                            </label>
                        </div>
                    </fieldset>
                    <div>
                        <label for="homepage-static-slug" class="block text-xs font-semibold text-stone-700">Static page slug</label>
                        <input id="homepage-static-slug" name="homepage_static_slug" type="text" value="<?= $s('homepage_static_slug') ?>"
                               placeholder="e.g. welcome"
                               class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30 font-mono text-xs"<?= $disabledAttr ?>/>
                    </div>
                    <label class="flex items-center justify-between gap-4 rounded-xl border border-stone-200 bg-white px-4 py-3 shadow-sm">
                        <span>
                            <span class="block text-sm font-semibold text-stone-900">RSS feed</span>
                            <span class="block text-xs text-stone-600">When enabled, expose a feed when the theme supports it.</span>
                        </span>
                        <input type="checkbox" name="rss_enabled" value="1" class="h-4 w-4 rounded border-stone-300 text-amber-700 focus:ring-amber-500/40" <?= $rssOn ? 'checked' : '' ?><?= $disabledAttr ?>/>
                    </label>
                </div>
            </section>
        </div>

        <div class="lg:col-span-5 space-y-6">
            <section class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80" aria-labelledby="comments-settings-heading">
                <h2 id="comments-settings-heading" class="text-sm font-semibold text-stone-900">Comments</h2>
                <p class="mt-1 text-xs text-stone-500">Policy flags for the public comment flow.</p>
                <div class="mt-5 space-y-3">
                    <label class="flex items-center justify-between gap-4 rounded-xl border border-stone-200 bg-stone-50/40 px-4 py-3">
                        <span class="text-sm font-medium text-stone-900">Allow comments</span>
                        <input type="checkbox" name="comments_enabled" value="1" class="h-4 w-4 rounded border-stone-300 text-amber-700 focus:ring-amber-500/40" <?= $commentsOn ? 'checked' : '' ?><?= $disabledAttr ?>/>
                    </label>
                    <label class="flex items-center justify-between gap-4 rounded-xl border border-stone-200 bg-stone-50/40 px-4 py-3">
                        <span class="text-sm font-medium text-stone-900">Require moderation</span>
                        <input type="checkbox" name="comments_require_moderation" value="1" class="h-4 w-4 rounded border-stone-300 text-amber-700 focus:ring-amber-500/40" <?= $modOn ? 'checked' : '' ?><?= $disabledAttr ?>/>
                    </label>
                    <label class="flex items-center justify-between gap-4 rounded-xl border border-stone-200 bg-stone-50/40 px-4 py-3">
                        <span class="text-sm font-medium text-stone-900">Close after 30 days</span>
                        <input type="checkbox" name="comments_close_after_30_days" value="1" class="h-4 w-4 rounded border-stone-300 text-amber-700 focus:ring-amber-500/40" <?= $close30 ? 'checked' : '' ?><?= $disabledAttr ?>/>
                    </label>
                </div>
            </section>

            <?php if ($canEditSettings) : ?>
                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-stone-900 px-5 py-2.5 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white lg:w-auto">
                        Save settings
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<div class="mt-6 grid gap-6 lg:grid-cols-12">
    <div class="lg:col-span-12">
        <section class="rounded-2xl border border-stone-200/90 bg-gradient-to-br from-white via-stone-50 to-amber-50/25 p-5 shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80" aria-labelledby="danger-zone-heading">
            <h2 id="danger-zone-heading" class="text-sm font-semibold text-stone-900">Danger zone</h2>
            <p class="mt-1 text-xs text-stone-500">Irreversible actions.</p>

            <?php if ($canEditSettings) : ?>
                <div class="mt-4 flex flex-wrap items-start gap-4">
                    <div>
                        <p class="text-xs font-semibold text-stone-700">Reset settings</p>
                        <p class="mt-1 max-w-md text-xs text-stone-600">Restore defaults for site title, reading, comments, and syndication.</p>
                        <button type="button"
                                data-modal-open="dashboard-confirm-modal"
                                data-confirm-title="Reset all settings?"
                                data-confirm-body="Site, reading, and comment policy values will return to their defaults."
                                data-confirm-label="Reset settings"
                                data-confirm-submit-form="form-reset-settings"
                                class="mt-3 inline-flex items-center justify-center rounded-xl border border-red-300 bg-white px-4 py-2.5 text-sm font-semibold text-red-900 shadow-sm hover:border-red-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/70 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                            Reset settings
                        </button>
                        <form id="form-reset-settings" method="post" action="<?= htmlspecialchars($settingsUrl, ENT_QUOTES, 'UTF-8') ?>" class="hidden">
                            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"/>
                            <input type="hidden" name="_action" value="reset_settings"/>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <div class="<?= $canEditSettings ? 'mt-6 border-t border-stone-200/80 pt-5' : 'mt-4' ?>">
                <p class="text-xs font-semibold text-red-900">Delete your account</p>
                <p class="mt-1 text-xs text-stone-600">Removes your user record and signs you out. You must have no posts, and you cannot be the only owner.</p>
                <form method="post" action="<?= htmlspecialchars($settingsUrl, ENT_QUOTES, 'UTF-8') ?>"
                      class="mt-4 max-w-md space-y-3"
                      onsubmit="return document.getElementById('delete-account-confirm').checked && confirm('Delete your account permanently? This cannot be undone.');">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"/>
                    <input type="hidden" name="_action" value="delete_account"/>
                    <div>
                        <label for="delete-account-password" class="block text-xs font-semibold text-stone-700">Current password</label>
                        <input id="delete-account-password" name="password" type="password" required autocomplete="current-password"
                               class="mt-1.5 w-full rounded-xl border border-stone-200 bg-white px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                    </div>
                    <label class="flex items-start gap-2 text-xs text-stone-700">
                        <input id="delete-account-confirm" type="checkbox" required class="mt-0.5 h-4 w-4 rounded border-stone-300 text-red-700 focus:ring-red-400/50"/>
                        <span>I understand this will delete my account and sign me out.</span>
                    </label>
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl border border-red-600 bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                        Delete my account
                    </button>
                </form>
            </div>
        </section>
    </div>
</div>

<?php require_once base_path('views/dashboard/partials/modals/settings.php'); ?>
<?php require_once base_path('views/dashboard/partials/footer.php'); ?>
