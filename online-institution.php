<?php
$pageTitle = 'HLTS Online Institution';
$pageDescription = 'Learn about HLTS Online Institution, our practical technology training and how to register.';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/nav.php';
?>

  <main class="institution-page">
    <section class="institution-hero">
      <div class="container">
        <div class="institution-hero-copy">
          <span class="eyebrow">HLTS Online Institution</span>
          <h1>Practical skills for the future you are building.</h1>
          <p>Learn technology with structure, support, and a clear path from beginner to confident builder.</p>
          <div class="institution-actions">
            <a href="registration-form.html" class="btn btn-primary">Register to learn <i class="bi bi-arrow-up-right"></i></a>
            <a href="course.html" class="institution-text-link">View available courses <i class="bi bi-arrow-right"></i></a>
          </div>
        </div>
        <div class="institution-hero-visual">
          <img src="images/achildcoding.jpeg" alt="A student learning with a laptop" loading="eager">
          <div class="institution-visual-badge"><i class="bi bi-stars"></i><span>Learn by building</span></div>
          <div class="institution-visual-caption"><span>HLTS Online Institution</span><strong>Skills that move with you.</strong></div>
          <div class="institution-hero-card">
            <span class="institution-card-label">Your learning journey</span>
            <div class="institution-journey-line"><span>01</span><strong>Choose a course</strong></div>
            <div class="institution-journey-line"><span>02</span><strong>Complete registration</strong></div>
            <div class="institution-journey-line"><span>03</span><strong>Start learning with HLTS</strong></div>
          </div>
        </div>
      </div>
    </section>

    <section class="institution-overview">
      <div class="container">
        <div class="institution-section-heading">
          <span class="eyebrow">Why learn with us</span>
          <h2>Learning that leads somewhere.</h2>
        </div>
        <div class="row g-4">
          <div class="col-lg-4"><article class="institution-feature"><i class="bi bi-compass"></i><h3>Clear direction</h3><p>Explore practical courses designed around skills you can use in school, work, and real projects.</p></article></div>
          <div class="col-lg-4"><article class="institution-feature"><i class="bi bi-person-workspace"></i><h3>Guided support</h3><p>Learn with structured instruction, expert guidance, and a community that keeps you moving.</p></article></div>
          <div class="col-lg-4"><article class="institution-feature"><i class="bi bi-graph-up-arrow"></i><h3>Visible progress</h3><p>Use your learner portal to follow courses, assignments, assessments, and your progress.</p></article></div>
        </div>
      </div>
    </section>

    <section class="institution-register" id="how-to-register">
      <div class="container">
        <div class="institution-register-panel">
          <div>
            <span class="eyebrow">How to register</span>
            <h2>Start in three simple steps.</h2>
            <p>Registration takes you from choosing a course to joining the HLTS learning community.</p>
          </div>
          <ol class="institution-steps">
            <li><span>01</span><div><strong>Choose your course</strong><small>Review the current courses and select the path that fits your goals.</small></div></li>
            <li><span>02</span><div><strong>Complete the registration form</strong><small>Share your details and preferred learning programme.</small></div></li>
            <li><span>03</span><div><strong>Receive your next steps</strong><small>Our team will confirm your registration and guide you through onboarding.</small></div></li>
          </ol>
          <a href="registration-form.html" class="btn btn-primary">Go to registration form <i class="bi bi-arrow-up-right"></i></a>
        </div>
      </div>
    </section>
  </main>

<?php include __DIR__ . '/partials/footer.php'; ?>
