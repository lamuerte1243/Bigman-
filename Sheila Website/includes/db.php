<?php
/**
 * Sheila The Writer — PDO Database Connection
 */

function get_db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // Don't expose DB errors publicly
            throw new RuntimeException('Database connection failed. Please check your configuration.');
        }
    }
    return $pdo;
}

/**
 * Auth helpers — admin
 */
function admin_logged_in(): bool {
    return !empty($_SESSION['admin_id']);
}

function require_admin_login(): void {
    if (!admin_logged_in()) {
        flash('info', 'Please log in to access the admin panel.');
        header('Location: ' . (isset($in_subdir) ? '../' : '') . 'admin-login.php');
        exit;
    }
}

function current_admin(): ?array {
    if (!admin_logged_in()) return null;
    return [
        'id'    => $_SESSION['admin_id'],
        'name'  => $_SESSION['admin_name'] ?? 'Admin',
        'email' => $_SESSION['admin_email'] ?? '',
        'role'  => $_SESSION['admin_role'] ?? 'admin',
    ];
}

/**
 * Auth helpers — student
 */
function student_logged_in(): bool {
    return !empty($_SESSION['student_id']);
}

function require_student_login(): void {
    if (!student_logged_in()) {
        flash('info', 'Please log in to continue.');
        header('Location: ' . (isset($in_subdir) ? '../' : '') . 'student/login.php');
        exit;
    }
}

function current_student(): ?array {
    if (!student_logged_in()) return null;
    return [
        'id'    => $_SESSION['student_id'],
        'name'  => $_SESSION['student_name'] ?? '',
        'email' => $_SESSION['student_email'] ?? '',
    ];
}
