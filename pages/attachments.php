
<?php
$attachments = [
    [
        'id' => 'hydraulic-chain-trencher',
        'name' => 'Hydraulic Chain Trencher',
        'image' => '/assets/images/hydraulic-chain-trencher.jpeg',
        'alt' => 'Hydraulic chain trencher attachment',
        'category' => 'Mini Skid Steer',
        'description' => 'Heavy-duty trenching attachment designed for mini skid steer equipment.',
        'spec' => 'Digging depth: 36–48 in · Width: 4–6 in · Hydraulic drive',
        'details' => [
            'Digging depth: 36–48 in (900–1200 mm)',
            'Digging width: 4–6 in (100–150 mm)',
            'Heavy-duty dirt / cup tooth chain',
            'Direct-drive hydraulic motor with dual hoses',
            'Soil discharge auger and trench-clearing crumber bar',
            'Universal mini skid steer quick-attach'
        ],
        'applications' => 'Trenching and groundwork'
    ],
    [
        'id' => 'skeleton-rock-bucket',
        'name' => 'Skeleton Bucket / Rock Bucket',
        'image' => '/assets/images/skeleton-rock-bucket.jpeg',
        'alt' => 'Skeleton bucket and rock bucket attachment',
        'category' => 'Loader Attachment',
        'description' => 'Heavy-duty steel bucket designed to separate rocks, roots and debris.',
        'spec' => 'Heavy-duty steel frame · Open-tine design · Compatibility to be confirmed',
        'details' => [
            'Heavy-duty steel frame',
            'Open-tine debris-separating design',
            'Loader quick-attach system; compatibility to be confirmed',
            'Designed for rock, root and debris separation'
        ],
        'applications' => 'Landscaping, site cleanup, land clearing and soil preparation'
    ],
    [
        'id' => 'hydraulic-skeleton-grapple-bucket',
        'name' => 'Hydraulic Skeleton Grapple Bucket',
        'image' => '/assets/images/hydraulic-skeleton-grapple-bucket.jpeg',
        'alt' => 'Hydraulic skeleton grapple bucket attachment',
        'category' => 'Grapple Attachment',
        'description' => 'Hydraulic grapple attachment for grabbing, lifting and separating debris.',
        'spec' => 'Approx. 42 in width · Hydraulic clamp · Mount compatibility to be confirmed',
        'details' => [
            'Brand: PROWORKS',
            'Approximate working width: 42 in; verify before rental',
            'Heavy-duty steel frame with open tines',
            'Hydraulic clamping arm, cylinder, hoses and fittings',
            'Mini skid steer mount compatibility to be confirmed'
        ],
        'applications' => 'Brush clearing, handling rocks and logs, landscaping and site cleanup'
    ],
    [
        'id' => 'hydraulic-multi-head-earth-drill',
        'name' => 'Hydraulic Multi-Head Earth Drill',
        'image' => '/assets/images/hydraulic-multi-head-earth-drill.jpeg',
        'alt' => 'Hydraulic multi-head earth drill attachment',
        'category' => 'Hydraulic Earth Drill',
        'description' => 'Multi-auger attachment designed to drill multiple holes in soil.',
        'spec' => 'Multiple spiral blades · Hydraulic drive · Replaceable digging teeth',
        'details' => [
            'Multiple spiral flighting blades',
            'Hydraulic drive with hose connections',
            'Replaceable digging teeth',
            'Loader mounting compatibility to be confirmed',
            'Designed for simultaneous soil drilling'
        ],
        'applications' => 'Fence-post holes, landscaping, planting and construction groundwork'
    ],
    [
        'id' => 'hydraulic-breaker-hammer',
        'name' => 'Hydraulic Breaker / Hammer',
        'image' => '/assets/images/hydraulic-breaker-hammer.jpeg',
        'alt' => 'Hydraulic breaker hammer attachment',
        'category' => 'Mini Excavator Attachment',
        'description' => 'Hydraulic breaker attachment for concrete breaking and rock removal.',
        'spec' => 'Hydraulic power · Steel breaker point · Pin compatibility to be confirmed',
        'details' => [
            'Compatible equipment: mini excavator; verify fit',
            'Hydraulic power system',
            'Steel chisel / breaker point',
            'Excavator bracket and pin compatibility to be confirmed',
            'Hydraulic hoses shown with the attachment'
        ],
        'applications' => 'Concrete breaking, asphalt removal, rock removal and demolition'
    ]
];

$selectedAttachment = null;
$itemId = isset($_GET['item']) ? (string) $_GET['item'] : '';

