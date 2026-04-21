<?php
$pageTitle = 'Page not found — Mini Blog';
$metaDescription = 'The page you requested does not exist.';
require_once base_path('views/partials/head.php');
require_once base_path('views/partials/navbar.php');
?>

<main id="main-content" tabindex="-1"
      class="mx-auto flex w-full max-w-6xl flex-1 flex-col items-center justify-center px-4 pb-24 pt-16 text-center sm:px-6 lg:px-8">
    <p class="text-7xl font-semibold uppercase tracking-[0.2em] text-stone-500">404</p>
    <h1 class="font-editorial mt-4 text-3xl font-semibold tracking-tight text-stone-900 sm:text-4xl">
        This page does not exist
    </h1>
    <p class="mt-4 max-w-md text-base leading-relaxed text-stone-600">
        The link may be broken or the post may have been removed. Try starting from the home page.
    </p>
    <p class="mt-10">
        <a href="<?= htmlspecialchars(blog_url(), ENT_QUOTES, 'UTF-8') ?>"
           class="inline-flex items-center justify-center rounded-lg bg-stone-900 px-5 py-2.5 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50">
            Back to home
        </a>
    </p>
</main>

<?php require_once base_path('views/partials/footer.php'); ?>
