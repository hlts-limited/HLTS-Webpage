<?php
$pageTitle = 'Tech Courses - HLTS Limited';
$pageDescription = 'HLTS Limited Tech Courses - Front-end, Back-end, Data Analysis, Graphics Design and more.';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/nav.php';
?>

    <main class="courses-page">
      <section class="courses-hero">
        <div class="container">
          <div class="courses-hero-layout">
            <div class="courses-hero-copy">
              <span class="eyebrow">HLTS Online Institution</span>
              <h1>Choose a skill. Build what comes next.</h1>
              <p>Explore practical technology courses designed to help you learn with purpose and create with confidence.</p>
              <a href="registration-form.html" class="btn btn-primary">Start your registration <i class="bi bi-arrow-up-right"></i></a>
            </div>
            <figure class="courses-hero-visual">
              <img src="images/webdev.jpeg" alt="A learner working with web development tools" fetchpriority="high">
              <figcaption class="courses-hero-note"><i class="bi bi-lightning-charge-fill" aria-hidden="true"></i><strong>6 learning paths</strong><span>From first code to professional workflows</span></figcaption>
            </figure>
          </div>
        </div>
      </section>

      <section class="courses-catalogue">
        <div class="container">
          <div class="courses-heading"><div><span class="eyebrow">Course catalogue</span><h2>Find your next direction.</h2></div><p>Choose a programme, review the available payment options, then register to begin.</p></div>
          <div class="courses-grid-modern">

          <!-- front Web Development -->
          <div class="col-md-4 course-card">
            <div class="card h-100">
              <div class="card-body">
                <div class="course-card-top"><span class="course-number">01</span><i class="bi bi-window-stack"></i></div>
                <h3 class="card-title">Front-end Web Development</h3>
                <p class="card-text">Learn to build the parts of a website people see and use. Work with HTML for structure, CSS for visual design, and JavaScript for interactive, responsive experiences.</p>
                <div class="course-fees"><span>Session <strong>₦300,000</strong></span><span>Semester <strong>₦340,000</strong></span><span>Monthly <strong>₦360,000</strong></span></div>
              </div>
            </div>
          </div>
          
          <!-- Data analysis -->
          <div class="col-md-4 course-card">
            <div class="card h-100">
              <div class="card-body">
                <div class="course-card-top"><span class="course-number">02</span><i class="bi bi-bar-chart-line"></i></div>
                <h3 class="card-title">Data Analysis</h3>
                <p class="card-text">Learn to collect, clean and explore data, identify patterns, and use evidence to answer questions and support better decisions.</p>
                <div class="course-fees"><span>Session <strong>₦240,000</strong></span><span>Semester <strong>₦380,000</strong></span><span>Monthly <strong>₦300,000</strong></span></div>
              </div>
            </div>
          </div>

          <!-- desktop -->
          <div class="col-md-4 course-card">
            <div class="card h-100">
              <div class="card-body">
                <div class="course-card-top"><span class="course-number">03</span><i class="bi bi-layout-text-window"></i></div>
                <h3 class="card-title">Desktop Publishing</h3>
                <p class="card-text">Combine text and graphics to create polished print and digital materials. Practice layout, typography and image composition for brochures, newsletters and eBooks.</p>
                <div class="course-fees"><span>Session <strong>₦140,000</strong></span><span>Semester <strong>₦160,000</strong></span><span>Monthly <strong>₦180,000</strong></span></div>
              </div>
            </div>
          </div>

          <!-- v-programming -->
          <div class="col-md-4 course-card">
            <div class="card h-100">
              <div class="card-body">
                <div class="course-card-top"><span class="course-number">04</span><i class="bi bi-puzzle"></i></div>
                <h3 class="card-title">Block-Based Programming</h3>
                <p class="card-text">Build programs by connecting visual blocks that represent commands, functions and logic. This approachable path introduces programming through hands-on practice.</p>
                <div class="course-fees"><span>Session <strong>₦180,000</strong></span><span>Semester <strong>₦220,000</strong></span><span>Monthly <strong>₦240,000</strong></span></div>
              </div>
            </div>
          </div>

          <!-- graphics -->
          <div class="col-md-4 course-card">
            <div class="card h-100">
              <div class="card-body">
                <div class="course-card-top"><span class="course-number">05</span><i class="bi bi-palette"></i></div>
                <h3 class="card-title">Graphics Design</h3>
                <p class="card-text">Use typography, imagery and layout to communicate ideas. Practice balance, hierarchy and contrast to create purposeful visuals for a specific audience.</p>
                <div class="course-fees"><span>Session <strong>₦260,000</strong></span><span>Semester <strong>₦280,000</strong></span><span>Monthly <strong>₦300,000</strong></span></div>
              </div>
            </div>
          </div>

          <!-- back-end -->
          <div class="col-md-4 course-card">
            <div class="card h-100">
              <div class="card-body">
                <div class="course-card-top"><span class="course-number">06</span><i class="bi bi-server"></i></div>
                <h3 class="card-title">Back-end Web Development</h3>
                <p class="card-text">Explore the server-side systems behind websites. Learn how application logic, data and APIs work together to support reliable, secure user experiences.</p>
                <div class="course-fees"><span>Session <strong>₦420,000</strong></span><span>Semester <strong>₦460,000</strong></span><span>Monthly <strong>₦480,000</strong></span></div>
              </div>
            </div>
          </div>

          </div>
        </div>
      </section>
      <section class="courses-cta"><div class="container"><div><span class="eyebrow">Ready when you are</span><h2>Turn interest into your next skill.</h2></div><a href="registration-form.html" class="btn btn-primary">Register for a course <i class="bi bi-arrow-up-right"></i></a></div></section>
    </main>

<?php include __DIR__ . '/partials/footer.php'; ?>
