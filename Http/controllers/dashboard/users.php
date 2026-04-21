<?php

require_once __DIR__ . '/guard.php';
auth_require_owner();

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);
$usersUrl = blog_url('dashboard/users');
$sessionId = (int) (auth_user()['id'] ?? 0);

$normalizeRole = static function (string $s): string {
  $s = strtolower(trim($s));
  return in_array($s, ['owner', 'editor', 'author', 'viewer'], true) ? $s : 'author';
};

$normalizeStatus = static function (string $s): string {
  $s = strtolower(trim($s));
  return in_array($s, ['active', 'suspended'], true) ? $s : 'active';
};

$flashSuccess = '';
$flashError = '';
if (isset($_GET['saved'])) {
  $flashSuccess = match ((string) $_GET['saved']) {
    'created' => 'User added.',
    'updated' => 'User updated.',
    'deleted' => 'User removed.',
    default => '',
  };
}
if (isset($_GET['error'])) {
  $flashError = match ((string) $_GET['error']) {
    'csrf' => 'Invalid session. Please refresh and try again.',
    'validation' => 'Please check the form fields.',
    'email_taken' => 'That email is already in use.',
    'not_found' => 'That user no longer exists.',
    'last_owner' => 'The site must keep at least one owner.',
    'last_active_owner' => 'Keep at least one active owner account.',
    'self_delete' => 'You cannot remove your own account here.',
    'has_posts' => 'Reassign or delete this user’s posts before removing the account.',
    'password_mismatch' => 'Passwords must match and be at least 8 characters.',
    'self_suspend' => 'You cannot suspend your own account.',
    default => 'Something went wrong.',
  };
}

if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  if (!auth_csrf_validate($_POST['_csrf'] ?? null)) {
    redirect($usersUrl . '?error=csrf');
  }

  $action = (string) ($_POST['_action'] ?? '');

  $rq = isset($_POST['_redirect_query']) ? trim((string) $_POST['_redirect_query']) : '';
  $rqSuffix = $rq !== '' ? '&' . $rq : '';

  if ($action === 'create') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');
    $role = $normalizeRole((string) ($_POST['role'] ?? 'author'));
    $status = $normalizeStatus((string) ($_POST['status'] ?? 'active'));

    if ($name === '' || mb_strlen($name) > 191 || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 191) {
      redirect($usersUrl . '?error=validation');
    }
    if (strlen($password) < 8 || $password !== $passwordConfirm) {
      redirect($usersUrl . '?error=password_mismatch');
    }

    $dup = $db->query('SELECT `id` FROM `users` WHERE `email` = ? LIMIT 1', [$email])->find();
    if ($dup) {
      redirect($usersUrl . '?error=email_taken');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $db->query(
      'INSERT INTO `users` (`name`, `email`, `password_hash`, `role`, `status`) VALUES (?, ?, ?, ?, ?)',
      [$name, $email, $hash, $role, $status]
    );
    redirect($usersUrl . '?saved=created' . $rqSuffix);
  }

  if ($action === 'update') {
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim((string) ($_POST['user_name'] ?? ''));
    $email = trim((string) ($_POST['user_email'] ?? ''));
    $role = $normalizeRole((string) ($_POST['user_role'] ?? 'author'));
    $status = $normalizeStatus((string) ($_POST['user_status'] ?? 'active'));

    if ($id < 1 || $name === '' || mb_strlen($name) > 191 || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 191) {
      redirect($usersUrl . '?error=validation');
    }

    $row = $db->query(
      'SELECT `id`, `role`, `status` FROM `users` WHERE `id` = ? LIMIT 1',
      [$id]
    )->find();
    if (!$row) {
      redirect($usersUrl . '?error=not_found');
    }

    $wasOwner = ((string) ($row['role'] ?? '')) === 'owner';
    $wasActive = ((string) ($row['status'] ?? '')) === 'active';

    $taken = $db->query('SELECT `id` FROM `users` WHERE `email` = ? AND `id` != ? LIMIT 1', [$email, $id])->find();
    if ($taken) {
      redirect($usersUrl . '?error=email_taken');
    }

    if ($id === $sessionId && $status === 'suspended') {
      redirect($usersUrl . '?error=self_suspend');
    }

    if ($wasOwner && $role !== 'owner' && blog_users_count_owners($db) <= 1) {
      redirect($usersUrl . '?error=last_owner');
    }

    if ($wasOwner && $wasActive && $status !== 'active') {
      if (blog_users_count_active_owners($db) <= 1) {
        redirect($usersUrl . '?error=last_active_owner');
      }
    }

    $db->query(
      'UPDATE `users` SET `name` = ?, `email` = ?, `role` = ?, `status` = ? WHERE `id` = ?',
      [$name, $email, $role, $status, $id]
    );

    if ($id === $sessionId) {
      auth_sync_session_profile([
        'name' => $name,
        'email' => $email,
        'role' => $role,
      ]);
    }

    redirect($usersUrl . '?saved=updated' . $rqSuffix);
  }

  if ($action === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id < 1) {
      redirect($usersUrl . '?error=validation');
    }
    if ($id === $sessionId) {
      redirect($usersUrl . '?error=self_delete');
    }

    $row = $db->query('SELECT `id`, `role` FROM `users` WHERE `id` = ? LIMIT 1', [$id])->find();
    if (!$row) {
      redirect($usersUrl . '?error=not_found');
    }

    if (((string) ($row['role'] ?? '')) === 'owner' && blog_users_count_owners($db) <= 1) {
      redirect($usersUrl . '?error=last_owner');
    }

    $postCount = (int) ($db->query('SELECT COUNT(*) AS `n` FROM `posts` WHERE `user_id` = ?', [$id])->find()['n'] ?? 0);
    if ($postCount > 0) {
      redirect($usersUrl . '?error=has_posts');
    }

    $db->query('DELETE FROM `users` WHERE `id` = ?', [$id]);
    redirect($usersUrl . '?saved=deleted' . $rqSuffix);
  }

  redirect($usersUrl);
}

