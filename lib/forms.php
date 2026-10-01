<?php
/**
 * Every public form on the site is defined here once.
 *
 * The same definition drives three things: the HTML fields (ui.php),
 * server-side validation, and what gets saved and emailed (submit.php).
 * To add a field, add it here; to add a form, add an entry and post to
 * submit.php with form=<key>.
 */

function course_options(): array
{
    $options = [];
    foreach (courses() as $slug => $course) {
        $options[$slug] = $course['title'];
    }
    return $options;
}

function module_options(): array
{
    $options = [];
    foreach (school_modules() as $key => $module) {
        $options[$key] = $module['title'];
    }
    return $options;
}

function module_icons(): array
{
    return array_map(fn ($m) => $m['icon'], school_modules());
}

function course_icons(): array
{
    return array_map(fn ($c) => $c['icon'], courses());
}

/** "₦180,000 per session · or ₦30,000/month" under each course option. */
function course_meta(): array
{
    $meta = [];
    foreach (courses() as $slug => $course) {
        $fees = $course['fees'];
        if (!$fees) {
            $meta[$slug] = 'Fees on request';
            continue;
        }
        $parts = [];
        if (isset($fees['session'])) {
            $parts[] = naira($fees['session']) . ' per session';
        }
        if (isset($fees['monthly'])) {
            $parts[] = 'or ' . naira(instalment_amount($fees['monthly'], 'monthly')) . '/month';
        }
        $meta[$slug] = implode(' · ', $parts);
    }
    return $meta;
}

/** Fees for the registration form script: total and single payment per plan. */
function course_fee_data(): array
{
    $data = [];
    foreach (courses() as $slug => $course) {
        foreach ($course['fees'] as $plan => $total) {
            $data[$slug][$plan] = [
                'each' => instalment_amount($total, $plan),
                'total' => $total,
                'count' => plan_instalments($plan),
            ];
        }
    }
    return $data;
}

