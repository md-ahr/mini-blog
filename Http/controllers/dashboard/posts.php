<?php

require_once __DIR__ . '/guard.php';

view('dashboard/posts.view.php', [
  'pageTitle' => 'Posts — Dashboard',
  'heading' => 'Posts',
  'subheading' => 'Search, filter, and open the editor once routes and persistence exist.',
  'metaDescription' => '',
  'dashboardNav' => 'posts',
]);
