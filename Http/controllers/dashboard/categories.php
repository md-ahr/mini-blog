<?php

require_once __DIR__ . '/guard.php';
auth_require_manage_taxonomy();

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$categoriesUrl = blog_url('dashboard/categories');

$flashSuccess = '';
$flashError = '';
if (isset($_GET['saved'])) {
  $flashSuccess = match ((string) $_GET['saved']) {
    'created' => 'Category created.',
    'updated' => 'Category updated.',
    'deleted' => 'Category deleted.',
    default => '',
  };
}
if (isset($_GET['error'])) {
  $flashError = match ((string) $_GET['error']) {
    'csrf' => 'Invalid session. Please refresh and try again.',
    'validation' => 'Please check the form fields.',
    'not_found' => 'That category no longer exists.',
    'duplicate' => 'A category with that slug already exists.',
    'parent' => 'Choose a valid parent category.',
    default => 'Something went wrong.',
  };
}

$categoryParentChainHasId = static function (Database $db, int $startParentId, int $forbiddenId): bool {
  $current = $startParentId;
  for ($i = 0; $i < 64; $i++) {
    if ($current === $forbiddenId) {
      return true;
    }
    $row = $db->query('SELECT `parent_id` FROM `categories` WHERE `id` = ? LIMIT 1', [$current])->find();
    if (!$row || $row['parent_id'] === null || $row['parent_id'] === '') {
      return false;
    }
    $current = (int) $row['parent_id'];
  }
  return true;
};

if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  if (!auth_csrf_validate($_POST['_csrf'] ?? null)) {
    redirect($categoriesUrl . '?error=csrf');
  }

  $action = (string) ($_POST['_action'] ?? '');

  if ($action === 'create') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $slugIn = trim((string) ($_POST['slug'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $color = blog_sanitize_color($_POST['color'] ?? null, '#57534e');
    $sortOrder = (int) ($_POST['sort_order'] ?? 0);
    $parentRaw = $_POST['parent_id'] ?? '';
    $parentId = $parentRaw === '' || $parentRaw === null ? null : (int) $parentRaw;
    if ($name === '' || mb_strlen($name) > 191 || mb_strlen($description) > 65535) {
      redirect($categoriesUrl . '?error=validation');
    }
    if ($parentId !== null) {
      if ($parentId < 1) {
        redirect($categoriesUrl . '?error=parent');
      }
      $p = $db->query('SELECT `id` FROM `categories` WHERE `id` = ? LIMIT 1', [$parentId])->find();
      if (!$p) {
        redirect($categoriesUrl . '?error=parent');
      }
    }
    $slug = $slugIn !== '' ? blog_unique_slug($db, 'categories', blog_slugify($slugIn)) : blog_unique_slug($db, 'categories', blog_slugify($name));
    $db->query(
      'INSERT INTO `categories` (`parent_id`, `name`, `slug`, `description`, `color`, `sort_order`)
       VALUES (?, ?, ?, NULLIF(?, ""), ?, ?)',
      [$parentId, $name, $slug, $description, $color, $sortOrder]
    );
    redirect($categoriesUrl . '?saved=created');
  }

  if ($action === 'update') {
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim((string) ($_POST['name'] ?? ''));
    $slugIn = trim((string) ($_POST['slug'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $color = blog_sanitize_color($_POST['color'] ?? null, '#57534e');
    $sortOrder = (int) ($_POST['sort_order'] ?? 0);
    $parentRaw = $_POST['parent_id'] ?? '';
    $parentId = $parentRaw === '' || $parentRaw === null ? null : (int) $parentRaw;
    if ($id < 1 || $name === '' || mb_strlen($name) > 191 || mb_strlen($description) > 65535) {
      redirect($categoriesUrl . '?error=validation');
    }
    $row = $db->query('SELECT `id` FROM `categories` WHERE `id` = ? LIMIT 1', [$id])->find();
    if (!$row) {
      redirect($categoriesUrl . '?error=not_found');
    }
    if ($parentId !== null) {
      if ($parentId < 1 || $parentId === $id) {
        redirect($categoriesUrl . '?error=parent');
      }
      $p = $db->query('SELECT `id` FROM `categories` WHERE `id` = ? LIMIT 1', [$parentId])->find();
      if (!$p) {
        redirect($categoriesUrl . '?error=parent');
      }
      if ($categoryParentChainHasId($db, $parentId, $id)) {
        redirect($categoriesUrl . '?error=parent');
      }
    }
    $baseForSlug = $slugIn !== '' ? blog_slugify($slugIn) : blog_slugify($name);
    $slug = blog_unique_slug($db, 'categories', $baseForSlug, $id);
    $db->query(
      'UPDATE `categories` SET `parent_id` = ?, `name` = ?, `slug` = ?, `description` = NULLIF(?, ""), `color` = ?, `sort_order` = ? WHERE `id` = ?',
      [$parentId, $name, $slug, $description, $color, $sortOrder, $id]
    );
    redirect($categoriesUrl . '?saved=updated');
  }

  if ($action === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id < 1) {
      redirect($categoriesUrl . '?error=validation');
    }
    $db->query('DELETE FROM `categories` WHERE `id` = ?', [$id]);
    redirect($categoriesUrl . '?saved=deleted');
  }

  redirect($categoriesUrl);
}

$categoryRows = $db->query(
  "SELECT `c`.`id`, `c`.`parent_id`, `c`.`name`, `c`.`slug`, `c`.`description`, `c`.`color`, `c`.`sort_order`,
          (SELECT COUNT(*) FROM `posts` `p` WHERE `p`.`category_id` = `c`.`id`) AS `post_count`
   FROM `categories` `c`
   ORDER BY `c`.`sort_order` ASC, `c`.`name` ASC"
)->get();

$byId = [];
foreach ($categoryRows as $c) {
  $byId[(int) $c['id']] = $c;
}
$categories = [];
foreach ($categoryRows as $c) {
  $pid = $c['parent_id'];
  $c['parent_label'] = ($pid !== null && $pid !== '' && (int) $pid > 0 && isset($byId[(int) $pid]))
    ? (string) $byId[(int) $pid]['name']
    : '';
  $categories[] = $c;
}

view('dashboard/categories.view.php', [
  'pageTitle' => 'Categories — Dashboard',
  'heading' => 'Categories',
  'subheading' => 'Each post can reference one category. URLs use the category slug on the blog.',
  'metaDescription' => '',
  'dashboardNav' => 'categories',
  'categories' => $categories,
  'categoriesUrl' => $categoriesUrl,
  'flashSuccess' => $flashSuccess,
  'flashError' => $flashError,
  'csrfToken' => auth_csrf_token(),
]);
