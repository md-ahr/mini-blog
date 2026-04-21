<?php

function dd($value)
{
  echo '<pre>';
  var_dump($value);
  echo '</pre>';
}

function abort($code = 404)
{
  http_response_code($code);
  require_once base_path("views/{$code}.php");
  die();
}

function base_path($path): string
{
  return BASE_PATH . $path;
}

/**
 * Public URL path (respects BLOG_BASE_PATH when the app lives in a subdirectory).
 */
function blog_url(string $path = ''): string
{
  $base = defined('BLOG_BASE_PATH') ? rtrim((string) BLOG_BASE_PATH, '/') : '';
  $path = '/' . ltrim($path, '/');
  if ($path === '/') {
    return $base === '' ? '/' : $base . '/';
  }
  return $base . $path;
}

/**
 * Request path relative to BLOG_BASE_PATH (for active nav).
 */
function blog_current_path(): string
{
  $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
  if (defined('BLOG_BASE_PATH')) {
    $prefix = rtrim((string) BLOG_BASE_PATH, '/');
    if ($prefix !== '' && str_starts_with($path, $prefix)) {
      $path = substr($path, strlen($prefix)) ?: '/';
    }
  }
  $path = '/' . ltrim($path, '/');
  return $path === '//' ? '/' : $path;
}

/**
 * Blog listing path segment: "blog" or "blogs" (matches current URL when possible).
 */
function blogs_archive_base_path(): string
{
  $path = blog_current_path();
  if ($path === '/blog' || str_starts_with($path, '/blog/')) {
    return 'blog';
  }
  return 'blogs';
}

/**
 * URL for the blog archive index (respects /blog vs /blogs from the active request).
 */
function blogs_archive_url(): string
{
  return blog_url(blogs_archive_base_path());
}

/**
 * Named parameters from the last matched dynamic route (e.g. :slug).
 */
function route_param(string $key, $default = null)
{
  return \Core\Router::$routeParams[$key] ?? $default;
}

function blog_post_url(string $slug): string
{
  $base = blogs_archive_base_path();
  return blog_url($base . '/' . ltrim($slug, '/'));
}

/**
 * Normalize a post row from MySQL (expects `published_at` and optional `author` from JOIN).
 *
 * @param array<string, mixed> $row
 * @return array<string, mixed>
 */
function blog_post_from_db_row(array $row): array
{
  $published = (string) ($row['published_at'] ?? '');
  $dt = date_create($published ?: 'now') ?: new DateTimeImmutable();
  $tags = [];
  if (isset($row['_tags']) && is_array($row['_tags'])) {
    foreach ($row['_tags'] as $t) {
      if (!is_array($t)) {
        continue;
      }
      $tags[] = [
        'name' => (string) ($t['name'] ?? ''),
        'slug' => (string) ($t['slug'] ?? ''),
        'color' => blog_sanitize_color($t['color'] ?? null),
      ];
    }
  }
  $firstTag = $tags[0]['name'] ?? '';
  if ($firstTag === '' && isset($row['tag'])) {
    $firstTag = trim((string) $row['tag']);
  }
  $authorAvatar = trim((string) ($row['author_avatar_url'] ?? ''));
  $authorAvatarAlt = trim((string) ($row['author_avatar_alt'] ?? ''));
  $authorName = (string) ($row['author'] ?? '');
  $updatedRaw = $row['updated_at'] ?? null;
  $updatedDt = $updatedRaw ? date_create((string) $updatedRaw) : false;
  $updatedDisplay = $updatedDt instanceof DateTimeInterface ? blog_format_localized_date($updatedDt, 'date') : '';
  $updatedIso = $updatedDt instanceof DateTimeInterface ? $updatedDt->format('c') : '';
  $publishedDt = $published !== '' ? date_create($published) : false;
  $showUpdated = false;
  if ($updatedDt instanceof DateTimeInterface && $publishedDt instanceof DateTimeInterface) {
    $showUpdated = $updatedDt->format('Y-m-d') !== $publishedDt->format('Y-m-d');
  }
  $out = [
    'slug' => $row['slug'],
    'title' => $row['title'],
    'excerpt' => $row['excerpt'],
    'tag' => $firstTag,
    'tags' => $tags,
    'category' => isset($row['category_name']) ? trim((string) $row['category_name']) : '',
    'category_slug' => isset($row['category_slug']) ? trim((string) $row['category_slug']) : '',
    'author' => $authorName,
    'authorAvatarUrl' => $authorAvatar !== '' ? $authorAvatar : null,
    'authorAvatarAlt' => $authorAvatarAlt !== '' ? $authorAvatarAlt : ($authorName !== '' ? $authorName : 'Author'),
    'authorBio' => trim((string) ($row['author_bio'] ?? '')),
    'dateIso' => $published,
    'dateDisplay' => blog_format_localized_date($dt, 'date'),
    'readingMinutes' => (int) ($row['reading_minutes'] ?? 0),
    'featuredImageUrl' => isset($row['featured_image_url']) && $row['featured_image_url'] !== null && $row['featured_image_url'] !== ''
      ? (string) $row['featured_image_url']
      : null,
    'updatedAtIso' => $updatedIso,
    'updatedAtDisplay' => $updatedDisplay,
    'showUpdatedDate' => $showUpdated,
    'content' => [],
  ];
  if (array_key_exists('content', $row) && $row['content'] !== null && $row['content'] !== '') {
    $raw = $row['content'];
    $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
    $out['content'] = is_array($decoded) ? $decoded : [];
  }
  return $out;
}

