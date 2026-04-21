<?php
/**
 * @var string $pageTitle
 * @var string $heading
 * @var string|null $subheading
 * @var string $dashboardNav
 * @var array<int, array<string, mixed>> $comments
 * @var string $commentsUrl
 * @var array{q: string, status: string} $filters
 * @var int $page
 * @var int $totalPages
 * @var int $totalComments
 * @var int $perPage
 * @var int $pendingCount
 * @var string $flashSuccess
 * @var string $flashError
 * @var string $csrfToken
 * @var string $redirectQuery
 */
require_once base_path('views/dashboard/partials/head.php');
require_once base_path('views/dashboard/partials/sidebar.php');

$comments = $comments ?? [];
$commentsUrl = $commentsUrl ?? blog_url('dashboard/comments');
$filters = $filters ?? ['q' => '', 'status' => 'all'];
$csrfToken = $csrfToken ?? auth_csrf_token();
$redirectQuery = $redirectQuery ?? '';
$flashSuccess = $flashSuccess ?? '';
$flashError = $flashError ?? '';
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;
$totalComments = $totalComments ?? 0;
$pendingCount = $pendingCount ?? 0;

$filterLink = static function (string $base, string $status, string $q): string {
  $params = [];
  if ($q !== '') {
    $params['q'] = $q;
  }
  if ($status !== '' && $status !== 'all') {
    $params['status'] = $status;
  }
  return $params === [] ? $base : $base . '?' . http_build_query($params);
};

$statusLabel = static function (string $s): string {
  return match ($s) {
    'pending' => 'Pending',
    'approved' => 'Approved',
    'spam' => 'Spam',
    'rejected' => 'Rejected',
    default => ucfirst($s),
  };
};

$pageActions = '';
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

