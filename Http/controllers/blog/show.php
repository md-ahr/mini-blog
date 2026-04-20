<?php

use Core\App;
use Core\Database;

$slug = rawurldecode((string) route_param('slug', ''));
if ($slug === '') {
  abort();
}

$db = App::resolve(Database::class);
$row = $db->query(
  'SELECT p.slug, p.title, p.excerpt, p.tag, p.reading_minutes, p.published_at, p.content, u.name AS author
   FROM posts p
   INNER JOIN users u ON u.id = p.user_id
   WHERE p.slug = ?
   LIMIT 1',
  [$slug]
)->find();

if (!$row) {
  abort();
}

$post = blog_post_from_db_row($row);

view('blog/show.view.php', [
  'post' => $post,
  'pageTitle' => $post['title'] . ' — Mini Blog',
  'metaDescription' => $post['excerpt'],
]);
