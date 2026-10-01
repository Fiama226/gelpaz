-- GELPAZ IMMO — Schéma MySQL/MariaDB
-- Généré automatiquement. Utilisez de préférence l'assistant /install.

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NULL,
  `email` VARCHAR(190) NULL,
  `password` VARCHAR(255) NULL,
  `role` VARCHAR(20) NULL,
  `last_login_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  UNIQUE KEY `uniq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `name` VARCHAR(100) NULL,
  `value` MEDIUMTEXT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `property_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NULL,
  `slug` VARCHAR(150) NULL,
  `description` TEXT NULL,
  `icon` VARCHAR(50) NULL,
  `sort` INT NULL,
  UNIQUE KEY `uniq_property_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sites` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(190) NULL,
  `slug` VARCHAR(190) NULL,
  `city` VARCHAR(120) NULL,
  `area` VARCHAR(190) NULL,
  `description` TEXT NULL,
  `status` VARCHAR(30) NULL,
  `image` VARCHAR(255) NULL,
  `image_remote` VARCHAR(500) NULL,
  `map_query` VARCHAR(255) NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `sort` INT NULL,
  `created_at` DATETIME NULL,
  UNIQUE KEY `uniq_sites_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `properties` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(190) NULL,
  `slug` VARCHAR(190) NULL,
  `reference` VARCHAR(50) NULL,
  `category_id` INT NULL,
  `site_id` INT NULL,
  `transaction` VARCHAR(20) NULL,
  `status` VARCHAR(30) NULL,
  `city` VARCHAR(120) NULL,
  `address` VARCHAR(255) NULL,
  `built_area` INT NULL,
  `land_area` INT NULL,
  `rooms` INT NULL,
  `bedrooms` INT NULL,
  `bathrooms` INT NULL,
  `living_rooms` INT NULL,
  `kitchens` INT NULL,
  `terraces` INT NULL,
  `garages` INT NULL,
  `floors` INT NULL,
  `year_built` INT NULL,
  `excerpt` TEXT NULL,
  `description` MEDIUMTEXT NULL,
  `features` TEXT NULL,
  `image` VARCHAR(255) NULL,
  `image_remote` VARCHAR(500) NULL,
  `thumb_remote` VARCHAR(500) NULL,
  `video_url` VARCHAR(255) NULL,
  `map_query` VARCHAR(255) NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_published` TINYINT(1) NOT NULL DEFAULT 0,
  `views` INT NULL,
  `sort` INT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  UNIQUE KEY `uniq_properties_slug` (`slug`),
  KEY `idx_properties_category_id` (`category_id`),
  KEY `idx_properties_site_id` (`site_id`),
  KEY `idx_properties_is_published` (`is_published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `property_images` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `property_id` INT NULL,
  `path` VARCHAR(255) NULL,
  `remote_url` VARCHAR(500) NULL,
  `remote_thumb` VARCHAR(500) NULL,
  `caption` VARCHAR(255) NULL,
  `sort` INT NULL,
  KEY `idx_property_images_property_id` (`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `property_plans` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `property_id` INT NULL,
  `title` VARCHAR(190) NULL,
  `image` VARCHAR(255) NULL,
  `image_remote` VARCHAR(500) NULL,
  `description` TEXT NULL,
  `sort` INT NULL,
  KEY `idx_property_plans_property_id` (`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `posts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NULL,
  `slug` VARCHAR(190) NULL,
  `legacy_slug` VARCHAR(190) NULL,
  `category` VARCHAR(120) NULL,
  `tags` VARCHAR(255) NULL,
  `author` VARCHAR(120) NULL,
  `excerpt` TEXT NULL,
  `content` MEDIUMTEXT NULL,
  `image` VARCHAR(255) NULL,
  `image_remote` VARCHAR(500) NULL,
  `thumb_remote` VARCHAR(500) NULL,
  `gallery` MEDIUMTEXT NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 0,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `published_at` DATETIME NULL,
  `views` INT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  UNIQUE KEY `uniq_posts_slug` (`slug`),
  KEY `idx_posts_is_published_published_at` (`is_published`, `published_at`),
  KEY `idx_posts_legacy_slug` (`legacy_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `comments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `post_id` INT NULL,
  `parent_id` INT NULL,
  `name` VARCHAR(120) NULL,
  `email` VARCHAR(190) NULL,
  `content` TEXT NULL,
  `status` VARCHAR(20) NULL,
  `ip` VARCHAR(45) NULL,
  `created_at` DATETIME NULL,
  KEY `idx_comments_post_id_status` (`post_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `faqs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `question` VARCHAR(255) NULL,
  `answer` TEXT NULL,
  `category` VARCHAR(100) NULL,
  `sort` INT NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NULL,
  `role` VARCHAR(190) NULL,
  `content` TEXT NULL,
  `rating` INT NULL,
  `photo` VARCHAR(255) NULL,
  `sort` INT NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `partners` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NULL,
  `logo` VARCHAR(255) NULL,
  `logo_remote` VARCHAR(500) NULL,
  `url` VARCHAR(255) NULL,
  `sort` INT NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `services` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(190) NULL,
  `slug` VARCHAR(190) NULL,
  `icon` VARCHAR(50) NULL,
  `excerpt` TEXT NULL,
  `content` MEDIUMTEXT NULL,
  `features` TEXT NULL,
  `image` VARCHAR(255) NULL,
  `sort` INT NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 0,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  UNIQUE KEY `uniq_services_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `slides` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `pre_title` VARCHAR(190) NULL,
  `title` VARCHAR(255) NULL,
  `text` TEXT NULL,
  `image` VARCHAR(255) NULL,
  `button_text` VARCHAR(100) NULL,
  `button_url` VARCHAR(255) NULL,
  `button2_text` VARCHAR(100) NULL,
  `button2_url` VARCHAR(255) NULL,
  `sort` INT NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `messages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `type` VARCHAR(30) NULL,
  `name` VARCHAR(150) NULL,
  `email` VARCHAR(190) NULL,
  `phone` VARCHAR(50) NULL,
  `subject` VARCHAR(190) NULL,
  `message` TEXT NULL,
  `property_id` INT NULL,
  `visit_date` VARCHAR(30) NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `ip` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `created_at` DATETIME NULL,
  KEY `idx_messages_is_read` (`is_read`),
  KEY `idx_messages_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(150) NULL,
  `email` VARCHAR(190) NULL,
  `phone` VARCHAR(50) NULL,
  `country` VARCHAR(100) NULL,
  `city` VARCHAR(100) NULL,
  `villa_type` VARCHAR(50) NULL,
  `site` VARCHAR(150) NULL,
  `payment_mode` VARCHAR(50) NULL,
  `message` TEXT NULL,
  `status` VARCHAR(30) NULL,
  `notes` TEXT NULL,
  `ip` VARCHAR(45) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  KEY `idx_subscriptions_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `newsletter` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(190) NULL,
  `ip` VARCHAR(45) NULL,
  `created_at` DATETIME NULL,
  UNIQUE KEY `uniq_newsletter_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `rate_limits` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `k` VARCHAR(190) NULL,
  `created_at` INT NULL,
  KEY `idx_rate_limits_k_created_at` (`k`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
