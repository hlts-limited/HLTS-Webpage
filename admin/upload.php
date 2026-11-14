<?php
/**
 * Pictures put inside a post (or an event, project or job) from the admin editor.
 *
 * The "Add picture" button above the text box sends the file here and gets back its path, which
 * it writes into the text as ![Caption](images/uploads/…). The same checks as the cover image
 * apply: it must really be a JPG, PNG or WebP, it is re-saved at no more than 2000px, and it is
 * stored under a random name in a folder where nothing can run as code.
 */
require __DIR__ . '/_admin.php';
require_admin();

if (!is_post()) {
    json_response(['ok' => false, 'message' => 'Use the Add picture button in the editor.'], 405);
}
csrf_require();

[$path, $error] = save_uploaded_image($_FILES['picture'] ?? null);
if ($error !== null) {
    json_response(['ok' => false, 'message' => $error], 422);
}
if ($path === null) {
    json_response(['ok' => false, 'message' => 'Choose a picture first.'], 400);
}

log_event('ADMIN_IMAGE_UPLOADED', $path);
json_response(['ok' => true, 'path' => $path]);