$roleFilter = trim((string) ($_GET['role'] ?? ''));
if ($roleFilter !== '' && !in_array($roleFilter, ['owner', 'editor', 'author', 'viewer'], true)) {
  $roleFilter = '';
}

$q = trim((string) ($_GET['q'] ?? ''));

$where = ['1=1'];
$params = [];
if ($roleFilter !== '') {
  $where[] = '`role` = ?';
  $params[] = $roleFilter;
}
if ($q !== '') {
  $like = '%' . addcslashes($q, '%_\\') . '%';
  $where[] = '(`name` LIKE ? OR `email` LIKE ?)';
  array_push($params, $like, $like);
}
$whereSql = implode(' AND ', $where);

$rows = $db->query(
  "SELECT `id`, `name`, `email`, `role`, `status`, `created_at`, `last_login_at` FROM `users` WHERE $whereSql ORDER BY `name` ASC",
  $params
)->get();

$userRows = [];
foreach ($rows as $r) {
  $pv = profile_format_for_view($r);
  $st = (string) ($r['status'] ?? 'active');
  $pv['status'] = $st;
  $pv['status_display'] = $st === 'suspended' ? 'Suspended' : 'Active';
  $userRows[] = $pv;
}

$listQuery = static function (array $parts): string {
  return http_build_query(array_filter($parts, static fn ($v) => $v !== '' && $v !== null));
};

$redirectQuery = $listQuery([
  'q' => $q,
  'role' => $roleFilter,
]);

view('dashboard/users.view.php', [
  'pageTitle' => 'Users — Dashboard',
  'heading' => 'Users',
  'subheading' => 'Add accounts, assign roles, and control who can sign in.',
  'metaDescription' => '',
  'dashboardNav' => 'users',
  'users' => $userRows,
  'usersUrl' => $usersUrl,
  'filters' => [
    'q' => $q,
    'role' => $roleFilter,
  ],
  'flashSuccess' => $flashSuccess,
  'flashError' => $flashError,
  'csrfToken' => auth_csrf_token(),
  'redirectQuery' => $redirectQuery,
]);
