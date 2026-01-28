# Bug Report: Skating Video Uploader Module Issues

## BUG-001: Dependency Injection Violations in Service Classes
**Severity:** Critical  
**Component:** Service Layer  
**Summary:** Service classes use static `\Drupal::` calls instead of dependency injection

### Steps to Reproduce:
1. Review `src/Service/MetadataExtractor.php` line 267
2. Review `src/Service/VideoProcessor.php` lines 121, 155, 184
3. Note the use of `\Drupal::database()` and `\Drupal::service()`

### Expected Result:
Services should receive all dependencies via constructor injection and service definitions in `.services.yml`

### Actual Result:
Services use static service locator pattern:
```php
// MetadataExtractor.php:267
$connection = \Drupal::database();

// VideoProcessor.php:121
$youtube_uploader = \Drupal::service('skating_video_uploader.youtube_uploader');

// VideoProcessor.php:155 and 184
$connection = \Drupal::database();
```

### Environment:
- Drupal 11
- PHP 8.3.6
- Module: skating_video_uploader (initial version)

### Fix Required:
1. Add database service injection to MetadataExtractor:
   - Update constructor to accept `@database` service
   - Update `skating_video_uploader.services.yml` to inject dependency
   - Replace all `\Drupal::database()` calls with `$this->database`

2. Add YouTubeUploader injection to VideoProcessor:
   - Update constructor to accept `@skating_video_uploader.youtube_uploader`
   - Update `skating_video_uploader.services.yml` to inject dependency
   - Replace `\Drupal::service()` call with `$this->youtubeUploader`

3. Add database service injection to VideoProcessor:
   - Update constructor to accept `@database` service
   - Update `skating_video_uploader.services.yml` to inject dependency
   - Replace all `\Drupal::database()` calls with `$this->database`

### Impact:
- Violates Drupal best practices
- Makes unit testing difficult/impossible
- Tight coupling to Drupal core
- Fails code review standards

**Assigned To:** @drupal-developer

---

## BUG-002: Missing Strict Type Declaration
**Severity:** High  
**Component:** Install File  
**Summary:** `.install` file missing required `declare(strict_types=1);`

### Steps to Reproduce:
1. Open `skating_video_uploader.install`
2. Check for `declare(strict_types=1);` after opening `<?php` tag
3. Note it is missing

### Expected Result:
All PHP files should have strict type declaration per project standards:
```php
<?php

declare(strict_types=1);

/**
 * @file
 * Install, update and uninstall functions...
 */
```

### Actual Result:
File starts with:
```php
<?php

/**
 * @file
 * Install, update and uninstall functions...
 */
```

### Environment:
- Drupal 11
- PHP 8.3.6
- Module: skating_video_uploader (initial version)

### Fix Required:
Add `declare(strict_types=1);` after the opening PHP tag:
```php
<?php

declare(strict_types=1);

/**
 * @file
 * Install, update and uninstall functions for the Skating Video Uploader module.
 */
```

### Impact:
- Violates project technical standards
- Reduces type safety
- Inconsistent with other module files (6/7 have it)

**Assigned To:** @drupal-developer

---

## ISSUE-001: Direct Database Queries Without Entity API
**Severity:** Medium  
**Component:** Service Layer  
**Summary:** Services use direct database queries instead of Entity API

### Description:
Both `MetadataExtractor` and `VideoProcessor` use direct database queries via `\Drupal::database()` instead of using Drupal's Entity API or Query API. While the queries appear to use proper parameterization, this approach:

1. Bypasses entity caching
2. Bypasses entity hooks
3. Makes schema changes more fragile
4. Reduces maintainability

### Recommendation:
Consider one of these approaches:
1. Create a custom entity type for `skating_video_metadata`
2. Use the Database API service with proper injection
3. Create a repository service that abstracts database operations

### Files Affected:
- `src/Service/MetadataExtractor.php`
- `src/Service/VideoProcessor.php`

### Impact:
- Reduced maintainability
- Potential performance issues (no entity caching)
- Schema changes require manual query updates

**Assigned To:** @architect (for decision) / @drupal-developer (for implementation)

---

## ISSUE-002: Synchronous YouTube Upload May Block
**Severity:** Low  
**Component:** YouTube Upload Service  
**Summary:** YouTube uploads happen synchronously during form submission

### Description:
When a user submits a video form with YouTube consent checked, the upload happens immediately during the form submission process. For large video files, this could:
1. Timeout the HTTP request
2. Block the user's browser for extended periods
3. Provide poor user experience

### Recommendation:
Implement asynchronous upload via Drupal Queue API:
1. Queue the upload operation on form submit
2. Process queue via cron or queue worker
3. Provide status feedback to user

### Files Affected:
- `skating_video_uploader.module` (form submit handler)
- `src/Service/VideoProcessor.php`
- `src/Service/YouTubeUploader.php`

### Impact:
- User experience issues for large files
- Potential timeout errors
- Server resource blocking

**Assigned To:** @architect (for decision)

---

## Generated: 2025-01-28
## Last Updated: 2025-01-28
## Status: Open - Awaiting Developer Fixes
