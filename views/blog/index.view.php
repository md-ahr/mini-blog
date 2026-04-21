<?php
/**
 * Blog archive: filters, search, pagination.
 *
 * @var array<int, array<string, mixed>> $posts
 * @var array<int, array{name: string, slug: string}> $allTags
 * @var array<int, array{name: string, slug: string}> $sidebarCategories
 * @var string $filterTag tag slug
 * @var string $filterCategory category slug
 * @var string $searchQuery
 * @var int $page
 * @var int $totalPages
 * @var int $totalCount
 * @var int $perPage
 * @var string $pageTitle
 * @var string $metaDescription
 */
require_once base_path('views/partials/head.php');
require_once base_path('views/partials/navbar.php');

$blogsUrl = blogs_archive_url();
$allTags = $allTags ?? [];
$sidebarCategories = $sidebarCategories ?? [];
$filterCategory = $filterCategory ?? '';
$startItem = $totalCount === 0 ? 0 : (($page - 1) * $perPage) + 1;
$endItem = min($totalCount, $page * $perPage);

$chipBase = 'inline-flex shrink-0 items-center rounded-full border px-3.5 py-1.5 text-xs font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50';
$chipIdle = 'border-stone-200 bg-white text-stone-700 hover:border-stone-300 hover:bg-stone-50';
$chipActive = 'border-amber-300/90 bg-amber-100/90 text-amber-950 ring-1 ring-amber-200/80';
?>

