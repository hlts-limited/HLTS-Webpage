<?php
/**
 * Rendering helpers shared by all pages: layout, section headings, buttons,
 * icons and form fields. Keeping these in one place keeps every page
 * consistent.
 */

/**
 * Start a public page.
 *
 *   page_start(['title' => 'About', 'description' => '...', 'page' => 'about']);
 */
function page_start(array $meta = []): void
{
    $GLOBALS['page'] = array_merge([
        'title' => 'HLTS Limited – Education, technology and people',
        'description' => 'HLTS Limited is a Lagos EdTech company supporting primary and secondary schools with technology, operations and staff, running the HLTS Online Institution and the TechMind Africa community, and building digital solutions.',
        'page' => basename($_SERVER['SCRIPT_NAME'] ?? 'index.php', '.php'),
        'body_class' => '',
        'scripts' => [],
        'image' => 'images/logoh.png',
        'noindex' => false,
    ], $meta);

    require APP_ROOT . '/partials/head.php';
    require APP_ROOT . '/partials/nav.php';
    echo '<main id="main" class="site-main">';
}

function page_end(): void
{
    echo '</main>';
    require APP_ROOT . '/partials/footer.php';
}

function current_page(): string
{
    return $GLOBALS['page']['page'] ?? '';
}

function icon(string $name, string $class = ''): string
{
    return '<i class="bi bi-' . h($name) . ($class ? ' ' . h($class) : '') . '" aria-hidden="true"></i>';
}

/** Image path with spaces and other characters encoded for use in src. */
function img(string $path): string
{
    return '/' . implode('/', array_map('rawurlencode', explode('/', ltrim($path, '/'))));
}

function eyebrow(string $text): string
{
    return '<span class="eyebrow">' . h($text) . '</span>';
}

function section_head(string $eyebrow, string $title, string $lead = '', string $align = 'center', string $id = ''): string
{
    $html = '<header class="section-head section-head--' . h($align) . '" data-reveal>';
    if ($eyebrow !== '') {
        $html .= eyebrow($eyebrow);
    }
    $html .= '<h2' . ($id ? ' id="' . h($id) . '"' : '') . '>' . $title . '</h2>';
    if ($lead !== '') {
        $html .= '<p class="lead">' . h($lead) . '</p>';
    }
    return $html . '</header>';
}

function button(string $label, string $href, string $style = 'primary', string $iconName = 'arrow-up-right', array $attrs = []): string
{
    $class = trim('btn-hl btn-hl--' . $style . ' ' . ($attrs['class'] ?? ''));
    unset($attrs['class']);
    if (preg_match('#^https?://#', $href) && !isset($attrs['target'])) {
        $attrs += ['target' => '_blank', 'rel' => 'noopener'];
    }
    $extra = '';
    foreach ($attrs as $key => $value) {
        $extra .= ' ' . h($key) . '="' . h($value) . '"';
    }
    $iconHtml = $iconName !== '' ? ' ' . icon($iconName) : '';
    return '<a class="' . h($class) . '" href="' . h($href) . '"' . $extra . '><span>' . h($label) . '</span>' . $iconHtml . '</a>';
}

/** Gradient call-to-action band used at the bottom of most pages. */
function cta_band(string $title, string $text, array $primary, ?array $secondary = null): string
{
    $html = '<section class="cta-band"><div class="container"><div class="cta-band__inner" data-reveal>';
    $html .= '<div class="cta-band__copy"><h2>' . h($title) . '</h2><p>' . h($text) . '</p></div>';
    $html .= '<div class="cta-band__actions">' . button($primary[0], $primary[1], 'light');
    if ($secondary) {
        $html .= button($secondary[0], $secondary[1], 'ghost-light', $secondary[2] ?? 'arrow-right');
    }
    return $html . '</div><span class="cta-band__loop" aria-hidden="true">' . infinity_svg('cta') . '</span></div></div></section>';
}

