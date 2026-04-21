<?php
/**
 * @var string $pageTitle
 * @var string $heading
 * @var string|null $subheading
 * @var string $dashboardNav
 */
require_once base_path('views/dashboard/partials/head.php');
require_once base_path('views/dashboard/partials/sidebar.php');

$stats = [
    ['label' => 'Published posts', 'value' => '24', 'hint' => '+3 this week', 'tone' => 'stone'],
    ['label' => 'Drafts', 'value' => '5', 'hint' => '2 ready to review', 'tone' => 'amber'],
    ['label' => 'Comments', 'value' => '128', 'hint' => '12 awaiting moderation', 'tone' => 'emerald'],
    ['label' => 'Newsletter', 'value' => '1.4k', 'hint' => 'Subscribers (demo)', 'tone' => 'sky'],
];

$activity = [
    ['title' => 'Post published', 'meta' => '“Winter light on the river” · Today, 9:12', 'badge' => 'Publish', 'badgeClass' => 'bg-emerald-50 text-emerald-900 ring-emerald-200/70'],
    ['title' => 'Comment held', 'meta' => 'On “Notes from a slower inbox” · Yesterday', 'badge' => 'Moderation', 'badgeClass' => 'bg-amber-50 text-amber-950 ring-amber-200/80'],
    ['title' => 'Draft updated', 'meta' => '“A small CMS checklist” · 2 days ago', 'badge' => 'Draft', 'badgeClass' => 'bg-stone-100 text-stone-800 ring-stone-200/80'],
    ['title' => 'Tag added', 'meta' => '“field-notes” created · 3 days ago', 'badge' => 'Taxonomy', 'badgeClass' => 'bg-sky-50 text-sky-950 ring-sky-200/80'],
];

$actionBtn = 'rounded-lg border border-stone-200 bg-white px-2 py-1 text-[11px] font-semibold text-stone-800 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
?>

<?php require_once base_path('views/dashboard/partials/page-header.php'); ?>

<section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Key metrics">
    <?php foreach ($stats as $s) : ?>
        <?php
        $ring = match ($s['tone']) {
            'amber' => 'ring-amber-200/80',
            'emerald' => 'ring-emerald-200/70',
            'sky' => 'ring-sky-200/80',
            default => 'ring-stone-200/90',
        };
        $pill = match ($s['tone']) {
            'amber' => 'bg-amber-50 text-amber-950 ring-amber-200/80',
            'emerald' => 'bg-emerald-50 text-emerald-950 ring-emerald-200/70',
            'sky' => 'bg-sky-50 text-sky-950 ring-sky-200/80',
            default => 'bg-stone-50 text-stone-800 ring-stone-200/80',
        };
        $sl = htmlspecialchars($s['label'], ENT_QUOTES, 'UTF-8');
        $sv = htmlspecialchars($s['value'], ENT_QUOTES, 'UTF-8');
        $sh = htmlspecialchars($s['hint'], ENT_QUOTES, 'UTF-8');
        ?>
        <article class="relative rounded-2xl border border-stone-200/90 bg-white p-5 shadow-sm shadow-stone-900/5 ring-1 <?= htmlspecialchars($ring, ENT_QUOTES, 'UTF-8') ?>">
            <div class="flex items-start justify-between gap-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-stone-500"><?= $sl ?></p>
                <button type="button" class="<?= $actionBtn ?>"
                        data-modal-open="modal-stat-view"
                        data-stat-label="<?= $sl ?>"
                        data-stat-value="<?= $sv ?>"
                        data-stat-hint="<?= $sh ?>">View</button>
            </div>
            <p class="mt-3 text-3xl font-semibold tabular-nums tracking-tight text-stone-900"><?= $sv ?></p>
            <p class="mt-3 inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 <?= htmlspecialchars($pill, ENT_QUOTES, 'UTF-8') ?>">
                <?= $sh ?>
            </p>
        </article>
    <?php endforeach; ?>
</section>

