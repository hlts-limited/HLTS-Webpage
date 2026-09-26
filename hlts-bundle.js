// ============================================
// RANDOM GALLERY FOR INDEX PAGE
// ============================================
function initializeRandomGallery() {
  var galleryImages = [
    {
      src: 'images/2025meeting/team.jpg',
      alt: 'HLTS Team Meeting - Main Session',
      title: 'Strategic Planning Session',
      desc: 'Our leadership team discussing future initiatives and growth strategies',
      featured: true
    },
    {
      src: 'images/2025meeting/team2.jpg',
      alt: 'Team Collaboration',
      title: 'Team Collaboration',
      desc: 'Team members brainstorming and collaborating on projects',
      featured: false
    },
    {
      src: 'images/2025meeting/CEO.jpg',
      alt: 'CEO Christopher Oyeh',
      title: 'Leadership Vision',
      desc: 'CEO Christopher Oyeh sharing the company\'s roadmap',
      featured: false
    },
    {
      src: 'images/2025meeting/Supervisor.jpeg',
      alt: 'Gen Supervisor Joseph Amos',
      title: 'Team Coordination',
      desc: 'Gen Supervisor Joseph Amos presenting operational updates',
      featured: false
    },
    {
      src: 'images/2025meeting/DepSuper.jpg',
      alt: 'Deputy Supervisor',
      title: 'Deputy Supervisor',
      desc: 'Deputy Supervisor engaging with the team',
      featured: false
    },
    {
      src: 'images/2025meeting/hlts.jpg',
      alt: 'HLTS Group',
      title: 'HLTS Group',
      desc: 'Group photo of HLTS team members',
      featured: false
    }
  ];

  function shuffle(array) {
    for (let i = array.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [array[i], array[j]] = [array[j], array[i]];
    }
    return array;
  }

  var shuffled = shuffle(galleryImages.slice());
  var selected = shuffled.slice(0, 3);
  // Always make the first image featured (bigger)
  if (selected.length > 0) {
    selected[0].featured = true;
    if (selected[1]) selected[1].featured = false;
    if (selected[2]) selected[2].featured = false;
  }
  var grid = document.getElementById('random-gallery-grid');
  if (grid) {
    grid.innerHTML = '';
    selected.forEach(function(img, idx) {
      var item = document.createElement('div');
      item.className = 'gallery-item' + (idx === 0 ? ' featured' : '');
      item.setAttribute('data-aos', 'zoom-in');
      item.setAttribute('data-aos-delay', 100 + idx * 100);

      var image = document.createElement('img');
      image.src = img.src;
      image.alt = img.alt;
      image.loading = 'lazy';
      item.appendChild(image);

      var overlay = document.createElement('div');
      overlay.className = 'gallery-overlay';
      var h5 = document.createElement('h5');
      h5.textContent = img.title;
      var p = document.createElement('p');
      p.textContent = img.desc;
      overlay.appendChild(h5);
      overlay.appendChild(p);
      item.appendChild(overlay);

      grid.appendChild(item);
    });
  }
}
// ============================================
// HLTS SECURITY MODULE
// ============================================

/**
 * Security utilities for HLTS website
 * Protects against XSS, CSRF, and other common vulnerabilities
 */

