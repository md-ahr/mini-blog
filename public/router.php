<?php

// #region agent debug
@file_put_contents(
  __DIR__ . '/router-debug.log',
  date('c') . ' ' . json_encode([
    'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? null,
    'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'] ?? null,
    'PATH' => parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH),
  ]) . PHP_EOL,
  FILE_APPEND
);
// #endregion

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$file = __DIR__ . $path;

if ($path !== '/' && is_file($file)) {
  return false;
}

require_once __DIR__ . '/index.php';
