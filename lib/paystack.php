<?php
/**
 * Paystack payments for course fees.
 *
 * Flow:
 *   1. paystack_start() creates a pending payment row and asks Paystack for a
 *      checkout link. The amount always comes from courses() on the server.
 *   2. The learner pays on Paystack and returns to payment-callback.php,
 *      which verifies the payment with Paystack before showing success.
 *   3. paystack-webhook.php receives Paystack's own confirmation, checks its
 *      signature, and marks the payment paid even if the learner closed the tab.
 *
 * Nothing happens until config "paystack.secret_key" is set.
 */

function paystack_enabled(): bool
{
    return trim((string) config('paystack.secret_key')) !== '';
}

function paystack_request(string $method, string $path, ?array $body = null): array
{
    $ch = curl_init('https://api.paystack.co' . $path);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . config('paystack.secret_key'),
            'Content-Type: application/json',
        ],
    ]);
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        throw new RuntimeException('Could not reach Paystack: ' . $error);
    }
    $data = json_decode((string) $response, true);
    if (!is_array($data)) {
        throw new RuntimeException('Unexpected response from Paystack.');
    }
    return $data;
}

/**
 * The amount charged in one payment for a course and plan, in kobo, or null
 * if it can't be paid online. Session = whole fee; semester = half of the
 * semester-plan total; monthly = one eighth of the monthly-plan total.
 */
function course_fee(string $courseSlug, string $plan): ?int
{
    $total = course($courseSlug)['fees'][$plan] ?? null;
    return is_int($total) && $total > 0 ? instalment_amount($total, $plan) : null;
}

/** Create a payment and return the Paystack checkout URL. */
function paystack_start(array $payer, string $courseSlug, string $plan, ?int $leadId = null): string
{
    $amount = course_fee($courseSlug, $plan);
    if ($amount === null) {
        throw new RuntimeException('This course plan cannot be paid online.');
    }

    $reference = 'HLTS-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(5)));
    db_insert('payments', [
        'reference' => $reference,
        'email' => $payer['email'],
        'name' => $payer['name'] ?? '',
        'phone' => $payer['phone'] ?? '',
        'course_slug' => $courseSlug,
        'plan' => $plan,
        'amount_kobo' => $amount,
        'status' => 'pending',
        'lead_id' => $leadId,
        'created_at' => now(),
    ]);

    $result = paystack_request('POST', '/transaction/initialize', [
        'email' => $payer['email'],
        'amount' => $amount,
        'currency' => 'NGN',
        'reference' => $reference,
        'callback_url' => absolute_url('payment-callback.php'),
        'metadata' => [
            'course' => $courseSlug,
            'plan' => $plan,
            'custom_fields' => [
                ['display_name' => 'Course', 'variable_name' => 'course', 'value' => course($courseSlug)['title'] ?? $courseSlug],
                ['display_name' => 'Learner', 'variable_name' => 'learner', 'value' => $payer['name'] ?? ''],
            ],
        ],
    ]);

    if (empty($result['status']) || empty($result['data']['authorization_url'])) {
        db_run('UPDATE payments SET status = ? WHERE reference = ?', ['failed', $reference]);
        throw new RuntimeException($result['message'] ?? 'Paystack did not accept the payment.');
    }

    return $result['data']['authorization_url'];
}

/**
 * Ask Paystack whether a payment succeeded and record the answer.
 * Returns the payment row, or null if the reference is unknown.
 */
function paystack_verify(string $reference): ?array
{
    $payment = db_one('SELECT * FROM payments WHERE reference = ?', [$reference]);
    if (!$payment) {
        return null;
    }
    if ($payment['status'] === 'paid') {
        return $payment;
    }

    $result = paystack_request('GET', '/transaction/verify/' . rawurlencode($reference));
    paystack_record($payment, $result['data'] ?? []);

    return db_one('SELECT * FROM payments WHERE reference = ?', [$reference]);
}

/** Save Paystack's transaction data against our payment, checking the amount matches. */
function paystack_record(array $payment, array $tx): void
{
    $status = $tx['status'] ?? 'unknown';
    $amountOk = (int) ($tx['amount'] ?? 0) === (int) $payment['amount_kobo'] && ($tx['currency'] ?? 'NGN') === 'NGN';

    if ($status === 'success' && $amountOk) {
        if ($payment['status'] !== 'paid') {
            db_run('UPDATE payments SET status = ?, channel = ?, paid_at = ?, raw = ? WHERE id = ?', [
                'paid', (string) ($tx['channel'] ?? ''), now(), json_encode($tx), $payment['id'],
            ]);
            notify_payment($payment);
        }
        return;
    }

    if ($status === 'success' && !$amountOk) {
        log_event('PAYMENT_AMOUNT_MISMATCH', $payment['reference']);
        $status = 'review';
    }

    db_run('UPDATE payments SET status = ?, raw = ? WHERE id = ? AND status <> ?', [
        in_array($status, ['failed', 'abandoned', 'review'], true) ? $status : 'pending',
        json_encode($tx),
        $payment['id'],
        'paid',
    ]);
}

function notify_payment(array $payment): void
{
    $course = course($payment['course_slug'])['title'] ?? $payment['course_slug'];
    $amount = naira((int) $payment['amount_kobo']);
    $planTotal = course($payment['course_slug'])['fees'][$payment['plan']] ?? null;
    $plan = (fee_plans()[$payment['plan']] ?? $payment['plan'])
        . ($planTotal ? ' – ' . plan_breakdown($planTotal, $payment['plan']) : '');

    send_mail(
        (string) config('notify_to'),
        "Payment received: $amount for $course",
        "Name: {$payment['name']}\nEmail: {$payment['email']}\nPhone: {$payment['phone']}\nCourse: $course\nPlan: $plan\nAmount paid: $amount\nReference: {$payment['reference']}",
        $payment['email']
    );
    send_mail(
        $payment['email'],
        'Payment received – HLTS Online Institution',
        "Hello {$payment['name']},\n\nWe have received your payment of $amount for $course.\nReference: {$payment['reference']}\n\nOur team will send your onboarding details shortly.\n\nHLTS Online Institution"
    );
}
