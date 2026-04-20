<?php

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$rows = $db->query(
  'SELECT p.slug, p.title, p.excerpt, p.tag, p.reading_minutes, p.published_at, u.name AS author
   FROM posts p
   INNER JOIN users u ON u.id = p.user_id
   ORDER BY p.published_at DESC, p.id DESC'
)->get();

$posts = array_map('blog_post_from_db_row', $rows);

view('index.view.php', [
  'heading' => 'A quiet corner for writing',
  'pageTitle' => 'Mini Blog — Writing & notes',
  'metaDescription' => 'A minimal PHP blog: clear typography, simple layout, and room to grow.',
  'posts' => $posts,
]);
