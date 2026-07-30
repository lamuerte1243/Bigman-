<?php
/**
 * Sheila The Writer — Database & Site Configuration
 * ======================================================
 * NAMECHEAP SHARED HOSTING: Edit DB_* constants below
 * after creating your MySQL database in cPanel.
 * ======================================================
 */

// ── Database ──────────────────────────────────────────────
define('DB_HOST',     'localhost');          // Almost always 'localhost' on Namecheap
define('DB_NAME',     'your_db_name');       // cPanel > MySQL Databases > Database name
define('DB_USER',     'your_db_user');       // cPanel > MySQL Databases > Username
define('DB_PASS',     'your_db_password');   // Password you set for that user
define('DB_CHARSET',  'utf8mb4');

// ── Site Settings ─────────────────────────────────────────
define('SITE_NAME',    'Sheila The Writer');
define('SITE_TAGLINE', 'Preferred Academic Consultant');
define('SITE_URL',     'https://yourdomain.com');  // No trailing slash
define('SITE_EMAIL',   'hello@sheilathewriter.com');
define('SITE_PHONE',   '+1 (302) 555-0142');
define('SITE_ADDRESS', 'Wilmington, Delaware, USA');

// ── Admin Credentials ─────────────────────────────────────
define('ADMIN_EMAIL',    'admin@sheilathewriter.com');
define('ADMIN_PASSWORD', 'ChangeMe!Admin2025');  // ← CHANGE IMMEDIATELY AFTER INSTALL

// ── Contact Form Email ────────────────────────────────────
define('CONTACT_TO_EMAIL', 'hello@sheilathewriter.com');
define('CONTACT_FROM_NAME', 'Sheila The Writer Website');

// ── Paths ─────────────────────────────────────────────────
define('ROOT_PATH',  dirname(__DIR__));
define('INCLUDES',   ROOT_PATH . '/includes');
define('UPLOADS',    ROOT_PATH . '/uploads');

// ── Error Reporting (set to 0 in production) ──────────────
error_reporting(E_ALL);
ini_set('display_errors', 0);        // Hide errors from browser in production
ini_set('log_errors', 1);

// ── Session ───────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

// ── Timezone ──────────────────────────────────────────────
date_default_timezone_set('America/New_York');
