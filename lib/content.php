<?php
/**
 * Site content that rarely changes: courses, school services, team, FAQs.
 * Edit here and every page that shows it updates.
 *
 * Content managed day to day (blog, events, portfolio, jobs, course
 * materials) lives in the database and is edited in the admin area.
 */

/**
 * Courses offered by HLTS Online Institution. Fees are in kobo (₦1 = 100 kobo).
 *
 * Each fee is the TOTAL for one 8-month session under that payment plan:
 *   session   paid once
 *   semester  paid in 2 instalments (a semester is 4 months)
 *   monthly   paid in 8 instalments
 * e.g. semester 22000000 = ₦220,000 total = ₦110,000 per semester.
 */
function courses(): array
{
    return [
        'frontend' => [
            'title' => 'Front-end Web Development',
            'icon' => 'window-stack',
            'track' => 'Web Development',
            'summary' => 'Build the parts of a website people see and use, with HTML for structure, CSS for design and JavaScript for interaction.',
            'outcomes' => ['Structure pages with semantic HTML', 'Design responsive layouts with CSS', 'Add interaction with JavaScript', 'Publish a portfolio website'],
            'fees' => ['session' => 30000000, 'semester' => 34000000, 'monthly' => 36000000],
            'image' => 'images/webdev.jpeg',
        ],
        'backend' => [
            'title' => 'Back-end Web Development',
            'icon' => 'server',
            'track' => 'Web Development',
            'summary' => 'Explore the server-side systems behind websites: application logic, data and APIs working together securely.',
            'outcomes' => ['Write server-side code', 'Design and query databases', 'Build and secure APIs', 'Deploy a working web application'],
            'fees' => ['session' => 42000000, 'semester' => 46000000, 'monthly' => 48000000],
            'image' => 'images/setup.jpeg',
        ],
        'fullstack' => [
            'title' => 'Full-Stack Development',
            'icon' => 'stack',
            'track' => 'Web Development',
            'summary' => 'Combine front-end and back-end skills to plan, build and launch complete web applications.',
            'outcomes' => ['Everything in front-end and back-end', 'Connect interfaces to real data', 'Work with version control', 'Ship a full project end to end'],
            'fees' => [],
            'image' => 'images/mockup.jpeg',
        ],
        'data-analysis' => [
            'title' => 'Data Analysis',
            'icon' => 'bar-chart-line',
            'track' => 'Data & Analytics',
            'summary' => 'Collect, clean and explore data, find patterns, and use evidence to answer questions and support decisions.',
            'outcomes' => ['Clean and organise data', 'Analyse with spreadsheets', 'Build clear charts and dashboards', 'Present findings with confidence'],
            'fees' => ['session' => 24000000, 'semester' => 38000000, 'monthly' => 30000000],
            'image' => 'images/data.jpeg',
        ],
        'graphic-design' => [
            'title' => 'Graphic Design',
            'icon' => 'palette',
            'track' => 'Design & Multimedia',
            'summary' => 'Use typography, imagery and layout to communicate ideas with balance, hierarchy and contrast.',
            'outcomes' => ['Apply design principles', 'Work with type and colour', 'Create brand and social assets', 'Build a design portfolio'],
            'fees' => ['session' => 26000000, 'semester' => 28000000, 'monthly' => 30000000],
            'image' => 'images/consult.jpeg',
        ],
        'video-editing' => [
            'title' => 'Video Editing',
            'icon' => 'camera-reels',
            'track' => 'Design & Multimedia',
            'summary' => 'Cut, sequence and polish video for social media, events and storytelling.',
            'outcomes' => ['Plan and cut a story', 'Add titles, music and effects', 'Colour and audio basics', 'Export for every platform'],
            'fees' => [],
            'image' => 'images/project4.jpeg',
        ],
        'desktop-publishing' => [
            'title' => 'Desktop Publishing',
            'icon' => 'layout-text-window',
            'track' => 'Design & Multimedia',
            'summary' => 'Combine text and graphics into polished print and digital materials such as brochures, newsletters and eBooks.',
            'outcomes' => ['Lay out multi-page documents', 'Use typography well', 'Prepare files for print', 'Publish digital documents'],
            'fees' => ['session' => 14000000, 'semester' => 16000000, 'monthly' => 18000000],
            'image' => 'images/project5.jpeg',
        ],
        'visual-programming' => [
            'title' => 'Block-Based Programming',
            'icon' => 'puzzle',
            'track' => 'Programming',
            'summary' => 'Build programs by connecting visual blocks for commands and logic. An approachable first step into coding.',
            'outcomes' => ['Think in steps and logic', 'Use loops and conditions', 'Build simple games and animations', 'Get ready for text-based code'],
            'fees' => ['session' => 18000000, 'semester' => 22000000, 'monthly' => 24000000],
            'image' => 'images/achildcoding.jpeg',
        ],
    ];
}

