<?php
declare(strict_types=1);
$base = rtrim((string) getenv('PAYLESS_SITE_URL'), '/');
if ($base === '' || !filter_var($base, FILTER_VALIDATE_URL) || !str_starts_with($base, 'https://')) {
  http_response_code(503);
  header('Content-Type: text/plain; charset=UTF-8');
  echo "Configure PAYLESS_SITE_URL with the verified HTTPS site origin to publish the sitemap.\n";
  exit;
}
header('Content-Type: application/xml; charset=UTF-8');
$paths = [
  '/', '/?page=equipment',
  '/?page=equipment&item=freightliner-m2',
  '/?page=equipment&item=sl-1050k',
  '/?page=equipment&item=rhf30-forklift',
  '/?page=equipment&item=john-deere-35',
  '/?page=equipment&item=bobcat-mt120',
  '/?page=equipment&item=cat-306-cr',
  '/?page=about', '/?page=gravel', '/?page=contact'
];
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
foreach ($paths as $path) {
  $url = htmlspecialchars($base . $path, ENT_XML1 | ENT_QUOTES, 'UTF-8');
  echo "  <url><loc>{$url}</loc></url>\n";
}
echo "</urlset>\n";
