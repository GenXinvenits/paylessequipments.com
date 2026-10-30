<?php
/**
 * Copy to includes/mail.local.php on the hosting server.
 * Replace the placeholder password there. Never commit mail.local.php.
 */
return [
  'host' => 'smtp.hostinger.com',
  'port' => 465,
  'encryption' => 'ssl',
  'username' => 'rentals@paylessequipments.com',
  'password' => 'REPLACE_WITH_REAL_MAILBOX_PASSWORD',
  'from_email' => 'rentals@paylessequipments.com',
  'from_name' => 'PayLess Equipment Rental',
];
