<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/site.php';
require_once __DIR__ . '/includes/database.php';
session_start();

function request_length(string $value): int {
  return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

function request_redirect(string $status): never {
  header('Location: /?page=contact&status=' . rawurlencode($status) . '#request-feedback');
  exit;
}

function request_mail_escape(string $value): string {
  return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function request_mail_row(string $label, string $value): string {
  if ($value === '') {
    return '';
  }
  return '<tr>'
    . '<td style="width:34%;padding:12px 14px;border-bottom:1px solid #e8ece6;color:#667064;font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;vertical-align:top;">' . request_mail_escape($label) . '</td>'
    . '<td style="padding:12px 14px;border-bottom:1px solid #e8ece6;color:#202820;font-size:14px;line-height:1.55;vertical-align:top;">' . nl2br(request_mail_escape($value)) . '</td>'
    . '</tr>';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /?page=contact'); exit; }
$honeypot = trim((string) ($_POST['website'] ?? ''));
if ($honeypot !== '') { request_redirect('invalid'); }

$formType = (string) ($_POST['form_type'] ?? 'equipment');
$allowedTypes = ['equipment', 'gravel'];
if (!in_array($formType, $allowedTypes, true)) { request_redirect('invalid'); }

$name = trim(strip_tags((string) ($_POST['name'] ?? '')));
$email = filter_var(trim((string) ($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
$phone = trim(strip_tags((string) ($_POST['phone'] ?? '')));
$location = trim(strip_tags((string) ($_POST['job_location'] ?? '')));
$consent = isset($_POST['contact_consent']);
if ($name === '' || !$email || $phone === '' || $location === '' || !$consent) { request_redirect('missing'); }
if (request_length($name) > 160 || request_length((string) $email) > 254 || request_length($phone) > 60 || request_length($location) > 500) { request_redirect('invalid'); }

if (!isset($_SESSION['request_last_sent'])) { $_SESSION['request_last_sent'] = 0; }
if (time() - (int) $_SESSION['request_last_sent'] < 15) { request_redirect('throttle'); }

$equipmentList = $_POST['equipment'] ?? [];
if (!is_array($equipmentList)) { $equipmentList = []; }
$equipmentList = array_values(array_unique(array_filter(array_map(static fn($v) => trim(strip_tags((string) $v)), $equipmentList))));
$allowedEquipmentNames = array_column($equipment, 'name');
$equipmentList = array_values(array_intersect($equipmentList, array_merge($allowedEquipmentNames, ['Other / not sure'])));
if ($formType === 'equipment' && count($equipmentList) === 0) { request_redirect('missing'); }

$attachmentList = $_POST['attachments'] ?? [];
if (!is_array($attachmentList)) { $attachmentList = []; }
$allowedAttachmentNames = array_column($requestAttachments, 'name');
$attachmentList = array_values(array_unique(array_intersect(
  array_filter(array_map(static fn($value) => trim(strip_tags((string) $value)), $attachmentList)),
  $allowedAttachmentNames
)));

$rentalPeriod = trim(strip_tags((string) ($_POST['rental_period'] ?? '')));
$rawStartDate = trim((string) ($_POST['start_date'] ?? ''));
$startDate = null;
if ($rawStartDate !== '') {
  $date = DateTimeImmutable::createFromFormat('!Y-m-d', $rawStartDate);
  $dateErrors = DateTimeImmutable::getLastErrors();
  if (!$date || ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0)) || $date->format('Y-m-d') !== $rawStartDate) {
    request_redirect('invalid');
  }
  $startDate = $rawStartDate;
}
$projectType = trim(strip_tags((string) ($_POST['project_type'] ?? '')));
$company = trim(strip_tags((string) ($_POST['company'] ?? '')));
$zip = trim(strip_tags((string) ($_POST['zip'] ?? '')));
$quantity = trim(strip_tags((string) ($_POST['quantity'] ?? '')));
$details = trim(strip_tags((string) ($_POST['details'] ?? '')));
if ($attachmentList) { $details = trim($details . "\n\nAttachments requested: " . implode(', ', $attachmentList)); }
if (request_length($company) > 160 || request_length($zip) > 20 || request_length($rentalPeriod) > 40 || request_length($projectType) > 80 || request_length($quantity) > 120 || request_length($details) > 10000) {
  request_redirect('invalid');
}

$equipmentJson = json_encode($equipmentList, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
try {
  $db = payless_db();
  $statement = $db->prepare(
    'INSERT INTO customer_requests
      (request_type, customer_name, company, phone, email, job_location, zip_code, rental_period, start_date, project_type, quantity, equipment_json, details, contact_consent_at)
     VALUES
      (:request_type, :customer_name, :company, :phone, :email, :job_location, :zip_code, :rental_period, :start_date, :project_type, :quantity, :equipment_json, :details, :contact_consent_at)'
  );
  $statement->execute([
    ':request_type' => $formType,
    ':customer_name' => $name,
    ':company' => $company,
    ':phone' => $phone,
    ':email' => (string) $email,
    ':job_location' => $location,
    ':zip_code' => $zip,
    ':rental_period' => $rentalPeriod,
    ':start_date' => $startDate,
    ':project_type' => $projectType,
    ':quantity' => $quantity,
    ':equipment_json' => $equipmentJson,
    ':details' => $details,
    ':contact_consent_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
  ]);
  $requestId = (int) $db->lastInsertId();
} catch (Throwable $exception) {
  error_log('PayLess request could not be saved. ' . get_class($exception) . ': ' . $exception->getMessage());
  request_redirect('database-error');
}

$requestLabel = $formType === 'gravel' ? 'Gravel delivery inquiry' : 'Equipment rental request';
$subject = 'New ' . $requestLabel . ' #' . $requestId;
$recipient = 'genxinvenits@rediffmail.com';
$sender = 'Rentals@paylessequipments.com';
$submittedAt = (new DateTimeImmutable())->format('F j, Y \a\t g:i A T');

$mailRows = '';
$textRows = [];
$locationLabel = $formType === 'gravel' ? 'Delivery address' : 'Job-site location';
$fields = [
  'Customer name' => $name,
  'Company / individual' => $company,
  'Email address' => (string) $email,
  'Phone number' => $phone,
  $locationLabel => $location,
  'ZIP code' => $zip,
  'Equipment requested' => implode(', ', $equipmentList),
  'Attachments requested' => implode(', ', $attachmentList),
  'Rental period' => $rentalPeriod,
  'Start date / needed by' => $startDate ?? '',
  'Project type' => $projectType,
  'Estimated quantity' => $quantity,
];
foreach ($fields as $label => $value) {
  if ($value === '') {
    continue;
  }
  $mailRows .= request_mail_row($label, $value);
  $textRows[] = $label . ': ' . $value;
}
$detailsHtml = $details !== ''
  ? nl2br(request_mail_escape($details))
  : '<span style="color:#7b8578;">No additional project details provided.</span>';
$detailsText = $details !== '' ? $details : 'No additional project details provided.';

$htmlBody = '<!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>'
  . '<body style="margin:0;padding:28px 12px;background:#f1f3ef;font-family:Arial,Helvetica,sans-serif;color:#202820;">'
  . '<div style="display:none;max-height:0;overflow:hidden;opacity:0;">New ' . request_mail_escape($requestLabel) . ' from the PayLess Equipment Rental website.</div>'
  . '<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:680px;margin:0 auto;background:#ffffff;border:1px solid #e2e7df;border-radius:14px;overflow:hidden;">'
  . '<tr><td style="padding:27px 32px;background:#171c19;border-bottom:4px solid #c99a36;">'
  . '<div style="font-size:11px;font-weight:700;letter-spacing:.16em;color:#d8b45e;text-transform:uppercase;">PAYLESS EQUIPMENT RENTAL LLC</div>'
  . '<div style="margin-top:12px;color:#ffffff;font-size:25px;font-weight:800;line-height:1.2;">New Customer Inquiry – PayLess Equipments</div>'
  . '<div style="margin-top:7px;color:#c5cdc3;font-size:13px;">' . request_mail_escape($requestLabel) . '</div>'
  . '</td></tr>'
  . '<tr><td style="padding:25px 32px 8px;">'
  . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>'
  . '<td style="color:#687365;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;">REQUEST REFERENCE</td>'
  . '<td align="right"><span style="display:inline-block;padding:7px 11px;border-radius:6px;background:#f5f0e4;color:#73551b;font-size:13px;font-weight:700;">#' . $requestId . '</span></td>'
  . '</tr></table>'
  . '<div style="margin-top:8px;color:#778174;font-size:12px;">Received ' . request_mail_escape($submittedAt) . '</div>'
  . '</td></tr>'
  . '<tr><td style="padding:18px 32px 8px;"><div style="margin-bottom:10px;color:#202820;font-size:15px;font-weight:800;">Customer information</div>'
  . '<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border:1px solid #e8ece6;border-radius:8px;overflow:hidden;">' . $mailRows . '</table>'
  . '</td></tr>'
  . '<tr><td style="padding:20px 32px 28px;"><div style="margin-bottom:10px;color:#202820;font-size:15px;font-weight:800;">Project details</div>'
  . '<div style="padding:16px 18px;background:#f6f7f4;border-left:3px solid #c99a36;border-radius:5px;color:#354033;font-size:14px;line-height:1.7;">' . $detailsHtml . '</div>'
  . '<div style="margin-top:22px;padding-top:17px;border-top:1px solid #e8ece6;color:#788174;font-size:11px;line-height:1.6;">This inquiry was submitted through the PayLess Equipment Rental website. Reply directly to this email to contact ' . request_mail_escape($name) . '.</div>'
  . '</td></tr>'
  . '</table><div style="max-width:680px;margin:14px auto 0;text-align:center;color:#8b9487;font-size:11px;line-height:1.6;">PayLess Equipment Rental LLC · Beaverton, Oregon · (503) 901-9990</div>'
  . '</body></html>';

$textBody = "PAYLESS EQUIPMENT RENTAL LLC\nNew {$requestLabel}\nRequest reference: #{$requestId}\nReceived: {$submittedAt}\n\n"
  . implode("\n", $textRows)
  . "\n\nProject details:\n{$detailsText}\n\nSubmitted through the PayLess Equipment Rental website.\n";

require_once __DIR__ . '/includes/mailer.php';

try {
  payless_send_email($recipient, $subject, $htmlBody, $textBody, (string) $email);
} catch (Throwable $exception) {
  // The customer request is already committed. Keep it even if SMTP is unavailable.
  error_log('PayLess request #' . $requestId . ' was saved, but SMTP notification failed: '
    . get_class($exception) . ': ' . $exception->getMessage());
  $_SESSION['request_last_sent'] = time();
  request_redirect('saved-email-failed');
}

$_SESSION['request_last_sent'] = time();
request_redirect('sent');
