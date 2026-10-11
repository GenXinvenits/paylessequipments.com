<?php
$legalPages = [
  'privacy' => [
    'label' => 'SITE INFORMATION / PRIVACY',
    'title' => 'Your details.<br><span>Handled with care.</span>',
    'intro' => 'This notice explains the intended use of information submitted through the PayLess website.',
    'sections' => [
      ['Information you submit', 'Equipment requests may include your name, company, phone number, email, job-site location, dates, project details and equipment selections. Please do not submit sensitive personal information that is not needed for your request.'],
      ['How requests are used', 'Details submitted through the form are used to review your inquiry and respond about equipment availability, pricing, delivery options and next steps. The website does not take online payments.'],
      ['Database storage, hosting and email', 'Equipment and gravel enquiries are stored in the website operator’s MariaDB database so PayLess can review and respond to requests. If configured, an email notification is also sent to the business inbox. Hosting and email providers may process information to deliver their services, and hosting systems may maintain technical logs. Access to stored enquiries should be limited to authorised staff.'],
      ['Retention and your choices', 'The business operator should retain enquiries only as long as needed for business, legal and recordkeeping purposes, then securely delete them. To ask about your information or request a correction or deletion, contact PayLess at (503) 259-0252. This notice should be reviewed against the actual hosting setup and applicable privacy laws before launch.']
    ]
  ],
  'terms' => [
    'label' => 'SITE INFORMATION / WEBSITE TERMS',
    'title' => 'Clear information.<br><span>Clear next steps.</span>',
    'intro' => 'These terms describe use of this informational website. They do not replace a signed rental agreement or other written agreement with PayLess Equipment Rental LLC.',
    'sections' => [
      ['Website information', 'Equipment descriptions, specifications, photographs and rates help visitors start a conversation. Confirm current machine configuration, pricing, availability, delivery charges and rental requirements directly with PayLess.'],
      ['Requests are not reservations', 'Submitting an equipment or gravel inquiry does not reserve equipment, guarantee delivery or create a rental agreement. A booking requires direct confirmation from PayLess and any required rental documents.'],
      ['Changes and corrections', 'Fleet details, prices and website content may change. Contact the team for confirmation before making project plans based on this website.'],
      ['Third-party services', 'The site may load images, fonts or scripts from third-party providers. Their services may be governed by their own terms and privacy practices. For questions, call (503) 259-0252.']
    ]
  ],
  'rental-terms' => [
    'label' => 'SITE INFORMATION / RENTAL TERMS',
    'title' => 'Know the terms<br><span>before the machine arrives.</span>',
    'intro' => 'This is a general guide only. The current written rental agreement and direct confirmation from PayLess govern each rental.',
    'sections' => [
      ['Rates and rental period', 'Confirm the exact rental period, current rate, applicable charges, extension rules and payment requirements before booking. Published website rates may change; monthly rates may require a quote.'],
      ['Availability and delivery', 'Availability is not guaranteed until PayLess confirms it. Delivery options, transport charges, timing, site access and pickup arrangements depend on the equipment and location.'],
      ['Machine setup and safe use', 'Confirm dimensions, attachment availability and charges, fuel and return expectations, capacity, operator requirements and site suitability before use. Follow manufacturer instructions and applicable laws and safety requirements.'],
      ['Other rental conditions', 'Insurance, deposits, damage responsibility, late returns, cleaning, cancellation and other contractual conditions vary and must be stated in the current written rental agreement. This website does not set or promise those terms.'],
      ['Get current terms', 'Contact PayLess at (503) 259-0252 to discuss your project and obtain the applicable written rental terms before committing to a rental.']
    ]
  ]
];
$content = $legalPages[$page] ?? $legalPages['terms'];
?>
<section class="page-hero page-hero--contact"><div class="container page-hero__inner"><p class="eyebrow"><span class="eyebrow-line"></span><?= e($content['label']) ?></p><h1><?= $content['title'] ?></h1><p><?= e($content['intro']) ?></p><a class="button button--primary" href="<?= e(page_url('contact')) ?>">Contact PayLess <span>↗</span></a></div></section>
<section class="section legal-section"><div class="container legal-layout"><aside><p class="eyebrow"><span class="eyebrow-line"></span> PAYLESS EQUIPMENT RENTAL LLC</p><p>4480 SW 185th Ave<br>Beaverton, OR 97078</p><a class="text-link" href="tel:<?= e(SITE_PHONE_LINK) ?>"><?= e(SITE_PHONE) ?> <span>↗</span></a><p class="legal-review-note">Business-specific legal terms should be reviewed and approved before launch.</p></aside><div class="legal-content"><?php foreach ($content['sections'] as $i => $section): ?><article><span class="legal-number">0<?= $i + 1 ?></span><div><h2><?= e($section[0]) ?></h2><p><?= e($section[1]) ?></p></div></article><?php endforeach; ?></div></div></section>
