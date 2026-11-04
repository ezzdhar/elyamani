<?php
/**
 * ELYAMANI Architecture & Execution - Contact Form Configuration Template
 * 
 * Hostinger Deployment Instructions:
 * 1. For best security, place your active configuration file OUTSIDE the public web root.
 *    For example, on Hostinger shared hosting:
 *    /home/u123456789/contact-config.php  (when your site is in /home/u123456789/public_html)
 * 2. Alternatively, set these keys as server environment variables in your hosting panel.
 * 3. Never commit passwords or live credentials to your Git repository.
 */

// Block direct browser access
if (!defined('CONTACT_FORM_LOADED')) {
    http_response_code(403);
    exit('Direct access not permitted');
}

return [
    // Hostinger SMTP Server Configuration
    // Standard Hostinger SMTP host is: smtp.hostinger.com
    'SMTP_HOST' => 'smtp.hostinger.com',
    'SMTP_PORT' => 465,                  // 465 for SSL, or 587 for TLS
    'SMTP_SECURE' => 'ssl',              // 'ssl' or 'tls'

    // Your authenticated Hostinger email account
    // Example: noreply@el-yamani.com or info@el-yamani.com
    'SMTP_USER' => 'noreply@el-yamani.com',
    'SMTP_PASS' => 'YOUR_HOSTINGER_EMAIL_PASSWORD',

    // Verified domain sender email address (must be hosted on your Hostinger account)
    'SMTP_FROM_EMAIL' => 'noreply@el-yamani.com',
    'SMTP_FROM_NAME' => 'ELYMANI Website',

    // Target recipient email (inquiries delivered here)
    'SMTP_TO_EMAIL' => 'elyamani.cfd@gmail.com',
    'SMTP_TO_NAME' => 'ELYAMANI',

    // Authorized frontend origins that are allowed to make cross-origin requests
    // Wildcards ('*') are strictly disallowed
    'ALLOWED_ORIGINS' => [
        'https://el-yamani.com',
        'https://www.el-yamani.com',
        'https://ezzdhar.github.io'
    ],

    // Rate Limiting (Abuse protection on shared hosting)
    'RATE_LIMIT_ENABLED' => true,
    'RATE_LIMIT_MAX' => 5,               // Max allowed submissions
    'RATE_LIMIT_WINDOW' => 600,          // Time window in seconds (600s = 10 minutes)
];