function course(string $slug): ?array
{
    return courses()[$slug] ?? null;
}

function fee_plans(): array
{
    return [
        'session' => 'Full session',
        'semester' => 'Per semester',
        'monthly' => 'Monthly',
    ];
}

/** How many payments each plan is split into over one 8-month session. */
function plan_instalments(string $plan): int
{
    return ['session' => 1, 'semester' => 2, 'monthly' => 8][$plan] ?? 1;
}

/** Amount of a single payment for a plan, in kobo. */
function instalment_amount(int $totalKobo, string $plan): int
{
    return (int) round($totalKobo / plan_instalments($plan));
}

/** "₦110,000 per semester", "₦30,000 per month", "₦180,000 once". */
function instalment_label(int $totalKobo, string $plan): string
{
    $unit = ['session' => ' once', 'semester' => ' per semester', 'monthly' => ' per month'][$plan] ?? '';
    return naira(instalment_amount($totalKobo, $plan)) . $unit;
}

/** Short explanation of a plan, e.g. "2 payments of ₦110,000 (₦220,000 total)". */
function plan_breakdown(int $totalKobo, string $plan): string
{
    $count = plan_instalments($plan);
    if ($count === 1) {
        return 'One payment for the 8-month session';
    }
    return $count . ' payments of ' . naira(instalment_amount($totalKobo, $plan)) . ' (' . naira($totalKobo) . ' total)';
}

/** Modules a school can choose from. Shown on School Management and in forms. */
function school_modules(): array
{
    return [
        'cbt' => ['title' => 'CBT exams', 'icon' => 'ui-checks-grid', 'text' => 'Computer-based tests and exams, from question banks to secure student access and automatic marking.'],
        'results' => ['title' => 'Result management', 'icon' => 'clipboard-data', 'text' => 'Score entry, automatic grading, report cards and online result checking for parents.'],
        'it-support' => ['title' => 'IT support', 'icon' => 'headset', 'text' => 'Help desk, device and network care, and a technician your school can call on.'],
        'operations' => ['title' => 'Daily operations', 'icon' => 'calendar2-week', 'text' => 'Admissions, records, attendance, timetables and fee tracking in one workflow.'],
        'staff' => ['title' => 'Staff deployment', 'icon' => 'person-workspace', 'text' => 'Vetted ICT facilitators, teachers and lab technicians placed in your school.'],
        'labs' => ['title' => 'Lab setup', 'icon' => 'pc-display', 'text' => 'Computer lab design, installation and maintenance for hands-on learning.'],
        'curriculum' => ['title' => 'Curriculum & training', 'icon' => 'journal-bookmark', 'text' => 'ICT curriculum that fits your school, and training for teachers and administrators.'],
        'integration' => ['title' => 'Integrations', 'icon' => 'plug', 'text' => 'Payments, SMS, email, Google Workspace and Microsoft Teams connected to your systems.'],
    ];
}

