<?php
/**
 * @var string $pageTitle
 * @var string $heading
 * @var string|null $subheading
 * @var string $dashboardNav
 */
require_once base_path('views/dashboard/partials/head.php');
require_once base_path('views/dashboard/partials/sidebar.php');

$pageActions = <<<'HTML'
<button type="button" data-modal-open="modal-user-invite" class="inline-flex items-center justify-center rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
    Invite user
</button>
HTML;

$demoUsers = [
        ['name' => 'Alex Rowan', 'email' => 'alex@example.com', 'role' => 'Owner', 'last' => 'Active now', 'status' => 'Active'],
        ['name' => 'Jamie Liu', 'email' => 'jamie@example.com', 'role' => 'Editor', 'last' => 'Apr 18, 2026', 'status' => 'Active'],
        ['name' => 'Riley Chen', 'email' => 'riley@example.com', 'role' => 'Author', 'last' => 'Apr 2, 2026', 'status' => 'Active'],
        ['name' => 'Taylor Brooks', 'email' => 'taylor@example.com', 'role' => 'Viewer', 'last' => 'Mar 9, 2026', 'status' => 'Suspended'],
];
$actionBtn = 'rounded-lg border border-stone-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-stone-800 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
?>

<?php require_once base_path('views/dashboard/partials/page-header.php'); ?>

