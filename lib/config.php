<?php
/**
 * Site configuration.
 *
 * Defaults live here. Real values (database password, Paystack keys, SMTP
 * login) go in config/config.local.php, which is not committed. Copy
 * config/config.example.php to start one.
 */

function config(string $key, $default = null)
{
    static $config = null;

    if ($config === null) {
        $config = [
            'site_name' => 'HLTS Limited',
            'site_url' => 'https://hltsltd.com',
            'timezone' => 'Africa/Lagos',
            'debug' => false,

            // Random secret used to sign result PINs and other tokens.
            // Set a long random value in config.local.php.
            'app_key' => 'change-me-in-config-local',

            // Contact details shown across the site.
            'phone' => '+2348107005789',
            'phone_display' => '+234 810 700 5789',
            'whatsapp' => '2348107005789',
            'email_public' => 'CEO@hltsltd.com',
            'address' => '8 Assembly Close, Folagoro, Somolu, Lagos, Nigeria',
            'office_hours' => 'Monday to Friday, 8am to 5pm',

            // Where new enquiries are emailed. Per-form overrides use the form key.
            'notify_to' => 'CEO@hltsltd.com',
            'notify_to_by_form' => [],

            // Database. Driver "sqlite" needs no setup and is used for local preview.
            // On the live server use "mysql" with the cPanel database details.
            'db' => [
                'driver' => 'sqlite',
                'sqlite_path' => APP_ROOT . '/storage/hlts.sqlite',
                'host' => 'localhost',
                'port' => 3306,
                'name' => '',
                'user' => '',
                'pass' => '',
            ],

            // Email. "mail" uses PHP mail(); "smtp" sends through a mailbox (recommended).
            'mail' => [
                'driver' => 'mail',
                'from' => 'no-reply@hltsltd.com',
                'from_name' => 'HLTS Limited',
                'smtp_host' => '',
                'smtp_port' => 465,
                'smtp_secure' => 'ssl', // "ssl" (port 465) or "tls" (port 587)
                'smtp_user' => '',
                'smtp_pass' => '',
                'send_confirmations' => true,
            ],

            // Paystack. Payments stay switched off until a secret key is set.
            'paystack' => [
                'public_key' => '',
                'secret_key' => '',
            ],

            // Grade scale for the results checker (minimum score => grade, remark).
            'grade_scale' => [
                [70, 'A', 'Excellent'],
                [60, 'B', 'Very good'],
                [50, 'C', 'Good'],
                [45, 'D', 'Fair'],
                [40, 'E', 'Pass'],
                [0, 'F', 'Fail'],
            ],
        ];

        $local = APP_ROOT . '/config/config.local.php';
        if (is_file($local)) {
            $overrides = require $local;
            if (is_array($overrides)) {
                $config = array_replace_recursive($config, $overrides);
            }
        }
    }

    $value = $config;
    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }

    return $value;
}
