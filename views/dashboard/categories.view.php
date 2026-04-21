<?php
/**
 * @var string $pageTitle
 * @var string $heading
 * @var string|null $subheading
 * @var string $dashboardNav
 * @var list<array<string, mixed>> $categories
 * @var string $categoriesUrl
 * @var string $flashSuccess
 * @var string $flashError
 * @var string $csrfToken
 */
require_once base_path('views/dashboard/partials/head.php');
require_once base_path('views/dashboard/partials/sidebar.php');

$h = static fn (string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');

$categories = $categories ?? [];
$categoriesUrl = $categoriesUrl ?? blog_url('dashboard/categories');
$flashSuccess = $flashSuccess ?? '';
$flashError = $flashError ?? '';
$csrfToken = $csrfToken ?? auth_csrf_token();

$pageActions = <<<'HTML'
<button type="button" data-modal-open="modal-category-add" class="inline-flex items-center justify-center rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
    New category
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

<div class="grid gap-6 xl:grid-cols-12">
    <section class="col-span-12" aria-labelledby="cat-list-heading">
        <div class="rounded-2xl border border-stone-200/90 bg-white shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
            <div class="border-b border-stone-100 p-5">
                <h2 id="cat-list-heading" class="text-sm font-semibold text-stone-900">Categories</h2>
                <p class="mt-1 text-xs text-stone-500">Deleting a category unsets it on posts (posts are kept). Child categories become top-level if their parent is removed.</p>
            </div>
            <ul class="divide-y divide-stone-100" role="list">
                <?php if ($categories === []) : ?>
                    <li class="px-5 py-10 text-center text-sm text-stone-600">No categories yet.</li>
                <?php endif; ?>
                <?php foreach ($categories as $cat) : ?>
                    <?php
                    $cid = (int) ($cat['id'] ?? 0);
                    $nm = (string) ($cat['name'] ?? '');
                    $sl = (string) ($cat['slug'] ?? '');
                    $pc = (int) ($cat['post_count'] ?? 0);
                    $ds = trim((string) ($cat['description'] ?? ''));
                    $cc = blog_sanitize_color($cat['color'] ?? null, '#57534e');
                    $pl = trim((string) ($cat['parent_label'] ?? ''));
                    $pid = $cat['parent_id'];
                    $sortOrder = (int) ($cat['sort_order'] ?? 0);
                    $delBody = 'Delete “' . $nm . '”? Posts in this category will have no category until you assign another.';
                    ?>
                    <li class="px-5 py-4 hover:bg-stone-50/60">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="flex min-w-0 gap-3">
                                <span class="mt-1 h-auto w-1 shrink-0 self-stretch rounded-full sm:mt-1.5"
                                      style="background-color: <?= $h($cc) ?>" aria-hidden="true"></span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-sm font-semibold text-stone-900"><?= $h($nm) ?></p>
                                        <?php if ($pl !== '') : ?>
                                            <span class="rounded-full bg-stone-100 px-2 py-0.5 text-[11px] font-medium text-stone-600 ring-1 ring-stone-200/80">Under <?= $h($pl) ?></span>
                                        <?php endif; ?>
                                        <span class="rounded-full bg-stone-100 px-2 py-0.5 text-[11px] font-semibold text-stone-700 ring-1 ring-stone-200/80 tabular-nums"><?= $pc ?> posts</span>
                                    </div>
                                    <?php if ($ds !== '') : ?>
                                        <p class="mt-2 text-sm text-stone-600"><?= $h($ds) ?></p>
                                    <?php endif; ?>
                                    <p class="mt-2 font-mono text-xs text-stone-500">/<?= $h($sl) ?> · sort <?= $sortOrder ?></p>
                                </div>
                            </div>
                            <div class="flex shrink-0 flex-wrap gap-2 sm:justify-end">
                                <button type="button" class="<?= $actionBtn ?>"
                                        data-modal-open="modal-category-view"
                                        data-cat-name="<?= $h($nm) ?>"
                                        data-cat-slug="<?= $h($sl) ?>"
                                        data-cat-posts="<?= $h((string) $pc) ?>"
                                        data-cat-description="<?= $h($ds) ?>"
                                        data-cat-color="<?= $h($cc) ?>"
                                        data-cat-parent="<?= $h($pl) ?>">View
                                </button>
                                <button type="button" class="<?= $actionBtn ?>"
                                        data-modal-open="modal-category-edit"
                                        data-cat-id="<?= $cid ?>"
                                        data-cat-name="<?= $h($nm) ?>"
                                        data-cat-slug="<?= $h($sl) ?>"
                                        data-cat-posts="<?= $h((string) $pc) ?>"
                                        data-cat-description="<?= $h($ds) ?>"
                                        data-cat-color="<?= $h($cc) ?>"
                                        data-cat-parent-id="<?= $h($pid !== null && $pid !== '' ? (string) (int) $pid : '') ?>"
                                        data-cat-sort="<?= $h((string) $sortOrder) ?>">Edit
                                </button>
                                <button type="button"
                                        class="rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-800 shadow-sm hover:border-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/70 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                                        data-modal-open="dashboard-confirm-modal"
                                        data-confirm-title="Delete this category?"
                                        data-confirm-body="<?= $h($delBody) ?>"
                                        data-confirm-label="Delete category"
                                        data-confirm-submit-form="delete-category-form-<?= $cid ?>">Delete
                                </button>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
</div>

<?php foreach ($categories as $cat) : ?>
    <?php $cid = (int) ($cat['id'] ?? 0); ?>
    <?php if ($cid < 1) {
      continue;
    } ?>
    <form id="delete-category-form-<?= $cid ?>" method="post" action="<?= $h($categoriesUrl) ?>" hidden>
        <input type="hidden" name="_csrf" value="<?= $h($csrfToken) ?>"/>
        <input type="hidden" name="_action" value="delete"/>
        <input type="hidden" name="id" value="<?= $cid ?>"/>
    </form>
<?php endforeach; ?>

<?php require_once base_path('views/dashboard/partials/modals/categories.php'); ?>
<?php require_once base_path('views/dashboard/partials/footer.php'); ?>
