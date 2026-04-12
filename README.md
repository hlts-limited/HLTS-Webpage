# HLTS Limited Website

## Overview

This repository contains the HLTS Limited public website and supporting portal, dashboard, registration, and security pages. The project has been updated into a more complete education and service platform, with shared styling, bundled assets, and documentation for future maintenance.

## Current Project State

The site now includes:

- A modern public homepage in [index.html](index.html)
- Company information pages in [about.html](about.html), [services.html](services.html), [course.html](course.html), and [contact.html](contact.html)
- Education-specific pages in [cbt.html](cbt.html), [registration-form.html](registration-form.html), and [school-form.html](school-form.html)
- Student and admin experiences in [portal.html](portal.html), [portal_interface.html](portal_interface.html), and [admin_dashboard.html](admin_dashboard.html)
- Security monitoring in [security-dashboard.html](security-dashboard.html)
- Server-side form handling in [send-registration.php](send-registration.php)
- Shared front-end assets in [hlts-bundle.css](hlts-bundle.css) and [hlts-bundle.js](hlts-bundle.js)
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

- Consolidated site styling into [hlts-bundle.css](hlts-bundle.css)
- Consolidated site scripting into [hlts-bundle.js](hlts-bundle.js)
- Kept the Bootstrap 5.3.8 distribution in [bootstrap-5.3.8-dist/](bootstrap-5.3.8-dist/)
- Kept image assets in [images/](images/)

## File Map

### Public Pages

- [index.html](index.html) - Main public homepage
- [about.html](about.html) - Company overview
- [services.html](services.html) - Services listing
- [course.html](course.html) - Course offerings
- [contact.html](contact.html) - Contact page
- [cbt.html](cbt.html) - CBT platform page

### Portal And Internal Pages

- [portal.html](portal.html) - Student portal login page
- [portal_interface.html](portal_interface.html) - Student dashboard interface
- [admin_dashboard.html](admin_dashboard.html) - Admin dashboard
- [security-dashboard.html](security-dashboard.html) - Security dashboard

### Forms And Server Logic

- [registration-form.html](registration-form.html) - Registration form
- [school-form.html](school-form.html) - School form
- [send-registration.php](send-registration.php) - Registration processing script

### Shared Resources

- [hlts-bundle.css](hlts-bundle.css) - Consolidated stylesheet
- [hlts-bundle.js](hlts-bundle.js) - Consolidated JavaScript
- [bootstrap-5.3.8-dist/](bootstrap-5.3.8-dist/) - Bootstrap framework assets
- [images/](images/) - Media assets

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
- PHP
- Bootstrap 5.3.8
- Bootstrap Icons
- AOS animations
- Google Fonts

## Local Preview

Open the site in a browser directly, or run it through a local web server for a more accurate preview.

### Example Local Servers

```bash
python -m http.server 8000
```

```bash
php -S localhost:8000
```

```bash
npx http-server
```

Then visit `http://localhost:8000`.

## Maintenance Notes

- Update this README whenever a new page, asset, workflow, or security change is added.
- Keep the file map in sync with the root of the repository.
- Add a new entry to the change log whenever the project structure changes in a meaningful way.

## Team

- Christopher Oyeh - Founder/CEO
- Joseph Amos - General Supervisor
- Nnamdi Osi - Deputy Supervisor
- Israel Akinola - Lead Developer
- Collin Duru - Chief Engineer

## Contact

- Website: [www.hltslimited.com](https://www.hltslimited.com)
- Email: info@hltsltd.com
- Phone: +234 810 700 5789
- Address: 8 Assembly Close, Folagoro, Somolu, Lagos, Nigeria

## License

© 2026 HLTS Limited. All rights reserved.
