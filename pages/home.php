<section class="hero">
  <div class="hero__background" aria-hidden="true"></div>
  <div class="hero__background-shade" aria-hidden="true"></div>
  <div class="hero__grid container">
    <div class="hero__copy">
      <p class="eyebrow" data-reveal><span class="eyebrow-line"></span> EQUIPMENT RENTAL / BEAVERTON, OR</p>
      <h1 data-reveal>PayLess.<br>Get More.<br><span>Get The Job Done.</span></h1>
      <p class="hero__sub" data-reveal>Cost-effective construction equipment rentals without the corporate markup.</p>
      <p class="hero__body" data-reveal>Excavators, skid steers, trailers, lifts &amp; more — dependable equipment, straightforward pricing.</p>
      <div class="hero__actions" data-reveal><a class="button button--primary" href="#equipment">View equipment <span aria-hidden="true">↗</span></a><a class="button button--outline" href="#request">Get a quote <span aria-hidden="true">↗</span></a></div>
      <div class="hero__meta" data-reveal><span><i class="fa-solid fa-award" aria-hidden="true"></i> Competitive rates</span><span><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Reliable equipment</span><span><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Local service</span></div>
    </div>
  </div>
</section>

<section class="value-bar" aria-label="PayLess service benefits">
  <div class="container value-bar__inner"><div><span class="value-icon"><i class="fa-solid fa-tags" aria-hidden="true"></i></span><span><strong>COST-EFFECTIVE RATES</strong><small>Practical pricing for real projects</small></span></div><div><span class="value-icon"><i class="fa-solid fa-helmet-safety" aria-hidden="true"></i></span><span><strong>EQUIPMENT THAT WORKS</strong><small>Capable machines for demanding jobs</small></span></div><div><span class="value-icon"><i class="fa-regular fa-calendar-check" aria-hidden="true"></i></span><span><strong>FLEXIBLE RENTALS</strong><small>Daily, weekly & selected monthly</small></span></div><div><span class="value-icon"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span><span><strong>LOCAL SERVICE</strong><small>Based in Beaverton, Oregon</small></span></div></div>
</section>

<section class="section equipment-fleet" id="equipment">
  <div class="container">
    <div class="section-heading section-heading--split"><div><p class="eyebrow"><span class="eyebrow-line"></span> FIND YOUR MACHINE · THE FLEET</p><h2>Right tool.<br><span>Right on time.</span></h2></div><div class="section-heading__aside"><p>Browse the fleet by equipment type. Product photos are representative; exact rental units and configurations may vary. Contact us to confirm availability and current rates.</p><a class="text-link" href="<?= e(page_url('equipment')) ?>">View full catalogue <span>↗</span></a></div></div>
    <div class="catalogue-tabs" role="tablist" aria-label="Equipment categories">
      <?php
      $homepageCatalogueCategories = $categories;
      foreach ($equipment as $homepageCatalogueItem) {
        $knownHomepageCategory = false;
        foreach ($homepageCatalogueCategories as $homepageCatalogueCategory) {
          if ($homepageCatalogueCategory['name'] === $homepageCatalogueItem['category']) { $knownHomepageCategory = true; break; }
        }
        if (!$knownHomepageCategory) {
          $homepageCatalogueCategories[] = [
            'name' => $homepageCatalogueItem['category'],
            'detail' => 'Browse available ' . strtolower($homepageCatalogueItem['category']) . ' options',
            'icon' => 'fa-solid fa-gear',
            'anchor' => 'cat-' . strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $homepageCatalogueItem['category']), '-'))
          ];
        }
      }
      $homepageCatalogueGroups = [];
      foreach ($homepageCatalogueCategories as $homepageCatalogueCategory) {
        $homepageCategoryItems = array_values(array_filter($equipment, static fn($homepageCatalogueItem) => $homepageCatalogueItem['category'] === $homepageCatalogueCategory['name']));
        if ($homepageCategoryItems) $homepageCatalogueGroups[] = ['category' => $homepageCatalogueCategory, 'items' => $homepageCategoryItems];
      }
      ?>
      <?php foreach ($homepageCatalogueGroups as $categoryIndex => $catalogueGroup): ?>
      <?php $catalogueCategory = $catalogueGroup['category']; $categoryItems = $catalogueGroup['items']; ?>
      <button class="catalogue-tab<?= $categoryIndex === 0 ? ' is-active' : '' ?>" type="button" role="tab" id="home-catalogue-tab-<?= e($catalogueCategory['anchor']) ?>" aria-controls="home-catalogue-panel-<?= e($catalogueCategory['anchor']) ?>" aria-selected="<?= $categoryIndex === 0 ? 'true' : 'false' ?>" tabindex="<?= $categoryIndex === 0 ? '0' : '-1' ?>" data-catalogue-tab data-machine-nav-filter="<?= e($catalogueCategory['anchor']) ?>">
        <span><?= e($catalogueCategory['name']) ?></span><small><?= count($categoryItems) ?></small>
      </button>
      <?php endforeach; ?>
    </div>
    <?php foreach ($homepageCatalogueGroups as $categoryIndex => $catalogueGroup): ?>
    <?php $catalogueCategory = $catalogueGroup['category']; $categoryItems = $catalogueGroup['items']; ?>
    <section class="catalogue-category<?= $categoryIndex === 0 ? ' is-active' : '' ?>" id="home-catalogue-panel-<?= e($catalogueCategory['anchor']) ?>" role="tabpanel" aria-labelledby="home-catalogue-tab-<?= e($catalogueCategory['anchor']) ?>"<?= $categoryIndex === 0 ? '' : ' hidden' ?>>
      <div class="section-heading section-heading--split catalogue-category__heading">
        <div><p class="eyebrow"><span class="eyebrow-line"></span> <?= e(strtoupper($catalogueCategory['name'])) ?> / <?= count($categoryItems) ?> <?= count($categoryItems) === 1 ? 'OPTION' : 'OPTIONS' ?></p><h2 id="home-<?= e($catalogueCategory['anchor']) ?>-title"><?= e($catalogueCategory['name']) ?><br><span>Find the right fit.</span></h2></div>
        <div class="section-heading__aside"><p><?= e($catalogueCategory['detail']) ?>. Product photos are representative; confirm current availability and rates with PayLess.</p></div>
      </div>
      <div class="equipment-grid">
        <?php foreach ($categoryItems as $item): ?>
        <article class="equipment-card" id="home-<?= e($item['id']) ?>" data-reveal>
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
  </div>
