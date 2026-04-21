<?php

auth_session_bootstrap();

if (auth_check()) {
  redirect(blog_url(auth_login_redirect_path($_GET['next'] ?? '')));
}

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$error = '';
$loginNext = auth_login_read_next($_GET['next'] ?? '');

if ($method === 'POST') {
  $postedNext = auth_login_read_next($_POST['next'] ?? '');
  if ($postedNext !== '') {
    $loginNext = $postedNext;
  }

  if (!auth_csrf_validate($_POST['_csrf'] ?? null)) {
    $error = 'Your session expired. Please try again.';
  } else {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    if (auth_attempt_login($email, $password)) {
      redirect(blog_url(auth_login_redirect_path($loginNext)));
    }
    $error = 'These credentials do not match our records.';
  }
}

view('auth/login.view.php', [
  'pageTitle' => 'Sign in — Mini Blog',
  'metaDescription' => 'Sign in to manage posts, taxonomy, and settings.',
  'pageRobots' => 'noindex, nofollow',
  'loginError' => $error,
  'loginNext' => $loginNext,
  'csrfToken' => auth_csrf_token(),
]);
