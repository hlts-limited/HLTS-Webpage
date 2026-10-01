<?php
require __DIR__ . '/lib/app.php';

page_start([
    'title' => 'Join TechMind Africa – Free tech community',
    'description' => 'Join TechMind Africa for free as a member, mentor, volunteer or partner organisation.',
    'form' => 'techmind',
]);

$form = stepped_form('techmind', [
    ['title' => 'You', 'heading' => 'How would you like to take part?', 'fields' => ['join_as', 'interests', 'level']],
    ['title' => 'Contact', 'heading' => 'Where can we reach you?', 'fields' => ['name', 'email', 'phone', 'city', 'terms']],
], 'Join TechMind');

echo form_page(
    [
        'crumbs' => [['TechMind Africa', page_url('community')], ['Join']],
        'eyebrow' => 'Free to join',
        'title' => 'Join <span class="grad-text">TechMind Africa.</span>',
        'lead' => 'Meet people learning and building with technology. Members, mentors, volunteers and partners are all welcome.',
    ],
    form_aside('What you get', 'Membership is free.', 'You can leave any time. We only message you about community activities.', [
        ['calendar-event', 'Meetups & workshops', 'Hands-on sessions in Lagos and online.'],
        ['people', 'Peers and mentors', 'People a few steps ahead who want to help.'],
        ['briefcase', 'Opportunities', 'Projects, internships and roles shared first with members.'],
    ]),
    $form,
    'Join the community',
    'Takes about a minute.'
);

page_end();
