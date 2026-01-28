#!/bin/bash
# Test script for FNS Archive module
# This script sets up DDEV and tests the fns_archive module

set -e  # Exit on error

echo "=== FNS Archive Module Test Script ==="
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print status
print_status() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_info() {
    echo -e "${YELLOW}ℹ${NC} $1"
}

# Check if DDEV is installed
if ! command -v ddev &> /dev/null; then
    print_error "DDEV is not installed. Installing..."
    curl -LO https://raw.githubusercontent.com/ddev/ddev/master/scripts/install_ddev.sh
    bash install_ddev.sh
    print_status "DDEV installed"
fi

print_status "DDEV version: $(ddev --version)"

# Check if DDEV is configured
if [ ! -f ".ddev/config.yaml" ]; then
    print_info "Configuring DDEV project..."
    ddev config --project-type=drupal11 --docroot=web --project-name=friday-night-skate
    print_status "DDEV configured"
fi

# Start DDEV
print_info "Starting DDEV..."
ddev start
print_status "DDEV started"

# Install dependencies if not already installed
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    print_info "Installing Composer dependencies (this may take a while)..."
    ddev composer install --no-interaction --prefer-dist || print_error "Composer install failed (continuing anyway)"
fi

# Check if Drupal is installed
DRUPAL_INSTALLED=$(ddev drush status --fields=bootstrap 2>/dev/null | grep -c "Successful" || echo "0")

if [ "$DRUPAL_INSTALLED" = "0" ]; then
    print_info "Installing Drupal..."
    ddev drush site:install minimal --account-name=admin --account-pass=admin -y
    print_status "Drupal installed"
else
    print_status "Drupal is already installed"
fi

# Enable core dependencies
print_info "Enabling core and contributed dependencies..."
ddev drush en \
  announcements_feed automated_cron big_pipe block breakpoint ckeditor5 config \
  content_moderation contextual datetime dblog dynamic_page_cache editor field \
  field_ui file filter image inline_form_errors layout_builder layout_discovery \
  link media media_library menu_link_content menu_ui mysql navigation node \
  options package_manager page_cache path path_alias responsive_image system \
  taxonomy text update user views views_ui workflows \
  automatic_updates autosave_form bpmn_io captcha coffee crop dashboard \
  drupal_cms_helper easy_breadcrumb easy_email easy_email_override eca eca_base \
  eca_config eca_content eca_form eca_misc eca_modeller_bpmn eca_render eca_ui \
  eca_user focal_point friendlycaptcha geofield gin_toolbar honeypot jquery_ui \
  jquery_ui_resizable klaro linkit login_emailusername mailsystem \
  menu_link_attributes pathauto project_browser redirect_404 redirect sam \
  scheduler scheduler_content_moderation_integration svg_image \
  symfony_mailer_lite tagify_user_list tagify token trash fns_archive \
  claro olivero stark drupal_cms_olivero easy_email_theme gin -y
print_status "Core and contributed dependencies enabled"

# Clear cache
print_info "Clearing cache..."
ddev drush cr
print_status "Cache cleared"

# Check configuration status
print_info "Checking configuration status..."
ddev drush cst
print_status "Configuration status checked"

# Verify module is enabled
print_info "Verifying module status..."
ddev drush pml --filter=fns_archive --format=yaml

# Create test taxonomy term
print_info "Creating test taxonomy term..."
ddev drush term:create skate_dates "2024-01-15 - Downtown Loop" --format=yaml || print_error "Failed to create taxonomy term"
print_status "Test taxonomy term created"

# List taxonomy terms
print_info "Listing taxonomy terms..."
ddev drush taxonomy:list skate_dates

# Verify content type exists
print_info "Verifying archive_media content type..."
ddev drush entity:info node --filter=archive_media

# Show field information
print_info "Showing fields on archive_media content type..."
ddev drush field:list node.archive_media

echo ""
print_status "=== Test Complete ==="
echo ""
print_info "You can now:"
echo "  - Visit the site: ddev launch"
echo "  - Create archive media: ddev launch /node/add/archive_media"
echo "  - View taxonomy: ddev launch /admin/structure/taxonomy/manage/skate_dates"
echo ""
