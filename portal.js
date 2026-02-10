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
  
  // Test credentials for demo
  const testUsers = [
    { id: 'student@hlts.com', password: 'demo123', name: 'John Doe' },
    { id: 'HLTS001', password: 'demo123', name: 'John Doe' },
    { id: 'admin', password: 'admin', name: 'Admin User' },
    { id: 'test', password: 'test', name: 'Test Student' }
  ];
  
  // Show loading state
  const submitBtn = document.querySelector('.portal-form button[type="submit"]');
  const originalText = submitBtn.innerHTML;
  submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Signing In...';
  submitBtn.disabled = true;
  
  // Simulate API call
  setTimeout(() => {
    // Check against test credentials
    const user = testUsers.find(u => 
      (u.id.toLowerCase() === studentId.toLowerCase()) && u.password === password
    );
    
    if (user) {
      // Store session
      if (rememberMe) {
        localStorage.setItem('hlts_student_id', studentId);
      }
      sessionStorage.setItem('hlts_user', JSON.stringify(user));
      
      // Show success message
      showNotification(`Welcome back, ${user.name}! Redirecting...`, 'success');
      
      // Redirect to dashboard
      setTimeout(() => {
        window.location.href = 'portal_interface.html';
      }, 1500);
    } else {
      showNotification('Invalid Student ID or Password. Try: test / test', 'error');
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    }
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
    const target = document.querySelector(this.getAttribute('href'));
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
