<?php
$fld = 'mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30';
$lbl = 'block text-xs font-semibold text-stone-700';
$btnPri = 'inline-flex items-center justify-center rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
$btnSec = 'inline-flex items-center justify-center rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white';

$postsUrl = $postsUrl ?? blog_url('dashboard/posts');
$csrfToken = $csrfToken ?? auth_csrf_token();
$users = $users ?? [];
$categories = $categories ?? [];
$currentUserId = (int) ($currentUserId ?? 0);
$canManageAllPosts = $canManageAllPosts ?? true;
$h = static fn (string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
?>

<div id="modal-post-add" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-post-add-title" tabindex="-1"
             class="max-h-[min(90vh,44rem)] w-full max-w-lg overflow-y-auto rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <div>
                    <h2 id="modal-post-add-title" class="text-base font-semibold text-stone-900">New post</h2>
                    <p class="mt-1 text-xs text-stone-500">Drafts stay private until you publish with a date.</p>
                </div>
                <button type="button" data-modal-close
                        class="rounded-lg p-2 text-stone-500 transition hover:bg-stone-100 hover:text-stone-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                        aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <form class="space-y-4 px-5 py-4" method="post" action="<?= $h($postsUrl) ?>" enctype="multipart/form-data">
                <input type="hidden" name="_csrf" value="<?= $h($csrfToken) ?>"/>
                <input type="hidden" name="_action" value="create"/>
                <div>
                    <label class="<?= $lbl ?>" for="post-add-title">Title</label>
                    <input id="post-add-title" name="title" type="text" required maxlength="500" autocomplete="off" class="<?= $fld ?>" placeholder="Working title"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="post-add-slug">Slug <span class="font-normal text-stone-500">(optional)</span></label>
                    <input id="post-add-slug" name="slug" type="text" maxlength="191" autocomplete="off" class="<?= $fld ?> font-mono text-xs" placeholder="url-friendly-slug"/>
                </div>
                <div class="grid gap-4 <?= $canManageAllPosts ? 'sm:grid-cols-2' : '' ?>">
                    <div>
                        <label class="<?= $lbl ?>" for="post-add-status">Status</label>
                        <select id="post-add-status" name="status" class="<?= htmlspecialchars($dashboardSelect, ENT_QUOTES, 'UTF-8') ?> mt-1.5">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="scheduled">Scheduled</option>
                        </select>
                    </div>
                    <?php if ($canManageAllPosts) : ?>
                    <div>
                        <label class="<?= $lbl ?>" for="post-add-author">Author</label>
                        <select id="post-add-author" name="user_id" required class="<?= htmlspecialchars($dashboardSelect, ENT_QUOTES, 'UTF-8') ?> mt-1.5">
                            <?php foreach ($users as $u) : ?>
                                <?php $uid = (int) ($u['id'] ?? 0); ?>
                                <option value="<?= $uid ?>" <?= $uid === $currentUserId ? 'selected' : '' ?>><?= $h((string) ($u['name'] ?? '')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php else : ?>
                    <input type="hidden" name="user_id" value="<?= (int) $currentUserId ?>"/>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="post-add-category">Category</label>
                    <select id="post-add-category" name="category_id" class="<?= htmlspecialchars($dashboardSelect, ENT_QUOTES, 'UTF-8') ?> mt-1.5">
                        <option value="">Uncategorized</option>
                        <?php foreach ($categories as $c) : ?>
                            <option value="<?= (int) ($c['id'] ?? 0) ?>"><?= $h((string) ($c['name'] ?? '')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="post-add-tags">Tags</label>
                    <input id="post-add-tags" name="tags" type="text" class="<?= $fld ?>" placeholder="essay, field-notes (comma-separated)" autocomplete="off"/>
                    <p class="mt-1 text-xs text-stone-500">New names create tags automatically.</p>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="post-add-excerpt">Excerpt</label>
                    <textarea id="post-add-excerpt" name="excerpt" rows="3" class="<?= $fld ?>" placeholder="Summary for listings and SEO."></textarea>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="post-add-body">Body</label>
                    <textarea id="post-add-body" name="content_body" rows="8" class="<?= $fld ?>" placeholder="Paragraphs separated by a blank line."></textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="<?= $lbl ?>" for="post-add-reading">Reading minutes</label>
                        <input id="post-add-reading" name="reading_minutes" type="number" min="0" max="32767" class="<?= $fld ?> tabular-nums" placeholder="Optional"/>
                    </div>
                    <div>
                        <label class="<?= $lbl ?>" for="post-add-published">Publish date</label>
                        <input id="post-add-published" name="published_at" type="date" class="<?= $fld ?> tabular-nums"/>
                        <p class="mt-1 text-xs text-stone-500">Used when status is Published.</p>
                    </div>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="post-add-scheduled">Scheduled for</label>
                    <input id="post-add-scheduled" name="scheduled_at" type="datetime-local" class="<?= $fld ?> tabular-nums"/>
                    <p class="mt-1 text-xs text-stone-500">Optional; stored when status is Scheduled (public listing still uses Published + date).</p>
                </div>
                <div class="rounded-xl border border-stone-200 bg-stone-50/80 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Featured image</p>
                    <p class="mt-1 text-xs text-stone-500">Upload (max <?= (int) (BLOG_FEATURED_IMAGE_MAX_BYTES / 1024 / 1024) ?> MB) or paste a URL.</p>
                    <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-start">
                        <div class="relative h-28 w-full overflow-hidden rounded-lg border border-stone-200 bg-stone-100 sm:w-40">
                            <img id="post-add-image-preview" src="" alt="" class="hidden h-full w-full object-cover"/>
                            <div id="post-add-image-placeholder" class="flex h-full w-full items-center justify-center text-xs text-stone-400">No image</div>
                        </div>
                        <div class="min-w-0 flex-1 space-y-3">
                            <div>
                                <label class="<?= $lbl ?>" for="post-add-image-file">Upload</label>
                                <input id="post-add-image-file" name="featured_file" type="file" accept="image/png,image/jpeg,image/webp,image/gif,.png,.jpg,.jpeg,.webp,.gif"
                                       class="mt-1.5 block w-full text-sm text-stone-600 file:mr-3 file:rounded-lg file:border-0 file:bg-stone-900 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-amber-50 hover:file:bg-stone-800"/>
                            </div>
                            <div>
                                <label class="<?= $lbl ?>" for="post-add-image-url">Image URL</label>
                                <input id="post-add-image-url" name="featured_image_url" type="url" maxlength="500" class="<?= $fld ?>" placeholder="https://…"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col-reverse gap-2 border-t border-stone-100 pt-4 sm:flex-row sm:justify-end">
                    <button type="button" data-modal-close class="<?= $btnSec ?> w-full sm:w-auto">Cancel</button>
                    <button type="submit" class="<?= $btnPri ?> w-full sm:w-auto">Create post</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modal-post-edit" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-post-edit-title" tabindex="-1"
             class="max-h-[min(90vh,44rem)] w-full max-w-lg overflow-y-auto rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <div>
                    <h2 id="modal-post-edit-title" class="text-base font-semibold text-stone-900">Edit post</h2>
                    <p class="mt-1 text-xs text-stone-500">Saving updates the live post if it is published.</p>
                </div>
                <button type="button" data-modal-close
                        class="rounded-lg p-2 text-stone-500 transition hover:bg-stone-100 hover:text-stone-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                        aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <form class="space-y-4 px-5 py-4" method="post" action="<?= $h($postsUrl) ?>" enctype="multipart/form-data">
                <input type="hidden" name="_csrf" value="<?= $h($csrfToken) ?>"/>
                <input type="hidden" name="_action" value="update"/>
                <input type="hidden" name="id" value=""/>
                <div>
                    <label class="<?= $lbl ?>" for="post-edit-title">Title</label>
                    <input id="post-edit-title" name="title" type="text" required maxlength="500" autocomplete="off" class="<?= $fld ?>"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="post-edit-slug">Slug</label>
                    <input id="post-edit-slug" name="slug" type="text" maxlength="191" autocomplete="off" class="<?= $fld ?> font-mono text-xs"/>
                </div>
                <div class="grid gap-4 <?= $canManageAllPosts ? 'sm:grid-cols-2' : '' ?>">
                    <div>
                        <label class="<?= $lbl ?>" for="post-edit-status">Status</label>
                        <select id="post-edit-status" name="status" class="<?= htmlspecialchars($dashboardSelect, ENT_QUOTES, 'UTF-8') ?> mt-1.5">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="scheduled">Scheduled</option>
                        </select>
                    </div>
                    <?php if ($canManageAllPosts) : ?>
                    <div>
                        <label class="<?= $lbl ?>" for="post-edit-author">Author</label>
                        <select id="post-edit-author" name="user_id" required class="<?= htmlspecialchars($dashboardSelect, ENT_QUOTES, 'UTF-8') ?> mt-1.5">
                            <?php foreach ($users as $u) : ?>
                                <option value="<?= (int) ($u['id'] ?? 0) ?>"><?= $h((string) ($u['name'] ?? '')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php else : ?>
                    <input type="hidden" name="user_id" value="<?= (int) $currentUserId ?>"/>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="post-edit-category">Category</label>
                    <select id="post-edit-category" name="category_id" class="<?= htmlspecialchars($dashboardSelect, ENT_QUOTES, 'UTF-8') ?> mt-1.5">
                        <option value="">Uncategorized</option>
                        <?php foreach ($categories as $c) : ?>
                            <option value="<?= (int) ($c['id'] ?? 0) ?>"><?= $h((string) ($c['name'] ?? '')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="post-edit-tags">Tags</label>
                    <input id="post-edit-tags" name="tags" type="text" class="<?= $fld ?>" autocomplete="off"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="post-edit-excerpt">Excerpt</label>
                    <textarea id="post-edit-excerpt" name="excerpt" rows="3" class="<?= $fld ?>"></textarea>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="post-edit-body">Body</label>
                    <textarea id="post-edit-body" name="content_body" rows="8" class="<?= $fld ?>"></textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="<?= $lbl ?>" for="post-edit-reading">Reading minutes</label>
                        <input id="post-edit-reading" name="reading_minutes" type="number" min="0" max="32767" class="<?= $fld ?> tabular-nums"/>
                    </div>
                    <div>
                        <label class="<?= $lbl ?>" for="post-edit-published">Publish date</label>
                        <input id="post-edit-published" name="published_at" type="date" class="<?= $fld ?> tabular-nums"/>
                    </div>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="post-edit-scheduled">Scheduled for</label>
                    <input id="post-edit-scheduled" name="scheduled_at" type="datetime-local" class="<?= $fld ?> tabular-nums"/>
                </div>
                <div class="rounded-xl border border-stone-200 bg-stone-50/80 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Featured image</p>
                    <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-start">
                        <div class="relative h-28 w-full overflow-hidden rounded-lg border border-stone-200 bg-stone-100 sm:w-40">
                            <img id="post-edit-image-preview" src="" alt="" class="hidden h-full w-full object-cover"/>
                            <div id="post-edit-image-placeholder" class="flex h-full w-full items-center justify-center text-xs text-stone-400">No image</div>
                        </div>
                        <div class="min-w-0 flex-1 space-y-3">
                            <div>
                                <label class="<?= $lbl ?>" for="post-edit-image-file">Upload</label>
                                <input id="post-edit-image-file" name="featured_file" type="file" accept="image/png,image/jpeg,image/webp,image/gif,.png,.jpg,.jpeg,.webp,.gif"
                                       class="mt-1.5 block w-full text-sm text-stone-600 file:mr-3 file:rounded-lg file:border-0 file:bg-stone-900 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-amber-50 hover:file:bg-stone-800"/>
                            </div>
                            <div>
                                <label class="<?= $lbl ?>" for="post-edit-image-url">Image URL</label>
                                <input id="post-edit-image-url" name="featured_image_url" type="url" maxlength="500" class="<?= $fld ?>" placeholder="https://…"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col-reverse gap-2 border-t border-stone-100 pt-4 sm:flex-row sm:justify-end">
                    <button type="button" data-modal-close class="<?= $btnSec ?> w-full sm:w-auto">Cancel</button>
                    <button type="submit" class="<?= $btnPri ?> w-full sm:w-auto">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modal-post-view" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-post-view-title" tabindex="-1"
             class="w-full max-w-lg rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <div>
                    <h2 id="modal-post-view-title" class="text-base font-semibold text-stone-900">View post</h2>
                    <p class="mt-1 text-xs text-stone-500">Read-only summary</p>
                </div>
                <button type="button" data-modal-close
                        class="rounded-lg p-2 text-stone-500 transition hover:bg-stone-100 hover:text-stone-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                        aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="space-y-4 px-5 py-4 text-sm">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Title</p>
                    <p class="mt-1 font-semibold text-stone-900" data-view="post-title">—</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Slug</p>
                    <p class="mt-1 font-mono text-xs text-stone-700" data-view="post-slug">—</p>
                </div>
                <div data-view="post-image-wrap" class="hidden overflow-hidden rounded-xl border border-stone-200 bg-stone-100">
                    <img data-view="post-image" src="" alt="" class="max-h-56 w-full object-cover"/>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Status</p>
                        <p class="mt-1 text-stone-800" data-view="post-status">—</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Updated</p>
                        <p class="mt-1 tabular-nums text-stone-800" data-view="post-updated">—</p>
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Author</p>
                        <p class="mt-1 text-stone-800" data-view="post-author">—</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Category</p>
                        <p class="mt-1 text-stone-800" data-view="post-category">—</p>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Tags</p>
                    <p class="mt-1 text-stone-800" data-view="post-tag">—</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Excerpt</p>
                    <p class="mt-2 leading-relaxed text-stone-700" data-view="post-excerpt">—</p>
                </div>
            </div>
            <div class="flex justify-end border-t border-stone-100 px-5 py-4">
                <button type="button" data-modal-close class="<?= $btnPri ?>">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    function wirePostImage(fileId, urlId, previewId, placeholderId) {
        var file = document.getElementById(fileId);
        var url = document.getElementById(urlId);
        var preview = document.getElementById(previewId);
        var placeholder = document.getElementById(placeholderId);
        if (!file || !url || !preview || !placeholder) {
            return;
        }
        function showPreview(src) {
            if (src) {
                preview.src = src;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            } else {
                preview.removeAttribute('src');
                preview.classList.add('hidden');
                placeholder.classList.remove('hidden');
            }
        }
        file.addEventListener('change', function () {
            var f = file.files && file.files[0];
            if (!f) {
                return;
            }
            var r = new FileReader();
            r.onload = function () {
                showPreview(r.result);
            };
            r.readAsDataURL(f);
        });
        url.addEventListener('input', function () {
            var v = url.value.trim();
            if (v && (!file.files || !file.files.length)) {
                showPreview(v);
            }
            if (!v && (!file.files || !file.files.length)) {
                showPreview('');
            }
        });
    }
    wirePostImage('post-add-image-file', 'post-add-image-url', 'post-add-image-preview', 'post-add-image-placeholder');
    wirePostImage('post-edit-image-file', 'post-edit-image-url', 'post-edit-image-preview', 'post-edit-image-placeholder');
})();
</script>
