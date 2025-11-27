# Security Policy

## Reporting a Vulnerability

LaraStore takes security seriously. If you discover any security vulnerabilities, please send an email to [rishabh.78275@gmail.com](mailto:rishabh.78275@gmail.com) instead of using the issue tracker.

Please provide a detailed description of the vulnerability, including steps to reproduce, potential impact, and any proof-of-concept code if possible. We will acknowledge your report within 48 hours and will work to address it promptly.

## Security Features Implemented

The LaraStore application includes comprehensive security measures across multiple areas:

### Authentication & Authorization
- **Two-Factor Authentication (2FA)**: Enhanced security with TOTP-based authentication using Google Authenticator
  - QR code setup for easy configuration
  - Backup recovery codes for account access
  - Mandatory 2FA for admin accounts (cannot be disabled)
- **Role-Based Permissions**: Advanced permission system beyond basic admin/user roles
  - Granular permissions for different admin roles (Super Admin, Product Manager, Order Manager, etc.)
  - Permission-based middleware for route protection
  - Flexible role assignment and permission management

### Session Management
- **Enhanced Session Security**:
  - Role-based session timeouts (30 minutes for regular users, 120 minutes for admins)
  - Secure session cookies with HTTPS, HttpOnly, and SameSite=strict attributes
  - Complete session invalidation on logout across all devices
  - Prevention of session hijacking

### Password Security
- **Password Strength Requirements**: Enforced strong passwords with:
  - Minimum 12 characters
  - Uppercase, lowercase, number, and special character requirements
- **Password Rotation Policy**: Automatic password expiration after 90 days
- **Secure Password Storage**: Bcrypt hashing with configurable rounds

### Brute Force Protection
- **Login Attempt Tracking**: Comprehensive logging of all login attempts with IP and user agent
- **Rate Limiting**: Protection against automated attacks with configurable limits
- **Account Lockout**: Automatic account suspension after multiple failed attempts (30-minute lockout)
- **IP-based Restrictions**: Protection against attacks from suspicious IP addresses

### Additional Security Measures
- **Email Verification**: Required for new account activation
- **CSRF Protection**: Cross-Site Request Forgery protection on all forms
- **Input Validation & Sanitization**: All user inputs are validated and sanitized
- **SQL Injection Prevention**: Prevention through Eloquent ORM usage
- **XSS Prevention**: Cross-Site Scripting prevention through Blade template escaping
- **Secure Password Hashing**: Passwords are hashed using Laravel's secure hashing methods
- **Authentication Middleware**: Protected routes with Laravel's authentication system
- **Role-Based Access Control**: Proper authorization based on user roles
- **Secure Session Management**: Laravel's secure session handling
- **File Upload Validation**: Validation for file types, sizes, and security checks
- **Database Encryption**: Sensitive data encryption at rest

## Security Best Practices for Users

If you're using or extending LaraStore, please follow these security best practices:

- Keep dependencies updated and apply security patches promptly
- Use strong, unique passwords for admin accounts
- Regularly backup your database and files
- Enable HTTPS in production environments
- Review and audit custom code for security vulnerabilities
- Limit access to admin panel to authorized personnel only
- Monitor logs for suspicious activity
- Regularly review user roles and permissions
- Enable two-factor authentication for all admin accounts
- Check for and apply security updates regularly

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 1.x     | :white_check_mark: |

## Security Updates

Security updates will be released as quickly as possible after a vulnerability is identified and confirmed. We recommend that all users keep their installations up to date with the latest version.