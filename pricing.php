<?php
require __DIR__ . '/lib/app.php';

page_start([
    'title' => 'Pricing for Schools – HLTS Limited',
    'description' => 'HLTS packages for schools, per term: Basic, Professional and Premium technology education, priced per pupil or per teacher. See starting prices and get your exact quote.',
    'form' => 'pricing',
]);

$packages = school_packages();
// The quote form starts on per-pupil pricing unless a link chose another way to pay.
$_GET['basis'] = in_array($_GET['basis'] ?? '', ['pupils', 'fulltime', 'parttime'], true) ? $_GET['basis'] : 'pupils';
$fullTimeFrom = min(array_map('min', full_time_prices()));
$partTimeFrom = min(part_time_prices());
$quoteUrl = fn (array $q) => page_url('pricing', $q) . '#quote';
$tick = fn ($v) => $v === true
    ? '<span class="compare__yes">' . icon('check-lg') . '<span class="visually-hidden">Included</span></span>'
    : ($v === false ? '<span class="compare__no" aria-label="Not included">–</span>' : '<span class="compare__level">' . h($v) . '</span>');

$peek = '<div class="price-peek" aria-hidden="true">';
foreach ($packages as $key => $p) {
    $peek .= '<div class="price-peek__row' . (!empty($p['featured']) ? ' is-featured' : '') . '"><span>' . h($p['name']) . '</span><strong>' . h(naira($p['minimum'])) . '</strong><small>from, per term</small></div>';
}
$peek .= '</div>';

echo page_hero([
    'crumbs' => [['For Schools', page_url('services')], ['Pricing']],
    'eyebrow' => 'Pricing',
    'title' => 'Clear prices, <span class="grad-text">per term.</span>',
    'lead' => 'Three packages, priced by the number of pupils or teachers your school needs. Add-ons are always quoted separately, so you only pay for what you use.',
    'actions' => button('Get your exact price', '#quote', 'primary', 'calculator') . button('Compare packages', '#compare', 'ghost-light', 'arrow-down'),
    'visual' => $peek,
]);
?>

<section class="section" id="packages">
  <div class="container">
    <?= section_head('Packages', 'Start where your school is today.', 'Every package includes a technology teacher, curriculum, lesson notes, assessment, supervision and a termly report.') ?>
    <div class="packages" data-reveal-group>
<?php foreach ($packages as $key => $p): $featured = !empty($p['featured']); ?>
      <article class="package<?= $featured ? ' package--featured' : '' ?>" data-reveal>
<?php if ($featured): ?>
        <span class="package__badge">Recommended</span>
<?php endif; ?>
        <h3><?= h($p['name']) ?></h3>
        <p class="package__for"><?= h($p['for']) ?></p>
        <p class="package__price"><small>From</small> <strong><?= h(naira($p['minimum'])) ?></strong> <small>per term</small></p>
        <ul class="check-list">
<?php foreach ($p['items'] as $item): ?>
          <li><?= h($item) ?></li>
<?php endforeach; ?>
        </ul>
        <?= button('Get my price', $quoteUrl(['basis' => 'pupils', 'package' => $key]), $featured ? 'light' : 'secondary', 'arrow-right') ?>
      </article>
<?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--alt">
  <div class="container">
    <?= section_head('How you pay', 'Three ways to pay, one clear price.', 'Choose whichever suits your school. Your quote shows exactly how it is worked out.') ?>
    <div class="grid grid--3" data-reveal-group>
      <a class="card-hl" href="<?= h($quoteUrl(['basis' => 'pupils'])) ?>" data-reveal>
        <span class="icon-tile"><?= icon('people') ?></span>
        <h3>Per pupil</h3>
        <p>A base fee for your school plus a fee for each participating pupil, with a minimum per term.</p>
        <span class="card-hl__foot text-link">From <?= h(naira(min(array_column($packages, 'minimum')))) ?> per term <?= icon('arrow-right') ?></span>
      </a>
      <a class="card-hl" href="<?= h($quoteUrl(['basis' => 'fulltime'])) ?>" data-reveal>
        <span class="icon-tile"><?= icon('person-workspace') ?></span>
        <h3>Full-time teachers</h3>
        <p>Pay by the number of teachers we place in your school, Monday to Friday.</p>
        <span class="card-hl__foot text-link">From <?= h(naira($fullTimeFrom)) ?> per term <?= icon('arrow-right') ?></span>
      </a>
      <a class="card-hl" href="<?= h($quoteUrl(['basis' => 'parttime'])) ?>" data-reveal>
        <span class="icon-tile"><?= icon('calendar-week') ?></span>
        <h3>Part-time teachers</h3>
        <p>For schools that don’t need a teacher every day: 2 days a week, on a set timetable.</p>
        <span class="card-hl__foot text-link">From <?= h(naira($partTimeFrom)) ?> per term <?= icon('arrow-right') ?></span>
      </a>
    </div>
  </div>