const HLTSSecurity = {
  
  // ============================================
  // XSS Protection - Input Sanitization
  // ============================================
  
  /**
   * Sanitize user input to prevent XSS attacks
   * @param {string} input - User input string
   * @returns {string} - Sanitized string
   */
  sanitizeInput: function(input) {
    if (typeof input !== 'string') return input;
    
    const div = document.createElement('div');
    div.textContent = input;
    return div.innerHTML;
  },

  /**
   * Sanitize HTML content
   * @param {string} html - HTML string
   * @returns {string} - Sanitized HTML
   */
  sanitizeHTML: function(html) {
    const tempDiv = document.createElement('div');
    tempDiv.textContent = html;
    return tempDiv.innerHTML
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#x27;')
      .replace(/\//g, '&#x2F;');
  },

  // ============================================
  // CSRF Protection
  // ============================================
  
  /**
   * Generate CSRF token
   * @returns {string} - CSRF token
   */
  generateCSRFToken: function() {
    const array = new Uint8Array(32);
    crypto.getRandomValues(array);
    return Array.from(array, byte => byte.toString(16).padStart(2, '0')).join('');
  },

  /**
   * Store CSRF token in session
   */
  setCSRFToken: function() {
    const token = this.generateCSRFToken();
    sessionStorage.setItem('csrf_token', token);
    document.cookie = `hlts_csrf_token=${token}; path=/; SameSite=Strict`;
    return token;
  },

  /**
   * Get CSRF token
   * @returns {string} - CSRF token
   */
  getCSRFToken: function() {
    let token = sessionStorage.getItem('csrf_token');
    if (!token) {
      token = this.setCSRFToken();
    }
    return token;
  },

  /**
   * Add CSRF token to form
   * @param {HTMLFormElement} form - Form element
   */
  addCSRFToForm: function(form) {
    const token = this.getCSRFToken();
    
    // Remove existing CSRF input if any
    const existingInput = form.querySelector('input[name="csrf_token"]');
    if (existingInput) {
      existingInput.value = token;
      return;
    }

    // Create new hidden input
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'csrf_token';
    input.value = token;
    form.appendChild(input);
  },

  // ============================================
  // Form Validation & Security
  // ============================================
  
  /**
   * Validate email format
   * @param {string} email - Email address
   * @returns {boolean} - Is valid
   */
  validateEmail: function(email) {
    const re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return re.test(String(email).toLowerCase());
  },

  /**
   * Validate phone number (Nigerian format)
   * @param {string} phone - Phone number
   * @returns {boolean} - Is valid
   */
  validatePhone: function(phone) {
    const re = /^(\+234|0)[7-9][0-1]\d{8}$/;
    return re.test(phone.replace(/\s/g, ''));
  },

  /**
   * Validate password strength
   * @param {string} password - Password
   * @returns {object} - Validation result
   */
  validatePassword: function(password) {
    const minLength = 8;
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumbers = /\d/.test(password);
    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);

    const isValid = password.length >= minLength && 
                    hasUpperCase && 
                    hasLowerCase && 
                    hasNumbers && 
                    hasSpecialChar;

    return {
      valid: isValid,
      minLength: password.length >= minLength,
      hasUpperCase,
      hasLowerCase,
      hasNumbers,
      hasSpecialChar,
      strength: this.getPasswordStrength(password)
    };
  },

  /**
   * Calculate password strength
   * @param {string} password - Password
   * @returns {string} - Strength level
   */
  getPasswordStrength: function(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;

    if (strength <= 2) return 'weak';
    if (strength <= 4) return 'medium';
    return 'strong';
  },

  /**
   * Sanitize and validate form data
   * @param {FormData} formData - Form data
   * @returns {object} - Sanitized data
   */
  sanitizeFormData: function(formData) {
    const sanitized = {};
    for (let [key, value] of formData.entries()) {
      sanitized[key] = this.sanitizeInput(value);
    }
    return sanitized;
  },

  // ============================================
  // Rate Limiting
  // ============================================
  
  rateLimiter: {
    attempts: {},
    
    /**
     * Check if action is rate limited
     * @param {string} action - Action identifier
     * @param {number} maxAttempts - Max attempts allowed
     * @param {number} timeWindow - Time window in seconds
     * @returns {boolean} - Is allowed
     */
    isAllowed: function(action, maxAttempts = 5, timeWindow = 60) {
      const now = Date.now();
      const key = action;

      if (!this.attempts[key]) {
        this.attempts[key] = [];
      }

      // Remove old attempts outside time window
      this.attempts[key] = this.attempts[key].filter(
        timestamp => now - timestamp < timeWindow * 1000
      );

      // Check if limit exceeded
      if (this.attempts[key].length >= maxAttempts) {
        return false;
      }

      // Record this attempt
      this.attempts[key].push(now);
      return true;
    },

    /**
     * Get remaining attempts
     * @param {string} action - Action identifier
     * @param {number} maxAttempts - Max attempts allowed
     * @returns {number} - Remaining attempts
     */
    getRemainingAttempts: function(action, maxAttempts = 5) {
      if (!this.attempts[action]) return maxAttempts;
      return Math.max(0, maxAttempts - this.attempts[action].length);
    }
  },

  // ============================================
  // SQL Injection Prevention (for backend)
  // ============================================
  
  /**
   * Escape SQL special characters
   * Note: This is a basic implementation. Use parameterized queries in backend!
   * @param {string} value - Input value
   * @returns {string} - Escaped value
   */
  escapeSQLInput: function(value) {
    if (typeof value !== 'string') return value;
    return value
      .replace(/'/g, "''")
      .replace(/"/g, '""')
      .replace(/\\/g, '\\\\')
      .replace(/\0/g, '\\0');
  },

  // ============================================
  // Secure Session Management
  // ============================================
  
  session: {
    /**
     * Set secure session data with encryption
     * @param {string} key - Session key
     * @param {any} value - Session value
     */
    set: function(key, value) {
      const data = JSON.stringify(value);
      const encoded = btoa(data); // Basic encoding, use proper encryption in production
      sessionStorage.setItem('hlts_' + key, encoded);
    },

    /**
     * Get secure session data
     * @param {string} key - Session key
     * @returns {any} - Session value
     */
    get: function(key) {
      const encoded = sessionStorage.getItem('hlts_' + key);
      if (!encoded) return null;
      try {
        const data = atob(encoded);
        return JSON.parse(data);
      } catch (e) {
        return null;
      }
    },

    /**
     * Remove session data
     * @param {string} key - Session key
     */
    remove: function(key) {
      sessionStorage.removeItem('hlts_' + key);
    },

    /**
     * Clear all session data
     */
    clear: function() {
      const keys = Object.keys(sessionStorage);
      keys.forEach(key => {
        if (key.startsWith('hlts_')) {
          sessionStorage.removeItem(key);
        }
      });
    }
  },

  // ============================================
  // Content Security
  // ============================================
  
  /**
   * Check if URL is safe (not a phishing or malicious link)
   * @param {string} url - URL to check
   * @returns {boolean} - Is safe
   */
  isSafeURL: function(url) {
    try {
      const parsedURL = new URL(url, window.location.origin);
      
      // Check protocol
      if (!['http:', 'https:', 'mailto:', 'tel:'].includes(parsedURL.protocol)) {
        return false;
      }

      // Check for javascript: protocol (XSS vector)
      if (parsedURL.protocol === 'javascript:') {
        return false;
      }

      // For external links, warn user
      if (parsedURL.origin !== window.location.origin) {
        return confirm(`You are about to visit an external site: ${parsedURL.hostname}\n\nDo you want to continue?`);
      }

      return true;
    } catch (e) {
      return false;
    }
  },

  // ============================================
  // Initialize Security
  // ============================================
  
  /**
   * Initialize security features on page load
   */
  init: function() {
    // Generate CSRF token
    this.setCSRFToken();

    // Add CSRF tokens to all forms
    const applyDOMSecurity = () => {
      const forms = document.querySelectorAll('form');
      forms.forEach(form => {
        this.addCSRFToForm(form);
        this.secureForm(form);
      });

      // Secure all external links
      this.secureExternalLinks();

      // Add security headers information
      this.logSecurityStatus();
    };

    // Apply immediately if DOM is ready, otherwise wait
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', applyDOMSecurity);
    } else {
      applyDOMSecurity();
    }

    // Handle form submissions
    document.addEventListener('submit', (e) => {
      const form = e.target;
      if (form.tagName === 'FORM') {
        this.handleFormSubmit(e);
      }
    });
  },

  /**
   * Secure individual form
   * @param {HTMLFormElement} form - Form element
   */
  secureForm: function(form) {
    // Add autocomplete attributes
    const emailInputs = form.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
      input.setAttribute('autocomplete', 'email');
    });

    const passwordInputs = form.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
      input.setAttribute('autocomplete', 'current-password');
    });

    // Add input sanitization on blur
    const textInputs = form.querySelectorAll('input[type="text"], input[type="email"], textarea');
    textInputs.forEach(input => {
      input.addEventListener('blur', () => {
        input.value = this.sanitizeInput(input.value);
      });
    });
  },

  /**
   * Handle form submission with security checks
   * @param {Event} e - Submit event
   */
  handleFormSubmit: function(e) {
    const form = e.target;
    const formId = form.id || form.name || 'unknown';

    // Rate limiting
    if (!this.rateLimiter.isAllowed('form_submit_' + formId, 3, 60)) {
      e.preventDefault();
      alert('Too many attempts. Please wait a moment before trying again.');
      return;
    }

    // Validate CSRF token
    const csrfInput = form.querySelector('input[name="csrf_token"]');
    if (csrfInput && csrfInput.value !== this.getCSRFToken()) {
      e.preventDefault();
      alert('Security validation failed. Please refresh the page and try again.');
      return;
    }
  },

  /**
   * Secure external links
   */
  secureExternalLinks: function() {
    const links = document.querySelectorAll('a[href^="http"]');
    links.forEach(link => {
      const url = new URL(link.href);
      if (url.origin !== window.location.origin) {
        link.setAttribute('rel', 'noopener noreferrer');
        link.setAttribute('target', '_blank');
      }
    });
  },

  /**
   * Log security status
   */
  logSecurityStatus: function() {
    // Security features active - CSRF, XSS, Rate Limiting, Sanitization, Sessions
  }
};

