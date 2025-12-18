# 🔒 HLTS Website Security - Quick Reference

## ✅ Immediate Actions Required

### 1. **Update .htaccess**
- [ ] Replace `yourdomain.com` with your actual domain in `.htaccess`
- [ ] Test HTTPS redirect: `curl -I http://yourdomain.com`
- [ ] Verify security headers: `curl -I https://yourdomain.com`

### 2. **Get SSL Certificate**
```bash
# Using Let's Encrypt (free)
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com
```

### 3. **Configure PHP Backend**
Edit `send-registration.php`:
- [ ] Update database connection details securely
- [ ] Configure error log path
- [ ] Set up email notifications
- [ ] Test CSRF protection

### 4. **Set File Permissions**
```bash
cd /var/www/html/HLTS-Webpage
chmod 644 *.html *.css *.js
chmod 755 images/
chmod 600 .htaccess
chmod 644 *.php
```

### 5. **Test Security Features**
- [ ] Try submitting forms multiple times (rate limiting)
- [ ] Check if CSRF tokens are working
- [ ] Test input sanitization
- [ ] Verify HTTPS redirects

---

## 🛡️ Security Features Active

| Feature | Status | File |
|---------|--------|------|
| CSRF Protection | ✅ Enabled | `security.js` |
| XSS Prevention | ✅ Enabled | `security.js` |
| Input Sanitization | ✅ Enabled | `security.js`, PHP |
| Rate Limiting | ✅ Enabled | `security.js`, PHP |
| SQL Injection Prevention | ✅ Enabled | PHP backend |
| Security Headers | ✅ Enabled | `.htaccess` |
| HTTPS Enforcement | ⚠️ Needs SSL | `.htaccess` |
| Session Security | ✅ Enabled | `security.js` |

---

## 🚀 Quick Usage

### Client-Side (JavaScript)

```javascript
// Validate email
if (!HLTSSecurity.validateEmail(email)) {
  alert('Invalid email');
}

// Validate password
const validation = HLTSSecurity.validatePassword(password);
if (!validation.valid) {
  // Show password requirements
}

// Sanitize input
const safeInput = HLTSSecurity.sanitizeInput(userInput);

// Check rate limit
if (!HLTSSecurity.rateLimiter.isAllowed('action', 5, 60)) {
  alert('Too many attempts');
}
```

### Server-Side (PHP)

```php
// In your PHP files
require_once 'send-registration.php';

// Check rate limit
checkRateLimit();

// Validate CSRF
validateCSRF();

// Sanitize input
$name = sanitizeInput($_POST['name']);
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

// Use prepared statements
$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
$stmt->execute([$name, $email]);
```

---

## 🔐 Password Requirements

For user accounts, enforce:
- ✅ Minimum 8 characters
- ✅ At least 1 uppercase letter
- ✅ At least 1 lowercase letter
- ✅ At least 1 number
- ✅ At least 1 special character

---

## 📊 Security Testing Tools

```bash
# Test SSL configuration
curl -I https://yourdomain.com

# Check security headers
curl -I https://yourdomain.com | grep -E "X-|Strict-Transport-Security|Content-Security-Policy"

# Test for common vulnerabilities
nikto -h https://yourdomain.com

# Check SSL rating
# Visit: https://www.ssllabs.com/ssltest/

# Check security headers
# Visit: https://securityheaders.com/
```

---

## 🚨 Warning Signs to Monitor

Watch for these in your logs:
- ⚠️ Multiple failed login attempts from same IP
- ⚠️ Unusual POST requests
- ⚠️ Requests with suspicious user agents
- ⚠️ 403/403 errors spike
- ⚠️ Large file uploads
- ⚠️ SQL keywords in request parameters

```bash
# Monitor access log
tail -f /var/log/apache2/access.log

# Monitor error log
tail -f /var/log/apache2/error.log

# Check for suspicious activity
grep "POST" /var/log/apache2/access.log | grep -i "union\|select\|drop\|insert"
```

---

## 📞 Emergency Response

If you detect a security breach:

1. **Immediate Actions**
   ```bash
   # Take site offline
   echo "Maintenance mode" > /var/www/html/index.html
   
   # Change all passwords
   # Check logs
   tail -1000 /var/log/apache2/access.log > breach-log.txt
   ```

2. **Contact**
   - Security Team: security@hltsltd.com
   - Hosting Support: [Your hosting provider]
   - Emergency: +234 810 700 5789

3. **Document Everything**
   - Take screenshots
   - Save logs
   - Note timeline
   - Record changes made

---

## 🔄 Regular Maintenance

### Daily
- [ ] Check error logs
- [ ] Monitor traffic patterns

### Weekly
- [ ] Review failed login attempts
- [ ] Check for suspicious files
- [ ] Update content

### Monthly
- [ ] Update all software
- [ ] Review security logs
- [ ] Test backups
- [ ] Check SSL certificate
- [ ] Run security scan

### Quarterly
- [ ] Full security audit
- [ ] Password policy review
- [ ] Penetration testing
- [ ] Update security documentation

---

## 📚 Important Files

| File | Purpose | Action Required |
|------|---------|-----------------|
| `security.js` | Client-side security | ✅ Include in all pages |
| `.htaccess` | Server security config | ✅ Update domain name |
| `send-registration.php` | Secure form handler | ✅ Configure database |
| `SECURITY_GUIDE.md` | Full documentation | 📖 Read thoroughly |

---

## 💡 Best Practices Reminder

1. ✅ **Always use HTTPS** - No exceptions
2. ✅ **Validate all inputs** - Client and server side
3. ✅ **Use prepared statements** - For all database queries
4. ✅ **Keep software updated** - PHP, MySQL, libraries
5. ✅ **Regular backups** - Automated daily backups
6. ✅ **Strong passwords** - For all accounts
7. ✅ **Monitor logs** - Check regularly for issues
8. ✅ **Limit privileges** - Use least privilege principle
9. ✅ **Test security** - Regular security audits
10. ✅ **Have incident plan** - Know what to do if breached

---

## 🎓 Training Resources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Web Security Basics](https://web.dev/secure/)
- [PHP Security Guide](https://www.php.net/manual/en/security.php)
- [Let's Encrypt Docs](https://letsencrypt.org/docs/)

---

## ✨ Quick Win Checklist

Complete these for immediate security improvement:

- [ ] Install SSL certificate
- [ ] Enable HTTPS redirect
- [ ] Add `security.js` to all pages
- [ ] Set correct file permissions
- [ ] Enable security headers (`.htaccess`)
- [ ] Test CSRF protection
- [ ] Configure rate limiting
- [ ] Set up backup system
- [ ] Enable error logging
- [ ] Test form validation
- [ ] Review database security
- [ ] Update all passwords
- [ ] Test on different browsers
- [ ] Run security scan
- [ ] Document security policies

---

**Remember**: Security is not a one-time task—it's an ongoing process!

**Questions?** Contact: security@hltsltd.com

**Last Updated**: December 2025
