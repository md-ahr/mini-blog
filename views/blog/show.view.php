<?php
/**
 * Single post (data from database via controller).
 *
 * @var array<string, mixed>|null $post
 * @var bool $postError Set when the database request fails
 * @var string $pageTitle
 * @var string $metaDescription
 * @var array<int, array<string, mixed>> $comments
 * @var string $commentCsrfToken
 * @var array<string, string> $commentFormErrors
 * @var array<string, mixed> $commentFormValues
 * @var string $commentFlash
 */
require_once base_path('views/partials/head.php');
require_once base_path('views/partials/navbar.php');

$postError = $postError ?? false;
$post = $post ?? null;

if ($postError || $post === null) {
    ?>
<main id="main-content" tabindex="-1"
      class="mx-auto w-full max-w-6xl flex-1 px-4 pb-24 pt-8 sm:px-6 lg:px-8">
    <nav class="mb-10 text-sm font-medium text-stone-600" aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-2">
            <li>
                <a href="<?= htmlspecialchars(blog_url(), ENT_QUOTES, 'UTF-8') ?>"
                   class="transition hover:text-stone-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50 rounded-sm">
                    Home
                </a>
            </li>
            <li class="text-stone-400" aria-hidden="true">/</li>
            <li>
                <a href="<?= htmlspecialchars(blog_url('blogs'), ENT_QUOTES, 'UTF-8') ?>"
                   class="transition hover:text-stone-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50 rounded-sm">
                    Blog
                </a>
            </li>
            <li class="text-stone-400" aria-hidden="true">/</li>
            <li class="max-w-[min(100%,28rem)] truncate text-stone-900" aria-current="page">Article</li>
        </ol>
    </nav>

    <div class="mx-auto max-w-2xl rounded-2xl border border-amber-200/80 bg-amber-50/50 p-8 text-center shadow-sm ring-1 ring-amber-100/80"
         role="alert">
        <p class="text-sm font-semibold uppercase tracking-wide text-amber-900/90">Something went wrong</p>
        <h1 class="font-editorial mt-3 text-2xl font-semibold tracking-tight text-stone-900 sm:text-3xl">
            We could not load this article
        </h1>
        <p class="mt-4 text-base leading-relaxed text-stone-600">
            Please check your connection and try again. If the problem continues, come back later or start from the blog index.
        </p>
        <p class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="<?= htmlspecialchars(blog_url('blogs'), ENT_QUOTES, 'UTF-8') ?>"
               class="inline-flex items-center justify-center rounded-lg bg-stone-900 px-5 py-2.5 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                View all posts
            </a>
            <a href="<?= htmlspecialchars(blog_url(), ENT_QUOTES, 'UTF-8') ?>"
               class="inline-flex items-center justify-center rounded-lg border border-stone-300 bg-white px-5 py-2.5 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                Home
            </a>
        </p>
    </div>
</main>
    <?php
    require_once base_path('views/partials/footer.php');
    return;
}

$path = blog_post_url($post['slug']);
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$absolutePostUrl = $scheme . '://' . $host . $path;

$postTags = isset($post['tags']) && is_array($post['tags']) ? $post['tags'] : [];
$categoryName = trim((string) ($post['category'] ?? ''));
$categorySlug = trim((string) ($post['category_slug'] ?? ''));
$contentBlocks = isset($post['content']) && is_array($post['content']) ? $post['content'] : [];
$hasBody = $contentBlocks !== [];
$readingMinutes = (int) ($post['readingMinutes'] ?? 0);
$featuredUrl = isset($post['featuredImageUrl']) ? trim((string) $post['featuredImageUrl']) : '';
$postTitle = trim((string) ($post['title'] ?? ''));
$featuredAlt = $postTitle !== '' ? $postTitle : 'Article cover image';
$authorName = (string) ($post['author'] ?? '');
$authorAvUrl = isset($post['authorAvatarUrl']) && $post['authorAvatarUrl'] !== null ? trim((string) $post['authorAvatarUrl']) : '';
$authorAvAlt = (string) ($post['authorAvatarAlt'] ?? $authorName);
$authorBio = isset($post['authorBio']) && $post['authorBio'] !== null ? trim((string) $post['authorBio']) : '';
$showUpdatedDate = !empty($post['showUpdatedDate']);
$updatedAtIso = (string) ($post['updatedAtIso'] ?? '');
$updatedAtDisplay = (string) ($post['updatedAtDisplay'] ?? '');
?>

