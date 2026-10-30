</main>
<div class="gravel-fab-wrapper" aria-label="Gravel delivery actions">
  <a class="gravel-fab" href="<?= e(page_url('contact')) ?>#gravel-request">
    <span class="gravel-fab__icon" aria-hidden="true"><i class="fa-solid fa-truck" aria-hidden="true"></i></span>
    <span class="gravel-fab__copy"><strong>Need gravel delivered?</strong><small>Get project pricing</small></span>
    <span class="gravel-fab__arrow" aria-hidden="true">↗</span>
  </a>
  <a class="gravel-fab gravel-fab--call" href="tel:<?= e(SITE_PHONE_LINK) ?>" aria-label="Call PayLess now" title="Call PayLess now"><span class="gravel-fab__icon"><i class="fa-solid fa-phone" aria-hidden="true"></i></span><span class="gravel-fab__copy"><i class="fa-solid fa-phone gravel-fab__copy-phone" aria-hidden="true"></i><strong>Call now</strong><small><?= e(SITE_PHONE) ?></small></span></a>
</div>
<footer class="site-footer">
  <div class="container footer-top">
    <div class="footer-brand">
      <a class="brand brand--footer" href="/" aria-label="PayLess Equipment Rental home"><span class="brand__footer-logo-frame"><img class="brand__footer-logo" src="/assets/images/payless-equipment-rental-logo-dark-animated.svg?v=embedded-1" alt=""></span></a>
      <p>PayLess. Get More. Get The Job Done.</p>
      <p class="footer-note">Professional equipment. Practical pricing. Straightforward communication.</p>
    </div>
    <div class="footer-column"><p class="footer-label">EXPLORE</p><a href="/">Home</a><a href="<?= e(page_url('equipment')) ?>">Equipment</a><a href="<?= e(page_url('about')) ?>">About Us</a><a href="<?= e(page_url('gravel')) ?>">Gravel Delivery</a><a href="<?= e(page_url('contact')) ?>">Contact</a></div>
    <div class="footer-column"><p class="footer-label">EQUIPMENT</p><?php foreach ($categories as $category): ?><a href="<?= e(page_url('equipment')) ?>"><?= e($category['name']) ?></a><?php endforeach; ?></div>
    <div class="footer-column footer-contact"><p class="footer-label">LET'S GET TO WORK</p><a class="footer-phone" href="tel:<?= e(SITE_PHONE_LINK) ?>"><?= e(SITE_PHONE) ?> ↗</a><a href="mailto:Rentals@paylessequipments.com">Rentals@paylessequipments.com</a><p><?= e(SITE_ADDRESS) ?></p><a href="https://maps.google.com/?q=4480+SW+185th+Ave+Beaverton+OR+97078" target="_blank" rel="noopener">Get directions ↗</a></div>
  </div>


<div class="container footer-bottom"><span>© <?= date('Y') ?> PayLess Equipment Rental LLC</span><span>Built around the way you work.</span><span class="footer-legal"><a href="<?= e(page_url('privacy')) ?>">Privacy</a><a href="<?= e(page_url('terms')) ?>">Terms &amp; Conditions</a><a href="<?= e(page_url('disclaimer')) ?>">Website disclaimer</a></span><a href="#top">Back to top ↑</a></div>





<div class="container footer-bottom-credit" style="display:flex; justify-content:center; align-items:center; flex-wrap:wrap; gap:8px; padding:14px 0 20px; margin-top:0; border-top:none; background:#14171D; color:#aeb6a9 !important; font-family:'DM Mono',monospace; font-size:10px; line-height:1.8; text-align:center; width:100%; box-sizing:border-box;">

  <span style="color:#aeb6a9 !important;">
    © <?= date('Y') ?> PayLess Equipment Rental LLC. All rights reserved.
  </span>

  <span style="color:#aeb6a9 !important;">| Designed and Developed by</span>

  <a href="https://www.crackajackllc.com/" target="_blank" rel="noopener noreferrer"
     style="color:#CA9C35 !important; text-decoration:none; transition:color .2s ease;"
     onmouseover="this.style.setProperty('color','#ffffff','important')"
     onmouseout="this.style.setProperty('color','#CA9C35','important')">
    CrackaJack Digital Solutions LLC.
  </a>

</div>





</footer>
<script src="https://cdn.jsdelivr.net/npm/lenis@1.3.8/dist/lenis.min.js" defer></script>
<script src="/assets/js/site.js?v=gravel-form-routing-1" defer></script>
</body>
</html>