</section>

<section class="request-section section" id="request">
  <img class="request-section__crane" src="/assets/images/animated-crane.svg?v=2" alt="" aria-hidden="true">
  <div class="container request-layout">
    <div class="request-intro"><p class="eyebrow"><span class="eyebrow-line"></span> LET'S GET TO WORK</p><h2>Build your<br><span>equipment list.</span></h2><p>Need more than one machine? Send your equipment list, timing and job-site details in one request. We’ll follow up with availability, pricing and next steps.</p><div class="request-step"><div><strong>Choose your machines</strong><small>Add one or more equipment options.</small></div></div><div class="request-step"><div><strong>Tell us about the job</strong><small>Dates, location and project type.</small></div></div><div class="request-step"><div><strong>We’ll follow up</strong><small>Confirm availability and current pricing.</small></div></div><p class="request-phone">Prefer to talk? <a href="tel:<?= e(SITE_PHONE_LINK) ?>"><?= e(SITE_PHONE) ?> ↗</a></p></div>
    <form class="request-form" action="/request.php" method="post">
      <input type="hidden" name="form_type" value="equipment">
      <div class="form-heading"><span>REQUEST</span><span class="form-required">* REQUIRED</span></div>
      <fieldset class="equipment-choice"><legend>What equipment do you need? <em>*</em></legend>
        <details class="equipment-picker">
          <summary><span data-equipment-summary>Choose equipment</span><span class="equipment-picker__chevron" aria-hidden="true">⌄</span></summary>
          <div class="equipment-picker__options">
            <?php foreach ($equipment as $item): ?><label class="equipment-picker__option"><input type="checkbox" name="equipment[]" value="<?= e($item['name']) ?>" data-equipment-option><span><?= e($item['name']) ?></span></label><?php endforeach; ?>
            <label class="equipment-picker__option"><input type="checkbox" name="equipment[]" value="Other / not sure" data-equipment-option><span>Other / not sure</span></label>
          </div>
        </details>
        <small class="field-error" data-equipment-error hidden>Please select at least one equipment option.</small>
      </fieldset>
      <div class="form-row"><label>Rental period <em>*</em><select name="rental_period" required><option value="">Choose a rental period</option><option>Daily</option><option>Weekly</option><option>Monthly</option><option>Not sure yet</option></select></label><label>Preferred start date<input type="date" name="start_date"></label></div>
      <div class="form-row"><label>Project type <em>*</em><select name="project_type" required><option value="">Select project type</option><option>Construction</option><option>Landscaping</option><option>Residential</option><option>Commercial</option><option>Utility</option><option>Site preparation</option><option>Material handling</option><option>Other</option></select></label><label>Job-site ZIP code <input name="zip" autocomplete="postal-code" placeholder="e.g. 97078"></label></div>
      <label>Job-site location <em>*</em><input name="job_location" required placeholder="Street, city or job-site address" autocomplete="street-address"></label>
      <label>Project details / attachments needed<textarea name="details" rows="3" placeholder="Tell us about the work, access, attachments or delivery needs."></textarea></label>
      <div class="form-row"><label>Your name <em>*</em><input name="name" required autocomplete="name" placeholder="Full name"></label><label>Company / Individual<input name="company" autocomplete="organization" placeholder="Company or individual name"></label></div>
      <div class="form-row"><label>Phone <em>*</em><input type="tel" name="phone" required autocomplete="tel" placeholder="(503) 555-0123"></label><label>Email <em>*</em><input type="email" name="email" required autocomplete="email" placeholder="you@company.com"></label></div>
      <label class="consent"><input type="checkbox" name="contact_consent" value="yes" required><span>I agree that PayLess may contact me about this request.</span></label>
      <button class="button button--primary button--wide" type="submit">Send equipment request <span aria-hidden="true">↗</span></button>
      <p class="form-disclaimer">Submitting a request does not reserve equipment. Availability, final pricing and delivery are confirmed by the PayLess team.</p>
    </form>
  </div>
