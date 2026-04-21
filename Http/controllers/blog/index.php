<?php

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$tagRows = $db->query(
  'SELECT `name`, `slug` FROM `tags` ORDER BY `name` ASC'
)->get();
$allTags = [];
foreach ($tagRows as $r) {
  $allTags[] = [
    'name' => (string) ($r['name'] ?? ''),
    'slug' => (string) ($r['slug'] ?? ''),
  ];
}

$validTagSlugs = array_values(array_filter(array_map(
  static fn (array $t): string => trim((string) ($t['slug'] ?? '')),
  $allTags
)));

$filterTag = isset($_GET['tag']) ? trim((string) $_GET['tag']) : '';
if ($filterTag !== '' && !in_array($filterTag, $validTagSlugs, true)) {
  $legacy = $db->query('SELECT `slug` FROM `tags` WHERE `name` = ? LIMIT 1', [$filterTag])->find();
  $filterTag = $legacy ? (string) ($legacy['slug'] ?? '') : '';
}
if ($filterTag !== '' && !in_array($filterTag, $validTagSlugs, true)) {
  $filterTag = '';
}

$categoryRows = $db->query(
  'SELECT `slug`, `name` FROM `categories` ORDER BY `sort_order` ASC, `name` ASC'
)->get();

$filterCategoryRaw = isset($_GET['category']) ? trim((string) $_GET['category']) : '';
$filterCategory = '';
if ($filterCategoryRaw !== '') {
  $want = mb_strtolower($filterCategoryRaw, 'UTF-8');
  foreach ($categoryRows as $r) {
    $sl = trim((string) ($r['slug'] ?? ''));
    if ($sl === '') {
      continue;
    }
    if (mb_strtolower($sl, 'UTF-8') === $want) {
      $filterCategory = $sl;
      break;
    }
  }
  if ($filterCategory === '') {
    foreach ($categoryRows as $r) {
      $sl = trim((string) ($r['slug'] ?? ''));
      $nm = trim((string) ($r['name'] ?? ''));
      if ($sl === '' || $nm === '') {
        continue;
      }
      if (mb_strtolower($nm, 'UTF-8') === $want) {
        $filterCategory = $sl;
        break;
      }
    }
  }
}

$sidebarCategories = [];
foreach ($categoryRows as $r) {
  $sl = trim((string) ($r['slug'] ?? ''));
  if ($sl === '') {
    continue;
  }
  $sidebarCategories[] = [
    'name' => (string) ($r['name'] ?? ''),
    'slug' => $sl,
  ];
}

$searchQuery = isset($_GET['q']) ? trim((string) $_GET['q']) : '';

$perPage = 6;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

$where = [];
$params = [];
$publishedSql = "p.status = 'published' AND p.published_at IS NOT NULL AND p.published_at <= CURDATE()";
if ($filterTag !== '') {
  $where[] = 'EXISTS (SELECT 1 FROM `post_tag` `pt` INNER JOIN `tags` `t` ON `t`.`id` = `pt`.`tag_id` WHERE `pt`.`post_id` = `p`.`id` AND `t`.`slug` = ?)';
  $params[] = $filterTag;
}
if ($filterCategory !== '') {
  $where[] = '`p`.`category_id` IN (SELECT `id` FROM `categories` WHERE `slug` = ? LIMIT 1)';
  $params[] = $filterCategory;
}
if ($searchQuery !== '') {
  $escaped = addcslashes($searchQuery, '%_\\');
  $like = '%' . $escaped . '%';
  $where[] = '(p.title LIKE ? OR p.excerpt LIKE ?)';
  $params[] = $like;
  $params[] = $like;
}
$filterSql = $where === [] ? '' : ' AND ' . implode(' AND ', $where);
$whereSql = $publishedSql . $filterSql;

$baseFrom = 'FROM `posts` `p` INNER JOIN `users` `u` ON `u`.`id` = `p`.`user_id` LEFT JOIN `categories` `cat` ON `cat`.`id` = `p`.`category_id`';

$totalInDb = (int) ($db->query(
  "SELECT COUNT(*) AS c FROM `posts` `p` WHERE $publishedSql"
)->find()['c'] ?? 0);

$totalCount = (int) ($db->query(
  "SELECT COUNT(*) AS c $baseFrom WHERE $whereSql",
  $params
)->find()['c'] ?? 0);

$totalPages = $totalCount > 0 ? (int) ceil($totalCount / $perPage) : 1;
if ($page > $totalPages) {
  $page = $totalPages;
}
$offset = ($page - 1) * $perPage;
$limit = $perPage;

$posts = [];
if ($totalCount > 0) {
  $rows = $db->query(
    "SELECT `p`.`id`, `p`.`slug`, `p`.`title`, `p`.`excerpt`, `p`.`reading_minutes`, `p`.`published_at`, `p`.`updated_at`, `p`.`featured_image_url`,
            `u`.`name` AS `author`, `u`.`avatar_url` AS `author_avatar_url`, `u`.`avatar_alt` AS `author_avatar_alt`, `u`.`bio` AS `author_bio`,
            `cat`.`name` AS `category_name`, `cat`.`slug` AS `category_slug`
     $baseFrom
     WHERE $whereSql
     ORDER BY `p`.`published_at` DESC, `p`.`id` DESC
     LIMIT $limit OFFSET $offset",
    $params
  )->get();
  $rows = blog_posts_with_tags($db, $rows);
  $posts = array_map('blog_post_from_db_row', $rows);
}

$metaParts = ['Articles and notes from the journal.'];
if ($filterTag !== '') {
  $label = $filterTag;
  foreach ($allTags as $t) {
    if (($t['slug'] ?? '') === $filterTag) {
      $label = (string) ($t['name'] ?? $filterTag);
      break;
    }
  }
  $metaParts[] = 'Topic: ' . $label . '.';
}
if ($filterCategory !== '') {
  $clabel = $filterCategory;
  foreach ($sidebarCategories as $c) {
    if (($c['slug'] ?? '') === $filterCategory) {
      $clabel = (string) ($c['name'] ?? $filterCategory);
      break;
    }
  }
  $metaParts[] = 'Category: ' . $clabel . '.';
}
if ($searchQuery !== '') {
  $metaParts[] = 'Search: ' . $searchQuery . '.';
}

view('blog/index.view.php', [
  'posts' => $posts,
  'allTags' => $allTags,
  'sidebarCategories' => $sidebarCategories,
  'filterTag' => $filterTag,
  'filterCategory' => $filterCategory,
  'searchQuery' => $searchQuery,
  'page' => $page,
  'totalPages' => $totalPages,
  'totalCount' => $totalCount,
  'totalPostsInDb' => $totalInDb,
  'perPage' => $perPage,
  'pageTitle' => 'Blog — Mini Blog',
  'metaDescription' => implode(' ', $metaParts),
]);
