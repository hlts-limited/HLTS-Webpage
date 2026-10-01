<?php
require __DIR__ . '/lib/app.php';

$slug = is_string($_GET['c'] ?? null) ? $_GET['c'] : '';
$course = course($slug);

if (!$course) {
    http_response_code(404);
    render_message_page('Course not found', 'We could not find that course. Browse the full list of courses instead.', false, page_url('course'), 'See all courses');
    exit;
}

page_start([
    'title' => $course['title'] . ' – HLTS Online Institution',
    'description' => $course['summary'],
    'canonical' => 'course-detail.html?c=' . $slug,
    'image' => $course['image'],
]);

$snippets = [
    'frontend' => "<section class=\"hero\">\n  <h1>Hello, Lagos!</h1>\n  <button id=\"go\">Start</button>\n</section>\n\ndocument.querySelector('#go')\n  .addEventListener('click', () => {\n    alert('You built this.');\n  });",
    'backend' => "app.get('/api/students', async (req, res) => {\n  const students = await db.query(\n    'SELECT name, class FROM students'\n  );\n  res.json(students);\n});",
    'fullstack' => "// Front end asks…\nconst res = await fetch('/api/results');\nconst results = await res.json();\n\n// …back end answers\nroute('/api/results', () => db.results.all());",
    'data-analysis' => "=AVERAGEIF(B2:B200, \"JSS2\", D2:D200)\n\nscores.groupby('class')['total']\n      .mean()\n      .plot(kind='bar')",
    'visual-programming' => "when green flag clicked\nrepeat 10\n  move 10 steps\n  turn 36 degrees\nend\nsay \"I made a shape!\"",
];
$snippet = $snippets[$slug] ?? null;
$visual = $snippet
    ? '<div class="code-card" data-tilt><div class="code-card__bar" aria-hidden="true"><i></i><i></i><i></i></div><pre data-typing aria-label="Example of what you will write in this course">' . h($snippet) . '</pre></div>'
    : photo_frame($course['image'], $course['title'], $course['track'], 'HLTS Online Institution', true);

echo page_hero([
    'crumbs' => [['Online Institution', page_url('online-institution')], ['Courses', page_url('course')], [$course['title']]],
    'eyebrow' => $course['track'],
    'title' => h($course['title']),
    'lead' => $course['summary'],
    'actions' => button('Register for this course', page_url('registration-form', ['course' => $slug]), 'primary', 'person-plus') . button('Ask a question', 'https://wa.me/' . config('whatsapp') . '?text=' . rawurlencode('Hello HLTS, I have a question about ' . $course['title']), 'ghost-light', 'whatsapp'),
    'visual' => $visual,
]);
?>

<section class="section">
  <div class="container split split--wide-left split--top">
    <div>
      <?= section_head('What you will learn', 'Skills you can use straight away.', '', 'left') ?>
      <div class="grid grid--2" data-reveal-group>
<?php foreach ($course['outcomes'] as $i => $outcome): ?>
        <div class="card-hl" data-reveal><span class="icon-tile"><?= h((string) ($i + 1)) ?></span><h3><?= h($outcome) ?></h3></div>
<?php endforeach; ?>
      </div>

      <div class="mt-5">
        <?= section_head('How it works', 'Learn by building.', '', 'left') ?>
        <ol class="path" data-path>
          <span class="path__progress" aria-hidden="true"></span>
          <li class="path__step"><span class="path__dot">1</span><div><h3>Register and get set up</h3><p>Confirm your place, receive your student ID and access the portal.</p></div></li>
          <li class="path__step"><span class="path__dot">2</span><div><h3>Learn with guidance</h3><p>Structured lessons, practice tasks and help from your instructor.</p></div></li>
          <li class="path__step"><span class="path__dot">3</span><div><h3>Build real projects</h3><p>Apply what you learn to projects you can show to schools and employers.</p></div></li>
          <li class="path__step"><span class="path__dot">4</span><div><h3>Earn your certificate</h3><p>A certificate with a unique number anyone can verify online.</p></div></li>
        </ol>
      </div>
    </div>

    <aside class="sticky-card" data-reveal>
      <h2>Fees & payment plans</h2>
<?php if ($course['fees']): ?>
      <p class="small muted mb-0">A session runs for 8 months (two 4-month semesters). Choose how you want to pay:</p>
      <div class="fee-table">
<?php foreach ($course['fees'] as $plan => $kobo): ?>
        <div class="fee-row"><span><?= h(fee_plans()[$plan]) ?><br><small class="muted"><?= h(plan_breakdown($kobo, $plan)) ?></small></span><strong><?= h(instalment_label($kobo, $plan)) ?></strong></div>
<?php endforeach; ?>
      </div>
<?php else: ?>
      <p>Fees for this course are confirmed when you register. Ask us for current fees and start dates.</p>
<?php endif; ?>
      <ul class="check-list small mb-4">
        <li>Online or in person in Lagos</li>
        <li>Pay online by card, transfer or USSD<?= paystack_enabled() ? '' : ' (coming soon)' ?></li>
        <li>Certificate on completion</li>
      </ul>
      <?= button('Register now', page_url('registration-form', ['course' => $slug]), 'primary', 'arrow-right', ['class' => 'btn-hl--block']) ?>
    </aside>
  </div>
</section>

<section class="section section--alt">
  <div class="container">
    <?= section_head('Keep exploring', 'Other courses you might like.') ?>
    <div class="grid grid--3" data-reveal-group>
<?php $others = array_filter(courses(), fn ($c, $s) => $s !== $slug && $c['track'] === $course['track'], ARRAY_FILTER_USE_BOTH);
      if (count($others) < 3) { $others += array_filter(courses(), fn ($c, $s) => $s !== $slug, ARRAY_FILTER_USE_BOTH); }
      foreach (array_slice($others, 0, 3, true) as $otherSlug => $other): ?>
      <?= course_card($otherSlug, $other) ?>
<?php endforeach; ?>
    </div>
  </div>
</section>

<?php page_end(); ?>
