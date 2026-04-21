<?php

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$rows = $db->query(
  "SELECT `p`.`id`, `p`.`slug`, `p`.`title`, `p`.`excerpt`, `p`.`reading_minutes`, `p`.`published_at`, `p`.`featured_image_url`,
          `u`.`name` AS `author`, `u`.`avatar_url` AS `author_avatar_url`, `u`.`avatar_alt` AS `author_avatar_alt`,
          `cat`.`name` AS `category_name`, `cat`.`slug` AS `category_slug`
   FROM `posts` `p`
   INNER JOIN `users` `u` ON `u`.`id` = `p`.`user_id`
   LEFT JOIN `categories` `cat` ON `cat`.`id` = `p`.`category_id`
   WHERE `p`.`status` = 'published'
     AND `p`.`published_at` IS NOT NULL
     AND `p`.`published_at` <= CURDATE()
   ORDER BY `p`.`published_at` DESC, `p`.`id` DESC"
)->get();

$rows = blog_posts_with_tags($db, $rows);
$posts = array_map('blog_post_from_db_row', $rows);

view('index.view.php', [
  'heading' => 'A quiet corner for writing',
  'pageTitle' => 'Mini Blog — Writing & notes',
  'metaDescription' => 'A minimal PHP blog: clear typography, simple layout, and room to grow.',
  'posts' => $posts,
]);