// Initialize security when script loads
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => HLTSSecurity.init());
} else {
  HLTSSecurity.init();
}

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
  module.exports = HLTSSecurity;
}
// ============================================
// HLTS MODERN WEBSITE - JAVASCRIPT
// ============================================

// Initialize on DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
  initializePublicNavigation();
  initializeRandomGallery();
  initializeAOS();
  initializeCounters();
  initializeBackToTop();
  initializeNavbar();
  initializeGallery();
  initializeLazyLoading();
  initializeFormValidation();
  initializeCarouselPreview();
  initializeEcosystemExplorer();
  initializeForUsersDetailModal();
  initializeTestimonialFolder();
});

function initializePublicNavigation() {
  const navbar = document.querySelector('nav.navbar');
  if (!navbar || document.body.classList.contains('dashboard-body') || document.body.classList.contains('admin-body')) return;

  const currentPage = window.location.pathname.split('/').pop() || 'index.html';
  const isCurrent = (pages) => pages.includes(currentPage) ? ' active' : '';

  navbar.innerHTML = `
    <div class="container-fluid public-nav-inner">
      <a class="navbar-brand" href="index.html" aria-label="HLTS Limited home">
        <img loading="lazy" src="images/logoh.png" alt="HLTS Logo" height="70">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Open navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="mainNavbar">
        <ul class="navbar-nav mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link${isCurrent(['index.html'])}" href="index.html"><i class="bi bi-house" aria-hidden="true"></i> Home</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle${isCurrent(['online-institution.html', 'course.html', 'registration-form.html'])}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-mortarboard" aria-hidden="true"></i> Learn</a>
            <ul class="dropdown-menu public-dropdown">
              <li><a class="dropdown-item" href="online-institution.html"><i class="bi bi-laptop"></i><span><strong>Online Institution</strong><small>Learn how HLTS Online Institution works</small></span></a></li>
              <li><a class="dropdown-item" href="course.html"><i class="bi bi-journal-text"></i><span><strong>Courses</strong><small>Explore available programmes</small></span></a></li>
              <li><a class="dropdown-item" href="registration-form.html"><i class="bi bi-person-plus"></i><span><strong>Register</strong><small>Start your learning journey</small></span></a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle${isCurrent(['school-form.html', 'services.html', 'cbt.html'])}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-grid" aria-hidden="true"></i> Solutions</a>
            <ul class="dropdown-menu public-dropdown">
              <li><a class="dropdown-item" href="school-form.html"><i class="bi bi-building"></i><span><strong>Academics</strong><small>Staff deployment for schools</small></span></a></li>
              <li><a class="dropdown-item" href="services.html"><i class="bi bi-diagram-3"></i><span><strong>School Operations</strong><small>Systems, support, and workflows</small></span></a></li>
              <li><a class="dropdown-item" href="cbt.html"><i class="bi bi-ui-checks-grid"></i><span><strong>CBT & Assessments</strong><small>Testing and result management</small></span></a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle${isCurrent(['community.html'])}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-people" aria-hidden="true"></i> Community</a>
            <ul class="dropdown-menu public-dropdown">
              <li><a class="dropdown-item" href="community.html"><i class="bi bi-globe2"></i><span><strong>TechMind Africa</strong><small>Connect, learn, and build together</small></span></a></li>
              <li><a class="dropdown-item" href="community.html#vision"><i class="bi bi-stars"></i><span><strong>Our Vision</strong><small>See what the community is building toward</small></span></a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle${isCurrent(['about.html', 'faq.html', 'terms.html', 'contact.html'])}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-info-circle" aria-hidden="true"></i> Company</a>
            <ul class="dropdown-menu public-dropdown">
              <li><a class="dropdown-item" href="about.html"><i class="bi bi-building-check"></i><span><strong>About HLTS</strong><small>Our mission and people</small></span></a></li>
              <li><a class="dropdown-item" href="faq.html"><i class="bi bi-question-circle"></i><span><strong>FAQs</strong><small>Answers to common questions</small></span></a></li>
              <li><a class="dropdown-item" href="contact.html"><i class="bi bi-envelope"></i><span><strong>Contact</strong><small>Let us plan your next step</small></span></a></li>
            </ul>
          </li>
          <li class="nav-item nav-portal-item"><a class="nav-link${isCurrent(['portal.html'])}" href="portal.html"><i class="bi bi-person-badge" aria-hidden="true"></i> Student Portal</a></li>
          <li class="nav-item"><a class="btn contact-btn" href="contact.html"><i class="bi bi-arrow-up-right" aria-hidden="true"></i> Talk to HLTS</a></li>
        </ul>
      </div>
    </div>`;
  const hoverNavigation = window.matchMedia('(min-width: 992px)');
  const dropdownItems = navbar.querySelectorAll('.nav-item.dropdown');

  const bindDropdownHover = () => {
    dropdownItems.forEach((item) => {
      const toggle = item.querySelector('[data-bs-toggle="dropdown"]');
      if (!toggle || item.dataset.hoverBound === 'true') return;

      const dropdown = bootstrap.Dropdown.getOrCreateInstance(toggle);
      let closeTimer;

      const openDropdown = () => {
        window.clearTimeout(closeTimer);
        dropdown.show();
      };

      const closeDropdown = () => {
        window.clearTimeout(closeTimer);
        closeTimer = window.setTimeout(() => dropdown.hide(), 120);
      };

      item.addEventListener('mouseenter', openDropdown);
      item.addEventListener('mouseleave', closeDropdown);
      item.dataset.hoverBound = 'true';
    });
  };

  const unbindDropdownHover = () => {
    dropdownItems.forEach((item) => {
      if (item.dataset.hoverBound !== 'true') return;
      const clone = item.cloneNode(true);
      item.replaceWith(clone);
    });
  };

  const updateDropdownHover = () => {
    if (hoverNavigation.matches) {
      bindDropdownHover();
    } else {
      unbindDropdownHover();
    }
  };

  updateDropdownHover();
  hoverNavigation.addEventListener('change', updateDropdownHover);
}

