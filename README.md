# HLTS Limited Website

## Overview

This repository contains the HLTS Limited public website and supporting portal, dashboard, registration, and security pages. The project has been updated into a more complete education and service platform, with shared styling, bundled assets, and documentation for future maintenance.

## Current Project State

The site now includes:

- A modern public homepage in [index.php](index.php)
- Company information pages in [about.php](about.php), [services.php](services.php), [course.php](course.php), [contact.php](contact.php), [faq.php](faq.php), and [terms.php](terms.php)
- Education-specific pages in [cbt.php](cbt.php), [online-institution.php](online-institution.php), [community.php](community.php), [registration-form.php](registration-form.php), and [school-form.php](school-form.php)
- A "launching soon" student portal page in [portal.php](portal.php)
- Demo dashboards in [portal_interface.html](portal_interface.html), [admin_dashboard.html](admin_dashboard.html), and [security-dashboard.html](security-dashboard.html). These are blocked on the live server until they have real logins.
- Server-side form handling in [send-registration.php](send-registration.php)
- A shared head, navigation and footer in [partials/](partials/)
- Shared styles in [css/](css/) and shared scripts in [hlts-bundle.js](hlts-bundle.js)
- Supporting documentation in [DEVELOPMENT_GUIDE.md](DEVELOPMENT_GUIDE.md), [FEATURE_SUMMARY.md](FEATURE_SUMMARY.md), [SECURITY_GUIDE.md](SECURITY_GUIDE.md), [SECURITY_QUICK_REFERENCE.md](SECURITY_QUICK_REFERENCE.md), [SECURITY_SUMMARY.md](SECURITY_SUMMARY.md), and [TESTING_CHECKLIST.md](TESTING_CHECKLIST.md)

## Change Log For Future Reference

This section records the major changes already made to the project so future updates can stay consistent with the current structure.

### Website Redesign

- Refreshed the public site with a more modern layout and consistent branding
- Added a shared design system using CSS variables, spacing consistency, shadows, and responsive layout rules
- Improved navigation, section spacing, and visual hierarchy across the main pages
- Added performance-oriented asset loading patterns such as preconnects and optimized external dependencies

### Homepage And Public Pages

- Updated the homepage experience in [index.html](index.html) to use a more polished landing-page layout
- Added or refined the supporting public pages: [about.html](about.html), [services.html](services.html), [course.html](course.html), and [contact.html](contact.html)
- Added the CBT offering page in [cbt.html](cbt.html) to present exam platform services
- Added reusable footer and navigation patterns across pages for a consistent user experience

### Portal And Dashboard Work

- Added the student login experience in [portal.html](portal.html)
- Added the student dashboard interface in [portal_interface.html](portal_interface.html)
- Added the admin-facing dashboard in [admin_dashboard.html](admin_dashboard.html)
- Included portal-oriented content for login, feature highlights, quick actions, schedules, grades, and announcements
- Wired the portal flow so the student experience can connect to admin-managed schedule and course data

### Registration And Form Handling

- Added [registration-form.html](registration-form.html) for user enrollment
- Added [school-form.html](school-form.html) for school-related submissions
- Added [send-registration.php](send-registration.php) as the backend form handler
- Documented the registration and verification flow so form submissions can be maintained safely

### Security Work

- Added [security-dashboard.html](security-dashboard.html) for monitoring and review of security status
- Added [SECURITY_GUIDE.md](SECURITY_GUIDE.md), [SECURITY_QUICK_REFERENCE.md](SECURITY_QUICK_REFERENCE.md), and [SECURITY_SUMMARY.md](SECURITY_SUMMARY.md) to document the security layer
- Added [.htaccess](.htaccess) rules for production hardening on Apache hosts
- Documented input validation, CSRF handling, rate limiting, and other defensive measures used by the project

### Shared Assets And Bundles

- Consolidated site scripting into [hlts-bundle.js](hlts-bundle.js)
- Kept the Bootstrap 5.3.8 distribution in [bootstrap-5.3.8-dist/](bootstrap-5.3.8-dist/)
- Kept image assets in [images/](images/)

### Shared Page Structure (October 2026)

