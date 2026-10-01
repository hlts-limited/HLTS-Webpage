<?php
/**
 * Copy this file to config/config.local.php and fill in real values.
 * config.local.php is ignored by git, so secrets never reach the repository.
 */

return [
    'debug' => false,

    // Generate with: php -r "echo bin2hex(random_bytes(32));"
    'app_key' => '',

    // One-time secret for creating the first admin at /admin/setup.php?token=...
    // Generate the same way as app_key. You can remove it after setup.
    'setup_token' => '',

    'notify_to' => 'CEO@hltsltd.com',

    // cPanel > MySQL Databases: create a database and user, then fill these in.
    'db' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'name' => '',
        'user' => '',
        'pass' => '',
    ],

    // cPanel > Email Accounts: create no-reply@hltsltd.com and use its login here.
    'mail' => [
        'driver' => 'smtp',
        'from' => 'no-reply@hltsltd.com',
        'smtp_host' => 'mail.hltsltd.com',
        'smtp_port' => 465,
        'smtp_secure' => 'ssl',
        'smtp_user' => 'no-reply@hltsltd.com',
        'smtp_pass' => '',
    ],

    // Shared secret for sending form submissions to the HLTS staff app. Generate it once with
    //   php -r "echo bin2hex(random_bytes(32));"
    // and put the same value in Vercel as WEBSITE_WEBHOOK_SECRET.
    'app_sync' => [
        'secret' => '',
    ],

    // Paystack dashboard > Settings > API Keys & Webhooks.
    // Set the webhook URL to https://hltsltd.com/paystack-webhook.php
    'paystack' => [
        'public_key' => '',
        'secret_key' => '',
    ],
];