<div class="grid gap-6 xl:grid-cols-12">
    <div class="xl:col-span-8">
        <div class="rounded-2xl border border-stone-200/90 bg-white shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
            <div class="flex flex-col gap-3 border-b border-stone-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                <div>
                    <h2 class="text-sm font-semibold text-stone-900">Team</h2>
                    <p class="mt-1 text-xs text-stone-500">Roles are illustrative until you wire authentication.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <label class="sr-only" for="user-role-filter">Role</label>
                    <select id="user-role-filter"
                            class="<?= htmlspecialchars($dashboardSelect, ENT_QUOTES, 'UTF-8') ?> sm:w-auto sm:min-w-[10.5rem]">
                        <option>All roles</option>
                        <option>Owner</option>
                        <option>Editor</option>
                        <option>Author</option>
                        <option>Viewer</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-100 text-left text-sm">
                    <thead class="bg-stone-50/80 text-xs font-semibold uppercase tracking-wider text-stone-500">
                    <tr>
                        <th scope="col" class="px-5 py-3">User</th>
                        <th scope="col" class="hidden px-3 py-3 md:table-cell">Role</th>
                        <th scope="col" class="hidden px-3 py-3 lg:table-cell">Last active</th>
                        <th scope="col" class="px-3 py-3">Status</th>
                        <th scope="col" class="px-3 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 bg-white">
                    <?php foreach ($demoUsers as $u) : ?>
                        <?php
                        $roleClass = match ($u['role']) {
                            'Owner' => 'bg-stone-900 text-amber-50 ring-stone-900/10',
                            'Editor' => 'bg-amber-50 text-amber-950 ring-amber-200/80',
                            'Author' => 'bg-sky-50 text-sky-950 ring-sky-200/80',
                            default => 'bg-stone-100 text-stone-800 ring-stone-200/80',
                        };
                        $statusClass = $u['status'] === 'Active'
                                ? 'text-emerald-800'
                                : 'text-red-800';
                        $nm = htmlspecialchars($u['name'], ENT_QUOTES, 'UTF-8');
                        $em = htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8');
                        $rl = htmlspecialchars($u['role'], ENT_QUOTES, 'UTF-8');
                        $la = htmlspecialchars($u['last'], ENT_QUOTES, 'UTF-8');
                        $st = htmlspecialchars($u['status'], ENT_QUOTES, 'UTF-8');
                        $delBody = htmlspecialchars('Remove ' . $u['name'] . ' from this workspace? Sessions will be revoked.', ENT_QUOTES, 'UTF-8');
                        $resetBody = htmlspecialchars('Send a password reset link to ' . $u['email'] . '?', ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr class="hover:bg-stone-50/60">
                            <th scope="row" class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-stone-200 to-stone-300 text-sm font-semibold text-stone-900 ring-2 ring-white shadow-sm"
                                          aria-hidden="true"><?= htmlspecialchars(strtoupper(substr($u['name'], 0, 1)), ENT_QUOTES, 'UTF-8') ?></span>
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-stone-900"><?= $nm ?></p>
                                        <p class="truncate text-xs text-stone-500"><?= $em ?></p>
                                        <p class="mt-2 md:hidden">
                                            <span class="inline-flex rounded-full px-2 py-0.5 text-[11px] font-semibold ring-1 <?= htmlspecialchars($roleClass, ENT_QUOTES, 'UTF-8') ?>">
                                                <?= $rl ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </th>
                            <td class="hidden px-3 py-4 md:table-cell">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 <?= htmlspecialchars($roleClass, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= $rl ?>
                                </span>
                            </td>
                            <td class="hidden px-3 py-4 tabular-nums text-stone-600 lg:table-cell"><?= $la ?></td>
                            <td class="px-3 py-4">
                                <span class="text-xs font-semibold <?= htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8') ?>"><?= $st ?></span>
                            </td>
                            <td class="px-3 py-4 text-right">
                                <div class="inline-flex flex-wrap justify-end gap-2">
                                    <button type="button" class="<?= $actionBtn ?>"
                                            data-modal-open="modal-user-view"
                                            data-user-name="<?= $nm ?>"
                                            data-user-email="<?= $em ?>"
                                            data-user-role="<?= $rl ?>"
                                            data-user-last="<?= $la ?>"
                                            data-user-status="<?= $st ?>">View
                                    </button>
                                    <button type="button" class="<?= $actionBtn ?>"
                                            data-modal-open="modal-user-edit"
                                            data-user-name="<?= $nm ?>"
                                            data-user-email="<?= $em ?>"
                                            data-user-role="<?= $rl ?>"
                                            data-user-status="<?= $st ?>">Edit
                                    </button>
                                    <button type="button" class="<?= $actionBtn ?>"
                                            data-modal-open="dashboard-confirm-modal"
                                            data-confirm-title="Send reset link?"
                                            data-confirm-body="<?= $resetBody ?>"
                                            data-confirm-label="Send email"
                                            data-confirm-variant="primary">Reset link
                                    </button>
                                    <button type="button"
                                            class="rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-800 shadow-sm hover:border-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/70 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                                            data-modal-open="dashboard-confirm-modal"
                                            data-confirm-title="Remove user?"
                                            data-confirm-body="<?= $delBody ?>"
                                            data-confirm-label="Remove user">Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <aside class="xl:col-span-4 space-y-6" aria-label="Role reference">
        <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
            <h2 class="text-sm font-semibold text-stone-900">Roles</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="rounded-xl bg-stone-50 p-3 ring-1 ring-stone-200/80">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-stone-500">Owner</dt>
                    <dd class="mt-1 text-stone-700">Full access, billing, destructive actions.</dd>
                </div>
                <div class="rounded-xl bg-stone-50 p-3 ring-1 ring-stone-200/80">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-stone-500">Editor</dt>
                    <dd class="mt-1 text-stone-700">Publish, moderate comments, manage taxonomy.</dd>
                </div>
                <div class="rounded-xl bg-stone-50 p-3 ring-1 ring-stone-200/80">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-stone-500">Author</dt>
                    <dd class="mt-1 text-stone-700">Create/edit own posts, upload media.</dd>
                </div>
                <div class="rounded-xl bg-stone-50 p-3 ring-1 ring-stone-200/80">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-stone-500">Viewer</dt>
                    <dd class="mt-1 text-stone-700">Read-only dashboard previews.</dd>
                </div>
            </dl>
        </div>
    </aside>
</div>

<?php require_once base_path('views/dashboard/partials/modals/users.php'); ?>
<?php require_once base_path('views/dashboard/partials/footer.php'); ?>
