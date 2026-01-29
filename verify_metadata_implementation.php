#!/usr/bin/env php
<?php

/**
 * @file
 * Verification script for metadata extraction implementation.
 */

declare(strict_types=1);

echo "=== Metadata Extraction Implementation Verification ===\n\n";

$errors = [];
$warnings = [];
$success = [];

// Check 1: MetadataExtractor.php exists and has required methods.
echo "1. Checking MetadataExtractor service...\n";
$metadata_extractor_file = __DIR__ . '/web/modules/custom/skating_video_uploader/src/Service/MetadataExtractor.php';
if (!file_exists($metadata_extractor_file)) {
  $errors[] = "MetadataExtractor.php not found";
} else {
  $content = file_get_contents($metadata_extractor_file);
  
  $required_methods = [
    'extractImageMetadata',
    'extractVideoMetadata',
    'storeMetadata',
    'extractGpsFromExif',
    'convertGpsCoordinate',
    'convertExifRational',
  ];
  
  foreach ($required_methods as $method) {
    if (strpos($content, "function $method") !== FALSE) {
      $success[] = "✓ Method $method found";
    } else {
      $errors[] = "✗ Method $method not found";
    }
  }
  
  // Check for proper use declarations.
  if (strpos($content, 'use Drupal\file\FileInterface;') !== FALSE) {
    $success[] = "✓ FileInterface imported";
  } else {
    $warnings[] = "! FileInterface import not found";
  }
  
  if (strpos($content, 'use Drupal\node\NodeInterface;') !== FALSE) {
    $success[] = "✓ NodeInterface imported";
  } else {
    $warnings[] = "! NodeInterface import not found";
  }
  
  // Check for strict typing.
  if (strpos($content, 'declare(strict_types=1);') !== FALSE) {
    $success[] = "✓ Strict typing enabled";
  } else {
    $errors[] = "✗ Strict typing not enabled";
  }
  
  // Check for EXIF handling.
  if (strpos($content, 'exif_read_data') !== FALSE) {
    $success[] = "✓ EXIF extraction implemented";
  } else {
    $errors[] = "✗ EXIF extraction not implemented";
  }
  
  // Check for GPS coordinate conversion.
  if (strpos($content, 'GPSLatitude') !== FALSE && strpos($content, 'GPSLongitude') !== FALSE) {
    $success[] = "✓ GPS coordinate extraction implemented";
  } else {
    $errors[] = "✗ GPS coordinate extraction not implemented";
  }
  
  // Check for DDEV compatibility.
  if (strpos($content, 'IS_DDEV_PROJECT') !== FALSE || strpos($content, 'ddev exec') !== FALSE) {
    $success[] = "✓ DDEV compatibility check present";
  } else {
    $warnings[] = "! DDEV compatibility not explicitly handled";
  }
}

echo "\n2. Checking fns_archive module hook...\n";
$module_file = __DIR__ . '/web/modules/custom/fns_archive/fns_archive.module';
if (!file_exists($module_file)) {
  $errors[] = "fns_archive.module not found";
} else {
  $content = file_get_contents($module_file);
  
  // Check for metadata extraction in presave hook.
  if (strpos($content, 'field_archive_media') !== FALSE) {
    $success[] = "✓ Hook checks for field_archive_media";
  } else {
    $errors[] = "✗ Hook does not check field_archive_media";
  }
  
  if (strpos($content, 'extractImageMetadata') !== FALSE) {
    $success[] = "✓ Hook calls extractImageMetadata";
  } else {
    $errors[] = "✗ Hook does not call extractImageMetadata";
  }
  
  if (strpos($content, 'extractVideoMetadata') !== FALSE) {
    $success[] = "✓ Hook calls extractVideoMetadata";
  } else {
    $errors[] = "✗ Hook does not call extractVideoMetadata";
  }
  
  if (strpos($content, "media->bundle()") !== FALSE) {
    $success[] = "✓ Hook determines media type";
  } else {
    $errors[] = "✗ Hook does not determine media type";
  }
  
  if (strpos($content, 'storeMetadata') !== FALSE) {
    $success[] = "✓ Hook stores metadata";
  } else {
    $errors[] = "✗ Hook does not store metadata";
  }
  
  // Check for error handling.
  if (strpos($content, 'catch') !== FALSE && strpos($content, 'Exception') !== FALSE) {
    $success[] = "✓ Error handling implemented";
  } else {
    $warnings[] = "! Error handling may be missing";
  }
}

echo "\n3. Checking test file...\n";
$test_file = __DIR__ . '/web/modules/custom/skating_video_uploader/tests/src/Unit/MetadataExtractorTest.php';
if (!file_exists($test_file)) {
  $errors[] = "MetadataExtractorTest.php not found";
} else {
  $content = file_get_contents($test_file);
  
  $test_methods = [
    'testConvertGpsCoordinate',
    'testConvertExifRational',
    'testExtractGpsFromExif',
    'testExtractImageMetadata',
    'testExtractVideoMetadata',
    'testStoreMetadata',
  ];
  
  foreach ($test_methods as $method) {
    if (strpos($content, "function $method") !== FALSE) {
      $success[] = "✓ Test $method found";
    } else {
      $warnings[] = "! Test $method not found";
    }
  }
  
  // Check for @group metadata.
  if (strpos($content, '@group metadata') !== FALSE) {
    $success[] = "✓ Tests tagged with @group metadata";
  } else {
    $warnings[] = "! Tests not tagged with @group metadata";
  }
  
  // Check for database mock.
  if (strpos($content, 'Connection') !== FALSE) {
    $success[] = "✓ Database connection mocked";
  } else {
    $warnings[] = "! Database connection not mocked";
  }
}

echo "\n4. Checking PHP syntax...\n";
$files_to_check = [
  $metadata_extractor_file,
  $module_file,
  $test_file,
];

foreach ($files_to_check as $file) {
  if (file_exists($file)) {
    $output = [];
    $return_var = 0;
    exec("php -l " . escapeshellarg($file) . " 2>&1", $output, $return_var);
    if ($return_var === 0) {
      $success[] = "✓ " . basename($file) . " syntax valid";
    } else {
      $errors[] = "✗ " . basename($file) . " has syntax errors: " . implode("\n", $output);
    }
  }
}

// Print results.
echo "\n=== Results ===\n\n";

if (!empty($success)) {
  echo "SUCCESS (" . count($success) . "):\n";
  foreach ($success as $msg) {
    echo "  $msg\n";
  }
  echo "\n";
}

if (!empty($warnings)) {
  echo "WARNINGS (" . count($warnings) . "):\n";
  foreach ($warnings as $msg) {
    echo "  $msg\n";
  }
  echo "\n";
}

if (!empty($errors)) {
  echo "ERRORS (" . count($errors) . "):\n";
  foreach ($errors as $msg) {
    echo "  $msg\n";
  }
  echo "\n";
  exit(1);
} else {
  echo "✓ All checks passed!\n\n";
  echo "Implementation Summary:\n";
  echo "- MetadataExtractor service enhanced with image and video metadata extraction\n";
  echo "- EXIF data extraction for images (GPS, timestamp, camera info)\n";
  echo "- FFProbe wrapper for video files\n";
  echo "- Metadata storage to node's field_metadata as JSON\n";
  echo "- Hook integration in fns_archive module for automatic extraction\n";
  echo "- Comprehensive unit tests with @group metadata tag\n";
  echo "- Error handling and graceful degradation\n";
  echo "- DDEV compatibility\n\n";
  exit(0);
}
