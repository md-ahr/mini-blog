<?php

require_once __DIR__ . '/guard.php';

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);
$uid = (int) (auth_user()['id'] ?? 0);
if ($uid < 1) {
  abort(403);
}

$flashSuccess = '';
$flashError = '';
if (isset($_GET['saved'])) {
  $flashSuccess = match ((string) $_GET['saved']) {
    'account' => 'Profile updated.',
    'password' => 'Password updated.',
    'avatar' => 'Profile photo saved.',
    default => '',
  };
}
if (isset($_GET['error'])) {
  $flashError = match ((string) $_GET['error']) {
    'csrf' => 'Invalid session. Please refresh and try again.',
    'validation' => 'Please check the form fields.',
    'email_taken' => 'That email is already in use.',
    'password_current' => 'Current password is incorrect.',
    'password_mismatch' => 'New passwords do not match or are too short.',
    'upload_no_file' => 'Choose an image file to upload.',
    'upload_invalid' => 'Use a JPEG, PNG, WebP, or GIF image.',
    'upload_too_large' => 'Image must be ' . (PROFILE_AVATAR_MAX_BYTES / 1024 / 1024) . ' MB or smaller.',
    'upload_failed' => 'Could not save the image. Try again.',
    default => 'Something went wrong.',
  };
}

if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  if (!auth_csrf_validate($_POST['_csrf'] ?? null)) {
    redirect(blog_url('dashboard/profile?error=csrf'));
  }

  $action = (string) ($_POST['_action'] ?? '');

  if ($action === 'account') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $bio = trim((string) ($_POST['bio'] ?? ''));
    $avatarUrl = trim((string) ($_POST['avatar_url'] ?? ''));
    $avatarAlt = trim((string) ($_POST['avatar_alt'] ?? ''));

    $bad = $name === '' || mb_strlen($name) > 191
      || $email === '' || mb_strlen($email) > 191 || !filter_var($email, FILTER_VALIDATE_EMAIL)
      || mb_strlen($bio) > 20000
      || mb_strlen($avatarUrl) > 500
      || mb_strlen($avatarAlt) > 191;

    if ($bad) {
      redirect(blog_url('dashboard/profile?error=validation'));
    }

    $taken = $db->query(
      'SELECT `id` FROM `users` WHERE `email` = ? AND `id` != ? LIMIT 1',
      [$email, $uid]
    )->find();
    if ($taken) {
      redirect(blog_url('dashboard/profile?error=email_taken'));
    }

    $prevAvatarRow = $db->query('SELECT `avatar_url` FROM `users` WHERE `id` = ? LIMIT 1', [$uid])->find();
    $prevAvatar = trim((string) ($prevAvatarRow['avatar_url'] ?? ''));
    if ($prevAvatar !== $avatarUrl && profile_avatar_is_managed_upload($uid, $prevAvatar)) {
      profile_avatar_delete_managed_file($uid, $prevAvatar);
    }

    $db->query(
      'UPDATE `users` SET `name` = ?, `email` = ?, `bio` = ?, `avatar_url` = NULLIF(?, ""), `avatar_alt` = NULLIF(?, "") WHERE `id` = ?',
      [$name, $email, $bio, $avatarUrl, $avatarAlt, $uid]
    );

    auth_sync_session_profile([
      'name' => $name,
      'email' => $email,
      'avatar_url' => $avatarUrl !== '' ? $avatarUrl : null,
      'avatar_alt' => $avatarAlt !== '' ? $avatarAlt : null,
    ]);

    redirect(blog_url('dashboard/profile?saved=account'));
  }

  if ($action === 'avatar_upload') {
    $alt = trim((string) ($_POST['profile_photo_alt'] ?? ''));
    if (mb_strlen($alt) > 191) {
      redirect(blog_url('dashboard/profile?error=validation'));
    }
    $prevRow = $db->query('SELECT `avatar_url` FROM `users` WHERE `id` = ? LIMIT 1', [$uid])->find();
    $prevAvatar = trim((string) ($prevRow['avatar_url'] ?? ''));
    $file = $_FILES['profile_photo'] ?? null;
    if (!is_array($file)) {
      redirect(blog_url('dashboard/profile?error=upload_no_file'));
    }
    $stored = profile_avatar_store_from_upload($uid, $file);
    if (!$stored['ok']) {
      redirect(blog_url('dashboard/profile?error=' . $stored['error']));
    }
    $url = $stored['url'];
    if ($prevAvatar !== '' && $prevAvatar !== $url && profile_avatar_is_managed_upload($uid, $prevAvatar)) {
      profile_avatar_delete_managed_file($uid, $prevAvatar);
    }
    $db->query(
      'UPDATE `users` SET `avatar_url` = ?, `avatar_alt` = NULLIF(?, "") WHERE `id` = ?',
      [$url, $alt, $uid]
    );
    auth_sync_session_profile([
      'avatar_url' => $url,
      'avatar_alt' => $alt !== '' ? $alt : null,
    ]);
    redirect(blog_url('dashboard/profile?saved=avatar'));
  }

  if ($action === 'password') {
    $current = (string) ($_POST['current_password'] ?? '');
    $new = (string) ($_POST['new_password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');

    $rowPw = $db->query('SELECT `password_hash` FROM `users` WHERE `id` = ? LIMIT 1', [$uid])->find();
    if (!$rowPw || !password_verify($current, (string) $rowPw['password_hash'])) {
      redirect(blog_url('dashboard/profile?error=password_current'));
    }
    if (strlen($new) < 8 || $new !== $confirm) {
      redirect(blog_url('dashboard/profile?error=password_mismatch'));
    }

    $db->query(
      'UPDATE `users` SET `password_hash` = ? WHERE `id` = ?',
      [password_hash($new, PASSWORD_DEFAULT), $uid]
    );

    redirect(blog_url('dashboard/profile?saved=password'));
  }

  redirect(blog_url('dashboard/profile'));
}

$row = $db->query(
  'SELECT `id`, `name`, `email`, `role`, `status`, `bio`, `avatar_url`, `avatar_alt`, `created_at`, `last_login_at` FROM `users` WHERE `id` = ? LIMIT 1',
  [$uid]
)->find();

if (!$row) {
  abort(404);
}

$profile = profile_format_for_view($row);

view('dashboard/profile.view.php', [
  'pageTitle' => 'Profile — Dashboard',
  'heading' => 'Profile',
  'subheading' => 'Your public byline, contact email, and security preferences.',
  'metaDescription' => '',
  'dashboardNav' => 'profile',
  'profile' => $profile,
  'flashSuccess' => $flashSuccess,
  'flashError' => $flashError,
  'csrfToken' => auth_csrf_token(),
]);