foreach ($attachments as $attachment) {
    if ($attachment['id'] === $itemId) {
        $selectedAttachment = $attachment;
        break;
    }
}
?>

<?php if ($selectedAttachment): ?>

<section class="page-hero page-hero--equipment">
    <div class="container page-hero__inner">
        <p class="eyebrow">
            <span class="eyebrow-line"></span>
            <?= e($selectedAttachment['category']) ?> / ATTACHMENTS
        </p>

        <h1>
            <?= e($selectedAttachment['name']) ?>
            <br>
            <span>Ready for the work ahead.</span>
        </h1>

        <p><?= e($selectedAttachment['description']) ?></p>

        <a class="button button--primary"
           href="<?= e(page_url('contact')) ?>">
            Request this attachment <span>↗</span>
        </a>

        <a class="page-hero__call"
           href="tel:<?= e(SITE_PHONE_LINK) ?>">
            Call <?= e(SITE_PHONE) ?>
        </a>
    </div>
</section>

<section class="section equipment-detail">
    <div class="container equipment-detail__layout">

        <div class="equipment-detail__media">
    <div class="equipment-image-slider" data-image-slider role="region" aria-label="Attachment photos">
      <div class="equipment-image-slider__track" data-slider-track>
        <?php foreach (($selectedAttachment['gallery'] ?? [$selectedAttachment['image']]) as $galleryIndex => $galleryImage): ?>
          <div class="equipment-image-slider__slide" data-slider-slide>
            <img src="<?= e($galleryImage) ?>" alt="<?= e($selectedAttachment['alt']) ?>" <?= $galleryIndex === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
          </div>
        <?php endforeach; ?>
      </div>
      <?php if (count($selectedAttachment['gallery'] ?? [$selectedAttachment['image']]) > 1): ?>
        <button class="equipment-image-slider__arrow equipment-image-slider__arrow--prev" type="button" data-slider-prev aria-label="Previous image">‹</button>
        <button class="equipment-image-slider__arrow equipment-image-slider__arrow--next" type="button" data-slider-next aria-label="Next image">›</button>
        <div class="equipment-image-slider__dots" data-slider-dots aria-label="Choose image"></div>
        <span class="sr-only" data-slider-status aria-live="polite"></span>
      <?php endif; ?>
    </div>
    <span class="equipment-detail__badge"><?= e($selectedAttachment['category']) ?></span>
  </div>

        <div class="equipment-detail__intro">
            <p class="eyebrow">
                <span class="eyebrow-line"></span>
                ATTACHMENT OVERVIEW
            </p>

            <h2>Built for the work<br><span>you need to do.</span></h2>

            <p><?= e($selectedAttachment['description']) ?></p>

            <div class="equipment-detail__rates equipment-detail__rates--prices">
                <div>
                    <small>DAILY RENTAL</small>
                    <strong>$25/day</strong>
                </div>
                <div>
                    <small>AVAILABILITY</small>
                    <strong>Contact us</strong>
                </div>
            </div>

            <p class="form-disclaimer">
                Confirm attachment compatibility, rental availability and
                specifications with PayLess before booking.
            </p>

            <a class="button button--primary button--wide"
               href="<?= e(page_url('contact')) ?>">
                Request this attachment <span>↗</span>
            </a>
        </div>

    </div>
</section>

<section class="section equipment-spec-section">
    <div class="container equipment-spec-layout">

        <div>
            <p class="eyebrow">
                <span class="eyebrow-line"></span>
                ATTACHMENT DETAILS
            </p>

            <h2>Know the<br><span>specs.</span></h2>

            <p>
                Review the available details and confirm fitment before rental.
            </p>
        </div>

        <div>
            <div class="spec-table">
                <div>
                    <span>Attachment</span>
                    <strong><?= e($selectedAttachment['name']) ?></strong>
                </div>

                <div>
                    <span>Category</span>
                    <strong><?= e($selectedAttachment['category']) ?></strong>
                </div>

                <div>
                    <span>Key specifications</span>
                    <strong><?= e($selectedAttachment['spec']) ?></strong>
                </div>

                <div>
                    <span>Applications</span>
                    <strong><?= e($selectedAttachment['applications']) ?></strong>
                </div>

                <div>
                    <span>Rental rate</span>
                    <strong>$25/day</strong>
                </div>
            </div>

            <div class="best-for">
                <p class="eyebrow">KEY FEATURES</p>

                <ul>
                    <?php foreach ($selectedAttachment['details'] as $detail): ?>
                        <li><?= e($detail) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

    </div>
</section>

