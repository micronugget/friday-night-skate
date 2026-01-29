# DDEV Usage Guide for Friday Night Skate

## Understanding DDEV Execution Context

### Two Different Execution Contexts

#### 1. **Host Machine** (Your Ubuntu 24.04 Workstation)
When you run commands directly on your workstation terminal:
```bash
git status
python3 scripts/create_github_issues.py
gh auth login
```

#### 2. **DDEV Container** (Inside Docker)
When you run commands that need to interact with Drupal:
```bash
ddev drush cr              # Run Drush commands
ddev composer require      # Install PHP dependencies
ddev phpunit              # Run PHPUnit tests
ddev exec ffprobe file.mp4 # Run ffprobe on a file
```

### Critical Understanding for PHP Developers

**When Drupal PHP code executes via `shell_exec()` or similar functions, it's ALREADY running inside the DDEV container.**

#### ✅ Correct Implementation
```php
<?php
// This code runs INSIDE the DDEV container when Drupal executes it
$command = "ffprobe -v quiet -print_format json /path/to/file.mp4";
$output = shell_exec($command);
```

#### ❌ Incorrect Implementation
```php
<?php
// WRONG: ddev exec is only for HOST machine commands
$command = "ddev exec ffprobe -v quiet -print_format json /path/to/file.mp4";
$output = shell_exec($command); // This will fail!
```

## Why This Matters

### The Workflow

1. **Developer on Host**: Types `ddev drush cr` on Ubuntu terminal
2. **DDEV**: Translates to running `drush cr` inside the container
3. **Drupal PHP Code**: Executes inside the container
4. **shell_exec() Calls**: Also execute inside the same container

### Example: Metadata Extraction Service

```php
<?php

namespace Drupal\skating_video_uploader\Service;

class MetadataExtractor {
  
  protected function extractWithFFProbe(string $file_path): ?array {
    // We're already inside the DDEV container when this runs
    // So ffprobe is directly available
    $escaped_path = escapeshellarg($file_path);
    $command = "ffprobe -v quiet -print_format json -show_format -show_streams {$escaped_path} 2>&1";
    $output = shell_exec($command);
    
    // Process output...
  }
}
```

## Common DDEV Commands

### Development Workflow
```bash
# Start DDEV
ddev start

# Install dependencies
ddev composer install

# Clear Drupal cache
ddev drush cr

# Enable modules
ddev drush en fns_archive skating_video_uploader -y

# Export configuration
ddev drush cex -y

# Import configuration
ddev drush cim -y

# Run PHPUnit tests
ddev phpunit --group metadata

# Run specific test file
ddev phpunit web/modules/custom/skating_video_uploader/tests/src/Unit/MetadataExtractorTest.php

# Run PHPStan
ddev phpstan

# Run Drush commands
ddev drush status
ddev drush entity:info node

# Stop DDEV
ddev stop
```

### Testing Media Extraction
```bash
# Test ffprobe (from host machine)
ddev exec ffprobe -v quiet -print_format json /var/www/html/web/sites/default/files/video.mp4

# Test EXIF reading (from host machine)
ddev exec php -r "print_r(exif_read_data('/var/www/html/web/sites/default/files/image.jpg'));"
```

### Debugging
```bash
# SSH into container
ddev ssh

# Inside container, you can now run commands directly:
ffprobe /var/www/html/web/sites/default/files/video.mp4
php -m | grep exif
which ffprobe
```

## Metadata Extraction Implementation

### What Was Implemented

1. **extractImageMetadata()** - Extracts EXIF data from images
   - GPS coordinates (latitude, longitude, altitude)
   - Timestamp (DateTimeOriginal)
   - Camera info (make, model, lens)
   - Exposure data (ISO, aperture, shutter speed, focal length)
   - Converts GPS from degrees/minutes/seconds to decimal format

2. **extractVideoMetadata()** - Extracts metadata from videos using ffprobe
   - GPS coordinates from MOV/MP4 metadata tags
   - Duration, resolution, codec information
   - Timecode data
   - Creation timestamp

3. **storeMetadata()** - Saves extracted metadata to Archive Media nodes
   - Stores as JSON in field_metadata
   - Automatically called on node save

### Integration Points

- **hook_node_presave()** in `fns_archive.module` triggers extraction
- Works with Archive Media content type (field_archive_media)
- Extracts metadata BEFORE YouTube upload (preserving GPS data)
- Graceful error handling when ffprobe or EXIF unavailable

## Testing the Implementation

### Unit Tests
```bash
# Run all metadata tests
ddev phpunit --group metadata

# Run specific test class
ddev phpunit web/modules/custom/skating_video_uploader/tests/src/Unit/MetadataExtractorTest.php

# Run with verbose output
ddev phpunit --group metadata --verbose
```

### Manual Testing
```bash
# 1. Create test media with GPS data
# 2. Create Archive Media node
ddev drush entity:create node --bundle=archive_media --title="Test Media"

# 3. Check the metadata field
ddev drush sql:query "SELECT field_metadata_value FROM node__field_metadata WHERE entity_id = 1"
```

## File Paths in DDEV

### Drupal File System
- `public://` → `/var/www/html/web/sites/default/files/`
- `private://` → `/var/www/html/private/` (if configured)
- `/tmp/` → `/tmp/` inside container

### When to Use Which Path
```php
<?php
// Good: Use Drupal's file system service
$uri = 'public://videos/skate.mp4';
$local_path = \Drupal::service('file_system')->realpath($uri);
// $local_path = '/var/www/html/web/sites/default/files/videos/skate.mp4'

// Then pass to ffprobe
$command = "ffprobe " . escapeshellarg($local_path);
```

## Troubleshooting

### "ffprobe: command not found"
```bash
# Check if ffprobe is installed in container
ddev ssh
which ffprobe

# If not installed, add to .ddev/config.yaml:
webimage_extra_packages: [ffmpeg]

# Then restart
ddev restart
```

### "exif_read_data(): Unable to open file"
```php
<?php
// Ensure you're using the real path, not the URI
$uri = $file->getFileUri(); // public://image.jpg
$real_path = \Drupal::service('file_system')->realpath($uri);
$exif = exif_read_data($real_path);
```

### Permission Issues
```bash
# Fix file permissions
ddev exec chmod -R 755 /var/www/html/web/sites/default/files
ddev exec chown -R www-data:www-data /var/www/html/web/sites/default/files
```

## Best Practices

1. **Always use DDEV commands** for Drupal operations from host
2. **Never use `ddev exec`** inside PHP shell_exec() calls
3. **Use file system service** to convert URIs to paths
4. **Escape shell arguments** with escapeshellarg()
5. **Check command availability** before executing
6. **Log errors** comprehensively
7. **Test in DDEV environment** before deploying

## Resources

- [DDEV Documentation](https://ddev.readthedocs.io/)
- [Drupal Coding Standards](https://www.drupal.org/docs/develop/standards)
- [FFprobe Documentation](https://ffmpeg.org/ffprobe.html)
- [PHP EXIF Extension](https://www.php.net/manual/en/book.exif.php)
