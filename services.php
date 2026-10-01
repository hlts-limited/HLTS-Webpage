<?php
$pageTitle = 'Our Services - HLTS Limited';
$pageDescription = 'HLTS Services - Comprehensive EdTech solutions including School Management Systems, CBT Platforms, Online Learning, and Educational Consulting.';
$bodyClass = 'services-page';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/nav.php';
?>

    <!-- Hero Section -->
    <section class="services-hero section-blend-bottom services-hero-bg" style="--section-blend-to: #fff;">
      <div class="container">
        <div class="services-hero-layout">
          <div class="services-hero-copy" data-aos="fade-up">
            <span class="eyebrow">Education, connected</span>
            <h1>Technology that helps education work better.</h1>
            <p>Support for school operations, digital learning and the technology behind better learning.</p>
            <div class="services-hero-actions">
              <a href="#services-grid" class="btn btn-primary">Explore our services <i class="bi bi-arrow-down-right" aria-hidden="true"></i></a>
              <a href="contact.html" class="services-hero-contact">Talk to HLTS <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
            </div>
            <p class="services-hero-note">For schools, educators and learners</p>
          </div>
          <figure class="services-hero-visual" data-aos="fade-up">
            <img src="images/slide3.jpg" alt="Students participating in a technology-supported learning activity" fetchpriority="high">
            <figcaption><span>HLTS education solutions</span><strong>People, platforms and practical support</strong></figcaption>
          </figure>
        </div>
      </div>
    </section>

    <!-- Services Grid Section -->
    <section id="services-grid" class="py-5 bg-light">
      <div class="container py-4">
        <div class="text-center mb-5" data-aos="fade-up">
          <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">Our Services</span>
          <h2 class="fw-bold text-primary">Complete EdTech Ecosystem</h2>
          <p class="text-muted mx-auto" style="max-width: 700px;">We offer a suite of interconnected solutions designed to digitize every aspect of educational management and delivery.</p>
        </div>

        <div class="row g-4">
          <!-- Service 1: HLTS@School -->
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="service-card h-100">
              <div class="service-icon">
                <i class="bi bi-building"></i>
              </div>
              <h4>HLTS@School</h4>
              <p>Complete school management system covering admissions, student records, staff management, 
                fee collection, timetabling, and communication.
              </p>
              <ul class="service-features">
                <li><i class="bi bi-check-circle-fill"></i> Student Information System</li>
                <li><i class="bi bi-check-circle-fill"></i> Automated Fee Management</li>
                <li><i class="bi bi-check-circle-fill"></i> Attendance Tracking</li>
                <li><i class="bi bi-check-circle-fill"></i> Report Card Generation</li>
              </ul>
              <a href="about.html#duties" class="btn btn-outline-primary mt-3">Learn More <i class="bi bi-arrow-right"></i></a>
            </div>
          </div>

          <!-- Service 2: Online Learning -->
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="service-card h-100">
              <div class="service-icon" style="background: linear-gradient(135deg, #00875a, #36b37e);">
                <i class="bi bi-laptop"></i>
              </div>
              <h4>Online Learning Platform</h4>
              <p>Robust LMS enabling virtual classrooms, video lessons, assignments, and interactive learning experiences for students anywhere.</p>
              <ul class="service-features">
                <li><i class="bi bi-check-circle-fill"></i> Live Virtual Classes</li>
                <li><i class="bi bi-check-circle-fill"></i> Video Course Library</li>
                <li><i class="bi bi-check-circle-fill"></i> Assignment Submission</li>
                <li><i class="bi bi-check-circle-fill"></i> Progress Tracking</li>
              </ul>
              <a href="about.html#tech-institution" class="btn btn-outline-primary mt-3">Learn More <i class="bi bi-arrow-right"></i></a>
            </div>
          </div>

          <!-- Service 3: CBT Platform -->
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="service-card h-100">
              <div class="service-icon" style="background: linear-gradient(135deg, #6554c0, #8777d9);">
                <i class="bi bi-ui-checks-grid"></i>
              </div>
              <h4>CBT Examination Platform</h4>
              <p>Secure computer-based testing system for exams, quizzes, and assessments with anti-cheating measures and instant results.</p>
              <ul class="service-features">
                <li><i class="bi bi-check-circle-fill"></i> WAEC & JAMB Practice</li>
                <li><i class="bi bi-check-circle-fill"></i> Auto-Grading System</li>
                <li><i class="bi bi-check-circle-fill"></i> Question Bank Management</li>
                <li><i class="bi bi-check-circle-fill"></i> Performance Analytics</li>
              </ul>
              <a href="cbt.html" class="btn btn-outline-primary mt-3">Learn More <i class="bi bi-arrow-right"></i></a>
            </div>
          </div>

          <!-- Service 4: Consulting -->
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="service-card h-100">
              <div class="service-icon" style="background: linear-gradient(135deg, #ff5630, #ff8b6a);">
                <i class="bi bi-people"></i>
              </div>
              <h4>Educational Consulting</h4>
              <p>Expert guidance on digital transformation, curriculum development, and operational optimization for educational institutions.</p>
              <ul class="service-features">
                <li><i class="bi bi-check-circle-fill"></i> Digital Strategy Planning</li>
                <li><i class="bi bi-check-circle-fill"></i> Staff Training Programs</li>
                <li><i class="bi bi-check-circle-fill"></i> Process Optimization</li>
                <li><i class="bi bi-check-circle-fill"></i> Technology Roadmapping</li>
              </ul>
              <a href="contact.html" class="btn btn-outline-primary mt-3">Learn More <i class="bi bi-arrow-right"></i></a>
            </div>
          </div>

          <!-- Service 5: Analytics -->
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
            <div class="service-card h-100">
              <div class="service-icon" style="background: linear-gradient(135deg, #0065ff, #4c9aff);">
                <i class="bi bi-graph-up-arrow"></i>
              </div>
              <h4>Analytics & Reporting</h4>
              <p>Powerful data visualization and reporting tools to track student performance, identify trends, and make informed decisions.</p>
              <ul class="service-features">
                <li><i class="bi bi-check-circle-fill"></i> Performance Dashboards</li>
                <li><i class="bi bi-check-circle-fill"></i> Trend Analysis</li>
                <li><i class="bi bi-check-circle-fill"></i> Custom Reports</li>
                <li><i class="bi bi-check-circle-fill"></i> Early Warning System</li>
              </ul>
              <a href="contact.html" class="btn btn-outline-primary mt-3">Learn More <i class="bi bi-arrow-right"></i></a>
            </div>
          </div>

          <!-- Service 6: Integration -->
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
            <div class="service-card h-100">
              <div class="service-icon" style="background: linear-gradient(135deg, #00b8d9, #36d8f5);">
                <i class="bi bi-plug"></i>
              </div>
              <h4>System Integration</h4>
              <p>Seamless integration with payment gateways, communication platforms, and third-party educational tools.</p>
              <ul class="service-features">
                <li><i class="bi bi-check-circle-fill"></i> Payment Gateway Integration</li>
                <li><i class="bi bi-check-circle-fill"></i> SMS & Email Notifications</li>
                <li><i class="bi bi-check-circle-fill"></i> Google Workspace Sync</li>
                <li><i class="bi bi-check-circle-fill"></i> API Access</li>
              </ul>
              <a href="cbt.html#system-integration" class="btn btn-outline-primary mt-3">Learn More <i class="bi bi-arrow-right"></i></a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-5 bg-white">
      <div class="container py-4">
        <div class="row align-items-center">
          <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-up">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">Why HLTS?</span>
            <h2 class="fw-bold text-primary mb-4">Why Schools Choose Us?</h2>
            <p class="text-muted mb-4">We don't just provide software – we partner with schools to ensure successful digital transformation. Our commitment to excellence sets us apart.</p>
            
            <div class="why-choose-item">
              <div class="why-icon"><i class="bi bi-shield-check"></i></div>
              <div>
                <h5>Reliable & Secure</h5>
                <p class="text-muted mb-0">99.9% uptime with enterprise-grade security and regular backups.</p>
              </div>
            </div>
            
            <div class="why-choose-item">
              <div class="why-icon"><i class="bi bi-headset"></i></div>
              <div>
                <h5>24/7 Support</h5>
                <p class="text-muted mb-0">Dedicated support team available round the clock via phone, email, and chat.</p>
              </div>
            </div>
            
            <div class="why-choose-item">
              <div class="why-icon"><i class="bi bi-gear"></i></div>
              <div>
                <h5>Customizable</h5>
                <p class="text-muted mb-0">Tailored solutions that adapt to your school's unique requirements.</p>
              </div>
            </div>
            
            <div class="why-choose-item">
              <div class="why-icon"><i class="bi bi-rocket-takeoff"></i></div>
              <div>
                <h5>Quick Deployment</h5>
                <p class="text-muted mb-0">Get up and running in as little as 2 weeks with full training included.</p>
              </div>
            </div>
          </div>
          
          <div class="col-lg-6" data-aos="fade-up">
            <div class="services-stats-grid">
              <div class="stat-box-service">
                <div class="stat-number">4+</div>
                <div class="stat-label">Schools Served</div>
              </div>
              <div class="stat-box-service">
                <div class="stat-number">50+</div>
                <div class="stat-label">Active Students</div>
              </div>
              <div class="stat-box-service">
                <div class="stat-number">10+</div>
                <div class="stat-label">Teachers Using HLTS</div>
              </div>
              <div class="stat-box-service">
                <div class="stat-number">99.9%</div>
                <div class="stat-label">Uptime Guarantee</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <section class="services-pricing-section py-5 bg-light">
      <div class="container py-4">
        <div class="text-center mb-5" data-aos="fade-up">
          <span class="eyebrow">Flexible engagement</span>
          <h2 class="fw-bold text-primary">A plan shaped around your school.</h2>
          <p class="text-muted mx-auto" style="max-width: 600px;">Start with your institution's goals, existing systems and support needs. HLTS can scope setup, training and ongoing support with your team.</p>
        </div>

        <div class="services-pricing-panel">
          <div class="services-pricing-copy">
            <span class="services-pricing-index">01 / Start a conversation</span>
            <h3>Build the right combination of people, systems and support.</h3>
            <p>Every institution starts from a different place. Share what you are working toward and the HLTS team can help define a practical next step.</p>
            <a href="contact.html" class="btn btn-primary">Discuss your requirements <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
          </div>
          <ul class="services-pricing-list">
            <li><i class="bi bi-check2" aria-hidden="true"></i><span>Understand your school and its priorities</span></li>
            <li><i class="bi bi-check2" aria-hidden="true"></i><span>Match relevant services to your needs</span></li>
            <li><i class="bi bi-check2" aria-hidden="true"></i><span>Plan onboarding, training and continued support</span></li>
          </ul>
        </div>

        <!-- <div class="row g-4 justify-content-center"> -->
          <!-- Starter Plan -->
          <!-- <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="pricing-card">
              <div class="pricing-header">
                <h5>Starter</h5>
                <p class="text-muted">For small schools</p>
              </div>
              <div class="pricing-price">
                <span class="currency">₦</span>
                <span class="amount">50,000</span>
                <span class="period">/month</span>
              </div>
              <ul class="pricing-features">
                <li><i class="bi bi-check-circle-fill text-success"></i> Up to 200 Students</li>
                <li><i class="bi bi-check-circle-fill text-success"></i> Basic School Management</li>
                <li><i class="bi bi-check-circle-fill text-success"></i> Report Card Generation</li>
                <li><i class="bi bi-check-circle-fill text-success"></i> Parent Portal Access</li>
                <li><i class="bi bi-check-circle-fill text-success"></i> Email Support</li>
                <li class="text-muted"><i class="bi bi-x-circle"></i> CBT Platform</li>
                <li class="text-muted"><i class="bi bi-x-circle"></i> Analytics Dashboard</li>
              </ul>
              <a href="contact.html" class="btn btn-outline-primary w-100">Get Started</a>
            </div>
          </div> -->

          <!-- Professional Plan -->
          <!-- <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="pricing-card featured">
              <div class="popular-badge">Most Popular</div>
              <div class="pricing-header">
                <h5>Professional</h5>
                <p class="text-muted">For growing schools</p>
              </div>
              <div class="pricing-price">
                <span class="currency">₦</span>
                <span class="amount">120,000</span>
                <span class="period">/month</span>
              </div>
              <ul class="pricing-features">
                <li><i class="bi bi-check-circle-fill text-success"></i> Up to 1,000 Students</li>
                <li><i class="bi bi-check-circle-fill text-success"></i> Full School Management</li>
                <li><i class="bi bi-check-circle-fill text-success"></i> CBT Examination Platform</li>
                <li><i class="bi bi-check-circle-fill text-success"></i> Online Learning (Basic)</li>
                <li><i class="bi bi-check-circle-fill text-success"></i> Analytics Dashboard</li>
                <li><i class="bi bi-check-circle-fill text-success"></i> Priority Support</li>
                <li><i class="bi bi-check-circle-fill text-success"></i> SMS Integration</li>
              </ul>
              <a href="contact.html" class="btn btn-primary w-100">Get Started</a>
            </div>
          </div> -->

          <!-- Enterprise Plan -->
          <!-- <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="pricing-card">
              <div class="pricing-header">
                <h5>Enterprise</h5>
                <p class="text-muted">For large institutions</p>
              </div>
              <div class="pricing-price">
                <span class="currency"></span>
                <span class="amount">Custom</span>
                <span class="period">pricing</span>
              </div>
              <ul class="pricing-features">
                <li><i class="bi bi-check-circle-fill text-success"></i> Unlimited Students</li>
                <li><i class="bi bi-check-circle-fill text-success"></i> All Professional Features</li>
                <li><i class="bi bi-check-circle-fill text-success"></i> Multi-Campus Support</li>
                <li><i class="bi bi-check-circle-fill text-success"></i> Custom Integrations</li>
                <li><i class="bi bi-check-circle-fill text-success"></i> Dedicated Account Manager</li>
                <li><i class="bi bi-check-circle-fill text-success"></i> On-Site Training</li>
                <li><i class="bi bi-check-circle-fill text-success"></i> SLA Guarantee</li>
              </ul>
              <a href="contact.html" class="btn btn-outline-primary w-100">Contact Sales</a>
            </div>
          </div> -->
        <!-- </div> -->
      </div>
    </section>

    <!-- CTA Section -->
    <section class="services-cta py-5">
      <div class="container text-center py-4">
        <h2 class="text-white fw-bold mb-3" data-aos="fade-up">Ready for a more connected approach?</h2>
        <p class="text-white-50 mb-4" data-aos="fade-up" data-aos-delay="100">Talk with HLTS about the tools and support that fit your educational institution.</p>
        <div data-aos="fade-up" data-aos-delay="200">
          <a href="contact.html" class="btn btn-light btn-lg me-3 mb-2">
            <i class="bi bi-chat-square-text me-2"></i>Discuss your needs
          </a>
          <a href="tel:+2348107005789" class="btn btn-outline-light btn-lg mb-2">
            <i class="bi bi-telephone me-2"></i>Call Us Now
          </a>
        </div>
      </div>
    </section>

    <!-- Footer -->

<?php include __DIR__ . '/partials/footer.php'; ?>
