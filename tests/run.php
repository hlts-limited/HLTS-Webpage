<?php
/**
 * Checks for the parts of the website where a mistake costs money or trust: school prices and
 * quotes, course fees and instalments, courses edited in the admin area, form validation, the
 * price list accepted from the staff app, payment records, grades, two-step codes and YouTube links.
 *
 *   php tests/run.php
 *
 * Command line only. Uses its own throwaway database (deleted afterwards), never the real one,
 * and never contacts the staff app, Paystack, YouTube or a mail server. Exit code 0 = all passed.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$root = dirname(__DIR__);
$testDb = $root . '/storage/test-' . bin2hex(random_bytes(4)) . '.sqlite';
// Windows keeps the database file open until PHP exits, so leftovers from earlier runs are cleared here.
foreach (glob($root . '/storage/test-*.sqlite') ?: [] as $old) {
    @unlink($root . '/storage/.schema-sqlite-' . md5($old));
    @unlink($old);
}
putenv('HLTS_TEST_SQLITE=' . $testDb); // lib/config.php switches to this database and turns outside services off
register_shutdown_function(function () use ($testDb, $root) {
    foreach (glob($root . '/storage/.schema-sqlite-*') ?: [] as $marker) {
        // Markers are named after the database path; drop the one made for this run.
        if (str_ends_with($marker, md5('' . $testDb))) {
            @unlink($marker);
        }
    }
    @unlink($testDb);
});

// admin/_admin.php loads lib/app.php itself.
require $root . '/admin/_admin.php';

$failures = 0;
$passes = 0;
function check(string $what, bool $ok, $detail = null): void
{
    global $failures, $passes;
    if ($ok) {
        $passes++;
        return;
    }
    $failures++;
    echo "FAIL  $what" . ($detail !== null ? '  ->  ' . (is_string($detail) ? $detail : json_encode($detail, JSON_UNESCAPED_UNICODE)) : '') . PHP_EOL;
}
function same(string $what, $actual, $expected): void
{
    check($what, $actual === $expected, ['got' => $actual, 'expected' => $expected]);
}

// ---- Safety: this run must not be able to reach anything real ---------------------------------
same('uses the throwaway database', config('db.sqlite_path'), $testDb);
same('staff app link is off', app_sync_enabled(), false);
same('Paystack is off', paystack_enabled(), false);

// ---- Money formatting -------------------------------------------------------------------------
same('naira formats kobo', naira(113000000), '₦1,130,000');
same('slugify', slugify('  Front-end Web Development! '), 'front-end-web-development');

// ---- School prices and quotes (built-in list: the app is not contacted in tests) ---------------
same('no app prices without the link', app_prices(), null);
[$total, $how] = pricing_quote(['basis' => 'pupils', 'package' => 'professional', 'pupils' => 120]);
same('per-pupil quote', $total, 113000000);
check('per-pupil quote explains itself', str_contains($how, '120 × ₦6,500'), $how);
[$total, $how] = pricing_quote(['basis' => 'pupils', 'package' => 'basic', 'pupils' => 10]);
same('minimum applies to a small school', $total, 40000000);
check('says the minimum applies', str_contains($how, 'minimum applies'), $how);
same('full-time, listed count', pricing_quote(['basis' => 'fulltime', 'package' => 'premium', 'teachers' => 2])[0], 300000000);
same('full-time, 4 teachers is a custom quote', pricing_quote(['basis' => 'fulltime', 'package' => 'premium', 'teachers' => 4])[0], null);
same('part-time', pricing_quote(['basis' => 'parttime', 'teachers' => 3])[0], 105000000);
check('custom quote wording', str_contains(pricing_quote_text(['basis' => 'fulltime', 'package' => 'basic', 'teachers' => 6]), 'custom quote'));
check('add-ons are listed separately', str_contains(pricing_quote_text(['basis' => 'pupils', 'package' => 'basic', 'pupils' => 200, 'add_ons' => ['cbt']]), 'CBT setup: ₦100,000 to ₦200,000 per term'));

// ---- The price list accepted from the staff app ------------------------------------------------
$good = [
    'packages' => ['basic' => ['base' => 1, 'per_pupil' => 2, 'minimum' => 3], 'professional' => ['base' => 4, 'per_pupil' => 5, 'minimum' => 6], 'premium' => ['base' => 7, 'per_pupil' => 8, 'minimum' => 9]],
    'full_time' => ['1' => ['basic' => 10, 'professional' => 11, 'premium' => 12]],
    'part_time' => ['1' => 13, '2' => 14],
    'add_ons' => [['name' => 'CBT setup', 'from' => 100, 'to' => 200], ['name' => 'Robotics programme', 'from' => null, 'to' => null]],
];
$checked = app_prices_check($good);
check('accepts a well-formed list', is_array($checked));
same('keeps whole-number amounts', $checked['packages']['professional']['per_pupil'] ?? null, 5);
same('teacher counts become numbers', array_keys($checked['part_time'] ?? []), [1, 2]);
foreach ([
    'a missing package' => fn ($p) => array_diff_key($p, []) + ['packages' => array_diff_key($p['packages'], ['premium' => 1])],
    'a negative price' => function ($p) { $p['packages']['basic']['base'] = -1; return $p; },
    'a price sent as text' => function ($p) { $p['part_time']['1'] = '13'; return $p; },
    'a zero price' => function ($p) { $p['full_time']['1']['basic'] = 0; return $p; },
    'an add-on whose top is below its bottom' => function ($p) { $p['add_ons'][0] = ['name' => 'CBT setup', 'from' => 200, 'to' => 100]; return $p; },
    'no teacher prices' => function ($p) { $p['full_time'] = []; return $p; },
] as $what => $break) {
    $broken = $break($good);
    if ($what === 'a missing package') {
        $broken = $good;
        unset($broken['packages']['premium']);
    }
    same("refuses $what", app_prices_check($broken), null);
}

// ---- Courses from the admin area --------------------------------------------------------------
$courses = courses();
check('courses were filled in on first run', count($courses) === count(default_courses()), count($courses));
same('fees are read back in kobo', $courses['frontend']['fees'], default_courses()['frontend']['fees']);
same('a course with no fees stays without fees', $courses['fullstack']['fees'], []);
same('outcomes are one per line', $courses['frontend']['outcomes'], default_courses()['frontend']['outcomes']);
same('instalments per plan', [plan_instalments('session'), plan_instalments('semester'), plan_instalments('monthly')], [1, 2, 8]);
same('monthly charge is one eighth', course_fee('frontend', 'monthly'), 4500000);
same('semester charge is half', course_fee('frontend', 'semester'), 17000000);
same('session charge is the whole fee', course_fee('frontend', 'session'), 30000000);
same('no online payment without a fee', course_fee('fullstack', 'session'), null);
same('unknown course cannot be paid', course_fee('no-such-course', 'session'), null);
same('unknown plan cannot be paid', course_fee('frontend', 'weekly'), null);
check('instalment label', instalment_label(36000000, 'monthly') === '₦45,000 per month', instalment_label(36000000, 'monthly'));

// ---- Form validation --------------------------------------------------------------------------
$contact = form_definition('contact');
[$data, $errors] = validate_form($contact, ['topic' => 'school', 'name' => 'Ada Obi', 'email' => 'ada@example.com', 'phone' => '0810 000 0000', 'message' => 'Hello there', 'terms' => '1']);
same('a complete contact form passes', $errors, []);
same('email is kept', $data['email'] ?? null, 'ada@example.com');
[, $errors] = validate_form($contact, ['topic' => 'nonsense', 'name' => '', 'email' => 'not-an-email', 'message' => '', 'terms' => '']);
foreach (['topic', 'name', 'email', 'message', 'terms'] as $field) {
    check("contact form rejects a bad $field", isset($errors[$field]), array_keys($errors));
}
$pricing = form_definition('pricing');
[, $errors] = validate_form($pricing, ['basis' => 'pupils', 'package' => 'basic', 'pupils' => '', 'school' => 'Test School', 'location' => 'Lagos', 'name' => 'Ada Obi', 'role' => 'Head', 'email' => 'ada@example.com', 'phone' => '08100000000', 'terms' => '1']);
check('per-pupil pricing needs a pupil count', isset($errors['pupils']), array_keys($errors));
check('teacher count is not asked for per-pupil pricing', !isset($errors['teachers']), array_keys($errors));

// ---- A confirmed payment is recorded once, and only for the right amount ------------------------
db_insert('payments', ['reference' => 'HLTS-TEST-0001', 'email' => 'ada@example.com', 'name' => 'Ada', 'phone' => '', 'course_slug' => 'frontend', 'plan' => 'monthly', 'amount_kobo' => 4500000, 'status' => 'pending', 'lead_id' => null, 'created_at' => now()]);
$payment = db_one('SELECT * FROM payments WHERE reference = ?', ['HLTS-TEST-0001']);
paystack_record($payment, ['status' => 'success', 'amount' => 100, 'currency' => 'NGN']);
same('a wrong amount is held for review', db_value('SELECT status FROM payments WHERE reference = ?', ['HLTS-TEST-0001']), 'review');
same('a wrong amount is not queued for Finance', (int) db_value("SELECT COUNT(*) FROM app_sync WHERE form = 'payment'"), 0);
db_run("UPDATE payments SET status = 'paid', paid_at = ? WHERE reference = ?", [now(), 'HLTS-TEST-0001']);
$paid = db_one('SELECT * FROM payments WHERE reference = ?', ['HLTS-TEST-0001']);
same('a paid payment is queued for Finance', app_sync_queue_payment($paid), true);
same('and never queued twice', app_sync_queue_payment($paid), false);
$queued = json_decode((string) db_value("SELECT payload FROM app_sync WHERE form = 'payment' AND ref = ?", ['HLTS-TEST-0001']), true);
same('the queued amount is the amount paid', $queued['amountKobo'] ?? null, 4500000);
same('the queued record names the course', $queued['course'] ?? null, 'Front-end Web Development');
same('nothing is sent while the link is off', app_sync_run(5)['disabled'] ?? false, true);

// ---- Results, two-step codes, links ------------------------------------------------------------
same('grade A', grade_for(70)[0], 'A');
same('grade C', grade_for(55.5)[0], 'C');
same('grade F', grade_for(12)[0], 'F');
check('PIN hashes hide the PIN', result_pin_hash('123456') !== '123456' && result_pin_hash('123456') === result_pin_hash('123456'));
$secret = totp_new_secret();
$step = intdiv(time(), 30);
check('a current two-step code is accepted', totp_verify($secret, totp_code($secret, $step)) !== null);
same('a code cannot be used twice', totp_verify($secret, totp_code($secret, $step), $step), null);
same('a wrong code is refused', totp_verify($secret, '000000' === totp_code($secret, $step) ? '111111' : '000000'), null);
same('YouTube watch link', youtube_id('https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=3'), 'dQw4w9WgXcQ');
same('YouTube short link', youtube_id('https://youtu.be/dQw4w9WgXcQ'), 'dQw4w9WgXcQ');
same('not a YouTube link', youtube_id('https://example.com/watch?v=dQw4w9WgXcQ'), null);
same('safe_url accepts https', safe_url('https://hltsltd.com/x'), true);
same('safe_url refuses javascript:', safe_url('javascript:alert(1)'), false);

// ---- Sign-in pass from the staff app ------------------------------------------------------------
$secret = 'test-secret';
$state = str_repeat('ab', 16);
$make = function (array $claims, string $key = 'test-secret'): string {
    $body = rtrim(strtr(base64_encode(json_encode($claims)), '+/', '-_'), '=');
    return $body . '.' . hash_hmac('sha256', 'sso.' . $body, $key);
};
$now = 1800000000;
$claims = ['v' => 1, 'email' => 'CEO@Example.com', 'name' => 'Ada', 'state' => $state, 'exp' => $now + 60];
same('a good pass is accepted', sso_read_pass($make($claims), $secret, $now), ['email' => 'ceo@example.com', 'name' => 'Ada', 'state' => $state]);
same('a pass signed with another key is refused', sso_read_pass($make($claims, 'other'), $secret, $now), null);
same('an expired pass is refused', sso_read_pass($make($claims), $secret, $now + 120), null);
same('a pass dated far ahead is refused', sso_read_pass($make(['exp' => $now + 3600] + $claims), $secret, $now), null);
same('a changed email breaks the signature', sso_read_pass(str_replace('.', 'x.', $make($claims)), $secret, $now), null);
same('a form-delivery signature is not a pass', sso_read_pass(explode('.', $make($claims))[0] . '.' . hash_hmac('sha256', explode('.', $make($claims))[0], $secret), $secret, $now), null);
same('a pass without a valid email is refused', sso_read_pass($make(['email' => 'nope'] + $claims), $secret, $now), null);
same('no secret, no sign-in', sso_read_pass($make($claims, ''), '', $now), null);

// ---- Links and bold in post text ----------------------------------------------------------------
$own = rtrim((string) config('site_url'), '/') . '/pricing.html'; // this site, wherever it runs
same('a link to this site stays in the same tab', render_inline("Read [our pricing]($own) today"), 'Read <a href="' . h($own) . '">our pricing</a> today');
same('a backslash path to another site is refused', render_inline('[x](/\evil.example)'), '[x](/\evil.example)');
same('another website opens in a new tab', render_inline('[Paystack](https://paystack.com/pricing)'), '<a href="https://paystack.com/pricing" target="_blank" rel="noopener">Paystack</a>');
same('one of our pages', render_inline('See [the FAQ](/faq.html#fees).'), 'See <a href="/faq.html#fees">the FAQ</a>.');
same('email link', render_inline('[Email us](mailto:ceo@hltsltd.com)'), '<a href="mailto:ceo@hltsltd.com">Email us</a>');
same('bold', render_inline('This is **very** important'), 'This is <strong>very</strong> important');
same('bold inside a link', render_inline('[**Book now**](/book-demo.html)'), '<a href="/book-demo.html"><strong>Book now</strong></a>');
same('a javascript: link is left as typed', render_inline('[click](javascript:alert(1))'), '[click](javascript:alert(1))');
same('a javascript: link without brackets is left as typed', render_inline('[click](javascript:alert`1`)'), '[click](javascript:alert`1`)');
same('a link to another site written as //host is refused', render_inline('[x](//evil.example)'), '[x](//evil.example)');
same('a data: link is refused', render_inline('[x](data:text/html,hi)'), '[x](data:text/html,hi)');
same('link words are escaped', render_inline('[<b>hi</b>](https://example.com)'), '<a href="https://example.com" target="_blank" rel="noopener">&lt;b&gt;hi&lt;/b&gt;</a>');
same('quotes in an address cannot break out', render_inline('[x](https://example.com/"onmouseover=alert(1))'), '[x](https://example.com/&quot;onmouseover=alert(1))');
same('text around links is escaped', render_inline('<script> & [a](/b)'), '&lt;script&gt; &amp; <a href="/b">a</a>');
same('plain text is unchanged', render_inline('Nothing special here'), 'Nothing special here');
same('links work in bullets and headings', render_text("## See [pricing](/pricing.html)\n\n- **One** and [two](/two.html)"), '<h2>See <a href="/pricing.html">pricing</a></h2><ul><li><strong>One</strong> and <a href="/two.html">two</a></li></ul>');

// ---- Pictures inside post text ------------------------------------------------------------------
check('a picture with a caption', str_contains(render_text('![Our new lab](images/setup.jpeg)'), '<figcaption>Our new lab</figcaption>') && str_contains(render_text('![Our new lab](images/setup.jpeg)'), 'src="/images/setup.jpeg"'), render_text('![Our new lab](images/setup.jpeg)'));
check('a picture carries its real size, so the page keeps its place', (bool) preg_match('/ width="\d+" height="\d+"/', render_text('![x](images/setup.jpeg)')), render_text('![x](images/setup.jpeg)'));
same('a picture without a caption', render_text('![](images/uploads/2026-11/abc123.jpg)'), '<figure class="prose-figure"><img src="/images/uploads/2026-11/abc123.jpg" alt="" loading="lazy" decoding="async"></figure>'); // a file that isn't there carries no size
check('a picture sits between paragraphs', (bool) preg_match('#^<p>One\.</p><figure class="prose-figure"><img .*</figure><p>Two\.</p>$#', render_text("One.

![A photo](images/setup.jpeg)

Two.")), render_text("One.

![A photo](images/setup.jpeg)

Two."));
same('a picture from another website is left as typed', render_text('![x](https://evil.example/pic.jpg)'), '<p>![x](https://evil.example/pic.jpg)</p>');
same('a path climbing out of images is refused', render_text('![x](images/../config/config.local.php)'), '<p>![x](images/../config/config.local.php)</p>');
same('a non-picture file is refused', render_text('![x](images/uploads/evil.php)'), '<p>![x](images/uploads/evil.php)</p>');
check('a caption cannot inject markup', !str_contains(render_text('![<img onerror=alert(1)>](images/setup.jpeg)'), '<img onerror') && str_contains(render_text('![<img onerror=alert(1)>](images/setup.jpeg)'), 'alt="&lt;img onerror=alert(1)&gt;"'), render_text('![<img onerror=alert(1)>](images/setup.jpeg)'));
same('spaces in a file name are kept safe', post_image_path('images/2025meeting/team 2.jpg'), 'images/2025meeting/team 2.jpg');
same('a backslash path is refused', post_image_path('images\evil.jpg'), null);
same('an image line inside a paragraph stays text', render_text('See ![x](images/setup.jpeg) here'), '<p>See ![x](images/setup.jpeg) here</p>');

// ---- A smaller picture with the text beside it --------------------------------------------------
$beside = render_text("![](images/setup.jpeg)
**Galaxy S27**
Standard flagship model.");
check('text on the next line runs beside the picture', str_starts_with($beside, '<div class="prose-beside">') && str_contains($beside, 'prose-beside__picture') && str_contains($beside, '<div class="prose-beside__text"><p><strong>Galaxy S27</strong><br>Standard flagship model.</p></div>'), $beside);
check('a blank line after the picture keeps it full width', str_starts_with(render_text("![](images/setup.jpeg)

A paragraph."), '<figure class="prose-figure">'), render_text("![](images/setup.jpeg)

A paragraph."));
check('two of them follow each other', substr_count(render_text("![](images/setup.jpeg)
One.

![](images/setup.jpeg)
Two."), '<div class="prose-beside">') === 2, render_text("![](images/setup.jpeg)
One.

![](images/setup.jpeg)
Two."));
check('a heading under a picture stays a heading', str_contains(render_text("![](images/setup.jpeg)
## A heading"), '<h2>A heading</h2>') && !str_contains(render_text("![](images/setup.jpeg)
## A heading"), 'prose-beside'), render_text("![](images/setup.jpeg)
## A heading"));
check('a bullet under a picture stays a bullet', str_contains(render_text("![](images/setup.jpeg)
- An item"), '<ul><li>An item</li></ul>'), render_text("![](images/setup.jpeg)
- An item"));
check('two pictures in a row do not merge', substr_count(render_text("![](images/setup.jpeg)
![](images/setup.jpeg)"), '<figure class="prose-figure">') === 2, render_text("![](images/setup.jpeg)
![](images/setup.jpeg)"));
check('links and bold work in the text beside', str_contains(render_text("![](images/setup.jpeg)
See [our pricing](/pricing.html)."), '<a href="/pricing.html">our pricing</a>'), render_text("![](images/setup.jpeg)
See [our pricing](/pricing.html)."));
check('a picture that is not ours keeps its text', str_contains(render_text("![](https://evil.example/x.jpg)
Some words."), 'Some words.'), render_text("![](https://evil.example/x.jpg)
Some words."));
same('the picture beside text carries its real size', (bool) preg_match('/prose-beside__picture"><img [^>]*width="\d+" height="\d+"/', render_text("![](images/setup.jpeg)
Words.")), true);

echo PHP_EOL . ($failures ? "$failures FAILED, " : '') . "$passes passed" . PHP_EOL;
exit($failures ? 1 : 0);
