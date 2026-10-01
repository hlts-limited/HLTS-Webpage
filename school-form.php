<?php
$pageTitle = 'School Registration - HLTS Limited';
$pageDescription = 'Register your school with HLTS@School for staff deployment, CBT, result management, IT support and school technology services.';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/nav.php';
?>

    <!-- Registration Hero Section -->
    <section class="portal-hero">
      <div class="container-custom">
        <div class="text-center" data-aos="fade-up">
          <h1 class="display-4 fw-bold">School Registration</h1>
          <p class="lead">Join HLTS@School program and start your journey towards the reformation of
            your school in education and technology.</p>
        </div>
      </div>
    </section>

    <!-- Registration Form Section -->
    <section class="py-5 px-4" style="background: var(--gray-50); min-height: 100vh; display: flex; align-items: center;">
      <div class="container">
        <div class="row g-5 align-items-center">
          
          <!-- Image Column (Hidden on mobile, shown on tablet and up) -->
          <div class="col-lg-5 d-none d-lg-block" data-aos="fade-right">
            <div class="position-relative">
              <img loading="lazy" 
                src="images/achildcoding.jpeg" 
                alt="Young student in yellow hoodie focused on learning to code on laptop" 
                class="img-fluid rounded-4 shadow-lg"
                style="border-radius: var(--radius-2xl); object-fit: cover; width: 100%; max-height: 600px;"
              >
              <div class="position-absolute bottom-0 start-0 p-4 text-white" style="background: linear-gradient(to top, rgba(0,32,96,0.9), transparent); width: 100%; border-radius: 0 0 var(--radius-2xl) var(--radius-2xl);">
                <h4 class="fw-bold mb-2">Empower Your School</h4>
                <p class="mb-0 small">Join our team of School leaders transforming their school 
                  with HLTS@School program</p>
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
            <div class="portal-login-card" data-aos="fade-up" data-aos-delay="100">
              <div class="card-header">
                <h3><i class="bi bi-building"></i> Register Your School</h3>
                <p class="mb-0">Tell us about your school and the service you need</p>
              </div>

              <form id="registration-form" action="send-registration.php" method="POST" class="portal-form">
                <input type="hidden" name="form_type" value="school">

                <!-- Personal Information Section -->
                <div class="mb-4">
                  <h6 class="text-primary fw-semibold mb-3 pb-2 border-bottom">
                    <i class="bi bi-person-badge me-2"></i>School Information
                  </h6>
                  
                  <div class="row g-3">
                    <!-- Full Name -->
                    <div class="col-12">
                      <label for="fullName" class="form-label">
                        <i class="bi bi-person-circle"></i> Name of School <span class="text-danger">*</span>
                      </label>
                      <input type="text" id="fullName" name="fullName" class="form-control" placeholder="Enter your school's name" required>
                      <small class="form-text text-muted">Please enter your school's name as it appears on official documents</small>
                    </div>

                    <!-- Email and Phone in two columns -->
                    <div class="col-md-6">
                      <label for="email" class="form-label">
                        <i class="bi bi-envelope"></i> Official Email Address <span class="text-danger">*</span>
                      </label>
                      <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" required>
                      <small class="form-text text-muted">We'll send confirmation here</small>
                    </div>

                    <div class="col-md-6">
                      <label for="phone" class="form-label">
                        <i class="bi bi-telephone"></i> Official Phone Number <span class="text-danger">*</span>
                      </label>
                      <input type="tel" id="phone" name="phone" class="form-control" placeholder="+234 8XX XXX XXXX" required>
                      <small class="form-text text-muted">For important updates</small>
                    </div>
                  </div>
                </div>

                <!-- Program Selection Section -->
                <div class="mb-4">
                  <h6 class="text-primary fw-semibold mb-3 pb-2 border-bottom">
                    <i class="bi bi-mortarboard me-2"></i>Service Selection
                  </h6>
                  
                  <div class="form-group">
                    <label for="program" class="form-label">
                      <i class="bi bi-book"></i> Choose Your Service <span class="text-danger">*</span>
                    </label>
                    <select id="program" name="program" class="form-select" required>
                      <option value="" selected disabled>Select a service to begin your journey...</option>
                      <optgroup label="Management">
                        <option value="ict-facilitator">ICT Facilitator Management</option>
                        <option value="cbt-management">CBT Exam Management</option>
                        <option value="result-management">Result Management</option>
                        <option value="cloud">Cloud Storage Management</option>
                        <option value="lab-management">Lab Management</option>
                      </optgroup>
                      <optgroup label="Developments">
                        <option value="curriculum">Curriculum Development</option>
                        <option value="web">Web Development</option>
                      </optgroup>
                      <optgroup label="Design & Multimedia">
                        <option value="graphic-design">Graphic Design</option>
                        <option value="video-editing">Video Editing</option>
                        <option value="desktop-publishing">Desktop Publishing</option>
                      </optgroup>
                      <optgroup label="Set-up & Integration">
                        <option value="result-setup">Result Integration/Setup</option>
                        <option value="lab-setup">Lab Setup</option>
                        <option value="cbt-setup">CBT Exam Setup</option>
                      </optgroup>
                    </select>
                    <small class="form-text text-muted">Not sure? Our advisors can help you choose the right service</small>
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
                    <textarea class="form-control" id="message" name="message" rows="4" placeholder="Share your goals, experience, or any questions you have..."></textarea>
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
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->

<?php include __DIR__ . '/partials/footer.php'; ?>