/** The infinity mark from the logo, drawn as a stroke so it can animate. */
function infinity_svg(string $id = 'mark', string $class = ''): string
{
    $gid = 'inf-grad-' . h($id);
    return '<svg class="infinity ' . h($class) . '" viewBox="0 0 200 100" aria-hidden="true" focusable="false">'
        . '<defs><linearGradient id="' . $gid . '" x1="0" x2="1" y1="0" y2="0">'
        . '<stop offset="0" stop-color="var(--indigo-500)"/><stop offset=".5" stop-color="var(--violet-500)"/><stop offset="1" stop-color="var(--magenta-500)"/>'
        . '</linearGradient></defs>'
        . '<path class="infinity__track" d="' . INFINITY_PATH . '"/>'
        . '<path class="infinity__line" d="' . INFINITY_PATH . '" stroke="url(#' . $gid . ')"/>'
        . '</svg>';
}

const INFINITY_PATH = 'M100 50 C 120 20, 170 15, 178 45 C 186 75, 140 85, 100 50 C 60 15, 14 25, 22 55 C 30 85, 80 80, 100 50 Z';

function page_hero(array $o): string
{
    $html = '<section class="page-hero' . (!empty($o['dark']) ? ' page-hero--dark' : '') . '">';
    $html .= '<div class="page-hero__aurora" aria-hidden="true"></div>';
    $html .= '<div class="container page-hero__grid' . (empty($o['visual']) ? ' page-hero__grid--solo' : '') . '">';
    $html .= '<div class="page-hero__copy">';
    if (!empty($o['crumbs'])) {
        $html .= breadcrumbs($o['crumbs']);
    }
    $html .= '<div data-reveal>' . eyebrow($o['eyebrow'] ?? '') . '</div>';
    $html .= '<h1 data-reveal data-reveal-delay="1">' . $o['title'] . '</h1>';
    if (!empty($o['lead'])) {
        $html .= '<p class="lead" data-reveal data-reveal-delay="2">' . h($o['lead']) . '</p>';
    }
    if (!empty($o['actions'])) {
        $html .= '<div class="page-hero__actions" data-reveal data-reveal-delay="3">' . $o['actions'] . '</div>';
    }
    if (!empty($o['note'])) {
        $html .= '<p class="page-hero__note" data-reveal data-reveal-delay="4">' . $o['note'] . '</p>';
    }
    $html .= '</div>';
    if (!empty($o['visual'])) {
        $html .= '<div class="page-hero__visual" data-reveal="zoom" data-reveal-delay="2">' . $o['visual'] . '</div>';
    }
    return $html . '</div></section>';
}

function breadcrumbs(array $crumbs): string
{
    $html = '<nav class="crumbs" aria-label="Breadcrumb"><ol><li><a href="/">Home</a></li>';
    $last = count($crumbs) - 1;
    foreach (array_values($crumbs) as $i => $crumb) {
        [$label, $href] = $crumb + [1 => null];
        $html .= $i === $last || !$href
            ? '<li aria-current="page">' . h($label) . '</li>'
            : '<li><a href="' . h($href) . '">' . h($label) . '</a></li>';
    }
    return $html . '</ol></nav>';
}

function photo_frame(string $src, string $alt, string $caption = '', string $label = '', bool $eager = false): string
{
    $html = '<figure class="photo-frame">';
    $html .= '<img src="' . h(img($src)) . '" alt="' . h($alt) . '"' . ($eager ? ' fetchpriority="high"' : ' loading="lazy"') . ' decoding="async">';
    if ($caption !== '') {
        $html .= '<figcaption>' . ($label ? '<span>' . h($label) . '</span>' : '') . '<strong>' . h($caption) . '</strong></figcaption>';
    }
    return $html . '</figure>';
}

function course_card(string $slug, array $course): string
{
    $html = '<a class="course-card" href="' . h(page_url('course-detail', ['c' => $slug])) . '" data-category="' . h(slugify($course['track'])) . '" data-reveal>';
    $html .= '<div class="course-card__media"><div class="course-card__media-clip"><img src="' . h(img($course['image'])) . '" alt="" loading="lazy" decoding="async"></div>';
    $html .= '<svg class="course-card__ring" viewBox="0 0 46 46" aria-hidden="true"><circle class="bg" cx="23" cy="23" r="20"/><circle class="fg" cx="23" cy="23" r="20"/></svg>';
    $html .= '<span class="course-card__icon">' . icon($course['icon']) . '</span></div>';
    $html .= '<div class="course-card__body"><span class="chip chip--violet">' . h($course['track']) . '</span><h3>' . h($course['title']) . '</h3><p>' . h($course['summary']) . '</p>';
    $html .= '<div class="course-card__fees">';
    if ($course['fees']) {
        foreach ($course['fees'] as $plan => $kobo) {
            $html .= '<span><strong>' . h(instalment_label($kobo, $plan)) . '</strong></span>';
        }
    } else {
        $html .= '<span>Fees on request</span>';
    }
    $html .= '</div><span class="text-link">View course ' . icon('arrow-right') . '</span></div></a>';
    return $html;
}

