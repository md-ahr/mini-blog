<?php

$slug = rawurldecode((string) route_param('slug', ''));
$posts = blog_posts();

if ($slug === '' || !isset($posts[$slug])) {
  abort(404);
}

$post = $posts[$slug];

view('blog/show.view.php', [
  'post' => $post,
  'pageTitle' => $post['title'] . ' — Mini Blog',
  'metaDescription' => $post['excerpt'],
]);
