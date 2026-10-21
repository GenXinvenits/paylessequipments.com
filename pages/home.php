<section class="hero">
  <div class="hero__background" aria-hidden="true">
    <video class="hero__background-video" autoplay muted loop playsinline preload="metadata" poster="/assets/images/hero-excavator-poster.jpg" tabindex="-1">
      <source src="/assets/videos/hero-excavator.mp4" type="video/mp4">
    </video>
    <div class="hero__background-shade"></div>
  </div>
  <div class="hero__grid container">
    <div class="hero__copy">
      <p class="eyebrow" data-reveal><span class="eyebrow-line"></span> EQUIPMENT RENTAL / BEAVERTON, OR</p>
      <h1 data-reveal>PayLess.<br>Get More.<br><span>Get The Job Done.</span></h1>
      <p class="hero__sub" data-reveal>Construction equipment rentals without the corporate markup.</p>
      <p class="hero__body" data-reveal>Dependable equipment for construction, landscaping, excavation and site work. Choose your machines, tell us what you need and we’ll help you get moving.</p>
      <div class="hero__actions" data-reveal><a class="button button--primary" href="#request">Build your equipment list <span aria-hidden="true">↗</span></a><a class="button button--outline" href="tel:<?= e(SITE_PHONE_LINK) ?>"><span class="button__phone" aria-hidden="true">↗</span> <?= e(SITE_PHONE) ?></a></div>
      <div class="hero__meta" data-reveal><span>DAILY & WEEKLY RENTALS</span><span>PRACTICAL PRICING</span><span>LOCAL SERVICE</span></div>
    </div>
    <div class="hero__visual-caption"><span class="label-dot"></span> READY WHEN THE JOB CALLS</div>
  </div>
  <div class="hero__bottom container"><span>BUILT FOR THE PEOPLE WHO BUILD THINGS.</span><span>SCROLL TO EXPLORE <b>↓</b></span></div>
</section>

<section class="value-bar" aria-label="PayLess service benefits">
  <div class="container value-bar__inner"><div><span class="value-icon"><i class="fa-solid fa-tags" aria-hidden="true"></i></span><span><strong>COST-EFFECTIVE RATES</strong><small>Practical pricing for real projects</small></span></div><div><span class="value-icon"><i class="fa-solid fa-helmet-safety" aria-hidden="true"></i></span><span><strong>EQUIPMENT THAT WORKS</strong><small>Capable machines for demanding jobs</small></span></div><div><span class="value-icon"><i class="fa-regular fa-calendar-check" aria-hidden="true"></i></span><span><strong>FLEXIBLE RENTALS</strong><small>Daily, weekly & selected monthly</small></span></div><div><span class="value-icon"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span><span><strong>LOCAL SERVICE</strong><small>Based in Beaverton, Oregon</small></span></div></div>
</section>

