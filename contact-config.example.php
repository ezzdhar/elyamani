<?php
/**
 * ELYAMANI Architecture & Execution - Contact Form Configuration Template
 * 
 * Deployment Instructions:
 * 1. Rename this file to `contact-config.php` and place it either:
 *    - In your Hostinger user root directory: `/home/uXXXX/contact-config.php` (Recommended for security)
 *    - OR in your public web root: `public_html/contact-config.php` or `public_html/api/contact-config.php`
 * 2. Configure either OPTION 1 (Gmail SMTP - Fastest & 100% Inbox Delivery) or OPTION 2 (Hostinger SMTP).
 * 3. Never commit passwords or live credentials to your Git repository.
 */

// Block direct browser access
if (!defined('CONTACT_FORM_LOADED')) {
    http_response_code(403);
    exit('Direct access not permitted');
}

return [
    // =========================================================================
    // OPTION 1: GMAIL SMTP WITH APP PASSWORD (RECOMMENDED FOR 100% INBOX DELIVERY)
    // =========================================================================
    // Why: Google authenticates its own servers; emails will NEVER be blocked or marked as spam!
    // How to get the 16-character App Password:
    // 1. Open Google Account (myaccount.google.com) for elyamani.cfd@gmail.com
    // 2. Go to Security -> Enable '2-Step Verification'
    // 3. Search 'App Passwords' -> Create one with name 'Elyamani Website'
    // 4. Paste the 16-character password below (without spaces)
    /*
    'SMTP_HOST'       => 'smtp.gmail.com',
    'SMTP_PORT'       => 587,
    'SMTP_SECURE'     => 'tls',
    'SMTP_USER'       => 'elyamani.cfd@gmail.com',
    'SMTP_PASS'       => 'YOUR_16_CHAR_GMAIL_APP_PASSWORD',
    'SMTP_FROM_EMAIL' => 'elyamani.cfd@gmail.com',
    'SMTP_FROM_NAME'  => 'ELYMANI Website',
    'SMTP_TO_EMAIL'   => 'elyamani.cfd@gmail.com',
    'SMTP_TO_NAME'    => 'ELYAMANI',
    */

    // =========================================================================
    // OPTION 2: HOSTINGER SMTP (USING YOUR DOMAIN EMAIL)
    // =========================================================================
    // In Hostinger hPanel -> Emails -> Create email account (e.g. noreply@el-yamani.com or info@el-yamani.com)
    // Hostinger automatically configures MX and SPF records in DNS.
    'SMTP_HOST'       => 'smtp.hostinger.com',
    'SMTP_PORT'       => 465,                  // 465 for SSL, or 587 for TLS
    'SMTP_SECURE'     => 'ssl',                // 'ssl' or 'tls'
    'SMTP_USER'       => 'noreply@el-yamani.com',
    'SMTP_PASS'       => 'YOUR_HOSTINGER_EMAIL_PASSWORD',
    'SMTP_FROM_EMAIL' => 'noreply@el-yamani.com',
    'SMTP_FROM_NAME'  => 'ELYMANI Website',
    'SMTP_TO_EMAIL'   => 'elyamani.cfd@gmail.com',
    'SMTP_TO_NAME'    => 'ELYAMANI',

    // =========================================================================
    // CORS & SECURITY
    // =========================================================================
    'ALLOWED_ORIGINS' => [
        'https://el-yamani.com',
        'https://www.el-yamani.com',
        'https://ezzdhar.github.io',
        'http://localhost:3000'
    ],

    // Rate Limiting (Abuse protection on shared hosting)
    'RATE_LIMIT_ENABLED' => true,
    'RATE_LIMIT_MAX'     => 5,               // Max allowed submissions per window
    'RATE_LIMIT_WINDOW'  => 600,             // Time window in seconds (600s = 10 minutes)
];