function empty_state(string $iconName, string $title, string $text, string $action = ''): string
{
    return '<div class="empty-state" data-reveal>' . icon($iconName) . '<h3>' . h($title) . '</h3><p>' . h($text) . '</p>' . $action . '</div>';
}

/**
 * The left column on form pages: what happens next, plus direct contact.
 * $points is a list of [icon, title, text].
 */
function form_aside(string $eyebrowText, string $title, string $text, array $points): string
{
    $html = '<aside class="form-page__aside" data-reveal>' . eyebrow($eyebrowText) . '<h2>' . h($title) . '</h2><p>' . h($text) . '</p><ul class="aside-list">';
    foreach ($points as [$iconName, $pointTitle, $pointText]) {
        $html .= '<li>' . icon($iconName) . '<div><strong>' . h($pointTitle) . '</strong><span>' . h($pointText) . '</span></div></li>';
    }
    $html .= '</ul><p class="small muted mb-2">Prefer to talk?</p><div class="contact-strip">';
    $html .= '<a href="https://wa.me/' . h(config('whatsapp')) . '" target="_blank" rel="noopener">' . icon('whatsapp') . ' Chat on WhatsApp ' . icon('arrow-up-right') . '</a>';
    $html .= '<a href="tel:' . h(config('phone')) . '">' . icon('telephone') . ' ' . h(config('phone_display')) . ' ' . icon('arrow-up-right') . '</a>';
    return $html . '</div></aside>';
}

/** Hero + two-column layout wrapper for form pages. */
function form_page(array $hero, string $aside, string $form, string $formTitle = '', string $formText = ''): string
{
    $html = page_hero($hero + ['crumbs' => $hero['crumbs'] ?? []]);
    $html .= '<section class="section" id="form"><div class="container"><div class="form-page">' . $aside;
    $html .= '<div class="form-shell" data-reveal="zoom">';
    if ($formTitle !== '') {
        $html .= '<div class="form-shell__head"><h2>' . h($formTitle) . '</h2>' . ($formText ? '<p>' . h($formText) . '</p>' : '') . '</div>';
    }
    $state = form_state($GLOBALS['page']['form'] ?? '');
    if (!empty($state['message'])) {
        $html .= '<div class="notice notice--error mb-3">' . icon('exclamation-circle') . '<p>' . h($state['message']) . '</p></div>';
    }
    return $html . $form . '</div></div></div></section>';
}

/* ------------------------------------------------------------------ */
/* Forms                                                               */
/* ------------------------------------------------------------------ */

function form_state(string $key): array
{
    static $state = null;
    if ($state === null) {
        $state = flash('form_state') ?? [];
    }
    return ($state['form'] ?? null) === $key ? $state : ['old' => [], 'errors' => []];
}

function form_open(string $key, array $attrs = []): string
{
    $class = 'smart-form ' . ($attrs['class'] ?? '');
    $html = '<form class="' . h(trim($class)) . '" method="post" action="/submit.php" data-form="' . h($key) . '" novalidate';
    // Forms with an upload (CVs) must send files.
    $definition = form_definition($key);
    if ($definition && in_array('file', array_column($definition['fields'], 'type'), true)) {
        $html .= ' enctype="multipart/form-data"';
    }
    foreach ($attrs as $name => $value) {
        if ($name !== 'class') {
            $html .= ' ' . h($name) . '="' . h($value) . '"';
        }
    }
    $html .= '>';
    $html .= csrf_field();
    $html .= '<input type="hidden" name="form" value="' . h($key) . '">';
    $html .= '<input type="hidden" name="return_to" value="' . h($_SERVER['REQUEST_URI'] ?? '/') . '">';
    // Honeypot: real visitors never see or fill this field.
    $html .= '<div class="hp" aria-hidden="true"><label>Leave this empty<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>';
    $html .= '<div class="form-alert" role="alert" hidden></div>';
    return $html;
}