/**
 * Two-letter style initials for author byline fallback.
 */
function blog_author_initials(string $name): string
{
  $name = trim($name);
  if ($name === '') {
    return '?';
  }
  $parts = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY);
  if ($parts !== false && count($parts) >= 2) {
    return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1), 'UTF-8');
  }
  return mb_strtoupper(mb_substr($name, 0, min(2, mb_strlen($name))), 'UTF-8');
}

/**
 * Short relative label for dashboard timelines (e.g. "2 hours ago", "Mar 4, 2026").
 */
function blog_short_relative_time(?string $mysqlDatetime): string
{
  if ($mysqlDatetime === null || $mysqlDatetime === '') {
    return '—';
  }
  $dt = date_create_immutable($mysqlDatetime);
  if (!$dt) {
    return '—';
  }
  $now = new DateTimeImmutable('now');
  $sec = $now->getTimestamp() - $dt->getTimestamp();
  if ($sec < 0) {
    return blog_format_localized_date($dt, 'date', true);
  }
  if ($sec < 60) {
    return 'Just now';
  }
  if ($sec < 3600) {
    $n = (int) floor($sec / 60);
    return $n === 1 ? '1 min ago' : $n . ' min ago';
  }
  if ($sec < 86400) {
    $n = (int) floor($sec / 3600);
    return $n === 1 ? '1 hour ago' : $n . ' hours ago';
  }
  if ($sec < 7 * 86400) {
    $n = (int) floor($sec / 86400);
    return $n === 1 ? '1 day ago' : $n . ' days ago';
  }
  return blog_format_localized_date($dt, 'date', true);
}

/**
 * Absolute URL for Open Graph / sharing when $url may be protocol-relative or path-only.
 */
function blog_absolute_url(string $pathOrUrl): string
{
  $pathOrUrl = trim($pathOrUrl);
  if ($pathOrUrl === '') {
    return '';
  }
  if (preg_match('#^https?://#i', $pathOrUrl)) {
    return $pathOrUrl;
  }
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
  $path = str_starts_with($pathOrUrl, '/') ? $pathOrUrl : '/' . $pathOrUrl;
  return $scheme . '://' . $host . $path;
}

/**
 * URL-safe slug from a title or name (max 191 chars).
 */
function blog_slugify(string $text): string
{
  $text = trim($text);
  if ($text === '') {
    return '';
  }
  $lower = mb_strtolower($text, 'UTF-8');
  $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $lower);
  if ($ascii !== false) {
    $lower = $ascii;
  }
  $lower = strtolower($lower);
  $slug = preg_replace('/[^a-z0-9]+/', '-', $lower) ?? '';
  $slug = trim((string) $slug, '-');
  if ($slug === '') {
    return '';
  }
  return mb_substr($slug, 0, 191);
}

/**
 * Normalize #RRGGBB for categories/tags.
 */
function blog_sanitize_color(?string $raw, string $default = '#78716c'): string
{
  $s = trim((string) $raw);
  if (preg_match('/^#[0-9a-fA-F]{6}$/', $s)) {
    return strtolower($s);
  }
  return $default;
}

/**
 * @param 'tags'|'categories' $table
 */
function blog_unique_slug(\Core\Database $db, string $table, string $baseSlug, ?int $excludeId = null): string
{
  if ($table !== 'tags' && $table !== 'categories' && $table !== 'posts') {
    throw new InvalidArgumentException('Invalid table for slug.');
  }
  $slug = blog_slugify($baseSlug);
  if ($slug === '') {
    $slug = 'item';
  }
  $candidate = $slug;
  $n = 2;
  while (true) {
    if ($excludeId !== null) {
      $found = $db->query(
        "SELECT `id` FROM `$table` WHERE `slug` = ? AND `id` != ? LIMIT 1",
        [$candidate, $excludeId]
      )->find();
    } else {
      $found = $db->query(
        "SELECT `id` FROM `$table` WHERE `slug` = ? LIMIT 1",
        [$candidate]
      )->find();
    }
    if (!$found) {
      return $candidate;
    }
    $suffix = '-' . $n;
    $candidate = mb_substr($slug, 0, 191 - mb_strlen($suffix)) . $suffix;
    $n++;
  }
}

