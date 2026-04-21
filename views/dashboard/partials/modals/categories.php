<?php
$fld = 'mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30';
$lbl = 'block text-xs font-semibold text-stone-700';
$btnPri = 'inline-flex items-center justify-center rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
$btnSec = 'inline-flex items-center justify-center rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white';

$categoriesUrl = $categoriesUrl ?? blog_url('dashboard/categories');
$csrfToken = $csrfToken ?? auth_csrf_token();
$categories = $categories ?? [];
$h = static fn (string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
?>

<div id="modal-category-add" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-category-add-title" tabindex="-1"
             class="max-h-[min(90vh,40rem)] w-full max-w-lg overflow-y-auto rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <h2 id="modal-category-add-title" class="text-base font-semibold text-stone-900">New category</h2>
                <button type="button" data-modal-close class="rounded-lg p-2 text-stone-500 hover:bg-stone-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <form class="space-y-4 px-5 py-4" method="post" action="<?= $h($categoriesUrl) ?>">
                <input type="hidden" name="_csrf" value="<?= $h($csrfToken) ?>"/>
                <input type="hidden" name="_action" value="create"/>
                <div>
                    <label class="<?= $lbl ?>" for="cat-add-name">Name</label>
                    <input id="cat-add-name" name="name" type="text" required maxlength="191" class="<?= $fld ?>" placeholder="Journal"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="cat-add-slug">Slug <span class="font-normal text-stone-500">(optional)</span></label>
                    <input id="cat-add-slug" name="slug" type="text" maxlength="191" class="<?= $fld ?> font-mono text-xs" placeholder="journal"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="cat-add-parent">Parent</label>
                    <select id="cat-add-parent" name="parent_id" class="<?= htmlspecialchars($dashboardSelect, ENT_QUOTES, 'UTF-8') ?> mt-1.5">
                        <option value="">None (top level)</option>
                        <?php foreach ($categories as $c) : ?>
                            <option value="<?= (int) ($c['id'] ?? 0) ?>"><?= $h((string) ($c['name'] ?? '')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="cat-add-sort">Sort order</label>
                    <input id="cat-add-sort" name="sort_order" type="number" value="0" class="<?= $fld ?> tabular-nums"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="cat-add-color">Accent color</label>
                    <input id="cat-add-color" name="color" type="color" value="#57534e"
                           class="mt-1.5 h-10 w-full cursor-pointer rounded-xl border border-stone-200 bg-white px-1 py-1 shadow-inner shadow-stone-900/5 focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="cat-add-desc">Description</label>
                    <textarea id="cat-add-desc" name="description" rows="4" class="<?= $fld ?>" placeholder="Archive intro copy."></textarea>
                </div>
                <div class="flex flex-col-reverse gap-2 border-t border-stone-100 pt-4 sm:flex-row sm:justify-end">
                    <button type="button" data-modal-close class="<?= $btnSec ?> w-full sm:w-auto">Cancel</button>
                    <button type="submit" class="<?= $btnPri ?> w-full sm:w-auto">Create category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modal-category-edit" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-category-edit-title" tabindex="-1"
             class="max-h-[min(90vh,40rem)] w-full max-w-lg overflow-y-auto rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <h2 id="modal-category-edit-title" class="text-base font-semibold text-stone-900">Edit category</h2>
                <button type="button" data-modal-close class="rounded-lg p-2 text-stone-500 hover:bg-stone-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <form class="space-y-4 px-5 py-4" method="post" action="<?= $h($categoriesUrl) ?>">
                <input type="hidden" name="_csrf" value="<?= $h($csrfToken) ?>"/>
                <input type="hidden" name="_action" value="update"/>
                <input type="hidden" name="id" value="" id="cat-edit-id"/>
                <div>
                    <label class="<?= $lbl ?>" for="cat-edit-name">Name</label>
                    <input id="cat-edit-name" name="name" type="text" required maxlength="191" class="<?= $fld ?>"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="cat-edit-slug">Slug</label>
                    <input id="cat-edit-slug" name="slug" type="text" maxlength="191" class="<?= $fld ?> font-mono text-xs"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="cat-edit-parent">Parent</label>
                    <select id="cat-edit-parent" name="parent_id" class="<?= htmlspecialchars($dashboardSelect, ENT_QUOTES, 'UTF-8') ?> mt-1.5">
                        <option value="">None (top level)</option>
                        <?php foreach ($categories as $c) : ?>
                            <option value="<?= (int) ($c['id'] ?? 0) ?>"><?= $h((string) ($c['name'] ?? '')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="cat-edit-sort">Sort order</label>
                    <input id="cat-edit-sort" name="sort_order" type="number" value="0" class="<?= $fld ?> tabular-nums"/>
                </div>
                <p class="text-xs text-stone-500">Posts in this category: <span id="cat-edit-posts-readonly" class="font-semibold text-stone-700 tabular-nums">0</span></p>
                <div>
                    <label class="<?= $lbl ?>" for="cat-edit-color">Accent color</label>
                    <input id="cat-edit-color" name="color" type="color" value="#57534e"
                           class="mt-1.5 h-10 w-full cursor-pointer rounded-xl border border-stone-200 bg-white px-1 py-1 shadow-inner shadow-stone-900/5 focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="cat-edit-desc">Description</label>
                    <textarea id="cat-edit-desc" name="description" rows="4" class="<?= $fld ?>"></textarea>
                </div>
                <div class="flex flex-col-reverse gap-2 border-t border-stone-100 pt-4 sm:flex-row sm:justify-end">
                    <button type="button" data-modal-close class="<?= $btnSec ?> w-full sm:w-auto">Cancel</button>
                    <button type="submit" class="<?= $btnPri ?> w-full sm:w-auto">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modal-category-view" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-category-view-title" tabindex="-1"
             class="w-full max-w-lg rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <h2 id="modal-category-view-title" class="text-base font-semibold text-stone-900">View category</h2>
                <button type="button" data-modal-close class="rounded-lg p-2 text-stone-500 hover:bg-stone-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="space-y-4 px-5 py-4 text-sm">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Name</p>
                    <p class="mt-1 font-semibold text-stone-900" data-view="cat-name">—</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Parent</p>
                    <p class="mt-1 text-stone-800" data-view="cat-parent">—</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Slug</p>
                    <p class="mt-1 font-mono text-xs text-stone-700" data-view="cat-slug">—</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Posts</p>
                    <p class="mt-1 tabular-nums text-stone-800" data-view="cat-posts">—</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Accent color</p>
                    <div class="mt-2 flex items-center gap-3">
                        <span data-view="cat-color-swatch" class="h-9 w-9 shrink-0 rounded-lg border border-stone-200 bg-stone-300 shadow-inner shadow-stone-900/10" aria-hidden="true"></span>
                        <p class="font-mono text-xs text-stone-700" data-view="cat-color-hex">—</p>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Description</p>
                    <p class="mt-2 leading-relaxed text-stone-700" data-view="cat-description">—</p>
                </div>
            </div>
            <div class="flex justify-end border-t border-stone-100 px-5 py-4">
                <button type="button" data-modal-close class="<?= $btnPri ?>">Close</button>
            </div>
        </div>
    </div>
</div>
