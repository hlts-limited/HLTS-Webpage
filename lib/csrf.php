<?php
/**
 * CSRF protection. One token per visitor session, created on the server.
 *
 * The old version made a new token in the browser on every page load and
 * stored it in a shared cookie, so opening a second tab (even the Terms link
 * on the form) broke the first tab's form. A session token stays the same
 * across tabs until the session ends.
 */

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

function csrf_valid(): bool
{
    $posted = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    return is_string($posted) && $posted !== '' && hash_equals(csrf_token(), $posted);
}

/** Stop the request if the token is missing or wrong. */
function csrf_require(): void
{
    if (csrf_valid()) {
        return;
    }
    log_event('CSRF_FAILED', $_SERVER['REQUEST_URI'] ?? '');
    $message = 'Your session expired. Refresh the page and try again.';
    if (wants_json()) {
        json_response(['ok' => false, 'message' => $message], 419);
    }
    http_response_code(419);
    render_message_page('Session expired', $message, false);
    exit;
}
