<?php

namespace Core;

class Router
{
  protected $routes = [];

  /** @var array<string, string> */
  public static array $routeParams = [];

  public function get($uri, $controller)
  {
    return $this->add('GET', $uri, $controller);
  }

  public function add($method, $uri, $controller)
  {
    $this->routes[] = [
      'uri' => $uri,
      'controller' => $controller,
      'method' => $method,
      'middleware' => null
    ];

    return $this;
  }

  public function post($uri, $controller)
  {
    return $this->add('POST', $uri, $controller);
  }

  public function patch($uri, $controller)
  {
    return $this->add('PATCH', $uri, $controller);
  }

  public function put($uri, $controller)
  {
    return $this->add('PUT', $uri, $controller);
  }

  public function delete($uri, $controller)
  {
    return $this->add('DELETE', $uri, $controller);
  }

  public function route($uri, $method)
  {
    self::$routeParams = [];
    $uri = $this->normalizePath($uri);

    foreach ($this->routes as $route) {
      if ($route['method'] !== strtoupper($method)) {
        continue;
      }
      $params = $this->matchRoute($route['uri'], $uri);
      if ($params !== null) {
        self::$routeParams = $params;
        return require_once base_path('Http/controllers/' . $route['controller']);
      }
    }

    $this->abort();
  }

  protected function normalizePath(string $uri): string
  {
    if ($uri === '' || $uri === '/') {
      return '/';
    }
    return rtrim($uri, '/') ?: '/';
  }

  /**
   * @return array<string, string>|null
   */
  protected function matchRoute(string $pattern, string $uri): ?array
  {
    if (!str_contains($pattern, ':')) {
      return $pattern === $uri ? [] : null;
    }

    $names = [];
    $regex = preg_replace_callback(
      '/:([a-zA-Z_][a-zA-Z0-9_]*)/',
      function (array $m) use (&$names) {
        $names[] = $m[1];
        return '([^/]+)';
      },
      $pattern
    );
    $regex = '#^' . $regex . '$#';
    if (!preg_match($regex, $uri, $matches)) {
      return null;
    }
    array_shift($matches);
    if ($names === []) {
      return [];
    }
    return array_combine($names, $matches);
  }

  protected function abort($code = 404)
  {
    http_response_code($code);
    require_once base_path("views/{$code}.php");
    die();
  }
}
