<?php
$navItems = [
  'Equipment' => page_url('equipment'),
  'About ' => page_url('about'),
  'Gravel ' => page_url('gravel'),
  'Contact' => page_url('contact'),
];
?>
<!doctype html>
<html lang="en" data-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="light dark">
  <title><?= e($pageTitle) ?> | PayLess Equipment Rental</title>
  <meta name="description" content="<?= e($pageDescription) ?>">
  <meta name="theme-color" content="#f4f6f2">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" referrerpolicy="no-referrer">
  <link rel="stylesheet" href="/assets/css/site.css?v=mega-intro-category-border-1">
  <link rel="icon" type="image/png" href="/assets/images/payless-equipment-rental-logo.png?v=favicon-2">
  <link rel="apple-touch-icon" href="/assets/images/payless-equipment-rental-logo.png?v=favicon-2">
  <script>
    (() => {
      try {
        const saved = localStorage.getItem('payless-theme');
        const preferred = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        document.documentElement.dataset.theme = saved || preferred;
      } catch (_) {}
    })();
  </script>
  <script type="application/ld+json"><?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'LocalBusiness',
    'name' => SITE_NAME,
    'telephone' => SITE_PHONE,
    'address' => ['@type' => 'PostalAddress', 'streetAddress' => '4480 SW 185th Ave', 'addressLocality' => 'Beaverton', 'addressRegion' => 'OR', 'postalCode' => '97078', 'addressCountry' => 'US'],
    'areaServed' => ['Beaverton', 'Portland metro area'],
    'description' => 'Construction equipment rental and gravel delivery based in Beaverton, Oregon.'
  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<div class="announcement"><span class="status-dot"></span> BEAVERTON, OREGON <span class="announcement__divider">/</span> EQUIPMENT RENTALS BUILT AROUND YOUR JOB <a href="tel:<?= e(SITE_PHONE_LINK) ?>">CALL <?= e(SITE_PHONE) ?> <span aria-hidden="true">↗</span></a></div>
<header class="site-header" id="top">
  <div class="container nav-wrap">
    <a class="brand" href="/" aria-label="PayLess Equipment Rental home">
      <span class="brand__symbol-frame"><img class="brand__symbol" src="/assets/images/payless-equipment-rental-logo-animated.svg?v=embedded-1" alt=""></span>
      <span class="brand__wordmark-frame" aria-hidden="true"><img class="brand__wordmark-art brand__wordmark-art--light" src="/assets/images/payless-equipment-rental-logo-animated.svg?v=embedded-1" alt=""><img class="brand__wordmark-art brand__wordmark-art--dark" src="/assets/images/payless-equipment-rental-logo-dark-animated.svg?v=embedded-1" alt=""></span>
    </a>
    <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-nav" aria-label="Open navigation"><span></span><span></span></button>
    <nav class="primary-nav" id="primary-nav" aria-label="Main navigation">
      <a class="<?= $page === 'home' ? 'is-active' : '' ?>" href="/">Home</a>
      <?php foreach ($navItems as $label => $url): ?>
        <?php if ($label === 'Equipment'): ?>
          <div class="nav-item--mega <?= $page === 'equipment' ? 'is-active' : '' ?>">
            <button class="nav-mega-trigger" type="button" aria-expanded="false" aria-controls="equipment-mega-menu">Equipments <span class="nav-mega-chevron" aria-hidden="true"></span></button>
            <div class="mega-menu" id="equipment-mega-menu">
              <div class="mega-menu__intro"><p class="eyebrow">THE PAYLESS FLEET</p><strong>Find the right machine for the work ahead.</strong><a href="<?= e($url) ?>">View full catalogue <span aria-hidden="true">↗</span></a></div>
              <div class="mega-menu__categories">
                <?php foreach ($categories as $category): ?>
                  <a class="mega-menu__category" href="<?= e(page_url('home')) ?>#equipment" data-machine-nav-filter="<?= e($category['anchor']) ?>"><img src="<?= e($category['image']) ?>" alt="" loading="lazy"><span><strong><?= e($category['name']) ?></strong><small><?= e($category['detail']) ?></small></span><b aria-hidden="true">↗</b></a>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        <?php else: ?>
          <a class="<?= ($label === 'About ' && $page === 'about') || ($label === 'Gravel ' && $page === 'gravel') || ($label === 'Contact' && $page === 'contact') ? 'is-active' : '' ?>" href="<?= e($url) ?>"><?= e($label) ?></a>
        <?php endif; ?>
      <?php endforeach; ?>
    </nav>
    <div class="nav-actions">
      <button class="theme-toggle" type="button" aria-label="Switch color theme" title="Switch light/dark theme"><span class="theme-toggle__icon" aria-hidden="true">◐</span></button>
      <a class="button button--small button--primary nav-cta" href="<?= e(page_url('home')) ?>#request">Build your list <span aria-hidden="true">↗</span></a>
      <a class="nav-call" href="tel:<?= e(SITE_PHONE_LINK) ?>" aria-label="Call PayLess">Call <span aria-hidden="true">↗</span></a>
    </div>
  </div>
</header>
<main id="main">
