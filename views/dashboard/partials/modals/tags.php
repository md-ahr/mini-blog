<?php
$fld = 'mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30';
$lbl = 'block text-xs font-semibold text-stone-700';
$btnPri = 'inline-flex items-center justify-center rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
$btnSec = 'inline-flex items-center justify-center rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white';

$tagsUrl = $tagsUrl ?? blog_url('dashboard/tags');
$csrfToken = $csrfToken ?? auth_csrf_token();
$h = static fn (string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
?>

<div id="modal-tag-add" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-tag-add-title" tabindex="-1"
             class="w-full max-w-md rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <h2 id="modal-tag-add-title" class="text-base font-semibold text-stone-900">Add tag</h2>
                <button type="button" data-modal-close class="rounded-lg p-2 text-stone-500 hover:bg-stone-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <form class="space-y-4 px-5 py-4" method="post" action="<?= $h($tagsUrl) ?>">
                <input type="hidden" name="_csrf" value="<?= $h($csrfToken) ?>"/>
                <input type="hidden" name="_action" value="create"/>
                <div>
                    <label class="<?= $lbl ?>" for="tag-add-name">Name</label>
                    <input id="tag-add-name" name="name" type="text" required maxlength="191" class="<?= $fld ?>" placeholder="Field notes"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="tag-add-slug">Slug <span class="font-normal text-stone-500">(optional)</span></label>
                    <input id="tag-add-slug" name="slug" type="text" maxlength="191" class="<?= $fld ?> font-mono text-xs" placeholder="field-notes"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="tag-add-color">Color</label>
                    <input id="tag-add-color" name="color" type="color" value="#78716c"
                           class="mt-1.5 h-10 w-full cursor-pointer rounded-xl border border-stone-200 bg-white px-1 py-1 shadow-inner shadow-stone-900/5 focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                </div>
                <div class="flex flex-col-reverse gap-2 border-t border-stone-100 pt-4 sm:flex-row sm:justify-end">
                    <button type="button" data-modal-close class="<?= $btnSec ?> w-full sm:w-auto">Cancel</button>
                    <button type="submit" class="<?= $btnPri ?> w-full sm:w-auto">Save tag</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modal-tag-edit" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-tag-edit-title" tabindex="-1"
             class="w-full max-w-md rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <h2 id="modal-tag-edit-title" class="text-base font-semibold text-stone-900">Edit tag</h2>
                <button type="button" data-modal-close class="rounded-lg p-2 text-stone-500 hover:bg-stone-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <form class="space-y-4 px-5 py-4" method="post" action="<?= $h($tagsUrl) ?>">
                <input type="hidden" name="_csrf" value="<?= $h($csrfToken) ?>"/>
                <input type="hidden" name="_action" value="update"/>
                <input type="hidden" name="id" value="" id="tag-edit-id"/>
                <div>
                    <label class="<?= $lbl ?>" for="tag-edit-name">Name</label>
                    <input id="tag-edit-name" name="name" type="text" required maxlength="191" class="<?= $fld ?>"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="tag-edit-slug">Slug</label>
                    <input id="tag-edit-slug" name="slug" type="text" maxlength="191" class="<?= $fld ?> font-mono text-xs"/>
                </div>
                <p class="text-xs text-stone-500">Posts using this tag: <span id="tag-edit-posts-readonly" class="font-semibold text-stone-700 tabular-nums">0</span></p>
                <div>
                    <label class="<?= $lbl ?>" for="tag-edit-color">Color</label>
                    <input id="tag-edit-color" name="color" type="color" value="#78716c"
                           class="mt-1.5 h-10 w-full cursor-pointer rounded-xl border border-stone-200 bg-white px-1 py-1 shadow-inner shadow-stone-900/5 focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                </div>
                <div class="flex flex-col-reverse gap-2 border-t border-stone-100 pt-4 sm:flex-row sm:justify-end">
                    <button type="button" data-modal-close class="<?= $btnSec ?> w-full sm:w-auto">Cancel</button>
                    <button type="submit" class="<?= $btnPri ?> w-full sm:w-auto">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modal-tag-view" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-tag-view-title" tabindex="-1"
             class="w-full max-w-md rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <h2 id="modal-tag-view-title" class="text-base font-semibold text-stone-900">View tag</h2>
                <button type="button" data-modal-close class="rounded-lg p-2 text-stone-500 hover:bg-stone-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="space-y-4 px-5 py-4 text-sm">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Name</p>
                    <p class="mt-1 font-semibold text-stone-900" data-view="tag-name">—</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Slug</p>
                    <p class="mt-1 font-mono text-xs text-stone-700" data-view="tag-slug">—</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Posts</p>
                    <p class="mt-1 tabular-nums text-stone-800" data-view="tag-posts">—</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Color</p>
                    <div class="mt-2 flex items-center gap-3">
                        <span data-view="tag-color-swatch" class="h-9 w-9 shrink-0 rounded-lg border border-stone-200 bg-stone-300 shadow-inner shadow-stone-900/10" aria-hidden="true"></span>
                        <p class="font-mono text-xs text-stone-700" data-view="tag-color-hex">—</p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end border-t border-stone-100 px-5 py-4">
                <button type="button" data-modal-close class="<?= $btnPri ?>">Close</button>
            </div>
        </div>
    </div>
</div>