<section class="final-cta final-cta--compact">
    <div class="container final-cta__inner">
        <p class="eyebrow">CHECK AVAILABILITY / GET A QUOTE</p>

        <h2>Get the right<br><span>attachment on site.</span></h2>

        <div>
            <a class="button button--primary"
               href="<?= e(page_url('contact')) ?>">
                Request a Rental <span>↗</span>
            </a>

            <a class="final-cta__phone"
               href="tel:<?= e(SITE_PHONE_LINK) ?>">
                Call <?= e(SITE_PHONE) ?> ↗
            </a>
        </div>
    </div>
</section>

<?php else: ?>

<section class="page-hero page-hero--equipment">
    <div class="container page-hero__inner">
        <p class="eyebrow">
            <span class="eyebrow-line"></span>
            EQUIPMENT ATTACHMENTS / PAYLESS
        </p>

        <h1>Equipment Attachments<br><span>for the work ahead.</span></h1>

        <p>
            Explore attachments for trenching, material handling, drilling
            and demolition. Contact us to confirm fitment and availability.
        </p>
    </div>
</section>

<section class="section catalogue-section">
    <div class="container">

        <div class="section-heading section-heading--split catalogue-category__heading">
            <div>
                <p class="eyebrow">
                    <span class="eyebrow-line"></span>
                    AVAILABLE ATTACHMENTS / <?= count($attachments) ?> OPTIONS
                </p>
                <h2>Find the right<br><span>attachment.</span></h2>
            </div>

            <div class="section-heading__aside">
                <p>
                    Rental rates are $25/day per attachment.
                    Confirm compatibility and availability before booking.
                </p>
            </div>
        </div>

        <div class="equipment-grid">
            <?php foreach ($attachments as $index => $attachment): ?>
                <article class="equipment-card" id="<?= e($attachment['id']) ?>">

                    <a class="equipment-card__image"
                       href="<?= e(page_url('attachments')) ?>&amp;item=<?= e($attachment['id']) ?>"
                       aria-label="View <?= e($attachment['name']) ?> details">

                        <img
                            src="<?= e($attachment['image']) ?>"
                            alt="<?= e($attachment['alt']) ?>"
                            loading="lazy"
                        >

                        <span class="equipment-card__number">
                            ATTACHMENT <?= sprintf('%02d', $index + 1) ?>
                        </span>

                        <span class="equipment-card__image-arrow" aria-hidden="true">↗</span>
                    </a>

                    <div class="equipment-card__body">

                       <div class="equipment-card__category">
    <span style="color: var(--accent);">
        <?= e(strtoupper($attachment['category'])) ?>
    </span>
    <span style="color: #a8adb7;"> / FOR RENT</span>
</div>
                        <h3>
                            <a href="<?= e(page_url('attachments')) ?>&amp;item=<?= e($attachment['id']) ?>">
                                <?= e($attachment['name']) ?>
                            </a>
                        </h3>

                        <p class="equipment-card__description">
                            <?= e($attachment['description']) ?>
                        </p>

                        <p class="equipment-card__spec">
                            <?= e($attachment['spec']) ?>
                        </p>

                        <div class="equipment-card__rates">
                            <div>
                                <small>DAILY RENTAL</small>
                                <strong>$25/day</strong>
                            </div>

                            <div>
                                <small>AVAILABILITY</small>
                                <strong>Contact us</strong>
                            </div>
                        </div>

                        <div class="equipment-card__actions">
                            <a href="<?= e(page_url('attachments')) ?>&amp;item=<?= e($attachment['id']) ?>">
                                View details <span>↗</span>
                            </a>

                            <a class="add-equipment"
                               href="<?= e(page_url('contact')) ?>">
                                Request rental
                            </a>
                        </div>

                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <p class="catalogue-disclaimer">
            All attachments are listed at $25/day. Availability, specifications
            and equipment compatibility must be confirmed with PayLess Equipment Rental LLC.
        </p>

    </div>
</section>

<section class="final-cta final-cta--compact">
    <div class="container final-cta__inner">
        <p class="eyebrow">READY WHEN YOU ARE</p>

        <h2>Let's line up<br><span>your attachment.</span></h2>

        <div>
            <a class="button button--primary"
               href="<?= e(page_url('contact')) ?>">
                Request a Rental <span>↗</span>
            </a>

            <a class="final-cta__phone"
               href="tel:<?= e(SITE_PHONE_LINK) ?>">
                Call <?= e(SITE_PHONE) ?> ↗
            </a>
        </div>
    </div>
</section>

<?php endif; ?>

