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

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
$method = isset($_POST['_method']) ? $_POST['_method'] : $_SERVER['REQUEST_METHOD'];

try {
  $router->route($uri, $method);
} catch (Exception $e) {
  abort();
}