/**
 * Default site settings (used when a key is missing from the database).
 *
 * @return array<string, string>
 */
function blog_settings_defaults(): array
{
  return [
    'site_title' => 'Mini Blog',
    'site_tagline' => 'Notes & long-form',
    'posts_per_page' => '12',
    'date_format' => 'M j, Y',
    'site_locale' => 'en_US',
    'rss_enabled' => '1',
    'homepage_display' => 'latest_posts',
    'homepage_static_slug' => '',
    'comments_enabled' => '1',
    'comments_require_moderation' => '1',
    'comments_close_after_30_days' => '0',
  ];
}

/**
 * Site settings from `settings` merged with defaults.
 *
 * @return array<string, string>
 */
function blog_settings_map(\Core\Database $db): array
{
  if (isset($GLOBALS['_blog_settings_map_cache']) && is_array($GLOBALS['_blog_settings_map_cache'])) {
    return $GLOBALS['_blog_settings_map_cache'];
  }
  $out = blog_settings_defaults();
  try {
    $rows = $db->query('SELECT `setting_key`, `setting_value` FROM `settings`')->get();
  } catch (\Throwable) {
    $GLOBALS['_blog_settings_map_cache'] = $out;
    return $out;
  }
  foreach ($rows as $r) {
    $k = (string) ($r['setting_key'] ?? '');
    if ($k !== '') {
      $out[$k] = (string) ($r['setting_value'] ?? '');
    }
  }
  $GLOBALS['_blog_settings_map_cache'] = $out;
  return $out;
}

function blog_settings_set(\Core\Database $db, string $key, string $value): void
{
  $db->query(
    'INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`)',
    [$key, $value]
  );
  unset($GLOBALS['_blog_settings_map_cache']);
}

/**
 * Normalize locale tag for ICU (e.g. en-US → en_US).
 */
function blog_normalize_site_locale(string $raw): string
{
  $raw = trim(str_replace('-', '_', $raw));
  if ($raw === '') {
    return 'en_US';
  }
  if (extension_loaded('intl')) {
    $c = \Locale::canonicalize($raw);
    if (is_string($c) && $c !== '') {
      return $c;
    }
  }
  if (preg_match('/^[a-z]{2}_[A-Za-z0-9]{2,}$/', $raw)) {
    return $raw;
  }
  return 'en_US';
}

/**
 * Format a date/time for display using Settings → date format + site locale.
 *
 * @param 'date'|'datetime'|'month_year'|'chart_day' $kind
 * @param bool $forceAbsolute When true, never use the "relative" setting (avoids recursion from blog_short_relative_time).
 */
