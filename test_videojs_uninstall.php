#!/usr/bin/env php
<?php

/**
 * Script to test VideoJS Mediablock uninstall issues
 * This script checks if the problematic configuration entities exist
 */

// Bootstrap Drupal
$autoloader = require_once 'vendor/autoload.php';
\Drupal\Core\DrupalKernel::bootEnvironment();

// Get the site path.
$site_path = \Drupal\Core\DrupalKernel::findSitePath(\Symfony\Component\HttpFoundation\Request::createFromGlobals());

// Bootstrap Drupal kernel.
$kernel = \Drupal\Core\DrupalKernel::createFromRequest(\Symfony\Component\HttpFoundation\Request::createFromGlobals(), $autoloader, 'prod', FALSE, $site_path);
$kernel->boot();
$kernel->preHandle(\Symfony\Component\HttpFoundation\Request::createFromGlobals());

// Initialize the container.
$container = $kernel->getContainer();
\Drupal::setContainer($container);

echo "Checking VideoJS Mediablock configuration entities...\n\n";

// List of entities mentioned in the error
$entities_to_check = [
  'field.field.media.videojs_audio.field_media_videojs_audio_file',
  'field.field.media.videojs_video.field_media_videojs_video_file',
  'field.storage.media.field_media_videojs_audio_file',
  'field.storage.media.field_media_videojs_video_file',
  'media.type.videojs_audio',
  'media.type.videojs_video'
];

$config_factory = \Drupal::configFactory();
$existing_entities = [];

foreach ($entities_to_check as $entity_name) {
  $config = $config_factory->get($entity_name);
  if (!$config->isNew()) {
    $existing_entities[] = $entity_name;
    echo "✗ FOUND: $entity_name exists in active configuration\n";
  } else {
    echo "✓ OK: $entity_name does not exist\n";
  }
}

echo "\n";
if (!empty($existing_entities)) {
  echo "ISSUE CONFIRMED: " . count($existing_entities) . " entities exist that should be cleaned up during uninstall.\n";
  echo "These entities will prevent module reinstallation.\n";
} else {
  echo "No problematic entities found.\n";
}

echo "\nModule status:\n";
$module_handler = \Drupal::moduleHandler();
if ($module_handler->moduleExists('videojs_mediablock')) {
  echo "✓ videojs_mediablock module is currently enabled\n";
} else {
  echo "✗ videojs_mediablock module is not enabled\n";
}
