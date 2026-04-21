<?php

require_once __DIR__ . '/guard.php';

view('dashboard/index.view.php', [
  'pageTitle' => 'Dashboard — Mini Blog',
  'heading' => 'Overview',
  'subheading' => 'A calm control room for posts, taxonomy, and community—logic comes next.',
  'metaDescription' => '',
  'dashboardNav' => 'overview',
]);
