<?php

require_once __DIR__ . '/guard.php';
auth_require_can_use_posts();

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$postsUrl = blog_url('dashboard/posts');
$sessionUserId = (int) (auth_user()['id'] ?? 0);
$canManageAllPosts = auth_can_manage_all_posts();

$flashSuccess = '';
$flashError = '';
if (isset($_GET['saved'])) {
  $flashSuccess = match ((string) $_GET['saved']) {
    'created' => 'Post created.',
    'updated' => 'Post updated.',
    'deleted' => 'Post deleted.',
    default => '',
  };
}
if (isset($_GET['error'])) {
  $flashError = match ((string) $_GET['error']) {
    'csrf' => 'Invalid session. Please refresh and try again.',
    'validation' => 'Please check the form fields.',
    'not_found' => 'That post no longer exists.',
    'forbidden' => 'You can only edit or remove your own posts.',
    'featured_upload' => 'Featured image must be a JPEG, PNG, WebP, or GIF under 2 MB.',
    default => 'Something went wrong.',
  };
}

$normalizeStatus = static function (string $s): string {
  $s = strtolower(trim($s));
  return in_array($s, ['draft', 'published', 'scheduled'], true) ? $s : 'draft';
};

$parseScheduled = static function (?string $raw): ?string {
  $raw = trim((string) $raw);
  if ($raw === '') {
    return null;
  }
  $dt = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $raw);
  if ($dt === false) {
    $dt = date_create_immutable($raw) ?: null;
  }
  return $dt instanceof DateTimeImmutable ? $dt->format('Y-m-d H:i:s') : null;
};

$applyFeaturedUpload = static function (array $file, string $existingUrl): string {
  if (!isset($file['error']) || (int) $file['error'] === UPLOAD_ERR_NO_FILE) {
    return $existingUrl;
  }
  $stored = blog_featured_image_store_upload($file);
  if (!$stored['ok']) {
    return '__UPLOAD_ERROR__';
  }
  return $stored['url'];
};

