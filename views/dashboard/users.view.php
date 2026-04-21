<?php
/**
 * @var string $pageTitle
 * @var string $heading
 * @var string|null $subheading
 * @var string $dashboardNav
 * @var array<int, array<string, mixed>> $users
 * @var string $usersUrl
 * @var array{q: string, role: string} $filters
 * @var string $flashSuccess
 * @var string $flashError
 * @var string $csrfToken
 * @var string $redirectQuery
 */
require_once base_path('views/dashboard/partials/head.php');
require_once base_path('views/dashboard/partials/sidebar.php');

$users = $users ?? [];
$usersUrl = $usersUrl ?? blog_url('dashboard/users');
$filters = $filters ?? ['q' => '', 'role' => ''];
$csrfToken = $csrfToken ?? auth_csrf_token();
$redirectQuery = $redirectQuery ?? '';
$flashSuccess = $flashSuccess ?? '';
$flashError = $flashError ?? '';

$roleFilter = (string) ($filters['role'] ?? '');
$qVal = trim((string) ($filters['q'] ?? ''));

$pageActions = <<<'HTML'
<button type="button" data-modal-open="modal-user-add" class="inline-flex items-center justify-center rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-amber-50 shadow-sm transition hover:bg-stone-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
    Add user
</button>
HTML;

$actionBtn = 'rounded-lg border border-stone-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-stone-800 shadow-sm hover:border-stone-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80 focus-visible:ring-offset-2 focus-visible:ring-offset-white';

$sessionUserId = (int) (auth_user()['id'] ?? 0);
?>

<?php require_once base_path('views/dashboard/partials/page-header.php'); ?>

<?php if ($flashSuccess !== '') : ?>
    <div class="mb-6 rounded-xl border border-emerald-200/90 bg-emerald-50/90 px-4 py-3 text-sm font-medium text-emerald-950 ring-1 ring-emerald-100"
         role="status">
        <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>
