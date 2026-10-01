<?php
/**
 * Shared site navigation. Grouped by HLTS business line.
 * Requires partials/head.php to have run (it sets $currentPage and h()).
 */

$navActive = function (array $pages) use ($currentPage) {
    return in_array($currentPage, $pages, true) ? ' active' : '';
};
$navCurrent = function (array $pages) use ($currentPage) {
    return in_array($currentPage, $pages, true) ? ' aria-current="page"' : '';
};
?>
    <nav class="navbar navbar-expand-lg navbar-dark" aria-label="Main">
      <div class="container-fluid public-nav-inner">
        <a class="navbar-brand" href="index.html" aria-label="HLTS Limited home">
          <img src="images/logoh.png" alt="HLTS Logo" height="70">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Open navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="mainNavbar">
          <ul class="navbar-nav mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link<?= $navActive(['index']) ?>" href="index.html"<?= $navCurrent(['index']) ?>><i class="bi bi-house" aria-hidden="true"></i> Home</a></li>

            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle<?= $navActive(['services', 'cbt', 'school-form']) ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-building" aria-hidden="true"></i> For Schools</a>
              <ul class="dropdown-menu public-dropdown">
                <li><a class="dropdown-item" href="services.html"<?= $navCurrent(['services']) ?>><i class="bi bi-diagram-3" aria-hidden="true"></i><span><strong>School Solutions</strong><small>Technology, operations and support</small></span></a></li>
                <li><a class="dropdown-item" href="cbt.html"<?= $navCurrent(['cbt']) ?>><i class="bi bi-ui-checks-grid" aria-hidden="true"></i><span><strong>CBT &amp; Assessments</strong><small>Exam setup and result management</small></span></a></li>
                <li><a class="dropdown-item" href="school-form.html"<?= $navCurrent(['school-form']) ?>><i class="bi bi-person-workspace" aria-hidden="true"></i><span><strong>Register Your School</strong><small>Staff deployment and school services</small></span></a></li>
              </ul>
            </li>

            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle<?= $navActive(['online-institution', 'course', 'registration-form']) ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-mortarboard" aria-hidden="true"></i> Online Institution</a>
              <ul class="dropdown-menu public-dropdown">
                <li><a class="dropdown-item" href="online-institution.html"<?= $navCurrent(['online-institution']) ?>><i class="bi bi-laptop" aria-hidden="true"></i><span><strong>About the Institution</strong><small>How HLTS Online Institution works</small></span></a></li>
                <li><a class="dropdown-item" href="course.html"<?= $navCurrent(['course']) ?>><i class="bi bi-journal-text" aria-hidden="true"></i><span><strong>Courses</strong><small>Programmes and fees</small></span></a></li>
                <li><a class="dropdown-item" href="registration-form.html"<?= $navCurrent(['registration-form']) ?>><i class="bi bi-person-plus" aria-hidden="true"></i><span><strong>Register</strong><small>Start your learning journey</small></span></a></li>
              </ul>
            </li>

            <li class="nav-item"><a class="nav-link<?= $navActive(['community']) ?>" href="community.html"<?= $navCurrent(['community']) ?>><i class="bi bi-globe2" aria-hidden="true"></i> TechMind Africa</a></li>

            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle<?= $navActive(['about', 'faq', 'terms', 'contact']) ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-info-circle" aria-hidden="true"></i> Company</a>
              <ul class="dropdown-menu public-dropdown">
                <li><a class="dropdown-item" href="about.html"<?= $navCurrent(['about']) ?>><i class="bi bi-building-check" aria-hidden="true"></i><span><strong>About HLTS</strong><small>Our mission and people</small></span></a></li>
                <li><a class="dropdown-item" href="faq.html"<?= $navCurrent(['faq']) ?>><i class="bi bi-question-circle" aria-hidden="true"></i><span><strong>FAQs</strong><small>Answers to common questions</small></span></a></li>
                <li><a class="dropdown-item" href="contact.html"<?= $navCurrent(['contact']) ?>><i class="bi bi-envelope" aria-hidden="true"></i><span><strong>Contact</strong><small>Talk to our team</small></span></a></li>
              </ul>
            </li>

            <li class="nav-item nav-portal-item"><a class="nav-link<?= $navActive(['portal']) ?>" href="portal.html"<?= $navCurrent(['portal']) ?>><i class="bi bi-person-badge" aria-hidden="true"></i> Student Portal</a></li>
            <li class="nav-item"><a class="btn contact-btn" href="contact.html"><i class="bi bi-arrow-up-right" aria-hidden="true"></i> Talk to HLTS</a></li>
          </ul>
        </div>
      </div>
    </nav>