function blog_format_localized_date(?\DateTimeInterface $dt, string $kind = 'date', bool $forceAbsolute = false): string
{
  if ($dt === null) {
    return '—';
  }
  try {
    $db = \Core\App::resolve(\Core\Database::class);
    $map = blog_settings_map($db);
  } catch (\Throwable) {
    return $dt->format($kind === 'datetime' ? 'M j, Y g:i a' : 'M j, Y');
  }

  $locale = blog_normalize_site_locale((string) ($map['site_locale'] ?? 'en_US'));
  $mode = (string) ($map['date_format'] ?? 'M j, Y');
  $tz = $dt->getTimezone();
  $tzStr = $tz->getName();

  if ($kind === 'chart_day') {
    if (extension_loaded('intl')) {
      $fmt = new \IntlDateFormatter(
        $locale,
        \IntlDateFormatter::NONE,
        \IntlDateFormatter::NONE,
        $tzStr,
        \IntlDateFormatter::GREGORIAN,
        'MMM d'
      );
      $out = $fmt->format($dt);
      return $out !== false ? (string) $out : $dt->format('M j');
    }
    return $dt->format('M j');
  }

  if ($mode === 'relative') {
    if ($kind === 'month_year') {
      $mode = 'M j, Y';
    } elseif (in_array($kind, ['date', 'datetime'], true)) {
      if (!$forceAbsolute) {
        return blog_short_relative_time($dt->format('Y-m-d H:i:s'));
      }
      $mode = 'M j, Y';
    }
  }

  if ($kind === 'datetime') {
    if (extension_loaded('intl')) {
      $fmt = new \IntlDateFormatter(
        $locale,
        $mode === 'Y-m-d' ? \IntlDateFormatter::SHORT : \IntlDateFormatter::MEDIUM,
        \IntlDateFormatter::SHORT,
        $tzStr,
        \IntlDateFormatter::GREGORIAN
      );
      if ($mode === 'Y-m-d') {
        $fmt->setPattern('yyyy-MM-dd HH:mm');
      }
      $out = $fmt->format($dt);
      return $out !== false ? (string) $out : $dt->format('Y-m-d H:i');
    }
    return $mode === 'Y-m-d' ? $dt->format('Y-m-d H:i') : $dt->format('M j, Y g:i a');
  }

  if ($kind === 'month_year') {
    if (extension_loaded('intl')) {
      $fmt = new \IntlDateFormatter(
        $locale,
        \IntlDateFormatter::NONE,
        \IntlDateFormatter::NONE,
        $tzStr,
        \IntlDateFormatter::GREGORIAN,
        'LLL y'
      );
      $out = $fmt->format($dt);
      return $out !== false ? (string) $out : $dt->format('M Y');
    }
    return $dt->format('M Y');
  }

  // kind === 'date'
  if (extension_loaded('intl')) {
    if ($mode === 'Y-m-d') {
      $fmt = new \IntlDateFormatter(
        $locale,
        \IntlDateFormatter::NONE,
        \IntlDateFormatter::NONE,
        $tzStr,
        \IntlDateFormatter::GREGORIAN,
        'yyyy-MM-dd'
      );
    } else {
      $fmt = new \IntlDateFormatter(
        $locale,
        \IntlDateFormatter::MEDIUM,
        \IntlDateFormatter::NONE,
        $tzStr,
        \IntlDateFormatter::GREGORIAN
      );
    }
    $out = $fmt->format($dt);
    return $out !== false ? (string) $out : $dt->format('M j, Y');
  }

  return blog_format_localized_date_fallback($dt, $locale, $mode, 'date', $tzStr);
}

/**
 * @param 'date'|'chart_day' $kind
 */
function blog_format_localized_date_fallback(
  \DateTimeInterface $dt,
  string $locale,
  string $mode,
  string $kind,
  string $tzStr
): string {
  if ($mode === 'Y-m-d') {
    return $dt->format('Y-m-d');
  }
  if ($kind === 'chart_day') {
    return $dt->format('M j');
  }
  return $dt->format('M j, Y');
}

/**
 * @return array<int, list<array{name: string, slug: string, color: string}>>
 */
function blog_tags_by_post_ids(\Core\Database $db, array $postIds): array
{
  $postIds = array_values(array_unique(array_filter(array_map(static fn ($v): int => (int) $v, $postIds))));
  if ($postIds === []) {
    return [];
  }
  $placeholders = implode(',', array_fill(0, count($postIds), '?'));
  $rows = $db->query(
    "SELECT `pt`.`post_id`, `t`.`name`, `t`.`slug`, `t`.`color`
     FROM `post_tag` `pt`
     INNER JOIN `tags` `t` ON `t`.`id` = `pt`.`tag_id`
     WHERE `pt`.`post_id` IN ($placeholders)
     ORDER BY `t`.`name` ASC",
    $postIds
  )->get();
  $map = [];
  foreach ($rows as $r) {
    $pid = (int) ($r['post_id'] ?? 0);
    if ($pid < 1) {
      continue;
    }
    if (!isset($map[$pid])) {
      $map[$pid] = [];
    }
    $map[$pid][] = [
      'name' => (string) ($r['name'] ?? ''),
      'slug' => (string) ($r['slug'] ?? ''),
      'color' => blog_sanitize_color($r['color'] ?? null),
    ];
  }
  return $map;
}

/**
 * Convert article body textarea (paragraphs separated by blank lines) to JSON blocks for `posts.content`.
 */
function blog_post_body_to_content_json(string $raw): string
{
  $raw = str_replace("\r\n", "\n", $raw);
  $raw = trim($raw);
  if ($raw === '') {
    return json_encode([['type' => 'p', 'text' => '']], JSON_UNESCAPED_UNICODE);
  }
  $chunks = preg_split('/\n\s*\n/', $raw, -1, PREG_SPLIT_NO_EMPTY);
  if ($chunks === false || $chunks === []) {
    $chunks = [$raw];
  }
  $blocks = [];
  foreach ($chunks as $c) {
    $t = trim((string) $c);
    if ($t !== '') {
      $blocks[] = ['type' => 'p', 'text' => $t];
    }
  }
  if ($blocks === []) {
    $blocks[] = ['type' => 'p', 'text' => trim($raw)];
  }
  return json_encode($blocks, JSON_UNESCAPED_UNICODE);
}

