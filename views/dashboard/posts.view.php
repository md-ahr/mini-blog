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
<button type="button" data-modal-open="modal-post-add" class="inline-flex items-center justify-center rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
    New post
</button>
HTML;

$demoPosts = [
    [
        'title' => 'Winter light on the river',
        'slug' => 'winter-light-on-the-river',
        'status' => 'Published',
        'author' => 'Alex Rowan',
        'updated' => 'Apr 18, 2026',
        'tag' => 'Essay',
        'category' => 'Journal',
        'excerpt' => 'Morning haze, slow water, and why long walks improve editing.',
        'image_url' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=240&fit=crop&q=80',
    ],
    [
        'title' => 'A small CMS checklist',
        'slug' => 'a-small-cms-checklist',
        'status' => 'Draft',
        'author' => 'Alex Rowan',
        'updated' => 'Apr 16, 2026',
        'tag' => 'Process',
        'category' => 'Workbench',
        'excerpt' => 'Drafts, slugs, previews, and the boring stuff that saves you on launch day.',
        'image_url' => '',
    ],
    [
        'title' => 'Notes from a slower inbox',
        'slug' => 'notes-from-a-slower-inbox',
        'status' => 'Scheduled',
        'author' => 'Jamie Liu',
        'updated' => 'Apr 12, 2026',
        'tag' => 'Letters',
        'category' => 'Journal',
        'excerpt' => 'What changes when you answer email in one calm block, twice a week.',
        'image_url' => 'https://images.unsplash.com/photo-1497215728101-856f1ea74174?w=400&h=240&fit=crop&q=80',
    ],
    [
        'title' => 'Typography in quiet UIs',
        'slug' => 'typography-in-quiet-uis',
        'status' => 'Published',
        'author' => 'Alex Rowan',
        'updated' => 'Mar 29, 2026',
        'tag' => 'Design',
        'category' => 'Reading list',
        'excerpt' => 'Hierarchy without shouting: spacing, measure, and type that feels effortless.',
        'image_url' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=400&h=240&fit=crop&q=80',
    ],
];
?>

<?php require_once base_path('views/dashboard/partials/page-header.php'); ?>

