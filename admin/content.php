<?php
/**
 * One editor for every kind of website content: articles, events,
 * portfolio projects, jobs and course materials. Each type is described in
 * content_types() below; the list and form are built from that.
 */

require __DIR__ . '/_admin.php';
require_admin();

function content_types(): array
{
    $status = ['type' => 'select', 'label' => 'Status', 'options' => ['draft' => 'Draft (hidden)', 'published' => 'Published']];
    $cover = ['type' => 'image', 'label' => 'Cover image'];

    return [
        'posts' => [
            'label' => 'Insights', 'single' => 'article', 'nav' => 'posts', 'public' => fn ($r) => page_url('post', ['p' => $r['slug']]),
            'list' => ['title', 'category', 'status', 'published_at'], 'order' => 'created_at DESC',
            'fields' => [
                'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
                'category' => ['type' => 'text', 'label' => 'Category', 'placeholder' => 'Teaching, Assessment, Technology…'],
                'excerpt' => ['type' => 'textarea', 'label' => 'Short summary', 'rows' => 2, 'help' => 'Shown on cards and in search results. One or two sentences.'],
                'body' => ['type' => 'textarea', 'label' => 'Article', 'rows' => 16, 'required' => true, 'help' => 'Leave a blank line between paragraphs. Start a line with "## " for a heading and "- " for a bullet.'],
                'cover' => $cover,
                'status' => $status,
            ],
        ],
        'events' => [
            'label' => 'Events', 'single' => 'event', 'nav' => 'events', 'public' => fn ($r) => page_url('events') . '#event-' . $r['slug'],
            'list' => ['title', 'starts_at', 'location', 'status'], 'order' => 'starts_at DESC',
            'fields' => [
                'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
                'starts_at' => ['type' => 'datetime', 'label' => 'Date and time', 'required' => true],
                'mode' => ['type' => 'select', 'label' => 'Format', 'options' => ['in-person' => 'In person', 'online' => 'Online', 'hybrid' => 'Hybrid']],
                'location' => ['type' => 'text', 'label' => 'Location', 'placeholder' => 'HLTS office, Somolu, Lagos'],
                'summary' => ['type' => 'textarea', 'label' => 'Summary', 'rows' => 2, 'required' => true],
                'body' => ['type' => 'textarea', 'label' => 'Details', 'rows' => 8],
                'register_url' => ['type' => 'url', 'label' => 'Registration link (optional)', 'help' => 'Leave empty to send people to the TechMind join form.'],
                'cover' => $cover,
                'status' => $status,
            ],
        ],
        'projects' => [
            'label' => 'Portfolio', 'single' => 'project', 'nav' => 'projects', 'public' => fn ($r) => page_url('portfolio'),
            'list' => ['title', 'category', 'client', 'status'], 'order' => 'sort_order, created_at DESC',
            'fields' => [
                'title' => ['type' => 'text', 'label' => 'Project name', 'required' => true],
                'client' => ['type' => 'text', 'label' => 'Client', 'placeholder' => 'Secondary school, Lagos'],
                'category' => ['type' => 'text', 'label' => 'Category', 'placeholder' => 'Website, Portal, App, Design'],
                'summary' => ['type' => 'textarea', 'label' => 'Summary', 'rows' => 2, 'required' => true],
                'results' => ['type' => 'text', 'label' => 'Result highlight', 'placeholder' => 'Results published in hours, not weeks'],
                'body' => ['type' => 'textarea', 'label' => 'Story', 'rows' => 8],
                'url' => ['type' => 'url', 'label' => 'Live link (optional)'],
                'cover' => $cover,
                'sort_order' => ['type' => 'number', 'label' => 'Order (lower shows first)'],
                'status' => $status,
            ],
        ],
        'jobs' => [
            'label' => 'Jobs', 'single' => 'job', 'nav' => 'jobs', 'public' => fn ($r) => page_url('careers', ['job' => $r['slug']]),
            'list' => ['title', 'job_type', 'location', 'closes_on', 'status'], 'order' => 'created_at DESC',
            'fields' => [
                'title' => ['type' => 'text', 'label' => 'Role title', 'required' => true],
                'job_type' => ['type' => 'select', 'label' => 'Type', 'options' => ['Full-time' => 'Full-time', 'Part-time' => 'Part-time', 'Contract' => 'Contract', 'Internship' => 'Internship', 'School placement' => 'School placement']],
                'location' => ['type' => 'text', 'label' => 'Location', 'required' => true],
                'summary' => ['type' => 'textarea', 'label' => 'Summary', 'rows' => 2, 'required' => true],
                'body' => ['type' => 'textarea', 'label' => 'Description', 'rows' => 10, 'help' => 'Use "## " for headings and "- " for bullets.'],
                'closes_on' => ['type' => 'date', 'label' => 'Closing date (optional)'],
                'status' => $status,
            ],
        ],
        'materials' => [
            'label' => 'Course materials', 'single' => 'material', 'nav' => 'materials', 'public' => null,
            'list' => ['title', 'course_slug', 'kind', 'status'], 'order' => 'course_slug, sort_order, id',
            'fields' => [
                'course_slug' => ['type' => 'select', 'label' => 'Course', 'options' => course_options(), 'required' => true],
                'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
                'url' => ['type' => 'url', 'label' => 'Link', 'required' => true, 'help' => 'Google Drive, YouTube, Classroom or any https link. Make sure students can open it.'],
                'kind' => ['type' => 'select', 'label' => 'Type', 'options' => ['document' => 'Document', 'video' => 'Video', 'assignment' => 'Assignment', 'link' => 'Link']],
                'sort_order' => ['type' => 'number', 'label' => 'Order'],
                'status' => $status,
            ],
        ],
    ];
}