</section>

<section class="gravel-feature">
  <div class="gravel-feature__image" role="img" aria-label="Photograph of a dump truck used for gravel and material hauling"></div><div class="gravel-feature__content"><p class="eyebrow"><span class="eyebrow-line"></span> ANOTHER WAY WE HELP</p><h2>Need gravel<br>on the ground?</h2><p>From driveway improvements to active construction sites, ask PayLess about gravel delivery options and project-specific pricing.</p><div class="gravel-feature__actions"><a class="button button--light" href="<?= e(page_url('gravel')) ?>">Request gravel pricing <span>↗</span></a><a class="gravel-phone" href="tel:<?= e(SITE_PHONE_LINK) ?>">Call <?= e(SITE_PHONE) ?></a></div></div><div class="gravel-feature__stamp">GRAVEL<br>DELIVERY<br><span>BEAVERTON / OR</span></div>
</section>

<section class="section process-section">
  <div class="container"><div class="section-heading section-heading--split"><div><p class="eyebrow"><span class="eyebrow-line"></span> HOW IT WORKS</p><h2>Three steps.<br><span>Then get moving.</span></h2></div><div class="section-heading__aside"><p>Keep the process simple. Tell us what you need and where you’re working; we’ll help with the next step.</p></div></div>
    <div class="process-grid"><article data-reveal><div class="process-grid__icon"><i class="fa-solid fa-truck-ramp-box" aria-hidden="true"></i></div><h3>Choose your equipment</h3><p>Browse the fleet and select the machines that fit your project.</p></article><article data-reveal><div class="process-grid__icon"><i class="fa-solid fa-list-check" aria-hidden="true"></i></div><h3>Send your requirements</h3><p>Tell us the rental period, job-site location and project details.</p></article><article data-reveal><div class="process-grid__icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div><h3>Confirm the next step</h3><p>Our team follows up with availability, pricing and delivery details.</p></article></div>
  </div>
</section>

<section class="section serve-section">
  <div class="container serve-layout"><div class="serve-intro"><p class="eyebrow"><span class="eyebrow-line"></span> MADE FOR THE WORK</p><h2>Whatever the site.<br><span>Let's get it done.</span></h2><p>Practical equipment options for the people who keep projects moving.</p></div><div class="serve-list"><div><span class="serve-card__icon"><i class="fa-solid fa-helmet-safety" aria-hidden="true"></i></span><span class="serve-card__copy"><strong>General contractors</strong><small>Construction &amp; site work</small></span></div><div><span class="serve-card__icon"><i class="fa-solid fa-leaf" aria-hidden="true"></i></span><span class="serve-card__copy"><strong>Landscapers</strong><small>Grading, clearing &amp; material handling</small></span></div><div><span class="serve-card__icon"><i class="fa-solid fa-house" aria-hidden="true"></i></span><span class="serve-card__copy"><strong>Residential builders</strong><small>Foundations, drainage &amp; access</small></span></div><div><span class="serve-card__icon"><i class="fa-solid fa-road" aria-hidden="true"></i></span><span class="serve-card__copy"><strong>Utility contractors</strong><small>Trenching &amp; excavation</small></span></div><div><span class="serve-card__icon"><i class="fa-solid fa-building" aria-hidden="true"></i></span><span class="serve-card__copy"><strong>Commercial teams</strong><small>Material handling &amp; larger projects</small></span></div><div><span class="serve-card__icon"><i class="fa-solid fa-house-user" aria-hidden="true"></i></span><span class="serve-card__copy"><strong>Property owners</strong><small>Driveways &amp; property projects</small></span></div></div></div>
