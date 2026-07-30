<?php
/**
 * AJAX Contact Form Handler
 * Accepts POST from contact.php and tutoring.php booking forms.
 * Saves to DB contacts table and sends email notification.
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

// Output as JSON
header('Content-Type: application/json');

// CSRF check
if (!csrf_check()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Security token mismatch. Please refresh the page and try again.']);
    exit;
}

// Collect and sanitize inputs
$first_name = trim(strip_tags($_POST['first_name'] ?? ''));
$last_name  = trim(strip_tags($_POST['last_name'] ?? ''));
$email      = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
$phone      = trim(strip_tags($_POST['phone'] ?? ''));
$subject    = trim(strip_tags($_POST['subject'] ?? ''));
$exam_date  = trim(strip_tags($_POST['exam_date'] ?? ''));
$budget     = trim(strip_tags($_POST['budget'] ?? ''));
$message    = trim(strip_tags($_POST['message'] ?? ''));
$newsletter = isset($_POST['newsletter_opt']) ? 1 : 0;

// -- Server-side validation --
$errors = [];

if (empty($first_name) || strlen($first_name) < 2) {
    $errors[] = 'Please enter your first name (at least 2 characters).';
}
if (empty($last_name) || strlen($last_name) < 2) {
    $errors[] = 'Please enter your last name (at least 2 characters).';
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}
if (empty($subject)) {
    $errors[] = 'Please select the topic you need help with.';
}
if (empty($message) || strlen($message) < 20) {
    $errors[] = 'Please enter a message (at least 20 characters).';
}
if (strlen($message) > 5000) {
    $errors[] = 'Message is too long (maximum 5,000 characters).';
}

// Simple honeypot check (if a "website" hidden field was added to the form)
if (!empty($_POST['website'] ?? '')) {
    // Silently reject bots
    echo json_encode(['success' => true, 'message' => 'Thank you! We\'ll be in touch soon.']);
    exit;
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// -- Save to database --
try {
    $contact_id = DB::insert('contacts', [
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'email'      => $email,
        'phone'      => $phone,
        'subject'    => $subject,
        'exam_date'  => $exam_date ?: null,
        'budget'     => $budget,
        'message'    => $message,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        'created_at' => date('Y-m-d H:i:s'),
    ]);
} catch (Exception $e) {
    // Log error but don't expose it to client
    error_log('Contact form DB error: ' . $e->getMessage());
    // Continue — still send email even if DB fails
    $contact_id = null;
}

// -- Handle newsletter opt-in --
if ($newsletter) {
    try {
        // Check if already subscribed
        $existing = DB::val("SELECT id FROM newsletter_subscribers WHERE email = ?", [$email]);
        if (!$existing) {
            DB::insert('newsletter_subscribers', [
                'email'      => $email,
                'name'       => $first_name . ' ' . $last_name,
                'source'     => 'contact_form',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    } catch (Exception $e) {
        error_log('Newsletter opt-in error: ' . $e->getMessage());
    }
}

// -- Send email notification to site owner --
$full_name   = $first_name . ' ' . $last_name;
$to          = defined('CONTACT_TO_EMAIL') ? CONTACT_TO_EMAIL : SITE_EMAIL;
$mail_subject = '[Sheila The Writer] New Inquiry: ' . $subject;

$exam_date_display = $exam_date ? date('F j, Y', strtotime($exam_date)) : 'Not specified';
$budget_display    = $budget ?: 'Not specified';

$body = "You have received a new contact form submission on Sheila The Writer.\n\n";
$body .= "----------------------------------------\n";
$body .= "Name:       {$full_name}\n";
$body .= "Email:      {$email}\n";
$body .= "Phone:      " . ($phone ?: 'Not provided') . "\n";
$body .= "Subject:    {$subject}\n";
$body .= "Exam Date:  {$exam_date_display}\n";
$body .= "Budget:     {$budget_display}\n";
$body .= "Newsletter: " . ($newsletter ? 'Yes' : 'No') . "\n";
$body .= "----------------------------------------\n\n";
$body .= "Message:\n{$message}\n\n";
$body .= "----------------------------------------\n";
$body .= "Submitted: " . date('F j, Y \a\t g:i A T') . "\n";
$body .= "IP Address: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "\n";
if ($contact_id) {
    $body .= "Database ID: #{$contact_id}\n";
}
$body .= "\n-- Sheila The Writer System --\n";

$headers  = "From: noreply@" . ($_SERVER['HTTP_HOST'] ?? 'sheilathewriter.com') . "\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "X-Mailer: PHP/" . PHP_VERSION . "\r\n";

$mail_sent = @mail($to, $mail_subject, $body, $headers);

// -- Send confirmation email to user --
$confirm_subject = 'We received your message – Sheila The Writer';
$confirm_body  = "Hi {$first_name},\n\n";
$confirm_body .= "Thank you for reaching out to Sheila The Writer! We've received your message and will get back to you within 2 business hours.\n\n";
$confirm_body .= "Here's a summary of your inquiry:\n";
$confirm_body .= "  Topic: {$subject}\n";
$confirm_body .= "  Exam Date: {$exam_date_display}\n\n";
$confirm_body .= "In the meantime, feel free to browse our free study tips at:\n";
$confirm_body .= (isset($_SERVER['HTTP_HOST']) ? 'https://' . $_SERVER['HTTP_HOST'] : 'https://sheilathewriter.com') . "/blog.php\n\n";
$confirm_body .= "Talk soon,\nSheila\nSheila The Writer\n";
$confirm_body .= SITE_EMAIL . " | " . SITE_PHONE . "\n";

$confirm_headers  = "From: " . SITE_NAME . " <" . SITE_EMAIL . ">\r\n";
$confirm_headers .= "Reply-To: " . SITE_EMAIL . "\r\n";
$confirm_headers .= "X-Mailer: PHP/" . PHP_VERSION . "\r\n";

@mail($email, $confirm_subject, $confirm_body, $confirm_headers);

// -- Return success --
echo json_encode([
    'success' => true,
    'message' => "Thank you, {$first_name}! Your message has been received. We'll respond within 2 business hours."
]);
exit;