<section class="section equipment-fleet" id="equipment">
  <div class="container">
    <div class="section-heading section-heading--split"><div><p class="eyebrow"><span class="eyebrow-line"></span> FIND YOUR MACHINE · THE FLEET</p><h2>Right tool.<br><span>Right on time.</span></h2></div><div class="section-heading__aside"><p>Browse the fleet by equipment type. Product photos are representative; exact rental units and configurations may vary. Contact us to confirm availability and current rates.</p><a class="text-link" href="<?= e(page_url('equipment')) ?>">View full catalogue <span>↗</span></a></div></div>
    <div class="equipment-tabs" role="tablist" aria-label="Filter equipment by category">
      <button class="equipment-tab is-active" type="button" role="tab" id="equipment-tab-all" aria-selected="true" aria-controls="equipment-list" tabindex="0" data-machine-filter="all" data-machine-description="Showing all <?= count($equipment) ?> equipment options. Select a category to narrow the fleet.">All equipment <span><?= count($equipment) ?></span></button>
      <?php foreach ($categories as $category): $count = count(array_filter($equipment, static fn($item) => $item['category'] === $category['name'])); if (!$count) continue; ?>
      <button class="equipment-tab" type="button" role="tab" id="equipment-tab-<?= e($category['anchor']) ?>" aria-selected="false" aria-controls="equipment-list" tabindex="-1" data-machine-filter="<?= e($category['anchor']) ?>" data-machine-description="<?= e($category['detail']) ?>"><i class="<?= e($category['icon']) ?>" aria-hidden="true"></i> <?= e($category['name']) ?> <span><?= $count ?></span></button>
      <?php endforeach; ?>
    </div>
    <p class="equipment-tab-detail" aria-live="polite">Showing all <?= count($equipment) ?> equipment options. Select a category to narrow the fleet.</p>
    <div class="equipment-grid" id="equipment-list" role="tabpanel" aria-labelledby="equipment-tab-all" tabindex="0">
      <?php foreach ($equipment as $i => $item): ?>
      <?php $categoryAnchor = ''; foreach ($categories as $category) { if ($item['category'] === $category['name']) { $categoryAnchor = $category['anchor']; break; } } ?>
      <article class="equipment-card" data-machine-category="<?= e($categoryAnchor) ?>" data-reveal>
        <a class="equipment-card__image" href="<?= e(page_url('equipment')) ?>&amp;item=<?= e($item['id']) ?>" aria-label="View <?= e($item['name']) ?>"><img src="<?= e($item['image']) ?>" alt="<?= e($item['alt']) ?>" loading="lazy"><span class="equipment-card__image-arrow">↗</span></a>
        <div class="equipment-card__body"><p class="equipment-card__category"><?= e($item['category']) ?> <span>•</span> <?= e($item['tag']) ?></p><h3><?= e($item['name']) ?></h3><p class="equipment-card__description"><?= e($item['description']) ?></p><p class="equipment-card__spec"><?= e($item['spec']) ?></p><div class="equipment-card__rates"><div><small>DAILY FROM</small><strong><?= e($item['daily']) ?></strong></div><div><small>WEEKLY FROM</small><strong><?= e($item['weekly']) ?></strong></div></div><div class="equipment-card__actions"><a href="<?= e(page_url('equipment')) ?>#<?= e($item['id']) ?>">View details <span>↗</span></a><button type="button" class="add-equipment" data-add-equipment="<?= e($item['name']) ?>">+ Add to list</button></div></div>
      </article>
      <?php endforeach; ?>
    </div>
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
      <div class="form-row"><label>Your name <em>*</em><input name="name" required autocomplete="name" placeholder="Full name"></label><label>Company<input name="company" autocomplete="organization" placeholder="Company name"></label></div>
      <div class="form-row"><label>Phone <em>*</em><input type="tel" name="phone" required autocomplete="tel" placeholder="(503) 555-0123"></label><label>Email <em>*</em><input type="email" name="email" required autocomplete="email" placeholder="you@company.com"></label></div>
      <label class="consent"><input type="checkbox" name="contact_consent" value="yes" required><span>I agree that PayLess may contact me about this request.</span></label>
      <button class="button button--primary button--wide" type="submit">Send equipment request <span aria-hidden="true">↗</span></button>
      <p class="form-disclaimer">Submitting a request does not reserve equipment. Availability, final pricing and delivery are confirmed by the PayLess team.</p>
    </form>
  </div>
</section>

<section class="section why-section">
  <div class="container">
    <div class="section-heading"><p class="eyebrow"><span class="eyebrow-line"></span> THE PAYLESS DIFFERENCE</p><h2>Less runaround.<br><span>More doing.</span></h2><p class="why-section__intro">Getting the right equipment should be a clear part of the plan. Browse machines for your project, send the details once, and talk directly with our Beaverton team about current availability, rates and delivery options.</p></div>
    <div class="why-grid">
      <article data-reveal><span class="why-grid__number">01</span><h3>Know what you’re asking for</h3><p>Explore equipment by type and compare options for excavation, site preparation, hauling and material handling. Not sure which machine fits? Include the work you need to do and we can help point you in the right direction.</p></article>
      <article data-reveal><span class="why-grid__number">02</span><h3>Clear rental details</h3><p>Daily, weekly and selected monthly rental periods are available. Tell us your expected dates and the machine you have in mind so we can confirm current pricing and availability for that time frame.</p></article>
      <article data-reveal><span class="why-grid__number">03</span><h3>One useful request</h3><p>Need more than one machine? Add them to a single equipment list, then share your project type, start date and job-site ZIP code. That gives our team the context to follow up with relevant next steps.</p></article>
      <article data-reveal><span class="why-grid__number">04</span><h3>Local follow-through</h3><p>PayLess is based in Beaverton and serves customers across the Portland metro area. Share where the work is happening and ask us to confirm whether delivery is available for your equipment and location.</p></article>
    </div>
    <div class="why-image-row" aria-label="Examples of equipment and project work"><figure><img src="/assets/images/equipment-excavator.svg" alt="Excavator for digging and trenching work" loading="lazy"><figcaption>Digging &amp; trenching</figcaption></figure><figure><img src="/assets/images/equipment-track-loader.svg" alt="Compact track loader for site preparation" loading="lazy"><figcaption>Site preparation</figcaption></figure><figure><img src="/assets/images/equipment-dump-truck.svg" alt="Dump truck for material hauling" loading="lazy"><figcaption>Material hauling</figcaption></figure></div>
    <div class="why-section__next"><p><strong>Have a project in mind?</strong> Browse the fleet or send your list to get a direct availability and pricing follow-up.</p><div><a class="text-link" href="<?= e(page_url('equipment')) ?>">Explore equipment <span>↗</span></a><a class="text-link" href="#request">Build your equipment list <span>↗</span></a></div></div>
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
  <div class="container serve-layout"><div><p class="eyebrow"><span class="eyebrow-line"></span> MADE FOR THE WORK</p><h2>Whatever the site.<br><span>Let's get it done.</span></h2><p>Practical equipment options for the people who keep projects moving.</p></div><div class="serve-list"><div><strong>General contractors</strong><small>Construction & site work</small></div><div><strong>Landscapers</strong><small>Grading, clearing & material handling</small></div><div><strong>Residential builders</strong><small>Foundations, drainage & access</small></div><div><strong>Utility contractors</strong><small>Trenching & excavation</small></div><div><strong>Commercial teams</strong><small>Material handling & larger projects</small></div><div><strong>Property owners</strong><small>Driveways & property projects</small></div></div></div>
