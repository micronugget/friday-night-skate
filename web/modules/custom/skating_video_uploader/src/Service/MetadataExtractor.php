<?php

namespace Drupal\skating_video_uploader\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\media\MediaInterface;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Exception;

/**
 * Service for extracting metadata from video files.
 */
class MetadataExtractor {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * Constructs a new MetadataExtractor object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system service.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    LoggerChannelFactoryInterface $logger_factory,
    FileSystemInterface $file_system
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->loggerFactory = $logger_factory->get('skating_video_uploader');
    $this->fileSystem = $file_system;
  }

  /**
   * Extracts metadata from a video file and stores it in the database.
   *
   * @param \Drupal\media\MediaInterface $media
   *   The media entity containing the video file.
   *
   * @return array|null
   *   An array of metadata or NULL if extraction failed.
   */
  public function extractMetadata(MediaInterface $media) {
    try {
      // Get the file entity from the media entity.
      $file = $this->getFileFromMedia($media);
      if (!$file) {
        $this->loggerFactory->error('No file found in media entity @id', ['@id' => $media->id()]);
        return NULL;
      }

      // Get the file URI and convert it to a local path.
      $uri = $file->getFileUri();
      $local_path = $this->fileSystem->realpath($uri);

      if (!$local_path || !file_exists($local_path)) {
        $this->loggerFactory->error('File not found at @uri', ['@uri' => $uri]);
        return NULL;
      }

      // Extract metadata using FFProbe.
      $ffprobe = FFProbe::create();
      $format = $ffprobe->format($local_path);
      $streams = $ffprobe->streams($local_path);
      $video_stream = NULL;

      // Find the video stream.
      foreach ($streams as $stream) {
        if ($stream->isVideo()) {
          $video_stream = $stream;
          break;
        }
      }

      if (!$video_stream) {
        $this->loggerFactory->error('No video stream found in file @uri', ['@uri' => $uri]);
        return NULL;
      }

      // Extract basic metadata.
      $metadata = [
        'media_id' => $media->id(),
        'file_id' => $file->id(),
        'creation_time' => $format->get('creation_time', ''),
        'duration' => $format->get('duration', 0),
        'created' => time(),
        'changed' => time(),
      ];

      // Extract GPS metadata if available.
      $tags = $format->get('tags', []);
      if (isset($tags['location'])) {
        // Parse location string (format: "+35.6812+139.7671/")
        $location = $tags['location'];
        preg_match('/([+-]\d+\.\d+)([+-]\d+\.\d+)/', $location, $matches);
        if (count($matches) >= 3) {
          $metadata['latitude'] = (float) $matches[1];
          $metadata['longitude'] = (float) $matches[2];
        }
      }

      // Extract additional GPS metadata if available.
      if (isset($tags['com.apple.quicktime.location.ISO6709'])) {
        $location = $tags['com.apple.quicktime.location.ISO6709'];
        preg_match('/([+-]\d+\.\d+)([+-]\d+\.\d+)([+-]\d+\.\d+)?/', $location, $matches);
        if (count($matches) >= 3) {
          $metadata['latitude'] = (float) $matches[1];
          $metadata['longitude'] = (float) $matches[2];
          if (isset($matches[3])) {
            $metadata['altitude'] = (float) $matches[3];
          }
        }
      }

      // Extract timecode data if available.
      $timecode_data = [];
      if ($video_stream->has('timecode')) {
        $timecode_data['timecode'] = $video_stream->get('timecode');
      }

      // Add any additional timecode-related metadata.
      if (!empty($timecode_data)) {
        $metadata['timecode_data'] = serialize($timecode_data);
      }

      // Store the metadata in the database.
      $this->storeMetadata($metadata);

      return $metadata;
    }
    catch (Exception $e) {
      $this->loggerFactory->error('Error extracting metadata: @error', ['@error' => $e->getMessage()]);
      return NULL;
    }
  }

  /**
   * Gets the file entity from a media entity.
   *
   * @param \Drupal\media\MediaInterface $media
   *   The media entity.
   *
   * @return \Drupal\file\FileInterface|null
   *   The file entity or NULL if not found.
   */
  protected function getFileFromMedia(MediaInterface $media) {
    // Check if this is a videojs_video media entity.
    if ($media->bundle() == 'videojs_video') {
      $field_name = 'field_media_videojs_video_file';
    }
    // Check if this is a standard video media entity.
    elseif ($media->bundle() == 'video') {
      $field_name = 'field_media_video_file';
    }
    else {
      return NULL;
    }

    // Get the file entity from the media entity.
    if ($media->hasField($field_name) && !$media->get($field_name)->isEmpty()) {
      $target_id = $media->get($field_name)->target_id;
      return $this->entityTypeManager->getStorage('file')->load($target_id);
    }

    return NULL;
  }

  /**
   * Stores metadata in the database.
   *
   * @param array $metadata
   *   The metadata to store.
   *
   * @return int|null
   *   The ID of the stored metadata or NULL if storage failed.
   */
  protected function storeMetadata(array $metadata) {
    try {
      // Check if metadata already exists for this media entity.
      $connection = \Drupal::database();
      $existing = $connection->select('skating_video_metadata', 'm')
        ->fields('m', ['id'])
        ->condition('media_id', $metadata['media_id'])
        ->execute()
        ->fetchField();

      if ($existing) {
        // Update existing metadata.
        $metadata['changed'] = time();
        $connection->update('skating_video_metadata')
          ->fields($metadata)
          ->condition('id', $existing)
          ->execute();
        return $existing;
      }
      else {
        // Insert new metadata.
        return $connection->insert('skating_video_metadata')
          ->fields($metadata)
          ->execute();
      }
    }
    catch (Exception $e) {
      $this->loggerFactory->error('Error storing metadata: @error', ['@error' => $e->getMessage()]);
      return NULL;
    }
  }

}