<main id="main-content" tabindex="-1"
      class="mx-auto w-full max-w-6xl flex-1 px-4 pb-24 pt-8 sm:px-6 lg:px-8">
    <nav class="mb-8 text-sm font-medium text-stone-600" aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-2">
            <li>
                <a href="<?= htmlspecialchars(blog_url(), ENT_QUOTES, 'UTF-8') ?>"
                   class="transition hover:text-stone-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50 rounded-sm">
                    Home
                </a>
            </li>
            <li class="text-stone-400" aria-hidden="true">/</li>
            <li class="text-stone-900" aria-current="page">Blog</li>
        </ol>
    </nav>

    <div class="mt-10 flex flex-col gap-8 lg:flex-row lg:items-start lg:gap-10">
        <div class="lg:sticky lg:top-24 lg:w-72 lg:shrink-0">
            <section class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-sm sm:p-6"
                     aria-labelledby="filter-heading">
                <h2 id="filter-heading" class="text-sm font-semibold text-stone-900">
                    Find posts
                </h2>
                <p class="mt-1 text-xs leading-relaxed text-stone-500">
                    Search updates the list below. Filters combine with search.
                </p>

                <form class="mt-5"
                      method="get"
                      action="<?= htmlspecialchars($blogsUrl, ENT_QUOTES, 'UTF-8') ?>"
                      role="search">
                    <?php if ($filterTag !== '') : ?>
                        <input type="hidden" name="tag"
                               value="<?= htmlspecialchars($filterTag, ENT_QUOTES, 'UTF-8') ?>">
                    <?php endif; ?>
                    <?php if ($filterCategory !== '') : ?>
                        <input type="hidden" name="category"
                               value="<?= htmlspecialchars($filterCategory, ENT_QUOTES, 'UTF-8') ?>">
                    <?php endif; ?>
                    <label class="sr-only" for="blog-search">Search posts</label>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-stretch">
                        <input id="blog-search"
                               name="q"
                               type="search"
                               autocomplete="off"
                               placeholder="Search title or excerpt…"
                               value="<?= htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8') ?>"
                               class="min-w-0 flex-1 rounded-lg border border-stone-300 bg-stone-50/80 px-3 py-2.5 text-sm text-stone-900 shadow-inner placeholder:text-stone-400 focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/25">
                        <button type="submit"
                                class="rounded-lg bg-stone-900 px-4 py-2.5 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white sm:shrink-0">
                            Search
                        </button>
                    </div>
                </form>

                <div class="mt-6 border-t border-stone-200/80 pt-6">
                    <p class="text-xs font-semibold uppercase tracking-wider text-stone-400">Topics</p>
                    <div class="-mx-1 mt-3 flex gap-2 overflow-x-auto pb-1 lg:flex-wrap lg:overflow-visible">
                        <a href="<?= htmlspecialchars(blogs_index_url([
                                'category' => $filterCategory,
                                'q' => $searchQuery,
                                'page' => 1,
                        ]), ENT_QUOTES, 'UTF-8') ?>"
                           class="<?= $chipBase ?> <?= $filterTag === '' ? $chipActive : $chipIdle ?>"
                                <?= $filterTag === '' ? 'aria-current="true"' : '' ?>>
                            All topics
                        </a>
                        <?php foreach ($allTags as $tag) : ?>
                            <?php
                            $tSlug = (string)($tag['slug'] ?? '');
                            $tName = (string)($tag['name'] ?? $tSlug);
                            if ($tSlug === '') {
                                continue;
                            }
                            ?>
                            <a href="<?= htmlspecialchars(blogs_index_url([
                                    'tag' => $tSlug,
                                    'category' => $filterCategory,
                                    'q' => $searchQuery,
                                    'page' => 1,
                            ]), ENT_QUOTES, 'UTF-8') ?>"
                               class="<?= $chipBase ?> <?= $filterTag === $tSlug ? $chipActive : $chipIdle ?>"
                                    <?= $filterTag === $tSlug ? 'aria-current="true"' : '' ?>>
                                <?= htmlspecialchars($tName, ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if ($sidebarCategories !== []) : ?>
                    <div class="mt-6 border-t border-stone-200/80 pt-6">
                        <p class="text-xs font-semibold uppercase tracking-wider text-stone-400">Categories</p>
                        <div class="-mx-1 mt-3 flex gap-2 overflow-x-auto pb-1 lg:flex-wrap lg:overflow-visible">
                            <a href="<?= htmlspecialchars(blogs_index_url([
                                    'tag' => $filterTag,
                                    'q' => $searchQuery,
                                    'page' => 1,
                            ]), ENT_QUOTES, 'UTF-8') ?>"
                               class="<?= $chipBase ?> <?= $filterCategory === '' ? $chipActive : $chipIdle ?>"
                                    <?= $filterCategory === '' ? 'aria-current="true"' : '' ?>>
                                All categories
                            </a>
                            <?php foreach ($sidebarCategories as $cat) : ?>
                                <?php
                                $cSlug = (string)($cat['slug'] ?? '');
                                $cName = (string)($cat['name'] ?? $cSlug);
                                if ($cSlug === '') {
                                    continue;
                                }
                                ?>
                                <a href="<?= htmlspecialchars(blogs_index_url([
                                        'tag' => $filterTag,
                                        'category' => $cSlug,
                                        'q' => $searchQuery,
                                        'page' => 1,
                                ]), ENT_QUOTES, 'UTF-8') ?>"
                                   class="<?= $chipBase ?> <?= $filterCategory === $cSlug ? $chipActive : $chipIdle ?>"
                                        <?= $filterCategory === $cSlug ? 'aria-current="true"' : '' ?>>
                                    <?= htmlspecialchars($cName, ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($filterTag !== '' || $filterCategory !== '' || $searchQuery !== '') : ?>
                    <p class="mt-5">
                        <a href="<?= htmlspecialchars($blogsUrl, ENT_QUOTES, 'UTF-8') ?>"
                           class="text-xs font-semibold text-amber-900 underline decoration-amber-300 underline-offset-4 transition hover:decoration-amber-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white rounded-sm">
                            Clear filters
                        </a>
                    </p>
                <?php endif; ?>
            </section>
        </div>

        <div class="min-w-0 flex-1">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-baseline sm:justify-between">
                <p class="text-sm text-stone-600" role="status">
                    <?php if ($totalCount === 0) : ?>
                        No posts match your filters.
                    <?php else : ?>
                        Showing
                        <span class="font-semibold tabular-nums text-stone-900"><?= (int)$startItem ?>–<?= (int)$endItem ?></span>
                        of
                        <span class="font-semibold tabular-nums text-stone-900"><?= (int)$totalCount ?></span>
                        <?= $totalCount === 1 ? 'post' : 'posts' ?>
                    <?php endif; ?>
                </p>
                <?php if ($totalPages > 1) : ?>
                    <p class="text-xs font-medium uppercase tracking-wider text-stone-400">
                        Page <?= (int)$page ?> of <?= (int)$totalPages ?>
                    </p>
                <?php endif; ?>
            </div>

            <?php if ($totalCount > 0) : ?>
                <ul class="mt-8 grid gap-6 sm:grid-cols-2">
                    <?php foreach ($posts as $post) : ?>
                        <?php
                        $detailUrl = blog_post_url($post['slug']);
                        $cardTitle = (string)($post['title'] ?? '');
                        $feat = isset($post['featuredImageUrl']) && $post['featuredImageUrl'] !== null && $post['featuredImageUrl'] !== ''
                                ? trim((string)$post['featuredImageUrl'])
                                : '';
                        $featAlt = $cardTitle !== '' ? $cardTitle : 'Article cover image';
                        $authorName = (string)($post['author'] ?? '');
                        $avUrl = isset($post['authorAvatarUrl']) && $post['authorAvatarUrl'] !== null ? trim((string)$post['authorAvatarUrl']) : '';
                        $avAlt = (string)($post['authorAvatarAlt'] ?? $authorName);
                        $readMin = (int)($post['readingMinutes'] ?? 0);
                        ?>
                        <li>
                            <article
                                    class="group relative flex h-full flex-col overflow-hidden rounded-2xl border border-stone-200/90 bg-white shadow-sm ring-1 ring-stone-200/60 transition hover:border-stone-300 hover:shadow-md">
                                <a href="<?= htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8') ?>"
                                   class="relative block aspect-[16/10] w-full shrink-0 overflow-hidden bg-gradient-to-br from-stone-200/90 to-stone-300/80 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                                    <?php if ($feat !== '') : ?>
                                        <img src="<?= htmlspecialchars($feat, ENT_QUOTES, 'UTF-8') ?>"
                                             alt="<?= htmlspecialchars($featAlt, ENT_QUOTES, 'UTF-8') ?>"
                                             class="h-full w-full object-cover transition duration-500 ease-out group-hover:scale-[1.03]"
                                             loading="lazy"
                                             decoding="async"
                                             sizes="(min-width: 1024px) 50vw, 100vw"/>
                                    <?php else : ?>
                                        <div class="flex h-full w-full flex-col items-center justify-center gap-2 text-stone-500"
                                             aria-hidden="true">
                                            <svg class="h-10 w-10 opacity-60" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                                <circle cx="8.5" cy="10" r="1.5"/>
                                                <path d="M21 17l-5-5-4 4-3-3-4 4"/>
                                            </svg>
                                            <span class="text-xs font-medium">No cover image</span>
                                        </div>
                                    <?php endif; ?>
                                </a>
                                <div class="flex flex-1 flex-col p-5 sm:p-6">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <time class="text-xs font-medium tabular-nums text-stone-500"
                                              datetime="<?= htmlspecialchars((string)($post['dateIso'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars((string)($post['dateDisplay'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                        </time>
                                        <div class="flex max-w-[min(100%,18rem)] flex-wrap items-center justify-end gap-1.5">
                                            <?php
                                            $catSlug = trim((string)($post['category_slug'] ?? ''));
                                            $catName = trim((string)($post['category'] ?? ''));
                                            if ($catSlug !== '' && $catName !== '') :
                                                ?>
                                                <a href="<?= htmlspecialchars(blogs_index_url(['category' => $catSlug, 'page' => 1]), ENT_QUOTES, 'UTF-8') ?>"
                                                   class="relative z-10 rounded-full bg-stone-100 px-2.5 py-0.5 text-xs font-semibold text-stone-800 ring-1 ring-stone-200/80 transition hover:bg-stone-200/90">
                                                    <?= htmlspecialchars($catName, ENT_QUOTES, 'UTF-8') ?>
                                                </a>
                                            <?php endif; ?>
                                            <?php
                                            $ptags = isset($post['tags']) && is_array($post['tags']) ? $post['tags'] : [];
                                            foreach ($ptags as $tg) :
                                                if (!is_array($tg)) {
                                                    continue;
                                                }
                                                $ts = trim((string)($tg['slug'] ?? ''));
                                                $tn = trim((string)($tg['name'] ?? ''));
                                                if ($ts === '') {
                                                    continue;
                                                }
                                                $tc = blog_sanitize_color($tg['color'] ?? null, '#78716c');
                                                ?>
                                                <a href="<?= htmlspecialchars(blogs_index_url(['tag' => $ts, 'page' => 1]), ENT_QUOTES, 'UTF-8') ?>"
                                                   class="relative z-10 inline-flex max-w-full items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold text-stone-900 ring-1 ring-stone-200/80 transition hover:opacity-90"
                                                   style="background-color: <?= htmlspecialchars($tc, ENT_QUOTES, 'UTF-8') ?>22;">
                                                    <span class="h-1.5 w-1.5 shrink-0 rounded-full ring-1 ring-stone-900/10"
                                                          style="background-color: <?= htmlspecialchars($tc, ENT_QUOTES, 'UTF-8') ?>"
                                                          aria-hidden="true"></span>
                                                    <span class="min-w-0 truncate"><?= htmlspecialchars($tn !== '' ? $tn : $ts, ENT_QUOTES, 'UTF-8') ?></span>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="mt-3 flex items-center gap-2.5 text-xs font-medium text-stone-600">
                                        <?php if ($avUrl !== '') : ?>
                                            <img src="<?= htmlspecialchars($avUrl, ENT_QUOTES, 'UTF-8') ?>"
                                                 alt="<?= htmlspecialchars($avAlt, ENT_QUOTES, 'UTF-8') ?>"
                                                 class="h-8 w-8 shrink-0 rounded-full object-cover ring-2 ring-white shadow-sm"
                                                 width="32"
                                                 height="32"
                                                 loading="lazy"/>
                                        <?php else : ?>
                                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-stone-200 text-[11px] font-bold text-stone-700 ring-2 ring-white shadow-sm"
                                                  aria-hidden="true"><?= htmlspecialchars(blog_author_initials($authorName), ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php endif; ?>
                                        <span class="min-w-0">
                                <span class="text-stone-500">By</span>
                                <?= htmlspecialchars($authorName, ENT_QUOTES, 'UTF-8') ?>
                                            <?php if ($readMin > 0) : ?>
                                                <span class="text-stone-400" aria-hidden="true"> · </span>
                                                <span class="tabular-nums text-stone-500"><?= $readMin ?> min read</span>
                                            <?php endif; ?>
                              </span>
                                    </div>
                                    <h2 class="font-editorial mt-4 text-lg font-semibold leading-snug text-stone-900">
                                        <a href="<?= htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8') ?>"
                                           class="transition hover:text-amber-950 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white rounded-sm">
                                            <?= htmlspecialchars($cardTitle, ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </h2>
                                    <p class="mt-3 flex-1 text-sm leading-relaxed text-stone-600 line-clamp-3">
                                        <?= htmlspecialchars((string)($post['excerpt'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                    <p class="mt-5">
                                        <a href="<?= htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8') ?>"
                                           class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wider text-amber-800 transition hover:text-amber-950 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white rounded-sm">
                                            Read article <span aria-hidden="true">→</span>
                                        </a>
                                    </p>
                                </div>
                            </article>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <div class="mt-10 rounded-xl border border-dashed border-stone-300 bg-stone-50/80 px-6 py-14 text-center">
                    <p class="font-editorial text-lg font-semibold text-stone-900">Nothing here yet</p>
                    <p class="mt-2 text-sm text-stone-600">
                        Try another topic, shorten your search, or
                        <a href="<?= htmlspecialchars($blogsUrl, ENT_QUOTES, 'UTF-8') ?>"
                           class="font-semibold text-amber-900 underline decoration-amber-300 underline-offset-4 hover:decoration-amber-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50 rounded-sm">view
                            all posts</a>.
                    </p>
                </div>
            <?php endif; ?>

            <?php if ($totalPages > 1) : ?>
                <nav class="mt-12 flex flex-col items-stretch gap-4 border-t border-stone-200/90 pt-8 sm:flex-row sm:items-center sm:justify-between"
                     aria-label="Pagination">
                    <div class="flex justify-center gap-2 sm:justify-start">
                        <?php if ($page > 1) : ?>
                            <a href="<?= htmlspecialchars(blogs_index_url([
                                    'tag' => $filterTag,
                                    'category' => $filterCategory,
                                    'q' => $searchQuery,
                                    'page' => $page - 1,
                            ]), ENT_QUOTES, 'UTF-8') ?>"
                               rel="prev"
                               class="inline-flex items-center justify-center rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 hover:bg-stone-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50">
                                ← Previous
                            </a>
                        <?php else : ?>
                            <span class="inline-flex cursor-not-allowed items-center justify-center rounded-lg border border-stone-200 bg-stone-100/80 px-4 py-2 text-sm font-semibold text-stone-400">
                                ← Previous
                            </span>
                        <?php endif; ?>
                        <?php if ($page < $totalPages) : ?>
                            <a href="<?= htmlspecialchars(blogs_index_url([
                                    'tag' => $filterTag,
                                    'category' => $filterCategory,
                                    'q' => $searchQuery,
                                    'page' => $page + 1,
                            ]), ENT_QUOTES, 'UTF-8') ?>"
                               rel="next"
                               class="inline-flex items-center justify-center rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 hover:bg-stone-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50">
                                Next →
                            </a>
                        <?php else : ?>
                            <span class="inline-flex cursor-not-allowed items-center justify-center rounded-lg border border-stone-200 bg-stone-100/80 px-4 py-2 text-sm font-semibold text-stone-400">
                                Next →
                            </span>
                        <?php endif; ?>
                    </div>
                    <ul class="flex flex-wrap items-center justify-center gap-1 sm:justify-end">
                        <?php
                        $window = 2;
                        $show = [];
                        for ($i = 1; $i <= $totalPages; $i++) {
                            if ($i === 1 || $i === $totalPages || abs($i - $page) <= $window) {
                                $show[] = $i;
                            }
                        }
                        $lastPrinted = 0;
                        foreach ($show as $i) {
                            if ($lastPrinted && $i - $lastPrinted > 1) {
                                echo '<li class="px-1 text-stone-400" aria-hidden="true">…</li>';
                            }
                            $lastPrinted = $i;
                            $isCurrent = $i === $page;
                            ?>
                            <li>
                                <?php if ($isCurrent) : ?>
                                    <span class="flex h-9 min-w-9 items-center justify-center rounded-lg bg-stone-900 text-sm font-semibold tabular-nums text-amber-50"
                                          aria-current="page"><?= $i ?></span>
                                <?php else : ?>
                                    <a href="<?= htmlspecialchars(blogs_index_url([
                                            'tag' => $filterTag,
                                            'category' => $filterCategory,
                                            'q' => $searchQuery,
                                            'page' => $i,
                                    ]), ENT_QUOTES, 'UTF-8') ?>"
                                       class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-transparent text-sm font-semibold tabular-nums text-stone-700 transition hover:border-stone-300 hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50">
                                        <?= $i ?>
                                    </a>
                                <?php endif; ?>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once base_path('views/partials/footer.php'); ?>
