<?php
declare(strict_types=1);

define('SITE_NAME', 'PayLess Equipment Rental LLC');
define('SITE_PHONE', '(503) 259-0252');
define('SITE_PHONE_LINK', '+15032590252');
define('SITE_ADDRESS', '4480 SW 185th Ave, Beaverton, OR 97078');
define('SITE_EMAIL', getenv('PAYLESS_CONTACT_EMAIL') ?: '');

$equipment = [
  [
    'id' => 'freightliner-m2', 'name' => '2027 Freightliner M2 Dump Truck', 'category' => 'Dump Trucks',
    'tag' => 'Material hauling', 'description' => 'Move dirt, gravel, debris and other materials around the job site.',
    'spec' => '8–10 cu yd capacity · 360 HP · Non-CDL listed',
    'daily' => '$275', 'weekly' => '$1,375', 'monthly' => 'Contact for pricing',
    'image' => '/assets/images/product-dump-truck.webp',
    'alt' => 'Representative Freightliner M2 dump truck product photo'
  ],
  [
    'id' => 'sl-1050k', 'name' => 'SL-1050K Mini Skid Steer', 'category' => 'Mini Skid Steers',
    'tag' => 'Compact power', 'description' => 'A compact machine for material handling, grading and landscaping work.',
    'spec' => '24 HP diesel · 39 in wide · 1,050 lb capacity',
    'daily' => '$200', 'weekly' => '$1,000', 'monthly' => 'Call for availability',
    'image' => '/assets/images/product-mini-skid-steer.webp',
    'alt' => 'SDLOOL SL-1050K mini skid steer product photo'
  ],
  [
    'id' => 'rhf30-forklift', 'name' => 'RHF30 Forklift', 'category' => 'Forklifts',
    'tag' => 'Material handling', 'description' => 'Move pallets and heavy materials around construction and industrial sites.',
    'spec' => '6,600 lb lift capacity · 15 ft max lift · Diesel',
    'daily' => '$200', 'weekly' => '$1,000', 'monthly' => 'Call for availability',
    'image' => '/assets/images/product-forklift.webp',
    'alt' => 'Representative forklift operating in a warehouse'
  ],
  [
    'id' => 'john-deere-35', 'name' => 'John Deere 35 P-Tier Compact Excavator', 'category' => 'Mini Excavators',
    'tag' => 'Compact excavation', 'description' => 'Compact digging performance for foundations, trenches and tight job sites.',
    'spec' => '23.3 HP diesel · 10 ft dig depth · Zero tail swing',
    'daily' => '$250', 'weekly' => '$1,250', 'monthly' => 'Call for availability',
    'image' => '/assets/images/product-mini-excavator.webp',
    'alt' => 'John Deere 35 P compact excavator product photo'
  ],
  [
    'id' => 'bobcat-mt120', 'name' => 'Bobcat MT120 Mini Track Loader', 'category' => 'Track Loaders',
    'tag' => 'Tight-access work', 'description' => 'Compact track loader for grading, loading, clearing and site cleanup.',
    'spec' => '41 in wide · 1,200 lb rated capacity · 81 in lift height',
    'daily' => '$275', 'weekly' => '$1,350', 'monthly' => 'Call for pricing',
    'image' => '/assets/images/product-track-loader.webp',
    'alt' => 'Bobcat MT120 mini track loader at work'
  ],
  [
    'id' => 'cat-306-cr', 'name' => '2026 CAT 306 CR Excavator', 'category' => 'Excavators',
    'tag' => '6-ton class', 'description' => 'A larger compact excavator for demanding digging and site-preparation work.',
    'spec' => '55.9 HP · 13.5 ft max dig depth · 21.4 ft max reach',
    'daily' => 'Contact', 'weekly' => 'Contact', 'monthly' => 'Contact for current rates',
    'image' => '/assets/images/product-excavator.webp',
    'alt' => 'Cat 306 CR mini hydraulic excavator product photo'
  ],
];

$categories = [
  ['name' => 'Excavators', 'detail' => 'Digging, trenching & site preparation', 'icon' => 'fa-solid fa-person-digging', 'anchor' => 'cat-excavators'],
  ['name' => 'Mini Excavators', 'detail' => 'Tight spaces & residential work', 'icon' => 'fa-solid fa-trowel-bricks', 'anchor' => 'cat-mini-excavators'],
  ['name' => 'Mini Skid Steers', 'detail' => 'Compact handling & grading', 'icon' => 'fa-solid fa-truck-front', 'anchor' => 'cat-mini-skid-steers'],
  ['name' => 'Track Loaders', 'detail' => 'Loading, clearing & material movement', 'icon' => 'fa-solid fa-truck-monster', 'anchor' => 'cat-track-loaders'],
  ['name' => 'Dump Trucks', 'detail' => 'Dirt, gravel & material hauling', 'icon' => 'fa-solid fa-truck', 'anchor' => 'cat-dump-trucks'],
  ['name' => 'Forklifts', 'detail' => 'Pallets & heavy materials', 'icon' => 'fa-solid fa-people-carry-box', 'anchor' => 'cat-forklifts'],
];

function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function page_url(string $page = 'home'): string { return $page === 'home' ? '/' : '/?page=' . rawurlencode($page); }
