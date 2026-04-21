<?php

use Core\Router;

const BASE_PATH = __DIR__ . '/../';

spl_autoload_register(function ($class) {
  if (strpos($class, 'Core\\') !== 0) {
    return;
  }
  $relative = substr($class, strlen('Core\\'));
  $file = BASE_PATH . 'Core/' . str_replace('\\', '/', $relative) . '.php';
  if (is_file($file)) {
    require_once $file;
  }
});

require_once BASE_PATH . 'Core/functions.php';

auth_session_bootstrap();

require_once base_path('bootstrap.php');

$router = new Router();
$routes = require_once base_path('routes.php');

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if ($uri === null || $uri === false || $uri === '') {
  $uri = '/';
}
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
$scriptDir = rtrim($scriptDir, '/');
if ($scriptDir !== '' && $scriptDir !== '.' && $scriptDir !== '/' && str_starts_with($uri, $scriptDir)) {
  $uri = substr($uri, strlen($scriptDir)) ?: '/';
}
if (defined('BLOG_BASE_PATH')) {
  $prefix = rtrim((string) BLOG_BASE_PATH, '/');
  if ($prefix !== '' && str_starts_with($uri, $prefix)) {
    $uri = substr($uri, strlen($prefix)) ?: '/';
  }
}
$uri = '/' . ltrim($uri, '/');
if ($uri === '//') {
  $uri = '/';
}
$method = isset($_POST['_method']) ? $_POST['_method'] : $_SERVER['REQUEST_METHOD'];

// #region agent debug
@file_put_contents(
  __DIR__ . '/index-debug.log',
  date('c') . ' ' . json_encode([
    'final_uri' => $uri,
    'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'] ?? null,
    'PATH_INFO' => $_SERVER['PATH_INFO'] ?? null,
    'QUERY_STRING' => $_SERVER['QUERY_STRING'] ?? null,
    'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? null,
  ]) . PHP_EOL,
  FILE_APPEND
);
// #endregion

try {
  $router->route($uri, $method);
} catch (Exception $e) {
  abort();
}