if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  if (!auth_csrf_validate($_POST['_csrf'] ?? null)) {
    redirect($postsUrl . '?error=csrf');
  }

  $action = (string) ($_POST['_action'] ?? '');

  if ($action === 'create') {
    $title = trim((string) ($_POST['title'] ?? ''));
    $slugIn = trim((string) ($_POST['slug'] ?? ''));
    $status = $normalizeStatus((string) ($_POST['status'] ?? 'draft'));
    $userId = $canManageAllPosts ? (int) ($_POST['user_id'] ?? 0) : $sessionUserId;
    $categoryRaw = $_POST['category_id'] ?? '';
    $categoryId = $categoryRaw === '' || $categoryRaw === null ? null : (int) $categoryRaw;
    $tags = trim((string) ($_POST['tags'] ?? ''));
    $excerpt = trim((string) ($_POST['excerpt'] ?? ''));
    $contentBody = (string) ($_POST['content_body'] ?? '');
    $readingRaw = trim((string) ($_POST['reading_minutes'] ?? ''));
    $readingMinutes = $readingRaw === '' ? null : max(0, min(32767, (int) $readingRaw));
    $featuredUrl = trim((string) ($_POST['featured_image_url'] ?? ''));
    $publishedRaw = trim((string) ($_POST['published_at'] ?? ''));
    $scheduledRaw = (string) ($_POST['scheduled_at'] ?? '');

    if ($title === '' || mb_strlen($title) > 500 || mb_strlen($excerpt) > 65535 || $userId < 1) {
      redirect($postsUrl . '?error=validation');
    }
    $userOk = $db->query('SELECT `id` FROM `users` WHERE `id` = ? LIMIT 1', [$userId])->find();
    if (!$userOk) {
      redirect($postsUrl . '?error=validation');
    }
    if ($categoryId !== null && $categoryId > 0) {
      $catOk = $db->query('SELECT `id` FROM `categories` WHERE `id` = ? LIMIT 1', [$categoryId])->find();
      if (!$catOk) {
        redirect($postsUrl . '?error=validation');
      }
    } else {
      $categoryId = null;
    }

    $slug = $slugIn !== ''
      ? blog_unique_slug($db, 'posts', blog_slugify($slugIn))
      : blog_unique_slug($db, 'posts', blog_slugify($title));

    $publishedAt = null;
    $scheduledAt = null;
    if ($status === 'published') {
      if ($publishedRaw !== '') {
        $pd = date_create_immutable($publishedRaw);
        $publishedAt = $pd ? $pd->format('Y-m-d') : date('Y-m-d');
      } else {
        $publishedAt = date('Y-m-d');
      }
    } elseif ($status === 'scheduled') {
      $scheduledAt = $parseScheduled($scheduledRaw);
    }

    $contentJson = blog_post_body_to_content_json($contentBody);

    $file = $_FILES['featured_file'] ?? null;
    if (is_array($file)) {
      $featuredUrl = $applyFeaturedUpload($file, $featuredUrl);
      if ($featuredUrl === '__UPLOAD_ERROR__') {
        redirect($postsUrl . '?error=featured_upload');
      }
    }
    if (mb_strlen($featuredUrl) > 500) {
      redirect($postsUrl . '?error=validation');
    }

    $db->connection->beginTransaction();
    try {
      $db->query(
        'INSERT INTO `posts` (`user_id`, `category_id`, `slug`, `title`, `excerpt`, `reading_minutes`, `content`, `status`, `featured_image_url`, `published_at`, `scheduled_at`)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, NULLIF(?, ""), ?, ?)',
        [
          $userId,
          $categoryId,
          $slug,
          $title,
          $excerpt,
          $readingMinutes,
          $contentJson,
          $status,
          $featuredUrl,
          $publishedAt,
          $scheduledAt,
        ]
      );
      $newId = (int) $db->connection->lastInsertId();
      blog_post_sync_tags($db, $newId, $tags);
      $db->connection->commit();
    } catch (\Throwable) {
      $db->connection->rollBack();
      redirect($postsUrl . '?error=validation');
    }

    redirect($postsUrl . '?saved=created');
  }

  if ($action === 'update') {
    $id = (int) ($_POST['id'] ?? 0);
    $title = trim((string) ($_POST['title'] ?? ''));
    $slugIn = trim((string) ($_POST['slug'] ?? ''));
    $status = $normalizeStatus((string) ($_POST['status'] ?? 'draft'));
    $userId = $canManageAllPosts ? (int) ($_POST['user_id'] ?? 0) : $sessionUserId;
    $categoryRaw = $_POST['category_id'] ?? '';
    $categoryId = $categoryRaw === '' || $categoryRaw === null ? null : (int) $categoryRaw;
    $tags = trim((string) ($_POST['tags'] ?? ''));
    $excerpt = trim((string) ($_POST['excerpt'] ?? ''));
    $contentBody = (string) ($_POST['content_body'] ?? '');
    $readingRaw = trim((string) ($_POST['reading_minutes'] ?? ''));
    $readingMinutes = $readingRaw === '' ? null : max(0, min(32767, (int) $readingRaw));
    $featuredUrl = trim((string) ($_POST['featured_image_url'] ?? ''));
    $publishedRaw = trim((string) ($_POST['published_at'] ?? ''));
    $scheduledRaw = (string) ($_POST['scheduled_at'] ?? '');

    if ($id < 1 || $title === '' || mb_strlen($title) > 500 || mb_strlen($excerpt) > 65535 || $userId < 1) {
      redirect($postsUrl . '?error=validation');
    }
    $existing = $db->query('SELECT `id`, `user_id` FROM `posts` WHERE `id` = ? LIMIT 1', [$id])->find();
    if (!$existing) {
      redirect($postsUrl . '?error=not_found');
    }
    if (!$canManageAllPosts && (int) ($existing['user_id'] ?? 0) !== $sessionUserId) {
      redirect($postsUrl . '?error=forbidden');
    }
    $userOk = $db->query('SELECT `id` FROM `users` WHERE `id` = ? LIMIT 1', [$userId])->find();
    if (!$userOk) {
      redirect($postsUrl . '?error=validation');
    }
    if ($categoryId !== null && $categoryId > 0) {
      $catOk = $db->query('SELECT `id` FROM `categories` WHERE `id` = ? LIMIT 1', [$categoryId])->find();
      if (!$catOk) {
        redirect($postsUrl . '?error=validation');
      }
    } else {
      $categoryId = null;
    }

    $baseSlug = $slugIn !== '' ? blog_slugify($slugIn) : blog_slugify($title);
    $slug = blog_unique_slug($db, 'posts', $baseSlug, $id);

    $publishedAt = null;
    $scheduledAt = null;
    if ($status === 'published') {
      if ($publishedRaw !== '') {
        $pd = date_create_immutable($publishedRaw);
        $publishedAt = $pd ? $pd->format('Y-m-d') : date('Y-m-d');
      } else {
        $publishedAt = date('Y-m-d');
      }
    } elseif ($status === 'scheduled') {
      $scheduledAt = $parseScheduled($scheduledRaw);
    }

    $contentJson = blog_post_body_to_content_json($contentBody);

    $file = $_FILES['featured_file'] ?? null;
    if (is_array($file)) {
      $featuredUrl = $applyFeaturedUpload($file, $featuredUrl);
      if ($featuredUrl === '__UPLOAD_ERROR__') {
        redirect($postsUrl . '?error=featured_upload');
      }
    }
    if (mb_strlen($featuredUrl) > 500) {
      redirect($postsUrl . '?error=validation');
    }

    $db->connection->beginTransaction();
    try {
      $db->query(
        'UPDATE `posts` SET `user_id` = ?, `category_id` = ?, `slug` = ?, `title` = ?, `excerpt` = ?, `reading_minutes` = ?, `content` = ?, `status` = ?, `featured_image_url` = NULLIF(?, ""), `published_at` = ?, `scheduled_at` = ? WHERE `id` = ?',
        [
          $userId,
          $categoryId,
          $slug,
          $title,
          $excerpt,
          $readingMinutes,
          $contentJson,
          $status,
          $featuredUrl,
          $publishedAt,
          $scheduledAt,
          $id,
        ]
      );
      blog_post_sync_tags($db, $id, $tags);
      $db->connection->commit();
    } catch (\Throwable) {
      $db->connection->rollBack();
      redirect($postsUrl . '?error=validation');
    }

    redirect($postsUrl . '?saved=updated');
  }

  if ($action === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id < 1) {
      redirect($postsUrl . '?error=validation');
    }
    $row = $db->query('SELECT `user_id` FROM `posts` WHERE `id` = ? LIMIT 1', [$id])->find();
    if (!$row) {
      redirect($postsUrl . '?error=not_found');
    }
    if (!$canManageAllPosts && (int) ($row['user_id'] ?? 0) !== $sessionUserId) {
      redirect($postsUrl . '?error=forbidden');
    }
    $db->query('DELETE FROM `posts` WHERE `id` = ?', [$id]);
    redirect($postsUrl . '?saved=deleted');
  }

  redirect($postsUrl);
}

