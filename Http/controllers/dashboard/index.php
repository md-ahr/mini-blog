<?php

require_once __DIR__ . '/guard.php';

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$sessionUserId = (int) (auth_user()['id'] ?? 0);
$canManageAllPosts = auth_can_manage_all_posts();
$usersForModals = $canManageAllPosts
  ? $db->query('SELECT `id`, `name` FROM `users` ORDER BY `name` ASC')->get()
  : $db->query('SELECT `id`, `name` FROM `users` WHERE `id` = ? LIMIT 1', [$sessionUserId])->get();
$categoriesForModals = $db->query('SELECT `id`, `name` FROM `categories` ORDER BY `sort_order` ASC, `name` ASC')->get();

$publishedCount = (int) ($db->query(
  "SELECT COUNT(*) AS `n` FROM `posts` WHERE `status` = 'published'"
)->find()['n'] ?? 0);

$publishedThisWeek = (int) ($db->query(
  "SELECT COUNT(*) AS `n` FROM `posts` WHERE `status` = 'published' AND `published_at` IS NOT NULL AND `published_at` >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)"
)->find()['n'] ?? 0);

$draftCount = (int) ($db->query(
  "SELECT COUNT(*) AS `n` FROM `posts` WHERE `status` = 'draft'"
)->find()['n'] ?? 0);

$scheduledCount = (int) ($db->query(
  "SELECT COUNT(*) AS `n` FROM `posts` WHERE `status` = 'scheduled'"
)->find()['n'] ?? 0);

$commentTotal = (int) ($db->query(
  "SELECT COUNT(*) AS `n` FROM `comments`"
)->find()['n'] ?? 0);

$pendingComments = (int) ($db->query(
  "SELECT COUNT(*) AS `n` FROM `comments` WHERE `status` = 'pending'"
)->find()['n'] ?? 0);

$catsCount = (int) ($db->query(
  "SELECT COUNT(*) AS `n` FROM `categories`"
)->find()['n'] ?? 0);

$tagsCount = (int) ($db->query(
  "SELECT COUNT(*) AS `n` FROM `tags`"
)->find()['n'] ?? 0);

$publishedHint = $publishedThisWeek > 0
  ? '+' . $publishedThisWeek . ' this week'
  : 'None this week';

$draftHint = $scheduledCount > 0
  ? ($scheduledCount === 1 ? '1 post scheduled' : $scheduledCount . ' posts scheduled')
  : 'No scheduled posts';

$commentHint = $pendingComments > 0
  ? ($pendingComments === 1 ? '1 awaiting moderation' : $pendingComments . ' awaiting moderation')
  : 'Queue clear';

$tagsHint = $catsCount === 0
  ? 'No categories yet'
  : ($catsCount === 1 ? '1 category' : $catsCount . ' categories');

$stats = [
  ['label' => 'Published posts', 'value' => (string) $publishedCount, 'hint' => $publishedHint, 'tone' => 'stone'],
  ['label' => 'Drafts', 'value' => (string) $draftCount, 'hint' => $draftHint, 'tone' => 'amber'],
  ['label' => 'Comments', 'value' => (string) $commentTotal, 'hint' => $commentHint, 'tone' => 'emerald'],
  ['label' => 'Tags', 'value' => (string) $tagsCount, 'hint' => $tagsHint, 'tone' => 'sky'],
];

$postRows = $db->query(
  "SELECT `id`, `title`, `slug`, `status`, `created_at`, `updated_at` FROM `posts` ORDER BY `updated_at` DESC LIMIT 12"
)->get();

$commentRows = $db->query(
  "SELECT `c`.`author_name`, `c`.`status`, `c`.`created_at`, `p`.`title` AS `post_title`
   FROM `comments` `c` INNER JOIN `posts` `p` ON `p`.`id` = `c`.`post_id`
   ORDER BY `c`.`created_at` DESC LIMIT 12"
)->get();

$tagRows = $db->query(
  "SELECT `name`, `created_at` FROM `tags` ORDER BY `created_at` DESC LIMIT 8"
)->get();

$events = [];

