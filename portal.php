<?php
require __DIR__ . '/lib/app.php';

if (auth_user('student')) {
    redirect('/student.php');
}

$error = '';
$identifier = '';

if (is_post()) {
    csrf_require();
    $identifier = trim((string) ($_POST['identifier'] ?? ''));
    try {
        if (auth_attempt('student', $identifier, (string) ($_POST['password'] ?? ''))) {
            redirect('/student.php');
        }
        $error = 'That student ID or email and password do not match. Check and try again.';
    } catch (RuntimeException $e) {
        $error = $e->getMessage();
    }
}

page_start([
    'title' => 'Student Portal Sign In – HLTS Online Institution',
    'description' => 'Sign in to the HLTS student portal to access your course materials, payments and certificate.',
]);

echo page_hero([
    'crumbs' => [['Online Institution', page_url('online-institution')], ['Student portal']],
    'eyebrow' => 'Student portal',
    'title' => 'Welcome back, <span class="grad-text">learner.</span>',
    'lead' => 'Sign in to see your course materials, payments and certificate.',
]);
?>

<section class="section" id="form">
  <div class="container">
    <div class="form-page">
      <?= form_aside('Need access?', 'New to the portal?', 'Your student ID and first password are sent when your registration is confirmed.', [
          ['person-plus', 'Not registered yet?', 'Register for a course and we will set up your account.'],
          ['key', 'Forgot your password?', 'Message us on WhatsApp from your registered number and we will reset it.'],
          ['shield-lock', 'Keep it private', 'Never share your password, even with classmates.'],
      ]) ?>
      <div class="form-shell" data-reveal="zoom">
        <div class="form-shell__head"><h2>Sign in</h2><p>Use your student ID (e.g. HLTS/2026/0001) or email.</p></div>
<?php if ($error): ?>
        <div class="notice notice--error mb-3" role="alert"><?= icon('exclamation-circle') ?><p><?= h($error) ?></p></div>
<?php endif; ?>
        <form method="post" action="/portal.php" class="form-grid" style="grid-template-columns: 1fr">
          <?= csrf_field() ?>
          <div class="field">
            <label class="field-label" for="identifier">Student ID or email</label>
            <div class="input-wrap"><input id="identifier" name="identifier" value="<?= h($identifier) ?>" autocomplete="username" required autofocus></div>
          </div>
          <div class="field">
            <label class="field-label" for="password">Password</label>
            <div class="input-wrap"><input id="password" type="password" name="password" autocomplete="current-password" required></div>
          </div>
          <p class="small muted">By signing in you agree to the <a href="/privacy.html" target="_blank" rel="noopener">Privacy Policy</a> and <a href="/terms.html" target="_blank" rel="noopener">Terms of Service</a>.</p>
          <div class="form-nav">
            <a class="text-link" href="<?= h(page_url('registration-form')) ?>">Register for a course <?= icon('arrow-right') ?></a>
            <button type="submit" class="btn-hl btn-hl--primary btn-hl--lg"><span>Sign in</span> <?= icon('box-arrow-in-right') ?></button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<?php page_end(); ?>
