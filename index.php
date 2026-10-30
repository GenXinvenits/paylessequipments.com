<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/site.php';
$page = isset($_GET['page']) ? (string) $_GET['page'] : 'home';
// Keep old rental-terms URLs working by routing them to the unified Terms & Conditions page.
if ($page === 'rental-terms') { $page = 'terms'; }
$allowedPages = ['home', 'equipment', 'about', 'gravel','attachments', 'contact', 'privacy', 'terms', 'disclaimer'];
if (!in_array($page, $allowedPages, true)) { http_response_code(404); $page = '404'; }
$pageTitles = [
  'home' => 'Construction Equipment Rental in Beaverton, OR',
  'equipment' => 'Construction Equipment for Rent',
  'about' => 'Equipment Rental Built Around the Way You Work',
  'gravel' => 'Gravel Delivery in Beaverton, Oregon',
  'attachments' => 'Equipment Attachments for Rent | PayLess Equipment Rental',
  'contact' => 'Contact PayLess Equipment Rental',
  'privacy' => 'Privacy Notice | PayLess Equipment Rental',
  'terms' => 'Website Terms | PayLess Equipment Rental',
  'disclaimer' => 'Website Disclaimer | PayLess Equipment Rentals LLC',
  '404' => 'Page Not Found'
];
$pageTitle = $pageTitles[$page] ?? $pageTitles['404'];
if ($page === 'equipment' && isset($_GET['item'])) {
  foreach ($equipment as $listedItem) {
    if ($listedItem['id'] === (string) $_GET['item']) {
      $pageTitle = $listedItem['name'] . ' Rental in Beaverton, OR';
      break;
    }
  }
}
$pageDescription = $page === 'home'
  ? 'Browse equipment for rent and sale in Beaverton, Oregon. Compare current prices and submit an equipment request with PayLess.'
  : ($page === 'disclaimer' ? 'Website disclaimer, equipment safety information, availability, pricing and rental terms from PayLess Equipment Rentals LLC.' : 'Explore equipment options and current prices from PayLess Equipment Rental LLC in Beaverton, Oregon.');
require __DIR__ . '/includes/header.php';
if ($page === 'home') {
  require __DIR__ . '/pages/home.php';
} elseif ($page === 'equipment') {
  require __DIR__ . '/pages/equipment.php';
} elseif ($page === 'about') {
  require __DIR__ . '/pages/about.php';
} elseif ($page === 'gravel') {
  require __DIR__ . '/pages/gravel.php';
} elseif ($page === 'attachments') {
  require __DIR__ . '/pages/attachments.php';
} elseif ($page === 'contact') {
  require __DIR__ . '/pages/contact.php';
} elseif (in_array($page, ['privacy', 'terms', 'disclaimer'], true)) {
  require __DIR__ . '/pages/legal.php';
} else {
  echo '<main class="container section"><p class="eyebrow">404 / NOT FOUND</p><h1>Looks like this path is off the job site.</h1><a class="button button--primary" href="/">Back to home <span aria-hidden="true">↗</span></a></main>';
}
require __DIR__ . '/includes/footer.php';
