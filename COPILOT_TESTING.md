# Testing with GitHub Copilot

This repository is configured to enable automated testing using GitHub Copilot Enterprise.

## How It Works

When you assign an issue to @copilot or create a pull request, Copilot can automatically:

1. **Boot an ephemeral environment** - A clean Ubuntu instance with Docker
2. **Set up DDEV** - Install and configure DDEV for Drupal 11
3. **Install dependencies** - Run composer install with proper authentication
4. **Test your code** - Enable modules, run tests, verify configuration
5. **Report results** - Provide feedback on what works and what doesn't

## Configuration

### `.github/copilot-setup-steps.yml`

This file defines how Copilot sets up the development environment. It includes:

- DDEV installation
- Project configuration
- Dependency installation
- Module enablement
- Test execution

### `test-fns-archive.sh`

A standalone script that can be run locally or in CI to test the FNS Archive module:

```bash
./test-fns-archive.sh
```

## Testing Locally

You can run the same tests that Copilot runs:

```bash
# 1. Ensure DDEV is installed
ddev --version

# 2. Run the test script
./test-fns-archive.sh

# 3. Or run steps manually
ddev start
ddev composer install
ddev drush site:install minimal -y
ddev drush en fns_archive -y
ddev drush cst
```

## Benefits

✅ **No manual testing needed** - Copilot tests code changes automatically
✅ **Consistent environment** - Same setup every time
✅ **Fast feedback** - Know if code works before merging
✅ **Real testing** - Not just "hallucination" - actual LAMP stack in the cloud

## Authentication

The setup uses `${{ secrets.GITHUB_TOKEN }}` for Composer authentication, allowing it to access GitHub repositories without manual token entry.

## See Also

- [TESTING.md](web/modules/custom/fns_archive/TESTING.md) - Module-specific testing guide
- [QUICKSTART_DDEV.md](QUICKSTART_DDEV.md) - DDEV setup guide
- [README.md](README.md) - Main project README
