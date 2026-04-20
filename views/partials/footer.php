<?php
$year = (int)date('Y');
$homeUrl = blog_url();
$blogUrl = blog_url('blogs');
$aboutUrl = blog_url('about');
?>

<footer class="mt-auto border-t border-stone-200 bg-white">
    <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-12 lg:gap-12">
            <div class="lg:col-span-5">
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-stone-900 text-sm font-semibold text-amber-50 shadow-sm ring-1 ring-stone-900/10"
                          aria-hidden="true">M</span>
                    <div>
                        <p class="text-sm font-semibold text-stone-900">Mini Blog</p>
                        <p class="text-xs font-medium text-stone-500">Personal writing</p>
                    </div>
                </div>
                <p class="mt-4 max-w-sm text-sm leading-relaxed text-stone-600">
                    A lightweight PHP starter for publishing—clear structure, readable type, and room to add posts when you are ready.
                </p>
            </div>

            <div class="sm:col-span-1 lg:col-span-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-stone-400">Explore</p>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li>
                        <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>"
                           class="font-medium text-stone-700 transition hover:text-stone-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white rounded-sm">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="<?= htmlspecialchars($blogUrl, ENT_QUOTES, 'UTF-8') ?>"
                           class="font-medium text-stone-700 transition hover:text-stone-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white rounded-sm">
                            Blog
                        </a>
                    </li>
                    <li>
                        <a href="<?= htmlspecialchars($aboutUrl, ENT_QUOTES, 'UTF-8') ?>"
                           class="font-medium text-stone-700 transition hover:text-stone-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white rounded-sm">
                            About
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sm:col-span-1 lg:col-span-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-stone-400">Colophon</p>
                <p class="mt-4 text-sm leading-relaxed text-stone-600">
                    Built with PHP, Tailwind CSS, and simple templates. Extend with a database when you need drafts, tags, and search.
                </p>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-stone-100 pt-8 sm:flex-row">
            <p class="text-xs text-stone-500">
                © <?= $year ?> Mini Blog. All rights reserved.
            </p>
            <p class="text-xs text-stone-400">
                Crafted for reading, not scrolling.
            </p>
        </div>
    </div>
</footer>
</body>
</html>