/** Render one field from a form definition. $extra can add 'meta' for option cards. */
function form_field(string $formKey, string $name, array $extra = []): string
{
    $definition = form_definition($formKey);
    $field = array_merge($definition['fields'][$name], $extra);
    $state = form_state($formKey);
    $old = $extra['value'] ?? ($state['old'][$name] ?? ($_GET[$name] ?? ''));
    $error = $state['errors'][$name] ?? '';
    $id = $formKey . '-' . $name;
    $required = !empty($field['required']);
    $describedBy = trim(($field['help'] ?? '') !== '' ? "$id-help $id-error" : "$id-error");
    $req = $required ? ' <span class="req" aria-hidden="true">*</span>' : ' <span class="optional">(optional)</span>';
    $type = $field['type'];
    $invalid = $error !== '' ? ' aria-invalid="true"' : '';

    if ($type === 'hidden') {
        return '<input type="hidden" name="' . h($name) . '" value="' . h($old) . '">';
    }

    $showWhen = '';
    $hiddenAttr = '';
    if (!empty($field['show_when'])) {
        $pairs = [];
        foreach ($field['show_when'] as $other => $values) {
            $pairs[] = $other . '=' . implode('|', $values);
        }
        $showWhen = ' data-show-when="' . h(implode('&', $pairs)) . '"';
        $answers = ($state['old'] ?? []) + $_GET;
        $hiddenAttr = field_applies($field, $answers) ? '' : ' hidden';
    }
    $html = '<div class="field field--' . h($type) . '" data-field="' . h($name) . '"' . $showWhen . $hiddenAttr . '>';

    if (in_array($type, ['radio', 'choice-cards', 'checkbox-cards'], true)) {
        $isCheckbox = $type === 'checkbox-cards';
        $html .= '<fieldset' . ($required ? ' data-required="true"' : '') . ' aria-describedby="' . h($describedBy) . '">';
        $html .= '<legend class="field-label">' . h(strip_tags($field['label'])) . $req . '</legend>';
        $html .= '<div class="' . ($type === 'radio' ? 'segmented' : 'option-cards') . '">';
        foreach ($field['options'] as $value => $label) {
            $checked = $isCheckbox ? in_array($value, (array) $old, true) : ((string) $old === (string) $value);
            $optionId = $id . '-' . $value;
            $meta = $field['meta'][$value] ?? '';
            $html .= '<label class="option" for="' . h($optionId) . '">';
            $html .= '<input type="' . ($isCheckbox ? 'checkbox' : 'radio') . '" id="' . h($optionId) . '" name="' . h($name) . ($isCheckbox ? '[]' : '') . '" value="' . h($value) . '"' . ($checked ? ' checked' : '') . ($required && !$isCheckbox ? ' required' : '') . '>';
            $html .= '<span class="option__body">';
            if (!empty($field['icons'][$value])) {
                $html .= icon($field['icons'][$value], 'option__icon');
            }
            $html .= '<span class="option__label">' . h($label) . '</span>';
            if ($meta !== '') {
                $html .= '<span class="option__meta">' . $meta . '</span>';
            }
            $html .= '<span class="option__tick" aria-hidden="true">' . icon('check-lg') . '</span></span></label>';
        }
        $html .= '</div></fieldset>';
    } elseif ($type === 'file') {
        $html .= '<label class="field-label" for="' . h($id) . '">' . h($field['label']) . $req . '</label>';
        $html .= '<label class="file-pick" for="' . h($id) . '">' . icon('file-earmark-arrow-up', 'file-pick__icon')
            . '<span class="file-pick__text" data-file-name>Choose a file</span><span class="file-pick__button">Browse</span>'
            . '<input type="file" id="' . h($id) . '" name="' . h($name) . '" accept="' . h($field['accept'] ?? '') . '" data-max-bytes="' . (int) ($field['max_bytes'] ?? 0) . '"' . ($required ? ' required' : '') . ' aria-describedby="' . h($describedBy) . '"' . $invalid . '></label>';
    } elseif ($type === 'consent') {
        $html .= '<label class="consent" for="' . h($id) . '"><input type="checkbox" id="' . h($id) . '" name="' . h($name) . '" value="1"' . ($old ? ' checked' : '') . ($required ? ' required' : '') . ' aria-describedby="' . h($describedBy) . '"' . $invalid . '><span class="consent__box" aria-hidden="true">' . icon('check-lg') . '</span><span>' . $field['label'] . '</span></label>';
    } else {
        $html .= '<label class="field-label" for="' . h($id) . '">' . h($field['label']) . $req . '</label>';
        $common = ' id="' . h($id) . '" name="' . h($name) . '"' . ($required ? ' required' : '') . ' aria-describedby="' . h($describedBy) . '"' . $invalid;
        if (!empty($field['autocomplete'])) {
            $common .= ' autocomplete="' . h($field['autocomplete']) . '"';
        }
        if (!empty($field['placeholder'])) {
            $common .= ' placeholder="' . h($field['placeholder']) . '"';
        }
        if (!empty($field['max'])) {
            $common .= ' maxlength="' . (int) $field['max'] . '"';
        }

        if ($type === 'textarea') {
            $html .= '<textarea' . $common . ' rows="4">' . h($old) . '</textarea>';
        } elseif ($type === 'select') {
            $html .= '<div class="select-wrap"><select' . $common . '><option value="">Select…</option>';
            foreach ($field['options'] as $value => $label) {
                $html .= '<option value="' . h($value) . '"' . ((string) $old === (string) $value ? ' selected' : '') . '>' . h($label) . '</option>';
            }
            $html .= '</select>' . icon('chevron-down') . '</div>';
        } else {
            $inputType = ['tel' => 'tel', 'email' => 'email', 'url' => 'url', 'date' => 'date'][$type] ?? 'text';
            $min = !empty($field['min_today']) ? ' min="' . date('Y-m-d') . '"' : '';
            $mode = $type === 'tel' ? ' inputmode="tel"' : (!empty($field['numeric']) ? ' inputmode="numeric" pattern="[0-9,]*"' : '');
            $html .= '<div class="input-wrap"><input type="' . $inputType . '"' . $common . $min . $mode . ' value="' . h($old) . '"' . (!empty($field['same_as']) ? ' data-same-target="' . h($field['same_as']) . '"' : '') . '><span class="input-ok" aria-hidden="true">' . icon('check-circle-fill') . '</span></div>';
            if (!empty($field['same_as'])) {
                $same = !empty($state['old'][$name . '_same']);
                $html .= '<label class="same-as"><input type="checkbox" name="' . h($name) . '_same" value="1" data-same-as="' . h($field['same_as']) . '"' . ($same ? ' checked' : '') . '> Same as my phone number</label>';
            }
        }
    }

    if (!empty($field['help'])) {
        $html .= '<p class="field-help" id="' . h($id) . '-help">' . h($field['help']) . '</p>';
    }
    $html .= '<p class="field-error" id="' . h($id) . '-error"' . ($error === '' ? ' hidden' : '') . '>' . icon('exclamation-circle') . '<span>' . h($error) . '</span></p>';

    return $html . '</div>';
}

