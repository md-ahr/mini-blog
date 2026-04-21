<?php
$fld = 'mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30';
$lbl = 'block text-xs font-semibold text-stone-700';
$btnPri = 'inline-flex items-center justify-center rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
$btnSec = 'inline-flex items-center justify-center rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white';

$profile = $profile ?? [];
$csrfToken = $csrfToken ?? auth_csrf_token();
$profileUrl = blog_url('dashboard/profile');
$h = static fn (string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
?>

<div id="modal-profile-add" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-profile-add-title" tabindex="-1"
             class="w-full max-w-md rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <div>
                    <h2 id="modal-profile-add-title" class="text-base font-semibold text-stone-900">Upload profile photo</h2>
                    <p class="mt-1 text-xs text-stone-500">JPEG, PNG, WebP, or GIF, up to <?= (int) (PROFILE_AVATAR_MAX_BYTES / 1024 / 1024) ?> MB. Or paste a hosted URL in <span class="font-medium text-stone-700">Profile photo URL</span> on the Account form.</p>
                </div>
                <button type="button" data-modal-close class="rounded-lg p-2 text-stone-500 hover:bg-stone-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="modal-profile-upload-form" class="space-y-4 px-5 py-4" method="post" action="<?= $h($profileUrl) ?>" enctype="multipart/form-data">
                <input type="hidden" name="_csrf" value="<?= $h($csrfToken) ?>"/>
                <input type="hidden" name="_action" value="avatar_upload"/>
                <div>
                    <label class="<?= $lbl ?>" for="profile-avatar-input">Image</label>
                    <input id="profile-avatar-input" name="profile_photo" type="file" required accept="image/png,image/jpeg,image/webp,image/gif,.png,.jpg,.jpeg,.webp,.gif"
                           class="mt-1.5 block w-full cursor-pointer text-sm text-stone-600 file:mr-4 file:cursor-pointer file:rounded-lg file:border-0 file:bg-stone-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-amber-50 hover:file:bg-stone-800"/>
                </div>
                <div class="relative flex min-h-[10rem] items-center justify-center overflow-hidden rounded-2xl border border-dashed border-stone-300 bg-stone-50/80">
                    <?php if (!empty($profile['avatar_url'])) : ?>
                        <img id="profile-avatar-existing" src="<?= $h((string) $profile['avatar_url']) ?>" alt="" class="max-h-48 w-auto object-contain"/>
                        <p id="profile-avatar-placeholder" class="hidden px-4 text-center text-sm text-stone-500">Preview appears after you choose a file.</p>
                    <?php else : ?>
                        <p id="profile-avatar-placeholder" class="px-4 text-center text-sm text-stone-500">Preview appears after you choose a file.</p>
                    <?php endif; ?>
                    <img id="profile-avatar-preview" src="" alt="" class="hidden max-h-48 w-auto object-contain"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="profile-photo-alt">Alt text</label>
                    <input id="profile-photo-alt" name="profile_photo_alt" type="text" maxlength="191" class="<?= $fld ?>" value="<?= $h((string) ($profile['avatar_alt'] ?? '')) ?>" placeholder="Describe the photo for accessibility"/>
                </div>
                <div class="flex flex-col-reverse gap-2 border-t border-stone-100 pt-4 sm:flex-row sm:justify-end">
                    <button type="button" data-modal-close class="<?= $btnSec ?> w-full sm:w-auto">Cancel</button>
                    <button type="submit" class="<?= $btnPri ?> w-full sm:w-auto">Save photo</button>
                </div>
            </form>
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
            var existing = document.getElementById('profile-avatar-existing');
            if (!img || !ph) {
                return;
            }
            var file = input.files && input.files[0];
            if (!file) {
                img.removeAttribute('src');
                img.classList.add('hidden');
                img.alt = '';
                if (existing) {
                    existing.classList.remove('hidden');
                } else {
                    ph.classList.remove('hidden');
                }
                return;
            }
            if (existing) {
                existing.classList.add('hidden');
            }
            ph.classList.add('hidden');
            var url = URL.createObjectURL(file);
            img.onload = function () {
                URL.revokeObjectURL(url);
            };
            img.src = url;
            img.alt = file.name || 'Preview';
            img.classList.remove('hidden');
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
            <form class="space-y-4 px-5 py-4" method="post" action="<?= $h($profileUrl) ?>">
                <input type="hidden" name="_csrf" value="<?= $h($csrfToken) ?>"/>
                <input type="hidden" name="_action" value="account"/>
                <div>
                    <label class="<?= $lbl ?>" for="profile-edit-name">Display name</label>
                    <input id="profile-edit-name" name="name" type="text" required maxlength="191" class="<?= $fld ?>" value="<?= $h((string) ($profile['name'] ?? '')) ?>"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="profile-edit-email">Email</label>
                    <input id="profile-edit-email" name="email" type="email" required maxlength="191" class="<?= $fld ?>" value="<?= $h((string) ($profile['email'] ?? '')) ?>"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="profile-edit-avatar-url">Photo URL</label>
                    <input id="profile-edit-avatar-url" name="avatar_url" type="url" maxlength="500" class="<?= $fld ?>" value="<?= $h((string) ($profile['avatar_url'] ?? '')) ?>" placeholder="https://…"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="profile-edit-avatar-alt">Photo alt</label>
                    <input id="profile-edit-avatar-alt" name="avatar_alt" type="text" maxlength="191" class="<?= $fld ?>" value="<?= $h((string) ($profile['avatar_alt'] ?? '')) ?>"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="profile-edit-bio">Bio</label>
                    <textarea id="profile-edit-bio" name="bio" rows="4" maxlength="20000" class="<?= $fld ?>"><?= $h((string) ($profile['bio'] ?? '')) ?></textarea>
                </div>
                <div class="flex flex-col-reverse gap-2 border-t border-stone-100 pt-4 sm:flex-row sm:justify-end">
                    <button type="button" data-modal-close class="<?= $btnSec ?> w-full sm:w-auto">Cancel</button>
                    <button type="submit" class="<?= $btnPri ?> w-full sm:w-auto">Save profile</button>
                </div>
            </form>
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
                <div class="flex justify-center">
                    <?php if (!empty($profile['avatar_url'])) : ?>
                        <img src="<?= $h((string) $profile['avatar_url']) ?>" alt="<?= $h((string) ($profile['avatar_alt_display'] ?? '')) ?>" class="h-20 w-20 rounded-full object-cover ring-2 ring-stone-200/80"/>
                    <?php else : ?>
                        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-stone-200 text-lg font-semibold text-stone-800 ring-2 ring-stone-200/80"><?= $h((string) ($profile['initials'] ?? '?')) ?></div>
                    <?php endif; ?>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Name</p>
                    <p class="mt-1 font-semibold text-stone-900"><?= $h((string) ($profile['name'] ?? '—')) ?></p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Email</p>
                    <p class="mt-1 text-stone-700"><?= $h((string) ($profile['email'] ?? '—')) ?></p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Role</p>
                    <p class="mt-1 text-stone-800"><?= $h((string) ($profile['role_display'] ?? '—')) ?></p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Bio</p>
                    <?php $bioText = trim((string) ($profile['bio'] ?? '')); ?>
                    <p class="mt-2 whitespace-pre-wrap leading-relaxed text-stone-700"><?= $bioText !== '' ? $h($bioText) : '—' ?></p>
                </div>
            </div>
            <div class="flex justify-end border-t border-stone-100 px-5 py-4">
                <button type="button" data-modal-close class="<?= $btnPri ?>">Close</button>
            </div>
        </div>
    </div>
</div>