$q = trim((string) ($_GET['q'] ?? ''));
$statusFilter = trim((string) ($_GET['status'] ?? ''));
if ($statusFilter !== '' && !in_array($statusFilter, ['draft', 'published', 'scheduled'], true)) {
  $statusFilter = '';
}
$authorFilter = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;
if (!$canManageAllPosts) {
  $authorFilter = $sessionUserId;
}
$categoryFilter = isset($_GET['category_id']) ? trim((string) $_GET['category_id']) : '';

$perPage = 15;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

$where = ['1=1'];
$params = [];
if ($q !== '') {
  $like = '%' . addcslashes($q, '%_\\') . '%';
  $where[] = '(`p`.`title` LIKE ? OR `p`.`excerpt` LIKE ?)';
  $params[] = $like;
  $params[] = $like;
}
if ($statusFilter !== '') {
  $where[] = '`p`.`status` = ?';
  $params[] = $statusFilter;
}
if (!$canManageAllPosts) {
  $where[] = '`p`.`user_id` = ?';
  $params[] = $sessionUserId;
} elseif ($authorFilter > 0) {
  $where[] = '`p`.`user_id` = ?';
  $params[] = $authorFilter;
}
if ($categoryFilter === '0') {
  $where[] = '`p`.`category_id` IS NULL';
} elseif ($categoryFilter !== '' && ctype_digit($categoryFilter)) {
  $where[] = '`p`.`category_id` = ?';
  $params[] = (int) $categoryFilter;
}

