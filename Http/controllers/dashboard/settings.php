<?php

require_once __DIR__ . '/guard.php';

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);
$settingsUrl = blog_url('dashboard/settings');
$uid = (int) (auth_user()['id'] ?? 0);
$isOwner = (auth_user()['role'] ?? '') === 'owner';

$flashSuccess = '';
$flashError = '';
if (isset($_GET['saved'])) {
  $flashSuccess = match ((string) $_GET['saved']) {
    'settings' => 'Settings saved.',
    'reset' => 'Settings restored to defaults.',
    default => '',
  };
}
if (isset($_GET['error'])) {
  $flashError = match ((string) $_GET['error']) {
    'csrf' => 'Invalid session. Please refresh and try again.',
    'forbidden' => 'You do not have permission to change site settings.',
    'validation' => 'Please check the form fields.',
    'password' => 'Password is incorrect.',
    'last_owner' => 'Assign another owner before removing the only owner account.',
    'has_posts' => 'Delete or reassign your posts before deleting your account.',
    default => 'Something went wrong.',
  };
}

if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  if (!auth_csrf_validate($_POST['_csrf'] ?? null)) {
    redirect($settingsUrl . '?error=csrf');
  }

  $action = (string) ($_POST['_action'] ?? '');

  if ($action === 'save_settings') {
    if (!$isOwner) {
      redirect($settingsUrl . '?error=forbidden');
    }

    $siteTitle = trim((string) ($_POST['site_title'] ?? ''));
    $siteTagline = trim((string) ($_POST['site_tagline'] ?? ''));
    $postsPerPage = (int) ($_POST['posts_per_page'] ?? 12);
    $dateFormat = trim((string) ($_POST['date_format'] ?? ''));
    $rssEnabled = isset($_POST['rss_enabled']) ? '1' : '0';
    $homepageDisplay = trim((string) ($_POST['homepage_display'] ?? 'latest_posts'));
    $homepageSlug = trim((string) ($_POST['homepage_static_slug'] ?? ''));
    $commentsEnabled = isset($_POST['comments_enabled']) ? '1' : '0';
    $commentsModeration = isset($_POST['comments_require_moderation']) ? '1' : '0';
    $commentsClose30 = isset($_POST['comments_close_after_30_days']) ? '1' : '0';

    $dateOk = in_array($dateFormat, ['M j, Y', 'Y-m-d', 'relative'], true);
    $homeOk = in_array($homepageDisplay, ['latest_posts', 'static_page'], true);
    if ($siteTitle === '' || mb_strlen($siteTitle) > 191 || mb_strlen($siteTagline) > 500 || !$dateOk || !$homeOk) {
      redirect($settingsUrl . '?error=validation');
    }
    if ($postsPerPage < 1 || $postsPerPage > 100) {
      redirect($settingsUrl . '?error=validation');
    }
    if ($homepageDisplay === 'static_page') {
      $homepageSlug = blog_slugify($homepageSlug);
      if ($homepageSlug === '' || mb_strlen($homepageSlug) > 191) {
        redirect($settingsUrl . '?error=validation');
      }
    } else {
      $homepageSlug = '';
    }

    blog_settings_set($db, 'site_title', $siteTitle);
    blog_settings_set($db, 'site_tagline', $siteTagline);
    blog_settings_set($db, 'posts_per_page', (string) $postsPerPage);
    blog_settings_set($db, 'date_format', $dateFormat);
    blog_settings_set($db, 'rss_enabled', $rssEnabled);
    blog_settings_set($db, 'homepage_display', $homepageDisplay);
    blog_settings_set($db, 'homepage_static_slug', $homepageSlug);
    blog_settings_set($db, 'comments_enabled', $commentsEnabled);
    blog_settings_set($db, 'comments_require_moderation', $commentsModeration);
    blog_settings_set($db, 'comments_close_after_30_days', $commentsClose30);

    redirect($settingsUrl . '?saved=settings');
  }

  if ($action === 'reset_settings') {
    if (!$isOwner) {
      redirect($settingsUrl . '?error=forbidden');
    }
    foreach (blog_settings_defaults() as $key => $value) {
      blog_settings_set($db, $key, $value);
    }
    redirect($settingsUrl . '?saved=reset');
  }

  if ($action === 'delete_account') {
    $password = (string) ($_POST['password'] ?? '');
    if ($password === '') {
      redirect($settingsUrl . '?error=validation');
    }

    $row = $db->query(
      'SELECT `id`, `password_hash`, `role`, `avatar_url` FROM `users` WHERE `id` = ? LIMIT 1',
      [$uid]
    )->find();
    if (!$row || !password_verify($password, (string) ($row['password_hash'] ?? ''))) {
      redirect($settingsUrl . '?error=password');
    }

    if (((string) ($row['role'] ?? '')) === 'owner' && blog_users_count_owners($db) <= 1) {
      redirect($settingsUrl . '?error=last_owner');
    }

    $postCount = (int) ($db->query('SELECT COUNT(*) AS `n` FROM `posts` WHERE `user_id` = ?', [$uid])->find()['n'] ?? 0);
    if ($postCount > 0) {
      redirect($settingsUrl . '?error=has_posts');
    }

    $avatarUrl = trim((string) ($row['avatar_url'] ?? ''));
    if ($avatarUrl !== '' && profile_avatar_is_managed_upload($uid, $avatarUrl)) {
      profile_avatar_delete_managed_file($uid, $avatarUrl);
    }

    $db->query('DELETE FROM `users` WHERE `id` = ?', [$uid]);
    auth_logout();
    redirect(blog_url('login?notice=account_deleted'));
  }

  redirect($settingsUrl);
}

$settings = blog_settings_map($db);

view('dashboard/settings.view.php', [
  'pageTitle' => 'Settings — Mini Blog',
  'heading' => 'Settings',
  'subheading' => 'Site-wide defaults for the blog and comments. Only owners can change these values.',
  'metaDescription' => '',
  'dashboardNav' => 'settings',
  'settings' => $settings,
  'canEditSettings' => $isOwner,
  'flashSuccess' => $flashSuccess,
  'flashError' => $flashError,
  'csrfToken' => auth_csrf_token(),
  'settingsUrl' => $settingsUrl,
]);
