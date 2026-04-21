<?php

use Core\App;
use Core\Database;

$slug = rawurldecode((string) route_param('slug', ''));
if ($slug === '') {
  abort();
}

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

try {
  $db = App::resolve(Database::class);
} catch (\Throwable) {
  http_response_code(503);
  view('blog/show.view.php', [
    'post' => null,
    'postError' => true,
    'pageTitle' => 'Unable to load article — Mini Blog',
    'metaDescription' => 'We could not load this article. Please try again shortly.',
  ]);
  return;
}

try {
  $row = $db->query(
    "SELECT `p`.`id`, `p`.`slug`, `p`.`title`, `p`.`excerpt`, `p`.`reading_minutes`, `p`.`published_at`, `p`.`updated_at`, `p`.`content`, `p`.`featured_image_url`,
            `u`.`name` AS `author`, `u`.`avatar_url` AS `author_avatar_url`, `u`.`avatar_alt` AS `author_avatar_alt`, `u`.`bio` AS `author_bio`,
            `cat`.`name` AS `category_name`, `cat`.`slug` AS `category_slug`
     FROM `posts` `p`
     INNER JOIN `users` `u` ON `u`.`id` = `p`.`user_id`
     LEFT JOIN `categories` `cat` ON `cat`.`id` = `p`.`category_id`
     WHERE LOWER(TRIM(`p`.`slug`)) = LOWER(TRIM(?))
       AND `p`.`status` = 'published'
       AND `p`.`published_at` IS NOT NULL
       AND `p`.`published_at` <= CURDATE()
     LIMIT 1",
    [$slug]
  )->find();
} catch (\Throwable) {
  http_response_code(503);
  view('blog/show.view.php', [
    'post' => null,
    'postError' => true,
    'pageTitle' => 'Unable to load article — Mini Blog',
    'metaDescription' => 'We could not load this article. Please try again shortly.',
  ]);
  return;
}

if (!$row) {
  if ($method === 'POST') {
    abort();
  }
  $want = mb_strtolower(trim($slug), 'UTF-8');
  $catRows = $db->query(
    'SELECT `slug` FROM `categories`'
  )->get();
  foreach ($catRows as $cr) {
    $cSlug = trim((string) ($cr['slug'] ?? ''));
    if ($cSlug !== '' && mb_strtolower($cSlug, 'UTF-8') === $want) {
      header('Location: ' . blogs_index_url(['category' => $cSlug, 'page' => 1]), true, 302);
      exit;
    }
  }
  abort();
}

$postId = (int) ($row['id'] ?? 0);
$postUrl = blog_post_url((string) ($row['slug'] ?? $slug));

if ($method === 'POST') {
  auth_session_bootstrap();
  if (!auth_csrf_validate($_POST['_csrf'] ?? null)) {
    redirect($postUrl . '?comment=csrf#comments');
  }

  $honeypot = trim((string) ($_POST['website'] ?? ''));
  if ($honeypot !== '') {
    redirect($postUrl . '?comment=sent#comments');
  }

  $authorName = trim((string) ($_POST['author_name'] ?? ''));
  $authorEmail = trim((string) ($_POST['author_email'] ?? ''));
  $body = trim((string) ($_POST['body'] ?? ''));
  $parentRaw = $_POST['parent_id'] ?? '';
  $parentId = $parentRaw === '' || $parentRaw === null ? null : (int) $parentRaw;

  $sessionUser = auth_user();
  if ($sessionUser !== null) {
    $authorName = trim((string) ($sessionUser['name'] ?? $authorName));
    $authorEmail = trim((string) ($sessionUser['email'] ?? $authorEmail));
  }

  $errors = [];
  if ($authorName === '' || mb_strlen($authorName) > 191) {
    $errors['author_name'] = $authorName === '' ? 'Please enter your name.' : 'Name is too long.';
  }
  if ($authorEmail === '' || !filter_var($authorEmail, FILTER_VALIDATE_EMAIL)) {
    $errors['author_email'] = $authorEmail === '' ? 'Please enter your email.' : 'Enter a valid email address.';
  } elseif (mb_strlen($authorEmail) > 191) {
    $errors['author_email'] = 'Email is too long.';
  }
  if ($body === '') {
    $errors['body'] = 'Please write a comment.';
  } elseif (mb_strlen($body) < 2) {
    $errors['body'] = 'Comment is too short.';
  } elseif (mb_strlen($body) > 8000) {
    $errors['body'] = 'Comment is too long (max 8,000 characters).';
  }

  $parentOk = null;
  if ($errors === [] && $parentId !== null) {
    if ($parentId < 1) {
      $errors['parent_id'] = 'Invalid reply target.';
    } else {
      $parentOk = $db->query(
        'SELECT `id` FROM `comments` WHERE `id` = ? AND `post_id` = ? AND `status` = ? LIMIT 1',
        [$parentId, $postId, 'approved']
      )->find();
      if (!$parentOk) {
        $errors['parent_id'] = 'That comment cannot be replied to.';
      }
    }
  }

  if ($errors !== []) {
    $_SESSION['comment_form_errors'] = $errors;
    $_SESSION['comment_form_values'] = [
      'author_name' => $sessionUser === null ? $authorName : '',
      'author_email' => $sessionUser === null ? $authorEmail : '',
      'body' => $body,
      'parent_id' => $parentId,
    ];
    redirect($postUrl . '#comments');
  }

  $userId = $sessionUser !== null ? (int) $sessionUser['id'] : null;

  try {
    $db->query(
      'INSERT INTO `comments` (`post_id`, `user_id`, `parent_id`, `author_name`, `author_email`, `body`, `status`)
       VALUES (?, ?, ?, ?, ?, ?, ?)',
      [
        $postId,
        $userId,
        $parentId,
        $authorName,
        $authorEmail,
        $body,
        'pending',
      ]
    );
  } catch (\Throwable) {
    redirect($postUrl . '?comment=error#comments');
  }

  unset($_SESSION['comment_form_errors'], $_SESSION['comment_form_values']);
  redirect($postUrl . '?comment=sent#comments');
}