function initializeEcosystemExplorer() {
  const tabs = document.querySelectorAll('.ecosystem-tab');
  const data = {
    academics: {
      kicker: 'HLTS@School',
      heading: 'Build a stronger school team.',
      description: 'Deploy the right education professionals into your school and choose a support plan that fits your stage of growth.',
      link: 'Explore Academic Plans',
      href: 'school-form.html',
      icon: 'bi-mortarboard-fill',
      outcomes: ['Qualified academic staff', 'Three flexible service plans', 'Training and ongoing support']
    },
    operations: {
      kicker: 'HLTS Operations',
      heading: 'Make every school process work smarter.',
      description: 'Bring CBT, result management, IT support, and daily school operations into a more reliable digital workflow.',
      link: 'View Operations Services',
      href: 'services.html',
      icon: 'bi-diagram-3-fill',
      outcomes: ['CBT and secure assessments', 'Result management tools', 'Responsive IT support']
    },
    institution: {
      kicker: 'HLTS Online Institution',
      heading: 'Turn ambition into practical skills.',
      description: 'Learn with structured courses, expert guidance, and a digital environment built for the next generation of African talent.',
      link: 'Explore the Institution',
      href: 'registration-form.html',
      icon: 'bi-laptop-fill',
      outcomes: ['Career-ready courses', 'Expert mentorship', 'Flexible digital learning']
    },
    community: {
      kicker: 'TechMind Africa',
      heading: 'Give technology a bigger purpose.',
      description: 'Join a community turning curiosity into capability through access, collaboration, and real opportunities to build.',
      link: 'Meet Our Community',
      href: 'about.html',
      icon: 'bi-globe2',
      outcomes: ['Peer learning network', 'Community-led projects', 'Access to tech opportunities']
    }
  };

  const elements = {
    kicker: document.getElementById('ecosystem-kicker'),
    heading: document.getElementById('ecosystem-heading'),
    description: document.getElementById('ecosystem-description'),
    link: document.getElementById('ecosystem-link'),
    icon: document.getElementById('ecosystem-icon'),
    outcomes: [1, 2, 3].map((index) => document.getElementById(`ecosystem-outcome-${index}`))
  };

  if (!tabs.length || !elements.kicker) return;

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      const track = data[tab.dataset.ecosystemTrack];
      if (!track) return;

      tabs.forEach((item) => {
        const isActive = item === tab;
        item.classList.toggle('is-active', isActive);
        item.setAttribute('aria-selected', String(isActive));
      });

      elements.kicker.textContent = track.kicker;
      elements.heading.textContent = track.heading;
      elements.description.textContent = track.description;
      elements.link.childNodes[0].textContent = `${track.link} `;
      elements.link.href = track.href;
      elements.icon.className = `bi ${track.icon}`;
      elements.outcomes.forEach((outcome, index) => {
        outcome.textContent = track.outcomes[index];
      });
    });
  });
}

