<?php

use Core\App;
use Core\Database;

$slug = rawurldecode((string) route_param('slug', ''));
if ($slug === '') {
  abort();
}

try {
  $db = App::resolve(Database::class);
  $row = $db->query(
    "SELECT `p`.`id`, `p`.`slug`, `p`.`title`, `p`.`excerpt`, `p`.`reading_minutes`, `p`.`published_at`, `p`.`content`, `p`.`featured_image_url`, `u`.`name` AS `author`,
            `cat`.`name` AS `category_name`, `cat`.`slug` AS `category_slug`
     FROM `posts` `p`
     INNER JOIN `users` `u` ON `u`.`id` = `p`.`user_id`
     LEFT JOIN `categories` `cat` ON `cat`.`id` = `p`.`category_id`
     WHERE `p`.`slug` = ?
       AND `p`.`status` = 'published'
       AND `p`.`published_at` IS NOT NULL
       AND `p`.`published_at` <= CURDATE()
     LIMIT 1",
    [$slug]
  )->find();
} catch (Throwable) {
  http_response_code(503);
  view('blog/show.view.php', [
    'post' => null,
    'postError' => true,
    'pageTitle' => 'Unable to load article — Mini Blog',
    'metaDescription' => 'We could not load this article. Please try again shortly.',
  ]);
  return;
}

if (!$row) {
  abort();
}

$postId = (int) ($row['id'] ?? 0);
$tagRows = $db->query(
  'SELECT `t`.`name`, `t`.`slug`, `t`.`color`
   FROM `post_tag` `pt`
   INNER JOIN `tags` `t` ON `t`.`id` = `pt`.`tag_id`
   WHERE `pt`.`post_id` = ?
   ORDER BY `t`.`name` ASC',
  [$postId]
)->get();
$tags = [];
foreach ($tagRows as $tr) {
  $tags[] = [
    'name' => (string) ($tr['name'] ?? ''),
    'slug' => (string) ($tr['slug'] ?? ''),
    'color' => blog_sanitize_color($tr['color'] ?? null, '#78716c'),
  ];
}
$row['_tags'] = $tags;

$post = blog_post_from_db_row($row);

view('blog/show.view.php', [
  'post' => $post,
  'postError' => false,
  'pageTitle' => $post['title'] . ' — Mini Blog',
  'metaDescription' => $post['excerpt'],
]);
