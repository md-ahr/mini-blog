        </main>

        <footer class="mt-auto border-t border-stone-200/90 bg-white">
            <div class="px-4 py-6 sm:px-6 lg:px-10">
                <div class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
                    <p class="text-xs text-stone-500">
                        Dashboard UI preview — wire up actions when you connect auth and persistence.
                    </p>
                    <a href="<?= htmlspecialchars(blog_url(), ENT_QUOTES, 'UTF-8') ?>"
                       class="text-xs font-semibold text-amber-900/90 underline decoration-amber-300/80 underline-offset-4 hover:text-amber-950 hover:decoration-amber-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white rounded-sm">
                        ← Back to site
                    </a>
                </div>
            </div>
        </footer>
    </div>
</div>

<?php require_once base_path('views/dashboard/partials/modal-runtime.php'); ?>

</body>
</html>
