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
<button type="button" data-modal-open="modal-comment-add" class="inline-flex items-center justify-center rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
    Add note
</button>
<button type="button"
        data-modal-open="dashboard-confirm-modal"
        data-confirm-title="Mark all as read?"
        data-confirm-body="Clear the moderation badge count for this inbox (demo)."
        data-confirm-label="Mark read"
        data-confirm-variant="primary"
        class="inline-flex items-center justify-center rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
    Mark all read
</button>
HTML;

$demoComments = [
    ['author' => 'Mara K.', 'email' => 'mara@example.com', 'body' => 'This reminded me to slow down my own publishing cadence and batch my writing sessions.', 'post' => 'Winter light on the river', 'when' => '2h ago', 'state' => 'Pending'],
    ['author' => 'devnull', 'email' => 'spam@invalid', 'body' => 'Cheap SEO services click here!!!', 'post' => 'Typography in quiet UIs', 'when' => '5h ago', 'state' => 'Spam'],
    ['author' => 'Elliot', 'email' => 'elliot@example.com', 'body' => 'Could you share the type scale you used for body and headings?', 'post' => 'Typography in quiet UIs', 'when' => 'Yesterday', 'state' => 'Approved'],
    ['author' => 'Sam V.', 'email' => 'sam@example.com', 'body' => 'Subtle and readable—thank you.', 'post' => 'Notes from a slower inbox', 'when' => 'Apr 16', 'state' => 'Pending'],
];
$actionBtn = 'rounded-lg border border-stone-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-stone-800 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
?>

<?php require_once base_path('views/dashboard/partials/page-header.php'); ?>

