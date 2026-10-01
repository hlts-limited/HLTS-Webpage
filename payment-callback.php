<?php
/**
 * Paystack sends the learner back here after checkout. We never trust the
 * redirect on its own: the payment is verified with Paystack first.
 */

require __DIR__ . '/lib/app.php';

$reference = is_string($_GET['reference'] ?? null) ? $_GET['reference'] : '';
$payment = null;
$error = '';

if ($reference === '' || !paystack_enabled()) {
    $error = 'We could not find a payment to check.';
} else {
    try {
        $payment = paystack_verify($reference);
        if (!$payment) {
            $error = 'We could not find that payment reference.';
        }
    } catch (Throwable $e) {
        log_event('PAYSTACK_VERIFY_FAILED', $e->getMessage());
        $error = 'We could not confirm your payment with Paystack just now. If you were charged, you will receive a confirmation email shortly.';
    }
}

if ($payment && $payment['status'] === 'paid') {
    $course = course($payment['course_slug'])['title'] ?? 'your course';
    render_message_page(
        'Payment successful',
        'We received ' . naira((int) $payment['amount_kobo']) . ' for ' . $course . '. Reference ' . $payment['reference'] . '. A receipt is on its way to ' . $payment['email'] . '.',
        true,
        page_url('portal'),
        'Go to the student portal'
    );
    exit;
}

render_message_page(
    $error ? 'Payment not confirmed' : 'Payment not completed',
    $error ?: 'Your payment was not completed. No money was taken. You can try again from the registration page or pay at our office.',
    false,
    page_url('registration-form'),
    'Back to registration'
);
