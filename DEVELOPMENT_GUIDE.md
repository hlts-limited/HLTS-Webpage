# HLTS Website - Development & Deployment Guide

## 📋 Table of Contents
1. [Development Setup](#development-setup)
2. [Architecture Overview](#architecture-overview)
3. [Component Documentation](#component-documentation)
4. [Deployment Guide](#deployment-guide)
5. [Maintenance & Updates](#maintenance--updates)
6. [Troubleshooting](#troubleshooting)

---

## 🛠️ Development Setup

### Local Environment

#### Option 1: Using Python
```bash
cd HLTS-Webpage
python -m http.server 8000
# Visit http://localhost:8000
```

#### Option 2: Using PHP
```bash
cd HLTS-Webpage
php -S localhost:8000
# Visit http://localhost:8000
``` neweed

#### Option 3: Using VS Code Live Server
1. Install "Live Server" extension
2. Right-click on `index.html`
3. Select "Open with Live Server"

### Development Tools Recommended
- **VS Code** with extensions:
  - Live Server
  - HTML CSS Support
  - JavaScript (ES6) code snippets
  - Prettier - Code formatter
  - Auto Rename Tag

---

## 🏗️ Architecture Overview

### Design Patterns Used

#### 1. Component-Based Structure
Each major section is a self-contained component:
```
Navigation Component → navbar
Hero Component → carousel-section
Features Component → values-section
Gallery Component → meeting-gallery-section
Portal Component → portal pages
Footer Component → footer
```

#### 2. Modern CSS Architecture
```
CSS Variables (Design Tokens)
    ↓
Base Styles
    ↓
Layout Components
    ↓
Feature Components
    ↓
Utility Classes
    ↓
Responsive Overrides
```

#### 3. Progressive Enhancement
- HTML provides structure
- CSS adds presentation
- JavaScript enhances interactivity
- Works even if JS is disabled (basic functionality)

### File Dependencies

```
index.html
├── Bootstrap CSS (CDN)
├── Bootstrap Icons (CDN)
├── Font Awesome (CDN)
├── Google Fonts (CDN)
├── AOS CSS (CDN)
├── style.css (Custom)
├── Bootstrap JS (CDN)
├── AOS JS (CDN)
└── script.js (Custom)

portal.html
├── [Same as above]
├── portal.css (Custom)
└── portal.js (Custom)
```

---

## 📚 Component Documentation

### Navigation Component

**File**: All HTML files
**Styles**: `style.css` lines 170-280
**JavaScript**: `script.js` - `initializeNavbar()`

**Features**:
- Fixed position with scroll effects
- Mobile responsive collapse
- Active link highlighting
- Smooth transitions

**Customization**:
```html
<!-- Add new nav item -->
<li class="nav-item me-3">
  <a class="nav-link" href="your-page.html">
    <i class="bi bi-your-icon"></i> YOUR LINK
  </a>
</li>
```

### Hero Carousel

**File**: `index.html` lines 80-145
**Styles**: `style.css` lines 282-390
**JavaScript**: Bootstrap Carousel (automatic)

**Add New Slide**:
```html
<div class="carousel-item">
  <img src="images/your-slide.jpg" class="d-block w-100" alt="Description">
  <div class="carousel-caption d-none d-md-block">
    <h5>Your Title</h5>
    <p>Your description text here.</p>
    <a href="#" class="btn btn-primary mt-3">Button Text</a>
  </div>
</div>
```

### Feature Cards

**File**: `index.html` lines 180-250
**Styles**: `style.css` lines 430-550
**JavaScript**: None required

**Add New Feature**:
```html
<div class="col-6">
  <div class="feature-box bg-light p-3 rounded shadow-sm">
    <i class="bi bi-your-icon"></i>
    <h6>Feature Title</h6>
    <p>Feature description here.</p>
  </div>
</div>
```

### Meeting Gallery

**File**: `index.html` (new section added)
**Styles**: `style.css` lines 570-700
**JavaScript**: `script.js` - `initializeGallery()`

**Add Gallery Item**:
```html
<div class="gallery-item" data-aos="zoom-in" data-aos-delay="100">
  <img src="images/your-image.jpg" alt="Description">
  <div class="gallery-overlay">
    <h5>Image Title</h5>
    <p>Image description</p>
  </div>
</div>
```

**Featured Item** (spans 2x2 grid):
```html
<div class="gallery-item featured" data-aos="zoom-in">
  <!-- Same content as above -->
</div>
```

### Counter Animation

**File**: Any page with `.counter` elements
**JavaScript**: `script.js` - `initializeCounters()`

**Usage**:
```html
<span class="counter" data-target="1500">0</span>
```
Will animate from 0 to 1500 when scrolled into view.

### Student Portal

**Files**: 
- `portal.html` - Main portal page
- `portal.css` - Portal styles
- `portal.js` - Portal functionality

**Key Features**:
- Login form with validation
- Password visibility toggle
- Remember me functionality
- Loading states
- Notification system

**Login Flow**:
1. User enters credentials
2. Client-side validation
3. Show loading state
4. Simulate API call (replace with actual)
5. Store session if "Remember Me"
6. Redirect to dashboard

---

## 🚀 Deployment Guide

### Pre-Deployment Checklist

- [ ] Test all pages on multiple browsers
- [ ] Verify mobile responsiveness
- [ ] Check all links and navigation
- [ ] Optimize images (compress)
- [ ] Update meta tags and descriptions
- [ ] Test form submissions
- [ ] Verify external CDN links
- [ ] Check console for errors
- [ ] Test loading performance
- [ ] Backup current version

### Deployment Options

#### Option 1: Traditional Web Hosting

1. **Prepare Files**:
```bash
# Create deployment folder
mkdir hlts-deploy
cp -r *.html *.css *.js images/ bootstrap-5.3.8-dist/ hlts-deploy/
```

2. **Upload via FTP/SFTP**:
- Use FileZilla or similar
- Connect to your hosting
- Upload all files to `public_html` or `www`
- Set permissions: 644 for files, 755 for folders

3. **Verify Deployment**:
- Visit your domain
- Test all pages
- Check browser console
- Test on mobile

#### Option 2: GitHub Pages (Free)

1. **Push to GitHub**:
```bash
git add .
git commit -m "Deploy modern HLTS website"
git push origin main
```

2. **Enable GitHub Pages**:
- Go to repository settings
- Find "Pages" section
- Select branch: `main`
- Select folder: `/ (root)`
- Save

3. **Access**:
- URL: `https://hlts-limited.github.io/HLTS-Webpage/`

#### Option 3: Netlify (Recommended)

1. **Install Netlify CLI**:
```bash
npm install -g netlify-cli
```

2. **Deploy**:
```bash
cd HLTS-Webpage
netlify deploy --prod
```

3. **Configure**:
- Set custom domain
- Enable HTTPS
- Configure redirects if needed

**netlify.toml** (optional):
```toml
[build]
  publish = "."

[[redirects]]
  from = "/*"
  to = "/index.html"
  status = 200
```

#### Option 4: Vercel

1. **Install Vercel CLI**:
```bash
npm install -g vercel
```

2. **Deploy**:
```bash
cd HLTS-Webpage
vercel --prod
```

### Performance Optimization

#### Image Optimization
```bash
# Install ImageMagick
# Optimize images
mogrify -format jpg -quality 85 -path images/ images/*.jpg
mogrify -format png -quality 85 -path images/ images/*.png
```

#### Minification

**CSS Minification**:
```bash
# Using npm package
npm install -g clean-css-cli
cleancss -o style.min.css style.css
cleancss -o portal.min.css portal.css
```

**JavaScript Minification**:
```bash
# Using npm package
npm install -g terser
terser script.js -o script.min.js -c -m
terser portal.js -o portal.min.js -c -m
```

Update HTML to use minified versions:
```html
<link rel="stylesheet" href="style.min.css">
<script src="script.min.js"></script>
```

---

## 🔄 Maintenance & Updates

### Regular Updates

#### Weekly:
- [ ] Check for broken links
- [ ] Review contact form submissions
- [ ] Monitor website analytics
- [ ] Check for console errors

#### Monthly:
- [ ] Update content (blog posts, news)
- [ ] Review and update gallery images
- [ ] Check CDN version updates
- [ ] Backup website files

#### Quarterly:
- [ ] Full design review
- [ ] Performance audit
- [ ] Security check
- [ ] Update dependencies

### Content Updates

#### Adding Blog Posts

1. Update `index.html` in blog section:
```html
<div class="col-md-4">
  <div class="card h-100 shadow-sm">
    <img src="images/new-post.jpg" class="card-img-top" alt="Post">
    <div class="card-body">
      <h5 class="card-title">Your Post Title</h5>
      <p class="card-text">Post excerpt...</p>
      <a href="blog-post-url.html" class="btn btn-outline-primary btn-sm">
        Read More
      </a>
    </div>
  </div>
</div>
```

#### Updating Gallery

1. Add new images to `/images/` folder
2. Update `index.html` gallery section
3. Optimize images before upload

#### Changing Colors/Branding

Edit CSS variables in `style.css`:
```css
:root {
  --primary-color: #YourColor;
  --secondary-color: #YourColor;
  /* etc. */
}
```

---

## 🐛 Troubleshooting

### Common Issues

#### Issue: Carousel Not Working
**Solution**:
1. Check Bootstrap JS is loaded
2. Verify carousel structure
3. Check console for errors
4. Ensure unique carousel ID

#### Issue: AOS Animations Not Working
**Solution**:
1. Verify AOS library loaded
2. Check initialization in script.js
3. Test with `data-aos="fade-up"` attribute
4. Check browser compatibility

#### Issue: Gallery Lightbox Not Opening
**Solution**:
1. Check if JavaScript is enabled
2. Verify gallery items have correct class
3. Check console for errors
4. Test click event listeners

#### Issue: Mobile Menu Not Collapsing
**Solution**:
1. Verify Bootstrap JS loaded
2. Check toggler button attributes
3. Ensure collapse target ID matches
4. Test on actual mobile device

#### Issue: Forms Not Submitting
**Solution**:
1. Check form action attribute
2. Verify PHP file exists (send-registration.php)
3. Check server PHP support
4. Review browser console for errors

### Performance Issues

#### Slow Loading
**Check**:
- Image sizes (compress if >500KB)
- CDN response times
- Number of HTTP requests
- Unused CSS/JS

**Fix**:
```bash
# Optimize images
# Minify CSS/JS
# Enable caching
# Use CDN for libraries
```

#### Layout Shifts
**Fix**:
- Add explicit width/height to images
- Preload critical fonts
- Avoid inserting content above existing content

---

## 📊 Analytics Setup

### Google Analytics

Add before `</head>`:
```html
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'GA_MEASUREMENT_ID');
</script>
```

### Track Custom Events

```javascript
// Track button clicks
document.querySelector('.contact-btn').addEventListener('click', () => {
  gtag('event', 'click', {
    'event_category': 'engagement',
    'event_label': 'contact_button'
  });
});
```

---

## 🔐 Security Best Practices

1. **Form Security**:
   - Implement server-side validation
   - Use CSRF tokens
   - Sanitize all inputs

2. **HTTPS**:
   - Always use SSL certificate
   - Force HTTPS redirects

3. **Headers**:
   Add security headers to server config:
   ```
   X-Frame-Options: SAMEORIGIN
   X-Content-Type-Options: nosniff
   X-XSS-Protection: 1; mode=block
   ```

4. **Updates**:
   - Keep frameworks updated
   - Monitor security advisories
   - Regular security audits

---

## 📞 Support

For technical support or questions:
- **Email**: info@hltsltd.com
- **Phone**: +234 810 700 5789
- **GitHub Issues**: [Report issues here](https://github.com/hlts-limited/HLTS-Webpage/issues)

---

**Last Updated**: December 2025
**Version**: 2.0.0
**Maintained by**: HLTS Limited Development Team
