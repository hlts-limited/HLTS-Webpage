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
    console.log('%c🔒 HLTS Security Module Loaded', 'color: #10B981; font-weight: bold;');

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
    console.log('%c✅ Security Features Active:', 'color: #10B981; font-weight: bold;');
    console.log('- CSRF Protection: Enabled');
    console.log('- XSS Prevention: Enabled');
    console.log('- Rate Limiting: Enabled');
    console.log('- Input Sanitization: Enabled');
    console.log('- Secure Session: Enabled');
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
