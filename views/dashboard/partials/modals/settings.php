<?php
$btnPri = 'inline-flex items-center justify-center rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
?>

<div id="modal-setting-view" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-setting-view-title" tabindex="-1"
             class="w-full max-w-lg rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <div>
                    <h2 id="modal-setting-view-title" class="text-base font-semibold text-stone-900">Settings snapshot</h2>
                    <p class="mt-1 text-xs text-stone-500">Read-only values from the database.</p>
                </div>
                <button type="button" data-modal-close class="rounded-lg p-2 text-stone-500 hover:bg-stone-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="space-y-4 px-5 py-4 text-sm">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Site title</p>
                    <p class="mt-1 font-semibold text-stone-900" data-view="setting-site-title">—</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Tagline</p>
                    <p class="mt-1 text-stone-800" data-view="setting-tagline">—</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Posts / page</p>
                        <p class="mt-1 tabular-nums text-stone-800" data-view="setting-posts-per-page">—</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Date format</p>
                        <p class="mt-1 text-stone-800" data-view="setting-date-format">—</p>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">RSS feed</p>
                    <p class="mt-1 text-stone-800" data-view="setting-rss">—</p>
                </div>
            </div>
            <div class="flex justify-end border-t border-stone-100 px-5 py-4">
                <button type="button" data-modal-close class="<?= $btnPri ?>">Close</button>
            </div>
        </div>
    </div>
</div>
