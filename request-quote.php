<?php
require __DIR__ . '/lib/app.php';

page_start([
    'title' => 'Request a Quote – HLTS Digital Solutions',
    'description' => 'Tell HLTS about your website, app, portal or design project and get a proposal within two working days.',
    'form' => 'quote',
]);

$form = stepped_form('quote', [
    ['title' => 'Project', 'heading' => 'What do you need?', 'fields' => ['project', 'description']],
    ['title' => 'Budget & timing', 'heading' => 'Budget and timeline', 'fields' => ['budget', 'timeline']],
    ['title' => 'Contact', 'heading' => 'How can we reach you?', 'fields' => ['name', 'organisation', 'email', 'phone', 'terms']],
], 'Send request');

echo form_page(
    [
        'crumbs' => [['Digital Solutions', page_url('digital-solutions')], ['Request a quote']],
        'eyebrow' => 'Start a project',
        'title' => 'Tell us what you <span class="grad-text">want to build.</span>',
        'lead' => 'A few questions now save weeks later. We reply within two working days.',
    ],
    form_aside('What happens next', 'From idea to proposal.', 'Budget ranges help us suggest what is realistic. We will never share your details.', [
        ['chat-square-text', 'A short call', 'We clarify goals, users and must-haves.'],
        ['file-earmark-richtext', 'A written proposal', 'Scope, timeline, cost and what we need from you.'],
        ['rocket-takeoff', 'Kick-off', 'Once you approve, we schedule discovery and design.'],
    ]),
    $form,
    'Request a quote',
    'Takes about three minutes.'
);

page_end();