<?php if ($flashError !== '') : ?>
    <div class="mb-6 rounded-xl border border-red-200/90 bg-red-50/90 px-4 py-3 text-sm font-medium text-red-900 ring-1 ring-red-100"
         role="alert">
        <?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<div class="grid gap-6 xl:grid-cols-12">
    <div class="xl:col-span-8">
        <div class="rounded-2xl border border-stone-200/90 bg-white shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
            <div class="flex flex-col gap-3 border-b border-stone-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                <div>
                    <h2 class="text-sm font-semibold text-stone-900">Team</h2>
                    <p class="mt-1 text-xs text-stone-500">Owners can add users and change roles. At least one owner must stay active.</p>
                </div>
                <form method="get" action="<?= htmlspecialchars($usersUrl, ENT_QUOTES, 'UTF-8') ?>"
                      class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <label class="sr-only" for="user-search">Search</label>
                    <input type="search" id="user-search" name="q" value="<?= htmlspecialchars($qVal, ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="Search name or email"
                           class="min-w-[12rem] rounded-xl border border-stone-200 bg-white px-3 py-2 text-sm text-stone-900 shadow-sm placeholder:text-stone-400 focus:border-amber-400/80 focus:outline-none focus:ring-2 focus:ring-amber-500/30"/>
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-semibold text-stone-800 hover:bg-stone-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/80">
                        Search
                    </button>
                    <label class="sr-only" for="user-role-filter">Role</label>
                    <select id="user-role-filter" name="role"
                            class="<?= htmlspecialchars($dashboardSelect, ENT_QUOTES, 'UTF-8') ?> sm:w-auto sm:min-w-[10.5rem]"
                            onchange="this.form.submit()">
                        <option value="" <?= $roleFilter === '' ? 'selected' : '' ?>>All roles</option>
                        <option value="owner" <?= $roleFilter === 'owner' ? 'selected' : '' ?>>Owner</option>
                        <option value="editor" <?= $roleFilter === 'editor' ? 'selected' : '' ?>>Editor</option>
                        <option value="author" <?= $roleFilter === 'author' ? 'selected' : '' ?>>Author</option>
                        <option value="viewer" <?= $roleFilter === 'viewer' ? 'selected' : '' ?>>Viewer</option>
                    </select>
                </form>
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
                    <?php if ($users === []) : ?>
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-stone-600">No users match these filters.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($users as $u) : ?>
                        <?php
                        $uid = (int) ($u['id'] ?? 0);
                        $roleKey = (string) ($u['role'] ?? 'author');
                        $roleClass = match ($roleKey) {
                          'owner' => 'bg-stone-900 text-amber-50 ring-stone-900/10',
                          'editor' => 'bg-amber-50 text-amber-950 ring-amber-200/80',
                          'author' => 'bg-sky-50 text-sky-950 ring-sky-200/80',
                          default => 'bg-stone-100 text-stone-800 ring-stone-200/80',
                        };
                        $statusKey = (string) ($u['status'] ?? 'active');
                        $statusClass = $statusKey === 'active'
                          ? 'text-emerald-800'
                          : 'text-red-800';
                        $nm = htmlspecialchars((string) ($u['name'] ?? ''), ENT_QUOTES, 'UTF-8');
                        $em = htmlspecialchars((string) ($u['email'] ?? ''), ENT_QUOTES, 'UTF-8');
                        $rl = htmlspecialchars((string) ($u['role_display'] ?? ''), ENT_QUOTES, 'UTF-8');
                        $la = htmlspecialchars((string) ($u['last_login_display'] ?? ''), ENT_QUOTES, 'UTF-8');
                        $st = htmlspecialchars((string) ($u['status_display'] ?? ''), ENT_QUOTES, 'UTF-8');
                        $roleRaw = htmlspecialchars($roleKey, ENT_QUOTES, 'UTF-8');
                        $statusCode = htmlspecialchars($statusKey, ENT_QUOTES, 'UTF-8');
                        $delBody = htmlspecialchars('Remove ' . (string) ($u['name'] ?? 'user') . ' from the site? This cannot be undone.', ENT_QUOTES, 'UTF-8');
                        $initials = htmlspecialchars((string) ($u['initials'] ?? '?'), ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr class="hover:bg-stone-50/60">
                            <th scope="row" class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-stone-200 to-stone-300 text-sm font-semibold text-stone-900 ring-2 ring-white shadow-sm"
                                          aria-hidden="true"><?= $initials ?></span>
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
                                            data-user-id="<?= (string) $uid ?>"
                                            data-user-name="<?= $nm ?>"
                                            data-user-email="<?= $em ?>"
                                            data-user-role="<?= $roleRaw ?>"
                                            data-user-status-code="<?= $statusCode ?>">Edit
                                    </button>
                                    <button type="button"
                                            class="rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-800 shadow-sm hover:border-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/70 focus-visible:ring-offset-2 focus-visible:ring-offset-white<?= $uid === $sessionUserId ? ' cursor-not-allowed opacity-50' : '' ?>"
                                            data-modal-open="dashboard-confirm-modal"
                                            data-confirm-title="Remove user?"
                                            data-confirm-body="<?= $delBody ?>"
                                            data-confirm-label="Remove"
                                            data-confirm-submit-form="form-delete-user-<?= $uid ?>"
                                            <?= $uid === $sessionUserId ? 'disabled aria-disabled="true" title="You cannot remove your own account"' : '' ?>
                                    >Delete
                                    </button>
                                </div>
                                <form id="form-delete-user-<?= $uid ?>" method="post" action="<?= htmlspecialchars($usersUrl, ENT_QUOTES, 'UTF-8') ?>" class="hidden">
                                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"/>
                                    <input type="hidden" name="_action" value="delete"/>
                                    <input type="hidden" name="_redirect_query" value="<?= htmlspecialchars($redirectQuery, ENT_QUOTES, 'UTF-8') ?>"/>
                                    <input type="hidden" name="id" value="<?= $uid ?>"/>
                                </form>
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
                    <dd class="mt-1 text-stone-700">Full access, including user management.</dd>
                </div>
                <div class="rounded-xl bg-stone-50 p-3 ring-1 ring-stone-200/80">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-stone-500">Editor</dt>
                    <dd class="mt-1 text-stone-700">Publish, moderate comments, manage taxonomy.</dd>
                </div>
                <div class="rounded-xl bg-stone-50 p-3 ring-1 ring-stone-200/80">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-stone-500">Author</dt>
                    <dd class="mt-1 text-stone-700">Create and edit posts.</dd>
                </div>
                <div class="rounded-xl bg-stone-50 p-3 ring-1 ring-stone-200/80">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-stone-500">Viewer</dt>
                    <dd class="mt-1 text-stone-700">Dashboard access without content changes (where applicable).</dd>
                </div>
            </dl>
        </div>
    </aside>
</div>

<?php require_once base_path('views/dashboard/partials/modals/users.php'); ?>
<?php require_once base_path('views/dashboard/partials/footer.php'); ?>
