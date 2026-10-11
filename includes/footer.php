</main>
<footer class="site-footer">
  <div class="container footer-top">
    <div class="footer-brand">
      <a class="brand brand--footer" href="/"><span class="brand__mark" aria-hidden="true">P<span>L</span></span><span class="brand__wordmark">PAYLESS<span>EQUIPMENT RENTAL</span></span></a>
      <p>PayLess. Get More. Get The Job Done.</p>
      <p class="footer-note">Professional equipment. Practical pricing. Straightforward communication.</p>
    </div>
    <div class="footer-column"><p class="footer-label">EXPLORE</p><a href="/">Home</a><a href="<?= e(page_url('equipment')) ?>">Equipment</a><a href="<?= e(page_url('about')) ?>">About Us</a><a href="<?= e(page_url('gravel')) ?>">Gravel Delivery</a><a href="<?= e(page_url('contact')) ?>">Contact</a></div>
    <div class="footer-column"><p class="footer-label">EQUIPMENT</p><?php foreach ($categories as $category): ?><a href="<?= e(page_url('equipment')) ?>"><?= e($category['name']) ?></a><?php endforeach; ?></div>
    <div class="footer-column footer-contact"><p class="footer-label">LET'S GET TO WORK</p><a class="footer-phone" href="tel:<?= e(SITE_PHONE_LINK) ?>"><?= e(SITE_PHONE) ?> ↗</a><p><?= e(SITE_ADDRESS) ?></p><a href="https://maps.google.com/?q=4480+SW+185th+Ave+Beaverton+OR+97078" target="_blank" rel="noopener">Get directions ↗</a></div>
  </div>
  <div class="container footer-bottom"><span>© <?= date('Y') ?> PayLess Equipment Rental LLC</span><span>Built around the way you work.</span><span class="footer-legal"><a href="<?= e(page_url('privacy')) ?>">Privacy</a><a href="<?= e(page_url('terms')) ?>">Website terms</a><a href="<?= e(page_url('rental-terms')) ?>">Rental terms</a></span><a href="#top">Back to top ↑</a></div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/gsap.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollTrigger.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/lenis@1.3.8/dist/lenis.min.js" defer></script>
<script src="/assets/js/site.js" defer></script>
</body>
</html>
