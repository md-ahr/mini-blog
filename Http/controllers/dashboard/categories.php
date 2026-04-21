<?php

require_once __DIR__ . '/guard.php';

view('dashboard/categories.view.php', [
  'pageTitle' => 'Categories — Dashboard',
  'heading' => 'Categories',
  'subheading' => 'Organize archives with clear parent/child rules and human-readable descriptions.',
  'metaDescription' => '',
  'dashboardNav' => 'categories',
]);
