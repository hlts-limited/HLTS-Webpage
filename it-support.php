<?php
require __DIR__ . '/lib/app.php';

page_start([
    'title' => 'IT Support Request – HLTS Limited',
    'description' => 'Partner schools can log an IT support request with HLTS for CBT, results, computers, network, portal or website issues.',
    'form' => 'support',
]);

$form = simple_form('support', ['school', 'category', 'urgency', 'description', 'name', 'email', 'phone', 'terms'], 'Send support request');

echo form_page(
    [
        'crumbs' => [['For Schools', page_url('services')], ['IT support request']],
        'eyebrow' => 'Partner school support',
        'title' => 'Something not working? <span class="grad-text">We are on it.</span>',
        'lead' => 'Log the issue here and the HLTS support team will pick it up. For exam-day emergencies, call us as well.',
    ],
    form_aside('How support works', 'Fast, tracked help.', 'Every request is logged, so nothing gets lost between calls and messages.', [
        ['lightning-charge', 'Urgent issues first', 'Problems affecting classes or exams are handled straight away.'],
        ['chat-dots', 'We keep you updated', 'You will hear from us by phone, WhatsApp or email.'],
        ['tools', 'Remote or on site', 'We fix what we can remotely and visit when needed.'],
    ]),
    $form,
    'Log a support request',
    'Partner schools only. Not a partner yet? Book a demo.'
);

page_end();