/** The privacy checkbox for forms that don't go through submit.php (results, certificates). */
function consent_checkbox(string $id, bool $checked = false, string $text = 'I have read and agree to the HLTS'): string
{
    return '<div class="field field--consent" data-field="terms"><label class="consent" for="' . h($id) . '"><input type="checkbox" id="' . h($id) . '" name="terms" value="1" required' . ($checked ? ' checked' : '') . '><span class="consent__box" aria-hidden="true">' . icon('check-lg') . '</span><span>' . h($text) . ' <a href="/privacy.html" target="_blank" rel="noopener">Privacy Policy</a>.</span></label></div>';
}

function form_fields(string $formKey, array $names): string
{
    return implode('', array_map(fn ($n) => form_field($formKey, $n), $names));
}

function submit_button(string $label, string $iconName = 'arrow-right'): string
{
    return '<button type="submit" class="btn-hl btn-hl--primary btn-hl--lg" data-submit><span class="btn-hl__label">' . h($label) . '</span> ' . icon($iconName) . '<span class="btn-hl__spinner" aria-hidden="true"></span></button>';
}

/**
 * A multi-step form. $steps is a list of ['title' => ..., 'fields' => [...], 'html' => optional extra].
 */
function stepped_form(string $formKey, array $steps, string $submitLabel, array $attrs = []): string
{
    $html = form_open($formKey, $attrs + ['data-stepped' => 'true']);
    $count = count($steps);

    $html .= '<ol class="stepper" aria-label="Form progress">';
    foreach ($steps as $i => $step) {
        $html .= '<li class="stepper__item' . ($i === 0 ? ' is-current' : '') . '" data-step-indicator="' . $i . '"><span class="stepper__dot">' . ($i + 1) . '</span><span class="stepper__label">' . h($step['title']) . '</span></li>';
    }
    $html .= '</ol><div class="stepper__bar" aria-hidden="true"><span></span></div>';

    foreach ($steps as $i => $step) {
        $html .= '<section class="form-step" data-step="' . $i . '"' . ($i === 0 ? '' : ' hidden') . ' aria-label="Step ' . ($i + 1) . ' of ' . $count . ': ' . h($step['title']) . '">';
        $html .= '<h3 class="form-step__title">' . h($step['heading'] ?? $step['title']) . '</h3>';
        $html .= $step['before'] ?? '';
        $html .= '<div class="form-grid">' . form_fields($formKey, $step['fields']) . '</div>';
        $html .= $step['after'] ?? '';
        $html .= '<div class="form-nav">';
        if ($i > 0) {
            $html .= '<button type="button" class="btn-hl btn-hl--ghost" data-step-back>' . icon('arrow-left') . ' <span>Back</span></button>';
        }
        $html .= $i < $count - 1
            ? '<button type="button" class="btn-hl btn-hl--primary" data-step-next><span>Continue</span> ' . icon('arrow-right') . '</button>'
            : submit_button($submitLabel, 'send');
        $html .= '</div></section>';
    }

    $html .= success_panel();
    return $html . '</form>';
}

