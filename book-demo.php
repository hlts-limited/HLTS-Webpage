<?php
require __DIR__ . '/lib/app.php';

page_start([
    'title' => 'Book a Free School Demo – HLTS Limited',
    'description' => 'See HLTS CBT, result management, school operations and support working for your school. Book a free demo at your school, online or at our Lagos office.',
    'form' => 'demo',
]);

$form = stepped_form('demo', [
    ['title' => 'Interests', 'heading' => 'What would you like to see?', 'fields' => ['interests']],
    ['title' => 'Date & format', 'heading' => 'When and how should we meet?', 'fields' => ['format', 'date', 'time']],
    ['title' => 'Your school', 'heading' => 'Tell us about your school', 'fields' => ['school', 'students', 'name', 'role', 'email', 'phone', 'terms']],
], 'Book my demo');

echo form_page(
    [
        'crumbs' => [['For Schools', page_url('services')], ['Book a demo']],
        'eyebrow' => 'Free school demo',
        'title' => 'See HLTS working <span class="grad-text">for your school.</span>',
        'lead' => 'A friendly 45-minute walkthrough of the modules you care about, shaped around your school. No cost, no obligation.',
    ],
    form_aside('Your demo', 'What to expect', 'We show real screens and answer real questions. Bring your bursar, exam officer or ICT lead.', [
        ['calendar-check', 'Confirmed within one working day', 'We will call or WhatsApp to confirm the time.'],
        ['easel2', 'A demo built around you', 'Only the modules you choose, using examples from schools like yours.'],
        ['file-earmark-text', 'Clear next steps', 'A written summary and quote within two working days if you want one.'],
    ]),
    $form,
    'Book a free demo',
    'Takes about one minute.'
);

page_end();
