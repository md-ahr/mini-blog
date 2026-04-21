<?php
$fld = 'mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30';
$lbl = 'block text-xs font-semibold text-stone-700';
$btnPri = 'inline-flex items-center justify-center rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
$btnSec = 'inline-flex items-center justify-center rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
?>

<div id="modal-comment-add" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-comment-add-title" tabindex="-1"
             class="w-full max-w-lg rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <div>
                    <h2 id="modal-comment-add-title" class="text-base font-semibold text-stone-900">Add staff note</h2>
                    <p class="mt-1 text-xs text-stone-500">Internal-only memo (demo UI).</p>
                </div>
                <button type="button" data-modal-close class="rounded-lg p-2 text-stone-500 hover:bg-stone-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <form class="space-y-4 px-5 py-4" action="#" method="get" onsubmit="return false;">
                <div>
                    <label class="<?= $lbl ?>" for="comment-add-post">Related post</label>
                    <input id="comment-add-post" name="comment_post" type="text" class="<?= $fld ?>" placeholder="Post title"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="comment-add-body">Note</label>
                    <textarea id="comment-add-body" name="comment_body" rows="4" class="<?= $fld ?>" placeholder="Why this thread is flagged, next steps, etc."></textarea>
                </div>
            </form>
            <div class="flex flex-col-reverse gap-2 border-t border-stone-100 px-5 py-4 sm:flex-row sm:justify-end">
                <button type="button" data-modal-close class="<?= $btnSec ?> w-full sm:w-auto">Cancel</button>
                <button type="button" class="<?= $btnPri ?> w-full sm:w-auto">Save note</button>
            </div>
        </div>
    </div>
</div>

<div id="modal-comment-edit" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-comment-edit-title" tabindex="-1"
             class="max-h-[min(90vh,44rem)] w-full max-w-lg overflow-y-auto rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <h2 id="modal-comment-edit-title" class="text-base font-semibold text-stone-900">Edit comment</h2>
                <button type="button" data-modal-close class="rounded-lg p-2 text-stone-500 hover:bg-stone-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <form class="space-y-4 px-5 py-4" action="#" method="get" onsubmit="return false;">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="<?= $lbl ?>" for="comment-edit-author">Author</label>
                        <input id="comment-edit-author" name="comment_author" type="text" class="<?= $fld ?>"/>
                    </div>
                    <div>
                        <label class="<?= $lbl ?>" for="comment-edit-email">Email</label>
                        <input id="comment-edit-email" name="comment_email" type="email" class="<?= $fld ?>"/>
                    </div>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="comment-edit-post">Post</label>
                    <input id="comment-edit-post" name="comment_post" type="text" class="<?= $fld ?>"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="comment-edit-state">Status</label>
                    <select id="comment-edit-state" name="comment_state" class="<?= $dashboardSelect ?> mt-1.5">
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Spam">Spam</option>
                    </select>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="comment-edit-body">Body</label>
                    <textarea id="comment-edit-body" name="comment_body" rows="5" class="<?= $fld ?>"></textarea>
                </div>
            </form>
            <div class="flex flex-col-reverse gap-2 border-t border-stone-100 px-5 py-4 sm:flex-row sm:justify-end">
                <button type="button" data-modal-close class="<?= $btnSec ?> w-full sm:w-auto">Cancel</button>
                <button type="button" class="<?= $btnPri ?> w-full sm:w-auto">Save comment</button>
            </div>
        </div>
    </div>
</div>

<div id="modal-comment-view" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-comment-view-title" tabindex="-1"
             class="max-h-[min(90vh,44rem)] w-full max-w-lg overflow-y-auto rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <h2 id="modal-comment-view-title" class="text-base font-semibold text-stone-900">View comment</h2>
                <button type="button" data-modal-close class="rounded-lg p-2 text-stone-500 hover:bg-stone-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="space-y-4 px-5 py-4 text-sm">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Author</p>
                        <p class="mt-1 font-semibold text-stone-900" data-view="comment-author">—</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Received</p>
                        <p class="mt-1 tabular-nums text-stone-800" data-view="comment-when">—</p>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Email</p>
                    <p class="mt-1 text-stone-700" data-view="comment-email">—</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Post</p>
                    <p class="mt-1 text-stone-800" data-view="comment-post">—</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Status</p>
                    <p class="mt-1 text-stone-800" data-view="comment-state">—</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Comment</p>
                    <p class="mt-2 leading-relaxed text-stone-700" data-view="comment-body">—</p>
                </div>
            </div>
            <div class="flex justify-end border-t border-stone-100 px-5 py-4">
                <button type="button" data-modal-close class="<?= $btnPri ?>">Close</button>
            </div>
        </div>
    </div>
</div>
