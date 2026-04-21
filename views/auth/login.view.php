<?php
/**
 * @var string $pageTitle
 * @var string $metaDescription
 * @var string $pageRobots
 * @var string $loginError
 * @var string $loginNotice
 * @var string $loginNext
 * @var string $csrfToken
 */
require_once base_path('views/partials/head.php');

$homeUrl = blog_url();
$loginUrl = blog_url('login');
$loginError = $loginError ?? '';
$loginNotice = $loginNotice ?? '';
$loginNext = $loginNext ?? '';
$csrfToken = $csrfToken ?? '';
?>

<main id="main-content" tabindex="-1" class="flex flex-1 flex-col items-center justify-center px-4 py-16 sm:px-6">
    <div class="w-full max-w-md">
        <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>"
           class="group mb-10 flex items-center justify-center gap-3 rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-stone-900 text-sm font-semibold tracking-tight text-amber-50 shadow-sm ring-1 ring-stone-900/10 transition group-hover:bg-stone-800"
                  aria-hidden="true">M</span>
            <span class="text-left">
                <span class="block text-sm font-semibold tracking-tight text-stone-900">Mini Blog</span>
                <span class="block text-xs font-medium text-stone-500">Dashboard access</span>
            </span>
        </a>

        <div class="rounded-2xl border border-stone-200/90 bg-white p-6 shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80 sm:p-8">
            <h1 class="font-editorial text-2xl font-semibold tracking-tight text-stone-900 text-center">Sign in</h1>
            <p class="mt-2 text-center text-sm leading-relaxed text-stone-600">
                Use your email and password
            </p>

            <?php if ($loginError !== '') : ?>
                <div class="mt-6 rounded-xl border border-red-200/90 bg-red-50/60 px-4 py-3 text-sm text-red-900 ring-1 ring-red-100/80"
                     role="alert">
                    <?= htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>
            <?php if ($loginNotice !== '') : ?>
                <div class="mt-6 rounded-xl border border-emerald-200/90 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-950 ring-1 ring-emerald-100/80"
                     role="status">
                    <?= htmlspecialchars($loginNotice, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form class="mt-8 space-y-5" method="post" action="<?= htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') ?>" novalidate>
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"/>
                <?php if ($loginNext !== '') : ?>
                    <input type="hidden" name="next" value="<?= htmlspecialchars($loginNext, ENT_QUOTES, 'UTF-8') ?>"/>
                <?php endif; ?>
                <div>
                    <label for="login-email" class="block text-xs font-semibold text-stone-700">Email</label>
                    <input id="login-email" name="email" type="email" autocomplete="username" required
                           class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2.5 text-sm text-stone-900 shadow-inner shadow-stone-900/5 placeholder:text-stone-400 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"
                           placeholder="you@example.com"/>
                </div>
                <div>
                    <label for="login-password" class="block text-xs font-semibold text-stone-700">Password</label>
                    <input id="login-password" name="password" type="password" autocomplete="current-password" required
                           class="mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2.5 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30"
                           placeholder="••••••••"/>
                </div>
                <button type="submit"
                        class="flex w-full items-center justify-center rounded-xl bg-stone-900 px-4 py-3 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                    Sign in
                </button>
            </form>
        </div>

        <p class="mt-8 text-center">
            <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>"
               class="text-sm font-semibold text-stone-700 underline decoration-stone-300/90 underline-offset-4 transition hover:text-stone-900 hover:decoration-stone-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50 rounded-sm">
                ← Back to site
            </a>
        </p>
    </div>
</main>

<footer class="mt-auto border-t border-stone-200/90 bg-white py-6">
    <p class="text-center text-xs text-stone-500">
        © <?= (int)date('Y') ?> Mini Blog
    </p>
</footer>

</body>
</html>
