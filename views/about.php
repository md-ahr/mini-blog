<?php
/**
 * About page.
 *
 * @var string $heading
 * @var string $pageTitle
 * @var string $metaDescription
 */
require_once base_path('views/partials/head.php');
require_once base_path('views/partials/navbar.php');
?>

<main id="main-content" tabindex="-1" class="mx-auto w-full max-w-6xl flex-1 px-4 pb-20 pt-10 sm:px-6 lg:px-8">
    <header class="max-w-2xl">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-500">About</p>
        <h1 class="font-editorial mt-3 text-4xl font-semibold tracking-tight text-stone-900 sm:text-5xl">
            <?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?>
        </h1>
        <p class="mt-5 text-lg leading-relaxed text-stone-600">
            This project is a compact PHP blog skeleton: a router, views, and optional MySQL—enough structure to
            publish without a heavy framework.
        </p>
    </header>

    <div class="mt-12 max-w-2xl space-y-6 text-base leading-relaxed text-stone-700">
        <p>
            The layout is built for reading: serif headlines, calm neutrals, and a clear hierarchy from the home
            hero through article lists. You can swap placeholder posts for real records when you connect your
            database.
        </p>
        <p>
            Headers and footers are shared partials, so navigation and branding stay consistent as you add pages.
            Extend routes in <code class="rounded bg-stone-100 px-1.5 py-0.5 text-sm text-stone-800">routes.php</code>
            and ship new controllers alongside templates in <code class="rounded bg-stone-100 px-1.5 py-0.5 text-sm text-stone-800">Http/controllers/</code>.
        </p>
    </div>

    <p class="mt-12">
        <a href="<?= htmlspecialchars(blog_url(), ENT_QUOTES, 'UTF-8') ?>"
           class="inline-flex items-center gap-2 text-sm font-semibold text-stone-900 underline decoration-stone-300 underline-offset-4 transition hover:decoration-stone-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50 rounded-sm">
            ← Back to home
        </a>
    </p>
</main>

<?php require_once base_path('views/partials/footer.php'); ?>
