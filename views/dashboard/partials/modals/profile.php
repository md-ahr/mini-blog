<?php
$fld = 'mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30';
$lbl = 'block text-xs font-semibold text-stone-700';
$btnPri = 'inline-flex items-center justify-center rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
$btnSec = 'inline-flex items-center justify-center rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
?>

<div id="modal-profile-add" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-profile-add-title" tabindex="-1"
             class="w-full max-w-md rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <div>
                    <h2 id="modal-profile-add-title" class="text-base font-semibold text-stone-900">Upload profile photo</h2>
                    <p class="mt-1 text-xs text-stone-500">PNG, JPEG, WebP, or GIF. Hook the field to your storage endpoint later.</p>
                </div>
                <button type="button" data-modal-close class="rounded-lg p-2 text-stone-500 hover:bg-stone-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <form class="space-y-4 px-5 py-4" action="#" method="get" enctype="multipart/form-data" onsubmit="return false;">
                <div>
                    <label class="<?= $lbl ?>" for="profile-avatar-input">Image</label>
                    <input id="profile-avatar-input" name="profile_photo" type="file" accept="image/png,image/jpeg,image/webp,image/gif,.png,.jpg,.jpeg,.webp,.gif"
                           class="mt-1.5 block w-full cursor-pointer text-sm text-stone-600 file:mr-4 file:cursor-pointer file:rounded-lg file:border-0 file:bg-stone-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-amber-50 hover:file:bg-stone-800"/>
                    <p class="mt-1.5 text-xs text-stone-500">Max size and cropping rules belong in server-side validation.</p>
                </div>
                <div class="relative flex min-h-[10rem] items-center justify-center overflow-hidden rounded-2xl border border-dashed border-stone-300 bg-stone-50/80">
                    <p id="profile-avatar-placeholder" class="px-4 text-center text-sm text-stone-500">Preview appears after you choose a file.</p>
                    <img id="profile-avatar-preview" src="" alt="" class="hidden max-h-48 w-auto object-contain"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="profile-photo-alt">Alt text</label>
                    <input id="profile-photo-alt" name="profile_photo_alt" type="text" class="<?= $fld ?>" placeholder="Describe the photo for accessibility"/>
                </div>
            </form>
            <div class="flex flex-col-reverse gap-2 border-t border-stone-100 px-5 py-4 sm:flex-row sm:justify-end">
                <button type="button" data-modal-close class="<?= $btnSec ?> w-full sm:w-auto">Cancel</button>
                <button type="button" class="<?= $btnPri ?> w-full sm:w-auto">Upload</button>
            </div>
        </div>
    </div>
</div>
<script>
    (function () {
        document.addEventListener('change', function (e) {
            if (!e.target || e.target.id !== 'profile-avatar-input') {
                return;
            }
            var input = e.target;
            var img = document.getElementById('profile-avatar-preview');
            var ph = document.getElementById('profile-avatar-placeholder');
            if (!img || !ph) {
                return;
            }
            var file = input.files && input.files[0];
            if (!file) {
                img.removeAttribute('src');
                img.classList.add('hidden');
                img.alt = '';
                ph.classList.remove('hidden');
                return;
            }
            var url = URL.createObjectURL(file);
            img.onload = function () {
                URL.revokeObjectURL(url);
            };
            img.src = url;
            img.alt = file.name || 'Preview';
            img.classList.remove('hidden');
            ph.classList.add('hidden');
        });
    })();
</script>

<div id="modal-profile-edit" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-profile-edit-title" tabindex="-1"
             class="max-h-[min(90vh,44rem)] w-full max-w-lg overflow-y-auto rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <h2 id="modal-profile-edit-title" class="text-base font-semibold text-stone-900">Edit profile</h2>
                <button type="button" data-modal-close class="rounded-lg p-2 text-stone-500 hover:bg-stone-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <form class="space-y-4 px-5 py-4" action="#" method="get" onsubmit="return false;">
                <div>
                    <label class="<?= $lbl ?>" for="profile-edit-name">Display name</label>
                    <input id="profile-edit-name" name="profile_name" type="text" class="<?= $fld ?>"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="profile-edit-email">Email</label>
                    <input id="profile-edit-email" name="profile_email" type="email" class="<?= $fld ?>"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="profile-edit-bio">Bio</label>
                    <textarea id="profile-edit-bio" name="profile_bio" rows="4" class="<?= $fld ?>"></textarea>
                </div>
            </form>
            <div class="flex flex-col-reverse gap-2 border-t border-stone-100 px-5 py-4 sm:flex-row sm:justify-end">
                <button type="button" data-modal-close class="<?= $btnSec ?> w-full sm:w-auto">Cancel</button>
                <button type="button" class="<?= $btnPri ?> w-full sm:w-auto">Save profile</button>
            </div>
        </div>
    </div>
</div>

<div id="modal-profile-view" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-profile-view-title" tabindex="-1"
             class="w-full max-w-md rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <h2 id="modal-profile-view-title" class="text-base font-semibold text-stone-900">Public profile</h2>
                <button type="button" data-modal-close class="rounded-lg p-2 text-stone-500 hover:bg-stone-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="space-y-4 px-5 py-4 text-sm">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Name</p>
                    <p class="mt-1 font-semibold text-stone-900" data-view="profile-name">—</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Email</p>
                    <p class="mt-1 text-stone-700" data-view="profile-email">—</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Role</p>
                    <p class="mt-1 text-stone-800" data-view="profile-role">—</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Bio</p>
                    <p class="mt-2 leading-relaxed text-stone-700" data-view="profile-bio">—</p>
                </div>
            </div>
            <div class="flex justify-end border-t border-stone-100 px-5 py-4">
                <button type="button" data-modal-close class="<?= $btnPri ?>">Close</button>
            </div>
        </div>
    </div>
</div>
