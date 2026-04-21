<?php

require_once __DIR__ . '/guard.php';

view('dashboard/settings.view.php', [
  'pageTitle' => 'Settings — Dashboard',
  'heading' => 'Settings',
  'subheading' => 'Site-wide defaults: reading behavior, comments policy, and syndication.',
  'metaDescription' => '',
  'dashboardNav' => 'settings',
]);
