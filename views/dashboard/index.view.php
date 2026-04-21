<?php
/**
 * @var string $pageTitle
 * @var string $heading
 * @var string|null $subheading
 * @var string $dashboardNav
 * @var array<int, array{label:string,value:string,hint:string,tone:string}> $stats
 * @var array<int, array{title:string,meta:string,badge:string,badgeClass:string}> $activity
 * @var string $reviewCommentPayloadJson JSON for modal-comment-view (same shape as comments list)
 * @var string $chartDataJson
 * @var string $chartSummaryText
 */
require_once base_path('views/dashboard/partials/head.php');
require_once base_path('views/dashboard/partials/sidebar.php');

$stats = $stats ?? [];
$activity = $activity ?? [];
$reviewCommentPayloadJson = $reviewCommentPayloadJson ?? '{}';
$chartDataJson = $chartDataJson ?? '{}';
$chartSummaryText = $chartSummaryText ?? '';

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
        <article
                class="relative rounded-2xl border border-stone-200/90 bg-white p-5 shadow-sm shadow-stone-900/5 ring-1 <?= htmlspecialchars($ring, ENT_QUOTES, 'UTF-8') ?>">
            <div class="flex items-start justify-between gap-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-stone-500"><?= $sl ?></p>
                <button type="button" class="<?= $actionBtn ?>"
                        data-modal-open="modal-stat-view"
                        data-stat-label="<?= $sl ?>"
                        data-stat-value="<?= $sv ?>"
                        data-stat-hint="<?= $sh ?>">View
                </button>
            </div>
            <p class="mt-3 text-3xl font-semibold tabular-nums tracking-tight text-stone-900"><?= $sv ?></p>
            <p class="mt-3 inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 <?= htmlspecialchars($pill, ENT_QUOTES, 'UTF-8') ?>">
                <?= $sh ?>
            </p>
        </article>
    <?php endforeach; ?>
</section>

<?php require_once base_path('views/dashboard/partials/overview-charts.php'); ?>

<section class="mt-10 gap-6" aria-labelledby="activity-heading">
    <div class="rounded-2xl border border-stone-200/90 bg-white shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-stone-100 px-5 py-4">
            <div>
                <h2 id="activity-heading" class="text-sm font-semibold text-stone-900">Recent activity</h2>
                <p class="mt-1 text-xs text-stone-500">Latest post edits, comments, and tags from your site.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-700 ring-1 ring-stone-200/80">Live</span>
            </div>
        </div>
        <ul class="divide-y divide-stone-100" role="list">
            <?php if ($activity === []) : ?>
                <li class="px-5 py-10 text-center text-sm text-stone-600">
                    No recent activity yet. Publish a post, add a tag, or receive a comment to see it here.
                </li>
            <?php endif; ?>
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
                                data-activity-badge="<?= $ab ?>">View
                        </button>
                        <button type="button"
                                class="rounded-lg border border-red-200 bg-red-50 px-2 py-1 text-[11px] font-semibold text-red-800 shadow-sm hover:border-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/70 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                                data-modal-open="dashboard-confirm-modal"
                                data-confirm-title="Delete activity?"
                                data-confirm-body="<?= $delBody ?>"
                                data-confirm-label="Delete">Delete
                        </button>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<?php require_once base_path('views/dashboard/partials/modals/posts.php'); ?>
<?php require_once base_path('views/dashboard/partials/modals/comments.php'); ?>
<?php require_once base_path('views/dashboard/partials/modals/overview.php'); ?>
<?php require_once base_path('views/dashboard/partials/footer.php'); ?>
