<?php
require __DIR__ . '/lib/app.php';

page_start([
    'title' => 'Register Your School – HLTS Limited',
    'description' => 'Register your school with HLTS for staff deployment, CBT, result management, IT support, labs and full school management.',
    'form' => 'school',
]);

$form = stepped_form('school', [
    ['title' => 'Services', 'heading' => 'Which services does your school need?', 'fields' => ['modules', 'package']],
    ['title' => 'Your school', 'heading' => 'Tell us about your school', 'fields' => ['school', 'level', 'students', 'location']],
    ['title' => 'Contact', 'heading' => 'Who should we speak to?', 'fields' => ['name', 'role', 'email', 'phone', 'message', 'terms']],
], 'Register school');

echo form_page(
    [
        'crumbs' => [['For Schools', page_url('services')], ['Register your school']],
        'eyebrow' => 'Register your school',
        'title' => 'Start working with <span class="grad-text">HLTS@School.</span>',
        'lead' => 'Three short steps. We will contact you within one working day to plan the next step.',
    ],
    form_aside('What happens next', 'A clear, no-pressure start.', 'Registering does not commit your school to anything. It helps us prepare a useful first conversation.', [
        ['telephone-inbound', 'We call you', 'Within one working day, to understand your priorities.'],
        ['building-check', 'We visit or meet online', 'A short discovery session with your leadership team.'],
        ['file-earmark-text', 'You get a proposal', 'Clear scope, timeline and pricing for the services you chose.'],
    ]),
    $form,
    'Register your school',
    'Takes about two minutes.'
);

page_end();
