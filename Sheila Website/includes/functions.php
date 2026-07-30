<?php
/**
 * Shared utility functions
 */
require_once __DIR__ . '/config.php';

// ── Security helpers ───────────────────────────────────────
function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function sanitize(string $s): string {
    return trim(strip_tags($s));
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_check(): bool {
    $token = $_POST['csrf_token'] ?? '';
    return hash_equals($_SESSION['csrf'] ?? '', $token);
}
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

// ── Flash messages ─────────────────────────────────────────
function flash(string $key, string $msg = ''): ?string {
    if ($msg) {
        $_SESSION['flash'][$key] = $msg;
        return null;
    }
    $val = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $val;
}
function flash_html(): string {
    $out = '';
    foreach (['success', 'error', 'info'] as $type) {
        $msg = flash($type);
        if ($msg) {
            $icon = $type === 'success' ? 'check-circle' : ($type === 'error' ? 'exclamation-circle' : 'info-circle');
            $out .= '<div class="alert alert-' . $type . '"><i class="fa-solid fa-' . $icon . '"></i> ' . h($msg) . '</div>';
        }
    }
    return $out;
}

// ── Auth helpers ───────────────────────────────────────────
function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}
function current_user(): ?array {
    if (!is_logged_in()) return null;
    return [
        'id'    => $_SESSION['user_id'],
        'name'  => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role'  => $_SESSION['user_role'] ?? 'student',
    ];
}
function require_login(string $redirect = 'login.php'): void {
    if (!is_logged_in()) {
        flash('info', 'Please log in to continue.');
        header('Location: ' . $redirect);
        exit;
    }
}
function require_admin(): void {
    require_login('login.php');
    if (current_user()['role'] !== 'admin') {
        http_response_code(403);
        die('Access denied.');
    }
}

// ── Email ──────────────────────────────────────────────────
function send_mail(string $to, string $subject, string $body, string $replyTo = ''): bool {
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= 'From: ' . CONTACT_FROM_NAME . ' <' . SITE_EMAIL . '>' . "\r\n";
    if ($replyTo) $headers .= 'Reply-To: ' . $replyTo . "\r\n";
    return mail($to, $subject, $body, $headers);
}

// ── String helpers ─────────────────────────────────────────
function slugify(string $s): string {
    $s = strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9\s-]/', '', $s);
    return preg_replace('/[\s-]+/', '-', $s);
}
function excerpt(string $text, int $words = 25): string {
    $text = strip_tags($text);
    $arr  = explode(' ', $text);
    if (count($arr) <= $words) return $text;
    return implode(' ', array_slice($arr, 0, $words)) . '…';
}
function format_date(string $date): string {
    return date('F j, Y', strtotime($date));
}

// ── Redirect ───────────────────────────────────────────────
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

// ── Active nav helper ──────────────────────────────────────
function is_active(string $page): string {
    $current = basename($_SERVER['PHP_SELF'], '.php');
    return $current === $page ? 'active' : '';
}
