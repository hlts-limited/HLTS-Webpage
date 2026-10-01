<?php
$pageTitle = 'CBT Exam Platform Setup & Management';
$pageDescription = 'HLTS Limited - Your Partner in Education. Transform your learning experience with innovative ed-tech solutions.';
$bodyClass = 'cbt-page';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/nav.php';
?>

        <main class="cbt-page-main">
            <section class="cbt-hero">
                <div class="container cbt-hero-layout">
                    <div class="cbt-hero-copy">
                        <span class="eyebrow">Assessment and exam operations</span>
                        <h1>Make exam delivery more reliable.</h1>
                        <p>HLTS helps schools set up and manage computer-based assessments, from question banks and secure student access to grading and reporting.</p>
                        <div class="cbt-hero-actions">
                            <a href="school-form.html" class="btn btn-primary">Plan your CBT setup <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                            <a href="#system-integration" class="cbt-text-link">Explore integrations <i class="bi bi-arrow-down-right" aria-hidden="true"></i></a>
                        </div>
                    </div>
                    <figure class="cbt-hero-visual">
                        <img src="images/cbt.jpg" alt="Computer-based testing tools for digital assessment" fetchpriority="high">
                        <figcaption><span>HLTS CBT support</span><strong>From setup through results</strong></figcaption>
                    </figure>
                </div>
            </section>

            <section class="cbt-capabilities">
                <div class="container">
                    <div class="cbt-section-heading">
                        <span class="eyebrow">What HLTS supports</span>
                        <h2>From exam setup to useful results.</h2>
                        <p>Bring the practical pieces of computer-based testing into one supported workflow for your school.</p>
                    </div>
                    <div class="cbt-capability-layout">
                        <ul class="cbt-bullet-list">
                            <li><span class="cbt-bullet"><i class="bi bi-gear-fill" aria-hidden="true"></i></span><span><strong>Platform setup</strong>Deployment and configuration tailored to your school's needs.</span></li>
                            <li><span class="cbt-bullet"><i class="bi bi-archive-fill" aria-hidden="true"></i></span><span><strong>Question bank management</strong>Upload, organize and categorize questions by subject and class.</span></li>
                            <li><span class="cbt-bullet"><i class="bi bi-calendar-event-fill" aria-hidden="true"></i></span><span><strong>Exam scheduling</strong>Set timing, duration and access for different student groups.</span></li>
                            <li><span class="cbt-bullet"><i class="bi bi-person-check-fill" aria-hidden="true"></i></span><span><strong>Student access</strong>Secure logins and real-time monitoring to support exam integrity.</span></li>
                            <li><span class="cbt-bullet"><i class="bi bi-clipboard-check-fill" aria-hidden="true"></i></span><span><strong>Automated grading</strong>Generate results for objective questions and review subjective answers.</span></li>
                            <li><span class="cbt-bullet"><i class="bi bi-bar-chart-fill" aria-hidden="true"></i></span><span><strong>Analytics and reporting</strong>Review performance and prepare reports for educators and administrators.</span></li>
                            <li><span class="cbt-bullet"><i class="bi bi-people-fill" aria-hidden="true"></i></span><span><strong>Support and training</strong>Onboarding and guidance for staff and students.</span></li>
                        </ul>
                        <aside class="cbt-workflow-panel">
                            <span class="cbt-workflow-label">A supported exam workflow</span>
                            <div><span>01</span><strong>Prepare</strong><small>Configure the platform, question bank and schedules.</small></div>
                            <div><span>02</span><strong>Deliver</strong><small>Give students secure access to their assessments.</small></div>
                            <div><span>03</span><strong>Review</strong><small>Grade work and review performance reports.</small></div>
                            <a href="school-form.html" class="cbt-panel-link">Discuss school setup <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                        </aside>
                    </div>
                </div>
            </section>

            <section class="cbt-learner-panel">
                <div class="container">
                    <div>
                        <span class="eyebrow">For individual learners</span>
                        <h2>Looking to build your own skills?</h2>
                        <p>Explore the HLTS Online Institution courses and choose a learning path that fits your goals.</p>
                    </div>
                    <div class="cbt-learner-actions">
                        <a href="course.html" class="btn btn-outline-primary">Browse courses</a>
                        <a href="registration-form.html" class="cbt-text-link">Register to learn <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                    </div>
                </div>
            </section>

        <!-- System Integration & Lab Setup Section -->
        <section id="system-integration" class="cbt-integration py-5 px-4 bg-white">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <div class="feature-icon mb-3" style="background: linear-gradient(135deg, #00b8d9, #36d8f5); display: inline-block; border-radius: 50%; padding: 18px;">
                                <i class="bi bi-plug" style="font-size: 2.5rem; color: #fff;"></i>
                            </div>
                            <h2 class="text-primary fw-bold mb-3">System Integration & Lab Setup</h2>
                            <p class="lead text-muted">Empower your institution with seamless integration of digital tools and modern computer labs. We connect your school to the best-in-class platforms for communication, payments, and learning, and set up state-of-the-art labs for hands-on education.</p>
                        </div>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Integration with payment gateways, SMS, and email platforms</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Google Workspace and Microsoft Teams setup</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Secure API access for custom solutions</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Full computer lab design, installation, and training</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Ongoing technical support and maintenance</li>
                        </ul>
                        <div class="text-center mt-4">
                            <a href="school-form.html" class="btn btn-primary btn-lg">Apply Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        </main>

<?php include __DIR__ . '/partials/footer.php'; ?>