$types = content_types();
$typeKey = isset($types[$_GET['type'] ?? '']) ? $_GET['type'] : 'posts';
$type = $types[$typeKey];
$table = $typeKey; // Table names match the type keys and never come from input.
$base = '/admin/content.php?type=' . $typeKey;
$hasSlug = $typeKey !== 'materials';

$id = (int) ($_GET['id'] ?? 0);
$action = $_GET['action'] ?? ($id ? 'edit' : 'list');
$row = $id ? db_one("SELECT * FROM $table WHERE id = ?", [$id]) : null;
$errors = [];
$values = $row ?? [];

if (is_post()) {
    csrf_require();

    if (($_POST['action'] ?? '') === 'delete' && $row) {
        db_run("DELETE FROM $table WHERE id = ?", [$row['id']]);
        admin_notice(ucfirst($type['single']) . ' deleted.', $base);
    }

    $values = [];
    foreach ($type['fields'] as $name => $field) {
        $value = trim((string) ($_POST[$name] ?? ''));
        if ($field['type'] === 'datetime' && $value !== '') {
            $value = str_replace('T', ' ', $value) . (strlen($value) === 16 ? ':00' : '');
        }
        if ($field['type'] === 'number') {
            $value = (int) $value;
        }
        if (!empty($field['required']) && $value === '') {
            $errors[$name] = $field['label'] . ' is required.';
        }
        if ($field['type'] === 'url' && $value !== '' && !safe_url($value)) {
            $errors[$name] = 'Enter a full link starting with https://';
        }
        if ($field['type'] === 'select' && $value !== '' && !isset($field['options'][$value])) {
            $errors[$name] = 'Choose one of the options.';
        }
        if ($field['type'] === 'image' && $value !== '' && !isset(image_choices()[$value])) {
            $errors[$name] = 'Choose an image from the list.';
        }
        $values[$name] = $value;
    }

    if (!$errors) {
        if ($hasSlug && !$row) {
            $slug = slugify($values['title']);
            $candidate = $slug;
            $n = 2;
            while (db_value("SELECT COUNT(*) FROM $table WHERE slug = ?", [$candidate])) {
                $candidate = $slug . '-' . $n++;
            }
            $values['slug'] = $candidate;
        }
        if ($typeKey === 'posts' && $values['status'] === 'published' && empty($row['published_at'])) {
            $values['published_at'] = now();
        }
        foreach (['closes_on', 'published_at'] as $nullable) {
            if (array_key_exists($nullable, $values) && $values[$nullable] === '') {
                $values[$nullable] = null;
            }
        }

        if ($row) {
            db_update($table, (int) $row['id'], $values);
            admin_notice(ucfirst($type['single']) . ' saved.', "$base&id=" . $row['id']);
        }
        $values['created_at'] = now();
        $newId = db_insert($table, $values);
        admin_notice(ucfirst($type['single']) . ' created.', "$base&id=$newId");
    }
    $action = $row ? 'edit' : 'new';
}

