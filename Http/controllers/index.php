<?php

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$rows = $db->query(
  "SELECT p.slug, p.title, p.excerpt, p.reading_minutes, p.published_at, p.featured_image_url, u.name AS author,
          (SELECT t.name FROM post_tag pt INNER JOIN tags t ON t.id = pt.tag_id WHERE pt.post_id = p.id ORDER BY t.name ASC LIMIT 1) AS tag
   FROM posts p
   INNER JOIN users u ON u.id = p.user_id
   WHERE p.status = 'published'
     AND p.published_at IS NOT NULL
     AND p.published_at <= CURDATE()
   ORDER BY p.published_at DESC, p.id DESC"
)->get();

$posts = array_map('blog_post_from_db_row', $rows);

view('index.view.php', [
  'heading' => 'A quiet corner for writing',
  'pageTitle' => 'Mini Blog — Writing & notes',
  'metaDescription' => 'A minimal PHP blog: clear typography, simple layout, and room to grow.',
  'posts' => $posts,
]);