</section>

<section class="section faq-section">
  <div class="container faq-layout"><div><p class="eyebrow"><span class="eyebrow-line"></span> GOOD TO KNOW</p><h2>Questions before<br><span>you get started?</span></h2><p>Quick answers to common equipment rental questions.</p><a class="text-link" href="<?= e(page_url('contact')) ?>">Still have a question? Contact us <span>↗</span></a></div><div class="faq-list">
    <?php $faqs = [
      ['What equipment is on the current list?', 'The current list includes excavators, skid steers, trailers, compactors, lifts, forklifts, dump trucks and other equipment. Contact us to confirm availability.'],
      ['What does equipment cost?', 'Current prices are shown on the equipment list, with previous prices shown where provided. Confirm what the listed price covers and the current availability with PayLess.'],
      ['Do you offer weekly or monthly rentals?', 'Weekly rentals are available for selected machines. Monthly rentals may be possible depending on the machine and fleet availability; contact PayLess for current pricing.'],
      ['Can equipment be delivered to my job site?', 'Delivery options and charges depend on the equipment and job-site location. Share your address and timing so the team can confirm details.'],
      ['Does PayLess deliver gravel?', 'Yes. PayLess offers gravel delivery for construction and property projects. Contact the team for current availability and project-specific pricing.'],
      ['Where is PayLess Equipment Rental located?', 'PayLess Equipment Rental LLC is located at 4480 SW 185th Ave, Beaverton, Oregon 97078 and serves customers in the surrounding Portland-area market.'],
      ['How do I request more than one machine?', 'Use the equipment request builder to select multiple machines, choose a rental period, provide job-site details and send one request. You can also call (503) 259-0252.'],
      ['What information should I include in a request?', 'Include the equipment you need, rental start date, rental duration, job-site location, project type, delivery needs and any attachments or access requirements.']
    ]; foreach ($faqs as $i => $faq): ?>
    <details class="faq-item" <?= $i === 0 ? 'open' : '' ?>><summary><span><?= e($faq[0]) ?></span><b aria-hidden="true">+</b></summary><div class="faq-item__answer"><p><?= e($faq[1]) ?></p></div></details>
    <?php endforeach; ?>
  </div></div>
  <script type="application/ld+json"><?= json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array_map(fn($faq) => ['@type' => 'Question', 'name' => $faq[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq[1]]], $faqs)], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
</section>

<section class="local-section">
  <div class="container local-layout"><div><p class="eyebrow"><span class="eyebrow-line"></span> YOUR LOCAL EQUIPMENT PARTNER</p><h2>Construction equipment rental in <span>Beaverton, Oregon.</span></h2><p>PayLess Equipment Rental LLC is based at 4480 SW 185th Ave, Beaverton, OR 97078. If your project is outside Beaverton, send us your job-site location and equipment needs so we can confirm availability and delivery options.</p><a class="button button--outline" href="<?= e(page_url('contact')) ?>">Contact PayLess <span>↗</span></a></div><div class="local-map"><iframe class="local-map__embed" src="https://maps.google.com/maps?q=4480%20SW%20185th%20Ave%2C%20Beaverton%2C%20OR%2097078&amp;z=14&amp;output=embed" title="Google Map showing PayLess Equipment Rental in Beaverton, Oregon" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe><div class="local-map__label"><span><strong>PAYLESS EQUIPMENT RENTAL</strong><small>4480 SW 185th Ave, Beaverton, OR 97078</small></span><a href="https://maps.google.com/?q=4480+SW+185th+Ave+Beaverton+OR+97078" target="_blank" rel="noopener">GET DIRECTIONS <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a></div></div></div>
</section>

<section class="final-cta"><div class="container final-cta__inner"><p class="eyebrow"><span class="eyebrow-line"></span> YOUR NEXT JOB STARTS HERE</p><h2>Ready to get<br><span>the job done?</span></h2><p>Tell us what you need, when you need it and where you’re working.</p><div><a class="button button--primary" href="#request">Build your equipment list <span>↗</span></a><a class="final-cta__phone" href="tel:<?= e(SITE_PHONE_LINK) ?>">Or call <?= e(SITE_PHONE) ?> ↗</a></div><span class="final-cta__ghost" aria-hidden="true">PL</span></div></section>
