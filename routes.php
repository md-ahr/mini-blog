<?php

/** @var Router $router */

use Core\Router;

$router->get('/', 'index.php');
$router->get('/about', 'about.php');
$router->get('/contact', 'contact.php');
$router->post('/contact', 'contact.php');

$router->get('/blogs', 'blog/index.php');
$router->get('/blogs/:slug', 'blog/show.php');
$router->post('/blogs/:slug', 'blog/show.php');
// Singular /blog (same controllers) — common shorthand; keeps query string e.g. ?category=
$router->get('/blog', 'blog/index.php');
$router->get('/blog/:slug', 'blog/show.php');
$router->post('/blog/:slug', 'blog/show.php');

$router->get('/login', 'auth/login.php');
$router->post('/login', 'auth/login.php');
$router->get('/logout', 'auth/logout.php');

$router->get('/dashboard', 'dashboard/index.php');
$router->get('/dashboard/posts', 'dashboard/posts.php');
$router->post('/dashboard/posts', 'dashboard/posts.php');
$router->get('/dashboard/tags', 'dashboard/tags.php');
$router->post('/dashboard/tags', 'dashboard/tags.php');
$router->get('/dashboard/categories', 'dashboard/categories.php');
$router->post('/dashboard/categories', 'dashboard/categories.php');
$router->get('/dashboard/comments', 'dashboard/comments.php');
$router->post('/dashboard/comments', 'dashboard/comments.php');
$router->get('/dashboard/users', 'dashboard/users.php');
$router->get('/dashboard/settings', 'dashboard/settings.php');
$router->get('/dashboard/profile', 'dashboard/profile.php');
$router->post('/dashboard/profile', 'dashboard/profile.php');
