<?php

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$tagRows = $db->query(
  "SELECT DISTINCT tag FROM posts WHERE tag IS NOT NULL AND TRIM(tag) <> '' ORDER BY tag ASC"
)->get();
$allTags = array_values(array_map(static fn (array $r): string => (string) $r['tag'], $tagRows));

$filterTag = isset($_GET['tag']) ? trim((string) $_GET['tag']) : '';
if ($filterTag !== '' && !in_array($filterTag, $allTags, true)) {
  $filterTag = '';
}

$searchQuery = isset($_GET['q']) ? trim((string) $_GET['q']) : '';

$perPage = 6;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

$where = [];
$params = [];
if ($filterTag !== '') {
  $where[] = 'p.tag = ?';
  $params[] = $filterTag;
}
if ($searchQuery !== '') {
  $escaped = addcslashes($searchQuery, '%_\\');
  $like = '%' . $escaped . '%';
  $where[] = '(p.title LIKE ? OR p.excerpt LIKE ?)';
  $params[] = $like;
  $params[] = $like;
}
$whereSql = $where === [] ? '' : ' AND ' . implode(' AND ', $where);

$baseFrom = 'FROM posts p INNER JOIN users u ON u.id = p.user_id';

$totalInDb = (int) ($db->query('SELECT COUNT(*) AS c FROM posts')->find()['c'] ?? 0);

$totalCount = (int) ($db->query(
  "SELECT COUNT(*) AS c $baseFrom WHERE 1=1 $whereSql",
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
    "SELECT p.slug, p.title, p.excerpt, p.tag, p.reading_minutes, p.published_at, u.name AS author
     $baseFrom
     WHERE 1=1 $whereSql
     ORDER BY p.published_at DESC, p.id DESC
     LIMIT $limit OFFSET $offset",
    $params
  )->get();
  $posts = array_map('blog_post_from_db_row', $rows);
}

$metaParts = ['Articles and notes from the journal.'];
if ($filterTag !== '') {
  $metaParts[] = 'Topic: ' . $filterTag . '.';
}
if ($searchQuery !== '') {
  $metaParts[] = 'Search: ' . $searchQuery . '.';
}

view('blog/index.view.php', [
  'posts' => $posts,
  'allTags' => $allTags,
  'filterTag' => $filterTag,
  'searchQuery' => $searchQuery,
  'page' => $page,
  'totalPages' => $totalPages,
  'totalCount' => $totalCount,
  'totalPostsInDb' => $totalInDb,
  'perPage' => $perPage,
  'pageTitle' => 'Blog — Mini Blog',
  'metaDescription' => implode(' ', $metaParts),
]);