</section>

<section class="section" id="compare">
  <div class="container container--narrow">
    <?= section_head('Compare', 'What each package includes.') ?>
    <div class="compare" data-reveal>
      <table class="compare__table">
        <caption class="visually-hidden">Services included in the Basic, Professional and Premium packages</caption>
        <thead>
          <tr>
            <th scope="col">Service</th>
<?php foreach ($packages as $p): ?>
            <th scope="col"<?= !empty($p['featured']) ? ' class="is-featured"' : '' ?>><?= h($p['name']) ?></th>
<?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
<?php foreach (package_comparison() as [$service, $basic, $professional, $premium]): ?>
          <tr>
            <th scope="row"><?= h($service) ?></th>
            <td><?= $tick($basic) ?></td>
            <td class="is-featured"><?= $tick($professional) ?></td>
            <td><?= $tick($premium) ?></td>
          </tr>
<?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<section class="section section--alt" id="add-ons">
  <div class="container">
    <?= section_head('Add-ons', 'Extras, when you need them.', 'Add-ons are never included automatically. Add any of them to a package, or take them on their own.') ?>
    <ul class="add-ons" data-reveal-group>
<?php foreach (pricing_add_ons() as $key => $a): ?>
      <li data-reveal><span><?= h($a['name']) ?></span><strong><?= $a['from'] === null ? 'Ask us' : 'From ' . h(naira($a['from'])) . ' <small>' . h($a['unit']) . '</small>' ?></strong></li>
<?php endforeach; ?>
    </ul>
    <p class="text-center mt-4" data-reveal><?= button('Add them to your quote', '#quote', 'secondary', 'plus-lg') ?></p>
  </div>
</section>

<section class="section" id="quote">
  <div class="container">
    <div class="form-page">
      <?= form_aside('Your exact price', 'Get your quote in two minutes.', 'Tell us about your school and we will show your price for the term straight away, and email you a copy.', [
          ['calculator', 'Worked out for your school', 'Based on your pupils or teachers and the package you choose.'],
          ['envelope-check', 'Sent to your email', 'A written copy you can share with your board.'],
          ['telephone-inbound', 'A call within a day', 'To answer questions and plan your start date. No obligation.'],
      ]) ?>
      <div class="form-shell" data-reveal="zoom">
        <div class="form-shell__head"><h2>Get your exact price</h2><p>All prices are per term.</p></div>
<?php $state = form_state('pricing'); if (!empty($state['message'])): ?>
        <div class="notice notice--error mb-3"><?= icon('exclamation-circle') ?><p><?= h($state['message']) ?></p></div>
<?php endif; ?>
        <?= simple_form('pricing', ['basis', 'package', 'pupils', 'teachers', 'add_ons', 'school', 'location', 'name', 'role', 'email', 'phone', 'terms'], 'Get my price') ?>
      </div>
    </div>
  </div>
</section>

<section class="section section--alt">
  <div class="container container--narrow">
    <?= section_head('Questions', 'Pricing questions.') ?>
    <div class="faq-group" data-reveal>
<?php foreach ([
    ['What does “per term” mean?', 'Every price is for one school term. We invoice at the start of each term.'],
    ['Why is there a minimum?', 'Each package has a minimum per term so that small schools still get a dedicated teacher, full curriculum and supervision.'],
    ['Can we mix services?', 'Yes. Start with any package and add extras such as a school website, CBT setup or a technology club whenever you need them.'],
    ['Which package do most schools choose?', 'Professional. It covers technology teaching plus CBT, exams, results and academic reporting, which is where most schools save time.'],
    ['Can we change package later?', 'Yes. You can move up or down at the start of any term.'],
] as [$question, $answer]): ?>
      <details class="faq-item">
        <summary><?= h($question) ?></summary>
        <div class="faq-item__body"><p><?= h($answer) ?></p></div>
      </details>
<?php endforeach; ?>
    </div>
  </div>
</section>

<?= cta_band('Want to see it in your school first?', 'Book a free demo and we will show you how HLTS works in practice.', ['Book a free demo', page_url('book-demo')], ['Register your school', page_url('school-form')]) ?>

<?php page_end(); ?>
