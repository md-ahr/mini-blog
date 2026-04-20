<?php
$year = (int)date('Y');
$navBase = defined('BLOG_BASE_PATH') ? rtrim(BLOG_BASE_PATH, '/') . '/' : '';
$homeUrl = $navBase . 'index.php';
?>

<footer class="mt-auto border-t border-slate-200/80 bg-white">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
        <div class="flex flex-col items-center justify-between gap-6 sm:flex-row sm:items-start">
            <div class="text-center sm:text-left">
                <p class="text-sm font-semibold text-slate-900">Mini Blog</p>
                <p class="mt-1 max-w-md text-sm leading-relaxed text-slate-600">
                    A small space for notes, ideas, and writing.
                </p>
            </div>
            <nav class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm font-medium text-slate-600"
                 aria-label="Footer">
                <a
                        href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>"
                        class="transition hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 focus-visible:ring-offset-white rounded-sm"
                >
                    Home
                </a>
            </nav>
        </div>
        <p class="mt-8 text-center text-xs text-slate-500 sm:text-left">
            © <?= $year ?> Mini Blog. All rights reserved.
        </p>
    </div>
</footer>
</body>
</html>
