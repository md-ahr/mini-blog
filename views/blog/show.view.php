<?php
/**
 * Single post.
 *
 * @var array<string, mixed> $post
 * @var string $pageTitle
 * @var string $metaDescription
 */
require_once base_path('views/partials/head.php');
require_once base_path('views/partials/navbar.php');

$path = blog_post_url($post['slug']);
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$absolutePostUrl = $scheme . '://' . $host . $path;
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
            <li class="text-stone-900" aria-current="page">Article</li>
        </ol>
    </nav>

    <article class="mx-auto max-w-2xl">
        <header class="border-b border-stone-200/90 pb-10">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-2 text-xs font-medium text-stone-500">
                <span class="rounded-full bg-amber-100/90 px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wide text-amber-900/90 ring-1 ring-amber-200/80">
                    <?= htmlspecialchars($post['tag'], ENT_QUOTES, 'UTF-8') ?>
                </span>
                <span class="text-stone-600">
                    By <span class="font-semibold text-stone-900"><?= htmlspecialchars($post['author'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                </span>
                <span class="text-stone-300" aria-hidden="true">·</span>
                <time datetime="<?= htmlspecialchars($post['dateIso'], ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($post['dateDisplay'], ENT_QUOTES, 'UTF-8') ?>
                </time>
                <span class="text-stone-300" aria-hidden="true">·</span>
                <span><?= (int) $post['readingMinutes'] ?> min read</span>
            </div>
            <h1 class="font-editorial mt-6 text-[2rem] font-semibold leading-[1.15] tracking-tight text-stone-900 sm:text-4xl sm:leading-[1.1]">
                <?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="mt-5 text-lg leading-relaxed text-stone-600 sm:text-xl">
                <?= htmlspecialchars($post['excerpt'], ENT_QUOTES, 'UTF-8') ?>
            </p>
        </header>

        <div class="article-body mt-12 space-y-6 text-[1.0625rem] leading-[1.75] text-stone-700 sm:text-lg sm:leading-[1.7]">
            <?php foreach ($post['content'] as $block) : ?>
                <?php if ($block['type'] === 'p') : ?>
                    <p><?= nl2br(htmlspecialchars($block['text'], ENT_QUOTES, 'UTF-8')) ?></p>
                <?php elseif ($block['type'] === 'h2') : ?>
                    <h2 class="font-editorial pt-4 text-2xl font-semibold tracking-tight text-stone-900 sm:text-[1.75rem]">
                        <?= htmlspecialchars($block['text'], ENT_QUOTES, 'UTF-8') ?>
                    </h2>
                <?php elseif ($block['type'] === 'blockquote') : ?>
                    <blockquote class="border-l-4 border-amber-400/90 bg-amber-50/50 py-1 pl-6 pr-4 font-editorial text-xl italic leading-snug text-stone-800">
                        <?= htmlspecialchars($block['text'], ENT_QUOTES, 'UTF-8') ?>
                    </blockquote>
                <?php elseif ($block['type'] === 'ul' && isset($block['items'])) : ?>
                    <ul class="list-disc space-y-2 pl-6 marker:text-amber-600">
                        <?php foreach ($block['items'] as $item) : ?>
                            <li><?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

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
                <a href="<?= htmlspecialchars(blog_url(), ENT_QUOTES, 'UTF-8') ?>"
                   class="inline-flex items-center gap-2 text-sm font-semibold text-stone-900 underline decoration-stone-300 underline-offset-4 transition hover:decoration-stone-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50 rounded-sm">
                    ← All posts
                </a>
            </p>
        </footer>
    </article>
</main>

<?php require_once base_path('views/partials/footer.php'); ?>
