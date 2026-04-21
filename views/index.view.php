<?php
/**
 * Home page. Variables come from Http/controllers/index.php via view() → extract().
 *
 * @var string $heading
 * @var string $pageTitle
 * @var string $metaDescription
 * @var array<int, array<string, mixed>> $posts
 */
require_once base_path('views/partials/head.php');
require_once base_path('views/partials/navbar.php');

$hasPosts = is_array($posts) && count($posts) > 0;
?>

<main id="main-content" tabindex="-1"
      class="mx-auto w-full max-w-6xl flex-1 px-4 pb-20 pt-10 sm:px-6 lg:px-8">
    <script>
        document.documentElement.classList.add('js');
    </script>
    <section
            class="relative overflow-hidden rounded-2xl border border-stone-200/90 bg-gradient-to-br from-white via-stone-50 to-amber-50/40 px-6 py-14 shadow-sm ring-1 ring-stone-900/5 sm:px-10 sm:py-16">
        <div class="pointer-events-none absolute -right-24 -top-24 h-64 w-64 rounded-full bg-amber-200/30 blur-3xl"
             aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-32 -left-16 h-72 w-72 rounded-full bg-stone-300/25 blur-3xl"
             aria-hidden="true"></div>
        <div class="relative max-w-2xl">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-500">Journal</p>
            <h1 class="font-editorial mt-4 text-4xl font-semibold leading-[1.1] tracking-tight text-stone-900 sm:text-5xl">
                <?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="mt-5 text-lg leading-relaxed text-stone-600 sm:text-xl">
                <span class="text-stone-800">Ideas, notes, and longer writing, published at your own pace.</span>
            </p>
            <div class="mt-8 flex flex-wrap items-center gap-4">
                <a href="#latest-posts"
                   class="inline-flex items-center justify-center rounded-lg bg-stone-900 px-5 py-2.5 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50">
                    Read latest posts
                </a>
                <a href="<?= htmlspecialchars(blog_url('about'), ENT_QUOTES, 'UTF-8') ?>"
                   class="inline-flex items-center justify-center rounded-lg border border-stone-300 bg-white/80 px-5 py-2.5 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50">
                    About this blog
                </a>
            </div>
        </div>
    </section>

    <section id="latest-posts" class="mt-16 scroll-mt-24" aria-labelledby="latest-heading">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 id="latest-heading"
                    class="font-editorial text-2xl font-semibold tracking-tight text-stone-900 sm:text-3xl">
                    Latest writing
                </h2>
                <p class="mt-2 max-w-xl text-sm leading-relaxed text-stone-600">
                    <?php if ($hasPosts) : ?>
                        The newest pieces from the journal—pull the full archive anytime.
                    <?php else : ?>
                        Nothing published yet. Add a post in the database to see it listed here.
                    <?php endif; ?>
                </p>
            </div>
            <a href="<?= htmlspecialchars(blog_url('blogs'), ENT_QUOTES, 'UTF-8') ?>"
               class="shrink-0 text-xs font-semibold uppercase tracking-wider text-amber-900/90 underline decoration-amber-300/80 underline-offset-4 transition hover:text-amber-950 hover:decoration-amber-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50 rounded-sm">
                All posts →
            </a>
        </div>

        <?php if (!$hasPosts) : ?>
            <div class="mt-10 rounded-xl border border-dashed border-stone-300 bg-stone-50/80 px-6 py-16 text-center sm:px-10">
                <p class="font-editorial text-xl font-semibold text-stone-900">No posts yet</p>
                <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-stone-600">
                    When your first article is in the database, it will show up in this grid automatically.
                </p>
                <p class="mt-8 flex flex-wrap items-center justify-center gap-4">
                    <a href="<?= htmlspecialchars(blog_url('blogs'), ENT_QUOTES, 'UTF-8') ?>"
                       class="inline-flex items-center justify-center rounded-lg bg-stone-900 px-5 py-2.5 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50">
                        Browse archive
                    </a>
                    <a href="<?= htmlspecialchars(blog_url('about'), ENT_QUOTES, 'UTF-8') ?>"
                       class="inline-flex items-center justify-center rounded-lg border border-stone-300 bg-white px-5 py-2.5 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50">
                        About
                    </a>
                </p>
            </div>
        <?php else : ?>
            <style>
                #home-posts-root .home-posts-skeleton {
                    display: none;
                }

                #home-posts-root .home-posts-list {
                    display: grid;
                }

                html.js #home-posts-root:not(.is-ready) .home-posts-skeleton {
                    display: grid;
                }

                html.js #home-posts-root:not(.is-ready) .home-posts-list {
                    display: none;
                }

                html.js #home-posts-root.is-ready .home-posts-skeleton {
                    display: none;
                }

                html.js #home-posts-root.is-ready .home-posts-list {
                    display: grid;
                }
            </style>
            <div id="home-posts-root"
                 class="mt-10"
                 aria-busy="true"
                 aria-live="polite">
                <span class="sr-only" id="home-posts-loading-label">Loading latest posts</span>
                <ul class="home-posts-skeleton grid list-none gap-6 lg:grid-cols-3"
                    role="presentation"
                    aria-hidden="true">
                    <?php for ($i = 0; $i < 3; $i++) : ?>
                        <li class="overflow-hidden rounded-2xl border border-stone-200/90 bg-white shadow-sm ring-1 ring-stone-200/60">
                            <div class="aspect-[16/10] w-full animate-pulse bg-stone-200/70" aria-hidden="true"></div>
                            <div class="p-5 sm:p-6">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="h-3 w-24 animate-pulse rounded bg-stone-200/90"></div>
                                    <div class="flex flex-wrap justify-end gap-1.5">
                                        <div class="h-6 w-14 animate-pulse rounded-full bg-stone-200/80"></div>
                                        <div class="h-6 w-12 animate-pulse rounded-full bg-stone-200/75"></div>
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center gap-2.5">
                                    <div class="h-8 w-8 shrink-0 animate-pulse rounded-full bg-stone-200/80"></div>
                                    <div class="h-3 w-32 animate-pulse rounded bg-stone-200/80"></div>
                                </div>
                                <div class="mt-5 h-5 w-full animate-pulse rounded bg-stone-200/90"></div>
                                <div class="mt-3 h-5 w-4/5 max-w-[18rem] animate-pulse rounded bg-stone-200/70"></div>
                                <div class="mt-3 space-y-2">
                                    <div class="h-3 w-full animate-pulse rounded bg-stone-200/60"></div>
                                    <div class="h-3 w-full animate-pulse rounded bg-stone-200/60"></div>
                                    <div class="h-3 w-2/3 animate-pulse rounded bg-stone-200/60"></div>
                                </div>
                                <div class="mt-6 h-3 w-28 animate-pulse rounded bg-stone-200/70"></div>
                            </div>
                        </li>
                    <?php endfor; ?>
                </ul>

                <ul class="home-posts-list grid list-none gap-6 lg:grid-cols-3"
                    aria-labelledby="latest-heading">
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
                                             sizes="(min-width: 1024px) 33vw, 100vw"/>
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
                                    <h3 class="font-editorial mt-4 text-lg font-semibold leading-snug text-stone-900">
                                        <a href="<?= htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8') ?>"
                                           class="transition hover:text-amber-950 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white rounded-sm">
                                            <?= htmlspecialchars($cardTitle, ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </h3>
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
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var root = document.getElementById('home-posts-root');
                    if (!root) {
                        return;
                    }
                    root.classList.add('is-ready');
                    root.setAttribute('aria-busy', 'false');
                });
            </script>
        <?php endif; ?>
    </section>
</main>

<?php require_once base_path('views/partials/footer.php'); ?>
