# HLTS Limited Website

The public website, admin area and student portal for HLTS Limited: EdTech for primary and secondary schools, school operations, the HLTS Online Institution, the TechMind Africa community and HLTS digital solutions.

## What's here

| Area | Pages |
|---|---|
| Home | `index.php`: animated "connected learning" hero, audience chooser, ecosystem tabs, live CBT demo, testimonials |
| For schools | `services.php`, `school-management.php`, `cbt.php`, `staff-deployment.php`, `school-form.php`, `book-demo.php`, `it-support.php`, `results.php` (parent result checker) |
| Online Institution | `online-institution.php`, `course.php`, `course-detail.php?c=…`, `registration-form.php` (with optional Paystack payment), `portal.php` + `student.php` (student portal), `verify-certificate.php` |
| Digital solutions | `digital-solutions.php`, `portfolio.php`, `request-quote.php` |
| TechMind Africa | `community.php`, `events.php`, `join-techmind.php` |
| Company | `about.php`, `blog.php` + `post.php`, `careers.php`, `faq.php`, `contact.php`, `terms.php` |
| Admin | `admin/`: leads, students, payments, results, certificates, articles, events, portfolio, jobs, course materials, staff accounts |
| Endpoints | `submit.php` (every public form), `payment-callback.php`, `paystack-webhook.php`, `sitemap.php` (served as `/sitemap.xml`), `error.php` |

Public pages keep their `.html` addresses (`/about.html` serves `about.php`), so old links still work.

## How it is built

- **PHP 8.1+**, no framework and no Composer. Every entry point starts with `require __DIR__ . '/lib/app.php';`.
- **`lib/`** holds the shared code:
  - `config.php`: settings, with local overrides from `config/config.local.php`
  - `db.php`: PDO for SQLite (local) or MySQL (live). Tables are created automatically on first connection.
  - `content.php`: courses, fees, school modules, packages, team, testimonials, FAQs and the menu. **Edit content here.**
  - `forms.php`: every public form defined once (fields, validation, email, what is saved)
  - `ui.php`: page layout and reusable components
  - `csrf.php`, `auth.php`, `ratelimit.php`, `mailer.php`, `paystack.php`, `results.php`
- **`partials/`**: shared `<head>`, header and menu, footer
- **`css/tokens.css`**: every colour, size, shadow and timing. All colours come from the HLTS logo (night navy, infinity indigo, violet, magenta, lavender, white). Never hard-code a colour elsewhere.
- **`css/ui/`**: `base`, `components`, `forms`, `sections`, `pages`, `motion`, `admin`
- **`js/`**: `boot.js` (runs first), `app.js` (menus, forms, search), `motion.js` (animations), `admin.js`
- **Bootstrap 5.3** for grid and accessible components; **Bootstrap Icons** for icons.

### Motion rules

Animations only move and fade things (`transform`, `opacity`). Small feedback takes 150–250 ms and content reveals take 400–800 ms. There is one standout scene per page: the network hero, the CBT demo, the result sheet, typing code, the device frames or the scroll path. Everything stops for visitors who have "reduce motion" switched on. Content is never hidden if scripts fail to load.

## Local preview

```bash
php -S localhost:8000 dev-router.php
```

Then open `http://localhost:8000`. Local preview uses SQLite (`storage/hlts.sqlite`) and writes emails to `storage/logs/mail.log` instead of sending them.

## Going live (cPanel)

1. Upload the files. `storage/` must be writable by PHP.
2. In cPanel create a **MySQL database and user**, and an email account such as `no-reply@hltsltd.com`.
3. Copy `config/config.example.php` to `config/config.local.php` and fill in:
   - `app_key` and `setup_token`: long random strings (`php -r "echo bin2hex(random_bytes(32));"`)
   - `db`: the MySQL details
   - `mail`: the SMTP login for `no-reply@hltsltd.com`
   - `paystack`: public and secret keys (optional; payments stay off until set)
4. Visit `https://hltsltd.com/admin/setup.php?token=YOUR_SETUP_TOKEN` to create the first staff account. Then remove `setup_token`.
5. In Paystack: **Settings → API Keys & Webhooks**, set the webhook URL to `https://hltsltd.com/paystack-webhook.php`.
6. Submit `https://hltsltd.com/sitemap.xml` in Google Search Console.

`config/`, `lib/`, `storage/` and `partials/` are blocked from the web by `.htaccess`.

## Everyday tasks

- **New enquiries** arrive by email and appear in **Admin → Leads**. Update the status and add notes as you follow up. Use **Export CSV** for reports.
- **Enrolling a learner:** open their registration lead and click **Enrol as student**. A student ID and temporary password are created and can be emailed to them.
- **Course materials:** go to **Admin → Course materials** and add links (Drive, YouTube, Classroom). Students see them in the portal.
- **Results:** go to **Admin → Results**, download the template, fill it in (one row per student, one column per subject), upload, then **print the PIN slips straight away**. PINs cannot be shown again, but you can issue a new one per student.
- **Certificates:** go to **Admin → Certificates** and issue one. Print the certificate number on the certificate; anyone can verify it at `/verify-certificate.html`.
- **Articles, events, portfolio and jobs:** create them in the admin, then set the status to **Published**. Example drafts marked "Example" are included; edit or delete them.
- **Course fees, school modules and FAQs:** edit `lib/content.php`.

## Security notes

- Server-side CSRF tokens on every form (one per session, so several tabs work).
- Validation on the server for every field, a honeypot for bots, and rate limits on forms, sign-in, the results checker and certificate checks.
- Passwords are hashed with `password_hash`. Sessions are renewed at sign-in and expire after 2 idle hours.
- Result PINs are stored as keyed hashes.
- Paystack amounts always come from the server; webhooks are signature-checked and payments re-verified with Paystack.
- Output is escaped everywhere. Admin-written articles use a safe text format, not raw HTML.
- There is a strict Content-Security-Policy with no inline scripts.

## Retired files

The rebuilt site no longer uses these. `.htaccess` blocks them, and they can be deleted:

- `admin_dashboard.html`, `portal_interface.html`, `security-dashboard.html` (replaced by `admin/` and `student.php`)
- `send-registration.php` (replaced by `submit.php`)
- `hlts-bundle.js` (replaced by `js/`)
- `css/base.css`, `css/components.css`, `css/pages.css`, `css/pages-extra.css`, `css/design-layer.css`, `css/dashboard-admin.css`, `css/dashboard-student.css`, `css/responsive.css`, `css/theme.css` (replaced by `css/tokens.css` and `css/ui/`)
- `bootstrap-5.3.8-dist/` (Bootstrap now loads from the jsDelivr CDN)
- `DEVELOPMENT_GUIDE.md`, `FEATURE_SUMMARY.md`, `SECURITY_*.md` and `TESTING_CHECKLIST.md` describe the old site

## Contact

- Website: [hltsltd.com](https://hltsltd.com)
- Email: CEO@hltsltd.com
- Phone: +234 810 700 5789
- Address: 8 Assembly Close, Folagoro, Somolu, Lagos, Nigeria

© HLTS Limited. All rights reserved.
