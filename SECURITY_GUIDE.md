# HLTS Website - Security Guide

## 🔒 Overview

This guide outlines the security measures implemented for the HLTS website and best practices for maintaining a secure web presence.

## 📋 Table of Contents

1. [Security Features Implemented](#security-features-implemented)
2. [Frontend Security](#frontend-security)
3. [Backend Security](#backend-security)
4. [Server Configuration](#server-configuration)
5. [Best Practices](#best-practices)
6. [Security Checklist](#security-checklist)
7. [Incident Response](#incident-response)

---

## 🛡️ Security Features Implemented

### 1. Cross-Site Scripting (XSS) Protection

**What it protects against**: Malicious scripts injected into your website

**Implementation**:
- Input sanitization in `security.js`
- HTML escaping for user inputs
- Content Security Policy (CSP) headers

**Usage**:
```javascript
// Sanitize user input
const safeInput = HLTSSecurity.sanitizeInput(userInput);

// Sanitize HTML
const safeHTML = HLTSSecurity.sanitizeHTML(htmlContent);
```

### 2. Cross-Site Request Forgery (CSRF) Protection

**What it protects against**: Unauthorized commands transmitted from a user the website trusts

**Implementation**:
- CSRF token generation and validation
- Automatic token insertion in forms
- Session-based token storage

**Usage**:
```javascript
// Automatically added to all forms on page load
// Token stored in sessionStorage
// Validated on form submission
```

### 3. SQL Injection Prevention

**What it protects against**: Malicious SQL code injection

**Implementation**:
- Input escaping functions
- Backend parameterized queries (recommended)

**Backend Example (PHP)**:
```php
// Use prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
```

### 4. Rate Limiting

**What it protects against**: Brute force attacks, spam, DoS

**Implementation**:
- Client-side rate limiting in `security.js`
- Tracks attempts per action
- Configurable time windows

**Usage**:
```javascript
if (!HLTSSecurity.rateLimiter.isAllowed('login', 5, 60)) {
  // Block action - too many attempts
}
```

### 5. Secure Session Management

**What it protects against**: Session hijacking, unauthorized access

**Implementation**:
- Encrypted session storage
- Secure token handling
- Session timeout

**Usage**:
```javascript
// Store secure data
HLTSSecurity.session.set('user_data', userData);

// Retrieve secure data
const userData = HLTSSecurity.session.get('user_data');
```

### 6. Security Headers

**What it protects against**: Various attack vectors

**Implementation** (in `.htaccess`):
- `X-Frame-Options: SAMEORIGIN` - Prevents clickjacking
- `X-XSS-Protection: 1; mode=block` - Enables XSS filter
- `X-Content-Type-Options: nosniff` - Prevents MIME sniffing
- `Strict-Transport-Security` - Enforces HTTPS
- `Content-Security-Policy` - Controls resource loading

---

## 🔐 Frontend Security

### Input Validation

**Email Validation**:
```javascript
if (!HLTSSecurity.validateEmail(email)) {
  alert('Please enter a valid email address');
  return false;
}
```

**Phone Validation** (Nigerian format):
```javascript
if (!HLTSSecurity.validatePhone(phone)) {
  alert('Please enter a valid Nigerian phone number');
  return false;
}
```

**Password Validation**:
```javascript
const validation = HLTSSecurity.validatePassword(password);
if (!validation.valid) {
  console.log('Password must contain:');
  if (!validation.minLength) console.log('- At least 8 characters');
  if (!validation.hasUpperCase) console.log('- Uppercase letter');
  if (!validation.hasLowerCase) console.log('- Lowercase letter');
  if (!validation.hasNumbers) console.log('- Number');
  if (!validation.hasSpecialChar) console.log('- Special character');
  return false;
}
```

### Secure Forms

All forms automatically:
1. Get CSRF tokens added
2. Have inputs sanitized on blur
3. Are rate-limited on submission
4. Validate on client and server side

---

## 🗄️ Backend Security

### PHP Security Best Practices

**1. Validate and Sanitize Inputs**:
```php
// In send-registration.php
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

$name = sanitizeInput($_POST['name']);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
```

**2. Validate CSRF Token**:
```php
session_start();

// Check CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    die('CSRF validation failed');
}
```

**3. Use Prepared Statements**:
```php
// Never do this
$query = "SELECT * FROM users WHERE email = '$email'"; // VULNERABLE!

// Always do this
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
```

**4. Secure Password Handling**:
```php
// Hash passwords
$hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Verify passwords
if (password_verify($inputPassword, $hashedPassword)) {
    // Password correct
}
```

**5. Error Handling**:
```php
// Don't reveal sensitive information
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/php-errors.log');
```

---

## ⚙️ Server Configuration

### Apache (.htaccess)

The `.htaccess` file includes:

1. **Force HTTPS**
2. **Security Headers**
3. **Directory Listing Prevention**
4. **File Protection**
5. **Compression**
6. **Caching**
7. **Bot Blocking**

### Nginx Configuration

If using Nginx, create `/etc/nginx/sites-available/hlts`:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com;

    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    root /var/www/hlts;
    index index.html;

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php7.4-fpm.sock;
    }

    # Block access to sensitive files
    location ~ /\. {
        deny all;
    }
}
```

---

## 🎯 Best Practices

### 1. HTTPS Everywhere

**Always use HTTPS** for your website:
- Obtain SSL/TLS certificate (free from Let's Encrypt)
- Force HTTPS redirects
- Enable HSTS

```bash
# Get free SSL with Let's Encrypt
sudo certbot --apache -d yourdomain.com
```

### 2. Regular Updates

- Keep server software updated
- Update PHP, MySQL, Apache/Nginx
- Monitor security advisories

```bash
# Ubuntu/Debian
sudo apt update && sudo apt upgrade

# Check for security updates
sudo apt list --upgradable | grep security
```

### 3. Strong Passwords

**Enforce for users**:
- Minimum 8 characters
- Mix of upper/lowercase
- Numbers and symbols
- No common passwords

**For admin accounts**:
- Use 16+ character passwords
- Use password manager
- Enable 2FA where possible

### 4. Database Security

```sql
-- Create limited privilege user
CREATE USER 'hlts_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT SELECT, INSERT, UPDATE ON hlts_db.* TO 'hlts_user'@'localhost';
FLUSH PRIVILEGES;

-- Never use root user for web applications
```

### 5. File Permissions

```bash
# Set correct permissions
chmod 644 *.html *.css *.js
chmod 755 images/
chmod 600 .htaccess
chmod 600 config files

# PHP files
chmod 644 *.php
```

### 6. Backup Strategy

```bash
# Daily automated backups
0 2 * * * /usr/local/bin/backup-hlts.sh

# Backup script should include:
# - Database dump
# - Website files
# - Store off-site (cloud storage)
# - Keep 30 days of backups
```

### 7. Monitoring

Set up monitoring for:
- Failed login attempts
- Unusual traffic patterns
- Error logs
- File changes
- SSL certificate expiry

**Log Analysis**:
```bash
# Check access logs for suspicious activity
tail -f /var/log/apache2/access.log | grep "POST"

# Check error logs
tail -f /var/log/apache2/error.log
```

---

## ✅ Security Checklist

### Pre-Launch Checklist

- [ ] SSL certificate installed and configured
- [ ] HTTPS redirect working
- [ ] Security headers implemented
- [ ] CSRF protection enabled
- [ ] Input validation on all forms
- [ ] SQL injection prevention (parameterized queries)
- [ ] XSS protection implemented
- [ ] Rate limiting configured
- [ ] Error messages don't reveal sensitive info
- [ ] File upload restrictions (if applicable)
- [ ] Admin area protected (if applicable)
- [ ] Database credentials secured
- [ ] Backup system configured
- [ ] Security monitoring enabled

### Monthly Security Review

- [ ] Review access logs
- [ ] Check for failed login attempts
- [ ] Update all software/libraries
- [ ] Test backup restoration
- [ ] Review user permissions
- [ ] Scan for vulnerabilities
- [ ] Check SSL certificate validity
- [ ] Review security headers
- [ ] Test forms for vulnerabilities
- [ ] Check for broken links

### Security Testing

```bash
# Test SSL configuration
curl -I https://yourdomain.com

# Test security headers
curl -I https://yourdomain.com | grep -i "x-"

# Test for common vulnerabilities
nikto -h https://yourdomain.com
```

---

## 🚨 Incident Response

### If You Suspect a Breach

1. **Don't Panic**
   - Document everything
   - Take screenshots
   - Save logs

2. **Immediate Actions**
   ```bash
   # Take site offline temporarily
   # Change all passwords
   # Review access logs
   tail -1000 /var/log/apache2/access.log
   
   # Check for unauthorized files
   find /var/www/hlts -type f -mtime -7
   ```

3. **Investigation**
   - Check database for unauthorized changes
   - Review user accounts
   - Scan for malware
   - Check file integrity

4. **Recovery**
   - Restore from clean backup
   - Update all software
   - Patch vulnerabilities
   - Reset all passwords

5. **Prevention**
   - Analyze attack vector
   - Implement additional security
   - Document incident
   - Train team

### Emergency Contacts

- **Hosting Provider Support**: [Contact Info]
- **Security Team**: security@hltsltd.com
- **Backup Contact**: [Emergency Contact]

---

## 🔧 Implementation Guide

### Step 1: Add Security JavaScript

Add to all HTML pages before closing `</body>`:

```html
<!-- Security Module -->
<script src="security.js"></script>
```

### Step 2: Update PHP Backend

Update `send-registration.php`:

```php
<?php
session_start();

// CSRF Protection
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    die('Security validation failed');
}

// Sanitize inputs
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Process form...
?>
```

### Step 3: Configure Server

1. Upload `.htaccess` to root directory
2. Verify permissions
3. Test HTTPS redirect
4. Verify security headers

```bash
# Test security headers
curl -I https://yourdomain.com
```

### Step 4: Test Everything

- Test forms with various inputs
- Try SQL injection attempts (on test environment)
- Verify CSRF protection
- Check rate limiting
- Test on different browsers

---

## 📚 Additional Resources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Mozilla Web Security Guidelines](https://infosec.mozilla.org/guidelines/web_security)
- [Let's Encrypt](https://letsencrypt.org/)
- [Security Headers Check](https://securityheaders.com/)
- [SSL Test](https://www.ssllabs.com/ssltest/)

---

## 📞 Support

For security concerns:
- **Email**: security@hltsltd.com
- **Emergency**: +234 810 700 5789

**Report vulnerabilities responsibly to our security team.**

---

**Remember**: Security is an ongoing process, not a one-time setup. Regular updates and monitoring are essential!

**Last Updated**: December 2025
