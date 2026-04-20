<?php

$message = 'Ideas, notes, and longer writing—';

view('index.view.php', [
  'heading' => 'A quiet corner for writing',
  'message' => $message,
  'pageTitle' => 'Mini Blog — Writing & notes',
  'metaDescription' => 'A minimal PHP blog: clear typography, simple layout, and room to grow.',
  'posts' => array_values(blog_posts()),
]);
