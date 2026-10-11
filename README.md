# PayLess Equipment Rental — Website Foundation

This branch contains the PHP-first, responsive website for PayLess Equipment Rental LLC.

## Stack
- PHP 8.1+ (server-rendered pages and shared templates)
- MariaDB for persistent equipment and gravel enquiries
- PDO MySQL driver (pdo_mysql)
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

Open http://127.0.0.1:8080. The request form will not save enquiries until the database is configured.

## MariaDB setup

1. Install MariaDB and PHP's PDO MySQL driver on the hosting server. On Ubuntu/Debian, the packages are commonly mariadb-server and php-mysql.
2. Create the database and a dedicated website user. Open MariaDB as an administrator (sudo mariadb) and run the following, replacing the example password with a long, unique password:

```sql
CREATE DATABASE payless CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'payless_web'@'127.0.0.1' IDENTIFIED BY 'REPLACE_WITH_A_LONG_RANDOM_PASSWORD';
```

3. Create the table from the repository root:

```sh
sudo mariadb payless < database/schema.sql
```

4. Give the website account only the permission it needs to save form submissions:

```sql
GRANT INSERT ON payless.customer_requests TO 'payless_web'@'127.0.0.1';
FLUSH PRIVILEGES;
```

5. Configure these environment variables in the PHP hosting environment (PHP-FPM/Apache service or hosting control panel). Do not commit real credentials to Git:

- PAYLESS_DB_HOST=127.0.0.1
- PAYLESS_DB_PORT=3306
- PAYLESS_DB_NAME=payless
- PAYLESS_DB_USER=payless_web
- PAYLESS_DB_PASSWORD=<the password chosen above>

If MariaDB is on another server, use that server's hostname and restrict database network access to the web server. Do not expose MariaDB publicly.

The schema is in database/schema.sql. Enquiries are saved in the customer_requests table. You can review them using an authorised database client such as the hosting provider's database administration tool. No public enquiry-management page is included.

## Email notifications
Database storage does not depend on email. To also notify the business inbox, configure PAYLESS_CONTACT_EMAIL with the monitored email address and configure working PHP mail delivery on the server. A successful form submission is saved first; the customer is shown a separate message if the email notification is not configured or fails.

## Customer data and security
- Keep database credentials outside the repository and web root.
- Use HTTPS in production and restrict access to database backups and administrative tools.
- Limit access to customer enquiries to authorised staff; the website database account has insert-only permission by default.
- Establish an appropriate retention/deletion schedule and include actual storage and retention practices in the privacy notice.
- Do not submit sensitive personal information that is not needed to respond to an enquiry.

## Content governance
- Pricing and equipment specs are taken from the supplied PayLess project documents.
- Availability, delivery charges and monthly rates are marked for confirmation where appropriate.
- No fabricated testimonials or project stories are published.
- Update the equipment data in includes/site.php when approved fleet information changes.
- Images currently use remote Unsplash URLs as design placeholders; replace them with approved PayLess fleet photography before launch.

## Deployment checks
- Confirm PHP version, pdo_mysql availability, database connectivity and form submission end to end.
- Set PAYLESS_CONTACT_EMAIL to the monitored request inbox and verify server mail delivery.
- Replace placeholder photography with licensed/approved images.
- Validate current pricing/specifications, delivery area, accessibility, structured data and all form flows.
- Configure HTTPS, secure backups and host-level security/rate limiting before launch.

## SEO deployment note
Set PAYLESS_SITE_URL to the verified canonical website origin (for example, the final HTTPS domain) before publishing the generated sitemap. The domain has deliberately not been assumed in the repository.