/** Engagement levels. Prices are agreed per school, so none are shown. */
function school_packages(): array
{
    return [
        [
            'name' => 'Essentials',
            'for' => 'Schools starting their digital journey',
            'items' => ['Result management and report cards', 'Online result checking for parents', 'Staff training', 'Email support'],
        ],
        [
            'name' => 'Growth',
            'for' => 'Schools ready to run exams and operations digitally',
            'featured' => true,
            'items' => ['Everything in Essentials', 'CBT exams and question banks', 'Daily operations tools', 'IT support visits', 'Priority support'],
        ],
        [
            'name' => 'Full Management',
            'for' => 'Schools that want HLTS to run their technology',
            'items' => ['Everything in Growth', 'Deployed ICT staff', 'Lab setup and maintenance', 'Curriculum support', 'A dedicated account manager'],
        ],
    ];
}

/** Roles HLTS places in schools. */
function staff_roles(): array
{
    return [
        ['title' => 'ICT facilitators', 'icon' => 'pc-display-horizontal', 'text' => 'Teach computer studies, run the lab and support CBT exams.'],
        ['title' => 'Subject teachers', 'icon' => 'easel2', 'text' => 'Qualified teachers for core and elective subjects, including STEM.'],
        ['title' => 'Lab technicians', 'icon' => 'tools', 'text' => 'Keep devices, networks and lab equipment working every day.'],
        ['title' => 'Exam & results officers', 'icon' => 'clipboard-check', 'text' => 'Manage exam setup, score entry and result publishing.'],
    ];
}

/** Digital products HLTS builds for schools, businesses and organisations. */
function digital_services(): array
{
    return [
        ['title' => 'School websites', 'icon' => 'window-sidebar', 'text' => 'Fast, mobile-friendly websites with admissions, news and galleries your staff can update.'],
        ['title' => 'Web & mobile apps', 'icon' => 'phone', 'text' => 'Custom apps for learning, operations or customers, designed around how people actually work.'],
        ['title' => 'Portals & dashboards', 'icon' => 'grid-1x2', 'text' => 'Student, parent and staff portals, plus dashboards that turn data into decisions.'],
        ['title' => 'Brand & design', 'icon' => 'vector-pen', 'text' => 'Logos, brand guides, prospectuses and social media design.'],
        ['title' => 'Systems integration', 'icon' => 'diagram-3', 'text' => 'Connect payments, SMS, email and third-party tools so information flows automatically.'],
        ['title' => 'Hosting & care', 'icon' => 'shield-check', 'text' => 'Secure hosting, backups, updates and support after launch.'],
    ];
}

function testimonials(): array
{
    return [
        ['quote' => 'HLTS@School completely transformed how we manage our school. From admissions to result processing, everything is now seamless and paperless. Our staff efficiency has improved by 60%.', 'name' => 'Mr Adebayo', 'role' => 'Principal, Engreg School, Lagos'],
        ['quote' => 'The CBT platform made our WAEC and JAMB prep seamless. Students can practise anywhere, anytime. We saw a 35% improvement in exam scores within one term.', 'name' => 'Miss Nnena', 'role' => 'Head Mistress, Engreg School, Lagos'],
        ['quote' => 'As a parent, I get real-time access to my children\'s grades, attendance and school announcements. I feel more connected to their education than ever.', 'name' => 'Chidi Okafor', 'role' => 'Parent, Engreg School'],
        ['quote' => 'The analytics dashboard is a game-changer. We track performance trends, identify students who need help early, and make data-driven decisions.', 'name' => 'Sarah Adeyemi', 'role' => 'Director, Nazareth School, Lagos'],
        ['quote' => 'HLTS helped us digitise our entire school operations in just 3 weeks. They trained all 45 of our staff members.', 'name' => 'Emmanuel Ibe', 'role' => 'Proprietor, St. Philip Catholic School, Lagos'],
    ];
}

