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
<button type="button" data-modal-open="modal-setting-add" class="inline-flex items-center justify-center rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
    Add snippet
</button>
<button type="button"
        data-modal-open="modal-setting-view"
        data-site-title="Mini Blog"
        data-tagline="Notes &amp; long-form"
        data-posts-per-page="12"
        data-date-format="M j, Y"
        data-rss="Enabled"
        class="inline-flex items-center justify-center rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
    View snapshot
</button>
<button type="button"
        data-modal-open="modal-setting-edit"
        data-site-title="Mini Blog"
        data-tagline="Notes &amp; long-form"
        data-posts-per-page="12"
        class="inline-flex items-center justify-center rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
    Edit branding
</button>
<button type="button"
        data-modal-open="dashboard-confirm-modal"
        data-confirm-title="Discard unsaved changes?"
        data-confirm-body="You will lose edits made in this session (demo)."
        data-confirm-label="Discard"
        class="inline-flex items-center justify-center rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
    Discard
</button>
<button type="button"
        data-modal-open="dashboard-confirm-modal"
        data-confirm-title="Save settings?"
        data-confirm-body="Persist site, reading, and comment policy (demo confirmation only)."
        data-confirm-label="Save changes"
        data-confirm-variant="primary"
        class="inline-flex items-center justify-center rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
    Save changes
</button>
HTML;
?>

<?php require_once base_path('views/dashboard/partials/page-header.php'); ?>

<div class="grid gap-6 lg:grid-cols-12">
    <div class="lg:col-span-7 space-y-6">
        <section class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80" aria-labelledby="site-settings-heading">
            <h2 id="site-settings-heading" class="text-sm font-semibold text-stone-900">Site</h2>
            <p class="mt-1 text-xs text-stone-500">Public-facing strings and defaults.</p>
            <form class="mt-5 space-y-4" action="#" method="get" onsubmit="return false;">
                <div>
                    <label for="site-title" class="block text-xs font-semibold text-stone-700">Site title</label>
                    <input id="site-title" type="text" value="Mini Blog"
                           class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                </div>
                <div>
                    <label for="site-tagline" class="block text-xs font-semibold text-stone-700">Tagline</label>
                    <input id="site-tagline" type="text" value="Notes &amp; long-form"
                           class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="posts-per-page" class="block text-xs font-semibold text-stone-700">Posts per page</label>
                        <input id="posts-per-page" type="number" min="1" value="12"
                               class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                    </div>
                    <div>
                        <label for="date-format" class="block text-xs font-semibold text-stone-700">Date format</label>
                        <select id="date-format"
                                class="<?= htmlspecialchars($dashboardSelect, ENT_QUOTES, 'UTF-8') ?> mt-1.5">
                            <option selected>M j, Y</option>
                            <option>Y-m-d</option>
                            <option>Relative</option>
                        </select>
                    </div>
                </div>
            </form>
        </section>

        <section class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80" aria-labelledby="reading-settings-heading">
            <h2 id="reading-settings-heading" class="text-sm font-semibold text-stone-900">Reading &amp; syndication</h2>
            <p class="mt-1 text-xs text-stone-500">Feeds and homepage behavior.</p>
            <div class="mt-5 space-y-4">
                <fieldset>
                    <legend class="text-xs font-semibold text-stone-700">Homepage displays</legend>
                    <div class="mt-2 space-y-2">
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-stone-200 bg-stone-50/40 p-3 hover:bg-stone-50/80">
                            <input type="radio" name="homepage" class="mt-1" checked/>
                            <span>
                                <span class="block text-sm font-semibold text-stone-900">Latest posts</span>
                                <span class="block text-xs text-stone-600">Show the blog index on <span class="font-mono text-stone-700">/</span>.</span>
                            </span>
                        </label>
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-stone-200 bg-white p-3 hover:bg-stone-50/60">
                            <input type="radio" name="homepage" class="mt-1"/>
                            <span>
                                <span class="block text-sm font-semibold text-stone-900">Static page</span>
                                <span class="block text-xs text-stone-600">Pick a page slug when pages exist.</span>
                            </span>
                        </label>
                    </div>
                </fieldset>
                <label class="flex items-center justify-between gap-4 rounded-xl border border-stone-200 bg-white px-4 py-3 shadow-sm">
                    <span>
                        <span class="block text-sm font-semibold text-stone-900">RSS feed</span>
                        <span class="block text-xs text-stone-600">Expose <span class="font-mono text-stone-700">/feed.xml</span> (demo label).</span>
                    </span>
                    <input type="checkbox" class="h-4 w-4 rounded border-stone-300 text-amber-700 focus:ring-amber-500/40" checked/>
                </label>
            </div>
        </section>
    </div>

    <div class="lg:col-span-5 space-y-6">
        <section class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80" aria-labelledby="comments-settings-heading">
            <h2 id="comments-settings-heading" class="text-sm font-semibold text-stone-900">Comments</h2>
            <p class="mt-1 text-xs text-stone-500">Policy toggles for later integration.</p>
            <div class="mt-5 space-y-3">
                <label class="flex items-center justify-between gap-4 rounded-xl border border-stone-200 bg-stone-50/40 px-4 py-3">
                    <span class="text-sm font-medium text-stone-900">Allow comments</span>
                    <input type="checkbox" class="h-4 w-4 rounded border-stone-300 text-amber-700 focus:ring-amber-500/40" checked/>
                </label>
                <label class="flex items-center justify-between gap-4 rounded-xl border border-stone-200 bg-stone-50/40 px-4 py-3">
                    <span class="text-sm font-medium text-stone-900">Require moderation</span>
                    <input type="checkbox" class="h-4 w-4 rounded border-stone-300 text-amber-700 focus:ring-amber-500/40" checked/>
                </label>
                <label class="flex items-center justify-between gap-4 rounded-xl border border-stone-200 bg-stone-50/40 px-4 py-3">
                    <span class="text-sm font-medium text-stone-900">Close after 30 days</span>
                    <input type="checkbox" class="h-4 w-4 rounded border-stone-300 text-amber-700 focus:ring-amber-500/40"/>
                </label>
            </div>
        </section>

        <section class="rounded-2xl border border-stone-200/90 bg-gradient-to-br from-white via-stone-50 to-amber-50/25 p-5 shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80" aria-labelledby="danger-zone-heading">
            <h2 id="danger-zone-heading" class="text-sm font-semibold text-stone-900">Danger zone</h2>
            <p class="mt-1 text-xs text-stone-500">Destructive operations need confirmations in real code.</p>
            <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                <button type="button"
                        data-modal-open="dashboard-confirm-modal"
                        data-confirm-title="Clear caches?"
                        data-confirm-body="Purge compiled views, OPcache, and CDN edge cache (demo)."
                        data-confirm-label="Clear caches"
                        data-confirm-variant="primary"
                        class="inline-flex flex-1 items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-900 shadow-sm hover:border-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/70 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                    Clear caches
                </button>
                <button type="button"
                        data-modal-open="dashboard-confirm-modal"
                        data-confirm-title="Reset all settings?"
                        data-confirm-body="This will restore defaults for site, reading, and comments. This cannot be undone."
                        data-confirm-label="Reset settings"
                        class="inline-flex flex-1 items-center justify-center rounded-xl border border-red-300 bg-white px-4 py-2.5 text-sm font-semibold text-red-900 shadow-sm hover:border-red-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/70 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                    Reset settings
                </button>
            </div>
        </section>
    </div>
</div>

<?php require_once base_path('views/dashboard/partials/modals/settings.php'); ?>
<?php require_once base_path('views/dashboard/partials/footer.php'); ?>