/**
 * @param mixed $contentJson string from DB or decoded array
 */
function blog_post_content_to_body_text(mixed $contentJson): string
{
  if (is_string($contentJson)) {
    $decoded = json_decode($contentJson, true);
  } else {
    $decoded = $contentJson;
  }
  if (!is_array($decoded)) {
    return '';
  }
  $parts = [];
  foreach ($decoded as $block) {
    if (!is_array($block)) {
      continue;
    }
    $type = (string) ($block['type'] ?? '');
    if ($type === 'p') {
      $parts[] = (string) ($block['text'] ?? '');
    }
  }
  return implode("\n\n", array_filter($parts, static fn (string $p): bool => $p !== ''));
}

const BLOG_FEATURED_IMAGE_MAX_BYTES = 2097152;

function blog_featured_uploads_dir(): string
{
  return base_path('public/uploads/featured');
}

/**
 * @param array{name?: string, type?: string, tmp_name?: string, error?: int, size?: int} $file
 * @return array{ok: true, url: string}|array{ok: false, error: string}
 */
function blog_featured_image_store_upload(array $file): array
{
  $err = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
  if ($err === UPLOAD_ERR_NO_FILE) {
    return ['ok' => false, 'error' => 'no_file'];
  }
  if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
    return ['ok' => false, 'error' => 'too_large'];
  }
  if ($err !== UPLOAD_ERR_OK) {
    return ['ok' => false, 'error' => 'failed'];
  }
  $tmp = (string) ($file['tmp_name'] ?? '');
  if ($tmp === '' || !is_uploaded_file($tmp)) {
    return ['ok' => false, 'error' => 'failed'];
  }
  $size = (int) ($file['size'] ?? 0);
  if ($size < 1 || $size > BLOG_FEATURED_IMAGE_MAX_BYTES) {
    return ['ok' => false, 'error' => 'too_large'];
  }
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime = $finfo->file($tmp);
  $extMap = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'image/gif' => 'gif',
  ];
  if (!is_string($mime) || !isset($extMap[$mime])) {
    return ['ok' => false, 'error' => 'invalid'];
  }
  $ext = $extMap[$mime];
  $dir = blog_featured_uploads_dir();
  if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
    return ['ok' => false, 'error' => 'failed'];
  }
  $basename = 'f_' . bin2hex(random_bytes(8)) . '.' . $ext;
  $dest = $dir . '/' . $basename;
  if (!move_uploaded_file($tmp, $dest)) {
    return ['ok' => false, 'error' => 'failed'];
  }
  return ['ok' => true, 'url' => blog_url('uploads/featured/' . $basename)];
}

/**
 * Replace post_tag rows from a comma-separated list of tag names (creates missing tags).
 */
function blog_post_sync_tags(\Core\Database $db, int $postId, string $tagListRaw): void
{
  $db->query('DELETE FROM `post_tag` WHERE `post_id` = ?', [$postId]);
  $parts = preg_split('/\s*,\s*/', trim($tagListRaw), -1, PREG_SPLIT_NO_EMPTY);
  $seen = [];
  foreach ($parts as $piece) {
    $name = mb_substr(trim($piece), 0, 191);
    if ($name === '') {
      continue;
    }
    $key = mb_strtolower($name, 'UTF-8');
    if (isset($seen[$key])) {
      continue;
    }
    $seen[$key] = true;
    $slugTry = blog_slugify($name);
    $tagRow = $db->query('SELECT `id` FROM `tags` WHERE `name` = ? LIMIT 1', [$name])->find();
    if (!$tagRow && $slugTry !== '') {
      $tagRow = $db->query('SELECT `id` FROM `tags` WHERE `slug` = ? LIMIT 1', [$slugTry])->find();
    }
    if (!$tagRow) {
      $slug = blog_unique_slug($db, 'tags', $name);
      $db->query(
        'INSERT INTO `tags` (`name`, `slug`, `color`) VALUES (?, ?, ?)',
        [$name, $slug, '#78716c']
      );
      $tagRow = $db->query('SELECT `id` FROM `tags` WHERE `slug` = ? LIMIT 1', [$slug])->find();
    }
    if (!$tagRow) {
      continue;
    }
    $tagId = (int) $tagRow['id'];
    $db->query(
      'INSERT IGNORE INTO `post_tag` (`post_id`, `tag_id`) VALUES (?, ?)',
      [$postId, $tagId]
    );
  }
}

/**
 * @param list<array<string, mixed>> $rows post rows including `id`
 * @return list<array<string, mixed>>
 */