function case_studies(): array
{
    return [
        ['school' => 'St. Philip Catholic School', 'place' => 'Lagos', 'quote' => 'HLTS transformed how we manage student data and communicate with parents.', 'stats' => [['35%', 'better grades'], ['85%', 'teacher adoption']]],
        ['school' => 'Nazareth School', 'place' => 'Festac, Lagos', 'quote' => 'Real-time analytics helped us identify struggling students early.', 'stats' => [['28%', 'less dropout'], ['92%', 'satisfaction']]],
        ['school' => 'Engreg School', 'place' => 'Lagos', 'quote' => 'Our teachers now focus on what they do best: teaching and mentoring.', 'stats' => [['15 hrs', 'saved per week'], ['98%', 'parent engagement']]],
    ];
}

function partner_schools(): array
{
    return ['Engreg School', 'Nazareth School', 'St. Philip Catholic School', 'Oren School'];
}

function site_stats(): array
{
    return [
        ['value' => 4, 'suffix' => '', 'label' => 'Partner schools'],
        ['value' => 1000, 'suffix' => '+', 'label' => 'Students reached'],
        ['value' => 3, 'suffix' => '', 'label' => 'Running projects'],
        ['value' => 98, 'suffix' => '%', 'label' => 'Satisfaction rate'],
    ];
}

function team(): array
{
    return [
        ['name' => 'Christopher Oyeh', 'role' => 'Founder & CEO · Full-stack Developer', 'image' => 'images/CEO.jpeg', 'bio' => 'Visionary leader with 10+ years in EdTech, driving innovation and digital transformation in education, and a hands-on full-stack developer behind HLTS platforms.'],
        ['name' => 'Israel Akinola', 'role' => 'Community Leader, TechMind Africa · Full-stack Developer', 'image' => 'images/2027images/ISRAEL.png', 'bio' => 'Leads TechMind Africa and supports learning programmes that help learners and educators build practical digital skills, while building web solutions as a full-stack developer.'],
        ['name' => 'Chike Ukem', 'role' => 'HR', 'image' => 'images/chike-ukem.jpg', 'bio' => 'Leads people and welfare at HLTS, from recruitment and onboarding to supporting the staff deployed in our partner schools.'],
        ['name' => 'Osi Emmanuel', 'role' => 'Lead Supervisor', 'image' => 'images/OSI.jpeg', 'bio' => 'Leads the supervision of HLTS staff in partner schools, keeping teaching, lab work and reporting on track.'],
    ];
}

function faqs(): array
{
    return [
        'General' => [
            ['What is HLTS Limited?', 'HLTS Limited is a Lagos-based EdTech company. We support primary and secondary schools with technology, operations and staff, run the HLTS Online Institution, host the TechMind Africa community, and build digital products.'],
            ['How do I contact support?', 'Use the <a href="/contact.html">contact page</a>, email CEO@hltsltd.com, or chat with us on WhatsApp. Partner schools can also <a href="/it-support.html">log a support request</a>.'],
            ['How secure is data?', 'Information is stored securely with role-based access, regular backups and encrypted connections. We never sell personal data or student records.'],
        ],
        'For schools' => [
            ['How do I get started as a school?', '<a href="/book-demo.html">Book a demo</a> or <a href="/school-form.html">register your school</a>. We visit or meet online, understand your needs, then propose the right mix of services.'],
            ['Can we choose only some services?', 'Yes. Every module (CBT, results, IT support, operations, staff, labs) can be taken on its own or combined into a package.'],
            ['How long does onboarding take?', 'Typically one to two weeks, depending on the size of the school and how much existing data needs to be moved.'],
            ['Do you train our staff?', 'Yes. Onboarding includes on-site or virtual training for administrators and teachers.'],
            ['How do parents check results?', 'Parents use the <a href="/results.html">results checker</a> with the student ID and PIN provided by the school.'],
        ],
        'For learners' => [
            ['How do I register for a course?', 'Choose a course on the <a href="/course.html">courses page</a> and complete the registration form. Our team confirms your place and next steps.'],
            ['How do I pay course fees?', 'You can pay online by card, bank transfer or USSD through Paystack at the end of registration, or pay later after speaking with our team.'],
            ['Do I get a certificate?', 'Yes. Learners who complete a course receive a certificate with a unique number that anyone can <a href="/verify-certificate.html">verify online</a>.'],
            ['What devices do I need?', 'A laptop is best for most courses. The learning portal works on any modern phone, tablet or computer.'],
        ],
        'TechMind Africa' => [
            ['Is TechMind Africa free to join?', 'Yes. <a href="/join-techmind.html">Join the community</a> to hear about meetups, projects and opportunities.'],
            ['Can I volunteer or mentor?', 'Yes. Choose "Mentor" or "Volunteer" when you join and the community team will reach out.'],
        ],
    ];
}

