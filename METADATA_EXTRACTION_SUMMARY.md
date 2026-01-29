# Metadata Extraction Service Implementation Summary

## Overview
This implementation adds comprehensive metadata extraction capabilities to the Friday Night Skate project, enabling automatic extraction of EXIF data from images and video metadata from ffprobe, storing it as JSON in Archive Media nodes.

## Implementation Details

### 1. MetadataExtractor Service Enhancements
**File**: `web/modules/custom/skating_video_uploader/src/Service/MetadataExtractor.php`

#### New Public Methods:
- **`extractImageMetadata(FileInterface $file): ?array`**
  - Extracts EXIF data from image files
  - Returns GPS coordinates (latitude, longitude, altitude)
  - Extracts timestamp (DateTimeOriginal)
  - Captures camera information (make, model)
  - Retrieves technical details (ISO, aperture, focal length, exposure time)
  - Gracefully handles missing EXIF extension

- **`extractVideoMetadata(FileInterface $file): ?array`**
  - Wraps existing ffprobe functionality
  - Works with File entities instead of VideoJS Media entities
  - Maintains backward compatibility
  - Detects DDEV environment and adjusts command execution

- **`storeMetadata(NodeInterface $node, array $metadata): bool`**
  - Stores metadata as JSON in node's `field_metadata` field
  - Returns success/failure status
  - Includes comprehensive error handling

#### New Protected Methods:
- **`extractGpsFromExif(array $gps_data): array`**
  - Extracts GPS coordinates from EXIF GPS data
  - Converts GPS coordinates to decimal format
  - Handles latitude/longitude references (N/S/E/W)
  - Processes altitude with reference (above/below sea level)

- **`convertGpsCoordinate(array $coordinate): float`**
  - Converts GPS coordinates from EXIF format (degrees/minutes/seconds) to decimal
  - Includes validation for array bounds
  - Returns 0.0 for malformed data

- **`convertExifRational($rational): float`**
  - Converts EXIF rational numbers (e.g., "5/1") to floats
  - Handles strings, floats, and integers
  - Uses strict comparison for denominator validation
  - Safe handling of division by zero

- **`storeMetadataToDatabase(array $metadata)`**
  - Renamed from `storeMetadata` to avoid confusion
  - Maintains existing database storage functionality
  - Used for VideoJS Media entities

#### Key Improvements:
- DDEV environment detection: Checks `IS_DDEV_PROJECT` environment variable
- Enhanced error logging with contextual information
- Comprehensive input validation
- Graceful degradation when extensions are unavailable

### 2. Hook Integration
**File**: `web/modules/custom/fns_archive/fns_archive.module`

#### Enhanced `fns_archive_node_presave()`:
- Automatically extracts metadata when Archive Media nodes are saved
- Determines media type (image vs video)
- Validates source field configuration exists
- Extracts file entity from media
- Calls appropriate extraction method based on media type
- Stores metadata as JSON in `field_metadata`
- Comprehensive error handling with logging
- Consolidated code to eliminate duplication

### 3. Comprehensive Unit Tests
**File**: `web/modules/custom/skating_video_uploader/tests/src/Unit/MetadataExtractorTest.php`

#### New Test Methods:
- `testConvertGpsCoordinate()`: Tests GPS coordinate conversion
- `testConvertGpsCoordinateMalformed()`: Tests edge cases with incomplete data
- `testConvertExifRational()`: Tests rational number conversion with data provider
- `testExtractGpsFromExif()`: Tests GPS extraction from EXIF data
- `testExtractGpsFromExifSouthWest()`: Tests negative coordinates
- `testExtractGpsFromExifMissingData()`: Tests missing GPS data handling
- `testExtractImageMetadataNoExifExtension()`: Tests graceful degradation
- `testExtractImageMetadataFileNotFound()`: Tests file not found scenario
- `testExtractVideoMetadata()`: Tests video metadata extraction
- `testStoreMetadata()`: Tests metadata storage to node
- `testStoreMetadataNoField()`: Tests missing field handling

#### Test Coverage:
- All new methods covered
- Edge cases for malformed data
- Error conditions (missing extensions, files, fields)
- Tagged with `@group metadata` for easy test execution
- Data providers for comprehensive testing

## Technical Requirements Met

