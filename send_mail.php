<?php
// send_mail.php — DigitalYes Marketing Contact Form Handler
// Upload this file to your PHP hosting server alongside index.html and contact.html

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// ── Sanitize & collect inputs
$name    = trim(strip_tags($_POST['name']    ?? ''));
$phone   = trim(strip_tags($_POST['phone']   ?? ''));
$email   = trim(strip_tags($_POST['email']   ?? ''));
$service = trim(strip_tags($_POST['service'] ?? ''));
$message = trim(strip_tags($_POST['message'] ?? ''));

// ── Validate required fields
if (!$name || !$phone || !$email || !$service) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

// ── Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

// ── Validate service is one of the allowed options
$allowed_services = [
    'Competitor Report',
    'Keyword Visibility Report',
    'Business Intelligence Dashboard'
];
if (!in_array($service, $allowed_services)) {
    echo json_encode(['success' => false, 'message' => 'Invalid service selection.']);
    exit;
}

// ── Build the email
$to      = 'thippesh@digitalyesmarketing.com';
$subject = "New Enquiry: $service — DigitalYes Marketing";

$body = "
You have a new service enquiry from your website digitalyesmarketing.com

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 ENQUIRY DETAILS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Name      : $name
Phone     : $phone
Email     : $email
Service   : $service

";

if (!empty($message)) {
    $body .= "Message:\n$message\n\n";
} else {
    $body .= "Message: (none provided)\n\n";
}

$body .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Sent via DigitalYes Marketing — Contact Form
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
";

// ── Email headers
$headers  = "From: noreply@digitalyesmarketing.com\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// ── Send the mail
if (mail($to, $subject, $body, $headers)) {
    echo json_encode(['success' => true, 'message' => 'Email sent successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send email. Please contact us directly.']);
}
?>
