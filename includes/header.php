<?php
$navItems = [
  'Equipment' => page_url('equipment'),
  'About Us' => page_url('about'),
  'Gravel Delivery' => page_url('gravel'),
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
  <meta name="theme-color" content="#f3f2ed">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" referrerpolicy="no-referrer">
  <link rel="stylesheet" href="/assets/css/site.css?v=hero-motion-2">
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Crect width='40' height='40' rx='8' fill='%23d8f15b'/%3E%3Cpath d='M9 11h22v6H15v3h13v6H15v3H9z' fill='%231a2016'/%3E%3C/svg%3E">
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
        <a class="<?= ($label === 'Equipment' && $page === 'equipment') || ($label === 'About Us' && $page === 'about') || ($label === 'Gravel Delivery' && $page === 'gravel') || ($label === 'Contact' && $page === 'contact') ? 'is-active' : '' ?>" href="<?= e($url) ?>"><?= e($label) ?></a>
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
