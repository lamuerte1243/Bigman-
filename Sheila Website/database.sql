-- ==========================================================================
-- Sheila The Writer — MySQL Database Schema
-- Compatible with: MySQL 5.7+ / MariaDB 10.3+ (Namecheap Shared Hosting)
-- ==========================================================================
-- INSTALLATION: Run this file in cPanel > phpMyAdmin after creating your database
-- ==========================================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;

-- ── Contacts (contact form submissions) ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS `contacts` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name`  VARCHAR(80)  NOT NULL,
  `last_name`   VARCHAR(80)  NOT NULL,
  `email`       VARCHAR(180) NOT NULL,
  `phone`       VARCHAR(30)  DEFAULT NULL,
  `subject`     VARCHAR(220) DEFAULT NULL,
  `exam_date`   DATE         DEFAULT NULL,
  `budget`      VARCHAR(50)  DEFAULT NULL,
  `message`     TEXT         NOT NULL,
  `ip_address`  VARCHAR(45)  DEFAULT NULL,
  `status`      ENUM('new','read','replied','spam') NOT NULL DEFAULT 'new',
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Legacy: Contact Messages (kept for backwards compat) ─────────────────────
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(120) NOT NULL,
  `email`       VARCHAR(180) NOT NULL,
  `phone`       VARCHAR(30)  DEFAULT NULL,
  `service`     VARCHAR(100) DEFAULT NULL,
  `subject`     VARCHAR(220) DEFAULT NULL,
  `message`     TEXT         NOT NULL,
  `ip_address`  VARCHAR(45)  DEFAULT NULL,
  `status`      ENUM('new','read','replied','spam') NOT NULL DEFAULT 'new',
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Tutoring Requests ────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `tutoring_requests` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(120) NOT NULL,
  `email`       VARCHAR(180) NOT NULL,
  `phone`       VARCHAR(30)  DEFAULT NULL,
  `subject`     VARCHAR(150) NOT NULL,
  `session_type` VARCHAR(80) DEFAULT NULL,
  `message`     TEXT         DEFAULT NULL,
  `preferred_time` VARCHAR(100) DEFAULT NULL,
  `status`      ENUM('new','contacted','scheduled','completed','cancelled') NOT NULL DEFAULT 'new',
  `ip_address`  VARCHAR(45)  DEFAULT NULL,
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Newsletter Subscribers ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email`           VARCHAR(180) NOT NULL,
  `name`            VARCHAR(160) DEFAULT NULL,
  `source`          VARCHAR(80)  DEFAULT 'website',
  `status`          ENUM('active','unsubscribed') NOT NULL DEFAULT 'active',
  `ip_address`      VARCHAR(45)  DEFAULT NULL,
  `unsubscribed_at` DATETIME     DEFAULT NULL,
  `resubscribed_at` DATETIME     DEFAULT NULL,
  `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Testimonials ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(120) NOT NULL,
  `role`        VARCHAR(120) DEFAULT NULL  COMMENT 'e.g. TEAS Exam — Nursing Program',
  `rating`      TINYINT      NOT NULL DEFAULT 5,
  `content`     TEXT         NOT NULL,
  `avatar_url`  VARCHAR(300) DEFAULT NULL,
  `active`      TINYINT(1)   NOT NULL DEFAULT 1,
  `sort_order`  INT          NOT NULL DEFAULT 0,
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_active_sort` (`active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Blog Posts ───────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`         VARCHAR(200) NOT NULL,
  `title`        VARCHAR(300) NOT NULL,
  `excerpt`      TEXT         DEFAULT NULL,
  `content`      LONGTEXT     NOT NULL,
  `category`     VARCHAR(80)  DEFAULT NULL,
  `author`       VARCHAR(120) DEFAULT NULL,
  `img`          VARCHAR(400) DEFAULT NULL,
  `meta_title`   VARCHAR(300) DEFAULT NULL,
  `meta_desc`    VARCHAR(400) DEFAULT NULL,
  `status`       ENUM('draft','published') NOT NULL DEFAULT 'draft',
  `published_at` DATE         DEFAULT NULL,
  `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`),
  KEY `idx_status_date` (`status`, `published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Admin Users ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(120) NOT NULL,
  `email`         VARCHAR(180) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role`          ENUM('superadmin','admin','editor') NOT NULL DEFAULT 'admin',
  `active`        TINYINT(1)   NOT NULL DEFAULT 1,
  `last_login`    DATETIME     DEFAULT NULL,
  `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Students ─────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `students` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`           VARCHAR(120) NOT NULL,
  `email`          VARCHAR(180) NOT NULL,
  `password_hash`  VARCHAR(255) NOT NULL,
  `phone`          VARCHAR(30)  DEFAULT NULL,
  `status`         ENUM('active','inactive','pending') NOT NULL DEFAULT 'active',
  `email_verified` TINYINT(1)   NOT NULL DEFAULT 0,
  `last_login`     DATETIME     DEFAULT NULL,
  `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_email` (`email`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Student Enrollments ───────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `student_enrollments` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id`   INT UNSIGNED NOT NULL,
  `course_id`    INT UNSIGNED NOT NULL,
  `course_name`  VARCHAR(300) NOT NULL,
  `exam_type`    VARCHAR(80)  DEFAULT NULL,
  `status`       ENUM('active','completed','paused','dropped') NOT NULL DEFAULT 'active',
  `progress`     TINYINT UNSIGNED NOT NULL DEFAULT 0  COMMENT '0–100 percentage',
  `enrolled_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` DATETIME     DEFAULT NULL,
  `updated_at`   DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_student` (`student_id`),
  KEY `idx_course`  (`course_id`),
  KEY `idx_status`  (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── FAQs ─────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `faqs` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `question`   VARCHAR(400) NOT NULL,
  `answer`     TEXT         NOT NULL,
  `category`   VARCHAR(80)  DEFAULT 'General',
  `sort_order` INT          NOT NULL DEFAULT 0,
  `active`     TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Site Settings (key-value store) ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `site_settings` (
  `setting_key`   VARCHAR(100) NOT NULL,
  `setting_value` LONGTEXT     DEFAULT NULL,
  `updated_at`    DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================================================
-- SEED DATA
-- ==========================================================================

-- Admin user (password: Admin@2025 — CHANGE IMMEDIATELY)
INSERT IGNORE INTO `admin_users` (`name`, `email`, `password_hash`, `role`, `active`) VALUES
('Site Admin', 'admin@sheilathewriter.com', '$2y$12$example_hash_change_this', 'superadmin', 1);

-- Testimonials
INSERT INTO `testimonials` (`name`, `role`, `rating`, `content`, `avatar_url`, `active`, `sort_order`) VALUES
('Jessica M.', 'TEAS Exam — Nursing Program', 5, 'I failed the TEAS twice on my own. After working with Sheila''s team, I passed with an 85%! They knew exactly what to focus on and how to prepare. Worth every penny.', 'https://i.pravatar.cc/96?img=47', 1, 1),
('Marcus T.', 'StraighterLine — Introduction to Statistics', 5, 'Completed my entire StraighterLine course with As and Bs. The team was professional, fast, and 100% confidential. I got my college credit without the stress!', 'https://i.pravatar.cc/96?img=12', 1, 2),
('Priya K.', 'HESI A2 — Healthcare Program', 5, 'The HESI was my last hurdle before nursing school. Sheila''s expert got me through it in one try. The subject knowledge was impressive and support was constant.', 'https://i.pravatar.cc/96?img=32', 1, 3),
('Darnell W.', 'GED — All 4 Subjects', 5, 'Passed my GED on the first attempt after 3 years of trying on my own. The personalized tutoring sessions made all the difference. Life-changing experience!', 'https://i.pravatar.cc/96?img=25', 1, 4),
('Amanda L.', 'Sophia Learning — Psychology Course', 5, 'My Sophia course was overwhelming until I found this service. Every assignment, quiz, and milestone exam was handled expertly. Got an A in the course!', 'https://i.pravatar.cc/96?img=38', 1, 5),
('Chen W.', 'GRE — Graduate School Admission', 5, 'GRE prep was a nightmare until these tutors stepped in. Verbal and Quantitative both improved significantly. Got into my top-choice graduate program!', 'https://i.pravatar.cc/96?img=55', 1, 6);

-- FAQs
INSERT INTO `faqs` (`question`, `answer`, `category`, `sort_order`) VALUES
('Is this service 100% confidential?', 'Absolutely. We have a strict confidentiality policy. Your name, email, and academic information are never shared with anyone under any circumstances.', 'General', 1),
('How quickly can you start helping me?', 'Most clients are matched with a qualified expert within 2–4 hours of submitting an inquiry. Urgent requests can often be handled within 1 hour.', 'General', 2),
('What if I don''t pass the exam?', 'We offer remediation sessions at no additional charge on qualifying packages. For full course packages, we also offer partial refunds. Please see our guarantee policy on contact.', 'Exams', 3),
('Do you handle proctored exams?', 'Yes, we have extensive experience with various proctored exam platforms. Contact us to discuss your specific situation.', 'Exams', 4),
('What online course platforms do you support?', 'We support StraighterLine, Sophia, Study.com, Excelsior, and many others. If your platform isn''t listed, contact us — we likely cover it.', 'Courses', 5),
('How do I get started?', 'Simply fill out our contact form or call us. Tell us your exam, course, or tutoring need and deadline. We''ll respond within 60 minutes with a plan and quote.', 'General', 6),
('What payment methods do you accept?', 'We accept all major credit cards, PayPal, CashApp, Zelle, and Venmo. Payment plans are available for larger packages.', 'Billing', 7),
('Is the work original and plagiarism-free?', 'Yes. All assignments are written specifically for your course and are completely original. We never recycle or resell any work.', 'Classes', 8);

-- Site settings
INSERT IGNORE INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'Sheila The Writer'),
('site_tagline', 'Preferred Academic Consultant'),
('contact_email', 'hello@sheilathewriter.com'),
('phone', '+1 (302) 555-0142'),
('maintenance_mode', '0'),
('google_analytics_id', ''),
('facebook_url', ''),
('twitter_url', ''),
('instagram_url', ''),
('linkedin_url', '');

-- Blog posts
INSERT INTO `blog_posts` (`slug`, `title`, `excerpt`, `content`, `category`, `author`, `img`, `status`, `published_at`) VALUES
('teas-exam-tips', '7 Proven Strategies to Pass the TEAS Exam on Your First Try', 'Discover the most effective study strategies that have helped hundreds of nursing students ace the TEAS exam.', '<p>The TEAS exam is one of the biggest barriers to nursing school admission. Here are 7 proven strategies...</p><h2>1. Start with a Diagnostic Test</h2><p>Before you start studying, take a full-length practice TEAS to identify your weakest areas...</p>', 'TEAS Tips', 'Dr. Sarah M.', 'https://images.unsplash.com/photo-1488190211105-8b0e65b80b4e?w=800&h=450&fit=crop&auto=format', 'published', '2026-07-10'),
('straighterline-guide', 'StraighterLine Courses: What to Expect and How to Succeed', 'A complete breakdown of how StraighterLine works and insider tips for getting the best grades.', '<p>StraighterLine has become one of the most popular ways to earn affordable college credits...</p>', 'Online Courses', 'Mr. David L.', 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=800&h=450&fit=crop&auto=format', 'published', '2026-07-05'),
('nclex-pass-rate', 'Why the NCLEX-RN Is Harder Than You Think (And How to Beat It)', 'Understanding the CAT format and preparation strategies that produce first-attempt passes.', '<p>The NCLEX-RN pass rate hovers around 80% for US-educated candidates. That means 1 in 5 nurses fails their first attempt...</p>', 'NCLEX', 'Ms. Angela R.', 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=800&h=450&fit=crop&auto=format', 'published', '2026-06-28');

SET foreign_key_checks = 1;

-- ==========================================================================
-- POST-INSTALL STEPS:
-- 1. Update the admin_users password_hash with a real bcrypt hash
-- 2. Run: php -r "echo password_hash('YourNewPassword', PASSWORD_DEFAULT);"
-- 3. Copy the output hash into the admin_users row above and re-run
-- ==========================================================================
