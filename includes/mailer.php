<?php
declare(strict_types=1);
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

/** Send one message through authenticated SMTP. There is deliberately no mail() fallback. */
function payless_send_email(string $recipient, string $subject, string $htmlBody, string $textBody, string $replyTo): void {
  $localConfigPath = __DIR__ . '/mail.local.php';
  $localConfig = is_file($localConfigPath) ? require $localConfigPath : [];
  if (!is_array($localConfig)) {
    throw new RuntimeException('Invalid includes/mail.local.php configuration.');
  }
  $setting = static function (string $key, string $env, mixed $default = '') use ($localConfig): mixed {
    $value = getenv($env);
    return ($value !== false && $value !== '') ? $value : ($localConfig[$key] ?? $default);
  };

  $host = trim((string) $setting('host', 'PAYLESS_SMTP_HOST', 'smtp.hostinger.com'));
  $port = (int) $setting('port', 'PAYLESS_SMTP_PORT', 465);
  $username = trim((string) $setting('username', 'PAYLESS_SMTP_USERNAME'));
  $password = (string) $setting('password', 'PAYLESS_SMTP_PASSWORD');
  $encryption = strtolower(trim((string) $setting('encryption', 'PAYLESS_SMTP_ENCRYPTION', 'ssl')));
  $fromEmail = trim((string) $setting('from_email', 'PAYLESS_SMTP_FROM', $username));
  $fromName = trim((string) $setting('from_name', 'PAYLESS_SMTP_FROM_NAME', 'PayLess Equipment Rental'));

  if ($host === '' || $username === '' || $password === '' || $fromEmail === '') {
    throw new RuntimeException('SMTP is not configured. Set PAYLESS_SMTP_* variables or create includes/mail.local.php on the hosting server.');
  }
  if (!filter_var($username, FILTER_VALIDATE_EMAIL) || !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
    throw new RuntimeException('Invalid SMTP username or sender email address.');
  }
  if (!in_array($port, [465, 587], true) || !in_array($encryption, ['ssl', 'tls'], true)) {
    throw new RuntimeException('Use SMTP port 465 with ssl or port 587 with tls.');
  }

  $mail = new PHPMailer(true);
  $mail->isSMTP();
  $mail->Host = $host;
  $mail->SMTPAuth = true;
  $mail->Username = $username;
  $mail->Password = $password;
  $mail->Port = $port;
  $mail->SMTPSecure = $encryption === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
  $mail->SMTPAutoTLS = true;
  $mail->Timeout = 20;
  $mail->CharSet = PHPMailer::CHARSET_UTF8;
  $mail->isHTML(true);
  $mail->setFrom($fromEmail, $fromName);
  $mail->addAddress($recipient);
  if ($replyTo !== '' && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
    $mail->addReplyTo($replyTo);
  }
  $mail->Subject = $subject;
  $mail->Body = $htmlBody;
  $mail->AltBody = $textBody;
  $mail->send();
}
