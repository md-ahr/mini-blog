<?php

require_once __DIR__ . '/guard.php';

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);
$commentsUrl = blog_url('dashboard/comments');

$flashSuccess = '';
$flashError = '';
if (isset($_GET['saved'])) {
  $flashSuccess = match ((string) $_GET['saved']) {
    'updated' => 'Comment updated.',
    'deleted' => 'Comment deleted.',
    'status' => 'Status updated.',
    default => '',
  };
}
if (isset($_GET['error'])) {
  $flashError = match ((string) $_GET['error']) {
    'csrf' => 'Invalid session. Please refresh and try again.',
    'validation' => 'Please check the form fields.',
    'not_found' => 'That comment no longer exists.',
    default => 'Something went wrong.',
  };
}

$normalizeCommentStatus = static function (string $s): string {
  $s = strtolower(trim($s));
  return in_array($s, ['pending', 'approved', 'spam', 'rejected'], true) ? $s : 'pending';
};

if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  if (!auth_csrf_validate($_POST['_csrf'] ?? null)) {
    redirect($commentsUrl . '?error=csrf');
  }

  $action = (string) ($_POST['_action'] ?? '');

  if ($action === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id < 1) {
      redirect($commentsUrl . '?error=validation');
    }
    $exists = $db->query('SELECT `id` FROM `comments` WHERE `id` = ? LIMIT 1', [$id])->find();
    if (!$exists) {
      redirect($commentsUrl . '?error=not_found');
    }
    $db->query('DELETE FROM `comments` WHERE `id` = ?', [$id]);
    redirect($commentsUrl . '?saved=deleted');
  }

  if ($action === 'set_status') {
    $id = (int) ($_POST['id'] ?? 0);
    $status = $normalizeCommentStatus((string) ($_POST['status'] ?? ''));
    if ($id < 1) {
      redirect($commentsUrl . '?error=validation');
    }
    $exists = $db->query('SELECT `id` FROM `comments` WHERE `id` = ? LIMIT 1', [$id])->find();
    if (!$exists) {
      redirect($commentsUrl . '?error=not_found');
    }
    $db->query('UPDATE `comments` SET `status` = ? WHERE `id` = ?', [$status, $id]);
    $qs = isset($_POST['_redirect_query']) ? trim((string) $_POST['_redirect_query']) : '';
    $target = $commentsUrl . '?saved=status' . ($qs !== '' ? '&' . ltrim($qs, '&') : '');
    redirect($target);
  }

  if ($action === 'update') {
    $id = (int) ($_POST['id'] ?? 0);
    $authorName = trim((string) ($_POST['author_name'] ?? ''));
    $authorEmail = trim((string) ($_POST['author_email'] ?? ''));
    $body = trim((string) ($_POST['body'] ?? ''));
    $status = $normalizeCommentStatus((string) ($_POST['status'] ?? 'pending'));

    if ($id < 1 || $authorName === '' || mb_strlen($authorName) > 191) {
      redirect($commentsUrl . '?error=validation');
    }
    if ($authorEmail === '' || !filter_var($authorEmail, FILTER_VALIDATE_EMAIL) || mb_strlen($authorEmail) > 191) {
      redirect($commentsUrl . '?error=validation');
    }
    if ($body === '' || mb_strlen($body) > 8000) {
      redirect($commentsUrl . '?error=validation');
    }

    $exists = $db->query('SELECT `id` FROM `comments` WHERE `id` = ? LIMIT 1', [$id])->find();
    if (!$exists) {
      redirect($commentsUrl . '?error=not_found');
    }

    $db->query(
      'UPDATE `comments` SET `author_name` = ?, `author_email` = ?, `body` = ?, `status` = ? WHERE `id` = ?',
      [$authorName, $authorEmail, $body, $status, $id]
    );
    $qs = isset($_POST['_redirect_query']) ? trim((string) $_POST['_redirect_query']) : '';
    $target = $commentsUrl . '?saved=updated' . ($qs !== '' ? '&' . ltrim($qs, '&') : '');
    redirect($target);
  }

  redirect($commentsUrl);
}

$q = trim((string) ($_GET['q'] ?? ''));
$statusFilter = trim((string) ($_GET['status'] ?? ''));
if ($statusFilter !== '' && !in_array($statusFilter, ['pending', 'approved', 'spam', 'rejected', 'all'], true)) {
  $statusFilter = '';
}
if ($statusFilter === '' || $statusFilter === 'all') {
  $statusFilter = 'all';
}

$perPage = 20;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

$where = ['1=1'];
$params = [];
if ($q !== '') {
  $like = '%' . addcslashes($q, '%_\\') . '%';
  $where[] = '(`c`.`body` LIKE ? OR `c`.`author_name` LIKE ? OR `c`.`author_email` LIKE ? OR `p`.`title` LIKE ?)';
  array_push($params, $like, $like, $like, $like);
}
if ($statusFilter !== 'all') {
  $where[] = '`c`.`status` = ?';
  $params[] = $statusFilter;
}

$whereSql = implode(' AND ', $where);
$baseFrom = 'FROM `comments` `c` INNER JOIN `posts` `p` ON `p`.`id` = `c`.`post_id`';

$totalComments = (int) ($db->query(
  "SELECT COUNT(*) AS `n` $baseFrom WHERE $whereSql",
  $params
)->find()['n'] ?? 0);

$totalPages = $totalComments > 0 ? (int) ceil($totalComments / $perPage) : 1;
if ($page > $totalPages) {
  $page = $totalPages;
}
$offset = ($page - 1) * $perPage;

$rows = $db->query(
  "SELECT `c`.`id`, `c`.`post_id`, `c`.`author_name`, `c`.`author_email`, `c`.`body`, `c`.`status`, `c`.`created_at`,
          `p`.`title` AS `post_title`, `p`.`slug` AS `post_slug`
   $baseFrom
   WHERE $whereSql
   ORDER BY `c`.`created_at` DESC, `c`.`id` DESC
   LIMIT $perPage OFFSET $offset",
  $params
)->get();

$pendingCount = (int) ($db->query(
  "SELECT COUNT(*) AS `n` FROM `comments` WHERE `status` = 'pending'"
)->find()['n'] ?? 0);

$listQuery = static function (array $parts): string {
  return http_build_query(array_filter($parts, static fn ($v) => $v !== '' && $v !== null));
};

$redirectQuery = $listQuery([
  'q' => $q,
  'status' => $statusFilter === 'all' ? '' : $statusFilter,
  'page' => $page > 1 ? $page : '',
]);

view('dashboard/comments.view.php', [
  'pageTitle' => 'Comments — Dashboard',
  'heading' => 'Comments',
  'subheading' => 'Approve, edit, or remove reader comments on your posts.',
  'metaDescription' => '',
  'dashboardNav' => 'comments',
  'comments' => $rows,
  'commentsUrl' => $commentsUrl,
  'filters' => [
    'q' => $q,
    'status' => $statusFilter,
  ],
  'page' => $page,
  'totalPages' => $totalPages,
  'totalComments' => $totalComments,
  'perPage' => $perPage,
  'pendingCount' => $pendingCount,
  'flashSuccess' => $flashSuccess,
  'flashError' => $flashError,
  'csrfToken' => auth_csrf_token(),
  'redirectQuery' => $redirectQuery,
]);
