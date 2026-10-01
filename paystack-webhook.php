<?php
/**
 * Paystack webhook. Set this URL in the Paystack dashboard:
 *   https://hltsltd.com/paystack-webhook.php
 *
 * Paystack signs each request with your secret key. Requests without a valid
 * signature are rejected.
 */

require __DIR__ . '/lib/app.php';

if (!is_post() || !paystack_enabled()) {
    http_response_code(404);
    exit;
}

$body = (string) file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '';
$expected = hash_hmac('sha512', $body, (string) config('paystack.secret_key'));

if (!is_string($signature) || !hash_equals($expected, $signature)) {
    log_event('PAYSTACK_BAD_SIGNATURE');
    http_response_code(401);
    exit;
}

$event = json_decode($body, true);
$data = $event['data'] ?? [];

if (($event['event'] ?? '') === 'charge.success' && !empty($data['reference'])) {
    $payment = db_one('SELECT * FROM payments WHERE reference = ?', [(string) $data['reference']]);
    if ($payment) {
        paystack_record($payment, $data);
    } else {
        log_event('PAYSTACK_UNKNOWN_REFERENCE', (string) $data['reference']);
    }
}

http_response_code(200);
echo 'ok';
