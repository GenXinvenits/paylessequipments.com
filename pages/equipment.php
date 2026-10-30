<?php
$itemId = isset($_GET['item']) ? (string) $_GET['item'] : '';
$selectedEquipment = null;
foreach ($equipment as $candidate) { if ($candidate['id'] === $itemId) { $selectedEquipment = $candidate; break; } }
if ($selectedEquipment) {
  $pageTitle = $selectedEquipment['name'] . ' Rental in Beaverton, OR';
  $faqs = [
    ['What is the current price?', 'The current and previous prices are shown below where supplied. Contact PayLess to confirm current availability and pricing details.'],
    ['How do I confirm availability?', 'Submit an equipment request with your preferred dates and job-site location, or call (503) 901-9990.'],
    ['Can this equipment be delivered?', 'Delivery options and charges depend on equipment and job-site location. Contact PayLess to confirm details.'],
    ['What should I check before booking?', 'Confirm dimensions, site access, operating requirements, transport, attachments and current rental terms with PayLess.'],
    ['Can I request more than one machine?', 'Yes. Add this machine to your equipment list and select any additional equipment in the request builder.']
  ];
  $offer = null;
  if ($selectedEquipment['price'] !== 'Contact') { $price = preg_replace('/[^0-9.]/', '', $selectedEquipment['price']); if ($price !== '') $offer = ['@type'=>'Offer','price'=>$price,'priceCurrency'=>'USD','availability'=>'https://schema.org/LimitedAvailability']; }
  ?>
  <section class="page-hero page-hero--equipment"><div class="container page-hero__inner"><p class="eyebrow"><span class="eyebrow-line"></span><?= e($selectedEquipment['category']) ?> / BEAVERTON, OR</p><h1><?= e($selectedEquipment['name']) ?><br><span>Rental for the work ahead.</span></h1><p><?= e($selectedEquipment['description']) ?></p><a class="button button--primary" href="<?= e(page_url('home')) ?>#request" data-add-link="<?= e($selectedEquipment['name']) ?>">Add to equipment list <span>↗</span></a><a class="page-hero__call" href="tel:<?= e(SITE_PHONE_LINK) ?>">Call <?= e(SITE_PHONE) ?></a></div></section>
  <section class="section equipment-detail"><div class="container equipment-detail__layout"><div class="equipment-detail__media"><img src="<?= e($selectedEquipment['image']) ?>" alt="<?= e($selectedEquipment['alt']) ?>"><span class="equipment-detail__badge"><?= e($selectedEquipment['category']) ?></span></div><div class="equipment-detail__intro"><p class="eyebrow"><span class="eyebrow-line"></span> EQUIPMENT OVERVIEW</p><h2>Built for the work<br><span>you need to do.</span></h2><p><?= e($selectedEquipment['description']) ?></p><p class="form-disclaimer">Representative product photo; exact rental unit and configuration may vary.</p><div class="equipment-detail__rates equipment-detail__rates--prices"><div><small>CURRENT PRICE</small><strong><?= e($selectedEquipment['price']) ?></strong></div><div><small>PREVIOUS PRICE</small><strong><?= $selectedEquipment['previous'] !== '' ? '<del>' . e($selectedEquipment['previous']) . '</del>' : '—' ?></strong></div></div><?php if (!empty($selectedEquipment['rentalRates'])): ?><div class="equipment-detail__rates equipment-detail__rates--rental"><div><small>DAILY</small><strong><?= e($selectedEquipment['rentalRates']['daily']) ?></strong></div><div><small>WEEKLY</small><strong><?= e($selectedEquipment['rentalRates']['weekly']) ?></strong></div><div><small>MONTHLY</small><strong><?= e($selectedEquipment['rentalRates']['monthly']) ?></strong></div></div><?php endif; ?><?php if (!empty($selectedEquipment['rentalNotice'])): ?><p class="form-disclaimer"><?= e($selectedEquipment['rentalNotice']) ?></p><?php endif; ?><?php if (!empty($selectedEquipment['deliveryNotice'])): ?><p class="form-disclaimer"><?= e($selectedEquipment['deliveryNotice']) ?></p><?php endif; ?><a class="button button--primary button--wide" href="<?= e(page_url('home')) ?>#request" data-add-link="<?= e($selectedEquipment['name']) ?>">Request this equipment <span>↗</span></a><p class="form-disclaimer">Prices and availability are subject to confirmation. No booking is created by submitting a request.</p></div></div></section>
  <section class="section equipment-spec-section"><div class="container equipment-spec-layout"><div><p class="eyebrow"><span class="eyebrow-line"></span> MACHINE DETAILS</p><h2>Know the<br><span>specs.</span></h2><p>Key specifications from the supplied PayLess project content. Confirm the current machine configuration before booking.</p></div><div><div class="spec-table"><div><span>Equipment</span><strong><?= e($selectedEquipment['name']) ?></strong></div><div><span>Category</span><strong><?= e($selectedEquipment['category']) ?></strong></div><div><span>Key specifications</span><strong><?= e($selectedEquipment['spec']) ?></strong></div><div><span>Typical applications</span><strong><?= e($selectedEquipment['description']) ?></strong></div><div><span>Rental availability</span><strong>Contact PayLess to confirm dates, transport and site access.</strong></div></div><div class="best-for"><p class="eyebrow">BEFORE YOU BOOK</p><p>Tell us about access restrictions, attachments, job-site conditions, rental duration and delivery needs. The team will confirm the correct setup and current terms.</p></div></div></div></section>
  <section class="section equipment-faq-section"><div class="container faq-layout"><div><p class="eyebrow"><span class="eyebrow-line"></span> MACHINE FAQ</p><h2>Before you<br><span>book.</span></h2><p>Check the details that matter before equipment reaches your site.</p></div><div class="faq-list"><?php foreach ($faqs as $i=>$faq): ?><details class="faq-item" <?= $i===0?'open':'' ?>><summary><span><?= e($faq[0]) ?></span><b aria-hidden="true">+</b></summary><div class="faq-item__answer"><p><?= e($faq[1]) ?></p></div></details><?php endforeach; ?></div></div></section>
  <section class="final-cta final-cta--compact"><div class="container final-cta__inner"><p class="eyebrow">CHECK AVAILABILITY / GET A QUOTE</p><h2>Get the right<br><span>machine on site.</span></h2><div><a class="button button--primary" href="<?= e(page_url('home')) ?>#request" data-add-link="<?= e($selectedEquipment['name']) ?>">Build your equipment list <span>↗</span></a><a class="final-cta__phone" href="tel:<?= e(SITE_PHONE_LINK) ?>">Call <?= e(SITE_PHONE) ?> ↗</a></div></div></section>
  <script type="application/ld+json"><?= json_encode(array_filter(['@context'=>'https://schema.org','@type'=>'Product','name'=>$selectedEquipment['name'],'description'=>$selectedEquipment['description'],'category'=>$selectedEquipment['category'],'offers'=>$offer]), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) ?></script>
  <script type="application/ld+json"><?= json_encode(['@context'=>'https://schema.org','@type'=>'FAQPage','mainEntity'=>array_map(fn($faq)=>['@type'=>'Question','name'=>$faq[0],'acceptedAnswer'=>['@type'=>'Answer','text'=>$faq[1]]],$faqs)], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) ?></script>
  <?php return;
}
?>
<section class="page-hero page-hero--equipment"><div class="container page-hero__inner"><p class="eyebrow"><span class="eyebrow-line"></span> CURRENT FLEET / BEAVERTON, OR</p><h1>Construction equipment<br><span>for the work ahead.</span></h1><p>Browse the current product list, compare current and previous prices, and add equipment to one request. Product photos are representative; actual units and configurations may vary.</p><a class="button button--primary" href="<?= e(page_url('home')) ?>#request">Build your equipment list <span>↗</span></a></div></section>
<section class="section catalogue-section">
  <div class="container">
    <div class="catalogue-toolbar"><span>ALL EQUIPMENT BY CATEGORY</span><span>PRICES IN USD · AVAILABILITY SUBJECT TO CONFIRMATION</span></div>
    <?php
    $catalogueCategories = $categories;
    foreach ($equipment as $catalogueItem) {
      $knownCategory = false;
      foreach ($catalogueCategories as $catalogueCategory) {
        if ($catalogueCategory['name'] === $catalogueItem['category']) { $knownCategory = true; break; }
      }
      if (!$knownCategory) {
        $catalogueCategories[] = [
          'name' => $catalogueItem['category'],
          'detail' => 'Browse available ' . strtolower($catalogueItem['category']) . ' options',
          'icon' => 'fa-solid fa-gear',
          'anchor' => 'cat-' . strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $catalogueItem['category']), '-'))
        ];
      }
    }
    $availableCatalogueCategories = [];
    foreach ($catalogueCategories as $catalogueCategory) {
      $categoryItems = array_values(array_filter($equipment, static fn($catalogueItem) => $catalogueItem['category'] === $catalogueCategory['name']));
      if ($categoryItems) $availableCatalogueCategories[] = ['category' => $catalogueCategory, 'items' => $categoryItems];
    }
    ?>
    <div class="catalogue-tabs" role="tablist" aria-label="Equipment categories">
      <?php foreach ($availableCatalogueCategories as $categoryIndex => $catalogueGroup): ?>
      <?php $catalogueCategory = $catalogueGroup['category']; $categoryItems = $catalogueGroup['items']; ?>
      <button class="catalogue-tab<?= $categoryIndex === 0 ? ' is-active' : '' ?>" type="button" role="tab" id="catalogue-tab-<?= e($catalogueCategory['anchor']) ?>" aria-controls="catalogue-panel-<?= e($catalogueCategory['anchor']) ?>" aria-selected="<?= $categoryIndex === 0 ? 'true' : 'false' ?>" tabindex="<?= $categoryIndex === 0 ? '0' : '-1' ?>" data-catalogue-tab>
        <span><?= e($catalogueCategory['name']) ?></span><small><?= count($categoryItems) ?></small>
      </button>
      <?php endforeach; ?>
    </div>
    <?php foreach ($availableCatalogueCategories as $categoryIndex => $catalogueGroup): ?>
    <?php $catalogueCategory = $catalogueGroup['category']; $categoryItems = $catalogueGroup['items']; ?>
    <section class="catalogue-category<?= $categoryIndex === 0 ? ' is-active' : '' ?>" id="catalogue-panel-<?= e($catalogueCategory['anchor']) ?>" role="tabpanel" aria-labelledby="catalogue-tab-<?= e($catalogueCategory['anchor']) ?>"<?= $categoryIndex === 0 ? '' : ' hidden' ?>>
      <div class="section-heading section-heading--split catalogue-category__heading">
        <div><p class="eyebrow"><span class="eyebrow-line"></span> <?= e(strtoupper($catalogueCategory['name'])) ?> / <?= count($categoryItems) ?> <?= count($categoryItems) === 1 ? 'OPTION' : 'OPTIONS' ?></p><h2 id="<?= e($catalogueCategory['anchor']) ?>-title"><?= e($catalogueCategory['name']) ?><br><span>Find the right fit.</span></h2></div>
        <div class="section-heading__aside"><p><?= e($catalogueCategory['detail']) ?>. Product photos are representative; confirm current availability and rates with PayLess.</p></div>
      </div>
      <div class="equipment-grid">
        <?php foreach ($categoryItems as $item): ?>
        <article class="equipment-card" id="<?= e($item['id']) ?>" data-reveal>
          <a class="equipment-card__image" href="<?= e(page_url('equipment')) ?>&amp;item=<?= e($item['id']) ?>" aria-label="View <?= e($item['name']) ?>"><img src="<?= e($item['image']) ?>" alt="<?= e($item['alt']) ?>" loading="lazy"><span class="equipment-card__image-arrow">↗</span></a>
          <div class="equipment-card__body">
            <p class="equipment-card__category"><?= e($item['category']) ?> <span>•</span> <?= e($item['tag']) ?></p>
            <h3><a href="<?= e(page_url('equipment')) ?>&amp;item=<?= e($item['id']) ?>"><?= e($item['name']) ?></a></h3>
            <p class="equipment-card__description"><?= e($item['description']) ?></p>
            <p class="equipment-card__spec"><?= e($item['spec']) ?></p>
            <div class="equipment-card__rates"><div><small>CURRENT PRICE</small><strong><?= e($item['price']) ?></strong></div><div><small>PREVIOUS PRICE</small><strong><?= $item['previous'] !== '' ? '<del>' . e($item['previous']) . '</del>' : '—' ?></strong></div></div>
            <div class="equipment-card__actions"><a href="<?= e(page_url('equipment')) ?>&amp;item=<?= e($item['id']) ?>">View details <span>↗</span></a><button type="button" class="add-equipment" data-add-equipment="<?= e($item['name']) ?>">+ Add to list</button></div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endforeach; ?>
    <p class="catalogue-disclaimer">Prices and specifications are based on the supplied product list. Confirm current availability, equipment details, rental period and any delivery charges with PayLess.</p>
  </div>
</section>
<section class="final-cta final-cta--compact"><div class="container final-cta__inner"><p class="eyebrow">READY WHEN YOU ARE</p><h2>Let's line up<br><span>your equipment.</span></h2><div><a class="button button--primary" href="<?= e(page_url('home')) ?>#request">Build your equipment list <span>↗</span></a><a class="final-cta__phone" href="tel:<?= e(SITE_PHONE_LINK) ?>">Call <?= e(SITE_PHONE) ?> ↗</a></div></div></section>
