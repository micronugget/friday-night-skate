<?php

namespace Drupal\skating_video_uploader\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\media\MediaInterface;
use Exception;
use Google_Client;
use Google_Service_YouTube;
use Google_Service_YouTube_Video;
use Google_Service_YouTube_VideoSnippet;
use Google_Service_YouTube_VideoStatus;
use Google_Http_MediaFileUpload;

/**
 * Service for uploading videos to YouTube.
 */
class YouTubeUploader {

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
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a new YouTubeUploader object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    LoggerChannelFactoryInterface $logger_factory,
    ConfigFactoryInterface $config_factory,
    MessengerInterface $messenger
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->loggerFactory = $logger_factory->get('skating_video_uploader');
    $this->configFactory = $config_factory;
    $this->messenger = $messenger;
  }

  /**
   * Uploads a video to YouTube.
   *
   * @param \Drupal\media\MediaInterface $media
   *   The media entity containing the video file.
   * @param array $metadata
   *   The metadata extracted from the video file.
   *
   * @return string|null
   *   The YouTube video ID if upload was successful, NULL otherwise.
   */
  public function uploadVideo(MediaInterface $media, array $metadata) {
    try {
      // Get the file entity from the media entity.
      $file = $this->getFileFromMedia($media);
      if (!$file) {
        $this->loggerFactory->error('No file found in media entity @id', ['@id' => $media->id()]);
        return NULL;
      }

      // Get the file URI and convert it to a local path.
      $uri = $file->getFileUri();
      $local_path = \Drupal::service('file_system')->realpath($uri);

      if (!$local_path || !file_exists($local_path)) {
        $this->loggerFactory->error('File not found at @uri', ['@uri' => $uri]);
        return NULL;
      }

      // Initialize the Google client.
      $client = $this->getAuthorizedClient();
      if (!$client) {
        $this->loggerFactory->error('Failed to initialize Google client');
        return NULL;
      }

      // Create a YouTube service object.
      $youtube = new Google_Service_YouTube($client);

      // Prepare the video metadata.
      $snippet = new Google_Service_YouTube_VideoSnippet();
      $snippet->setTitle($media->label() ?: 'Skating Video ' . date('Y-m-d H:i:s'));
      $snippet->setDescription('Uploaded from Friday Night Skate Club website');
      $snippet->setTags(['skating', 'friday night skate', 'club']);
      $snippet->setCategoryId('17'); // Sports category

      // Set the video status.
      $status = new Google_Service_YouTube_VideoStatus();
      $status->setPrivacyStatus('unlisted'); // 'private', 'public', or 'unlisted'

      // Create a video resource with the snippet and status.
      $video = new Google_Service_YouTube_Video();
      $video->setSnippet($snippet);
      $video->setStatus($status);

      // Set up the chunked upload.
      $client->setDefer(true);
      $insertRequest = $youtube->videos->insert('snippet,status', $video);
      $media_file = new Google_Http_MediaFileUpload(
        $client,
        $insertRequest,
        'video/*',
        NULL,
        TRUE,
        1 * 1024 * 1024 // Chunk size in bytes (1MB)
      );
      $media_file->setFileSize(filesize($local_path));

      // Upload the file in chunks.
      $status = FALSE;
      $handle = fopen($local_path, 'rb');
      while (!$status && !feof($handle)) {
        $chunk = fread($handle, 1 * 1024 * 1024);
        $status = $media_file->nextChunk($chunk);
      }
      fclose($handle);

      // If upload was successful, return the YouTube video ID.
      if ($status) {
        $youtube_id = $status->getId();
        $this->loggerFactory->notice('Video uploaded to YouTube with ID @id', ['@id' => $youtube_id]);
        return $youtube_id;
      }
      else {
        $this->loggerFactory->error('Failed to upload video to YouTube');
        return NULL;
      }
    }
    catch (Exception $e) {
      $this->loggerFactory->error('Error uploading video to YouTube: @error', ['@error' => $e->getMessage()]);
      return NULL;
    }
  }

  /**
   * Gets an authorized Google client.
   *
   * @return \Google_Client|null
   *   The authorized Google client or NULL if authorization failed.
   */
  protected function getAuthorizedClient() {
    try {
      // Create a Google client.
      $client = new Google_Client();
      $client->setApplicationName('Friday Night Skate Club Video Uploader');
      $client->setScopes([
        'https://www.googleapis.com/auth/youtube.upload',
      ]);

      // Set the client ID, client secret, and redirect URI from configuration.
      $config = $this->configFactory->get('skating_video_uploader.settings');
      $client->setClientId($config->get('youtube_client_id'));
      $client->setClientSecret($config->get('youtube_client_secret'));
      $client->setRedirectUri($config->get('youtube_redirect_uri'));

      // Set the access token from configuration.
      $access_token = $config->get('youtube_access_token');
      if ($access_token) {
        $client->setAccessToken($access_token);
      }

      // Refresh the token if it's expired.
      if ($client->isAccessTokenExpired()) {
        $refresh_token = $config->get('youtube_refresh_token');
        if ($refresh_token) {
          $client->refreshToken($refresh_token);
          // Save the new access token to configuration.
          $this->saveAccessToken($client->getAccessToken());
        }
        else {
          $this->loggerFactory->error('No refresh token available');
          return NULL;
        }
      }

      return $client;
    }
    catch (Exception $e) {
      $this->loggerFactory->error('Error initializing Google client: @error', ['@error' => $e->getMessage()]);
      return NULL;
    }
  }

  /**
   * Saves the access token to configuration.
   *
   * @param string $access_token
   *   The access token to save.
   */
  protected function saveAccessToken($access_token) {
    $config = $this->configFactory->getEditable('skating_video_uploader.settings');
    $config->set('youtube_access_token', $access_token)->save();
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

}