function blog_posts_with_tags(\Core\Database $db, array $rows): array
{
  if ($rows === []) {
    return [];
  }
  $ids = [];
  foreach ($rows as $r) {
    if (isset($r['id'])) {
      $ids[] = (int) $r['id'];
    }
  }
  $tagMap = blog_tags_by_post_ids($db, $ids);
  $out = [];
  foreach ($rows as $r) {
    $id = isset($r['id']) ? (int) $r['id'] : 0;
    $r['_tags'] = $tagMap[$id] ?? [];
    unset($r['tag']);
    $out[] = $r;
  }
  return $out;
}

/**
 * Build blog archive URL (/blogs or /blog) with optional tag, category, search (q), page.
 *
 * @param array{tag?: string, category?: string, q?: string, page?: int} $query
 */
function blogs_index_url(array $query = []): string
{
  $params = [];
  $tag = trim((string) ($query['tag'] ?? ''));
  if ($tag !== '') {
    $params['tag'] = $tag;
  }
  $category = trim((string) ($query['category'] ?? ''));
  if ($category !== '') {
    $params['category'] = $category;
  }
  $q = trim((string) ($query['q'] ?? ''));
  if ($q !== '') {
    $params['q'] = $q;
  }
  $page = isset($query['page']) ? (int) $query['page'] : 1;
  if ($page > 1) {
    $params['page'] = $page;
  }
  $base = blog_url(blogs_archive_base_path());
  return $params === [] ? $base : $base . '?' . http_build_query($params);
}

function auth_session_bootstrap(): void
{
  if (session_status() !== PHP_SESSION_NONE) {
    return;
  }
  $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
  session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $secure,
    'httponly' => true,
    'samesite' => 'Lax',
  ]);
  session_start();
}

/**
 * @return array{id: int, name: string, email: string, role: string, avatar_url?: string|null, avatar_alt?: string|null}|null
 */
function auth_user(): ?array
{
  $u = $_SESSION['user'] ?? null;
  if (!is_array($u) || !isset($u['id'], $u['email'])) {
    return null;
  }
  return $u;
}

/**
 * Neutral placeholder avatar (SVG data URI) when the user has no profile image.
 */
function auth_default_avatar_data_uri(): string
{
  $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none"><rect width="64" height="64" rx="20" fill="#e7e5e4"/><circle cx="32" cy="22" r="11" fill="#a8a29e"/><path d="M14 54c0-10 8-18 18-18s18 8 18 18" stroke="#78716c" stroke-width="3" stroke-linecap="round"/></svg>';

  return 'data:image/svg+xml,' . rawurlencode($svg);
}

/**
 * @param array<string, mixed>|null $user
 */
function auth_user_avatar_src(?array $user): string
{
  if ($user === null) {
    return auth_default_avatar_data_uri();
  }
  $url = trim((string) ($user['avatar_url'] ?? ''));
  if ($url !== '') {
    return $url;
  }
  return auth_default_avatar_data_uri();
}

/**
 * @param array<string, mixed>|null $user
 */
function auth_user_avatar_alt(?array $user): string
{
  if ($user === null) {
    return 'User';
  }
  $alt = trim((string) ($user['avatar_alt'] ?? ''));
  if ($alt !== '') {
    return $alt;
  }
  $name = trim((string) ($user['name'] ?? ''));
  if ($name !== '') {
    return $name;
  }
  return 'Profile photo';
}

function auth_check(): bool
{
  return auth_user() !== null;
}

function auth_csrf_token(): string
{
  auth_session_bootstrap();
  if (empty($_SESSION['_csrf'])) {
    $_SESSION['_csrf'] = bin2hex(random_bytes(32));
  }
  return (string) $_SESSION['_csrf'];
}

function auth_csrf_validate(?string $token): bool
{
  auth_session_bootstrap();
  if ($token === null || $token === '' || empty($_SESSION['_csrf'])) {
    return false;
  }
  return hash_equals((string) $_SESSION['_csrf'], $token);
}

/**
 * Safe path segment after login (e.g. dashboard/posts). Empty = use default.
 */
function auth_login_read_next(string $raw): string
{
  $raw = trim($raw);
  if ($raw === '') {
    return '';
  }
  if (!preg_match('#^[a-z][a-z0-9/-]*$#i', $raw)) {
    return '';
  }
  return $raw;
}

function auth_login_redirect_path(string $next): string
{
  $n = auth_login_read_next($next);
  return $n === '' ? 'dashboard' : $n;
}

function auth_require_user(): void
{
  auth_session_bootstrap();
  if (auth_check()) {
    return;
  }
  $next = ltrim(blog_current_path(), '/');
  if ($next === '' || str_starts_with($next, 'login')) {
    $next = 'dashboard';
  }
  redirect(blog_url('login') . '?next=' . rawurlencode($next));
}