- Public pages are now PHP files (`about.php`) that keep their `.html` addresses (`/about.html`). [.htaccess](.htaccess) maps one to the other, so old links and search results still work. Keep linking to `.html` in page content.
- The `<head>`, navigation and footer live once in [partials/](partials/) and are included by every public page. Edit them there, not in individual pages.
- The single 10,000-line stylesheet was split into ordered files in [css/](css/), with all design tokens in [css/tokens.css](css/tokens.css). The old magenta and violet theme colours now point at the brand coral and blue.

## File Map

### Public Pages

Each public page sets its title and description at the top, then includes the shared partials.

- [index.php](index.php) - Main public homepage
- [about.php](about.php) - Company overview
- [services.php](services.php) - School solutions
- [cbt.php](cbt.php) - CBT and assessments
- [online-institution.php](online-institution.php) - Online Institution overview
- [course.php](course.php) - Course offerings and fees
- [community.php](community.php) - TechMind Africa
- [contact.php](contact.php) - Contact page
- [faq.php](faq.php) - Frequently asked questions
- [terms.php](terms.php) - Terms of service and privacy policy
- [portal.php](portal.php) - Student portal (launching soon)

### Demo Dashboards (blocked on the live server)

- [portal_interface.html](portal_interface.html) - Student dashboard interface
- [admin_dashboard.html](admin_dashboard.html) - Admin dashboard
- [security-dashboard.html](security-dashboard.html) - Security dashboard

### Forms And Server Logic

- [registration-form.php](registration-form.php) - Student registration form
- [school-form.php](school-form.php) - School registration form
- [send-registration.php](send-registration.php) - Processes both forms (`form_type` field says which)

### Shared Resources

- [partials/head.php](partials/head.php) - `<head>`, meta tags and stylesheets
- [partials/nav.php](partials/nav.php) - Main navigation, grouped by business line
- [partials/footer.php](partials/footer.php) - Footer, newsletter and site scripts
- [css/](css/) - Stylesheets, loaded in this order: `tokens`, `base`, `components`, `pages`, `dashboard-student`, `pages-extra`, `design-layer`, `dashboard-admin`, `responsive`
- [hlts-bundle.js](hlts-bundle.js) - Consolidated JavaScript
- [bootstrap-5.3.8-dist/](bootstrap-5.3.8-dist/) - Bootstrap framework assets
- [images/](images/) - Media assets
- [dev-router.php](dev-router.php) - Local preview router (not served on the live site)

### Documentation

- [README.md](README.md) - Project overview and reference
- [DEVELOPMENT_GUIDE.md](DEVELOPMENT_GUIDE.md) - Development notes
- [FEATURE_SUMMARY.md](FEATURE_SUMMARY.md) - Feature-level summary
- [SECURITY_GUIDE.md](SECURITY_GUIDE.md) - Security implementation guide
- [SECURITY_QUICK_REFERENCE.md](SECURITY_QUICK_REFERENCE.md) - Security checklist
- [SECURITY_SUMMARY.md](SECURITY_SUMMARY.md) - Security summary
- [TESTING_CHECKLIST.md](TESTING_CHECKLIST.md) - Testing checklist

## Tech Stack

- HTML5
- CSS3
- JavaScript
- PHP (pages and form handling)
- Bootstrap 5.3.8
- Bootstrap Icons
- AOS animations
- Google Fonts

## Local Preview

The pages are PHP, so they need PHP to preview. Opening the files directly in a browser, or using a plain static server, will not show the navigation or footer.

1. Install PHP 8 (for example from https://windows.php.net/download or with XAMPP).
2. From the project folder, run:

```bash
php -S localhost:8000 dev-router.php
```

3. Visit `http://localhost:8000`.

The router does locally what `.htaccess` does on the live server: `/about.html` serves `about.php`.

## Maintenance Notes

- Update this README whenever a new page, asset, workflow, or security change is added.
- Keep the file map in sync with the root of the repository.
- Add a new entry to the change log whenever the project structure changes in a meaningful way.

## Team

- Christopher Oyeh - Founder/CEO
- Joseph Amos - General Supervisor
- Nnamdi Osi - Deputy Supervisor
- Israel Akinola - Software Engineer
- Collin Duru - Chief Engineer

## Contact

- Website: [www.hltslimited.com](https://www.hltslimited.com)
- Email: info@hltsltd.com
- Phone: +234 810 700 5789
- Address: 8 Assembly Close, Folagoro, Somolu, Lagos, Nigeria

## License

© 2026 HLTS Limited. All rights reserved.