$listColumns = ['title' => 'Title', 'category' => 'Category', 'status' => 'Status', 'published_at' => 'Published', 'starts_at' => 'Date', 'location' => 'Location', 'client' => 'Client', 'job_type' => 'Type', 'closes_on' => 'Closes', 'course_slug' => 'Course', 'kind' => 'Type'];

admin_start($type['label'], $type['nav']);

if ($action === 'list'):
    $rows = db_all("SELECT * FROM $table ORDER BY {$type['order']}");
?>
<div class="panel">
  <div class="panel__head">
    <h2><?= count($rows) ?> <?= h(strtolower($type['label'])) ?></h2>
    <a class="btn-hl btn-hl--primary" href="<?= h($base) ?>&amp;action=new"><?= icon('plus-lg') ?> <span>New <?= h($type['single']) ?></span></a>
  </div>
<?php if ($rows): ?>
  <div class="table-wrap"><table class="table-hl">
    <thead><tr><?php foreach ($type['list'] as $col): ?><th><?= h($listColumns[$col]) ?></th><?php endforeach; ?></tr></thead>
    <tbody>
<?php foreach ($rows as $r): ?>
      <tr class="row-link" data-href="<?= h($base) ?>&amp;id=<?= (int) $r['id'] ?>">
<?php foreach ($type['list'] as $i => $col): $v = (string) ($r[$col] ?? ''); ?>
        <td><?php
          if ($col === 'status') echo status_chip($v);
          elseif (in_array($col, ['published_at', 'starts_at', 'closes_on'], true)) echo h(format_date($v, $col === 'starts_at' ? 'j M Y, g:ia' : 'j M Y'));
          elseif ($col === 'course_slug') echo h(course($v)['title'] ?? $v);
          elseif ($i === 0) echo '<a href="' . h($base) . '&amp;id=' . (int) $r['id'] . '"><strong>' . h($v) . '</strong></a>' . (str_starts_with((string) ($r['summary'] ?? $r['excerpt'] ?? $r['body'] ?? ''), 'EXAMPLE') ? ' <span class="chip chip--magenta">Example</span>' : '');
          else echo h($v);
        ?></td>
<?php endforeach; ?>
      </tr>
<?php endforeach; ?>
    </tbody>
  </table></div>
<?php else: ?>
  <?= empty_state('plus-circle', 'Nothing here yet', 'Create the first ' . $type['single'] . '.') ?>
<?php endif; ?>
</div>

<?php else: ?>
<a class="text-link mb-3 d-inline-flex" href="<?= h($base) ?>"><?= icon('arrow-left') ?> All <?= h(strtolower($type['label'])) ?></a>
<form method="post" class="admin-grid admin-grid--editor">
  <?= csrf_field() ?>
  <section class="panel admin-form">
<?php if ($errors): ?>
    <div class="notice notice--error" role="alert"><?= icon('exclamation-circle') ?><p>Please fix the highlighted fields.</p></div>
<?php endif; ?>
<?php foreach ($type['fields'] as $name => $field): if (in_array($name, ['status', 'cover', 'sort_order'], true)) continue;
      $value = (string) ($values[$name] ?? ''); $err = $errors[$name] ?? ''; $fid = 'f-' . $name; ?>
    <div class="field">
      <label class="field-label" for="<?= h($fid) ?>"><?= h($field['label']) ?><?= !empty($field['required']) ? ' <span class="req">*</span>' : '' ?></label>
<?php if ($field['type'] === 'textarea'): ?>
      <textarea id="<?= h($fid) ?>" name="<?= h($name) ?>" rows="<?= (int) ($field['rows'] ?? 4) ?>"<?= $err ? ' aria-invalid="true"' : '' ?>><?= h($value) ?></textarea>
<?php elseif ($field['type'] === 'select'): ?>
      <div class="select-wrap"><select id="<?= h($fid) ?>" name="<?= h($name) ?>"><?php foreach ($field['options'] as $k => $label): ?><option value="<?= h($k) ?>"<?= $value === (string) $k ? ' selected' : '' ?>><?= h($label) ?></option><?php endforeach; ?></select><?= icon('chevron-down') ?></div>
