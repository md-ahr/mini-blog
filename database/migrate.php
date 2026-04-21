#!/usr/bin/env php
<?php

/**
 * Apply SQL files in database/migrations/ in lexical order, once each.
 *
 * Usage (from project root):
 *   php database/migrate.php
 *
 * Requires: database exists (see config.php / env DB_*). Creates schema_migrations.
 */

declare(strict_types=1);

$basePath = dirname(__DIR__);

$config = require $basePath . '/config.php';
$db = $config['database'];

$dsn = sprintf(
  'mysql:host=%s;port=%d;dbname=%s;charset=%s',
  $db['host'],
  $db['port'],
  $db['dbname'],
  $db['charset']
);

$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
];

try {
  $pdo = new PDO($dsn, $db['username'], $db['password'], $options);
} catch (PDOException $e) {
  fwrite(STDERR, 'Database connection failed: ' . $e->getMessage() . PHP_EOL);
  fwrite(STDERR, 'Create the database if needed, then try again.' . PHP_EOL);
  exit(1);
}

$migrationsDir = __DIR__ . '/migrations';
$files = glob($migrationsDir . '/*.sql') ?: [];
sort($files, SORT_STRING);

if ($files === []) {
  fwrite(STDERR, 'No .sql files in ' . $migrationsDir . PHP_EOL);
  exit(1);
}

$pdo->exec(
  'CREATE TABLE IF NOT EXISTS `schema_migrations` (
    `version` VARCHAR(191) NOT NULL,
    `applied_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`version`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
);

foreach ($files as $path) {
  $version = basename($path);
  $check = $pdo->prepare('SELECT 1 FROM `schema_migrations` WHERE `version` = ? LIMIT 1');
  $check->execute([$version]);
  if ($check->fetchColumn()) {
    echo "[skip] {$version}" . PHP_EOL;
    continue;
  }

  $sql = file_get_contents($path);
  if ($sql === false) {
    fwrite(STDERR, "Cannot read {$path}" . PHP_EOL);
    exit(1);
  }

  try {
    $pdo->exec($sql);
  } catch (PDOException $e) {
    fwrite(STDERR, "Failed applying {$version}: " . $e->getMessage() . PHP_EOL);
    exit(1);
  }

  $ins = $pdo->prepare('INSERT INTO `schema_migrations` (`version`) VALUES (?)');
  $ins->execute([$version]);
  echo "[ok]   {$version}" . PHP_EOL;
}

echo 'Done.' . PHP_EOL;
