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

$subject = $formType === 'gravel' ? 'New gravel delivery inquiry' : 'New equipment rental request';
$body = "PayLess website request #{$requestId}\n\nType: {$formType}\nName: {$name}\nCompany: {$company}\nPhone: {$phone}\nEmail: {$email}\nJob-site location: {$location}\nZIP: {$zip}\nRental period: " . ($rentalPeriod !== '' ? $rentalPeriod : 'Not specified') . "\nStart date / needed by: " . ($startDate ?? 'Not specified') . "\nProject type: " . ($projectType !== '' ? $projectType : 'Not specified') . "\nQuantity: " . ($quantity !== '' ? $quantity : 'Not specified') . "\nEquipment: " . implode(', ', $equipmentList) . "\n\nDetails:\n" . ($details !== '' ? $details : 'Not provided') . "\n";
$recipient = SITE_EMAIL;
if ($recipient === '' || !filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
  $_SESSION['request_last_sent'] = time();
  request_redirect('saved-email-not-configured');
}
$headers = [
  'From' => 'website@' . (preg_replace('/[^a-z0-9.-]/i', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost')) ?: 'localhost'),
  'Reply-To' => (string) $email,
  'Content-Type' => 'text/plain; charset=UTF-8',
];
if (!mail($recipient, $subject, $body, $headers)) {
  $_SESSION['request_last_sent'] = time();
  request_redirect('saved-email-failed');
}
$_SESSION['request_last_sent'] = time();
request_redirect('sent');
