<?php
$pageTitle = 'Student Registration - HLTS Limited';
$pageDescription = 'HLTS Limited - Student Registration. Join our institution and start your learning journey.';
$bodyClass = 'registration-page';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/nav.php';
?>

    <!-- Registration Hero Section -->
    <section class="portal-hero registration-hero">
      <div class="container-custom">
        <div class="registration-hero-copy" data-aos="fade-up">
          <span class="eyebrow">HLTS Online Institution</span>
          <h1><span>Your next skill</span><span>starts here.</span></h1>
          <p class="lead">Choose your learning path, share a few details, and let us guide you into the right HLTS programme.</p>
        </div>
      </div>
    </section>

    <!-- Registration Form Section -->
    <section class="registration-workspace py-5 px-4">
      <div class="container">
        <div class="row g-5 align-items-center">
          
          <!-- Image Column (Hidden on mobile, shown on tablet and up) -->
          <div class="col-lg-5 d-none d-lg-block registration-aside" data-aos="fade-right">
            <div class="position-relative">
              <img loading="lazy" 
                src="images/achildcoding.jpeg" 
                alt="Young student in yellow hoodie focused on learning to code on laptop" 
                class="img-fluid rounded-4 shadow-lg"
                style="border-radius: var(--radius-2xl); object-fit: cover; width: 100%; max-height: 600px;"
              >
              <div class="registration-image-caption">
                <span class="eyebrow">A simple beginning</span>
                <h4 class="fw-bold mb-2">Start your tech journey.</h4>
                <p class="mb-0 small">Choose a path, register your interest, and grow with HLTS.</p>
              </div>
            </div>
          </div>

          <!-- Form Column -->
          <div class="col-lg-7">
            <!-- Mobile Image (Shown only on small screens) -->
            <div class="d-lg-none mb-4" data-aos="fade-up">
              <img loading="lazy" 
                src="images/achildcoding.jpeg" 
                alt="Young student in yellow hoodie focused on learning to code on laptop" 
                class="img-fluid rounded-4 shadow"
                style="border-radius: var(--radius-xl); object-fit: cover; width: 100%; max-height: 300px;"
              >
            </div>

            <!-- Registration Card -->
            <div class="portal-login-card registration-card" data-aos="fade-up" data-aos-delay="100">
              <div class="card-header">
                <h3><i class="bi bi-person-plus"></i> Create Your Account</h3>
                <p class="mb-0">Fill in your details to begin your enrollment process</p>
              </div>

              <form id="registration-form" action="send-registration.php" method="POST" class="portal-form">
                <input type="hidden" name="form_type" value="student">

                <!-- Personal Information Section -->
                <div class="mb-4">
                  <h6 class="text-primary fw-semibold mb-3 pb-2 border-bottom">
                    <i class="bi bi-person-badge me-2"></i>Personal Information
                  </h6>
                  
                  <div class="row g-3">
                    <!-- Full Name -->
                    <div class="col-12">
                      <label for="fullName" class="form-label">
                        <i class="bi bi-person-circle"></i> Full Name <span class="text-danger">*</span>
                      </label>
                      <input type="text" id="fullName" name="fullName" class="form-control" placeholder="Enter your full name" required>
                      <small class="form-text text-muted">Please enter your name as it appears on official documents</small>
                    </div>

                    <!-- Email and Phone in two columns -->
                    <div class="col-md-6">
                      <label for="email" class="form-label">
                        <i class="bi bi-envelope"></i> Email Address <span class="text-danger">*</span>
                      </label>
                      <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" required>
                      <small class="form-text text-muted">We'll send confirmation here</small>
                    </div>

                    <div class="col-md-6">
                      <label for="phone" class="form-label">
                        <i class="bi bi-telephone"></i> Phone Number <span class="text-danger">*</span>
                      </label>
                      <input type="tel" id="phone" name="phone" class="form-control" placeholder="+234 8XX XXX XXXX" required>
                      <small class="form-text text-muted">For important updates</small>
                    </div>
                  </div>
                </div>

                <!-- Program Selection Section -->
                <div class="mb-4">
                  <h6 class="text-primary fw-semibold mb-3 pb-2 border-bottom">
                    <i class="bi bi-mortarboard me-2"></i>Program Selection
                  </h6>
                  
                  <div class="form-group">
                    <label for="program" class="form-label">
                      <i class="bi bi-book"></i> Choose Your Program <span class="text-danger">*</span>
                    </label>
                    <select id="program" name="program" class="form-select" required>
                      <option value="" selected disabled>Select a program to begin your journey...</option>
                      <optgroup label="Web Development">
                        <option value="frontend">Front-end Development</option>
                        <option value="backend">Back-end Development</option>
                        <option value="fullstack">Full-Stack Development</option>
                      </optgroup>
                      <optgroup label="Data & Analytics">
                        <option value="data-analysis">Data Analysis</option>
                      </optgroup>
                      <optgroup label="Design & Multimedia">
                        <option value="graphic-design">Graphic Design</option>
                        <option value="video-editing">Video Editing</option>
                        <option value="desktop-publishing">Desktop Publishing</option>
                      </optgroup>
                      <optgroup label="Programming">
                        <option value="visual-programming">Visual Based Programming</option>
                      </optgroup>
                    </select>
                    <small class="form-text text-muted">Not sure? Our advisors can help you choose the right program</small>
                  </div>
                </div>

                <!-- Additional Information Section -->
                <div class="mb-4">
                  <h6 class="text-primary fw-semibold mb-3 pb-2 border-bottom">
                    <i class="bi bi-chat-left-text me-2"></i>Tell Us More (Optional)
                  </h6>
                  
                  <div class="form-group">
                    <label for="message" class="form-label">
                      <i class="bi bi-chat-dots"></i> Your Message
                    </label>
                    <textarea class="form-control" id="message" name="message" rows="4" placeholder="Share your learning goals, experience level, or any questions you have..."></textarea>
                    <small class="form-text text-muted">Help us understand your background and goals better</small>
                  </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="form-check mb-4">
                  <input class="form-check-input" type="checkbox" id="terms" name="terms" value="1" required>
                  <label class="form-check-label small" for="terms">
                    I agree to the <a href="terms.html#terms-of-service" class="text-primary" target="_blank">Terms and Conditions</a> and <a href="terms.html#privacy-policy" class="text-primary" target="_blank">Privacy Policy</a> <span class="text-danger">*</span>
                  </label>
                </div>

                <!-- Submit Button -->
                <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-primary btn-lg py-3">
                    <i class="bi bi-check-circle me-2"></i> Complete Registration
                  </button>
                  <p class="text-center text-muted small mb-0 mt-2">
                    <i class="bi bi-shield-check"></i> Your information is secure and encrypted
                  </p>
                </div>
              </form>

              <!-- Footer -->
              <div class="portal-footer mt-4 pt-3 border-top">
                <p class="text-center mb-0">Already have an account? <a href="portal.html" class="fw-semibold">Sign in here</a></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->

<?php include __DIR__ . '/partials/footer.php'; ?>
