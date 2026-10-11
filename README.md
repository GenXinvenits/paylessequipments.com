# PayLess Equipment Rental — Website Foundation

This branch contains the PHP-first, responsive website for PayLess Equipment Rental LLC.

## Stack
- PHP 8.1+ (server-rendered pages and shared templates)
- MariaDB for persistent equipment and gravel enquiries
- PDO MySQL driver (`pdo_mysql`)
- Semantic HTML and responsive CSS
- Vanilla JavaScript for navigation, equipment-list interactions and theme controls
- GSAP + ScrollTrigger for restrained motion
- Lenis for smooth scrolling, with reduced-motion and native-scroll fallbacks
- LocalBusiness and FAQPage JSON-LD structured data

## Existing database setup

The current hosting setup uses the existing MariaDB database `db_example_com`, with database user `admin`. Do not create a second database or user just to deploy this branch.

The schema is in `database/schema.sql`. Import it into the existing database only if the `customer_requests` table has not already been created:

```sh
sudo mariadb db_example_com < database/schema.sql
```

## Database connection password

The connection settings are in `includes/database.php`. On the server, set the actual MariaDB password for the configured user in that file. The connection defaults are:

- Host: `localhost`
- Port: `3306`
- Database: `db_example_com`
- Username: `admin`

**Important:** this repository is public. Never commit the actual password to GitHub. Keep the server's real password in the server copy of `includes/database.php` and avoid pushing that modified file. A dedicated database user with only the necessary privileges is safer than using an administrator account; consider this before production.

The website saves equipment and gravel enquiries to the `customer_requests` table. A private owner/admin dashboard is available at `/admin/login.php`; it is not linked from the public customer navigation.

### Install the private admin dashboard

1. Deploy the latest `paylessequipment` branch to the site.
2. Import `database/admin-dashboard.sql` into the existing `db_example_com` database using phpMyAdmin. This creates the admin-account and login-rate-limit tables; it does not modify existing customer requests.
3. From the project directory on the server, run the owner provisioning script using PHP CLI (not a browser):
   ```sh
   /usr/local/lsws/lsphp83/bin/php tools/create-owner.php owner@your-company.example
   ```
   Enter the owner's display name and a strong password of at least 14 characters when prompted. The script refuses to create another owner if one already exists.
4. Sign in at `https://YOUR-DOMAIN/admin/login.php`. The owner can assign or disable admin accounts from the dashboard. Assigned admins can view and update requests but cannot grant access to other people.
5. Keep HTTPS enabled. Do not share owner credentials. Disable an admin account immediately when that person no longer needs access.

The dashboard uses hashed passwords, CSRF-protected forms, secure session cookies, a login-attempt limit, no-index metadata, and server-side role checks. It still needs testing on the deployed host before being considered production-verified.

## Email notifications

Database storage does not depend on email. Set `PAYLESS_CONTACT_EMAIL` to the monitored business inbox and configure working PHP mail delivery on the server. A successful form submission is saved first; the customer is shown a separate message if the email notification is not configured or fails.

## Customer data and security
- Keep real database credentials out of the public repository. If a real database password has ever been committed, rotate it in MariaDB and update the server configuration immediately.
- Use HTTPS in production and restrict access to database backups and administrative tools.
- Limit access to customer enquiries to authorised staff.
- Establish an appropriate retention/deletion schedule and include actual storage and retention practices in the privacy notice.
- Do not submit sensitive personal information that is not needed to respond to an enquiry.

## Content governance
- Pricing and equipment specs are taken from the supplied PayLess project documents.
- Availability, delivery charges and monthly rates are marked for confirmation where appropriate.
- No fabricated testimonials or project stories are published.
- Update the equipment data in `includes/site.php` when approved fleet information changes.
- Images currently use remote Unsplash URLs as design placeholders; replace them with approved PayLess fleet photography before launch.

## Deployment checks
- Confirm database connectivity and test an equipment request and a gravel request end to end.
- Set `PAYLESS_CONTACT_EMAIL` to the monitored request inbox and verify server mail delivery.
- Replace placeholder photography with licensed/approved PayLess fleet photography.
- Validate current pricing/specifications, delivery area, accessibility, structured data and all form flows.
- Configure HTTPS, secure backups and host-level security/rate limiting before launch.
- Remove the temporary `db-test.php` diagnostic endpoint after database testing is complete.

## SEO deployment note
Set `PAYLESS_SITE_URL` to the verified canonical website origin (for example, the final HTTPS domain) before publishing the generated sitemap. The domain has deliberately not been assumed in the repository.
