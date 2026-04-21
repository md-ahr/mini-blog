<?php

require_once __DIR__ . '/guard.php';

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$tagsUrl = blog_url('dashboard/tags');

$flashSuccess = '';
$flashError = '';
if (isset($_GET['saved'])) {
  $flashSuccess = match ((string) $_GET['saved']) {
    'created' => 'Tag created.',
    'updated' => 'Tag updated.',
    'deleted' => 'Tag deleted.',
    default => '',
  };
}
if (isset($_GET['error'])) {
  $flashError = match ((string) $_GET['error']) {
    'csrf' => 'Invalid session. Please refresh and try again.',
    'validation' => 'Please check the form fields.',
    'not_found' => 'That tag no longer exists.',
    'duplicate' => 'A tag with that name or slug already exists.',
    default => 'Something went wrong.',
  };
}

if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  if (!auth_csrf_validate($_POST['_csrf'] ?? null)) {
    redirect($tagsUrl . '?error=csrf');
  }

  $action = (string) ($_POST['_action'] ?? '');

  if ($action === 'create') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $slugIn = trim((string) ($_POST['slug'] ?? ''));
    $color = blog_sanitize_color($_POST['color'] ?? null, '#78716c');
    if ($name === '' || mb_strlen($name) > 191) {
      redirect($tagsUrl . '?error=validation');
    }
    $slug = $slugIn !== '' ? blog_unique_slug($db, 'tags', blog_slugify($slugIn)) : blog_unique_slug($db, 'tags', blog_slugify($name));
    $exists = $db->query('SELECT `id` FROM `tags` WHERE `name` = ? LIMIT 1', [$name])->find();
    if ($exists) {
      redirect($tagsUrl . '?error=duplicate');
    }
    $db->query(
      'INSERT INTO `tags` (`name`, `slug`, `color`) VALUES (?, ?, ?)',
      [$name, $slug, $color]
    );
    redirect($tagsUrl . '?saved=created');
  }

  if ($action === 'update') {
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim((string) ($_POST['name'] ?? ''));
    $slugIn = trim((string) ($_POST['slug'] ?? ''));
    $color = blog_sanitize_color($_POST['color'] ?? null, '#78716c');
    if ($id < 1 || $name === '' || mb_strlen($name) > 191) {
      redirect($tagsUrl . '?error=validation');
    }
    $row = $db->query('SELECT `id` FROM `tags` WHERE `id` = ? LIMIT 1', [$id])->find();
    if (!$row) {
      redirect($tagsUrl . '?error=not_found');
    }
    $taken = $db->query(
      'SELECT `id` FROM `tags` WHERE `name` = ? AND `id` != ? LIMIT 1',
      [$name, $id]
    )->find();
    if ($taken) {
      redirect($tagsUrl . '?error=duplicate');
    }
    $baseForSlug = $slugIn !== '' ? blog_slugify($slugIn) : blog_slugify($name);
    $slug = blog_unique_slug($db, 'tags', $baseForSlug, $id);
    $db->query(
      'UPDATE `tags` SET `name` = ?, `slug` = ?, `color` = ? WHERE `id` = ?',
      [$name, $slug, $color, $id]
    );
    redirect($tagsUrl . '?saved=updated');
  }

  if ($action === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id < 1) {
      redirect($tagsUrl . '?error=validation');
    }
    $db->query('DELETE FROM `tags` WHERE `id` = ?', [$id]);
    redirect($tagsUrl . '?saved=deleted');
  }

  redirect($tagsUrl);
}

$sort = (string) ($_GET['sort'] ?? 'usage');
if (!in_array($sort, ['usage', 'name', 'recent'], true)) {
  $sort = 'usage';
}

$orderSql = match ($sort) {
  'name' => '`t`.`name` ASC',
  'recent' => '`t`.`updated_at` DESC, `t`.`name` ASC',
  default => '`post_count` DESC, `t`.`name` ASC',
};

$tags = $db->query(
  "SELECT `t`.`id`, `t`.`name`, `t`.`slug`, `t`.`color`, `t`.`created_at`, `t`.`updated_at`,
          COUNT(`pt`.`post_id`) AS `post_count`
   FROM `tags` `t`
   LEFT JOIN `post_tag` `pt` ON `pt`.`tag_id` = `t`.`id`
   GROUP BY `t`.`id`, `t`.`name`, `t`.`slug`, `t`.`color`, `t`.`created_at`, `t`.`updated_at`
   ORDER BY $orderSql"
)->get();

view('dashboard/tags.view.php', [
  'pageTitle' => 'Tags — Dashboard',
  'heading' => 'Tags',
  'subheading' => 'Labels are stored in MySQL and linked to posts through post_tag.',
  'metaDescription' => '',
  'dashboardNav' => 'tags',
  'tags' => $tags,
  'sort' => $sort,
  'tagsUrl' => $tagsUrl,
  'flashSuccess' => $flashSuccess,
  'flashError' => $flashError,
  'csrfToken' => auth_csrf_token(),
]);