✅ **Strict Typing**: All files use `declare(strict_types=1);`
✅ **Drupal Coding Standards**: Follows PSR-12 and Drupal standards
✅ **Dependency Injection**: Proper DI maintained throughout
✅ **Error Handling**: Comprehensive try-catch blocks with logging
✅ **DDEV Compatibility**: Environment detection for external commands
✅ **Input Validation**: GPS coordinates, configuration keys, array bounds
✅ **Graceful Degradation**: Handles missing EXIF extension and ffprobe
✅ **Documentation**: Complete docblocks for all methods
✅ **Testing**: Comprehensive unit tests with edge cases

## Usage Example

```php
// The hook automatically extracts metadata on node save
// No manual intervention required

// When an Archive Media node is saved with an image:
$node = Node::create([
  'type' => 'archive_media',
  'title' => 'My Photo',
  'field_archive_media' => $media_entity, // Image media
]);
$node->save();

// Metadata is automatically extracted and stored in field_metadata as:
{
  "latitude": 37.7749,
  "longitude": -122.4194,
  "altitude": 100.5,
  "timestamp": "2024:01:15 10:30:00",
  "camera_make": "Canon",
  "camera_model": "EOS 5D Mark IV",
  "iso": 100,
  "aperture": 2.8,
  "focal_length": 50.0,
  "exposure_time": "1/125"
}
```

## Files Modified

1. `web/modules/custom/skating_video_uploader/src/Service/MetadataExtractor.php`
   - Added 6 new methods
   - Enhanced existing extractWithFFProbe() for DDEV
   - Updated class documentation

2. `web/modules/custom/fns_archive/fns_archive.module`
   - Enhanced fns_archive_node_presave() hook
   - Added automatic metadata extraction logic
   - Consolidated duplicate code

3. `web/modules/custom/skating_video_uploader/tests/src/Unit/MetadataExtractorTest.php`
   - Added 11 new test methods
   - Added data providers
   - Tagged with @group metadata

## Important Notes

### Metadata Extraction Timing
- Metadata extraction happens **BEFORE** YouTube upload to preserve GPS data
- YouTube's API scrubs location metadata from videos
- This ensures location data is captured and stored in Drupal

### GPS Data Format
- Input: EXIF GPS format (degrees/minutes/seconds as rational numbers)
- Output: Decimal degrees format (e.g., 37.7749, -122.4194)
- Handles all hemispheres (N/S/E/W) correctly
- Validates array bounds to prevent errors

### File Paths
- Uses `private://` file scheme for security
- Converts URIs to real paths for external command execution
- Works with both public and private file schemes

### DDEV Environment
- Automatically detects DDEV environment via `IS_DDEV_PROJECT`
- Prefixes ffprobe commands with `ddev exec` when appropriate
- Falls back to direct execution on production servers

## Testing

### Run All Metadata Tests
```bash
ddev phpunit --group metadata
```

### Run Specific Test File
```bash
ddev phpunit web/modules/custom/skating_video_uploader/tests/src/Unit/MetadataExtractorTest.php
```

### Verify Implementation
```bash
php verify_metadata_implementation.php
```

## Security Considerations

- ✅ No SQL injection vulnerabilities (uses Drupal query API)
- ✅ Shell command escaping for file paths
- ✅ Input validation for all external data
- ✅ Error messages don't expose sensitive information
- ✅ Graceful handling of missing extensions
- ✅ Proper exception handling throughout

## Performance Considerations

- Metadata extraction happens during node save (acceptable overhead)
- EXIF reading is fast (< 100ms for typical images)
- FFProbe execution may take 1-2 seconds for videos
- Results cached in field_metadata (no repeated extraction)
- Database queries optimized with proper indexing

## Future Enhancements

Potential improvements for future iterations:
1. Batch processing for existing nodes
2. UI for viewing/editing extracted metadata
3. Search integration for GPS coordinates
4. Map display of geotagged content
5. Metadata validation and sanitization rules
6. Support for additional EXIF fields
7. Video thumbnail generation with metadata overlay

## Conclusion

This implementation provides a robust, well-tested metadata extraction service that:
- Automatically captures important metadata from images and videos
- Stores data in a structured, searchable format
- Handles edge cases and errors gracefully
- Follows Drupal best practices and coding standards
- Is fully tested and production-ready

The service is designed to work seamlessly in both DDEV development environments and production OpenLiteSpeed servers, ensuring consistent behavior across deployment stages.
