<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/site.php';
session_start();

function request_redirect(string $status): never {
  header('Location: /?page=contact&status=' . rawurlencode($status) . '#equipment-request');
  exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /?page=contact'); exit; }
if (!empty($_POST['website'])) { request_redirect('invalid'); }
$honeypot = trim((string) ($_POST['website'] ?? ''));
if ($honeypot !== '') { request_redirect('invalid'); }

$formType = (string) ($_POST['form_type'] ?? 'equipment');
$allowedTypes = ['equipment', 'gravel'];
if (!in_array($formType, $allowedTypes, true)) { request_redirect('invalid'); }
$name = trim((string) ($_POST['name'] ?? ''));
$email = filter_var(trim((string) ($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
$phone = trim((string) ($_POST['phone'] ?? ''));
$location = trim((string) ($_POST['job_location'] ?? ''));
$consent = isset($_POST['contact_consent']);
if ($name === '' || !$email || $phone === '' || $location === '' || !$consent) { request_redirect('missing'); }
if (!isset($_SESSION['request_last_sent'])) { $_SESSION['request_last_sent'] = 0; }
if (time() - (int) $_SESSION['request_last_sent'] < 15) { request_redirect('throttle'); }

$equipmentList = $_POST['equipment'] ?? [];
if (!is_array($equipmentList)) { $equipmentList = []; }
$equipmentList = array_values(array_filter(array_map(static fn($v) => trim(strip_tags((string) $v)), $equipmentList)));
if ($formType === 'equipment' && count($equipmentList) === 0) { request_redirect('missing'); }
$allowedEquipmentNames = array_column($equipment, 'name');
$equipmentList = array_values(array_intersect($equipmentList, array_merge($allowedEquipmentNames, ['Other / not sure'])));
if ($formType === 'equipment' && count($equipmentList) === 0) { request_redirect('invalid'); }
$rentalPeriod = trim(strip_tags((string) ($_POST['rental_period'] ?? 'Not specified')));
$startDate = trim(strip_tags((string) ($_POST['start_date'] ?? 'Not specified')));
$projectType = trim(strip_tags((string) ($_POST['project_type'] ?? 'Not specified')));
$company = trim(strip_tags((string) ($_POST['company'] ?? 'Not specified')));
$zip = trim(strip_tags((string) ($_POST['zip'] ?? 'Not specified')));
$quantity = trim(strip_tags((string) ($_POST['quantity'] ?? 'Not specified')));
$details = trim(strip_tags((string) ($_POST['details'] ?? 'Not provided')));
$subject = $formType === 'gravel' ? 'New gravel delivery inquiry' : 'New equipment rental request';
$body = "PayLess website request\n\nType: {$formType}\nName: {$name}\nCompany: {$company}\nPhone: {$phone}\nEmail: {$email}\nJob-site location: {$location}\nZIP: {$zip}\nRental period: {$rentalPeriod}\nStart date / needed by: {$startDate}\nProject type: {$projectType}\nQuantity: {$quantity}\nEquipment: " . implode(', ', $equipmentList) . "\n\nDetails:\n{$details}\n";
$recipient = SITE_EMAIL;
if ($recipient === '' || !filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
  request_redirect('not-configured');
}
$headers = [
  'From' => 'website@' . (preg_replace('/[^a-z0-9.-]/i', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost')) ?: 'localhost'),
  'Reply-To' => $email,
  'Content-Type' => 'text/plain; charset=UTF-8',
];
$sent = mail($recipient, $subject, $body, $headers);
if (!$sent) { request_redirect('send-failed'); }
$_SESSION['request_last_sent'] = time();
header('Location: /?page=contact&status=sent#equipment-request');
exit;
