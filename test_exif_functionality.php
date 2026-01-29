#!/usr/bin/env php
<?php

/**
 * @file
 * Test script to verify EXIF functionality and GPS conversion in DDEV.
 */

echo "=== EXIF Functionality Test ===\n\n";

// Check if EXIF extension is loaded.
if (!extension_loaded('exif')) {
  echo "❌ EXIF extension is NOT loaded\n";
  exit(1);
}
echo "✅ EXIF extension is loaded\n\n";

// Test GPS coordinate conversion function.
echo "Testing GPS coordinate conversion:\n";

function convertExifRational(string $rational): float {
  if (strpos($rational, '/') === FALSE) {
    return (float) $rational;
  }
  
  [$numerator, $denominator] = explode('/', $rational, 2);
  if ($denominator == 0) {
    return 0.0;
  }
  
  return (float) $numerator / (float) $denominator;
}

function convertGpsCoordinate(array $coordinate, string $hemisphere): ?float {
  if (count($coordinate) < 3) {
    return NULL;
  }
  
  $degrees = convertExifRational($coordinate[0]);
  $minutes = convertExifRational($coordinate[1]);
  $seconds = convertExifRational($coordinate[2]);
  
  // Validate values are in expected ranges.
  if ($degrees < 0 || $degrees > 180) {
    return NULL;
  }
  if ($minutes < 0 || $minutes >= 60) {
    return NULL;
  }
  if ($seconds < 0 || $seconds >= 60) {
    return NULL;
  }
  
  $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);
  
  // Apply hemisphere (negative for South and West).
  if ($hemisphere === 'S' || $hemisphere === 'W') {
    $decimal = -$decimal;
  }
  
  return $decimal;
}

// Test cases
$test_cases = [
  [
    'lat' => ['40/1', '45/1', '23/100'],
    'lat_ref' => 'N',
    'lon' => ['73/1', '59/1', '37/100'],
    'lon_ref' => 'W',
    'expected_lat' => 40.750063888889,
    'expected_lon' => -73.983436111111,
  ],
  [
    'lat' => ['37/1', '46/1', '30/100'],
    'lat_ref' => 'N',
    'lon' => ['122/1', '25/1', '10/100'],
    'lon_ref' => 'W',
    'expected_lat' => 37.76675,
    'expected_lon' => -122.41669444444,
  ],
];

$passed = 0;
$failed = 0;

foreach ($test_cases as $i => $test) {
  $lat = convertGpsCoordinate($test['lat'], $test['lat_ref']);
  $lon = convertGpsCoordinate($test['lon'], $test['lon_ref']);
  
  echo "\nTest " . ($i + 1) . ":\n";
  echo "  Input: {$test['lat'][0]} {$test['lat'][1]} {$test['lat'][2]} {$test['lat_ref']}, ";
  echo "{$test['lon'][0]} {$test['lon'][1]} {$test['lon'][2]} {$test['lon_ref']}\n";
  echo "  Expected: {$test['expected_lat']}, {$test['expected_lon']}\n";
  echo "  Got:      {$lat}, {$lon}\n";
  
  if (abs($lat - $test['expected_lat']) < 0.000001 && 
      abs($lon - $test['expected_lon']) < 0.000001) {
    echo "  ✅ PASS\n";
    $passed++;
  }
  else {
    echo "  ❌ FAIL\n";
    $failed++;
  }
}

echo "\n=== Test Summary ===\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";

if ($failed > 0) {
  exit(1);
}

echo "\n✅ All EXIF functionality tests passed!\n";
exit(0);
