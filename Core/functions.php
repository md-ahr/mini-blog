<?php

function dd($value)
{
  echo '<pre>';
  var_dump($value);
  echo '</pre>';
}

function abort($code = 404)
{
  http_response_code($code);
  require_once base_path("views/{$code}.php");
  die();
}

function base_path($path): string
{
  return BASE_PATH . $path;
}

/**
 * Public URL path (respects BLOG_BASE_PATH when the app lives in a subdirectory).
 */
function blog_url(string $path = ''): string
{
  $base = defined('BLOG_BASE_PATH') ? rtrim((string) BLOG_BASE_PATH, '/') : '';
  $path = '/' . ltrim($path, '/');
  if ($path === '/') {
    return $base === '' ? '/' : $base . '/';
  }
  return $base . $path;
}

/**
 * Request path relative to BLOG_BASE_PATH (for active nav).
 */
function blog_current_path(): string
{
  $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
  if (defined('BLOG_BASE_PATH')) {
    $prefix = rtrim((string) BLOG_BASE_PATH, '/');
    if ($prefix !== '' && str_starts_with($path, $prefix)) {
      $path = substr($path, strlen($prefix)) ?: '/';
    }
  }
  $path = '/' . ltrim($path, '/');
  return $path === '//' ? '/' : $path;
}

/**
 * Named parameters from the last matched dynamic route (e.g. :slug).
 */
function route_param(string $key, $default = null)
{
  return \Core\Router::$routeParams[$key] ?? $default;
}

function blog_post_url(string $slug): string
{
  return blog_url('blogs/' . ltrim($slug, '/'));
}

/**
 * @return array<string, array<string, mixed>>
 */
function blog_posts(): array
{
  static $cache;
  if ($cache === null) {
    $cache = require base_path('data/posts.php');
  }
  return $cache;
}

function view($path, $attributes = [])
{
  extract($attributes);
  require_once base_path('views/' . $path);
}

function redirect($path)
{
  header('Location: ' . $path);
  exit();
}

function urlIs($value): bool
{
  return $_SERVER['REQUEST_URI'] === $value;
}