// Carousel Preview Hover Effect
function initializeCarouselPreview() {
  const carousel = document.getElementById('mainCarousel');
  if (!carousel) return;
  const slides = Array.from(carousel.querySelectorAll('.carousel-item'));
  const prevBtn = carousel.querySelector('.carousel-control-prev');
  const nextBtn = carousel.querySelector('.carousel-control-next');
  const prevPreview = prevBtn.querySelector('.carousel-preview');
  const nextPreview = nextBtn.querySelector('.carousel-preview');

  function getActiveIndex() {
    const active = carousel.querySelector('.carousel-item.active');
    return slides.indexOf(active);
  }

  function getSlidePreview(index) {
    const slide = slides[index];
    if (!slide) return null;
    const caption = slide.querySelector('.carousel-caption');
    const title = caption?.querySelector('h1, h2, h5')?.textContent.trim() || '';
    const description = caption?.querySelector('p')?.textContent.trim() || '';
    return { title, description };
  }

  function showPreview(previewEl, index) {
    const preview = getSlidePreview(index);
    if (!preview || (!preview.title && !preview.description)) {
      previewEl.innerHTML = '';
      return;
    }

    previewEl.innerHTML = '';
    const wrapper = document.createElement('div');
    wrapper.className = 'carousel-preview-content';
    const strong = document.createElement('strong');
    strong.textContent = preview.title;
    const p = document.createElement('p');
    p.textContent = preview.description;
    wrapper.appendChild(strong);
    wrapper.appendChild(p);
    previewEl.appendChild(wrapper);
  }

  function updatePreview(previewEl, index) {
    showPreview(previewEl, index);
  }

  prevBtn.addEventListener('mouseenter', function() {
    const total = slides.length;
    const activeIdx = getActiveIndex();
    const prevIdx = (activeIdx - 1 + total) % total;
    updatePreview(prevPreview, prevIdx);
  });

  nextBtn.addEventListener('mouseenter', function() {
    const total = slides.length;
    const activeIdx = getActiveIndex();
    const nextIdx = (activeIdx + 1) % total;
    updatePreview(nextPreview, nextIdx);
  });

  prevBtn.addEventListener('mouseleave', function() {
    prevPreview.innerHTML = '';
  });

  nextBtn.addEventListener('mouseleave', function() {
    nextPreview.innerHTML = '';
  });
}

function initializeForUsersDetailModal() {
  const buttons = document.querySelectorAll('.btn-learn-more');
  const modalEl = document.getElementById('forUsersDetailModal');
  const titleEl = document.getElementById('forUsersDetailModalLabel');
  const summaryEl = document.getElementById('forUsersDetailSummary');
  const listEl = document.getElementById('forUsersDetailList');
  if (!modalEl || !titleEl || !summaryEl || !listEl || !buttons.length) return;

  const details = {
    administrators: {
      title: 'For School Administrators',
      summary: 'HLTS provides school administrators with operational systems that boost efficiency, reduce manual work, and make decision-making data-driven across the whole institution.',
      buttonLink: 'school-form.html',
      items: [
        { icon: 'bi-bar-chart-line-fill', text: 'Enrollment tools: Seamlessly onboard new students with simplified digital registration.' },
        { icon: 'bi-globe2', text: 'Grading systems: Automate and manage grade calculations securely in one place.' },
        { icon: 'bi-gear-fill', text: 'Parent communication: Send updates, notices, and direct messages to parents easily.' },
        { icon: 'bi-person-badge-fill', text: 'Compliance dashboards: Track and fulfill regulatory requirements with real-time oversight.' }
      ]
    },
    teachers: {
      title: 'For Teachers & Educators',
      summary: 'Teachers gain modern classroom tools to design engaging lessons, give faster feedback, and manage student progress with less administrative overhead.',
      buttonLink: 'school-form.html',
      items: [
        { icon: 'bi-book-half', text: 'Lesson planning: Create, organize, and reuse interactive lesson plans.' },
        { icon: 'bi-pencil-square', text: 'Digital assignments: Distribute and collect work completely paper-free.' },
        { icon: 'bi-people-fill', text: 'Student progress tracking: Monitor individual performance with analytical tools.' },
        { icon: 'bi-award-fill', text: 'CBT tools: Build and deploy computer-based tests securely and automatically score them.' }
      ]
    },
    students: {
      title: 'For Students',
      summary: 'Students enjoy a personalized learning environment with instant access to lessons, assignments, support resources, and collaborative tools.',
      buttonLink: 'portal.html',
      items: [
        { icon: 'bi-stars', text: 'Access to lessons: Review course materials anytime, anywhere.' },
        { icon: 'bi-journal-album', text: 'Grade checking: See current standing and feedback on past work instantly.' },
        { icon: 'bi-people', text: 'Assignment submission: Upload homework and projects easily through the portal.' },
        { icon: 'bi-lightbulb-fill', text: 'Peer collaboration: Work together with classmates on interactive group tasks.' }
      ]
    },
    parents: {
      title: 'For Parents',
      summary: 'Parents stay involved and informed with clear progress tracking, improved communication, and practical support for supporting learning at home.',
      buttonLink: 'portal.html',
      items: [
        { icon: 'bi-eye-fill', text: 'Real-time grade monitoring: Stay updated on your child\'s performance effortlessly.' },
        { icon: 'bi-chat-dots-fill', text: 'Attendance tracking: Track daily attendance and punctuality records.' },
        { icon: 'bi-house-door-fill', text: 'Teacher messaging: Communicate directly with educators regarding your child\'s progress.' },
        { icon: 'bi-people-fill', text: 'School announcements: Receive important news and updates directly to your device.' }
      ]
    }
  };

  const getStartedBtn = document.getElementById('forUsersDetailGetStarted');
  const bsModal = new bootstrap.Modal(modalEl);

  buttons.forEach((button) => {
    button.addEventListener('click', function () {
      const key = this.getAttribute('data-card');
      const data = details[key];
      if (!data) return;

      titleEl.textContent = data.title;
      summaryEl.textContent = data.summary;
      listEl.innerHTML = data.items.map(item => {
        const [label, description] = item.text.split(': ');
        return `
          <div class="list-group-item detail-list-item border-0 px-0 py-2">
            <i class="bi ${item.icon} detail-item-icon"></i>
            <span><strong>${label}:</strong> ${description || ''}</span>
          </div>
        `;
      }).join('');
      if (getStartedBtn && data.buttonLink) {
        getStartedBtn.setAttribute('href', data.buttonLink);
      }

      bsModal.show();
    });
  });
}