$whereSql = implode(' AND ', $where);
$baseFrom = 'FROM `posts` `p`
  INNER JOIN `users` `u` ON `u`.`id` = `p`.`user_id`
  LEFT JOIN `categories` `c` ON `c`.`id` = `p`.`category_id`';

$totalPosts = (int) ($db->query(
  "SELECT COUNT(*) AS `c` $baseFrom WHERE $whereSql",
  $params
)->find()['c'] ?? 0);

$totalPages = $totalPosts > 0 ? (int) ceil($totalPosts / $perPage) : 1;
if ($page > $totalPages) {
  $page = $totalPages;
}
$offset = ($page - 1) * $perPage;

$rows = $db->query(
  "SELECT `p`.`id`, `p`.`slug`, `p`.`title`, `p`.`status`, `p`.`excerpt`, `p`.`reading_minutes`, `p`.`featured_image_url`,
          `p`.`published_at`, `p`.`scheduled_at`, `p`.`updated_at`, `p`.`user_id`, `p`.`category_id`, `p`.`content`,
          `u`.`name` AS `author_name`, `c`.`name` AS `category_name`,
          (SELECT GROUP_CONCAT(DISTINCT `t`.`name` ORDER BY `t`.`name` SEPARATOR ', ') FROM `post_tag` `pt` INNER JOIN `tags` `t` ON `t`.`id` = `pt`.`tag_id` WHERE `pt`.`post_id` = `p`.`id`) AS `tag_names`
   $baseFrom
   WHERE $whereSql
   ORDER BY `p`.`updated_at` DESC, `p`.`id` DESC
   LIMIT $perPage OFFSET $offset",
  $params
)->get();

$users = $canManageAllPosts
  ? $db->query('SELECT `id`, `name` FROM `users` ORDER BY `name` ASC')->get()
  : $db->query('SELECT `id`, `name` FROM `users` WHERE `id` = ? LIMIT 1', [$sessionUserId])->get();
$categories = $db->query('SELECT `id`, `name` FROM `categories` ORDER BY `sort_order` ASC, `name` ASC')->get();

$currentUserId = $sessionUserId;

view('dashboard/posts.view.php', [
  'pageTitle' => 'Posts — Dashboard',
  'heading' => 'Posts',
  'subheading' => $canManageAllPosts
    ? 'Create and edit articles, categories, tags, and publishing status.'
    : 'Create and edit posts attributed to your account.',
  'metaDescription' => '',
  'dashboardNav' => 'posts',
  'posts' => $rows,
  'users' => $users,
  'categories' => $categories,
  'postsUrl' => $postsUrl,
  'filters' => [
    'q' => $q,
    'status' => $statusFilter,
    'user_id' => $authorFilter,
    'category_id' => $categoryFilter === '' ? '' : (string) $categoryFilter,
  ],
  'page' => $page,
  'totalPages' => $totalPages,
  'totalPosts' => $totalPosts,
  'perPage' => $perPage,
  'currentUserId' => $currentUserId,
  'canManageAllPosts' => $canManageAllPosts,
  'flashSuccess' => $flashSuccess,
  'flashError' => $flashError,
  'csrfToken' => auth_csrf_token(),
]);