foreach ($postRows as $r) {
  $st = (string) ($r['status'] ?? 'draft');
  $titleEsc = trim((string) ($r['title'] ?? ''));
  $shortTitle = mb_strlen($titleEsc) > 56 ? mb_substr($titleEsc, 0, 53) . '…' : $titleEsc;
  $when = blog_short_relative_time((string) ($r['updated_at'] ?? ''));
  $meta = '“' . $shortTitle . '” · ' . $when;
  $createdTs = strtotime((string) ($r['created_at'] ?? '')) ?: 0;
  $updatedTs = strtotime((string) ($r['updated_at'] ?? '')) ?: 0;
  $isFresh = $createdTs > 0 && $updatedTs > 0 && abs($updatedTs - $createdTs) < 180;

  if ($st === 'published') {
    $events[] = [
      'ts' => $updatedTs,
      'title' => $isFresh ? 'Post published' : 'Post updated',
      'meta' => $meta,
      'badge' => 'Published',
      'badgeClass' => 'bg-emerald-50 text-emerald-900 ring-emerald-200/70',
    ];
  } elseif ($st === 'scheduled') {
    $events[] = [
      'ts' => $updatedTs,
      'title' => 'Scheduled post',
      'meta' => $meta,
      'badge' => 'Scheduled',
      'badgeClass' => 'bg-amber-50 text-amber-950 ring-amber-200/80',
    ];
  } else {
    $events[] = [
      'ts' => $updatedTs,
      'title' => $isFresh ? 'Draft created' : 'Draft updated',
      'meta' => $meta,
      'badge' => 'Draft',
      'badgeClass' => 'bg-stone-100 text-stone-800 ring-stone-200/80',
    ];
  }
}

foreach ($commentRows as $r) {
  $st = (string) ($r['status'] ?? 'pending');
  $postTitle = trim((string) ($r['post_title'] ?? ''));
  $shortPost = mb_strlen($postTitle) > 40 ? mb_substr($postTitle, 0, 37) . '…' : $postTitle;
  $author = trim((string) ($r['author_name'] ?? ''));
  $when = blog_short_relative_time((string) ($r['created_at'] ?? ''));
  $meta = 'On “' . $shortPost . '” · ' . $author . ' · ' . $when;

  $badge = match ($st) {
    'pending' => 'Moderation',
    'approved' => 'Approved',
    'spam' => 'Spam',
    'rejected' => 'Rejected',
    default => 'Comment',
  };
  $badgeClass = match ($st) {
    'pending' => 'bg-amber-50 text-amber-950 ring-amber-200/80',
    'approved' => 'bg-emerald-50 text-emerald-900 ring-emerald-200/70',
    'spam' => 'bg-red-50 text-red-900 ring-red-200/70',
    'rejected' => 'bg-stone-100 text-stone-800 ring-stone-200/80',
    default => 'bg-stone-100 text-stone-800 ring-stone-200/80',
  };

  $title = $st === 'pending' ? 'Comment awaiting review' : 'Comment';

  $events[] = [
    'ts' => strtotime((string) ($r['created_at'] ?? '')) ?: 0,
    'title' => $title,
    'meta' => $meta,
    'badge' => $badge,
    'badgeClass' => $badgeClass,
  ];
}

foreach ($tagRows as $r) {
  $name = trim((string) ($r['name'] ?? ''));
  $when = blog_short_relative_time((string) ($r['created_at'] ?? ''));
  $events[] = [
    'ts' => strtotime((string) ($r['created_at'] ?? '')) ?: 0,
    'title' => 'Tag added',
    'meta' => '“' . $name . '” · ' . $when,
    'badge' => 'Taxonomy',
    'badgeClass' => 'bg-sky-50 text-sky-950 ring-sky-200/80',
  ];
}

usort($events, static function (array $a, array $b): int {
  return ($b['ts'] ?? 0) <=> ($a['ts'] ?? 0);
});

$activity = [];
foreach (array_slice($events, 0, 12) as $e) {
  unset($e['ts']);
  $activity[] = $e;
}

$reviewComment = $db->query(
  "SELECT `c`.`author_name`, `c`.`author_email`, `c`.`body`, `c`.`status`, `c`.`created_at`, `p`.`title` AS `post_title`
   FROM `comments` `c` INNER JOIN `posts` `p` ON `p`.`id` = `c`.`post_id`
   WHERE `c`.`status` = 'pending'
   ORDER BY `c`.`created_at` ASC
   LIMIT 1"
)->find();

$reviewPayload = [
  'author_name' => '—',
  'author_email' => '—',
  'post_title' => '—',
  'status' => 'pending',
  'body' => 'No comments are awaiting moderation.',
];

if (is_array($reviewComment)) {
  $reviewPayload = [
    'author_name' => trim((string) ($reviewComment['author_name'] ?? '')) ?: '—',
    'author_email' => trim((string) ($reviewComment['author_email'] ?? '')) ?: '—',
    'post_title' => trim((string) ($reviewComment['post_title'] ?? '')) ?: '—',
    'status' => (string) ($reviewComment['status'] ?? 'pending'),
    'body' => trim((string) ($reviewComment['body'] ?? '')) ?: '—',
  ];
}

$reviewCommentPayloadJson = json_encode($reviewPayload, JSON_UNESCAPED_UNICODE);
if ($reviewCommentPayloadJson === false) {
  $reviewCommentPayloadJson = '{}';
}

$commentsUrl = blog_url('dashboard/comments');
$postsUrl = blog_url('dashboard/posts');

