<?php
/**
 * Home page. Variables come from Http/controllers/index.php via view() → extract().
 *
 * @var string $message
 * @var string $heading
 */
require_once base_path('views/partials/head.php');
require_once base_path('views/partials/navbar.php');
?>

<main class="mx-auto w-full max-w-7xl flex-1 px-4 py-16 sm:px-6">
    <h1 class="text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">
        <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
    </h1>
    <p class="mt-4 max-w-2xl text-base leading-relaxed text-slate-600">
        Your personal writing space—simple, fast, and easy to extend.
    </p>
</main>

<?php require_once base_path('views/partials/footer.php'); ?>
