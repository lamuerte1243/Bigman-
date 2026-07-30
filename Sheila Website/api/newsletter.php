<?php
/**
 * AJAX Newsletter Signup Handler
 * Accepts POST with email (and optional name).
 * Saves subscriber to DB and sends a welcome email.
 */

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

header('Content-Type: application/json');

// CSRF check
if (!csrf_check()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Security token mismatch. Please refresh and try again.']);
    exit;
}

// Input
$email  = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
$name   = trim(strip_tags($_POST['name'] ?? ''));
$source = trim(strip_tags($_POST['source'] ?? 'website'));

// Honeypot
if (!empty($_POST['website'] ?? '')) {
    echo json_encode(['success' => true, 'message' => 'Thank you for subscribing!']);
    exit;
}

// Validate
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

// Check duplicate
try {
    $existing = DB::row("SELECT id, unsubscribed_at FROM newsletter_subscribers WHERE email = ?", [$email]);

    if ($existing) {
        if ($existing['unsubscribed_at']) {
            // Re-subscribe
            DB::query("UPDATE newsletter_subscribers SET unsubscribed_at = NULL, resubscribed_at = ?, source = ?, name = ? WHERE email = ?", [
                date('Y-m-d H:i:s'),
                $source,
                $name ?: $existing['name'] ?? '',
                $email
            ]);
            $action = 're-subscribed';
        } else {
            // Already active subscriber
            echo json_encode(['success' => true, 'message' => 'You\'re already on our list! Check your inbox for our latest tips.']);
            exit;
        }
    } else {
        // New subscriber
        DB::insert('newsletter_subscribers', [
            'email'      => $email,
            'name'       => $name,
            'source'     => $source,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $action = 'subscribed';
    }
} catch (Exception $e) {
    error_log('Newsletter DB error: ' . $e->getMessage());
    // Still send email below, just skip DB
    $action = 'subscribed';
}

// Notify site owner
$admin_to      = defined('CONTACT_TO_EMAIL') ? CONTACT_TO_EMAIL : SITE_EMAIL;
$admin_subject = '[Sheila The Writer] New Newsletter Subscriber';
$admin_body    = "New newsletter signup:\n\nEmail: {$email}\nName: " . ($name ?: 'Not provided') . "\nSource: {$source}\nTime: " . date('F j, Y \a\t g:i A T');
$admin_headers = "From: noreply@" . ($_SERVER['HTTP_HOST'] ?? 'sheilathewriter.com') . "\r\n";
@mail($admin_to, $admin_subject, $admin_body, $admin_headers);

// Welcome email to subscriber
$first = $name ? explode(' ', $name)[0] : 'there';
$welcome_subject = 'Welcome to Sheila The Writer – Free Study Tips Start Now';
$welcome_body  = "Hi {$first},\n\n";
$welcome_body .= "You're in! Welcome to the Sheila The Writer community.\n\n";
$welcome_body .= "Every week you'll receive:\n";
$welcome_body .= "  ✓ Exam-specific study tips and strategies\n";
$welcome_body .= "  ✓ Study schedule templates\n";
$welcome_body .= "  ✓ Exclusive subscriber discounts on courses and materials\n";
$welcome_body .= "  ✓ Real student success stories and insights\n\n";
$welcome_body .= "In the meantime, here are some free resources to get you started:\n\n";
$welcome_body .= "  → Free Blog Articles: " . (isset($_SERVER['HTTP_HOST']) ? 'https://' . $_SERVER['HTTP_HOST'] : 'https://sheilathewriter.com') . "/blog.php\n";
$welcome_body .= "  → Browse Courses: " . (isset($_SERVER['HTTP_HOST']) ? 'https://' . $_SERVER['HTTP_HOST'] : 'https://sheilathewriter.com') . "/courses.php\n";
$welcome_body .= "  → Study Materials: " . (isset($_SERVER['HTTP_HOST']) ? 'https://' . $_SERVER['HTTP_HOST'] : 'https://sheilathewriter.com') . "/study-materials.php\n\n";
$welcome_body .= "To unsubscribe at any time, simply reply to any of our emails with 'UNSUBSCRIBE' in the subject line.\n\n";
$welcome_body .= "To your success,\nSheila\n";
$welcome_body .= SITE_NAME . "\n" . SITE_EMAIL . " | " . SITE_PHONE . "\n";

$welcome_headers  = "From: " . SITE_NAME . " <" . SITE_EMAIL . ">\r\n";
$welcome_headers .= "Reply-To: " . SITE_EMAIL . "\r\n";
$welcome_headers .= "X-Mailer: PHP/" . PHP_VERSION . "\r\n";

@mail($email, $welcome_subject, $welcome_body, $welcome_headers);

echo json_encode([
    'success' => true,
    'message' => 'You\'re subscribed! Check your inbox for a welcome message with free study resources.'
]);
exit;
