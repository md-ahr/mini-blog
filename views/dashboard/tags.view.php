<?php
/**
 * @var string $pageTitle
 * @var string $heading
 * @var string|null $subheading
 * @var string $dashboardNav
 * @var list<array<string, mixed>> $tags
 * @var string $sort
 * @var string $tagsUrl
 * @var string $flashSuccess
 * @var string $flashError
 * @var string $csrfToken
 */
require_once base_path('views/dashboard/partials/head.php');
require_once base_path('views/dashboard/partials/sidebar.php');

$h = static fn (string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');

$tags = $tags ?? [];
$sort = $sort ?? 'usage';
$tagsUrl = $tagsUrl ?? blog_url('dashboard/tags');
$flashSuccess = $flashSuccess ?? '';
$flashError = $flashError ?? '';
$csrfToken = $csrfToken ?? auth_csrf_token();

$pageActions = <<<'HTML'
<button type="button" data-modal-open="modal-tag-add" class="inline-flex items-center justify-center rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
    Add tag
</button>
HTML;

$actionBtn = 'rounded-lg border border-stone-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-stone-800 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
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

<div class="grid gap-6 lg:grid-cols-12">
    <div class="col-span-12">
        <div class="rounded-2xl border border-stone-200/90 bg-white shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
            <div class="flex flex-col gap-3 border-b border-stone-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                <div>
                    <h2 class="text-sm font-semibold text-stone-900">All tags</h2>
                    <p class="mt-1 text-xs text-stone-500">Deleting a tag removes its links from posts; posts are not deleted.</p>
                </div>
                <form method="get" action="<?= $h($tagsUrl) ?>" class="flex items-center gap-2">
                    <label class="sr-only" for="tag-sort">Sort tags</label>
                    <select id="tag-sort" name="sort" onchange="this.form.submit()"
                            class="<?= htmlspecialchars($dashboardSelect, ENT_QUOTES, 'UTF-8') ?> sm:w-auto sm:min-w-[12rem]">
                        <option value="usage" <?= $sort === 'usage' ? 'selected' : '' ?>>Most used</option>
                        <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>A–Z</option>
                        <option value="recent" <?= $sort === 'recent' ? 'selected' : '' ?>>Recently updated</option>
                    </select>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-100 text-left text-sm">
                    <thead class="bg-stone-50/80 text-xs font-semibold uppercase tracking-wider text-stone-500">
                    <tr>
                        <th scope="col" class="px-5 py-3">Tag</th>
                        <th scope="col" class="px-3 py-3">Slug</th>
                        <th scope="col" class="px-3 py-3 text-right">Posts</th>
                        <th scope="col" class="px-3 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 bg-white">
                    <?php if ($tags === []) : ?>
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-sm text-stone-600">No tags yet. Add one to label posts.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($tags as $tag) : ?>
                        <?php
                        $tid = (int) ($tag['id'] ?? 0);
                        $nm = (string) ($tag['name'] ?? '');
                        $sl = (string) ($tag['slug'] ?? '');
                        $pc = (int) ($tag['post_count'] ?? 0);
                        $col = blog_sanitize_color($tag['color'] ?? null, '#78716c');
                        $delBody = 'Remove the tag “' . $nm . '” from all posts? This cannot be undone.';
                        ?>
                        <tr class="hover:bg-stone-50/60">
                            <th scope="row" class="px-5 py-4">
                                <span class="inline-flex items-center gap-2 font-semibold text-stone-900">
                                    <span class="h-2.5 w-2.5 shrink-0 rounded-full ring-2 ring-stone-200/80"
                                          style="background-color: <?= $h($col) ?>" aria-hidden="true"></span>
                                    <?= $h($nm) ?>
                                </span>
                            </th>
                            <td class="px-3 py-4 font-mono text-xs text-stone-600"><?= $h($sl) ?></td>
                            <td class="px-3 py-4 text-right tabular-nums text-stone-700"><?= $pc ?></td>
                            <td class="px-3 py-4 text-right">
                                <div class="inline-flex flex-wrap justify-end gap-2">
                                    <button type="button" class="<?= $actionBtn ?>"
                                            data-modal-open="modal-tag-view"
                                            data-tag-name="<?= $h($nm) ?>"
                                            data-tag-slug="<?= $h($sl) ?>"
                                            data-tag-posts="<?= $h((string) $pc) ?>"
                                            data-tag-color="<?= $h($col) ?>">View
                                    </button>
                                    <button type="button" class="<?= $actionBtn ?>"
                                            data-modal-open="modal-tag-edit"
                                            data-tag-id="<?= $tid ?>"
                                            data-tag-name="<?= $h($nm) ?>"
                                            data-tag-slug="<?= $h($sl) ?>"
                                            data-tag-posts="<?= $h((string) $pc) ?>"
                                            data-tag-color="<?= $h($col) ?>">Edit
                                    </button>
                                    <button type="button"
                                            class="rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-800 shadow-sm hover:border-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/70 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                                            data-modal-open="dashboard-confirm-modal"
                                            data-confirm-title="Delete this tag?"
                                            data-confirm-body="<?= $h($delBody) ?>"
                                            data-confirm-label="Delete tag"
                                            data-confirm-submit-form="delete-tag-form-<?= $tid ?>">Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php foreach ($tags as $tag) : ?>
    <?php $tid = (int) ($tag['id'] ?? 0); ?>
    <?php if ($tid < 1) {
      continue;
    } ?>
    <form id="delete-tag-form-<?= $tid ?>" method="post" action="<?= $h($tagsUrl) ?>" hidden>
        <input type="hidden" name="_csrf" value="<?= $h($csrfToken) ?>"/>
        <input type="hidden" name="_action" value="delete"/>
        <input type="hidden" name="id" value="<?= $tid ?>"/>
    </form>
<?php endforeach; ?>

<?php require_once base_path('views/dashboard/partials/modals/tags.php'); ?>
<?php require_once base_path('views/dashboard/partials/footer.php'); ?>
