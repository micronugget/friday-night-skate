<?php

declare(strict_types=1);

namespace Drupal\skating_video_uploader\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\videojs_media\VideoJsMediaInterface;
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
   * @param \Drupal\videojs_media\VideoJsMediaInterface $videojs_media
   *   The VideoJS Media entity containing the video file.
   *
   * @return array|null
   *   An array of metadata or NULL if extraction failed.
   */
  public function extractMetadata(VideoJsMediaInterface $videojs_media) {
    try {
      // Get the file entity from the VideoJS Media entity.
      $file = $this->getFileFromVideoJsMedia($videojs_media);
      if (!$file) {
        $this->loggerFactory->error('No file found in VideoJS Media entity @id', ['@id' => $videojs_media->id()]);
        return NULL;
      }

      // Get the file URI and convert it to a local path.
      $uri = $file->getFileUri();
      $local_path = $this->fileSystem->realpath($uri);

      if (!$local_path || !file_exists($local_path)) {
        $this->loggerFactory->error('File not found at @uri', ['@uri' => $uri]);
        return NULL;
      }

      // Extract metadata using ffprobe command.
      $metadata = $this->extractWithFFProbe($local_path);
      if (!$metadata) {
        $this->loggerFactory->error('Failed to extract metadata from @uri', ['@uri' => $uri]);
        return NULL;
      }

      // Add VideoJS Media and file IDs.
      $metadata['videojs_media_id'] = $videojs_media->id();
      $metadata['file_id'] = $file->id();
      $metadata['created'] = time();
      $metadata['changed'] = time();

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
   * Extracts metadata from a video file using ffprobe.
   *
   * @param string $file_path
   *   The local path to the video file.
   *
   * @return array|null
   *   An array of metadata or NULL if extraction failed.
   */
  protected function extractWithFFProbe(string $file_path): ?array {
    // Escape the file path for shell command.
    $escaped_path = escapeshellarg($file_path);

    // Run ffprobe to get metadata in JSON format.
    $command = "ffprobe -v quiet -print_format json -show_format -show_streams {$escaped_path} 2>&1";
    $output = shell_exec($command);

    if (!$output) {
      $this->loggerFactory->error('ffprobe command failed or produced no output');
      return NULL;
    }

    // Decode the JSON output.
    $data = json_decode($output, TRUE);
    if (json_last_error() !== JSON_ERROR_NONE) {
      $this->loggerFactory->error('Failed to decode ffprobe output: @error', ['@error' => json_last_error_msg()]);
      return NULL;
    }

    $metadata = [];

    // Extract basic format metadata.
    if (isset($data['format'])) {
      $format = $data['format'];
      if (isset($format['duration'])) {
        $metadata['duration'] = (float) $format['duration'];
      }

      // Extract GPS and creation time from format tags.
      if (isset($format['tags'])) {
        $tags = $format['tags'];

        // Try different tag names for creation time.
        foreach (['creation_time', 'com.apple.quicktime.creationdate', 'date'] as $tag_name) {
          if (isset($tags[$tag_name])) {
            $metadata['creation_time'] = $tags[$tag_name];
            break;
          }
        }

        // Extract GPS metadata - format: "+35.6812+139.7671/" or similar.
        if (isset($tags['location'])) {
          $gps_data = $this->parseGpsLocation($tags['location']);
          if ($gps_data) {
            $metadata = array_merge($metadata, $gps_data);
          }
        }

        // Try Apple's location format.
        if (isset($tags['com.apple.quicktime.location.ISO6709'])) {
          $gps_data = $this->parseGpsLocation($tags['com.apple.quicktime.location.ISO6709']);
          if ($gps_data) {
            $metadata = array_merge($metadata, $gps_data);
          }
        }
      }
    }

    // Extract timecode data from video stream.
    if (isset($data['streams']) && is_array($data['streams'])) {
      $timecode_data = [];
      foreach ($data['streams'] as $stream) {
        if (isset($stream['codec_type']) && $stream['codec_type'] === 'video') {
          // Extract timecode if available.
          if (isset($stream['tags']['timecode'])) {
            $timecode_data['timecode'] = $stream['tags']['timecode'];
          }
          // Add frame rate information.
          if (isset($stream['r_frame_rate'])) {
            $timecode_data['frame_rate'] = $stream['r_frame_rate'];
          }
          break;
        }
      }

      if (!empty($timecode_data)) {
        $metadata['timecode_data'] = serialize($timecode_data);
      }
    }

    return $metadata;
  }

  /**
   * Parses GPS location string into latitude, longitude, and altitude.
   *
   * @param string $location
   *   The location string (e.g., "+35.6812+139.7671/" or "+35.6812+139.7671+100.5/").
   *
   * @return array
   *   An array with latitude, longitude, and optionally altitude.
   */
  protected function parseGpsLocation(string $location): array {
    $result = [];

    // Parse location string - format: "+35.6812+139.7671/" or "+35.6812+139.7671+100.5/".
    if (preg_match('/([+-]\d+\.\d+)([+-]\d+\.\d+)([+-]\d+\.\d+)?/', $location, $matches)) {
      $result['latitude'] = (float) $matches[1];
      $result['longitude'] = (float) $matches[2];
      if (isset($matches[3]) && $matches[3] !== '') {
        $result['altitude'] = (float) $matches[3];
      }
    }

    return $result;
  }

  /**
   * Gets the file entity from a VideoJS Media entity.
   *
   * @param \Drupal\videojs_media\VideoJsMediaInterface $videojs_media
   *   The VideoJS Media entity.
   *
   * @return \Drupal\file\FileInterface|null
   *   The file entity or NULL if not found.
   */
  protected function getFileFromVideoJsMedia(VideoJsMediaInterface $videojs_media) {
    // Check if this is a local_video or local_audio VideoJS Media entity.
    $bundle = $videojs_media->bundle();
    if ($bundle !== 'local_video' && $bundle !== 'local_audio') {
      return NULL;
    }

    // VideoJS Media uses field_media_file for local video/audio files.
    $field_name = 'field_media_file';

    // Get the file entity from the VideoJS Media entity.
    if ($videojs_media->hasField($field_name) && !$videojs_media->get($field_name)->isEmpty()) {
      $target_id = $videojs_media->get($field_name)->target_id;
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
      // Check if metadata already exists for this VideoJS Media entity.
      $connection = \Drupal::database();
      $existing = $connection->select('skating_video_metadata', 'm')
        ->fields('m', ['id'])
        ->condition('videojs_media_id', $metadata['videojs_media_id'])
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