</section>

<section class="local-section">
  <div class="container local-layout"><div><p class="eyebrow"><span class="eyebrow-line"></span> YOUR LOCAL EQUIPMENT PARTNER</p><h2>Construction equipment rental in <span>Beaverton, Oregon.</span></h2><p>PayLess Equipment Rental LLC is based at 4480 SW 185th Ave, Beaverton, OR 97078. If your project is outside Beaverton, send us your job-site location and equipment needs so we can confirm availability and delivery options.</p><a class="button button--outline" href="<?= e(page_url('contact')) ?>">Contact PayLess <span>↗</span></a></div><div class="local-map"><iframe class="local-map__embed" src="https://maps.google.com/maps?q=4480%20SW%20185th%20Ave%2C%20Beaverton%2C%20OR%2097078&amp;z=14&amp;output=embed" title="Google Map showing PayLess Equipment Rental in Beaverton, Oregon" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe><div class="local-map__label"><span><strong>PAYLESS EQUIPMENT RENTAL</strong><small>4480 SW 185th Ave, Beaverton, OR 97078</small></span><a href="https://maps.google.com/?q=4480+SW+185th+Ave+Beaverton+OR+97078" target="_blank" rel="noopener">GET DIRECTIONS <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a></div></div></div>
</section>

<section class="section proof-section">
  <div class="container proof-layout"><div><p class="eyebrow"><span class="eyebrow-line"></span> EQUIPMENT THAT GETS IT DONE</p><h2>Built for the<br><span>work ahead.</span></h2></div><div><p class="proof-copy">Good equipment is only part of the job. Clear information and a practical rental process matter too. Explore the current fleet, check the specifications and send us what you need.</p><a class="text-link" href="<?= e(page_url('equipment')) ?>">Explore the equipment catalogue <span>↗</span></a><p class="proof-note">Customer testimonials and project stories will be added when verified reviews and approved project materials are supplied.</p></div></div>
</section>

<section class="section faq-section">
  <div class="container faq-layout"><div><p class="eyebrow"><span class="eyebrow-line"></span> GOOD TO KNOW</p><h2>Questions before<br><span>you get started?</span></h2><p>Quick answers to common equipment rental questions.</p><a class="text-link" href="<?= e(page_url('contact')) ?>">Still have a question? Contact us <span>↗</span></a></div><div class="faq-list">
    <?php $faqs = [
      ['What equipment can I rent from PayLess?', 'The current featured fleet includes excavators, mini excavators, mini skid steers, track loaders, dump trucks and forklifts. Contact us to confirm current availability.'],
      ['How much does construction equipment rental cost?', 'Rates depend on the machine and rental period. Supplied daily rates start at $200 for selected equipment, with weekly rates also available. Some equipment requires a current quote.'],
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

<section class="final-cta"><div class="container final-cta__inner"><p class="eyebrow"><span class="eyebrow-line"></span> YOUR NEXT JOB STARTS HERE</p><h2>Ready to get<br><span>the job done?</span></h2><p>Tell us what you need, when you need it and where you’re working.</p><div><a class="button button--primary" href="#request">Build your equipment list <span>↗</span></a><a class="final-cta__phone" href="tel:<?= e(SITE_PHONE_LINK) ?>">Or call <?= e(SITE_PHONE) ?> ↗</a></div><span class="final-cta__ghost" aria-hidden="true">PL</span></div></section>
