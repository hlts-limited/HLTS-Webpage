<?php
$pageTitle = 'Contact Us - HLTS Limited';
$pageDescription = 'Contact HLTS Limited - Get in touch with our team in Lagos, Nigeria.';
$bodyClass = 'contact-page';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/nav.php';
?>

    <main>
      <section class="contact-hero">
        <div class="container">
          <span class="eyebrow">Talk to HLTS</span>
          <h1>Let’s talk about what you need.</h1>
          <p>For school support, education technology or a new partnership, reach out to the HLTS team in Lagos.</p>
        </div>
      </section>

      <section class="contact-details-section">
        <div class="container contact-details-grid">
          <div class="contact-details-copy">
            <span class="eyebrow">Office and direct contact</span>
            <h2>We’re here to help you take the next step.</h2>

            <div class="contact-method-list">
              <a class="contact-method" href="tel:+2348107005789">
                <span class="contact-method-icon"><i class="bi bi-telephone" aria-hidden="true"></i></span>
                <span><small>Call our team</small><strong>+234 810 700 5789</strong></span>
                <i class="bi bi-arrow-up-right contact-method-arrow" aria-hidden="true"></i>
              </a>
              <a class="contact-method" href="mailto:info@hltsltd.com">
                <span class="contact-method-icon"><i class="bi bi-envelope" aria-hidden="true"></i></span>
                <span><small>Email us</small><strong>info@hltsltd.com</strong></span>
                <i class="bi bi-arrow-up-right contact-method-arrow" aria-hidden="true"></i>
              </a>
              <a class="contact-method" href="https://www.google.com/maps/search/?api=1&amp;query=8+Assembly+Close%2C+Folagoro%2C+Somolu%2C+Lagos%2C+Nigeria" target="_blank" rel="noopener noreferrer">
                <span class="contact-method-icon"><i class="bi bi-geo-alt" aria-hidden="true"></i></span>
                <span><small>Visit our office</small><strong>8 Assembly Close, Folagoro, Somolu, Lagos</strong></span>
                <i class="bi bi-arrow-up-right contact-method-arrow" aria-hidden="true"></i>
              </a>
            </div>

            <p class="contact-office-hours"><i class="bi bi-clock" aria-hidden="true"></i><span><strong>Office hours</strong>Monday to Friday, 8am to 5pm</span></p>
          </div>

          <div class="contact-map-panel">
            <div class="contact-map-heading">
              <div><span class="eyebrow">Find us in Lagos</span><h2>Visit the HLTS office.</h2></div>
              <a href="https://www.google.com/maps/search/?api=1&amp;query=8+Assembly+Close%2C+Folagoro%2C+Somolu%2C+Lagos%2C+Nigeria" target="_blank" rel="noopener noreferrer" aria-label="Get directions to the HLTS office"><i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
            </div>
            <div class="map-container">
              <iframe title="Map showing the HLTS office in Folagoro, Somolu, Lagos" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.9572092385833!2d3.37573667586253!3d6.5270888231198425!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x103b8d0074f29cff%3A0xbb486cf6bcfc9b1c!2sAssembly%20close%2C%20fola%20agoro!5e0!3m2!1sen!2sng!4v1757783462436!5m2!1sen!2sng" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
            </div>
          </div>
        </div>
      </section>
    </main>
    
    <!-- Footer -->

<?php include __DIR__ . '/partials/footer.php'; ?>