<section class="mt-10 grid gap-6 lg:grid-cols-12" aria-labelledby="activity-heading">
    <div class="lg:col-span-7">
        <div class="rounded-2xl border border-stone-200/90 bg-white shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <div>
                    <h2 id="activity-heading" class="text-sm font-semibold text-stone-900">Recent activity</h2>
                    <p class="mt-1 text-xs text-stone-500">Latest edits, comments, and publishes (placeholder).</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-700 ring-1 ring-stone-200/80">Demo</span>
                    <button type="button" data-modal-open="modal-activity-add"
                            class="inline-flex items-center justify-center rounded-lg bg-stone-900 px-3 py-1.5 text-xs font-semibold text-amber-50 shadow-sm hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                        Add
                    </button>
                </div>
            </div>
            <ul class="divide-y divide-stone-100" role="list">
                <?php foreach ($activity as $row) : ?>
                    <?php
                    $at = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
                    $am = htmlspecialchars($row['meta'], ENT_QUOTES, 'UTF-8');
                    $ab = htmlspecialchars($row['badge'], ENT_QUOTES, 'UTF-8');
                    $delBody = htmlspecialchars('Remove this activity entry from the timeline? (' . $row['title'] . ')', ENT_QUOTES, 'UTF-8');
                    ?>
                    <li class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-stone-900"><?= $at ?></p>
                            <p class="mt-1 text-xs text-stone-600"><?= $am ?></p>
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center gap-2 sm:justify-end">
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide ring-1 <?= htmlspecialchars($row['badgeClass'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= $ab ?>
                            </span>
                            <button type="button" class="<?= $actionBtn ?>"
                                    data-modal-open="modal-activity-view"
                                    data-activity-title="<?= $at ?>"
                                    data-activity-meta="<?= $am ?>"
                                    data-activity-badge="<?= $ab ?>">View</button>
                            <button type="button" class="<?= $actionBtn ?>"
                                    data-modal-open="modal-activity-edit"
                                    data-activity-title="<?= $at ?>"
                                    data-activity-meta="<?= $am ?>"
                                    data-activity-badge="<?= $ab ?>">Edit</button>
                            <button type="button"
                                    class="rounded-lg border border-red-200 bg-red-50 px-2 py-1 text-[11px] font-semibold text-red-800 shadow-sm hover:border-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/70 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                                    data-modal-open="dashboard-confirm-modal"
                                    data-confirm-title="Delete activity?"
                                    data-confirm-body="<?= $delBody ?>"
                                    data-confirm-label="Delete">Delete</button>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="lg:col-span-5 space-y-6">
        <div class="rounded-2xl border border-stone-200/90 bg-gradient-to-br from-white via-stone-50 to-amber-50/35 p-5 shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
            <h2 class="text-sm font-semibold text-stone-900">Quick actions</h2>
            <p class="mt-1 text-xs text-stone-600">Shortcuts open modals—wire them to real routes later.</p>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <button type="button" data-modal-open="modal-post-add"
                        class="inline-flex items-center justify-center rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                    New post
                </button>
                <button type="button"
                        data-modal-open="modal-comment-view"
                        data-comment-author="Mara K."
                        data-comment-email="mara@example.com"
                        data-comment-post="Winter light on the river"
                        data-comment-state="Pending"
                        data-comment-when="2h ago"
                        data-comment-body="This reminded me to slow down my own publishing cadence and batch my writing sessions."
                        class="inline-flex items-center justify-center rounded-xl border border-stone-300 bg-white/80 px-4 py-2.5 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                    Review comments
                </button>
            </div>
        </div>

        <div class="rounded-2xl border border-dashed border-stone-300 bg-stone-50/70 p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-sm font-semibold text-stone-900">Health</h2>
                    <p class="mt-2 text-sm leading-relaxed text-stone-600">
                        Add checks here later: broken links, scheduled posts, storage, and backups.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" class="<?= $actionBtn ?>"
                            data-modal-open="modal-activity-view"
                            data-activity-title="Health check"
                            data-activity-meta="Last run · Never (demo)"
                            data-activity-badge="Idle">View</button>
                    <button type="button" class="<?= $actionBtn ?>"
                            data-modal-open="modal-activity-edit"
                            data-activity-title="Health check"
                            data-activity-meta="Last run · Never (demo)"
                            data-activity-badge="Idle">Edit</button>
                    <button type="button"
                            class="rounded-lg border border-red-200 bg-red-50 px-2 py-1 text-[11px] font-semibold text-red-800 shadow-sm hover:border-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/70 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                            data-modal-open="dashboard-confirm-modal"
                            data-confirm-title="Remove health panel?"
                            data-confirm-body="Hide the health card from the overview (demo)."
                            data-confirm-label="Remove">Delete</button>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap gap-2">
                <button type="button" data-modal-open="modal-activity-add"
                        class="inline-flex items-center justify-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-stone-700 ring-1 ring-stone-200/90 hover:bg-stone-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50">
                    Add check
                </button>
                <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-stone-700 ring-1 ring-stone-200/90">SEO basics</span>
                <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-stone-700 ring-1 ring-stone-200/90">Accessibility</span>
                <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-stone-700 ring-1 ring-stone-200/90">Feeds</span>
            </div>
        </div>
    </div>
</section>

<?php require_once base_path('views/dashboard/partials/modals/posts.php'); ?>
<?php require_once base_path('views/dashboard/partials/modals/comments.php'); ?>
<?php require_once base_path('views/dashboard/partials/modals/overview.php'); ?>
<?php require_once base_path('views/dashboard/partials/footer.php'); ?>
