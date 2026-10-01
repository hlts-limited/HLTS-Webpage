<?php
/**
 * Receives every public form on the site.
 *
 * With JavaScript the form posts here with fetch() and gets JSON back, so the
 * visitor stays on the page. Without JavaScript it posts normally and gets a
 * full results page (or is sent back to the form with their answers kept).
 */

require __DIR__ . '/lib/app.php';

if (!is_post()) {
    http_response_code(405);
    render_message_page('Use the form', 'Please submit this from one of the forms on the website.', false);
    exit;
}

$key = is_string($_POST['form'] ?? null) ? $_POST['form'] : '';
$definition = form_definition($key);
$returnTo = is_string($_POST['return_to'] ?? null) && str_starts_with($_POST['return_to'], '/') && !str_starts_with($_POST['return_to'], '//')
    ? $_POST['return_to']
    : '/';

if (!$definition) {
    http_response_code(400);
    render_message_page('Unknown form', 'We could not tell which form this came from. Please try again from the website.', false);
    exit;
}

csrf_require();

$fail = function (string $message, array $errors = [], int $status = 422) use ($key, $returnTo) {
    if (wants_json()) {
        json_response(['ok' => false, 'message' => $message, 'errors' => $errors], $status);
    }
    flash('form_state', ['form' => $key, 'old' => $_POST, 'errors' => $errors, 'message' => $message]);
    redirect($returnTo . (str_contains($returnTo, '#') ? '' : '#form'));
};

// Bots fill in the hidden "website" field. Pretend it worked and stop.
if (!empty($_POST['website'])) {
    log_event('HONEYPOT', $key);
    wants_json() ? json_response(['ok' => true, 'message' => $definition['success']]) : render_message_page('Thank you', $definition['success'], true);
    exit;
}

if (!rate_limit("form:$key:" . client_ip(), 6, 600)) {
    log_event('FORM_RATE_LIMITED', $key);
    $fail('Too many submissions from your connection. Please wait a few minutes and try again.', [], 429);
}

[$data, $errors] = validate_form($definition, $_POST);

// Course-specific rules.
if ($key === 'student' && !isset($errors['course'])) {
    $hasFees = course($data['course'])['fees'] ?? [];
    if ($hasFees && $data['plan'] === '') {
        $errors['plan'] = 'Choose a payment plan.';
    }
    if ($data['age_group'] === 'under-13' || $data['age_group'] === '13-17') {
        if ($data['guardian'] === '') {
            $errors['guardian'] = 'Add a parent or guardian name for learners under 18.';
        }
    }
}

if ($errors) {
    $fail('Please check the highlighted fields.', $errors);
}

$map = $definition['lead'];
$details = describe_submission($definition, $data);

try {
    $leadId = db_insert('leads', [
        'type' => $key,
        'name' => (string) ($data[$map['name'] ?? ''] ?? ''),
        'email' => (string) ($data[$map['email'] ?? ''] ?? ''),
        'phone' => (string) ($data[$map['phone'] ?? ''] ?? ''),
        'organisation' => (string) ($data[$map['organisation'] ?? ''] ?? ''),
        'summary' => mb_substr(($definition['summary'])($data), 0, 250),
        'payload' => json_encode($data, JSON_UNESCAPED_UNICODE),
        'status' => 'new',
        'ip' => client_ip(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
} catch (Throwable $e) {
    error_log('Lead save failed: ' . $e->getMessage());
    $leadId = null;
}

// Email the team.
$body = '';
foreach ($details as $label => $value) {
    $body .= $label . ': ' . $value . "\n";
}
$body .= "\nReceived: " . now() . ($leadId ? "\nView in admin: " . absolute_url("admin/lead.php?id=$leadId") : '');
$notifyTo = config("notify_to_by_form.$key") ?: config('notify_to');
$emailOk = send_mail((string) $notifyTo, $definition['subject'] . (isset($data['name']) && $data['name'] !== '' ? ': ' . $data['name'] : ''), $body, $data['email'] ?? null);

if (!$leadId && !$emailOk) {
    log_event('SUBMISSION_LOST', $key);
    $fail('We could not save your details just now. Please try again, or contact us on WhatsApp.', [], 500);
}

// Confirmation to the visitor.
if (($definition['confirm'] ?? true) && config('mail.send_confirmations') && !empty($data['email'])) {
    $name = $data['name'] ?? '';
    $copy = '';
    foreach ($details as $label => $value) {
        $copy .= "- $label: $value\n";
    }
    send_mail(
        $data['email'],
        'We received your ' . strtolower($definition['title']) . ' – HLTS Limited',
        "Hello" . ($name ? " $name" : '') . ",\n\n" . $definition['success'] . "\n\nHere is what you sent:\n$copy\nIf anything is wrong, just reply to this email.\n\nHLTS Limited\n" . config('phone_display') . ' · ' . config('email_public')
    );
}

// Optional online payment straight after course registration.
$redirect = null;
if ($key === 'student' && !empty($_POST['pay_now']) && paystack_enabled() && course_fee($data['course'], $data['plan']) !== null) {
    try {
        $redirect = paystack_start($data, $data['course'], $data['plan'], $leadId);
    } catch (Throwable $e) {
        log_event('PAYSTACK_INIT_FAILED', $e->getMessage());
    }
}

if (wants_json()) {
    json_response(['ok' => true, 'message' => $definition['success'], 'redirect' => $redirect]);
}
if ($redirect) {
    redirect($redirect);
}
render_message_page('Thank you', $definition['success'], true, $returnTo, 'Back to the page');
