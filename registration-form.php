<?php
require __DIR__ . '/lib/app.php';

page_start([
    'title' => 'Register for a Course – HLTS Online Institution',
    'description' => 'Register for an HLTS Online Institution course in web development, data analysis, design, video editing, desktop publishing or block-based programming.',
    'form' => 'student',
]);

$fees = course_fee_data();
$payBox = paystack_enabled()
    ? '<label class="pay-option" data-pay-option hidden><span class="consent"><input type="checkbox" name="pay_now" value="1"><span class="consent__box" aria-hidden="true">' . icon('check-lg') . '</span></span>'
      . '<span><strong>Pay <span data-pay-amount></span> now with Paystack</strong><span data-pay-note>Card, bank transfer or USSD. Secure checkout; you can also pay later.</span></span></label>'
    : '';

$form = stepped_form('student', [
    ['title' => 'Course', 'heading' => 'Which course would you like to take?', 'fields' => ['course', 'plan', 'mode']],
    ['title' => 'Your details', 'heading' => 'Tell us about you', 'fields' => ['name', 'email', 'phone', 'age_group', 'guardian', 'message']],
    ['title' => 'Review', 'heading' => 'Check and confirm', 'before' => '<dl class="review-list" data-review></dl>', 'fields' => ['terms'], 'after' => $payBox],
], 'Complete registration', ['data-fees' => json_encode($fees)]);

echo form_page(
    [
        'crumbs' => [['Online Institution', page_url('online-institution')], ['Register']],
        'eyebrow' => 'HLTS Online Institution',
        'title' => 'Your next skill <span class="grad-text">starts here.</span>',
        'lead' => 'Choose your course, share a few details and we will guide you into the right programme.',
    ],
    form_aside('After you register', 'What happens next', 'We confirm your place, answer your questions and get you set up on the student portal.', [
        ['envelope-check', 'Confirmation email', 'Straight away, with a copy of your registration.'],
        ['telephone', 'A call from our team', 'Within one working day to confirm schedule and payment.'],
        ['person-badge', 'Portal access', 'Your student ID and login to course materials.'],
    ]),
    $form,
    'Register for a course',
    'Takes about two minutes.'
);

page_end();
