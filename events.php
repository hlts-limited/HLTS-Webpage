<?php
require __DIR__ . '/lib/app.php';

$upcoming = db_all("SELECT * FROM events WHERE status = 'published' AND starts_at >= ? ORDER BY starts_at", [now()]);
$past = db_all("SELECT * FROM events WHERE status = 'published' AND starts_at < ? ORDER BY starts_at DESC LIMIT 12", [now()]);

page_start([
    'title' => 'Events – TechMind Africa | HLTS',
    'description' => 'Meetups, workshops and hackathons from TechMind Africa and HLTS.',
]);

echo page_hero([
    'crumbs' => [['TechMind Africa', page_url('community')], ['Events']],
    'eyebrow' => 'Events',
    'title' => 'Meetups, workshops <span class="grad-text">and hackathons.</span>',
    'lead' => 'Come and learn, build and meet people who care about technology in Africa.',
    'actions' => button('Join for updates', page_url('join-techmind'), 'primary', 'bell'),
]);

$renderEvent = function (array $event, bool $isPast) {
    $start = strtotime($event['starts_at']);
    ob_start(); ?>
      <article class="event-feature" id="event-<?= h($event['slug']) ?>" data-reveal>
        <div class="event-feature__media"><?php if ($event['cover']): ?><img src="<?= h(img($event['cover'])) ?>" alt="" loading="lazy"><?php endif; ?></div>
        <div class="event-feature__body">
          <span class="chip<?= $isPast ? ' chip--line' : ' chip--magenta' ?>"><?= $isPast ? 'Past event' : 'Upcoming' ?></span>
          <h3 class="mt-3"><?= h($event['title']) ?></h3>
          <div class="event-meta">
            <span><?= icon('calendar3') ?> <?= date('l j F Y', $start) ?></span>
            <span><?= icon('clock') ?> <?= date('g:ia', $start) ?></span>
            <span><?= icon($event['mode'] === 'online' ? 'camera-video' : 'geo-alt') ?> <?= h($event['mode'] === 'online' ? 'Online' : $event['location']) ?></span>
          </div>
          <p><?= h($event['summary']) ?></p>
<?php if ($event['body']): ?>
          <details><summary class="text-link">Details</summary><div class="prose small mt-2"><?= render_text($event['body']) ?></div></details>
<?php endif; ?>
<?php if (!$isPast): ?>
          <div class="actions mt-3">
            <?= $event['register_url'] ? button('Register', $event['register_url'], 'primary', 'arrow-up-right') : button('Register interest', page_url('join-techmind'), 'primary', 'arrow-right') ?>
          </div>
<?php endif; ?>
        </div>
      </article>
<?php return ob_get_clean();
};
?>

<section class="section">
  <div class="container">
    <?= section_head('Coming up', 'Upcoming events', '', 'left') ?>
<?php if ($upcoming): ?>
    <div class="list-stack">
<?php foreach ($upcoming as $event): echo $renderEvent($event, false); endforeach; ?>
    </div>
<?php else: ?>
    <?= empty_state('calendar-plus', 'No events scheduled right now', 'Join TechMind Africa and we will let you know as soon as the next one is announced.', button('Join for updates', page_url('join-techmind'), 'primary', 'arrow-right')) ?>
<?php endif; ?>

<?php if ($past): ?>
    <div class="mt-5">
      <?= section_head('Archive', 'Past events', '', 'left') ?>
      <div class="list-stack">
<?php foreach ($past as $event): echo $renderEvent($event, true); endforeach; ?>
      </div>
    </div>
<?php endif; ?>
  </div>
</section>

<?php page_end(); ?>