// ============================================
// AOS Animation Initialization
// ============================================
// Desktop Folder with Single-Card View (shared)
// ============================================
function createDeskFolder(slotEl, tabLabel, theme, icon) {
  var cardRow = slotEl.querySelector('.row.g-4');
  if (!cardRow) return;

  var folder = document.createElement('div');
  folder.className = 'desk-folder desk-folder--' + theme;

  // Tab
  var tab = document.createElement('div');
  tab.className = 'desk-folder-tab';
  var tabSpan = document.createElement('span');
  tabSpan.textContent = tabLabel;
  tab.appendChild(tabSpan);

  // Body
  var body = document.createElement('div');
  body.className = 'desk-folder-body';

  // Front face
  var front = document.createElement('div');
  front.className = 'desk-folder-front';

  var iconEl = document.createElement('i');
  iconEl.className = 'bi bi-' + icon + ' folder-icon-display';
  front.appendChild(iconEl);

  var hint = document.createElement('span');
  hint.className = 'folder-hint';
  hint.textContent = 'Click to open';
  front.appendChild(hint);

  var cards = Array.from(cardRow.querySelectorAll(':scope > [class*="col-"]'));
  var badge = document.createElement('span');
  badge.className = 'folder-count';
  badge.textContent = cards.length + ' items';
  front.appendChild(badge);

  // Navigation
  var nav = document.createElement('div');
  nav.className = 'folder-nav';

  var prevBtn = document.createElement('button');
  prevBtn.className = 'folder-nav-btn';
  prevBtn.innerHTML = '<i class="bi bi-chevron-left"></i>';
  prevBtn.setAttribute('aria-label', 'Previous');

  var nextBtn = document.createElement('button');
  nextBtn.className = 'folder-nav-btn';
  nextBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
  nextBtn.setAttribute('aria-label', 'Next');

  var dotsWrap = document.createElement('div');
  dotsWrap.className = 'folder-nav-dots';

  var dots = [];
  cards.forEach(function(_, i) {
    var dot = document.createElement('button');
    dot.className = 'folder-nav-dot' + (i === 0 ? ' active' : '');
    dot.setAttribute('aria-label', 'Go to item ' + (i + 1));
    dot.addEventListener('click', function(e) {
      e.stopPropagation();
      showCard(i);
    });
    dotsWrap.appendChild(dot);
    dots.push(dot);
  });

  nav.appendChild(prevBtn);
  nav.appendChild(dotsWrap);
  nav.appendChild(nextBtn);

  // Assemble DOM
  slotEl.innerHTML = '';
  slotEl.appendChild(folder);
  folder.appendChild(tab);
  folder.appendChild(body);
  body.appendChild(cardRow);
  body.appendChild(front);
  body.appendChild(nav);

  // State
  var currentIndex = 0;
  if (cards.length > 0) cards[0].classList.add('folder-card-active');

  function showCard(idx) {
    cards[currentIndex].classList.remove('folder-card-active');
    dots[currentIndex].classList.remove('active');
    currentIndex = idx;
    cards[currentIndex].classList.add('folder-card-active');
    dots[currentIndex].classList.add('active');
  }

  prevBtn.addEventListener('click', function(e) {
    e.stopPropagation();
    showCard(currentIndex > 0 ? currentIndex - 1 : cards.length - 1);
  });

  nextBtn.addEventListener('click', function(e) {
    e.stopPropagation();
    showCard(currentIndex < cards.length - 1 ? currentIndex + 1 : 0);
  });

  front.addEventListener('click', function() {
    folder.classList.add('folder-open');
  });

  document.addEventListener('click', function(e) {
    if (!folder.contains(e.target)) {
      folder.classList.remove('folder-open');
    }
  });
}

function initializeTestimonialFolder() {
  var testimonialsSlot = document.getElementById('testimonials-folder-slot');
  if (testimonialsSlot) {
    createDeskFolder(testimonialsSlot, 'Reviews', 'amber', 'folder-fill');
  }

  var storiesSlot = document.getElementById('success-stories-folder-slot');
  if (storiesSlot) {
    createDeskFolder(storiesSlot, 'Stories', 'blue', 'folder2-open');
  }
}

// ============================================
function initializeAOS() {
  if (typeof AOS !== 'undefined') {
    AOS.init({
      duration: 800,
      easing: 'ease-in-out',
      once: true,
      offset: 100,
      delay: 50
    });
  }
}

// ============================================
// Counter Animation with Intersection Observer
// ============================================
function initializeCounters() {
  const counters = document.querySelectorAll('.counter');
  if (counters.length === 0) return;

  const speed = 100; // Lower is faster, higher is slower
  let hasAnimated = false;

  const animateCounters = () => {
    if (hasAnimated) return;
    hasAnimated = true;

    counters.forEach(counter => {
      const updateCount = () => {
        const target = +counter.getAttribute('data-target');
        const count = +counter.innerText;
        // Increase increment for faster counting
        const increment = Math.max(1, Math.ceil(target / speed));

        if (count < target) {
          counter.innerText = Math.min(count + increment, target);
          requestAnimationFrame(updateCount);
        } else {
          counter.innerText = target.toLocaleString();
        }
      };
      updateCount();
    });
  };

  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateCounters();
      }
    });
  }, { threshold: 0.3 });

  counters.forEach(counter => observer.observe(counter));
}

