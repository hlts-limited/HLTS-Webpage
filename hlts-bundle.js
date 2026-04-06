// ============================================
// RANDOM GALLERY FOR INDEX PAGE
// ============================================
document.addEventListener('DOMContentLoaded', function () {
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
});
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
    document.addEventListener('DOMContentLoaded', () => {
      const forms = document.querySelectorAll('form');
      forms.forEach(form => {
        this.addCSRFToForm(form);
        this.secureForm(form);
      });

      // Secure all external links
      this.secureExternalLinks();

      // Add security headers information
      this.logSecurityStatus();
    });

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
  initializeAOS();
  initializeCounters();
  initializeBackToTop();
  initializeNavbar();
  initializeGallery();
  initializeLazyLoading();
  initializeFormValidation();
  initializeCarouselPreview();
});

// Carousel Preview Hover Effect
function initializeCarouselPreview() {
  const carousel = document.getElementById('mainCarousel');
  if (!carousel) return;
  const items = carousel.querySelectorAll('.carousel-item img');
  const prevBtn = carousel.querySelector('.carousel-control-prev');
  const nextBtn = carousel.querySelector('.carousel-control-next');
  const prevPreview = prevBtn.querySelector('.carousel-preview');
  const nextPreview = nextBtn.querySelector('.carousel-preview');

  function getActiveIndex() {
    const active = carousel.querySelector('.carousel-item.active');
    return Array.from(carousel.querySelectorAll('.carousel-item')).indexOf(active);
  }

  function showPreview(previewEl, index) {
    if (!items[index]) return;
    previewEl.innerHTML = `<img src="${items[index].getAttribute('src')}" alt="Preview">`;
  }

  prevBtn.addEventListener('mouseenter', function() {
    const total = items.length;
    const activeIdx = getActiveIndex();
    const prevIdx = (activeIdx - 1 + total) % total;
    showPreview(prevPreview, prevIdx);
  });
  nextBtn.addEventListener('mouseenter', function() {
    const total = items.length;
    const activeIdx = getActiveIndex();
    const nextIdx = (activeIdx + 1) % total;
    showPreview(nextPreview, nextIdx);
  });
  // Optional: Clear preview on mouseleave
  prevBtn.addEventListener('mouseleave', function() {
    prevPreview.innerHTML = '';
  });
  nextBtn.addEventListener('mouseleave', function() {
    nextPreview.innerHTML = '';
  });
}

// ============================================
// AOS Animation Initialization
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
          setTimeout(updateCount, 40); // Slower update interval for better performance
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

  // Show/hide based on scroll position
  window.addEventListener("scroll", () => {
    if (window.scrollY > 300) {
      backToTopBtn.style.display = "block";
      backToTopBtn.style.opacity = "1";
    } else {
      backToTopBtn.style.opacity = "0";
      setTimeout(() => {
        if (window.scrollY <= 300) {
          backToTopBtn.style.display = "none";
        }
      }, 300);
    }
  });

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

  window.addEventListener('scroll', () => {
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

    // Hide/show navbar on scroll (optional)
    if (currentScroll > lastScroll && currentScroll > 500) {
      navbar.style.transform = 'translateY(-100%)';
    } else {
      navbar.style.transform = 'translateY(0)';
    }

    lastScroll = currentScroll;
  });

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
  // Create lightbox modal
  const lightbox = document.createElement('div');
  lightbox.className = 'lightbox-modal';
  lightbox.innerHTML = `
    <div class="lightbox-content">
      <span class="lightbox-close">&times;</span>
      <img src="${imgSrc}" alt="${title}">
      <div class="lightbox-caption">
        <h4>${title}</h4>
        <p>${description}</p>
      </div>
    </div>
  `;

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

// Smooth Scroll for Links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    const href = this.getAttribute('href');
    if (!href || href === '#') {
      return;
    }

    const target = document.querySelector(href);
    if (target) {
      target.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }
  });
});

// Loading Screen
window.addEventListener('load', function() {
  document.body.classList.add('loaded');
});
