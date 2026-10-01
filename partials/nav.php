<?php
/**
 * Site header: desktop mega menu, mobile off-canvas menu and phone bottom bar.
 * Menu items come from nav_groups() in lib/content.php.
 */

$current = current_page();
$groups = nav_groups();
$isIn = fn (array $group) => in_array($current, $group['match'], true);
?>
    <header class="site-header<?= !empty($GLOBALS['page']['solid_header']) ? ' is-solid' : '' ?>" data-header>
      <div class="container">
        <div class="site-header__bar">
          <a class="brand" href="/" aria-label="HLTS Limited home">
            <img src="/images/brand/logo-192.png" alt="" width="46" height="46">
            <span class="brand__text"><span class="brand__name">HLTS</span><span class="brand__tag">TECHNOLOGY</span></span>
          </a>

          <nav class="main-nav" aria-label="Main">
            <ul class="main-nav__list">
<?php foreach ($groups as $key => $group): ?>
              <li class="main-nav__item dropdown" data-hover-dropdown>
                <button class="main-nav__link<?= $isIn($group) ? ' is-active' : '' ?>" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" id="nav-<?= h($key) ?>">
                  <?= h($group['label']) ?> <?= icon('chevron-down') ?>
                </button>
                <div class="mega dropdown-menu" aria-labelledby="nav-<?= h($key) ?>">
<?php foreach ($group['items'] as [$page, $label, $hint, $iconName]): ?>
                  <a class="mega__item<?= $current === $page ? ' is-active' : '' ?>" href="<?= h(page_url($page)) ?>"<?= link_target($page) ?><?= $current === $page ? ' aria-current="page"' : '' ?>>
                    <span class="mega__icon"><?= icon($iconName) ?></span>
                    <span><strong><?= h($label) ?></strong><small><?= h($hint) ?></small></span>
                  </a>
<?php endforeach; ?>
                </div>
              </li>
<?php endforeach; ?>
            </ul>
          </nav>

          <div class="site-header__actions">
            <?= button('Book a demo', page_url('book-demo'), 'primary', 'calendar-check', ['data-magnetic' => '']) ?>
            <button class="nav-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu" aria-label="Open menu">
              <?= icon('list') ?>
            </button>
          </div>
        </div>
      </div>
    </header>

    <div class="offcanvas offcanvas-end mobile-menu" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
      <div class="offcanvas-header">
        <a class="brand" href="/"><img src="/images/brand/logo-192.png" alt="" width="40" height="40"><span class="brand__text"><span class="brand__name" id="mobileMenuLabel">HLTS</span><span class="brand__tag">TECHNOLOGY</span></span></a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close menu"></button>
      </div>
      <div class="offcanvas-body">
<?php foreach ($groups as $group): ?>
        <details class="mobile-group"<?= $isIn($group) ? ' open' : '' ?>>
          <summary><?= icon($group['icon']) ?> <?= h($group['label']) ?> <?= icon('chevron-down') ?></summary>
          <ul>
<?php foreach ($group['items'] as [$page, $label, , $iconName]): ?>
            <li><a href="<?= h(page_url($page)) ?>"<?= link_target($page) ?><?= $current === $page ? ' class="is-active" aria-current="page"' : '' ?>><?= icon($iconName) ?> <?= h($label) ?></a></li>
<?php endforeach; ?>
          </ul>
        </details>
<?php endforeach; ?>
        <div class="mobile-menu__actions">
          <?= button('Book a school demo', page_url('book-demo'), 'primary', 'calendar-check') ?>
          <?= button('Chat on WhatsApp', 'https://wa.me/' . config('whatsapp'), 'ghost-light', 'whatsapp') ?>
        </div>
      </div>
    </div>

    <nav class="mobile-bar" aria-label="Quick links" data-mobile-bar>
      <a href="/"<?= $current === 'index' ? ' class="is-active" aria-current="page"' : '' ?>><?= icon('house') ?><span>Home</span></a>
      <a href="<?= h(page_url('services')) ?>"<?= $isIn($groups['schools']) ? ' class="is-active"' : '' ?>><?= icon('building') ?><span>Schools</span></a>
      <a href="<?= h(page_url('course')) ?>"<?= $isIn($groups['learn']) ? ' class="is-active"' : '' ?>><?= icon('mortarboard') ?><span>Courses</span></a>
      <a href="https://wa.me/<?= h(config('whatsapp')) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?><span>WhatsApp</span></a>
      <a class="mobile-bar__cta" href="<?= h(page_url('contact')) ?>"><?= icon('chat-dots') ?><span>Talk to us</span></a>
    </nav>
