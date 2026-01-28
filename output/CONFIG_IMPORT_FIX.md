# How to Fix Config Import Issues in Drupal
## Problem
When running `ddev drush cim`, you get errors:
- Site UUID in source storage does not match the target storage
- Entities exist and need to be deleted before importing
- The workflow Basic is being used, and cannot be deleted
## Solution Options
### Option 1: Fresh Site Install with Config (RECOMMENDED for new projects)
This completely reinstalls Drupal using your config as the starting point:
```bash
# 1. Export your current UUID first (for reference)
ddev drush cget system.site uuid
# 2. Reinstall Drupal completely with config
ddev drush site:install --existing-config -y
# 3. Clear cache
ddev drush cr
# 4. Login
ddev drush uli
```
**WARNING**: This will DELETE ALL existing content and users!
### Option 2: Match UUIDs and Clean Import (for preserving some content)
```bash
# 1. Find UUID in your config/sync/system.site.yml
grep uuid web/sites/default/files/config_*/sync/system.site.yml
# 2. Set your site to match that UUID
ddev drush config:set system.site uuid "UUID-FROM-CONFIG-FILE" -y
# 3. Delete conflicting content
ddev drush entity:delete node --bundle=page
ddev drush entity:delete media --bundle=image
ddev drush php:eval "\Drupal::entityTypeManager()->getStorage('crop')->delete(\Drupal::entityTypeManager()->getStorage('crop')->loadMultiple());"
# 4. Try import again
ddev drush cim -y
```
### Option 3: Don't Import Config (work with current setup)
If you're just starting development, you might NOT need to import config from the repo.
Instead, build your site and EXPORT config:
```bash
# After making changes in the UI, export:
ddev drush cex -y
# Then commit to git:
git add -A
git commit -m "config: export current site configuration"
```
## Which Option Should You Use?
- **New project, starting fresh?** → Use Option 1
- **Have existing content you want to keep?** → Don't use config import yet
- **Syncing from production/staging?** → Use Option 2
## According to Your Coding Instructions
From `.github/copilot-instructions.md`:
> Always export config (`ddev drush cex`) and include the `.yml` changes in the commit.
This suggests you should be EXPORTING config, not importing it (unless you're syncing between environments).