$tagRows = $db->query(
  'SELECT `t`.`name`, `t`.`slug`, `t`.`color`
   FROM `post_tag` `pt`
   INNER JOIN `tags` `t` ON `t`.`id` = `pt`.`tag_id`
   WHERE `pt`.`post_id` = ?
   ORDER BY `t`.`name` ASC',
  [$postId]
)->get();
$tags = [];
foreach ($tagRows as $tr) {
  $tags[] = [
    'name' => (string) ($tr['name'] ?? ''),
    'slug' => (string) ($tr['slug'] ?? ''),
    'color' => blog_sanitize_color($tr['color'] ?? null, '#78716c'),
  ];
}
$row['_tags'] = $tags;

$post = blog_post_from_db_row($row);

$commentRows = $db->query(
  'SELECT `id`, `parent_id`, `author_name`, `body`, `created_at`
   FROM `comments`
   WHERE `post_id` = ? AND `status` = ?
   ORDER BY `created_at` ASC',
  [$postId, 'approved']
)->get();

$nodes = [];
foreach ($commentRows as $cr) {
  $cid = (int) ($cr['id'] ?? 0);
  if ($cid < 1) {
    continue;
  }
  $pid = $cr['parent_id'] ?? null;
  $parentInt = $pid !== null && $pid !== '' ? (int) $pid : null;
  $dt = !empty($cr['created_at']) ? date_create((string) $cr['created_at']) : false;
  $nodes[$cid] = [
    'id' => $cid,
    'parent_id' => $parentInt !== null && $parentInt > 0 ? $parentInt : null,
    'author_name' => (string) ($cr['author_name'] ?? ''),
    'body' => (string) ($cr['body'] ?? ''),
    'created_at_display' => $dt instanceof DateTimeInterface ? blog_format_localized_date($dt, 'datetime') : '',
    'created_at_iso' => $dt instanceof DateTimeInterface ? $dt->format('c') : '',
    'children' => [],
  ];
}

foreach ($nodes as $cid => &$n) {
  $p = $n['parent_id'];
  if ($p !== null && isset($nodes[$p])) {
    $nodes[$p]['children'][] = &$n;
  }
}
unset($n);

$commentTree = [];
foreach ($nodes as $cid => &$n) {
  $p = $n['parent_id'];
  if ($p === null || !isset($nodes[$p])) {
    $commentTree[] = &$n;
  }
}
unset($n);

auth_session_bootstrap();
$commentFormErrors = $_SESSION['comment_form_errors'] ?? [];
$commentFormValues = $_SESSION['comment_form_values'] ?? [];
if (!is_array($commentFormErrors)) {
  $commentFormErrors = [];
}
if (!is_array($commentFormValues)) {
  $commentFormValues = [];
}
unset($_SESSION['comment_form_errors'], $_SESSION['comment_form_values']);

$cu = auth_user();
if ($cu !== null) {
  $commentFormValues['author_name'] = (string) ($cu['name'] ?? '');
  $commentFormValues['author_email'] = (string) ($cu['email'] ?? '');
}

$commentFlash = trim((string) ($_GET['comment'] ?? ''));

$canonicalUrl = blog_absolute_url(blog_post_url($post['slug']));
$ogImage = '';
if (!empty($post['featuredImageUrl'])) {
  $ogImage = blog_absolute_url((string) $post['featuredImageUrl']);
}

view('blog/show.view.php', [
  'post' => $post,
  'postError' => false,
  'pageTitle' => $post['title'] . ' — Mini Blog',
  'metaDescription' => $post['excerpt'],
  'canonicalUrl' => $canonicalUrl,
  'ogUrl' => $canonicalUrl,
  'ogImage' => $ogImage,
  'ogTitle' => $post['title'],
  'comments' => $commentTree,
  'commentCsrfToken' => auth_csrf_token(),
  'commentFormErrors' => $commentFormErrors,
  'commentFormValues' => $commentFormValues,
  'commentFlash' => $commentFlash,
]);
