<?php
/**
 * Global confirm dialog + dashboard modal behavior (open/close, ESC, backdrop, scroll lock).
 * Expects modal shells to use data-dashboard-modal on the outer fixed wrapper.
 */
?>
<div id="dashboard-confirm-modal"
     data-dashboard-modal
     class="fixed inset-0 z-[120] hidden"
     aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog"
             aria-modal="true"
             aria-labelledby="dashboard-confirm-title"
             tabindex="-1"
             class="w-full max-w-md rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="border-b border-stone-100 px-5 py-4">
                <h2 id="dashboard-confirm-title" class="text-base font-semibold text-stone-900">Confirm</h2>
            </div>
            <div class="px-5 py-4">
                <div id="dashboard-confirm-body" class="text-sm leading-relaxed text-stone-600"></div>
            </div>
            <div class="flex flex-col-reverse gap-2 border-t border-stone-100 px-5 py-4 sm:flex-row sm:justify-end">
                <button type="button"
                        data-modal-close
                        class="inline-flex w-full items-center justify-center rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white sm:w-auto">
                    Cancel
                </button>
                <button type="button"
                        id="dashboard-confirm-action"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-red-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white sm:w-auto">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var openCount = 0;
        var lastFocus = null;
        var lastConfirmTrigger = null;

        function modalRoot(el) {
            return el && el.closest ? el.closest('[data-dashboard-modal]') : null;
        }

        function isHidden(modal) {
            return !modal || modal.classList.contains('hidden');
        }

        function openModal(modal) {
            if (!modal || !isHidden(modal)) {
                return;
            }
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            openCount += 1;
            document.documentElement.classList.add('overflow-hidden');
            lastFocus = document.activeElement;
            var dialog = modal.querySelector('[role="dialog"]');
            if (dialog) {
                dialog.focus();
            }
        }

        function closeModal(modal) {
            if (!modal || isHidden(modal)) {
                return;
            }
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
            if (modal.id === 'dashboard-confirm-modal') {
                lastConfirmTrigger = null;
            }
            openCount = Math.max(0, openCount - 1);
            if (openCount === 0) {
                document.documentElement.classList.remove('overflow-hidden');
            }
            if (lastFocus && typeof lastFocus.focus === 'function') {
                lastFocus.focus();
            }
        }

        function textOr(el, attr, fallback) {
            var v = el.getAttribute(attr);
            return v !== null && v !== '' ? v : (fallback || '');
        }

        function populateConfirm(trigger) {
            var title = textOr(trigger, 'data-confirm-title', 'Are you sure?');
            var body = textOr(trigger, 'data-confirm-body', 'This action cannot be undone.');
            var label = textOr(trigger, 'data-confirm-label', 'Confirm');
            var variant = textOr(trigger, 'data-confirm-variant', 'danger');

            var titleEl = document.getElementById('dashboard-confirm-title');
            var bodyEl = document.getElementById('dashboard-confirm-body');
            var actionEl = document.getElementById('dashboard-confirm-action');
            if (titleEl) {
                titleEl.textContent = title;
            }
            if (bodyEl) {
                bodyEl.textContent = body;
            }
            if (actionEl) {
                actionEl.textContent = label;
                actionEl.classList.remove('bg-red-700', 'hover:bg-red-800', 'bg-stone-900', 'hover:bg-stone-800');
                if (variant === 'primary') {
                    actionEl.classList.add('bg-stone-900', 'hover:bg-stone-800');
                } else {
                    actionEl.classList.add('bg-red-700', 'hover:bg-red-800');
                }
            }
        }

        function setVal(modal, selector, value) {
            var el = modal.querySelector(selector);
            if (!el) {
                return;
            }
            el.value = value != null ? value : '';
        }

        function setText(modal, selector, value) {
            var el = modal.querySelector(selector);
            if (!el) {
                return;
            }
            el.textContent = value != null ? value : '';
        }

        function setPostFeaturedImage(modal, url) {
            var wrap = modal.querySelector('[data-view="post-image-wrap"]');
            var img = modal.querySelector('[data-view="post-image"]');
            if (!wrap || !img) {
                return;
            }
            var u = url != null ? String(url).trim() : '';
            if (u !== '') {
                img.src = u;
                img.alt = '';
                wrap.classList.remove('hidden');
            } else {
                img.removeAttribute('src');
                wrap.classList.add('hidden');
            }
        }

        function paintSwatch(el, hex) {
            if (!el) {
                return;
            }
            var h = hex != null && String(hex).trim() !== '' ? String(hex).trim() : '#78716c';
            el.style.backgroundColor = h;
        }

        function prefillModal(trigger, id) {
            var modal = document.getElementById(id);
            if (!modal) {
                return;
            }

            if (id === 'modal-post-edit') {
                var editPayloadId = trigger.getAttribute('data-post-payload');
                var editScript = editPayloadId ? document.getElementById(editPayloadId) : null;
                if (editScript && editScript.textContent) {
                    try {
                        var ed = JSON.parse(editScript.textContent);
                        setVal(modal, '[name="id"]', ed.id != null ? String(ed.id) : '');
                        setVal(modal, '[name="title"]', ed.title || '');
                        setVal(modal, '[name="slug"]', ed.slug || '');
                        setVal(modal, '[name="status"]', ed.status || 'draft');
                        setVal(modal, '[name="user_id"]', ed.user_id != null ? String(ed.user_id) : '');
                        setVal(modal, '[name="category_id"]', ed.category_id != null && ed.category_id !== '' ? String(ed.category_id) : '');
                        setVal(modal, '[name="tags"]', ed.tags || '');
                        setVal(modal, '[name="excerpt"]', ed.excerpt || '');
                        setVal(modal, '[name="content_body"]', ed.content_body || '');
                        setVal(modal, '[name="reading_minutes"]', ed.reading_minutes != null && ed.reading_minutes !== '' ? String(ed.reading_minutes) : '');
                        setVal(modal, '[name="published_at"]', ed.published_at || '');
                        setVal(modal, '[name="scheduled_at"]', ed.scheduled_at || '');
                        var urlIn = modal.querySelector('#post-edit-image-url');
                        if (urlIn) {
                            urlIn.value = ed.featured_image_url || '';
                        }
                        var f = modal.querySelector('#post-edit-image-file');
                        if (f) {
                            f.value = '';
                        }
                        var pv = modal.querySelector('#post-edit-image-preview');
                        var ph = modal.querySelector('#post-edit-image-placeholder');
                        var u = ed.featured_image_url || '';
                        if (pv && ph) {
                            if (u) {
                                pv.src = u;
                                pv.classList.remove('hidden');
                                ph.classList.add('hidden');
                            } else {
                                pv.removeAttribute('src');
                                pv.classList.add('hidden');
                                ph.classList.remove('hidden');
                            }
                        }
                    } catch (err) {
                    }
                }
            }

            if (id === 'modal-post-view') {
                var viewPayloadId = trigger.getAttribute('data-post-payload');
                var viewScript = viewPayloadId ? document.getElementById(viewPayloadId) : null;
                if (viewScript && viewScript.textContent) {
                    try {
                        var vd = JSON.parse(viewScript.textContent);
                        setText(modal, '[data-view="post-title"]', vd.title || '—');
                        setText(modal, '[data-view="post-status"]', vd.status_label || '—');
                        setText(modal, '[data-view="post-author"]', vd.author_name || '—');
                        setText(modal, '[data-view="post-tag"]', vd.tags || '—');
                        setText(modal, '[data-view="post-category"]', vd.category_name || '—');
                        setText(modal, '[data-view="post-updated"]', vd.updated || '—');
                        setText(modal, '[data-view="post-excerpt"]', vd.excerpt || '—');
                        setText(modal, '[data-view="post-slug"]', vd.slug || '—');
                        setPostFeaturedImage(modal, vd.featured_image_url || '');
                    } catch (err2) {
                    }
                }
            }

            if (id === 'modal-tag-edit') {
                setVal(modal, '[name="id"]', trigger.getAttribute('data-tag-id'));
                setVal(modal, '[name="name"]', trigger.getAttribute('data-tag-name'));
                setVal(modal, '[name="slug"]', trigger.getAttribute('data-tag-slug'));
                setVal(modal, '[name="color"]', trigger.getAttribute('data-tag-color'));
                var tagPostsRo = modal.querySelector('#tag-edit-posts-readonly');
                if (tagPostsRo) {
                    tagPostsRo.textContent = trigger.getAttribute('data-tag-posts') || '0';
                }
            }

            if (id === 'modal-tag-view') {
                setText(modal, '[data-view="tag-name"]', trigger.getAttribute('data-tag-name'));
                setText(modal, '[data-view="tag-slug"]', trigger.getAttribute('data-tag-slug'));
                setText(modal, '[data-view="tag-posts"]', trigger.getAttribute('data-tag-posts'));
                var th = trigger.getAttribute('data-tag-color');
                setText(modal, '[data-view="tag-color-hex"]', th || '—');
                paintSwatch(modal.querySelector('[data-view="tag-color-swatch"]'), th);
            }

            if (id === 'modal-category-edit') {
                var catId = trigger.getAttribute('data-cat-id');
                setVal(modal, '[name="id"]', catId);
                setVal(modal, '[name="name"]', trigger.getAttribute('data-cat-name'));
                setVal(modal, '[name="slug"]', trigger.getAttribute('data-cat-slug'));
                setVal(modal, '[name="description"]', trigger.getAttribute('data-cat-description'));
                setVal(modal, '[name="color"]', trigger.getAttribute('data-cat-color'));
                setVal(modal, '[name="sort_order"]', trigger.getAttribute('data-cat-sort'));
                var catPar = modal.querySelector('#cat-edit-parent');
                if (catPar) {
                    Array.prototype.forEach.call(catPar.options, function (o) {
                        o.disabled = false;
                    });
                    setVal(modal, '[name="parent_id"]', trigger.getAttribute('data-cat-parent-id'));
                    Array.prototype.forEach.call(catPar.options, function (o) {
                        if (catId && o.value === String(catId)) {
                            o.disabled = true;
                        }
                    });
                }
                var catPostsRo = modal.querySelector('#cat-edit-posts-readonly');
                if (catPostsRo) {
                    catPostsRo.textContent = trigger.getAttribute('data-cat-posts') || '0';
                }
            }

            if (id === 'modal-category-view') {
                setText(modal, '[data-view="cat-name"]', trigger.getAttribute('data-cat-name'));
                var cpar = trigger.getAttribute('data-cat-parent');
                setText(modal, '[data-view="cat-parent"]', cpar && cpar.trim() !== '' ? cpar : 'Top level');
                setText(modal, '[data-view="cat-slug"]', trigger.getAttribute('data-cat-slug'));
                setText(modal, '[data-view="cat-posts"]', trigger.getAttribute('data-cat-posts'));
                setText(modal, '[data-view="cat-description"]', trigger.getAttribute('data-cat-description'));
                var ch = trigger.getAttribute('data-cat-color');
                setText(modal, '[data-view="cat-color-hex"]', ch || '—');
                paintSwatch(modal.querySelector('[data-view="cat-color-swatch"]'), ch);
            }

            if (id === 'modal-comment-edit') {
                var payloadCm = trigger.getAttribute('data-comment-payload');
                if (payloadCm) {
                    try {
                        var cd = JSON.parse(payloadCm);
                        setVal(modal, '[name="id"]', cd.id != null ? String(cd.id) : '');
                        var postTitleRo = modal.querySelector('[data-readonly-post-title]');
                        if (postTitleRo) {
                            postTitleRo.textContent = cd.post_title || '—';
                        }
                        setVal(modal, '[name="author_name"]', cd.author_name || '');
                        setVal(modal, '[name="author_email"]', cd.author_email || '');
                        setVal(modal, '[name="body"]', cd.body || '');
                        setVal(modal, '[name="status"]', cd.status || 'pending');
                    } catch (errCm) {
                    }
                }
            }

            if (id === 'modal-comment-view') {
                var payloadCv = trigger.getAttribute('data-comment-payload');
                if (payloadCv) {
                    try {
                        var cv = JSON.parse(payloadCv);
                        var stRaw = cv.status || '';
                        var stLabel = stRaw === 'pending' ? 'Pending' : stRaw === 'approved' ? 'Approved' : stRaw === 'spam' ? 'Spam' : stRaw === 'rejected' ? 'Rejected' : stRaw;
                        setText(modal, '[data-view="comment-author"]', cv.author_name || '—');
                        setText(modal, '[data-view="comment-email"]', cv.author_email || '—');
                        setText(modal, '[data-view="comment-post"]', cv.post_title || '—');
                        setText(modal, '[data-view="comment-state"]', stLabel || '—');
                        setText(modal, '[data-view="comment-body"]', cv.body || '—');
                    } catch (errCv) {
                    }
                }
            }

            if (id === 'modal-user-edit') {
                setVal(modal, '[name="id"]', trigger.getAttribute('data-user-id'));
                setVal(modal, '[name="user_name"]', trigger.getAttribute('data-user-name'));
                setVal(modal, '[name="user_email"]', trigger.getAttribute('data-user-email'));
                setVal(modal, '[name="user_role"]', trigger.getAttribute('data-user-role'));
                var uss = trigger.getAttribute('data-user-status-code');
                setVal(modal, '[name="user_status"]', uss || trigger.getAttribute('data-user-status'));
            }

            if (id === 'modal-user-view') {
                setText(modal, '[data-view="user-name"]', trigger.getAttribute('data-user-name'));
                setText(modal, '[data-view="user-email"]', trigger.getAttribute('data-user-email'));
                setText(modal, '[data-view="user-role"]', trigger.getAttribute('data-user-role'));
                setText(modal, '[data-view="user-last"]', trigger.getAttribute('data-user-last'));
                setText(modal, '[data-view="user-status"]', trigger.getAttribute('data-user-status'));
            }

            if (id === 'modal-setting-view') {
                setText(modal, '[data-view="setting-site-title"]', trigger.getAttribute('data-site-title'));
                setText(modal, '[data-view="setting-tagline"]', trigger.getAttribute('data-tagline'));
                setText(modal, '[data-view="setting-posts-per-page"]', trigger.getAttribute('data-posts-per-page'));
                setText(modal, '[data-view="setting-date-format"]', trigger.getAttribute('data-date-format'));
                setText(modal, '[data-view="setting-rss"]', trigger.getAttribute('data-rss'));
            }

            if (id === 'modal-activity-view') {
                setText(modal, '[data-view="activity-title"]', trigger.getAttribute('data-activity-title'));
                setText(modal, '[data-view="activity-meta"]', trigger.getAttribute('data-activity-meta'));
                setText(modal, '[data-view="activity-badge"]', trigger.getAttribute('data-activity-badge'));
            }

            if (id === 'modal-activity-edit') {
                setVal(modal, '[name="activity_title"]', trigger.getAttribute('data-activity-title'));
                setVal(modal, '[name="activity_meta"]', trigger.getAttribute('data-activity-meta'));
                setVal(modal, '[name="activity_badge"]', trigger.getAttribute('data-activity-badge'));
            }

            if (id === 'modal-stat-view') {
                setText(modal, '[data-view="stat-label"]', trigger.getAttribute('data-stat-label'));
                setText(modal, '[data-view="stat-value"]', trigger.getAttribute('data-stat-value'));
                setText(modal, '[data-view="stat-hint"]', trigger.getAttribute('data-stat-hint'));
            }
        }

        document.addEventListener('click', function (e) {
            if (e.target && e.target.id === 'dashboard-confirm-action') {
                e.preventDefault();
                var confirmModal = document.getElementById('dashboard-confirm-modal');
                var submitFormId = lastConfirmTrigger ? lastConfirmTrigger.getAttribute('data-confirm-submit-form') : null;
                var redirect = lastConfirmTrigger ? lastConfirmTrigger.getAttribute('data-confirm-redirect') : null;
                closeModal(confirmModal);
                lastConfirmTrigger = null;
                if (submitFormId) {
                    var f = document.getElementById(submitFormId);
                    if (f) {
                        f.submit();
                    }
                    return;
                }
                if (redirect) {
                    window.location.href = redirect;
                }
                return;
            }

            var openBtn = e.target.closest('[data-modal-open]');
            if (openBtn) {
                e.preventDefault();
                var id = openBtn.getAttribute('data-modal-open');
                if (!id) {
                    return;
                }
                if (id === 'dashboard-confirm-modal') {
                    lastConfirmTrigger = openBtn;
                    populateConfirm(openBtn);
                } else {
                    prefillModal(openBtn, id);
                }
                if (id === 'modal-category-add') {
                    var addPar = document.getElementById('cat-add-parent');
                    if (addPar) {
                        Array.prototype.forEach.call(addPar.options, function (o) {
                            o.disabled = false;
                        });
                    }
                }
                var modal = document.getElementById(id);
                if (!modal) {
                    return;
                }
                openModal(modal);
                return;
            }

            var closeBtn = e.target.closest('[data-modal-close]');
            if (closeBtn) {
                var modalToClose = modalRoot(closeBtn);
                if (modalToClose) {
                    closeModal(modalToClose);
                }
                return;
            }

            if (e.target && e.target.hasAttribute && e.target.hasAttribute('data-modal-backdrop')) {
                var m = modalRoot(e.target);
                if (m) {
                    closeModal(m);
                }
            }
        }, true);

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') {
                return;
            }
            var stack = Array.prototype.slice.call(document.querySelectorAll('[data-dashboard-modal]:not(.hidden)'));
            if (!stack.length) {
                return;
            }
            closeModal(stack[stack.length - 1]);
        });
    })();
</script>
