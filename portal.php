<?php
$pageTitle = 'Student Portal (Launching Soon) - HLTS Online School';
$pageDescription = 'The HLTS Online Institution student portal is launching soon. Enrolled students can contact HLTS for course materials and updates.';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/nav.php';
?>

  <!-- Portal Hero Section -->
  <section class="portal-hero">
    <div class="container-custom">
      <div class="row align-items-center">
        <div class="col-lg-6" data-aos="fade-right">
          <div class="portal-hero-copy">
            <span class="eyebrow">HLTS Online Institution</span>
            <h1>The student portal is launching soon.</h1>
            <p class="lead">We are building a portal where you will access your courses, track your progress, and stay connected to your instructors. Until it opens, our team supports enrolled students directly.</p>
          </div>
        </div>
        <div class="col-lg-6" data-aos="fade-left">
          <!-- Launching soon card. The previous login accepted any credentials, so it is
               disabled until the portal has real accounts. -->
          <div class="portal-login-card">
            <div class="card-header">
              <h3><i class="bi bi-hourglass-split"></i> Portal launching soon</h3>
              <p>Already enrolled? Contact us for your course materials and updates.</p>
            </div>
            <div class="portal-form d-grid gap-3">
              <a href="https://wa.me/2348107005789" class="btn btn-primary w-100">
                <i class="bi bi-whatsapp"></i> Chat with us on WhatsApp
              </a>
              <a href="mailto:info@hltsltd.com" class="btn btn-outline-primary w-100">
                <i class="bi bi-envelope"></i> Email info@hltsltd.com
              </a>
            </div>
            <div class="portal-footer">
              <p>Not enrolled yet? <a href="registration-form.html">Register for a course</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Portal Features Section -->
  <section class="portal-features section-padding">
    <div class="container-custom">
      <div class="section-title" data-aos="fade-up">
        <h2>What's Coming in the HLTS Portal</h2>
        <p>Tools and resources we are building for your learning journey</p>
      </div>
      
      <div class="row g-4 mt-5">
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
          <div class="portal-feature-card">
            <div class="feature-icon">
              <i class="bi bi-speedometer2"></i>
            </div>
            <h4>Personal Dashboard</h4>
            <p>Track your progress, assignments, and grades in one centralized location.</p>
          </div>
        </div>
        
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
          <div class="portal-feature-card">
            <div class="feature-icon">
              <i class="bi bi-journal-text"></i>
            </div>
            <h4>Course Materials</h4>
            <p>Access all your course content, videos, and resources anytime, anywhere.</p>
          </div>
        </div>
        
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
          <div class="portal-feature-card">
            <div class="feature-icon">
              <i class="bi bi-pencil-square"></i>
            </div>
            <h4>Online Assessments</h4>
            <p>Take quizzes and exams with instant feedback and detailed analytics.</p>
          </div>
        </div>
        
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
          <div class="portal-feature-card">
            <div class="feature-icon">
              <i class="bi bi-chat-dots"></i>
            </div>
            <h4>Live Communication</h4>
            <p>Connect with instructors and peers through integrated messaging.</p>
          </div>
        </div>
        
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
          <div class="portal-feature-card">
            <div class="feature-icon">
              <i class="bi bi-calendar-event"></i>
            </div>
            <h4>Smart Calendar</h4>
            <p>Stay organized with automated reminders for classes and deadlines.</p>
          </div>
        </div>
        
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
          <div class="portal-feature-card">
            <div class="feature-icon">
              <i class="bi bi-graph-up"></i>
            </div>
            <h4>Performance Analytics</h4>
            <p>Visualize your learning progress with detailed reports and insights.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Portal CTA Section -->
  <section class="portal-cta">
    <div class="container-custom">
      <div class="row align-items-center">
        <div class="col-lg-8" data-aos="fade-right">
          <h2>Ready to Start Your Learning Journey?</h2>
          <p>Join thousands of students already succeeding with HLTS Online School</p>
        </div>
        <div class="col-lg-4 text-lg-end" data-aos="fade-left">
          <a href="registration-form.html" class="btn btn-primary btn-lg">
            <i class="bi bi-person-plus"></i> Register Now
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->

<?php include __DIR__ . '/partials/footer.php'; ?>
