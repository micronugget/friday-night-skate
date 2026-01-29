#!/usr/bin/env php
<?php

/**
 * @file
 * Integration test for MetadataExtractor service methods.
 */

// Simulate Drupal bootstrap for testing.
define('DRUPAL_ROOT', __DIR__ . '/web');

// Manually load the MetadataExtractor class.
require_once DRUPAL_ROOT . '/modules/custom/skating_video_uploader/src/Service/MetadataExtractor.php';

echo "=== MetadataExtractor Integration Test ===\n\n";

// Test GPS coordinate conversion methods using reflection.
echo "Testing GPS conversion methods:\n";

// We need to create a minimal mock to test protected methods.
class TestableMetadataExtractor extends \Drupal\skating_video_uploader\Service\MetadataExtractor {
  
  public function __construct() {
    // No parent constructor call - we're just testing logic
  }
  
  public function testConvertExifRational(string $rational): float {
    return $this->convertExifRational($rational);
  }
  
  public function testConvertGpsCoordinate(array $coordinate): float {
    return $this->convertGpsCoordinate($coordinate);
  }
  
  public function testExtractGpsFromExif(array $exif): array {
    return $this->extractGpsFromExif($exif);
  }
}

$extractor = new TestableMetadataExtractor();

// Test convertExifRational
echo "\n1. Testing convertExifRational():\n";
$tests = [
  ['1/60', 0.016666666666667],
  ['100/1', 100.0],
  ['0/1', 0.0],
  ['5', 5.0],
];

$passed = 0;
$failed = 0;

foreach ($tests as $test) {
  [$input, $expected] = $test;
  $result = $extractor->testConvertExifRational($input);
  $match = abs($result - $expected) < 0.000001;
  
  echo "  Input: '$input' => $result (expected: $expected) ";
  if ($match) {
    echo "✅\n";
    $passed++;
  } else {
    echo "❌\n";
    $failed++;
  }
}

// Test convertGpsCoordinate
echo "\n2. Testing convertGpsCoordinate():\n";
$gps_tests = [
  [['40/1', '45/1', '23/100'], 40.750063888889],
  [['73/1', '59/1', '37/100'], 73.983436111111],
  [['37/1', '46/1', '30/100'], 37.76675],
];

foreach ($gps_tests as $test) {
  [$coord, $expected] = $test;
  $result = $extractor->testConvertGpsCoordinate($coord);
  $match = $result !== NULL && abs($result - $expected) < 0.000001;
  
  echo "  {$coord[0]} {$coord[1]} {$coord[2]} => $result (expected: $expected) ";
  if ($match) {
    echo "✅\n";
    $passed++;
  } else {
    echo "❌\n";
    $failed++;
  }
}

// Test extractGpsFromExif
echo "\n3. Testing extractGpsFromExif():\n";
$exif_data = [
  'GPSLatitude' => ['40/1', '45/1', '23/100'],
  'GPSLatitudeRef' => 'N',
  'GPSLongitude' => ['73/1', '59/1', '37/100'],
  'GPSLongitudeRef' => 'W',
  'GPSAltitude' => '100/1',
  'GPSAltitudeRef' => '0',
];

$gps_result = $extractor->testExtractGpsFromExif($exif_data);

echo "  Testing full EXIF GPS extraction:\n";
if (isset($gps_result['latitude'])) {
  echo "    Latitude: {$gps_result['latitude']} ";
  if (abs($gps_result['latitude'] - 40.750063888889) < 0.000001) {
    echo "✅\n";
    $passed++;
  } else {
    echo "❌\n";
    $failed++;
  }
} else {
  echo "    Latitude: MISSING ❌\n";
  $failed++;
}

if (isset($gps_result['longitude'])) {
  echo "    Longitude: {$gps_result['longitude']} ";
  if (abs($gps_result['longitude'] - (-73.983436111111)) < 0.000001) {
    echo "✅\n";
    $passed++;
  } else {
    echo "❌\n";
    $failed++;
  }
} else {
  echo "    Longitude: MISSING ❌\n";
  $failed++;
}

if (isset($gps_result['altitude'])) {
  echo "    Altitude: {$gps_result['altitude']} ";
  if (abs($gps_result['altitude'] - 100.0) < 0.000001) {
    echo "✅\n";
    $passed++;
  } else {
    echo "❌\n";
    $failed++;
  }
} else {
  echo "    Altitude: MISSING ❌\n";
  $failed++;
}

// Test without altitude
echo "\n  Testing GPS without altitude:\n";
$exif_no_alt = [
  'GPSLatitude' => ['37/1', '46/1', '30/100'],
  'GPSLatitudeRef' => 'N',
  'GPSLongitude' => ['122/1', '25/1', '10/100'],
  'GPSLongitudeRef' => 'W',
];

$gps_result2 = $extractor->testExtractGpsFromExif($exif_no_alt);
if (isset($gps_result2['latitude']) && isset($gps_result2['longitude']) && !isset($gps_result2['altitude'])) {
  echo "    Correctly extracted lat/lon without altitude ✅\n";
  $passed++;
} else {
  echo "    Failed to handle missing altitude ❌\n";
  $failed++;
}

echo "\n=== Test Summary ===\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";

if ($failed > 0) {
  echo "\n❌ Some tests failed\n";
  exit(1);
}

echo "\n✅ All MetadataExtractor tests passed!\n";
exit(0);