$dayCount = 30;
$today = new DateTimeImmutable('today');
$dateKeys = [];
$dailyLabels = [];
for ($i = $dayCount - 1; $i >= 0; $i--) {
  $d = $today->sub(new DateInterval('P' . $i . 'D'));
  $dateKeys[] = $d->format('Y-m-d');
  $dailyLabels[] = blog_format_localized_date($d, 'chart_day');
}
$chartStartDate = $dateKeys[0];

$dailyComments = array_fill(0, $dayCount, 0);
$commentDailyRows = $db->query(
  'SELECT DATE(`created_at`) AS `d`, COUNT(*) AS `n` FROM `comments` WHERE `created_at` >= ? GROUP BY DATE(`created_at`)',
  [$chartStartDate . ' 00:00:00']
)->get();
$dateToIdx = array_flip($dateKeys);
foreach ($commentDailyRows as $row) {
  $d = (string) ($row['d'] ?? '');
  if ($d !== '' && isset($dateToIdx[$d])) {
    $dailyComments[(int) $dateToIdx[$d]] = (int) ($row['n'] ?? 0);
  }
}

$dailyPublished = array_fill(0, $dayCount, 0);
$publishedDailyRows = $db->query(
  'SELECT `published_at` AS `d`, COUNT(*) AS `n` FROM `posts` WHERE `status` = \'published\' AND `published_at` IS NOT NULL AND `published_at` >= ? GROUP BY `published_at`',
  [$chartStartDate]
)->get();
foreach ($publishedDailyRows as $row) {
  $d = (string) ($row['d'] ?? '');
  if ($d !== '' && isset($dateToIdx[$d])) {
    $dailyPublished[(int) $dateToIdx[$d]] = (int) ($row['n'] ?? 0);
  }
}

$postStatusRows = $db->query('SELECT `status`, COUNT(*) AS `n` FROM `posts` GROUP BY `status`')->get();
$postStatusMap = [];
foreach ($postStatusRows as $row) {
  $postStatusMap[(string) ($row['status'] ?? '')] = (int) ($row['n'] ?? 0);
}
$postStatusChart = [
  'labels' => ['Draft', 'Published', 'Scheduled'],
  'data' => [
    $postStatusMap['draft'] ?? 0,
    $postStatusMap['published'] ?? 0,
    $postStatusMap['scheduled'] ?? 0,
  ],
  'colors' => ['#d6d3d1', '#10b981', '#fbbf24'],
];

$commentStatusRows = $db->query('SELECT `status`, COUNT(*) AS `n` FROM `comments` GROUP BY `status`')->get();
$commentStatusMap = [];
foreach ($commentStatusRows as $row) {
  $commentStatusMap[(string) ($row['status'] ?? '')] = (int) ($row['n'] ?? 0);
}
$commentStatusChart = [
  'labels' => ['Pending', 'Approved', 'Spam', 'Rejected'],
  'data' => [
    $commentStatusMap['pending'] ?? 0,
    $commentStatusMap['approved'] ?? 0,
    $commentStatusMap['spam'] ?? 0,
    $commentStatusMap['rejected'] ?? 0,
  ],
  'colors' => ['#fbbf24', '#34d399', '#f87171', '#a8a29e'],
];

$chartData = [
  'dailyLabels' => $dailyLabels,
  'dailyComments' => $dailyComments,
  'dailyPublished' => $dailyPublished,
  'postStatus' => $postStatusChart,
  'commentStatus' => $commentStatusChart,
];

$chartDataJson = json_encode($chartData, JSON_UNESCAPED_UNICODE);
if ($chartDataJson === false) {
  $chartDataJson = '{}';
}
$chartSummaryText = sprintf(
  'Last %d days: %d new comments, %d posts published (by publish date).',
  $dayCount,
  array_sum($dailyComments),
  array_sum($dailyPublished)
);

view('dashboard/index.view.php', [
  'pageTitle' => 'Dashboard — Mini Blog',
  'heading' => 'Overview',
  'subheading' => 'Snapshot of posts, comments, and taxonomy from your database.',
  'metaDescription' => '',
  'dashboardNav' => 'overview',
  'stats' => $stats,
  'activity' => $activity,
  'reviewCommentPayloadJson' => $reviewCommentPayloadJson,
  'commentsUrl' => $commentsUrl,
  'postsUrl' => $postsUrl,
  'users' => $usersForModals,
  'categories' => $categoriesForModals,
  'currentUserId' => $sessionUserId,
  'canManageAllPosts' => $canManageAllPosts,
  'csrfToken' => auth_csrf_token(),
  'redirectQuery' => '',
  'chartDataJson' => $chartDataJson,
  'chartSummaryText' => $chartSummaryText,
]);