// ============================================
// Back to Top Button
// ============================================
function initializeBackToTop() {
  const backToTopBtn = document.getElementById("backToTop");
  if (!backToTopBtn) return;

  // Throttled scroll handler using requestAnimationFrame
  let ticking = false;
  window.addEventListener("scroll", () => {
    if (!ticking) {
      requestAnimationFrame(() => {
        if (window.scrollY > 300) {
          backToTopBtn.style.display = "block";
          backToTopBtn.style.opacity = "1";
        } else {
          backToTopBtn.style.opacity = "0";
          backToTopBtn.style.display = "none";
        }
        ticking = false;
      });
      ticking = true;
    }
  }, { passive: true });

  // Smooth scroll to top
  backToTopBtn.addEventListener("click", (e) => {
    e.preventDefault();
    window.scrollTo({ 
      top: 0, 
      behavior: "smooth" 
    });
  });
}

// ============================================
// Modern Navbar Effects
// ============================================
function initializeNavbar() {
  const navbar = document.querySelector('.navbar');
  if (!navbar) return;

  let lastScroll = 0;

  const heroSection = document.querySelector('section[class*="hero"], section.carousel-section');

  let navTicking = false;
  window.addEventListener('scroll', () => {
    if (!navTicking) {
      requestAnimationFrame(() => {
        const currentScroll = window.pageYOffset;
        const heroThreshold = heroSection
          ? heroSection.offsetTop + heroSection.offsetHeight - navbar.offsetHeight
          : 50;

        // Change navbar background after scrolling past the hero section
        if (currentScroll >= heroThreshold) {
          navbar.classList.add('navbar-scrolled');
        } else {
          navbar.classList.remove('navbar-scrolled');
        }

        // Hide/show navbar on scroll
        if (currentScroll > lastScroll && currentScroll > 500) {
          navbar.style.transform = 'translateX(-50%) translateY(-100%)';
        } else {
          navbar.style.transform = 'translateX(-50%) translateY(0)';
        }

        lastScroll = currentScroll;
        navTicking = false;
      });
      navTicking = true;
    }
  }, { passive: true });

  // Active link highlighting
  const navLinks = document.querySelectorAll('.nav-link');
  const currentPath = window.location.pathname;

  navLinks.forEach(link => {
    if (link.getAttribute('href') === currentPath.split('/').pop()) {
      link.classList.add('active');
    }
  });
}

// ============================================
// Gallery Lightbox Effect
// ============================================
function initializeGallery() {
  const galleryItems = document.querySelectorAll('.gallery-item');
  if (galleryItems.length === 0) return;

  galleryItems.forEach(item => {
    item.addEventListener('click', function() {
      const imgSrc = this.querySelector('img').src;
      const title = this.querySelector('.gallery-overlay h5')?.textContent || '';
      const description = this.querySelector('.gallery-overlay p')?.textContent || '';
      
      openLightbox(imgSrc, title, description);
    });
  });
}

function openLightbox(imgSrc, title, description) {
  // Create lightbox modal using DOM methods to prevent XSS
  const lightbox = document.createElement('div');
  lightbox.className = 'lightbox-modal';

  const content = document.createElement('div');
  content.className = 'lightbox-content';

  const closeSpan = document.createElement('span');
  closeSpan.className = 'lightbox-close';
  closeSpan.innerHTML = '&times;';

  const img = document.createElement('img');
  img.src = imgSrc;
  img.alt = title;

  const caption = document.createElement('div');
  caption.className = 'lightbox-caption';
  const h4 = document.createElement('h4');
  h4.textContent = title;
  const p = document.createElement('p');
  p.textContent = description;
  caption.appendChild(h4);
  caption.appendChild(p);

  content.appendChild(closeSpan);
  content.appendChild(img);
  content.appendChild(caption);
  lightbox.appendChild(content);

  document.body.appendChild(lightbox);
  document.body.style.overflow = 'hidden';

  // Animate in
  setTimeout(() => lightbox.classList.add('active'), 10);

  // Close handlers
  const closeBtn = lightbox.querySelector('.lightbox-close');
  closeBtn.addEventListener('click', () => closeLightbox(lightbox));
  lightbox.addEventListener('click', (e) => {
    if (e.target === lightbox) {
      closeLightbox(lightbox);
    }
  });

  // ESC key to close
  document.addEventListener('keydown', function escHandler(e) {
    if (e.key === 'Escape') {
      closeLightbox(lightbox);
      document.removeEventListener('keydown', escHandler);
    }
  });
}

function closeLightbox(lightbox) {
  lightbox.classList.remove('active');
  document.body.style.overflow = '';
  setTimeout(() => lightbox.remove(), 300);
}

// ============================================
// Lazy Loading Images
// ============================================
function initializeLazyLoading() {
  const images = document.querySelectorAll('img[data-src]');
  if (images.length === 0) return;

  const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.src = img.dataset.src;
        img.removeAttribute('data-src');
        observer.unobserve(img);
      }
    });
  });

  images.forEach(img => imageObserver.observe(img));
}

// ============================================
// Form Validation
// ============================================
function initializeFormValidation() {
  const forms = document.querySelectorAll('.needs-validation');
  
  forms.forEach(form => {
    form.addEventListener('submit', function(event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
}

// ============================================
// Smooth Scroll for Anchor Links
// ============================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    const href = this.getAttribute('href');
    if (href === '#') return;
    
    const target = document.querySelector(href);
    if (target) {
      e.preventDefault();
      const navbarHeight = document.querySelector('.navbar')?.offsetHeight || 0;
      const targetPosition = target.offsetTop - navbarHeight - 20;
      
      window.scrollTo({
        top: targetPosition,
        behavior: 'smooth'
      });
    }
  });
});

