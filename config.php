<?php

$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort = (int)getenv('DB_PORT') ?: 3306;
$dbName = getenv('DB_DATABASE') ?: 'mini_blog';
$dbUser = getenv('DB_USERNAME') ?: 'root';
$dbPass = getenv('DB_PASSWORD') ?: 'Mysql#123';

return [
  'database' => [
    'host' => $dbHost,
    'port' => $dbPort,
    'dbname' => $dbName,
    'username' => $dbUser,
    'password' => $dbPass,
    'charset' => 'utf8mb4',
  ],
];
