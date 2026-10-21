<?php
declare(strict_types=1);
$base = rtrim((string) getenv('PAYLESS_SITE_URL'), '/');
if ($base === '' || !filter_var($base, FILTER_VALIDATE_URL) || !str_starts_with($base, 'https://')) {
  http_response_code(503);
  header('Content-Type: text/plain; charset=UTF-8');
  echo "Configure PAYLESS_SITE_URL with the verified HTTPS site origin to publish the sitemap.\n";
  exit;
}
require_once __DIR__ . '/includes/site.php';
header('Content-Type: application/xml; charset=UTF-8');
$paths = [
  '/', '/?page=equipment',
  '/?page=about', '/?page=gravel', '/?page=contact'
];
foreach ($equipment as $item) {
  $paths[] = '/?page=equipment&item=' . rawurlencode($item['id']);
}
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
foreach ($paths as $path) {
  $url = htmlspecialchars($base . $path, ENT_XML1 | ENT_QUOTES, 'UTF-8');
  echo "  <url><loc>{$url}</loc></url>\n";
}
echo "</urlset>\n";
