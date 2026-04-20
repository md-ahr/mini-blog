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
