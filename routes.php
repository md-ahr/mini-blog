<?php

/** @var Router $router */

use Core\Router;

$router->get('/', 'index.php');
$router->get('/about', 'about.php');

$router->get('/blogs/:slug', 'blog/show.php');
