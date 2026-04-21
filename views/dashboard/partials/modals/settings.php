<?php
$fld = 'mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30';
$lbl = 'block text-xs font-semibold text-stone-700';
$btnPri = 'inline-flex items-center justify-center rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
$btnSec = 'inline-flex items-center justify-center rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
?>

<div id="modal-setting-add" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-setting-add-title" tabindex="-1"
             class="w-full max-w-lg rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <div>
                    <h2 id="modal-setting-add-title" class="text-base font-semibold text-stone-900">Add snippet</h2>
                    <p class="mt-1 text-xs text-stone-500">Footer scripts, analytics, or theme injections (demo).</p>
                </div>
                <button type="button" data-modal-close class="rounded-lg p-2 text-stone-500 hover:bg-stone-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <form class="space-y-4 px-5 py-4" action="#" method="get" onsubmit="return false;">
                <div>
                    <label class="<?= $lbl ?>" for="setting-add-label">Label</label>
                    <input id="setting-add-label" name="snippet_label" type="text" class="<?= $fld ?>" placeholder="Plausible analytics"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="setting-add-placement">Placement</label>
                    <select id="setting-add-placement" name="snippet_placement" class="<?= $dashboardSelect ?> mt-1.5">
                        <option value="head">Head</option>
                        <option value="footer">Footer</option>
                    </select>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="setting-add-code">Code</label>
                    <textarea id="setting-add-code" name="snippet_code" rows="6" class="<?= $fld ?> font-mono text-xs" placeholder="&lt;script&gt;…&lt;/script&gt;"></textarea>
                </div>
            </form>
            <div class="flex flex-col-reverse gap-2 border-t border-stone-100 px-5 py-4 sm:flex-row sm:justify-end">
                <button type="button" data-modal-close class="<?= $btnSec ?> w-full sm:w-auto">Cancel</button>
                <button type="button" class="<?= $btnPri ?> w-full sm:w-auto">Save snippet</button>
            </div>
        </div>
    </div>
</div>

<div id="modal-setting-edit" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-setting-edit-title" tabindex="-1"
             class="w-full max-w-lg rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <h2 id="modal-setting-edit-title" class="text-base font-semibold text-stone-900">Edit branding</h2>
                <button type="button" data-modal-close class="rounded-lg p-2 text-stone-500 hover:bg-stone-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <form class="space-y-4 px-5 py-4" action="#" method="get" onsubmit="return false;">
                <div>
                    <label class="<?= $lbl ?>" for="setting-edit-site-title">Site title</label>
                    <input id="setting-edit-site-title" name="setting_site_title" type="text" class="<?= $fld ?>"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="setting-edit-tagline">Tagline</label>
                    <input id="setting-edit-tagline" name="setting_tagline" type="text" class="<?= $fld ?>"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="setting-edit-posts-per-page">Posts per page</label>
                    <input id="setting-edit-posts-per-page" name="setting_posts_per_page" type="number" min="1" class="<?= $fld ?> tabular-nums"/>
                </div>
            </form>
            <div class="flex flex-col-reverse gap-2 border-t border-stone-100 px-5 py-4 sm:flex-row sm:justify-end">
                <button type="button" data-modal-close class="<?= $btnSec ?> w-full sm:w-auto">Cancel</button>
                <button type="button" class="<?= $btnPri ?> w-full sm:w-auto">Apply</button>
            </div>
        </div>
    </div>
</div>

<div id="modal-setting-view" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-setting-view-title" tabindex="-1"
             class="w-full max-w-lg rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <div>
                    <h2 id="modal-setting-view-title" class="text-base font-semibold text-stone-900">Settings snapshot</h2>
                    <p class="mt-1 text-xs text-stone-500">Read-only values for support and audits.</p>
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