<div class="rounded-2xl border border-stone-200/90 bg-white shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
    <div class="flex flex-col gap-4 border-b border-stone-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
        <div class="flex flex-wrap items-center gap-2">
            <?php
            $filters = ['All', 'Pending', 'Approved', 'Spam'];
            foreach ($filters as $idx => $label) :
                $active = $idx === 0;
                ?>
                <button type="button"
                        class="rounded-full px-3 py-1.5 text-xs font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white <?= $active ? 'bg-stone-900 text-amber-50 shadow-sm' : 'bg-stone-100 text-stone-700 hover:bg-stone-200/80' ?>">
                    <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                </button>
            <?php endforeach; ?>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <label class="sr-only" for="comment-search">Search comments</label>
            <input id="comment-search" type="search" placeholder="Search comment text…" autocomplete="off"
                   class="min-w-[12rem] flex-1 rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30 sm:max-w-xs sm:flex-none"/>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-stone-100 text-left text-sm">
            <thead class="bg-stone-50/80 text-xs font-semibold uppercase tracking-wider text-stone-500">
            <tr>
                <th scope="col" class="px-5 py-3">Comment</th>
                <th scope="col" class="hidden px-3 py-3 lg:table-cell">Post</th>
                <th scope="col" class="px-3 py-3">Status</th>
                <th scope="col" class="hidden px-3 py-3 md:table-cell">Received</th>
                <th scope="col" class="px-3 py-3"><span class="sr-only">Actions</span></th>
            </tr>
            </thead>
            <tbody class="divide-y divide-stone-100 bg-white">
            <?php foreach ($demoComments as $c) : ?>
                <?php
                $stateClass = match ($c['state']) {
                    'Pending' => 'bg-amber-50 text-amber-950 ring-amber-200/80',
                    'Approved' => 'bg-emerald-50 text-emerald-900 ring-emerald-200/70',
                    'Spam' => 'bg-red-50 text-red-900 ring-red-200/70',
                    default => 'bg-stone-100 text-stone-800 ring-stone-200/80',
                };
                $au = htmlspecialchars($c['author'], ENT_QUOTES, 'UTF-8');
                $em = htmlspecialchars($c['email'], ENT_QUOTES, 'UTF-8');
                $po = htmlspecialchars($c['post'], ENT_QUOTES, 'UTF-8');
                $wh = htmlspecialchars($c['when'], ENT_QUOTES, 'UTF-8');
                $st = htmlspecialchars($c['state'], ENT_QUOTES, 'UTF-8');
                $bd = htmlspecialchars($c['body'], ENT_QUOTES, 'UTF-8');
                $preview = $c['body'];
                if (strlen($preview) > 72) {
                    $preview = substr($preview, 0, 72) . '…';
                }
                $exShort = htmlspecialchars($preview, ENT_QUOTES, 'UTF-8');
                $delBody = htmlspecialchars('Permanently delete this comment by ' . $c['author'] . '?', ENT_QUOTES, 'UTF-8');
                ?>
                <tr class="align-top hover:bg-stone-50/60">
                    <th scope="row" class="px-5 py-4">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-stone-900"><?= $au ?></p>
                            <p class="mt-0.5 text-xs text-stone-500"><?= $em ?></p>
                            <p class="mt-2 line-clamp-2 text-sm text-stone-700"><?= $exShort ?></p>
                            <p class="mt-2 text-xs text-stone-500 lg:hidden">On “<?= $po ?>”</p>
                        </div>
                    </th>
                    <td class="hidden px-3 py-4 text-stone-700 lg:table-cell">
                        <span class="line-clamp-2"><?= $po ?></span>
                    </td>
                    <td class="px-3 py-4">
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 <?= htmlspecialchars($stateClass, ENT_QUOTES, 'UTF-8') ?>">
                            <?= $st ?>
                        </span>
                    </td>
                    <td class="hidden px-3 py-4 tabular-nums text-stone-600 md:table-cell"><?= $wh ?></td>
                    <td class="px-3 py-4 text-right">
                        <div class="inline-flex flex-col items-end gap-2 sm:flex-row sm:flex-wrap sm:justify-end">
                            <button type="button" class="<?= $actionBtn ?> w-full sm:w-auto"
                                    data-modal-open="modal-comment-view"
                                    data-comment-author="<?= $au ?>"
                                    data-comment-email="<?= $em ?>"
                                    data-comment-post="<?= $po ?>"
                                    data-comment-state="<?= $st ?>"
                                    data-comment-when="<?= $wh ?>"
                                    data-comment-body="<?= $bd ?>">View</button>
                            <button type="button" class="<?= $actionBtn ?> w-full sm:w-auto"
                                    data-modal-open="modal-comment-edit"
                                    data-comment-author="<?= $au ?>"
                                    data-comment-email="<?= $em ?>"
                                    data-comment-post="<?= $po ?>"
                                    data-comment-state="<?= $st ?>"
                                    data-comment-body="<?= $bd ?>">Edit</button>
                            <button type="button" class="w-full rounded-lg bg-emerald-700 px-2.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white sm:w-auto">Approve</button>
                            <button type="button" class="w-full rounded-lg border border-stone-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-stone-800 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white sm:w-auto">Reply</button>
                            <button type="button"
                                    class="w-full rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-800 shadow-sm hover:border-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/70 focus-visible:ring-offset-2 focus-visible:ring-offset-white sm:w-auto"
                                    data-modal-open="dashboard-confirm-modal"
                                    data-confirm-title="Delete this comment?"
                                    data-confirm-body="<?= $delBody ?>"
                                    data-confirm-label="Delete comment">Delete</button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="flex flex-col items-center justify-between gap-3 border-t border-stone-100 px-5 py-4 sm:flex-row">
        <p class="text-xs text-stone-500">Bulk actions and moderation queues can attach to these rows.</p>
        <div class="flex items-center gap-2">
            <button type="button" class="rounded-lg border border-stone-200 bg-white px-3 py-2 text-xs font-semibold text-stone-700 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white" disabled>
                Previous
            </button>
            <button type="button" class="rounded-lg border border-stone-200 bg-white px-3 py-2 text-xs font-semibold text-stone-700 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                Next
            </button>
        </div>
    </div>
</div>

<?php require_once base_path('views/dashboard/partials/modals/comments.php'); ?>
<?php require_once base_path('views/dashboard/partials/footer.php'); ?>
