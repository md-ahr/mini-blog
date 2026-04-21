<?php

/** @var Router $router */

use Core\Router;

$router->get('/', 'index.php');
$router->get('/about', 'about.php');

$router->get('/blogs', 'blog/index.php');
$router->get('/blogs/:slug', 'blog/show.php');

$router->get('/login', 'auth/login.php');

$router->get('/dashboard', 'dashboard/index.php');
$router->get('/dashboard/posts', 'dashboard/posts.php');
$router->get('/dashboard/tags', 'dashboard/tags.php');
$router->get('/dashboard/categories', 'dashboard/categories.php');
$router->get('/dashboard/comments', 'dashboard/comments.php');
$router->get('/dashboard/users', 'dashboard/users.php');
$router->get('/dashboard/settings', 'dashboard/settings.php');
$router->get('/dashboard/profile', 'dashboard/profile.php');