/** Main navigation, grouped by business line. Used by the header and footer. */
function nav_groups(): array
{
    return [
        'schools' => [
            'label' => 'For Schools',
            'icon' => 'building',
            'match' => ['services', 'school-management', 'cbt', 'staff-deployment', 'school-form', 'book-demo', 'it-support', 'results'],
            'items' => [
                ['services', 'School Solutions', 'Everything HLTS does for schools', 'grid-1x2'],
                ['school-management', 'School Management', 'Pick the modules your school needs', 'diagram-3'],
                ['cbt', 'CBT & Assessments', 'Secure computer-based exams', 'ui-checks-grid'],
                ['staff-deployment', 'Staff Deployment', 'Vetted staff placed in your school', 'person-workspace'],
                ['results', 'Check Results', 'For parents and students', 'clipboard-data'],
                ['it-support', 'IT Support Request', 'For partner schools', 'headset'],
            ],
        ],
        'learn' => [
            'label' => 'Online Institution',
            'icon' => 'mortarboard',
            'match' => ['online-institution', 'course', 'course-detail', 'registration-form', 'verify-certificate', 'portal', 'student'],
            'items' => [
                ['online-institution', 'About the Institution', 'How learning with HLTS works', 'laptop'],
                ['course', 'Courses', 'Programmes and fees', 'journal-text'],
                ['registration-form', 'Register', 'Start your learning journey', 'person-plus'],
                ['portal', 'Student Portal', 'Sign in to your courses', 'person-badge'],
                ['verify-certificate', 'Verify a Certificate', 'Check a certificate number', 'patch-check'],
            ],
        ],
        'build' => [
            'label' => 'Digital Solutions',
            'icon' => 'code-slash',
            'match' => ['digital-solutions', 'portfolio', 'request-quote'],
            'items' => [
                ['digital-solutions', 'What We Build', 'Websites, apps, portals and design', 'code-slash'],
                ['portfolio', 'Portfolio', 'Projects we have delivered', 'collection'],
                ['request-quote', 'Request a Quote', 'Tell us about your project', 'chat-square-quote'],
            ],
        ],
        'community' => [
            'label' => 'TechMind Africa',
            'icon' => 'globe2',
            'match' => ['community', 'events', 'join-techmind'],
            'items' => [
                ['community', 'The Community', 'Learn together, build together', 'globe2'],
                ['events', 'Events', 'Meetups, workshops and hackathons', 'calendar-event'],
                ['join-techmind', 'Join TechMind', 'Free to join', 'people'],
            ],
        ],
        'company' => [
            'label' => 'Company',
            'icon' => 'info-circle',
            'match' => ['about', 'blog', 'post', 'careers', 'faq', 'contact', 'terms'],
            'items' => [
                ['about', 'About HLTS', 'Our mission and people', 'building-check'],
                ['blog', 'Insights', 'Ideas on education and technology', 'newspaper'],
                ['careers', 'Careers', 'Work with HLTS', 'briefcase'],
                ['https://hlts-hr.vercel.app/', 'Staff Portal', 'For HLTS staff', 'person-lock'],
                ['faq', 'FAQs', 'Answers to common questions', 'question-circle'],
                ['contact', 'Contact', 'Talk to our team', 'envelope'],
            ],
        ],
    ];
}