/**
 * Dashboard routes that manage accounts (e.g. users list) — owner role only.
 */
function auth_require_owner(): void
{
  auth_require_user();
  $u = auth_user();
  if (($u['role'] ?? '') !== 'owner') {
    abort(403);
  }
}

/**
 * @return int Number of users with role owner (any status).
 */
function blog_users_count_owners(\Core\Database $db): int
{
  return (int) ($db->query('SELECT COUNT(*) AS `n` FROM `users` WHERE `role` = \'owner\'')->find()['n'] ?? 0);
}

/**
 * @return int Users who are owners and active (can sign in).
 */
function blog_users_count_active_owners(\Core\Database $db): int
{
  return (int) ($db->query(
    'SELECT COUNT(*) AS `n` FROM `users` WHERE `role` = \'owner\' AND `status` = \'active\''
  )->find()['n'] ?? 0);
}

function auth_logout(): void
{
  auth_session_bootstrap();
  $_SESSION = [];
  if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
  }
}

/**
 * Merge updated profile fields into the signed-in session user.
 *
 * @param array<string, mixed> $patch name, email, avatar_url, avatar_alt, role (optional)
 */
function auth_sync_session_profile(array $patch): void
{
  auth_session_bootstrap();
  if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
    return;
  }
  foreach (['name', 'email', 'role', 'avatar_url', 'avatar_alt'] as $key) {
    if (!array_key_exists($key, $patch)) {
      continue;
    }
    $val = $patch[$key];
    if ($key === 'avatar_url' || $key === 'avatar_alt') {
      $val = $val !== null && trim((string) $val) !== '' ? trim((string) $val) : null;
    }
    $_SESSION['user'][$key] = $val;
  }
}

/**
 * @param array<string, mixed> $row users table row
 * @return array<string, mixed>
 */
const PROFILE_AVATAR_MAX_BYTES = 2097152;

/**
 * Filesystem directory for stored profile avatars (public web root).
 */
function profile_avatar_uploads_dir(): string
{
  return base_path('public/uploads/avatars');
}

/**
 * Whether the stored avatar URL points at a file this app uploaded for the user.
 */
function profile_avatar_is_managed_upload(int $userId, ?string $avatarUrl): bool
{
  if ($avatarUrl === null || $avatarUrl === '') {
    return false;
  }
  $path = parse_url($avatarUrl, PHP_URL_PATH);
  if (!is_string($path) || $path === '') {
    $path = $avatarUrl;
  }
  if (!str_contains($path, '/uploads/avatars/')) {
    return false;
  }
  $basename = basename($path);
  return (bool) preg_match('/^' . preg_quote((string) $userId, '/') . '_[a-f0-9]{16}\.(jpe?g|png|gif|webp)$/i', $basename);
}

/**
 * Remove a previously stored avatar file from disk if it belongs to this user.
 */
function profile_avatar_delete_managed_file(int $userId, ?string $avatarUrl): void
{
  if (!profile_avatar_is_managed_upload($userId, $avatarUrl)) {
    return;
  }
  $path = parse_url((string) $avatarUrl, PHP_URL_PATH);
  if (!is_string($path) || $path === '') {
    $path = (string) $avatarUrl;
  }
  $basename = basename($path);
  $full = profile_avatar_uploads_dir() . '/' . $basename;
  if (is_file($full)) {
    @unlink($full);
  }
}

/**
 * Validate and store an uploaded profile image; returns a public URL path for `avatar_url`.
 *
 * @param array{name?: string, type?: string, tmp_name?: string, error?: int, size?: int} $file
 * @return array{ok: true, url: string}|array{ok: false, error: string}
 */
function profile_avatar_store_from_upload(int $userId, array $file): array
{
  $err = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
  if ($err === UPLOAD_ERR_NO_FILE) {
    return ['ok' => false, 'error' => 'upload_no_file'];
  }
  if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
    return ['ok' => false, 'error' => 'upload_too_large'];
  }
  if ($err !== UPLOAD_ERR_OK) {
    return ['ok' => false, 'error' => 'upload_failed'];
  }
  $tmp = (string) ($file['tmp_name'] ?? '');
  if ($tmp === '' || !is_uploaded_file($tmp)) {
    return ['ok' => false, 'error' => 'upload_failed'];
  }
  $size = (int) ($file['size'] ?? 0);
  if ($size < 1 || $size > PROFILE_AVATAR_MAX_BYTES) {
    return ['ok' => false, 'error' => 'upload_too_large'];
  }

  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime = $finfo->file($tmp);
  $extMap = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'image/gif' => 'gif',
  ];
  if (!is_string($mime) || !isset($extMap[$mime])) {
    return ['ok' => false, 'error' => 'upload_invalid'];
  }
  $ext = $extMap[$mime];

  $dir = profile_avatar_uploads_dir();
  if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
    return ['ok' => false, 'error' => 'upload_failed'];
  }

  $basename = $userId . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
  $dest = $dir . '/' . $basename;
  if (!move_uploaded_file($tmp, $dest)) {
    return ['ok' => false, 'error' => 'upload_failed'];
  }

  return ['ok' => true, 'url' => blog_url('uploads/avatars/' . $basename)];
}