function form_definitions(): array
{
    $consent = ['type' => 'consent', 'label' => 'I agree to the <a href="/terms.html#terms-of-service" target="_blank" rel="noopener">Terms</a> and <a href="/terms.html#privacy-policy" target="_blank" rel="noopener">Privacy Policy</a>.', 'required' => true];
    $name = ['type' => 'text', 'label' => 'Full name', 'required' => true, 'autocomplete' => 'name', 'max' => 120];
    $email = ['type' => 'email', 'label' => 'Email address', 'required' => true, 'autocomplete' => 'email', 'placeholder' => 'you@example.com'];
    $phone = ['type' => 'tel', 'label' => 'Phone number', 'required' => true, 'autocomplete' => 'tel', 'placeholder' => '0810 000 0000'];

    return [
        'student' => [
            'title' => 'Course registration',
            'subject' => 'New course registration',
            'success' => 'You are registered. We have emailed you a confirmation and our team will contact you within one working day.',
            'fields' => [
                'course' => ['type' => 'choice-cards', 'label' => 'Choose your course', 'required' => true, 'options' => course_options(), 'icons' => course_icons(), 'meta' => array_map('h', course_meta())],
                'plan' => ['type' => 'radio', 'label' => 'Payment plan', 'required' => false, 'options' => fee_plans(), 'help' => 'A session is 8 months. Per semester is 2 payments; monthly is 8 payments.'],
                'mode' => ['type' => 'radio', 'label' => 'How would you like to learn?', 'required' => true, 'options' => ['online' => 'Online', 'in-person' => 'In person (Lagos)', 'either' => 'Either works']],
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'age_group' => ['type' => 'select', 'label' => 'Age group', 'required' => false, 'options' => ['under-13' => 'Under 13', '13-17' => '13–17', '18-24' => '18–24', '25-plus' => '25 and above']],
                'guardian' => ['type' => 'text', 'label' => 'Parent or guardian name', 'required' => false, 'help' => 'Required for learners under 18.', 'max' => 120],
                'message' => ['type' => 'textarea', 'label' => 'Your goals or questions', 'required' => false, 'placeholder' => 'What would you like to be able to do after this course?', 'max' => 2000],
                'terms' => $consent,
            ],
            'lead' => ['name' => 'name', 'email' => 'email', 'phone' => 'phone'],
            'summary' => fn ($d) => (course_options()[$d['course']] ?? 'Course') . ($d['plan'] ? ' · ' . (fee_plans()[$d['plan']] ?? '') : ''),
        ],

        'school' => [
            'title' => 'School registration',
            'subject' => 'New school registration',
            'success' => 'Thank you for registering your school. We have emailed a confirmation and will contact you to plan the next step.',
            'fields' => [
                'modules' => ['type' => 'checkbox-cards', 'label' => 'Which services do you need?', 'required' => true, 'options' => module_options(), 'icons' => module_icons()],
                'school' => ['type' => 'text', 'label' => 'School name', 'required' => true, 'autocomplete' => 'organization', 'max' => 160],
                'level' => ['type' => 'radio', 'label' => 'School level', 'required' => true, 'options' => ['primary' => 'Primary', 'secondary' => 'Secondary', 'both' => 'Primary & secondary']],
                'students' => ['type' => 'select', 'label' => 'Number of students', 'required' => true, 'options' => ['under-200' => 'Under 200', '200-500' => '200–500', '500-1000' => '500–1,000', '1000-plus' => 'Over 1,000']],
                'location' => ['type' => 'text', 'label' => 'School location', 'required' => true, 'placeholder' => 'Area and state', 'max' => 160],
                'name' => ['type' => 'text', 'label' => 'Your name', 'required' => true, 'autocomplete' => 'name', 'max' => 120],
                'role' => ['type' => 'text', 'label' => 'Your role', 'required' => true, 'placeholder' => 'e.g. Principal, Proprietor, Admin', 'max' => 80],
                'email' => ['type' => 'email', 'label' => 'Official email', 'required' => true, 'autocomplete' => 'email'],
                'phone' => ['type' => 'tel', 'label' => 'Official phone', 'required' => true, 'autocomplete' => 'tel'],
                'message' => ['type' => 'textarea', 'label' => 'Anything else we should know?', 'required' => false, 'max' => 2000],
                'terms' => $consent,
            ],
            'lead' => ['name' => 'name', 'email' => 'email', 'phone' => 'phone', 'organisation' => 'school'],
            'summary' => fn ($d) => implode(', ', array_map(fn ($m) => module_options()[$m] ?? $m, (array) $d['modules'])),
        ],

        'demo' => [
            'title' => 'Demo request',
            'subject' => 'New demo booking',
            'success' => 'Your demo request is in. We will confirm a date and time with you within one working day.',
            'fields' => [
                'interests' => ['type' => 'checkbox-cards', 'label' => 'What would you like to see?', 'required' => true, 'options' => module_options(), 'icons' => module_icons()],
                'school' => ['type' => 'text', 'label' => 'School name', 'required' => true, 'autocomplete' => 'organization', 'max' => 160],
                'students' => ['type' => 'select', 'label' => 'Number of students', 'required' => true, 'options' => ['under-200' => 'Under 200', '200-500' => '200–500', '500-1000' => '500–1,000', '1000-plus' => 'Over 1,000']],
                'format' => ['type' => 'radio', 'label' => 'Demo format', 'required' => true, 'options' => ['visit' => 'Visit our school', 'online' => 'Online meeting', 'office' => 'At the HLTS office']],
                'date' => ['type' => 'date', 'label' => 'Preferred date', 'required' => true, 'min_today' => true],
                'time' => ['type' => 'select', 'label' => 'Preferred time', 'required' => true, 'options' => ['morning' => 'Morning (9am–12pm)', 'afternoon' => 'Afternoon (12pm–3pm)', 'late' => 'Late afternoon (3pm–5pm)']],
                'name' => ['type' => 'text', 'label' => 'Your name', 'required' => true, 'autocomplete' => 'name', 'max' => 120],
                'role' => ['type' => 'text', 'label' => 'Your role', 'required' => true, 'placeholder' => 'e.g. Principal', 'max' => 80],
                'email' => $email,
                'phone' => $phone,
                'terms' => $consent,
            ],
            'lead' => ['name' => 'name', 'email' => 'email', 'phone' => 'phone', 'organisation' => 'school'],
            'summary' => fn ($d) => 'Demo ' . format_date($d['date']) . ' · ' . ($d['format'] ?? ''),
        ],

        'support' => [
            'title' => 'IT support request',
            'subject' => 'New IT support request',
            'success' => 'Your support request has been logged. Our team will respond according to the urgency you selected.',
            'fields' => [
                'school' => ['type' => 'text', 'label' => 'School name', 'required' => true, 'autocomplete' => 'organization', 'max' => 160],
                'category' => ['type' => 'select', 'label' => 'What needs attention?', 'required' => true, 'options' => ['cbt' => 'CBT / exams', 'results' => 'Results / report cards', 'computers' => 'Computers or lab', 'network' => 'Internet or network', 'portal' => 'Portal or website', 'other' => 'Something else']],
                'urgency' => ['type' => 'radio', 'label' => 'How urgent is it?', 'required' => true, 'options' => ['low' => 'Can wait a few days', 'normal' => 'Within 24 hours', 'urgent' => 'Urgent – affecting classes or exams']],
                'description' => ['type' => 'textarea', 'label' => 'Describe the problem', 'required' => true, 'placeholder' => 'What happened, when it started, and what you have tried.', 'max' => 3000],
                'name' => ['type' => 'text', 'label' => 'Your name', 'required' => true, 'autocomplete' => 'name', 'max' => 120],
                'email' => $email,
                'phone' => $phone,
            ],
            'lead' => ['name' => 'name', 'email' => 'email', 'phone' => 'phone', 'organisation' => 'school'],
            'summary' => fn ($d) => strtoupper($d['urgency']) . ' · ' . ($d['category'] ?? ''),
        ],

        'quote' => [
            'title' => 'Project quote request',
            'subject' => 'New digital project enquiry',
            'success' => 'Thanks for telling us about your project. We will reply with questions or a proposal within two working days.',
            'fields' => [
                'project' => ['type' => 'checkbox-cards', 'label' => 'What do you need?', 'required' => true, 'options' => ['website' => 'Website', 'app' => 'Web or mobile app', 'portal' => 'Portal or dashboard', 'design' => 'Brand & design', 'integration' => 'Integration', 'other' => 'Something else']],
                'budget' => ['type' => 'radio', 'label' => 'Budget range', 'required' => true, 'options' => ['under-500k' => 'Under ₦500k', '500k-2m' => '₦500k – ₦2m', '2m-5m' => '₦2m – ₦5m', '5m-plus' => 'Over ₦5m', 'unsure' => 'Not sure yet']],
                'timeline' => ['type' => 'radio', 'label' => 'When do you need it?', 'required' => true, 'options' => ['asap' => 'As soon as possible', '1-3m' => '1–3 months', '3m-plus' => '3+ months', 'flexible' => 'Flexible']],
                'description' => ['type' => 'textarea', 'label' => 'Describe your project', 'required' => true, 'placeholder' => 'Who is it for, what should it do, and what does success look like?', 'max' => 4000],
                'name' => $name,
                'organisation' => ['type' => 'text', 'label' => 'Organisation', 'required' => false, 'autocomplete' => 'organization', 'max' => 160],
                'email' => $email,
                'phone' => $phone,
                'terms' => $consent,
            ],
            'lead' => ['name' => 'name', 'email' => 'email', 'phone' => 'phone', 'organisation' => 'organisation'],
            'summary' => fn ($d) => implode(', ', (array) $d['project']) . ' · ' . ($d['budget'] ?? ''),
        ],

        'techmind' => [
            'title' => 'TechMind Africa membership',
            'subject' => 'New TechMind Africa member',
            'success' => 'Welcome to TechMind Africa! We will message you on WhatsApp with the community link and upcoming events.',
            'fields' => [
                'join_as' => ['type' => 'radio', 'label' => 'I want to join as a', 'required' => true, 'options' => ['member' => 'Member', 'mentor' => 'Mentor', 'volunteer' => 'Volunteer', 'partner' => 'Partner organisation']],
                'interests' => ['type' => 'checkbox-cards', 'label' => 'What are you interested in?', 'required' => true, 'options' => ['web' => 'Web development', 'data' => 'Data & AI', 'design' => 'Design', 'hardware' => 'Robotics & hardware', 'teaching' => 'Teaching with tech', 'startups' => 'Startups']],
                'level' => ['type' => 'select', 'label' => 'Experience level', 'required' => true, 'options' => ['curious' => 'Just curious', 'beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'professional' => 'Professional']],
                'name' => $name,
                'email' => $email,
                'phone' => ['type' => 'tel', 'label' => 'WhatsApp number', 'required' => true, 'autocomplete' => 'tel', 'help' => 'We share meetup updates on WhatsApp.'],
                'city' => ['type' => 'text', 'label' => 'City', 'required' => true, 'max' => 80],
                'terms' => $consent,
            ],
            'lead' => ['name' => 'name', 'email' => 'email', 'phone' => 'phone'],
            'summary' => fn ($d) => ucfirst($d['join_as']) . ' · ' . ($d['city'] ?? ''),
        ],

        'contact' => [
            'title' => 'Contact message',
            'subject' => 'New contact message',
            'success' => 'Message sent. We usually reply within one working day.',
            'fields' => [
                'topic' => ['type' => 'select', 'label' => 'Topic', 'required' => true, 'options' => ['school' => 'School services', 'course' => 'Courses', 'digital' => 'Digital project', 'community' => 'TechMind Africa', 'partnership' => 'Partnership', 'other' => 'Other']],
                'name' => $name,
                'email' => $email,
                'phone' => ['type' => 'tel', 'label' => 'Phone number', 'required' => false, 'autocomplete' => 'tel'],
                'message' => ['type' => 'textarea', 'label' => 'Message', 'required' => true, 'max' => 4000],
            ],
            'lead' => ['name' => 'name', 'email' => 'email', 'phone' => 'phone'],
            'summary' => fn ($d) => 'Topic: ' . ($d['topic'] ?? ''),
        ],

        'career' => [
            'title' => 'Job application',
            'subject' => 'New job application',
            'success' => 'Application received. If your profile matches, our team will contact you for the next stage.',
            'fields' => [
                'job' => ['type' => 'hidden', 'label' => 'Role', 'required' => true, 'max' => 191],
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'cv_url' => ['type' => 'url', 'label' => 'Link to your CV or LinkedIn', 'required' => true, 'placeholder' => 'https://', 'help' => 'Google Drive, Dropbox or LinkedIn. Make sure the link is viewable.'],
                'message' => ['type' => 'textarea', 'label' => 'Why are you a good fit?', 'required' => true, 'max' => 3000],
                'terms' => $consent,
            ],
            'lead' => ['name' => 'name', 'email' => 'email', 'phone' => 'phone'],
            'summary' => fn ($d) => 'Applied for: ' . $d['job'],
        ],

        'newsletter' => [
            'title' => 'Newsletter signup',
            'subject' => 'New newsletter subscriber',
            'success' => 'Thanks for subscribing!',
            'confirm' => false,
            'fields' => [
                'email' => ['type' => 'email', 'label' => 'Email address', 'required' => true],
            ],
            'lead' => ['email' => 'email'],
            'summary' => fn ($d) => 'Newsletter',
        ],
    ];
}

function form_definition(string $key): ?array
{
    return form_definitions()[$key] ?? null;
}

/** Normalise a phone number to digits with an optional leading +. */
function normalise_phone(string $phone): string
{
    $phone = preg_replace('/[\s\-().]/', '', $phone) ?? '';
    if (str_starts_with($phone, '+2340')) {
        $phone = '+234' . substr($phone, 5);
    }
    return $phone;
}

/**
 * Validate posted values against a form definition.
 * Returns [cleanData, errors] where errors is field => message.
 */
function validate_form(array $definition, array $input): array
{
    $data = [];
    $errors = [];

    foreach ($definition['fields'] as $name => $field) {
        $raw = $input[$name] ?? null;
        $label = strip_tags($field['label']);
        $required = !empty($field['required']);

        if ($field['type'] === 'checkbox-cards') {
            $values = array_values(array_filter(array_map('strval', (array) $raw)));
            $values = array_values(array_intersect($values, array_keys($field['options'])));
            if ($required && $values === []) {
                $errors[$name] = 'Choose at least one option.';
            }
            $data[$name] = $values;
            continue;
        }

        if ($field['type'] === 'consent') {
            $data[$name] = !empty($raw);
            if ($required && !$data[$name]) {
                $errors[$name] = 'Please agree to continue.';
            }
            continue;
        }

        $value = is_string($raw) ? trim($raw) : '';
        $value = preg_replace('/[^\P{C}\n\t]/u', '', $value) ?? '';
        if ($field['type'] !== 'textarea') {
            $value = str_replace(["\n", "\t"], ' ', $value);
        }

        if ($value === '') {
            if ($required) {
                $errors[$name] = isset($field['options']) ? 'Choose an option.' : "$label is required.";
            }
            $data[$name] = '';
            continue;
        }

        $max = $field['max'] ?? 255;
        if (mb_strlen($value) > $max) {
            $errors[$name] = "Keep this under $max characters.";
        }

        switch ($field['type']) {
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$name] = 'Enter a valid email address, like name@example.com.';
                }
                $value = strtolower($value);
                break;
            case 'tel':
                $value = normalise_phone($value);
                if (!preg_match('/^\+?\d{10,15}$/', $value)) {
                    $errors[$name] = 'Enter a valid phone number, like 0810 000 0000.';
                }
                break;
            case 'url':
                if (!filter_var($value, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $value)) {
                    $errors[$name] = 'Enter a full link starting with https://';
                }
                break;
            case 'date':
                $date = DateTime::createFromFormat('Y-m-d', $value);
                if (!$date || $date->format('Y-m-d') !== $value) {
                    $errors[$name] = 'Choose a valid date.';
                } elseif (!empty($field['min_today']) && $value < date('Y-m-d')) {
                    $errors[$name] = 'Choose today or a later date.';
                }
                break;
        }

        if (isset($field['options']) && !array_key_exists($value, $field['options'])) {
            $errors[$name] = 'Choose one of the listed options.';
        }

        $data[$name] = $value;
    }

    return [$data, $errors];
}

/** Human-readable version of the submitted data for emails and the admin area. */
function describe_submission(array $definition, array $data): array
{
    $lines = [];
    foreach ($definition['fields'] as $name => $field) {
        if ($field['type'] === 'consent') {
            continue;
        }
        $value = $data[$name] ?? '';
        if (is_array($value)) {
            $value = implode(', ', array_map(fn ($v) => $field['options'][$v] ?? $v, $value));
        } elseif (isset($field['options'][$value])) {
            $value = $field['options'][$value];
        }
        if ($value === '' || $value === null) {
            continue;
        }
        $lines[strip_tags($field['label'])] = (string) $value;
    }
    return $lines;
}
