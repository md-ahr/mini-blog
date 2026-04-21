-- Mini Blog — categories, tags, comments, settings, profile fields, post relations
-- Run after 001 (or use: php database/migrate.php from project root).

SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- ---------------------------------------------------------------------------
-- Categories (optional parent for nested menus later)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` BIGINT UNSIGNED DEFAULT NULL,
  `name` VARCHAR(191) NOT NULL,
  `slug` VARCHAR(191) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `color` CHAR(7) NOT NULL DEFAULT '#57534e',
  `sort_order` SMALLINT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_index` (`parent_id`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Tags (many-to-many with posts)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tags` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(191) NOT NULL,
  `slug` VARCHAR(191) NOT NULL,
  `color` CHAR(7) NOT NULL DEFAULT '#78716c',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tags_slug_unique` (`slug`),
  UNIQUE KEY `tags_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Post ↔ tag pivot
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `post_tag` (
  `post_id` BIGINT UNSIGNED NOT NULL,
  `tag_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`post_id`, `tag_id`),
  KEY `post_tag_tag_id_index` (`tag_id`),
  CONSTRAINT `post_tag_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `post_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Comments (guest or signed-in; optional threading)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `comments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED DEFAULT NULL,
  `parent_id` BIGINT UNSIGNED DEFAULT NULL,
  `author_name` VARCHAR(191) NOT NULL,
  `author_email` VARCHAR(191) NOT NULL,
  `body` TEXT NOT NULL,
  `status` ENUM('pending', 'approved', 'spam', 'rejected') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `comments_post_id_index` (`post_id`),
  KEY `comments_user_id_index` (`user_id`),
  KEY `comments_parent_id_index` (`parent_id`),
  KEY `comments_status_index` (`status`),
  CONSTRAINT `comments_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Site settings (key/value; value is plain text or JSON string)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(191) NOT NULL,
  `setting_value` TEXT NOT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_setting_key_unique` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Users: roles, status, profile (avatar + bio)
-- ---------------------------------------------------------------------------
ALTER TABLE `users`
  ADD COLUMN `role` ENUM('owner', 'editor', 'author', 'viewer') NOT NULL DEFAULT 'author' AFTER `email`,
  ADD COLUMN `status` ENUM('active', 'suspended') NOT NULL DEFAULT 'active' AFTER `role`,
  ADD COLUMN `avatar_url` VARCHAR(500) DEFAULT NULL AFTER `password_hash`,
  ADD COLUMN `avatar_alt` VARCHAR(191) DEFAULT NULL AFTER `avatar_url`,
  ADD COLUMN `bio` TEXT DEFAULT NULL AFTER `avatar_alt`,
  ADD COLUMN `last_login_at` TIMESTAMP NULL DEFAULT NULL AFTER `bio`;

-- ---------------------------------------------------------------------------
-- Migrate legacy single `posts.tag` into `tags` + `post_tag`
-- ---------------------------------------------------------------------------
INSERT IGNORE INTO `tags` (`name`, `slug`, `color`, `created_at`, `updated_at`)
SELECT DISTINCT
  TRIM(`tag`) AS `name`,
  LOWER(REPLACE(REPLACE(TRIM(`tag`), ' ', '-'), '/', '-')) AS `slug`,
  '#78716c',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP
FROM `posts`
WHERE `tag` IS NOT NULL AND TRIM(`tag`) <> '';

INSERT IGNORE INTO `post_tag` (`post_id`, `tag_id`)
SELECT `p`.`id`, `t`.`id`
FROM `posts` `p`
INNER JOIN `tags` `t` ON `t`.`name` = TRIM(`p`.`tag`)
WHERE `p`.`tag` IS NOT NULL AND TRIM(`p`.`tag`) <> '';

-- ---------------------------------------------------------------------------
-- Posts: category, status, featured image, schedule; drop legacy tag column
-- ---------------------------------------------------------------------------
ALTER TABLE `posts`
  ADD COLUMN `category_id` BIGINT UNSIGNED DEFAULT NULL AFTER `user_id`,
  ADD COLUMN `status` ENUM('draft', 'published', 'scheduled') NOT NULL DEFAULT 'published' AFTER `content`,
  ADD COLUMN `featured_image_url` VARCHAR(500) DEFAULT NULL AFTER `excerpt`,
  ADD COLUMN `scheduled_at` TIMESTAMP NULL DEFAULT NULL AFTER `published_at`;

ALTER TABLE `posts`
  MODIFY COLUMN `published_at` DATE DEFAULT NULL;

ALTER TABLE `posts`
  DROP INDEX `posts_tag_index`,
  DROP COLUMN `tag`;

ALTER TABLE `posts`
  ADD KEY `posts_category_id_index` (`category_id`),
  ADD KEY `posts_status_published_index` (`status`, `published_at`),
  ADD CONSTRAINT `posts_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- ---------------------------------------------------------------------------
-- Default settings (idempotent)
-- ---------------------------------------------------------------------------
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
  ('site_title', 'Mini Blog'),
  ('site_tagline', 'Notes & long-form'),
  ('posts_per_page', '12'),
  ('date_format', 'M j, Y'),
  ('rss_enabled', '1')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);
