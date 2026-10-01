<?php
require __DIR__ . '/lib/app.php';

$email = (string) config('email_public');
$updated = (new DateTimeImmutable(PRIVACY_VERSION))->format('j F Y');

page_start([
    'title' => 'Privacy Policy – HLTS Limited',
    'description' => 'How HLTS Limited collects, uses, protects and deletes personal information under the Nigeria Data Protection Act 2023.',
]);

echo page_hero([
    'crumbs' => [['Company'], ['Privacy Policy']],
    'eyebrow' => 'Data protection',
    'title' => 'Privacy <span class="grad-text">Policy</span>',
    'lead' => 'What we collect through this website, why, who can see it, how long we keep it, and your rights under the Nigeria Data Protection Act 2023.',
    'actions' => button('Your rights', '#your-rights', 'ghost-light', 'arrow-down') . button('Contact us about your data', 'mailto:' . $email, 'ghost-light', 'envelope'),
]);
?>

<section class="section">
  <div class="container" style="max-width: 860px">
    <article class="prose" data-reveal>
      <p class="small muted">Version <?= h(PRIVACY_VERSION) ?> · last updated <?= h($updated) ?></p>

      <h2 id="who-we-are">1. Who we are</h2>
      <p>HLTS Limited (“HLTS”, “we”, “us”) is the data controller for personal information collected through hltsltd.com and our forms. We are based at <?= h(config('address')) ?>. For anything about your personal information, email <a href="mailto:<?= h($email) ?>"><?= h($email) ?></a> with “Data protection” in the subject line.</p>

      <h2 id="what-we-collect">2. What we collect</h2>
      <p>We only ask for what we need for each request. Depending on the form you use, this includes:</p>
      <ul>
        <li><strong>Job applications and the talent pool:</strong> your name, email, phone and WhatsApp numbers, the roles you want, qualifications, experience, skills, where you live, when you can start, expected salary (optional) and your CV.</li>
        <li><strong>Course registration:</strong> the learner’s name, email, phone, age group, chosen course, payment plan and learning mode, and a parent or guardian’s name for learners under 18.</li>
        <li><strong>School registration, demo bookings and quote requests:</strong> your name, role, organisation, contact details, location and what you need.</li>
        <li><strong>IT support requests:</strong> your name, school, contact details and a description of the problem.</li>
        <li><strong>TechMind Africa membership:</strong> your name, email, WhatsApp number, city, interests and experience level.</li>
        <li><strong>Contact messages and newsletter sign-ups:</strong> your name, email, phone (optional) and message, or just your email for the newsletter.</li>
        <li><strong>Result checks and certificate checks:</strong> the student ID and PIN or certificate number you enter, used only to find the record.</li>
        <li><strong>Payments:</strong> handled by Paystack. We receive the payment reference, amount and status, never your full card details.</li>
        <li><strong>Technical information:</strong> your IP address and basic request details, used to protect the site from abuse (for example, limiting repeated submissions).</li>
      </ul>
      <p>With every form we also record that you agreed to this policy, when, and which version.</p>

      <h2 id="how-we-use-it">3. Why we use it, and our lawful basis</h2>
      <ul>
        <li><strong>Your consent</strong>, given by ticking the box on each form: to respond to your request, consider you for jobs, keep you in our talent pool, add you to TechMind Africa, or send our newsletter.</li>
        <li><strong>A contract with you or your school</strong>: to deliver courses, school services and IT support you have asked for, and to process payments.</li>
        <li><strong>Our legitimate interests</strong>: to keep the website secure, prevent fraud and spam, and improve our services, in ways you would reasonably expect.</li>
        <li><strong>Legal obligations</strong>: to keep financial and tax records as Nigerian law requires.</li>
      </ul>
      <p>We do not sell personal information, and we do not use it for automated decisions that significantly affect you.</p>

      <h2 id="who-sees-it">4. Who can see it</h2>
      <p>Submissions go to the HLTS staff app, where only the team responsible can see them:</p>
      <ul>
        <li>Job applications and CVs: HR and the CEO only. Every time a CV is opened it is recorded.</li>
        <li>School enquiries and IT support: our Operations team and the CEO.</li>
        <li>Course registrations, TechMind Africa members and newsletter subscribers: our Community Leader and the CEO.</li>
      </ul>
      <p>We use trusted service providers who process data for us and only on our instructions: our website host, Vercel and Neon (which host the staff app and its database), our email provider and Paystack for payments. Some of these providers store data outside Nigeria, for example in the European Union. Where that happens we rely on the safeguards the Nigeria Data Protection Act requires, such as the destination’s adequate level of protection or contractual protections.</p>
      <p>For partner schools, student results are processed on the school’s instructions; the school decides how those records are used.</p>

      <h2 id="how-long">5. How long we keep it</h2>
      <ul>
        <li><strong>Job seekers:</strong> until we decide on your application. If we decide not to proceed, your details and CV are deleted automatically 30 days after that decision. If you are hired, they become part of your staff record.</li>
        <li><strong>Enquiries, IT support requests and contact messages:</strong> for up to 2 years after our last contact, unless you become a client.</li>
        <li><strong>Course registrations and payment records:</strong> for the length of the course and then up to 6 years, as tax and accounting law requires.</li>
        <li><strong>TechMind Africa membership:</strong> until you leave the community. <strong>Newsletter:</strong> until you unsubscribe.</li>
        <li><strong>Security logs:</strong> up to 12 months.</li>
      </ul>
      <p>The website itself keeps only what it needs to deliver your submission to the staff app. Job seekers’ details and CVs are removed from the website as soon as they reach the staff app.</p>

      <h2 id="security">6. How we protect it</h2>
      <p>Connections are encrypted, access is limited by role and protected with two-step sign-in for sensitive roles, sensitive fields are encrypted, uploaded files are stored privately and checked to be genuine documents, and we keep encrypted backups. If a breach puts your rights at risk, we will tell you and the Nigeria Data Protection Commission as the law requires.</p>

      <h2 id="children">7. Children</h2>
      <p>For learners under 18, a parent or guardian must complete or approve the registration and give their name. Only our Community Leader and the CEO can see these registrations.</p>

      <h2 id="your-rights">8. Your rights</h2>
      <p>Under the Nigeria Data Protection Act 2023 you can ask us to:</p>
      <ul>
        <li>tell you what information we hold about you and give you a copy;</li>
        <li>correct information that is wrong or incomplete;</li>
        <li>delete your information, or stop or limit how we use it;</li>
        <li>give you your information in a common electronic format, or send it to someone else;</li>
        <li>stop sending you newsletters or other messages, at any time.</li>
      </ul>
      <p>You can withdraw your consent at any time. This does not affect anything we did before you withdrew it. Email <a href="mailto:<?= h($email) ?>"><?= h($email) ?></a>; we reply within one month. If you are unhappy with our answer, you can complain to the <a href="https://ndpc.gov.ng" target="_blank" rel="noopener">Nigeria Data Protection Commission</a>.</p>

      <h2 id="cookies">9. Cookies</h2>
      <p>We use one essential cookie to keep forms secure and keep you signed in to the student portal. We do not use advertising or tracking cookies. The contact page shows a Google Maps map, and Google may set its own cookies when it loads.</p>

      <h2 id="changes">10. Changes to this policy</h2>
      <p>When we change this policy we update the version and date at the top. Each form records which version you agreed to.</p>
    </article>
  </div>
</section>

<?php page_end(); ?>
