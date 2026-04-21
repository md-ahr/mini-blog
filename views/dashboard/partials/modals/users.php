<?php
$fld = 'mt-1.5 w-full rounded-xl border border-stone-200 bg-stone-50/60 px-3 py-2 text-sm text-stone-900 shadow-inner shadow-stone-900/5 focus:border-amber-400/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30';
$lbl = 'block text-xs font-semibold text-stone-700';
$btnPri = 'inline-flex items-center justify-center rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
$btnSec = 'inline-flex items-center justify-center rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-semibold text-stone-800 shadow-sm transition hover:border-stone-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
?>

<div id="modal-user-invite" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-user-invite-title" tabindex="-1"
             class="w-full max-w-md rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <div>
                    <h2 id="modal-user-invite-title" class="text-base font-semibold text-stone-900">Invite user</h2>
                    <p class="mt-1 text-xs text-stone-500">Send an email invite when mailer is wired.</p>
                </div>
                <button type="button" data-modal-close class="rounded-lg p-2 text-stone-500 hover:bg-stone-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <form class="space-y-4 px-5 py-4" action="#" method="get" onsubmit="return false;">
                <div>
                    <label class="<?= $lbl ?>" for="user-invite-email">Email</label>
                    <input id="user-invite-email" name="user_email" type="email" class="<?= $fld ?>" placeholder="name@example.com"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="user-invite-role">Role</label>
                    <select id="user-invite-role" name="user_role" class="<?= $dashboardSelect ?> mt-1.5">
                        <option value="Viewer">Viewer</option>
                        <option value="Author">Author</option>
                        <option value="Editor">Editor</option>
                        <option value="Owner">Owner</option>
                    </select>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="user-invite-note">Note</label>
                    <textarea id="user-invite-note" name="user_note" rows="3" class="<?= $fld ?>" placeholder="Optional message in the invite."></textarea>
                </div>
            </form>
            <div class="flex flex-col-reverse gap-2 border-t border-stone-100 px-5 py-4 sm:flex-row sm:justify-end">
                <button type="button" data-modal-close class="<?= $btnSec ?> w-full sm:w-auto">Cancel</button>
                <button type="button" class="<?= $btnPri ?> w-full sm:w-auto">Send invite</button>
            </div>
        </div>
    </div>
</div>

<div id="modal-user-edit" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-user-edit-title" tabindex="-1"
             class="w-full max-w-md rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <h2 id="modal-user-edit-title" class="text-base font-semibold text-stone-900">Edit user</h2>
                <button type="button" data-modal-close class="rounded-lg p-2 text-stone-500 hover:bg-stone-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <form class="space-y-4 px-5 py-4" action="#" method="get" onsubmit="return false;">
                <div>
                    <label class="<?= $lbl ?>" for="user-edit-name">Name</label>
                    <input id="user-edit-name" name="user_name" type="text" class="<?= $fld ?>"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="user-edit-email">Email</label>
                    <input id="user-edit-email" name="user_email" type="email" class="<?= $fld ?>"/>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="user-edit-role">Role</label>
                    <select id="user-edit-role" name="user_role" class="<?= $dashboardSelect ?> mt-1.5">
                        <option value="Owner">Owner</option>
                        <option value="Editor">Editor</option>
                        <option value="Author">Author</option>
                        <option value="Viewer">Viewer</option>
                    </select>
                </div>
                <div>
                    <label class="<?= $lbl ?>" for="user-edit-status">Status</label>
                    <select id="user-edit-status" name="user_status" class="<?= $dashboardSelect ?> mt-1.5">
                        <option value="Active">Active</option>
                        <option value="Suspended">Suspended</option>
                    </select>
                </div>
            </form>
            <div class="flex flex-col-reverse gap-2 border-t border-stone-100 px-5 py-4 sm:flex-row sm:justify-end">
                <button type="button" data-modal-close class="<?= $btnSec ?> w-full sm:w-auto">Cancel</button>
                <button type="button" class="<?= $btnPri ?> w-full sm:w-auto">Save user</button>
            </div>
        </div>
    </div>
</div>

<div id="modal-user-view" data-dashboard-modal class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div data-modal-backdrop class="absolute inset-0 bg-stone-900/45 backdrop-blur-[2px]"></div>
    <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
        <div role="dialog" aria-modal="true" aria-labelledby="modal-user-view-title" tabindex="-1"
             class="w-full max-w-md rounded-2xl bg-white shadow-2xl shadow-stone-900/20 ring-1 ring-stone-900/10 outline-none">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <h2 id="modal-user-view-title" class="text-base font-semibold text-stone-900">View user</h2>
                <button type="button" data-modal-close class="rounded-lg p-2 text-stone-500 hover:bg-stone-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="space-y-4 px-5 py-4 text-sm">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Name</p>
                    <p class="mt-1 font-semibold text-stone-900" data-view="user-name">—</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Email</p>
                    <p class="mt-1 text-stone-700" data-view="user-email">—</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Role</p>
                        <p class="mt-1 text-stone-800" data-view="user-role">—</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Status</p>
                        <p class="mt-1 text-stone-800" data-view="user-status">—</p>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Last active</p>
                    <p class="mt-1 tabular-nums text-stone-800" data-view="user-last">—</p>
                </div>
            </div>
            <div class="flex justify-end border-t border-stone-100 px-5 py-4">
                <button type="button" data-modal-close class="<?= $btnPri ?>">Close</button>
            </div>
        </div>
    </div>
</div>
