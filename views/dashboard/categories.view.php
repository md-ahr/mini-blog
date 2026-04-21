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
<button type="button" data-modal-open="modal-category-add" class="inline-flex items-center justify-center rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
    New category
</button>
HTML;

$demoCategories = [
        ['name' => 'Journal', 'slug' => 'journal', 'posts' => 14, 'description' => 'Longer essays and personal writing.', 'color' => '#b45309'],
        ['name' => 'Workbench', 'slug' => 'workbench', 'posts' => 6, 'description' => 'Build notes, tools, and experiments.', 'color' => '#0369a1'],
        ['name' => 'Reading list', 'slug' => 'reading-list', 'posts' => 4, 'description' => 'Short reactions and highlights.', 'color' => '#4f46e5'],
];
$actionBtn = 'rounded-lg border border-stone-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-stone-800 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
?>

<?php require_once base_path('views/dashboard/partials/page-header.php'); ?>

<div class="grid gap-6 xl:grid-cols-12">
    <section class="col-span-12" aria-labelledby="cat-list-heading">
        <div class="rounded-2xl border border-stone-200/90 bg-white shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
            <div class="border-b border-stone-100 p-5">
                <h2 id="cat-list-heading" class="text-sm font-semibold text-stone-900">Categories</h2>
                <p class="mt-1 text-xs text-stone-500">Order, parents, and SEO fields can land here when you model the
                    tree.</p>
            </div>
            <ul class="divide-y divide-stone-100" role="list">
                <?php foreach ($demoCategories as $cat) : ?>
                    <?php
                    $nm = htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8');
                    $sl = htmlspecialchars($cat['slug'], ENT_QUOTES, 'UTF-8');
                    $pc = htmlspecialchars((string)(int)$cat['posts'], ENT_QUOTES, 'UTF-8');
                    $ds = htmlspecialchars($cat['description'], ENT_QUOTES, 'UTF-8');
                    $cc = htmlspecialchars($cat['color'] ?? '#57534e', ENT_QUOTES, 'UTF-8');
                    $delBody = htmlspecialchars('Delete the category "' . $cat['name'] . '"? Archive URLs may change.', ENT_QUOTES, 'UTF-8');
                    ?>
                    <li class="px-5 py-4 hover:bg-stone-50/60">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="flex min-w-0 gap-3">
                                <span class="mt-1 h-auto w-1 shrink-0 self-stretch rounded-full sm:mt-1.5"
                                      style="background-color: <?= $cc ?>" aria-hidden="true"></span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-sm font-semibold text-stone-900"><?= $nm ?></p>
                                        <span class="rounded-full bg-stone-100 px-2 py-0.5 text-[11px] font-semibold text-stone-700 ring-1 ring-stone-200/80 tabular-nums"><?= (int)$cat['posts'] ?> posts</span>
                                    </div>
                                    <p class="mt-2 text-sm text-stone-600"><?= $ds ?></p>
                                    <p class="mt-2 font-mono text-xs text-stone-500">/<?= $sl ?></p>
                                </div>
                            </div>
                            <div class="flex shrink-0 flex-wrap gap-2 sm:justify-end">
                                <button type="button" class="<?= $actionBtn ?>"
                                        data-modal-open="modal-category-view"
                                        data-cat-name="<?= $nm ?>"
                                        data-cat-slug="<?= $sl ?>"
                                        data-cat-posts="<?= $pc ?>"
                                        data-cat-description="<?= $ds ?>"
                                        data-cat-color="<?= $cc ?>">View
                                </button>
                                <button type="button" class="<?= $actionBtn ?>"
                                        data-modal-open="modal-category-edit"
                                        data-cat-name="<?= $nm ?>"
                                        data-cat-slug="<?= $sl ?>"
                                        data-cat-posts="<?= $pc ?>"
                                        data-cat-description="<?= $ds ?>"
                                        data-cat-color="<?= $cc ?>">Edit
                                </button>
                                <button type="button" class="<?= $actionBtn ?>"
                                        data-modal-open="dashboard-confirm-modal"
                                        data-confirm-title="Reorder categories?"
                                        data-confirm-body="Open the drag-and-drop sorter when it exists. This is a placeholder confirmation."
                                        data-confirm-label="Continue"
                                        data-confirm-variant="primary">Reorder
                                </button>
                                <button type="button"
                                        class="rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-800 shadow-sm hover:border-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/70 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                                        data-modal-open="dashboard-confirm-modal"
                                        data-confirm-title="Delete this category?"
                                        data-confirm-body="<?= $delBody ?>"
                                        data-confirm-label="Delete category">Delete
                                </button>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
</div>

<?php require_once base_path('views/dashboard/partials/modals/categories.php'); ?>
<?php require_once base_path('views/dashboard/partials/footer.php'); ?>
