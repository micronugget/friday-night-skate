# Friday Night Skate - Project Foundation & Standards

## 0. CRITICAL: Environment Setup (First Step)

**⚠️ BEFORE STARTING ANY WORK: You MUST set up the DDEV environment.**

This project requires DDEV to be installed and running. All commands (Drush, Composer, PHPUnit) must be executed via `ddev`.

### Automated Setup
Run this command **FIRST** when starting work on this repository:

```bash
bash .github/copilot-setup.sh
```

This script will:
- Install DDEV (if not present)
- Configure the project
- Start DDEV containers
- Install all dependencies
- Set up Drupal
- Enable required modules

### Manual Setup Alternative
If the automated script fails, follow the steps in `.github/copilot-setup-steps.yml` manually.

### Verification
After setup, verify DDEV is working:

```bash
ddev --version          # Should show DDEV version
ddev describe           # Should show project details
ddev drush status       # Should show Drupal is installed
```

**❌ DO NOT proceed without completing this setup step!**

---

## 1. Project Context
- **System:** Drupal 11 / Drupal CMS 2
- **Theming:** Radix 6 (Bootstrap 5 subtheme)
- **Local Dev:** Ubuntu 24.04 with DDEV (Dockerized LAMP)
- **Production:** Ubuntu 24.04 with OpenLiteSpeed and MySQL 8.0
- **Deployment:** User interaction and metadata extraction occur on the live OLS server. Code must be built locally, tested, and deployed via Git.

## 2. Technical Commandments
- **The "DDEV" Rule:** All CLI commands (Drush, Composer, PHPUnit) MUST be prefixed with `ddev`.
- **GPS Extraction:** Use `ffprobe` (via `ddev exec`) to extract location metadata from media in `private://` before external API transfer.
- **Media Workflow:** Videos are handled via `videojs_media_lock`.
- **Performance:** Image caching must be efficient." Use responsive image styles with bootstrap 5 breakpoints and WebP format as default.
- **Frontend:** Use Masonry.js for views. Implement Swiper.js for mobile-friendly modal navigation between individual images and poster images of videos.

## 3. Coding Standards & Quality
- **Drupal Standards:** Strictly follow Drupal Coding Standards (SNC) and PSR-12.
- **Strict Typing:** Use `declare(strict_types=1);` in all new PHP files.
- **Auto-Testing:** Do not propose a Pull Request unless `ddev drush test-run` and `ddev phpstan` pass.
- **Git Hygiene:** - One feature per branch.
  - Use conventional commits (e.g., `feat:`, `fix:`, `refactor:`).
  - Always export config (`ddev drush cex`) and include the `.yml` changes in the commit.

## 4. Environment-Specific Logic
- **OLS Compatibility:** Be aware that OpenLiteSpeed handles `.htaccess` and rewrites differently.
- **Metadata Protection:** Metadata extraction must happen in the "intermediate" stage on the server before YouTube's API scrubs the file.

# Validation Rules
- To validate PHP logic, run: `ddev phpunit`
- To validate UI changes, run: `ddev yarn test:nightwatch`
- Always run `ddev drush cr` after changing hooks or services.
- If a test fails, read the log, fix the code, and retry until passing.
