<?php

require_once __DIR__ . '/guard.php';

view('dashboard/users.view.php', [
  'pageTitle' => 'Users — Dashboard',
  'heading' => 'Users',
  'subheading' => 'Invite teammates and align roles before exposing sensitive actions.',
  'metaDescription' => '',
  'dashboardNav' => 'users',
]);