function simple_form(string $formKey, array $fields, string $submitLabel, array $attrs = []): string
{
    $html = form_open($formKey, $attrs);
    $html .= '<div class="form-body"><div class="form-grid">' . form_fields($formKey, $fields) . '</div>';
    $html .= '<div class="form-nav">' . submit_button($submitLabel, 'send') . '</div></div>';
    $html .= success_panel();
    return $html . '</form>';
}

function success_panel(): string
{
    return '<div class="form-success" hidden tabindex="-1">'
        . '<svg class="tick-anim" viewBox="0 0 52 52" aria-hidden="true"><circle cx="26" cy="26" r="24"/><path d="M15 27 l7 7 l15 -16"/></svg>'
        . '<h3>Done!</h3><p class="form-success__text"></p>'
        . '<div class="form-success__actions"><a class="btn-hl btn-hl--ghost" href="/">Back to home</a></div></div>';
}

/** Full-page message used when JavaScript is off or for errors. */
function render_message_page(string $title, string $message, bool $success, string $backHref = '/', string $backLabel = 'Back to home'): void
{
    page_start(['title' => $title . ' – HLTS Limited', 'noindex' => true]);
    echo '<section class="message-page"><div class="container"><div class="message-card ' . ($success ? 'is-success' : 'is-error') . '">';
    echo $success
        ? '<svg class="tick-anim" viewBox="0 0 52 52" aria-hidden="true"><circle cx="26" cy="26" r="24"/><path d="M15 27 l7 7 l15 -16"/></svg>'
        : '<span class="message-card__icon">' . icon('exclamation-triangle') . '</span>';
    echo '<h1>' . h($title) . '</h1><p>' . h($message) . '</p>';
    echo '<div class="message-card__actions">' . button($backLabel, $backHref, 'primary', 'arrow-right') . '</div>';
    echo '</div></div></section>';
    page_end();
}
