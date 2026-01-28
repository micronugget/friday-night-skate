# Security Policy

## Sensitive Files

This repository is public. The following types of files must **NEVER** be committed:

### Configuration Files
- `settings.php` - Contains database credentials and sensitive configuration
- `settings.local.php` - Local development settings with credentials
- `.env` files (except `.env.example`) - Environment-specific secrets

### Database Files
- `*.sql` - Database dumps may contain user data, passwords, and sensitive information
- `*.sql.gz`, `*.sql.zip` - Compressed database dumps
- `*.dump`, `*.backup` - Database backup files

### Cryptographic Materials
- `*.key` - Private keys
- `*.pem` - PEM-encoded certificates and keys
- `*.p12`, `*.pfx` - PKCS#12 certificate bundles
- `*.crt`, `*.csr`, `*.cer`, `*.der` - Certificates

## Protected by .gitignore

The `.gitignore` file is configured to prevent accidental commits of sensitive files. However, developers should:

1. Never force-add (`git add -f`) ignored files
2. Review all changes before committing (`git status`, `git diff`)
3. Use `git secrets` or similar tools to scan for accidentally committed secrets
4. Keep local development credentials separate from the repository

## Example Files

Example configuration files are safe to commit and should have `.example` suffix:
- `.env.example`
- `settings.example.php`

## Reporting Security Issues

If you discover a security vulnerability in this repository, please report it to the repository maintainers privately.