<?php else: $inputType = ['datetime' => 'datetime-local', 'date' => 'date', 'url' => 'url', 'number' => 'number'][$field['type']] ?? 'text';
      if ($field['type'] === 'datetime' && $value) $value = str_replace(' ', 'T', substr($value, 0, 16)); ?>
      <input id="<?= h($fid) ?>" type="<?= $inputType ?>" name="<?= h($name) ?>" value="<?= h($value) ?>" placeholder="<?= h($field['placeholder'] ?? '') ?>"<?= $err ? ' aria-invalid="true"' : '' ?>>
<?php endif; ?>
<?php if (!empty($field['help'])): ?><p class="field-help"><?= h($field['help']) ?></p><?php endif; ?>
<?php if ($err): ?><p class="field-error"><?= icon('exclamation-circle') ?><span><?= h($err) ?></span></p><?php endif; ?>
    </div>
<?php endforeach; ?>
  </section>

  <aside class="admin-stack">
    <section class="panel admin-form">
<?php foreach (['status', 'sort_order'] as $name): if (!isset($type['fields'][$name])) continue; $field = $type['fields'][$name]; $value = (string) ($values[$name] ?? ''); ?>
      <div class="field">
        <label class="field-label" for="f-<?= h($name) ?>"><?= h($field['label']) ?></label>
<?php if ($field['type'] === 'select'): ?>
        <div class="select-wrap"><select id="f-<?= h($name) ?>" name="<?= h($name) ?>"><?php foreach ($field['options'] as $k => $label): ?><option value="<?= h($k) ?>"<?= $value === (string) $k ? ' selected' : '' ?>><?= h($label) ?></option><?php endforeach; ?></select><?= icon('chevron-down') ?></div>
<?php else: ?>
        <input id="f-<?= h($name) ?>" type="number" name="<?= h($name) ?>" value="<?= h($value) ?>">
<?php endif; ?>
      </div>
<?php endforeach; ?>
      <button class="btn-hl btn-hl--primary btn-hl--block" type="submit"><?= icon('check-lg') ?> <span>Save</span></button>
<?php if ($row && $type['public'] && ($row['status'] ?? '') === 'published'): ?>
      <a class="btn-hl btn-hl--ghost btn-hl--block" href="<?= h(($type['public'])($row)) ?>" target="_blank" rel="noopener"><?= icon('box-arrow-up-right') ?> <span>View on website</span></a>
<?php endif; ?>
    </section>

<?php if (isset($type['fields']['cover'])): $cover = (string) ($values['cover'] ?? ''); ?>
    <section class="panel admin-form">
      <div class="field">
        <label class="field-label" for="f-cover">Cover image</label>
        <div class="select-wrap"><select id="f-cover" name="cover" data-image-preview="#cover-preview"><option value="">No image</option><?php foreach (image_choices() as $path): ?><option value="<?= h($path) ?>"<?= $cover === $path ? ' selected' : '' ?>><?= h(substr($path, 7)) ?></option><?php endforeach; ?></select><?= icon('chevron-down') ?></div>
        <p class="field-help">To use a new image, add it to the website's images folder first.</p>
<?php if (!empty($errors['cover'])): ?><p class="field-error"><?= icon('exclamation-circle') ?><span><?= h($errors['cover']) ?></span></p><?php endif; ?>
      </div>
      <img id="cover-preview" class="cover-preview" src="<?= $cover ? h(img($cover)) : '' ?>" alt=""<?= $cover ? '' : ' hidden' ?>>
    </section>
<?php endif; ?>
  </aside>
</form>

<?php if ($row): ?>
<form method="post" class="mt-4" data-confirm="Delete this <?= h($type['single']) ?> permanently?">
  <?= csrf_field() ?><input type="hidden" name="action" value="delete">
  <button class="btn-hl btn-hl--ghost btn-hl--sm" type="submit"><?= icon('trash') ?> <span>Delete <?= h($type['single']) ?></span></button>
</form>
<?php endif; ?>
<?php endif; ?>
<?php admin_end(); ?>
