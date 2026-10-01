<?php
/** One event card. Expects $event (a row from the events table). */
$start = strtotime($event['starts_at']);
?>
      <a class="event-card" href="<?= h(page_url('events', ['e' => $event['slug']])) ?>#event-<?= h($event['slug']) ?>" data-reveal>
        <span class="event-card__date"><strong><?= date('j', $start) ?></strong><span><?= date('M', $start) ?></span></span>
        <span class="event-card__body">
          <span class="chip chip--line"><?= icon($event['mode'] === 'online' ? 'camera-video' : 'geo-alt') ?> <?= h($event['mode'] === 'online' ? 'Online' : $event['location']) ?></span>
          <strong><?= h($event['title']) ?></strong>
          <small><?= date('l, g:ia', $start) ?></small>
        </span>
        <?= icon('arrow-up-right', 'event-card__arrow') ?>
      </a>