<div class="rounded-2xl border border-stone-200/90 bg-white shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
    <div class="flex flex-col gap-4 border-b border-stone-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
        <div class="flex min-w-0 flex-1 flex-col gap-3 sm:flex-row sm:items-center">
            <label class="sr-only" for="post-search">Search posts</label>
            <div class="relative min-w-[12rem] flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-stone-400" aria-hidden="true">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                </span>
                <input id="post-search" type="search" placeholder="Search titles…" autocomplete="off"
                       class="w-full rounded-xl border border-stone-200 bg-stone-50/60 py-2.5 pl-10 pr-3 text-sm text-stone-900 placeholder:text-stone-400 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
            </div>
            <div class="flex flex-wrap gap-2">
                <label class="sr-only" for="post-status">Status</label>
                <select id="post-status"
                        class="<?= htmlspecialchars($dashboardSelect, ENT_QUOTES, 'UTF-8') ?> sm:w-auto sm:min-w-[9.5rem]">
                    <option>All statuses</option>
                    <option>Published</option>
                    <option>Draft</option>
                    <option>Scheduled</option>
                </select>
                <label class="sr-only" for="post-author">Author</label>
                <select id="post-author"
                        class="<?= htmlspecialchars($dashboardSelect, ENT_QUOTES, 'UTF-8') ?> sm:w-auto sm:min-w-[9.5rem]">
                    <option>All authors</option>
                    <option>Alex Rowan</option>
                    <option>Jamie Liu</option>
                </select>
                <label class="sr-only" for="post-category-filter">Category</label>
                <select id="post-category-filter"
                        class="<?= htmlspecialchars($dashboardSelect, ENT_QUOTES, 'UTF-8') ?> sm:w-auto sm:min-w-[11.5rem]">
                    <option>All categories</option>
                    <option>Journal</option>
                    <option>Workbench</option>
                    <option>Reading list</option>
                    <option>Uncategorized</option>
                </select>
            </div>
        </div>
        <p class="text-xs font-medium text-stone-500">Showing <span class="tabular-nums text-stone-800">4</span> of <span class="tabular-nums text-stone-800">24</span></p>
    </div>

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
            <?php foreach ($demoPosts as $post) : ?>
                <?php
                $statusClass = match ($post['status']) {
                    'Published' => 'bg-emerald-50 text-emerald-900 ring-emerald-200/70',
                    'Draft' => 'bg-stone-100 text-stone-800 ring-stone-200/80',
                    'Scheduled' => 'bg-sky-50 text-sky-950 ring-sky-200/80',
                    default => 'bg-stone-100 text-stone-800 ring-stone-200/80',
                };
                $t = htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8');
                $slug = htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8');
                $st = htmlspecialchars($post['status'], ENT_QUOTES, 'UTF-8');
                $au = htmlspecialchars($post['author'], ENT_QUOTES, 'UTF-8');
                $tg = htmlspecialchars($post['tag'], ENT_QUOTES, 'UTF-8');
                $cat = htmlspecialchars($post['category'], ENT_QUOTES, 'UTF-8');
                $up = htmlspecialchars($post['updated'], ENT_QUOTES, 'UTF-8');
                $ex = htmlspecialchars($post['excerpt'], ENT_QUOTES, 'UTF-8');
                $imgUrl = isset($post['image_url']) ? (string) $post['image_url'] : '';
                $imgAttr = htmlspecialchars($imgUrl, ENT_QUOTES, 'UTF-8');
                $delBody = htmlspecialchars('You are about to delete "' . $post['title'] . '". This cannot be undone.', ENT_QUOTES, 'UTF-8');
                $actionBtn = 'rounded-lg border border-stone-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-stone-800 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
                ?>
                <tr class="hover:bg-stone-50/60">
                    <td class="hidden px-3 py-4 sm:table-cell">
                        <?php if ($imgUrl !== '') : ?>
                            <img src="<?= $imgAttr ?>" alt="" class="h-11 w-16 rounded-lg object-cover ring-1 ring-stone-200/80"/>
                        <?php else : ?>
                            <span class="flex h-11 w-16 items-center justify-center rounded-lg bg-stone-100 text-[10px] font-medium text-stone-400 ring-1 ring-stone-200/80" aria-hidden="true">—</span>
                        <?php endif; ?>
                    </td>
                    <th scope="row" class="px-5 py-4 font-semibold text-stone-900">
                        <div class="flex flex-col gap-1">
                            <span class="line-clamp-2"><?= $t ?></span>
                            <span class="inline-flex w-fit flex-wrap gap-1 md:hidden">
                                <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-950 ring-1 ring-amber-200/80"><?= $tg ?></span>
                                <span class="rounded-full bg-stone-100 px-2 py-0.5 text-[11px] font-semibold text-stone-800 ring-1 ring-stone-200/80"><?= $cat ?></span>
                            </span>
                        </div>
                    </th>
                    <td class="hidden px-3 py-4 md:table-cell">
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 <?= htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8') ?>">
                            <?= $st ?>
                        </span>
                    </td>
                    <td class="hidden px-3 py-4 text-stone-600 lg:table-cell"><?= $au ?></td>
                    <td class="hidden px-3 py-4 text-stone-600 lg:table-cell"><?= $cat ?></td>
                    <td class="hidden px-3 py-4 tabular-nums text-stone-600 xl:table-cell"><?= $up ?></td>
                    <td class="px-3 py-4 text-right">
                        <div class="inline-flex flex-wrap items-center justify-end gap-2">
                            <button type="button" class="<?= $actionBtn ?>"
                                    data-modal-open="modal-post-view"
                                    data-post-title="<?= $t ?>"
                                    data-post-status="<?= $st ?>"
                                    data-post-author="<?= $au ?>"
                                    data-post-tag="<?= $tg ?>"
                                    data-post-category="<?= $cat ?>"
                                    data-post-updated="<?= $up ?>"
                                    data-post-excerpt="<?= $ex ?>"
                                    data-post-image-url="<?= $imgAttr ?>">View</button>
                            <button type="button" class="<?= $actionBtn ?>"
                                    data-modal-open="modal-post-edit"
                                    data-post-title="<?= $t ?>"
                                    data-post-slug="<?= $slug ?>"
                                    data-post-status="<?= $st ?>"
                                    data-post-author="<?= $au ?>"
                                    data-post-tag="<?= $tg ?>"
                                    data-post-category="<?= $cat ?>"
                                    data-post-excerpt="<?= $ex ?>"
                                    data-post-image-url="<?= $imgAttr ?>">Edit</button>
                            <button type="button"
                                    class="rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-800 shadow-sm hover:border-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/70 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                                    data-modal-open="dashboard-confirm-modal"
                                    data-confirm-title="Delete this post?"
                                    data-confirm-body="<?= $delBody ?>"
                                    data-confirm-label="Delete post">Delete</button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="flex flex-col items-center justify-between gap-3 border-t border-stone-100 px-5 py-4 sm:flex-row">
        <p class="text-xs text-stone-500">Pagination will plug in here.</p>
        <div class="flex items-center gap-2">
            <button type="button" class="rounded-lg border border-stone-200 bg-white px-3 py-2 text-xs font-semibold text-stone-700 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white disabled:opacity-40" disabled>
                Previous
            </button>
            <button type="button" class="rounded-lg border border-stone-200 bg-white px-3 py-2 text-xs font-semibold text-stone-700 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                Next
            </button>
        </div>
    </div>
</div>

<?php require_once base_path('views/dashboard/partials/modals/posts.php'); ?>
<?php require_once base_path('views/dashboard/partials/footer.php'); ?>
