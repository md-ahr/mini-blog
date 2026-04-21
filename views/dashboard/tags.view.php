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
<button type="button" data-modal-open="modal-tag-add" class="inline-flex items-center justify-center rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
    Add tag
</button>
HTML;

$demoTags = [
        ['name' => 'Essay', 'slug' => 'essay', 'posts' => 9, 'color' => '#d97706'],
        ['name' => 'Field notes', 'slug' => 'field-notes', 'posts' => 4, 'color' => '#0d9488'],
        ['name' => 'Letters', 'slug' => 'letters', 'posts' => 6, 'color' => '#7c3aed'],
        ['name' => 'Process', 'slug' => 'process', 'posts' => 3, 'color' => '#57534e'],
        ['name' => 'Design', 'slug' => 'design', 'posts' => 7, 'color' => '#be185d'],
];
$actionBtn = 'rounded-lg border border-stone-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-stone-800 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
?>

<?php require_once base_path('views/dashboard/partials/page-header.php'); ?>

<div class="grid gap-6 lg:grid-cols-12">
    <div class="col-span-12">
        <div class="rounded-2xl border border-stone-200/90 bg-white shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
            <div class="flex flex-col gap-3 border-b border-stone-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                <div>
                    <h2 class="text-sm font-semibold text-stone-900">All tags</h2>
                    <p class="mt-1 text-xs text-stone-500">Rename, merge, and delete will map to your schema later.</p>
                </div>
                <div class="flex items-center gap-2">
                    <label class="sr-only" for="tag-sort">Sort</label>
                    <select id="tag-sort"
                            class="<?= htmlspecialchars($dashboardSelect, ENT_QUOTES, 'UTF-8') ?> sm:w-auto sm:min-w-[12rem]">
                        <option>Most used</option>
                        <option>A–Z</option>
                        <option>Recently added</option>
                    </select>
                </div>
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
                    <?php foreach ($demoTags as $tag) : ?>
                        <?php
                        $nm = htmlspecialchars($tag['name'], ENT_QUOTES, 'UTF-8');
                        $sl = htmlspecialchars($tag['slug'], ENT_QUOTES, 'UTF-8');
                        $pc = htmlspecialchars((string)(int)$tag['posts'], ENT_QUOTES, 'UTF-8');
                        $col = htmlspecialchars($tag['color'] ?? '#78716c', ENT_QUOTES, 'UTF-8');
                        $delBody = htmlspecialchars('Delete the tag "' . $tag['name'] . '"? Posts will keep their content, but the label may detach.', ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr class="hover:bg-stone-50/60">
                            <th scope="row" class="px-5 py-4">
                                <span class="inline-flex items-center gap-2 font-semibold text-stone-900">
                                    <span class="h-2.5 w-2.5 shrink-0 rounded-full ring-2 ring-stone-200/80"
                                          style="background-color: <?= $col ?>" aria-hidden="true"></span>
                                    <?= $nm ?>
                                </span>
                            </th>
                            <td class="px-3 py-4 font-mono text-xs text-stone-600"><?= $sl ?></td>
                            <td class="px-3 py-4 text-right tabular-nums text-stone-700"><?= (int)$tag['posts'] ?></td>
                            <td class="px-3 py-4 text-right">
                                <div class="inline-flex flex-wrap justify-end gap-2">
                                    <button type="button" class="<?= $actionBtn ?>"
                                            data-modal-open="modal-tag-view"
                                            data-tag-name="<?= $nm ?>"
                                            data-tag-slug="<?= $sl ?>"
                                            data-tag-posts="<?= $pc ?>"
                                            data-tag-color="<?= $col ?>">View
                                    </button>
                                    <button type="button" class="<?= $actionBtn ?>"
                                            data-modal-open="modal-tag-edit"
                                            data-tag-name="<?= $nm ?>"
                                            data-tag-slug="<?= $sl ?>"
                                            data-tag-posts="<?= $pc ?>"
                                            data-tag-color="<?= $col ?>">Edit
                                    </button>
                                    <button type="button"
                                            class="rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-800 shadow-sm hover:border-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/70 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                                            data-modal-open="dashboard-confirm-modal"
                                            data-confirm-title="Delete this tag?"
                                            data-confirm-body="<?= $delBody ?>"
                                            data-confirm-label="Delete tag">Delete
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

<?php require_once base_path('views/dashboard/partials/modals/tags.php'); ?>
<?php require_once base_path('views/dashboard/partials/footer.php'); ?>