// ============================================
// Performance: Debounce Function
// ============================================
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// ============================================
// Show Page Load Progress
// ============================================
window.addEventListener('load', () => {
  document.body.classList.add('loaded');
  
  // Refresh AOS after all content loaded
  if (typeof AOS !== 'undefined') {
    AOS.refresh();
  }
});

// ============================================
// HLTS Bundle Loaded
// ============================================

// Add lightbox styles dynamically
const lightboxStyles = document.createElement('style');
lightboxStyles.textContent = `
  .lightbox-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.95);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
  }
  
  .lightbox-modal.active {
    opacity: 1;
  }
  
  .lightbox-content {
    position: relative;
    max-width: 90%;
    max-height: 90%;
    animation: zoomIn 0.3s ease;
  }
  
  .lightbox-content img {
    max-width: 100%;
    max-height: 80vh;
    border-radius: 12px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
  }
  
  .lightbox-close {
    position: absolute;
    top: -40px;
    right: 0;
    font-size: 40px;
    color: white;
    cursor: pointer;
    transition: transform 0.2s ease;
  }
  
  .lightbox-close:hover {
    transform: scale(1.2) rotate(90deg);
  }
  
  .lightbox-caption {
    text-align: center;
    color: white;
    margin-top: 20px;
  }
  
  .lightbox-caption h4 {
    color: white;
    font-size: 24px;
    margin-bottom: 8px;
  }
  
  .lightbox-caption p {
    color: rgba(255, 255, 255, 0.8);
    font-size: 16px;
  }
  
  @keyframes zoomIn {
    from {
      transform: scale(0.8);
      opacity: 0;
    }
    to {
      transform: scale(1);
      opacity: 1;
    }
  }
`;
document.head.appendChild(lightboxStyles);
// ============================================
// STUDENT PORTAL JAVASCRIPT
// ============================================

// Student Login Form Handler
document.addEventListener('DOMContentLoaded', function() {
  const loginForm = document.getElementById('studentLoginForm');
  
  if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
      e.preventDefault();
      handleLogin();
    });
  }
});

// Handle Login
function handleLogin() {
  const studentId = document.getElementById('studentId').value.trim();
  const password = document.getElementById('password').value;
  const rememberMe = document.getElementById('rememberMe').checked;

  if (!studentId || !password) {
    showNotification('Enter your student ID and password to continue.', 'error');
    return;
  }

  const submitBtn = document.querySelector('.portal-form button[type="submit"]');
  const originalText = submitBtn.innerHTML;
  submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Signing In...';
  submitBtn.disabled = true;
  
  setTimeout(() => {
    const user = {
      id: studentId,
      name: studentId,
      role: 'student'
    };

    if (rememberMe) {
      localStorage.setItem('hlts_student_id', studentId);
    } else {
      localStorage.removeItem('hlts_student_id');
    }

    sessionStorage.setItem('hlts_user', JSON.stringify(user));
    showNotification('Login successful. Opening your dashboard...', 'success');

    setTimeout(() => {
      window.location.href = 'portal_interface.html';
    }, 1200);
  }, 1500);
}

// Show Notification
function showNotification(message, type = 'info') {
  // Create notification element
  const notification = document.createElement('div');
  notification.className = `portal-notification ${type}`;
  notification.innerHTML = `
    <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : 'info-circle'}"></i>
    <span>${message}</span>
  `;
  
  // Add to body
  document.body.appendChild(notification);
  
  // Animate in
  setTimeout(() => {
    notification.classList.add('show');
  }, 100);
  
  // Remove after 3 seconds
  setTimeout(() => {
    notification.classList.remove('show');
    setTimeout(() => {
      notification.remove();
    }, 300);
  }, 3000);
}

// Check for saved student ID
window.addEventListener('load', function() {
  const savedStudentId = localStorage.getItem('hlts_student_id');
  if (savedStudentId) {
    const studentIdInput = document.getElementById('studentId');
    if (studentIdInput) {
      studentIdInput.value = savedStudentId;
      document.getElementById('rememberMe').checked = true;
    }
  }
});

// Add notification styles dynamically
const style = document.createElement('style');
style.textContent = `
  .portal-notification {
    position: fixed;
    top: 100px;
    right: -400px;
    background: white;
    padding: 1rem 1.5rem;
    border-radius: 0.75rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    z-index: 9999;
    transition: right 0.3s ease-out;
    max-width: 350px;
  }
  
  .portal-notification.show {
    right: 20px;
  }
  
  .portal-notification i {
    font-size: 1.5rem;
  }
  
  .portal-notification.success {
    border-left: 4px solid #10B981;
  }
  
  .portal-notification.success i {
    color: #10B981;
  }
  
  .portal-notification.error {
    border-left: 4px solid #EF4444;
  }
  
  .portal-notification.error i {
    color: #EF4444;
  }
  
  .portal-notification.info {
    border-left: 4px solid #3B82F6;
  }
  
  .portal-notification.info i {
    color: #3B82F6;
  }
  
  .portal-notification span {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
  }
`;
document.head.appendChild(style);

// Form Validation
function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(String(email).toLowerCase());
}

// Enhanced Password Toggle with Animation
function togglePassword() {
  const passwordInput = document.getElementById('password');
  const toggleBtn = document.querySelector('.toggle-password i');
  if (!passwordInput || !toggleBtn) {
    return;
  }
  
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    toggleBtn.classList.remove('bi-eye');
    toggleBtn.classList.add('bi-eye-slash');
    passwordInput.style.animation = 'fadeIn 0.3s ease-out';
  } else {
    passwordInput.type = 'password';
    toggleBtn.classList.remove('bi-eye-slash');
    toggleBtn.classList.add('bi-eye');
    passwordInput.style.animation = 'fadeIn 0.3s ease-out';
  }
}

// Loading Screen
window.addEventListener('load', function() {
  document.body.classList.add('loaded');
});
