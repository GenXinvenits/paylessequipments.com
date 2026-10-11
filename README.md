# PayLess Equipment Rental — Website Foundation

This branch is being rebuilt as a PHP-first, responsive website for PayLess Equipment Rental LLC.

## Stack
- PHP 8.1+ (server-rendered pages and shared templates)
- Semantic HTML and responsive CSS
- Vanilla JavaScript for navigation, equipment-list interactions and theme controls
- GSAP + ScrollTrigger for restrained motion
- Lenis for smooth scrolling, with reduced-motion and native-scroll fallbacks
- LocalBusiness and FAQPage JSON-LD structured data

## Run locally
From the repository root, run:

```sh
php -S 127.0.0.1:8080
```

Open http://127.0.0.1:8080.

## Contact form setup
The request handler uses PHP's `mail()` and requires the host's mail transport to be configured. Set the environment variable `PAYLESS_CONTACT_EMAIL` to the approved business inbox before accepting live requests. Until configured, the form intentionally displays a clear fallback asking the customer to call (503) 259-0252; it does not pretend a request was delivered.

## Content governance
- Pricing and equipment specs are taken from the supplied PayLess project documents.
- Availability, delivery charges and monthly rates are marked for confirmation where appropriate.
- No fabricated testimonials or project stories are published.
- Update the equipment data in `includes/site.php` when approved fleet information changes.
- Images currently use remote Unsplash URLs as design placeholders; replace them with approved PayLess fleet photography before launch.

## Deployment checks
- Confirm PHP version and mail delivery on the target host.
- Set `PAYLESS_CONTACT_EMAIL` to the monitored request inbox.
- Replace placeholder photography with licensed/approved images.
- Validate current pricing/specifications, delivery area, accessibility, structured data and all form flows.
- Configure HTTPS and host-level security/rate limiting before launch.