<main id="main-content" tabindex="-1" class="mx-auto w-full max-w-6xl flex-1 px-4 pb-24 pt-8 sm:px-6 lg:px-8">
    <nav class="mb-10 text-sm font-medium text-stone-600" aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-2">
            <li>
                <a href="<?= htmlspecialchars(blog_url(), ENT_QUOTES, 'UTF-8') ?>"
                   class="transition hover:text-stone-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50 rounded-sm">
                    Home
                </a>
            </li>
            <li class="text-stone-400" aria-hidden="true">/</li>
            <li>
                <a href="<?= htmlspecialchars(blog_url('blogs'), ENT_QUOTES, 'UTF-8') ?>"
                   class="transition hover:text-stone-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50 rounded-sm">
                    Blog
                </a>
            </li>
            <li class="text-stone-400" aria-hidden="true">/</li>
            <li class="min-w-0 max-w-[min(100%,36rem)] truncate text-stone-900" aria-current="page">
                <?= htmlspecialchars($postTitle !== '' ? $postTitle : 'Article', ENT_QUOTES, 'UTF-8') ?>
            </li>
        </ol>
    </nav>

    <article class="mx-auto max-w-2xl">
        <?php if ($featuredUrl !== '') : ?>
            <figure class="mb-10 overflow-hidden rounded-2xl border border-stone-200/90 bg-stone-100 shadow-sm ring-1 ring-stone-200/80">
                <img src="<?= htmlspecialchars($featuredUrl, ENT_QUOTES, 'UTF-8') ?>"
                     alt="<?= htmlspecialchars($featuredAlt, ENT_QUOTES, 'UTF-8') ?>"
                     class="max-h-[min(70vh,36rem)] w-full object-cover"
                     loading="eager"
                     decoding="async"
                     sizes="(min-width: 768px) 42rem, 100vw"/>
            </figure>
        <?php endif; ?>

        <header class="border-b border-stone-200/90 pb-10">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-2 text-xs font-medium text-stone-500">
                <?php if ($categoryName !== '' && $categorySlug !== '') : ?>
                    <a href="<?= htmlspecialchars(blogs_index_url(['category' => $categorySlug, 'page' => 1]), ENT_QUOTES, 'UTF-8') ?>"
                       class="rounded-full bg-stone-100 px-2.5 py-0.5 text-xs font-semibold text-stone-800 ring-1 ring-stone-200/80 transition hover:bg-stone-200/90">
                        <?= htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endif; ?>
                <?php foreach ($postTags as $tg) : ?>
                    <?php
                    if (!is_array($tg)) {
                      continue;
                    }
                    $ts = trim((string) ($tg['slug'] ?? ''));
                    $tn = trim((string) ($tg['name'] ?? ''));
                    if ($ts === '') {
                      continue;
                    }
                    $tc = blog_sanitize_color($tg['color'] ?? null, '#78716c');
                    ?>
                    <a href="<?= htmlspecialchars(blogs_index_url(['tag' => $ts, 'page' => 1]), ENT_QUOTES, 'UTF-8') ?>"
                       class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold text-stone-900 ring-1 ring-stone-200/80 transition hover:opacity-90"
                       style="background-color: <?= htmlspecialchars($tc, ENT_QUOTES, 'UTF-8') ?>22;">
                        <span class="h-1.5 w-1.5 shrink-0 rounded-full ring-1 ring-stone-900/10" style="background-color: <?= htmlspecialchars($tc, ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true"></span>
                        <?= htmlspecialchars($tn !== '' ? $tn : $ts, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endforeach; ?>
                <?php if ($categoryName === '' && $postTags === []) : ?>
                    <span class="rounded-full bg-stone-100 px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wide text-stone-600 ring-1 ring-stone-200/80">
                        Article
                    </span>
                <?php endif; ?>
            </div>

            <div class="mt-5 flex flex-wrap items-center gap-x-4 gap-y-3">
                <?php if ($authorAvUrl !== '') : ?>
                    <img src="<?= htmlspecialchars($authorAvUrl, ENT_QUOTES, 'UTF-8') ?>"
                         alt="<?= htmlspecialchars($authorAvAlt, ENT_QUOTES, 'UTF-8') ?>"
                         class="h-12 w-12 shrink-0 rounded-full object-cover ring-2 ring-stone-100 shadow-sm"
                         width="48"
                         height="48"
                         loading="eager"/>
                <?php else : ?>
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-stone-200 text-sm font-bold text-stone-700 ring-2 ring-stone-100 shadow-sm"
                          aria-hidden="true"><?= htmlspecialchars(blog_author_initials($authorName), ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
                <div class="min-w-0 flex-1 text-sm text-stone-600">
                    <p class="font-semibold text-stone-900">
                        <?= htmlspecialchars($authorName, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <p class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-medium text-stone-500">
                        <span>Published</span>
                        <time datetime="<?= htmlspecialchars((string) ($post['dateIso'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="tabular-nums text-stone-700">
                            <?= htmlspecialchars((string) ($post['dateDisplay'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </time>
                        <?php if ($showUpdatedDate && $updatedAtIso !== '' && $updatedAtDisplay !== '') : ?>
                            <span class="text-stone-300" aria-hidden="true">·</span>
                            <span>Updated</span>
                            <time datetime="<?= htmlspecialchars($updatedAtIso, ENT_QUOTES, 'UTF-8') ?>" class="tabular-nums text-stone-700">
                                <?= htmlspecialchars($updatedAtDisplay, ENT_QUOTES, 'UTF-8') ?>
                            </time>
                        <?php endif; ?>
                        <?php if ($readingMinutes > 0) : ?>
                            <span class="text-stone-300" aria-hidden="true">·</span>
                            <span class="tabular-nums"><?= $readingMinutes ?> min read</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <h1 class="font-editorial mt-8 text-[2rem] font-semibold leading-[1.15] tracking-tight text-stone-900 sm:text-4xl sm:leading-[1.1]">
                <?= htmlspecialchars($postTitle, ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="mt-5 text-lg leading-relaxed text-stone-600 sm:text-xl">
                <?= htmlspecialchars((string) ($post['excerpt'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
            </p>
        </header>

        <div class="article-body mt-12 space-y-6 text-[1.0625rem] leading-[1.75] text-stone-700 sm:text-lg sm:leading-[1.7]">
            <?php if (!$hasBody) : ?>
                <div class="rounded-2xl border border-dashed border-stone-300 bg-stone-50/80 px-6 py-10 text-center">
                    <p class="text-sm font-semibold text-stone-800">No article body yet</p>
                    <p class="mt-2 text-sm leading-relaxed text-stone-600">
                        The full text for this post has not been published. Check back later or browse other articles.
                    </p>
                    <p class="mt-6">
                        <a href="<?= htmlspecialchars(blog_url('blogs'), ENT_QUOTES, 'UTF-8') ?>"
                           class="text-sm font-semibold text-amber-900 underline decoration-amber-300 underline-offset-4 hover:decoration-amber-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50 rounded-sm">
                            Browse the blog
                        </a>
                    </p>
                </div>
            <?php else : ?>
                <?php foreach ($contentBlocks as $block) : ?>
                    <?php if (!is_array($block) || !isset($block['type'])) : ?>
                        <?php continue; ?>
                    <?php endif; ?>
                    <?php if ($block['type'] === 'p') : ?>
                        <p><?= nl2br(htmlspecialchars((string) ($block['text'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></p>
                    <?php elseif ($block['type'] === 'h2') : ?>
                        <h2 class="font-editorial pt-4 text-2xl font-semibold tracking-tight text-stone-900 sm:text-[1.75rem]">
                            <?= htmlspecialchars((string) ($block['text'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </h2>
                    <?php elseif ($block['type'] === 'blockquote') : ?>
                        <blockquote class="border-l-4 border-amber-400/90 bg-amber-50/50 py-1 pl-6 pr-4 font-editorial text-xl italic leading-snug text-stone-800">
                            <?= htmlspecialchars((string) ($block['text'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </blockquote>
                    <?php elseif ($block['type'] === 'ul' && isset($block['items']) && is_array($block['items'])) : ?>
                        <ul class="list-disc space-y-2 pl-6 marker:text-amber-600">
                            <?php foreach ($block['items'] as $item) : ?>
                                <li><?= htmlspecialchars((string) $item, ENT_QUOTES, 'UTF-8') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if ($authorBio !== '') : ?>
            <section class="mt-14 rounded-2xl border border-stone-200/90 bg-stone-50/80 p-6 shadow-sm ring-1 ring-stone-200/60 sm:p-8"
                     aria-labelledby="post-author-heading">
                <p id="post-author-heading" class="text-xs font-semibold uppercase tracking-wider text-stone-500">
                    About the author
                </p>
                <div class="mt-4 flex gap-4">
                    <?php if ($authorAvUrl !== '') : ?>
                        <img src="<?= htmlspecialchars($authorAvUrl, ENT_QUOTES, 'UTF-8') ?>"
                             alt=""
                             class="h-14 w-14 shrink-0 rounded-full object-cover ring-2 ring-white shadow-sm"
                             width="56"
                             height="56"
                             loading="lazy"
                             decoding="async"/>
                    <?php else : ?>
                        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-stone-200 text-base font-bold text-stone-700 ring-2 ring-white shadow-sm"
                              aria-hidden="true"><?= htmlspecialchars(blog_author_initials($authorName), ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                    <div class="min-w-0">
                        <p class="font-semibold text-stone-900"><?= htmlspecialchars($authorName, ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="mt-2 text-sm leading-relaxed text-stone-600">
                            <?= nl2br(htmlspecialchars($authorBio, ENT_QUOTES, 'UTF-8')) ?>
                        </p>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php
        $comments = isset($comments) && is_array($comments) ? $comments : [];
        $commentFormErrors = isset($commentFormErrors) && is_array($commentFormErrors) ? $commentFormErrors : [];
        $commentFormValues = isset($commentFormValues) && is_array($commentFormValues) ? $commentFormValues : [];
        $commentFlash = isset($commentFlash) ? trim((string) $commentFlash) : '';
        $commentCsrfToken = isset($commentCsrfToken) ? (string) $commentCsrfToken : auth_csrf_token();
        $cfName = trim((string) ($commentFormValues['author_name'] ?? ''));
        $cfEmail = trim((string) ($commentFormValues['author_email'] ?? ''));
        $cfBody = (string) ($commentFormValues['body'] ?? '');
        $cfParent = isset($commentFormValues['parent_id']) && $commentFormValues['parent_id'] !== null && $commentFormValues['parent_id'] !== ''
          ? (int) $commentFormValues['parent_id'] : 0;
        $commentCount = 0;
        $countWalk = static function (array $nodes) use (&$countWalk, &$commentCount): void {
          foreach ($nodes as $n) {
            $commentCount++;
            if (!empty($n['children']) && is_array($n['children'])) {
              $countWalk($n['children']);
            }
          }
        };
        $countWalk($comments);
        $renderCommentBranch = null;
        $renderCommentBranch = function (array $nodes, int $depth) use (&$renderCommentBranch): void {
          foreach ($nodes as $c) {
            if (!is_array($c)) {
              continue;
            }
            $cid = (int) ($c['id'] ?? 0);
            $cname = trim((string) ($c['author_name'] ?? ''));
            $cbody = (string) ($c['body'] ?? '');
            $cdisp = (string) ($c['created_at_display'] ?? '');
            $ciso = (string) ($c['created_at_iso'] ?? '');
            $margin = $depth > 0 ? ' ml-6 border-l border-stone-200 pl-5 sm:ml-10 sm:pl-6' : '';
            ?>
            <li class="rounded-xl border border-stone-200/90 bg-white p-4 shadow-sm ring-1 ring-stone-200/60 sm:p-5<?= $margin !== '' ? $margin : '' ?>">
                <div class="flex flex-wrap items-baseline justify-between gap-2">
                    <p class="font-semibold text-stone-900"><?= htmlspecialchars($cname !== '' ? $cname : 'Reader', ENT_QUOTES, 'UTF-8') ?></p>
                    <?php if ($ciso !== '') : ?>
                        <time datetime="<?= htmlspecialchars($ciso, ENT_QUOTES, 'UTF-8') ?>"
                              class="text-xs font-medium tabular-nums text-stone-500"><?= htmlspecialchars($cdisp, ENT_QUOTES, 'UTF-8') ?></time>
                    <?php endif; ?>
                </div>
                <div class="mt-3 text-sm leading-relaxed text-stone-700">
                    <?= nl2br(htmlspecialchars($cbody, ENT_QUOTES, 'UTF-8')) ?>
                </div>
                <?php if ($cid > 0) : ?>
                    <p class="mt-3">
                        <button type="button"
                                class="text-xs font-semibold text-amber-900 underline decoration-amber-300 underline-offset-4 hover:decoration-amber-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white rounded-sm"
                                data-comment-reply="<?= $cid ?>">
                            Reply
                        </button>
                    </p>
                <?php endif; ?>
                <?php
                if (!empty($c['children']) && is_array($c['children'])) {
                  echo '<ul class="mt-4 space-y-4 list-none p-0 m-0">';
                  $renderCommentBranch($c['children'], $depth + 1);
                  echo '</ul>';
                }
                ?>
            </li>
            <?php
          }
        };
        ?>

        <section id="comments" class="mt-14 scroll-mt-24" aria-labelledby="comments-heading">
            <div class="flex flex-wrap items-end justify-between gap-3 border-b border-stone-200 pb-6">
                <h2 id="comments-heading" class="font-editorial text-2xl font-semibold tracking-tight text-stone-900">
                    Comments
                    <?php if ($commentCount > 0) : ?>
                        <span class="text-lg font-medium text-stone-500">(<?= $commentCount ?>)</span>
                    <?php endif; ?>
                </h2>
            </div>

            <?php if ($commentFlash === 'sent') : ?>
                <div class="mt-6 rounded-xl border border-emerald-200/90 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-950 ring-1 ring-emerald-100"
                     role="status">
                    Thanks—your comment was received and will appear after moderation.
                </div>
            <?php elseif ($commentFlash === 'csrf') : ?>
                <div class="mt-6 rounded-xl border border-red-200/90 bg-red-50/80 px-4 py-3 text-sm text-red-900 ring-1 ring-red-100"
                     role="alert">
                    Your session expired. Refresh the page and try again.
                </div>
            <?php elseif ($commentFlash === 'error') : ?>
                <div class="mt-6 rounded-xl border border-red-200/90 bg-red-50/80 px-4 py-3 text-sm text-red-900 ring-1 ring-red-100"
                     role="alert">
                    We could not save your comment. Please try again.
                </div>
            <?php endif; ?>

            <?php if ($comments !== []) : ?>
                <ul class="mt-8 space-y-4 list-none p-0 m-0">
                    <?php $renderCommentBranch($comments, 0); ?>
                </ul>
            <?php else : ?>
                <p class="mt-8 text-sm text-stone-600">No comments yet—be the first to share what you think.</p>
            <?php endif; ?>

            <div class="mt-10 rounded-2xl border border-stone-200/90 bg-stone-50/50 p-6 shadow-sm ring-1 ring-stone-200/60 sm:p-8">
                <h3 class="text-base font-semibold text-stone-900">Leave a comment</h3>
                <p class="mt-1 text-sm text-stone-600">Comments are moderated. Your email is not published.</p>

                <form method="post" action="<?= htmlspecialchars(blog_post_url($post['slug']), ENT_QUOTES, 'UTF-8') ?>#comments"
                      class="mt-6 space-y-4">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($commentCsrfToken, ENT_QUOTES, 'UTF-8') ?>"/>
                    <input type="hidden" name="parent_id" id="comment-parent-id" value="<?= $cfParent > 0 ? $cfParent : '' ?>"/>
                    <p class="hidden" aria-hidden="true">
                        <label for="comment-website">Website</label>
                        <input id="comment-website" name="website" type="text" tabindex="-1" autocomplete="off"/>
                    </p>

                    <?php if (auth_user() === null) : ?>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="comment-author-name" class="block text-sm font-semibold text-stone-800">Name</label>
                                <input id="comment-author-name" name="author_name" type="text" required maxlength="191"
                                       value="<?= htmlspecialchars($cfName, ENT_QUOTES, 'UTF-8') ?>"
                                       class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-900 shadow-inner focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                                <?php if (isset($commentFormErrors['author_name'])) : ?>
                                    <p class="mt-1 text-xs font-medium text-red-700"><?= htmlspecialchars($commentFormErrors['author_name'], ENT_QUOTES, 'UTF-8') ?></p>
                                <?php endif; ?>
                            </div>
                            <div>
                                <label for="comment-author-email" class="block text-sm font-semibold text-stone-800">Email</label>
                                <input id="comment-author-email" name="author_email" type="email" required maxlength="191"
                                       value="<?= htmlspecialchars($cfEmail, ENT_QUOTES, 'UTF-8') ?>"
                                       class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-900 shadow-inner focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                                <?php if (isset($commentFormErrors['author_email'])) : ?>
                                    <p class="mt-1 text-xs font-medium text-red-700"><?= htmlspecialchars($commentFormErrors['author_email'], ENT_QUOTES, 'UTF-8') ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <p class="text-sm text-stone-600">
                            Posting as <span class="font-semibold text-stone-900"><?= htmlspecialchars($cfName, ENT_QUOTES, 'UTF-8') ?></span>
                            (<?= htmlspecialchars($cfEmail, ENT_QUOTES, 'UTF-8') ?>).
                        </p>
                    <?php endif; ?>

                    <div>
                        <label for="comment-body" class="block text-sm font-semibold text-stone-800">Comment</label>
                        <textarea id="comment-body" name="body" rows="5" required maxlength="8000"
                                  class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-900 shadow-inner focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30"><?= htmlspecialchars($cfBody, ENT_QUOTES, 'UTF-8') ?></textarea>
                        <?php if (isset($commentFormErrors['body'])) : ?>
                            <p class="mt-1 text-xs font-medium text-red-700"><?= htmlspecialchars($commentFormErrors['body'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                        <?php if (isset($commentFormErrors['parent_id'])) : ?>
                            <p class="mt-1 text-xs font-medium text-red-700"><?= htmlspecialchars($commentFormErrors['parent_id'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </div>

                    <p id="comment-reply-hint" class="hidden text-xs font-medium text-amber-900"></p>

                    <div class="flex flex-wrap items-center gap-3">
                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-stone-900 px-5 py-2.5 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50">
                            Submit comment
                        </button>
                        <button type="button" id="comment-reply-cancel"
                                class="hidden text-sm font-semibold text-stone-700 underline decoration-stone-300 underline-offset-4 hover:decoration-stone-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50 rounded-sm">
                            Cancel reply
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <script>
            (function () {
                var parentInput = document.getElementById('comment-parent-id');
                var hint = document.getElementById('comment-reply-hint');
                var cancel = document.getElementById('comment-reply-cancel');
                var form = parentInput && parentInput.form;
                if (!parentInput || !hint || !cancel || !form) {
                    return;
                }
                function refreshReplyUi() {
                    var v = String(parentInput.value || '').trim();
                    if (v !== '') {
                        hint.textContent = 'Replying to comment #' + v + '.';
                        hint.classList.remove('hidden');
                        cancel.classList.remove('hidden');
                    } else {
                        hint.textContent = '';
                        hint.classList.add('hidden');
                        cancel.classList.add('hidden');
                    }
                }
                document.addEventListener('click', function (e) {
                    var btn = e.target && e.target.closest ? e.target.closest('[data-comment-reply]') : null;
                    if (!btn) {
                        return;
                    }
                    e.preventDefault();
                    parentInput.value = btn.getAttribute('data-comment-reply') || '';
                    refreshReplyUi();
                    var ta = document.getElementById('comment-body');
                    if (ta) {
                        ta.focus();
                    }
                    form.scrollIntoView({behavior: 'smooth', block: 'nearest'});
                });
                cancel.addEventListener('click', function () {
                    parentInput.value = '';
                    refreshReplyUi();
                });
                refreshReplyUi();
            })();
        </script>

        <footer class="mt-14 border-t border-stone-200 pt-10">
            <div class="flex flex-col gap-6 rounded-xl border border-stone-200/90 bg-white p-6 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-stone-400">Share</p>
                    <p class="mt-1 text-sm text-stone-600">Copy the link to send this article to someone.</p>
                </div>
                <div class="flex min-w-0 flex-1 flex-col gap-2 sm:max-w-md sm:flex-row sm:items-center">
                    <label class="sr-only" for="post-url">Article URL</label>
                    <input id="post-url" type="text" readonly
                           value="<?= htmlspecialchars($absolutePostUrl, ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full min-w-0 rounded-lg border border-stone-300 bg-stone-50 px-3 py-2 text-sm text-stone-800 shadow-inner focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    <button type="button"
                            class="shrink-0 rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50"
                            data-copy="<?= htmlspecialchars($absolutePostUrl, ENT_QUOTES, 'UTF-8') ?>"
                            onclick="navigator.clipboard.writeText(this.dataset.copy); this.textContent='Copied'; setTimeout(()=>this.textContent='Copy', 1800);">
                        Copy
                    </button>
                </div>
            </div>

            <p class="mt-10">
                <a href="<?= htmlspecialchars(blog_url('blogs'), ENT_QUOTES, 'UTF-8') ?>"
                   class="inline-flex items-center gap-2 text-sm font-semibold text-stone-900 underline decoration-stone-300 underline-offset-4 transition hover:decoration-stone-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50 rounded-sm"
                   onclick="if (window.history.length > 1) { event.preventDefault(); window.history.back(); }">
                    ← Back
                </a>
            </p>
        </footer>
    </article>
</main>

<?php require_once base_path('views/partials/footer.php'); ?>
