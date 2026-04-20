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
                        <li class="rounded-xl border border-stone-200/90 bg-white p-6 shadow-sm">
                            <div class="flex items-center justify-between gap-3">
                                <div class="h-3 w-24 animate-pulse rounded bg-stone-200/90"></div>
                                <div class="h-6 w-16 animate-pulse rounded-full bg-stone-200/80"></div>
                            </div>
                            <div class="mt-4 h-3 w-32 animate-pulse rounded bg-stone-200/80"></div>
                            <div class="mt-5 h-5 w-full animate-pulse rounded bg-stone-200/90"></div>
                            <div class="mt-3 h-5 w-4/5 max-w-[18rem] animate-pulse rounded bg-stone-200/70"></div>
                            <div class="mt-3 space-y-2">
                                <div class="h-3 w-full animate-pulse rounded bg-stone-200/60"></div>
                                <div class="h-3 w-full animate-pulse rounded bg-stone-200/60"></div>
                                <div class="h-3 w-2/3 animate-pulse rounded bg-stone-200/60"></div>
                            </div>
                            <div class="mt-6 h-3 w-28 animate-pulse rounded bg-stone-200/70"></div>
                        </li>
                    <?php endfor; ?>
                </ul>

                <ul class="home-posts-list grid list-none gap-6 lg:grid-cols-3"
                    aria-labelledby="latest-heading">
                    <?php foreach ($posts as $post) : ?>
                        <?php
                        $detailUrl = blog_post_url($post['slug']);
                        ?>
                        <li>
                            <article
                                    class="relative flex h-full flex-col rounded-xl border border-stone-200/90 bg-white p-6 shadow-sm transition hover:border-stone-300 hover:shadow-md">
                                <div class="flex items-center justify-between gap-3">
                                    <time class="text-xs font-medium tabular-nums text-stone-500"
                                          datetime="<?= htmlspecialchars($post['dateIso'], ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($post['dateDisplay'], ENT_QUOTES, 'UTF-8') ?>
                                    </time>
                                    <?php if (($post['tag'] ?? '') !== '') : ?>
                                        <span class="rounded-full bg-amber-100/90 px-2.5 py-0.5 text-xs font-semibold text-amber-900/90 ring-1 ring-amber-200/80">
                                            <?= htmlspecialchars($post['tag'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <p class="mt-2 text-xs font-medium text-stone-600">
                                    By <?= htmlspecialchars($post['author'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </p>
                                <h3 class="font-editorial mt-3 text-lg font-semibold leading-snug text-stone-900">
                                    <a href="<?= htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8') ?>"
                                       class="after:absolute after:inset-0 after:rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                                        <?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </h3>
                                <p class="mt-3 flex-1 text-sm leading-relaxed text-stone-600">
                                    <?= htmlspecialchars($post['excerpt'], ENT_QUOTES, 'UTF-8') ?>
                                </p>
                                <p class="mt-5">
                                    <a href="<?= htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8') ?>"
                                       class="relative z-10 text-xs font-semibold uppercase tracking-wider text-amber-800 transition hover:text-amber-950 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white rounded-sm">
                                        Read article →
                                    </a>
                                </p>
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
