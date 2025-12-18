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
});

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

  const speed = 200;
  let hasAnimated = false;

  const animateCounters = () => {
    if (hasAnimated) return;
    hasAnimated = true;

    counters.forEach(counter => {
      const updateCount = () => {
        const target = +counter.getAttribute('data-target');
        const count = +counter.innerText;
        const increment = Math.ceil(target / speed);

        if (count < target) {
          counter.innerText = count + increment;
          setTimeout(updateCount, 20);
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

  window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;

    // Add shadow on scroll
    if (currentScroll > 50) {
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
// Console Welcome Message
// ============================================
console.log('%c🚀 Welcome to HLTS Limited', 'color: #002060; font-size: 24px; font-weight: bold;');
console.log('%cTransforming Education Through Innovation', 'color: #FF00FF; font-size: 14px;');

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
