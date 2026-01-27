#!/bin/bash
set -e # Exit on error

echo "--- STARTING DDEV CLOUD SETUP ---"

# 1. Install DDEV (Optimized for GitHub Runner)
if ! command -v ddev &> /dev/null; then
    curl -fsSL https://ddev.com/install.sh | bash
fi

# 2. Configure DDEV for Non-Interactive Cloud Use
ddev config --auto

# 3. Start DDEV (This boots OLS, PHP, and MariaDB)
# We use -y to skip prompts and ensure it runs in the background
ddev start -y

# 4. Install Dependencies
echo "--- INSTALLING COMPOSER DEPS ---"
ddev composer install --no-interaction --prefer-dist

# 5. Verify OpenLiteSpeed Configuration
# This ensures your .htaccess and OLS rules are valid
echo "--- VERIFYING OLS CONFIG ---"
ddev exec "ls -la /var/www/html/web/.htaccess"

# 6. Database Setup (Optional but Recommended)
# If you have a small SQL dump for testing, uncomment below:
# ddev import-db --src=.github/tests/mock-db.sql

# 7. Clear Drupal Cache
ddev drush cr

echo "--- DDEV IS READY FOR AGENT MISSIONS ---"
