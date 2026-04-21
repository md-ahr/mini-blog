<?php
/**
 * @var string $pageTitle
 * @var string $heading
 * @var string|null $subheading
 * @var string $dashboardNav
 * @var list<array<string, mixed>> $posts
 * @var list<array<string, mixed>> $users
 * @var list<array<string, mixed>> $categories
 * @var string $postsUrl
 * @var array{q: string, status: string, user_id: int, category_id: string} $filters
 * @var int $page
 * @var int $totalPages
 * @var int $totalPosts
 * @var int $perPage
 * @var int $currentUserId
 * @var bool $canManageAllPosts
 * @var string $flashSuccess
 * @var string $flashError
 * @var string $csrfToken
 */
require_once base_path('views/dashboard/partials/head.php');
require_once base_path('views/dashboard/partials/sidebar.php');

$h = static fn (string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');

$posts = $posts ?? [];
$users = $users ?? [];
$categories = $categories ?? [];
$postsUrl = $postsUrl ?? blog_url('dashboard/posts');
$filters = $filters ?? ['q' => '', 'status' => '', 'user_id' => 0, 'category_id' => ''];
$page = max(1, (int) ($page ?? 1));
$totalPages = max(1, (int) ($totalPages ?? 1));
$totalPosts = (int) ($totalPosts ?? 0);
$perPage = (int) ($perPage ?? 15);
$flashSuccess = $flashSuccess ?? '';
$flashError = $flashError ?? '';
$csrfToken = $csrfToken ?? auth_csrf_token();
$canManageAllPosts = $canManageAllPosts ?? true;

$jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE;

$postsPageUrl = static function (array $override) use ($postsUrl, $filters): string {
  $params = [];
  $q = trim((string) $filters['q']);
  if ($q !== '') {
    $params['q'] = $q;
  }
  if ($filters['status'] !== '') {
    $params['status'] = $filters['status'];
  }
  if ($filters['user_id'] > 0) {
    $params['user_id'] = $filters['user_id'];
  }
  if ($filters['category_id'] !== '') {
    $params['category_id'] = $filters['category_id'];
  }
  $params = array_merge($params, $override);
  if (isset($params['page']) && (int) $params['page'] <= 1) {
    unset($params['page']);
  }
  return $params === [] ? $postsUrl : $postsUrl . '?' . http_build_query($params);
};

$pageActions = <<<'HTML'
<button type="button" data-modal-open="modal-post-add" class="inline-flex items-center justify-center rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
    New post
</button>
HTML;

$actionBtn = 'rounded-lg border border-stone-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-stone-800 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
$startItem = $totalPosts === 0 ? 0 : (($page - 1) * $perPage) + 1;
$endItem = min($totalPosts, $page * $perPage);
?>

<?php require_once base_path('views/dashboard/partials/page-header.php'); ?>

<?php if ($flashSuccess !== '') : ?>
    <div class="mb-6 rounded-xl border border-emerald-200/90 bg-emerald-50/70 px-4 py-3 text-sm font-medium text-emerald-900 ring-1 ring-emerald-100/80" role="status">
        <?= $h($flashSuccess) ?>
    </div>
<?php endif; ?>
<?php if ($flashError !== '') : ?>
    <div class="mb-6 rounded-xl border border-red-200/90 bg-red-50/70 px-4 py-3 text-sm font-medium text-red-900 ring-1 ring-red-100/80" role="alert">
        <?= $h($flashError) ?>
    </div>
<?php endif; ?>

<div class="rounded-2xl border border-stone-200/90 bg-white shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
    <form method="get" action="<?= $h($postsUrl) ?>"
          class="flex flex-col gap-4 border-b border-stone-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
        <div class="flex min-w-0 flex-1 flex-col gap-3 sm:flex-row sm:items-center">
            <label class="sr-only" for="post-search">Search posts</label>
            <div class="relative min-w-[12rem] flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-stone-400" aria-hidden="true">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                </span>
                <input id="post-search" name="q" type="search" placeholder="Search titles…" autocomplete="off"
                       value="<?= $h((string) $filters['q']) ?>"
                       class="w-full rounded-xl border border-stone-200 bg-stone-50/60 py-2.5 pl-10 pr-3 text-sm text-stone-900 placeholder:text-stone-400 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
            </div>
            <div class="flex flex-wrap gap-2">
                <label class="sr-only" for="post-status">Status</label>
                <select id="post-status" name="status"
                        class="<?= htmlspecialchars($dashboardSelect, ENT_QUOTES, 'UTF-8') ?> sm:w-auto sm:min-w-[9.5rem]">
                    <option value="" <?= $filters['status'] === '' ? 'selected' : '' ?>>All statuses</option>
                    <option value="draft" <?= $filters['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="published" <?= $filters['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="scheduled" <?= $filters['status'] === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                </select>
                <?php if ($canManageAllPosts) : ?>
                <label class="sr-only" for="post-author">Author</label>
                <select id="post-author" name="user_id"
                        class="<?= htmlspecialchars($dashboardSelect, ENT_QUOTES, 'UTF-8') ?> sm:w-auto sm:min-w-[9.5rem]">
                    <option value="0">All authors</option>
                    <?php foreach ($users as $u) : ?>
                        <?php $uid = (int) ($u['id'] ?? 0); ?>
                        <option value="<?= $uid ?>" <?= $filters['user_id'] === $uid ? 'selected' : '' ?>><?= $h((string) ($u['name'] ?? '')) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
                <label class="sr-only" for="post-category-filter">Category</label>
                <select id="post-category-filter" name="category_id"
                        class="<?= htmlspecialchars($dashboardSelect, ENT_QUOTES, 'UTF-8') ?> sm:w-auto sm:min-w-[11.5rem]">
                    <option value="" <?= $filters['category_id'] === '' ? 'selected' : '' ?>>All categories</option>
                    <option value="0" <?= $filters['category_id'] === '0' ? 'selected' : '' ?>>Uncategorized</option>
                    <?php foreach ($categories as $c) : ?>
                        <?php $cid = (int) ($c['id'] ?? 0); ?>
                        <option value="<?= $cid ?>" <?= (string) $filters['category_id'] === (string) $cid ? 'selected' : '' ?>><?= $h((string) ($c['name'] ?? '')) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit"
                        class="rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                    Apply
                </button>
            </div>
        </div>
        <p class="text-xs font-medium text-stone-500">
            Showing <span class="tabular-nums text-stone-800"><?= (int) $startItem ?>–<?= (int) $endItem ?></span>
            of <span class="tabular-nums text-stone-800"><?= (int) $totalPosts ?></span>
        </p>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-stone-100 text-left text-sm">
            <thead class="bg-stone-50/80 text-xs font-semibold uppercase tracking-wider text-stone-500">
            <tr>
                <th scope="col" class="hidden w-20 px-3 py-3 sm:table-cell">Image</th>
                <th scope="col" class="px-5 py-3">Title</th>
                <th scope="col" class="hidden px-3 py-3 md:table-cell">Status</th>
                <th scope="col" class="hidden px-3 py-3 lg:table-cell">Author</th>
                <th scope="col" class="hidden px-3 py-3 lg:table-cell">Category</th>
                <th scope="col" class="hidden px-3 py-3 xl:table-cell">Updated</th>
                <th scope="col" class="px-3 py-3"><span class="sr-only">Actions</span></th>
            </tr>
            </thead>
            <tbody class="divide-y divide-stone-100 bg-white">
            <?php if ($posts === []) : ?>
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-sm text-stone-600">No posts match your filters.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($posts as $post) : ?>
                <?php
                $pid = (int) ($post['id'] ?? 0);
                if ($pid < 1) {
                  continue;
                }
                $stRaw = (string) ($post['status'] ?? 'draft');
                $statusLabel = $stRaw !== '' ? ucfirst($stRaw) : 'Draft';
                $statusClass = match ($stRaw) {
                  'published' => 'bg-emerald-50 text-emerald-900 ring-emerald-200/70',
                  'draft' => 'bg-stone-100 text-stone-800 ring-stone-200/80',
                  'scheduled' => 'bg-sky-50 text-sky-950 ring-sky-200/80',
                  default => 'bg-stone-100 text-stone-800 ring-stone-200/80',
                };
                $title = (string) ($post['title'] ?? '');
                $slug = (string) ($post['slug'] ?? '');
                $author = (string) ($post['author_name'] ?? '');
                $tagStr = trim((string) ($post['tag_names'] ?? ''));
                $catName = trim((string) ($post['category_name'] ?? ''));
                $catDisp = $catName !== '' ? $catName : '—';
                $excerpt = (string) ($post['excerpt'] ?? '');
                $imgUrl = trim((string) ($post['featured_image_url'] ?? ''));
                $upd = $post['updated_at'] ?? null;
                $updDt = $upd ? date_create((string) $upd) : false;
                $updDisp = $updDt instanceof DateTimeInterface ? blog_format_localized_date($updDt, 'datetime') : '—';
                $delBody = 'Delete “' . $title . '”? This cannot be undone.';
                ?>
                <tr class="hover:bg-stone-50/60">
                    <td class="hidden px-3 py-4 sm:table-cell">
                        <?php if ($imgUrl !== '') : ?>
                            <img src="<?= $h($imgUrl) ?>" alt="" class="h-11 w-16 rounded-lg object-cover ring-1 ring-stone-200/80"/>
                        <?php else : ?>
                            <span class="flex h-11 w-16 items-center justify-center rounded-lg bg-stone-100 text-[10px] font-medium text-stone-400 ring-1 ring-stone-200/80" aria-hidden="true">—</span>
                        <?php endif; ?>
                    </td>
                    <th scope="row" class="px-5 py-4 font-semibold text-stone-900">
                        <div class="flex flex-col gap-1">
                            <span class="line-clamp-2"><?= $h($title) ?></span>
                            <span class="inline-flex w-fit flex-wrap gap-1 md:hidden">
                                <?php if ($tagStr !== '') : ?>
                                    <span class="max-w-[12rem] truncate rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-950 ring-1 ring-amber-200/80"><?= $h($tagStr) ?></span>
                                <?php endif; ?>
                                <span class="rounded-full bg-stone-100 px-2 py-0.5 text-[11px] font-semibold text-stone-800 ring-1 ring-stone-200/80"><?= $h($catDisp) ?></span>
                            </span>
                        </div>
                    </th>
                    <td class="hidden px-3 py-4 md:table-cell">
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 <?= $h($statusClass) ?>">
                            <?= $h($statusLabel) ?>
                        </span>
                    </td>
                    <td class="hidden px-3 py-4 text-stone-600 lg:table-cell"><?= $h($author) ?></td>
                    <td class="hidden px-3 py-4 text-stone-600 lg:table-cell"><?= $h($catDisp) ?></td>
                    <td class="hidden px-3 py-4 tabular-nums text-stone-600 xl:table-cell"><?= $h($updDisp) ?></td>
                    <td class="px-3 py-4 text-right">
                        <div class="inline-flex flex-wrap items-center justify-end gap-2">
                            <button type="button" class="<?= $actionBtn ?>"
                                    data-modal-open="modal-post-view"
                                    data-post-payload="post-view-json-<?= $pid ?>">View</button>
                            <button type="button" class="<?= $actionBtn ?>"
                                    data-modal-open="modal-post-edit"
                                    data-post-payload="post-edit-json-<?= $pid ?>">Edit</button>
                            <button type="button"
                                    class="rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-800 shadow-sm hover:border-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/70 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                                    data-modal-open="dashboard-confirm-modal"
                                    data-confirm-title="Delete this post?"
                                    data-confirm-body="<?= $h($delBody) ?>"
                                    data-confirm-label="Delete post"
                                    data-confirm-submit-form="delete-post-form-<?= $pid ?>">Delete</button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php foreach ($posts as $post) : ?>
        <?php
        $pid = (int) ($post['id'] ?? 0);
        if ($pid < 1) {
          continue;
        }
        $stRaw = (string) ($post['status'] ?? 'draft');
        $statusLabel = $stRaw !== '' ? ucfirst($stRaw) : 'Draft';
        $title = (string) ($post['title'] ?? '');
        $slug = (string) ($post['slug'] ?? '');
        $author = (string) ($post['author_name'] ?? '');
        $tagStr = trim((string) ($post['tag_names'] ?? ''));
        $catName = trim((string) ($post['category_name'] ?? ''));
        $catDisp = $catName !== '' ? $catName : '—';
        $excerpt = (string) ($post['excerpt'] ?? '');
        $imgUrl = trim((string) ($post['featured_image_url'] ?? ''));
        $upd = $post['updated_at'] ?? null;
        $updDt = $upd ? date_create((string) $upd) : false;
        $updDisp = $updDt instanceof DateTimeInterface ? blog_format_localized_date($updDt, 'datetime') : '—';
        $pubForm = '';
        $pAt = $post['published_at'] ?? null;
        if ($pAt !== null && $pAt !== '') {
          $pubForm = (string) $pAt;
        }
        $schedForm = '';
        $sAt = $post['scheduled_at'] ?? null;
        if ($sAt !== null && $sAt !== '') {
          $sd = date_create_immutable((string) $sAt);
          if ($sd) {
            $schedForm = $sd->format('Y-m-d\TH:i');
          }
        }
        $catId = $post['category_id'];
        $bodyText = blog_post_content_to_body_text($post['content'] ?? '');
        $reading = $post['reading_minutes'];
        $readingDisp = $reading !== null && $reading !== '' ? (string) (int) $reading : '';
        $uid = (int) ($post['user_id'] ?? 0);
        $editPayload = [
          'id' => $pid,
          'slug' => $slug,
          'title' => $title,
          'status' => $stRaw,
          'user_id' => $uid,
          'category_id' => $catId !== null && $catId !== '' ? (int) $catId : '',
          'tags' => $tagStr,
          'excerpt' => $excerpt,
          'content_body' => $bodyText,
          'featured_image_url' => $imgUrl,
          'reading_minutes' => $readingDisp,
          'published_at' => $pubForm,
          'scheduled_at' => $schedForm,
        ];
        $viewPayload = [
          'title' => $title,
          'slug' => $slug,
          'status_label' => $statusLabel,
          'author_name' => $author,
          'tags' => $tagStr !== '' ? $tagStr : '—',
          'category_name' => $catDisp,
          'updated' => $updDisp,
          'excerpt' => $excerpt !== '' ? $excerpt : '—',
          'featured_image_url' => $imgUrl,
        ];
        $editJson = json_encode($editPayload, $jsonFlags);
        $viewJson = json_encode($viewPayload, $jsonFlags);
        ?>
        <script type="application/json" id="post-edit-json-<?= $pid ?>"><?= $editJson !== false ? $editJson : '{}' ?></script>
        <script type="application/json" id="post-view-json-<?= $pid ?>"><?= $viewJson !== false ? $viewJson : '{}' ?></script>
    <?php endforeach; ?>

    <div class="flex flex-col items-center justify-between gap-3 border-t border-stone-100 px-5 py-4 sm:flex-row">
        <p class="text-xs text-stone-500">Page <?= (int) $page ?> of <?= (int) $totalPages ?></p>
        <div class="flex items-center gap-2">
            <?php if ($page > 1) : ?>
                <a href="<?= $h($postsPageUrl(['page' => $page - 1])) ?>"
                   class="rounded-lg border border-stone-200 bg-white px-3 py-2 text-xs font-semibold text-stone-700 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white">Previous</a>
            <?php else : ?>
                <span class="rounded-lg border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-semibold text-stone-400">Previous</span>
            <?php endif; ?>
            <?php if ($page < $totalPages) : ?>
                <a href="<?= $h($postsPageUrl(['page' => $page + 1])) ?>"
                   class="rounded-lg border border-stone-200 bg-white px-3 py-2 text-xs font-semibold text-stone-700 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white">Next</a>
            <?php else : ?>
                <span class="rounded-lg border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-semibold text-stone-400">Next</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php foreach ($posts as $post) : ?>
    <?php $pid = (int) ($post['id'] ?? 0); ?>
    <?php if ($pid < 1) {
      continue;
    } ?>
    <form id="delete-post-form-<?= $pid ?>" method="post" action="<?= $h($postsUrl) ?>" hidden>
        <input type="hidden" name="_csrf" value="<?= $h($csrfToken) ?>"/>
        <input type="hidden" name="_action" value="delete"/>
        <input type="hidden" name="id" value="<?= $pid ?>"/>
    </form>
<?php endforeach; ?>

<?php require_once base_path('views/dashboard/partials/modals/posts.php'); ?>
<?php require_once base_path('views/dashboard/partials/footer.php'); ?>