<div class="rounded-2xl border border-stone-200/90 bg-white shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
    <div class="flex flex-col gap-4 border-b border-stone-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
        <div class="flex flex-wrap items-center gap-2">
            <?php
            $st = (string) ($filters['status'] ?? 'all');
            $qVal = trim((string) ($filters['q'] ?? ''));
            $filterDefs = [
                ['key' => 'all', 'label' => 'All'],
                ['key' => 'pending', 'label' => 'Pending'],
                ['key' => 'approved', 'label' => 'Approved'],
                ['key' => 'spam', 'label' => 'Spam'],
                ['key' => 'rejected', 'label' => 'Rejected'],
            ];
            foreach ($filterDefs as $fd) :
                $active = $st === $fd['key'];
                $href = htmlspecialchars($filterLink($commentsUrl, $fd['key'], $qVal), ENT_QUOTES, 'UTF-8');
                ?>
                <a href="<?= $href ?>"
                   class="rounded-full px-3 py-1.5 text-xs font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white <?= $active ? 'bg-stone-900 text-amber-50 shadow-sm' : 'bg-stone-100 text-stone-700 hover:bg-stone-200/80' ?>">
                    <?= htmlspecialchars($fd['label'], ENT_QUOTES, 'UTF-8') ?>
                    <?php if ($fd['key'] === 'pending' && $pendingCount > 0) : ?>
                        <span class="ml-1 tabular-nums opacity-90">(<?= $pendingCount ?>)</span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
        <form method="get" action="<?= htmlspecialchars($commentsUrl, ENT_QUOTES, 'UTF-8') ?>" class="flex flex-wrap items-center gap-2">
            <?php if ($st !== '' && $st !== 'all') : ?>
                <input type="hidden" name="status" value="<?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?>"/>
            <?php endif; ?>
            <label class="sr-only" for="comment-search">Search comments</label>
            <input id="comment-search" name="q" type="search" value="<?= htmlspecialchars($qVal, ENT_QUOTES, 'UTF-8') ?>" placeholder="Search text, author, post…" autocomplete="off"
                   class="min-w-[12rem] flex-1 rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30 sm:max-w-xs sm:flex-none"/>
            <button type="submit" class="rounded-xl bg-stone-900 px-3 py-2 text-xs font-semibold text-amber-50 shadow-sm hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                Search
            </button>
        </form>
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
            <?php if ($comments === []) : ?>
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center text-sm text-stone-600">No comments match this view.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($comments as $c) : ?>
                <?php
                $cid = (int) ($c['id'] ?? 0);
                if ($cid < 1) {
                  continue;
                }
                $rawStatus = (string) ($c['status'] ?? 'pending');
                $stateClass = match ($rawStatus) {
                  'pending' => 'bg-amber-50 text-amber-950 ring-amber-200/80',
                  'approved' => 'bg-emerald-50 text-emerald-900 ring-emerald-200/70',
                  'spam' => 'bg-red-50 text-red-900 ring-red-200/70',
                  'rejected' => 'bg-stone-100 text-stone-800 ring-stone-200/80',
                  default => 'bg-stone-100 text-stone-800 ring-stone-200/80',
                };
                $au = (string) ($c['author_name'] ?? '');
                $em = (string) ($c['author_email'] ?? '');
                $bd = (string) ($c['body'] ?? '');
                $postTitle = (string) ($c['post_title'] ?? '');
                $postSlug = trim((string) ($c['post_slug'] ?? ''));
                $postLink = $postSlug !== '' ? blog_post_url($postSlug) : '';
                $createdRaw = $c['created_at'] ?? '';
                $createdDt = $createdRaw ? date_create((string) $createdRaw) : false;
                $whenDisplay = $createdDt instanceof DateTimeInterface ? $createdDt->format('M j, Y g:i a') : '—';
                $preview = mb_strlen($bd) > 100 ? mb_substr($bd, 0, 100) . '…' : $bd;
                $payload = [
                  'id' => $cid,
                  'author_name' => $au,
                  'author_email' => $em,
                  'post_title' => $postTitle,
                  'body' => $bd,
                  'status' => $rawStatus,
                ];
                $payloadJson = htmlspecialchars(json_encode($payload, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
                $actionBtn = 'rounded-lg border border-stone-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-stone-800 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
                $delBody = 'Permanently delete this comment by ' . $au . '?';
                ?>
                <tr class="align-top hover:bg-stone-50/60">
                    <th scope="row" class="px-5 py-4">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-stone-900"><?= htmlspecialchars($au, ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="mt-0.5 text-xs text-stone-500"><?= htmlspecialchars($em, ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="mt-2 line-clamp-2 text-sm text-stone-700"><?= htmlspecialchars($preview, ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="mt-2 text-xs text-stone-500 lg:hidden">On “<?= htmlspecialchars($postTitle, ENT_QUOTES, 'UTF-8') ?>”</p>
                        </div>
                    </th>
                    <td class="hidden px-3 py-4 text-stone-700 lg:table-cell">
                        <?php if ($postLink !== '') : ?>
                            <a href="<?= htmlspecialchars($postLink, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"
                               class="line-clamp-2 font-medium text-amber-900 underline decoration-amber-300 underline-offset-2 hover:decoration-amber-500">
                                <?= htmlspecialchars($postTitle, ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php else : ?>
                            <span class="line-clamp-2"><?= htmlspecialchars($postTitle, ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-3 py-4">
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 <?= htmlspecialchars($stateClass, ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($statusLabel($rawStatus), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td class="hidden px-3 py-4 tabular-nums text-stone-600 md:table-cell"><?= htmlspecialchars($whenDisplay, ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-3 py-4 text-right">
                        <div class="inline-flex flex-col items-end gap-2 sm:flex-row sm:flex-wrap sm:justify-end">
                            <button type="button" class="<?= $actionBtn ?> w-full sm:w-auto"
                                    data-modal-open="modal-comment-view"
                                    data-comment-payload="<?= $payloadJson ?>">View</button>
                            <button type="button" class="<?= $actionBtn ?> w-full sm:w-auto"
                                    data-modal-open="modal-comment-edit"
                                    data-comment-payload="<?= $payloadJson ?>">Edit</button>

                            <?php if ($rawStatus !== 'approved') : ?>
                                <form method="post" action="<?= htmlspecialchars($commentsUrl, ENT_QUOTES, 'UTF-8') ?>" class="inline w-full sm:w-auto">
                                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"/>
                                    <input type="hidden" name="_action" value="set_status"/>
                                    <input type="hidden" name="id" value="<?= $cid ?>"/>
                                    <input type="hidden" name="status" value="approved"/>
                                    <input type="hidden" name="_redirect_query" value="<?= htmlspecialchars($redirectQuery, ENT_QUOTES, 'UTF-8') ?>"/>
                                    <button type="submit" class="w-full rounded-lg bg-emerald-700 px-2.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white sm:w-auto">Approve</button>
                                </form>
                            <?php endif; ?>

                            <?php if ($rawStatus !== 'spam') : ?>
                                <form method="post" action="<?= htmlspecialchars($commentsUrl, ENT_QUOTES, 'UTF-8') ?>" class="inline w-full sm:w-auto">
                                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"/>
                                    <input type="hidden" name="_action" value="set_status"/>
                                    <input type="hidden" name="id" value="<?= $cid ?>"/>
                                    <input type="hidden" name="status" value="spam"/>
                                    <input type="hidden" name="_redirect_query" value="<?= htmlspecialchars($redirectQuery, ENT_QUOTES, 'UTF-8') ?>"/>
                                    <button type="submit" class="w-full rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-800 shadow-sm hover:border-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/70 focus-visible:ring-offset-2 focus-visible:ring-offset-white sm:w-auto">Spam</button>
                                </form>
                            <?php endif; ?>

                            <?php if ($rawStatus !== 'rejected' && $rawStatus !== 'spam') : ?>
                                <form method="post" action="<?= htmlspecialchars($commentsUrl, ENT_QUOTES, 'UTF-8') ?>" class="inline w-full sm:w-auto">
                                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"/>
                                    <input type="hidden" name="_action" value="set_status"/>
                                    <input type="hidden" name="id" value="<?= $cid ?>"/>
                                    <input type="hidden" name="status" value="rejected"/>
                                    <input type="hidden" name="_redirect_query" value="<?= htmlspecialchars($redirectQuery, ENT_QUOTES, 'UTF-8') ?>"/>
                                    <button type="submit" class="w-full rounded-lg border border-stone-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-stone-800 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white sm:w-auto">Reject</button>
                                </form>
                            <?php endif; ?>

                            <form id="form-delete-comment-<?= $cid ?>" method="post" action="<?= htmlspecialchars($commentsUrl, ENT_QUOTES, 'UTF-8') ?>" class="hidden">
                                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"/>
                                <input type="hidden" name="_action" value="delete"/>
                                <input type="hidden" name="id" value="<?= $cid ?>"/>
                            </form>
                            <button type="button"
                                    class="w-full rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-800 shadow-sm hover:border-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/70 focus-visible:ring-offset-2 focus-visible:ring-offset-white sm:w-auto"
                                    data-modal-open="dashboard-confirm-modal"
                                    data-confirm-title="Delete this comment?"
                                    data-confirm-body="<?= htmlspecialchars($delBody, ENT_QUOTES, 'UTF-8') ?>"
                                    data-confirm-label="Delete comment"
                                    data-confirm-submit-form="form-delete-comment-<?= $cid ?>">Delete</button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php
    $page = max(1, $page);
    $totalPages = max(1, $totalPages);
    $prevDisabled = $page <= 1;
    $nextDisabled = $page >= $totalPages;
    $pageBaseParams = [];
    if ($qVal !== '') {
      $pageBaseParams['q'] = $qVal;
    }
    if ($st !== '' && $st !== 'all') {
      $pageBaseParams['status'] = $st;
    }
    $pageLink = static function (int $p) use ($commentsUrl, $pageBaseParams): string {
      $params = $pageBaseParams;
      if ($p > 1) {
        $params['page'] = $p;
      }
      return $params === [] ? $commentsUrl : $commentsUrl . '?' . http_build_query($params);
    };
    ?>
    <div class="flex flex-col items-center justify-between gap-3 border-t border-stone-100 px-5 py-4 sm:flex-row">
        <p class="text-xs text-stone-500">
            <?= $totalComments ?> comment<?= $totalComments !== 1 ? 's' : '' ?>
            <?php if ($totalPages > 1) : ?>
                · Page <?= $page ?> of <?= $totalPages ?>
            <?php endif; ?>
        </p>
        <div class="flex items-center gap-2">
            <?php if ($totalPages > 1) : ?>
                <a href="<?= htmlspecialchars($pageLink($page - 1), ENT_QUOTES, 'UTF-8') ?>"
                   class="rounded-lg border border-stone-200 bg-white px-3 py-2 text-xs font-semibold text-stone-700 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white <?= $prevDisabled ? 'pointer-events-none opacity-40' : '' ?>">
                    Previous
                </a>
                <a href="<?= htmlspecialchars($pageLink($page + 1), ENT_QUOTES, 'UTF-8') ?>"
                   class="rounded-lg border border-stone-200 bg-white px-3 py-2 text-xs font-semibold text-stone-700 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white <?= $nextDisabled ? 'pointer-events-none opacity-40' : '' ?>">
                    Next
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once base_path('views/dashboard/partials/modals/comments.php'); ?>
<?php require_once base_path('views/dashboard/partials/footer.php'); ?>