function profile_format_for_view(array $row): array
{
  $name = (string) ($row['name'] ?? '');
  $email = (string) ($row['email'] ?? '');
  $bio = (string) ($row['bio'] ?? '');
  $role = (string) ($row['role'] ?? 'author');
  $created = !empty($row['created_at']) ? date_create((string) $row['created_at']) : false;
  $last = !empty($row['last_login_at']) ? date_create((string) $row['last_login_at']) : false;

  $initials = '';
  $nameTrim = trim($name);
  if ($nameTrim !== '') {
    $parts = preg_split('/\s+/u', $nameTrim, -1, PREG_SPLIT_NO_EMPTY);
    if (count($parts) >= 2) {
      $initials = mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1));
    } elseif (mb_strlen($nameTrim) >= 2) {
      $initials = mb_strtoupper(mb_substr($nameTrim, 0, 2));
    } else {
      $initials = mb_strtoupper(mb_substr($nameTrim, 0, 1));
    }
  }
  if ($initials === '') {
    $initials = $email !== '' ? mb_strtoupper(mb_substr($email, 0, 1)) : '?';
  }

  return [
    'id' => (int) ($row['id'] ?? 0),
    'name' => $name,
    'email' => $email,
    'role' => $role,
    'role_display' => $role !== '' ? ucfirst(strtolower($role)) : 'Member',
    'bio' => $bio,
    'avatar_url' => trim((string) ($row['avatar_url'] ?? '')),
    'avatar_alt' => trim((string) ($row['avatar_alt'] ?? '')),
    'initials' => $initials,
    'member_since_display' => $created instanceof DateTimeInterface ? blog_format_localized_date($created, 'month_year') : '—',
    'last_login_display' => $last instanceof DateTimeInterface ? blog_format_localized_date($last, 'datetime') : 'Never',
    'avatar_src' => auth_user_avatar_src([
      'avatar_url' => trim((string) ($row['avatar_url'] ?? '')),
      'avatar_alt' => trim((string) ($row['avatar_alt'] ?? '')),
      'name' => $name,
      'email' => $email,
    ]),
    'avatar_alt_display' => auth_user_avatar_alt([
      'avatar_alt' => trim((string) ($row['avatar_alt'] ?? '')),
      'name' => $name,
      'email' => $email,
    ]),
  ];
}

function auth_attempt_login(string $email, string $password): bool
{
  $email = trim($email);
  if ($email === '' || $password === '') {
    return false;
  }

  try {
    $db = \Core\App::resolve(\Core\Database::class);
  } catch (\Throwable) {
    return false;
  }

  $row = $db->query(
    'SELECT `id`, `name`, `email`, `password_hash`, `role`, `status`, `avatar_url`, `avatar_alt` FROM `users` WHERE `email` = ? LIMIT 1',
    [$email]
  )->find();

  if (!$row || ($row['status'] ?? '') !== 'active') {
    return false;
  }

  if (!password_verify($password, (string) $row['password_hash'])) {
    return false;
  }

  auth_session_bootstrap();
  session_regenerate_id(true);
  $avatarUrl = $row['avatar_url'] ?? null;
  $avatarAlt = $row['avatar_alt'] ?? null;
  $_SESSION['user'] = [
    'id' => (int) $row['id'],
    'name' => (string) $row['name'],
    'email' => (string) $row['email'],
    'role' => (string) ($row['role'] ?? 'author'),
    'avatar_url' => $avatarUrl !== null && trim((string) $avatarUrl) !== '' ? trim((string) $avatarUrl) : null,
    'avatar_alt' => $avatarAlt !== null && trim((string) $avatarAlt) !== '' ? trim((string) $avatarAlt) : null,
  ];

  try {
    $db->query('UPDATE `users` SET `last_login_at` = CURRENT_TIMESTAMP WHERE `id` = ?', [(int) $row['id']]);
  } catch (\Throwable) {
    // ignore telemetry failure
  }

  return true;
}

function view($path, $attributes = [])
{
  extract($attributes);
  require_once base_path('views/' . $path);
}

function redirect($path)
{
  header('Location: ' . $path);
  exit();
}

function urlIs($value): bool
{
  return $_SERVER['REQUEST_URI'] === $value;
}
