-- Run this file once against the MariaDB database created for the PayLess website.
-- Use a dedicated database user with only the privileges needed by the website.
CREATE TABLE IF NOT EXISTS customer_requests (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  request_type VARCHAR(20) NOT NULL,
  customer_name VARCHAR(160) NOT NULL,
  company VARCHAR(160) NOT NULL DEFAULT '',
  phone VARCHAR(60) NOT NULL,
  email VARCHAR(254) NOT NULL,
  job_location VARCHAR(500) NOT NULL,
  zip_code VARCHAR(20) NOT NULL DEFAULT '',
  rental_period VARCHAR(40) NOT NULL DEFAULT '',
  start_date DATE NULL,
  project_type VARCHAR(80) NOT NULL DEFAULT '',
  quantity VARCHAR(120) NOT NULL DEFAULT '',
  equipment_json LONGTEXT NOT NULL,
  details TEXT NOT NULL,
  contact_consent_at DATETIME NOT NULL,
  request_status VARCHAR(24) NOT NULL DEFAULT 'new',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_customer_requests_created_at (created_at),
  KEY idx_customer_requests_status_created (request_status, created_at),
  KEY idx_customer_requests_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
