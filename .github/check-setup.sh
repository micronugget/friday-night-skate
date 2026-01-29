#!/bin/bash
# Quick check to see if DDEV environment is set up
# Usage: bash .github/check-setup.sh

echo "🔍 Checking Friday Night Skate development environment..."
echo ""

SETUP_NEEDED=0

# Check 1: DDEV installed
if command -v ddev &> /dev/null; then
    echo "✅ DDEV is installed: $(ddev --version | head -n1)"
else
    echo "❌ DDEV is NOT installed"
    SETUP_NEEDED=1
fi

# Check 2: DDEV project configured
if [ -d .ddev ]; then
    echo "✅ DDEV project is configured"
else
    echo "❌ DDEV project is NOT configured"
    SETUP_NEEDED=1
fi

# Check 3: DDEV running
if command -v ddev &> /dev/null && ddev describe &> /dev/null; then
    echo "✅ DDEV is running"
    
    # Check 4: Drupal installed
    if ddev drush status --fields=bootstrap 2>/dev/null | grep -q "Successful"; then
        echo "✅ Drupal is installed and working"
    else
        echo "⚠️  Drupal may not be fully installed"
        SETUP_NEEDED=1
    fi
else
    echo "⚠️  DDEV is not running (may need 'ddev start')"
    SETUP_NEEDED=1
fi

echo ""
echo "================================================"

if [ $SETUP_NEEDED -eq 0 ]; then
    echo "✨ Environment is ready! You can start working."
    echo ""
    echo "Quick commands:"
    echo "  ddev drush status   # Check Drupal status"
    echo "  ddev drush uli      # Get admin login link"
    echo "  ddev launch         # Open site in browser"
else
    echo "⚠️  Setup needed! Run the setup script:"
    echo ""
    echo "  bash .github/copilot-setup.sh"
    echo ""
    echo "Or manually follow steps in .github/copilot-setup-steps.yml"
fi

echo "================================================"

exit $SETUP_NEEDED
