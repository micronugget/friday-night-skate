<?php

namespace Drupal\skating_video_uploader\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\media\MediaInterface;
use Drupal\block_content\BlockContentInterface;
use Exception;

/**
 * Service for processing video entities and coordinating metadata extraction and YouTube uploads.
 */
class VideoProcessor {

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
   * The metadata extractor service.
   *
   * @var \Drupal\skating_video_uploader\Service\MetadataExtractor
   */
  protected $metadataExtractor;

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
   * Constructs a new VideoProcessor object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory.
   * @param \Drupal\skating_video_uploader\Service\MetadataExtractor $metadata_extractor
   *   The metadata extractor service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    LoggerChannelFactoryInterface $logger_factory,
    MetadataExtractor $metadata_extractor,
    ConfigFactoryInterface $config_factory,
    MessengerInterface $messenger
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->loggerFactory = $logger_factory->get('skating_video_uploader');
    $this->metadataExtractor = $metadata_extractor;
    $this->configFactory = $config_factory;
    $this->messenger = $messenger;
  }

  /**
   * Processes an entity for metadata extraction and YouTube upload.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to process.
   *
   * @return bool
   *   TRUE if processing was successful, FALSE otherwise.
   */
  public function processEntity(EntityInterface $entity) {
    try {
      // If this is a block content entity, extract the media entity.
      if ($entity instanceof BlockContentInterface && $entity->bundle() == 'videojs_mediablock') {
        $media = $this->getMediaFromBlock($entity);
        if (!$media) {
          $this->loggerFactory->error('No media found in block entity @id', ['@id' => $entity->id()]);
          $this->messenger->addError(t('No media found in the block.'));
          return FALSE;
        }
      }
      // If this is already a media entity, use it directly.
      elseif ($entity instanceof MediaInterface) {
        $media = $entity;
      }
      else {
        $this->loggerFactory->error('Unsupported entity type @type', ['@type' => $entity->getEntityTypeId()]);
        $this->messenger->addError(t('Unsupported entity type.'));
        return FALSE;
      }

      // Extract metadata from the media entity.
      $metadata = $this->metadataExtractor->extractMetadata($media);
      if (!$metadata) {
        $this->loggerFactory->error('Failed to extract metadata from media entity @id', ['@id' => $media->id()]);
        $this->messenger->addError(t('Failed to extract metadata from the video.'));
        return FALSE;
      }

      // Update the metadata with consent information.
      $this->updateConsentStatus($media->id(), TRUE);

      // Upload the video to YouTube.
      $youtube_uploader = \Drupal::service('skating_video_uploader.youtube_uploader');
      $youtube_id = $youtube_uploader->uploadVideo($media, $metadata);
      if (!$youtube_id) {
        $this->loggerFactory->error('Failed to upload video to YouTube for media entity @id', ['@id' => $media->id()]);
        $this->messenger->addError(t('Failed to upload the video to YouTube.'));
        return FALSE;
      }

      // Update the metadata with the YouTube ID.
      $this->updateYouTubeId($media->id(), $youtube_id);

      $this->messenger->addStatus(t('Video successfully processed and uploaded to YouTube.'));
      return TRUE;
    }
    catch (Exception $e) {
      $this->loggerFactory->error('Error processing entity: @error', ['@error' => $e->getMessage()]);
      $this->messenger->addError(t('Error processing the video: @error', ['@error' => $e->getMessage()]));
      return FALSE;
    }
  }

  /**
   * Gets the media entity from a block entity.
   *
   * @param \Drupal\block_content\BlockContentInterface $block
   *   The block entity.
   *
   * @return \Drupal\media\MediaInterface|null
   *   The media entity or NULL if not found.
   */
  protected function getMediaFromBlock(BlockContentInterface $block) {
    // Check if this is a videojs_mediablock block.
    if ($block->bundle() != 'videojs_mediablock') {
      return NULL;
    }

    // Get the media location field value.
    if ($block->hasField('field_videojs_media_location') && !$block->get('field_videojs_media_location')->isEmpty()) {
      $media_location = $block->get('field_videojs_media_location')->value;
    }
    else {
      return NULL;
    }

    // Determine which field to use based on the media location.
    $field_name = NULL;
    switch ($media_location) {
      case 'local':
        if ($block->hasField('field_videojs_local') && !$block->get('field_videojs_local')->isEmpty()) {
          $field_name = 'field_videojs_local';
        }
        break;

      case 'remote':
        if ($block->hasField('field_videojs_remote') && !$block->get('field_videojs_remote')->isEmpty()) {
          $field_name = 'field_videojs_remote';
        }
        break;

      case 'youtube':
        // YouTube URLs are stored as strings, not media references.
        return NULL;
    }

    // Get the media entity from the block.
    if ($field_name && $block->hasField($field_name) && !$block->get($field_name)->isEmpty()) {
      $target_id = $block->get($field_name)->target_id;
      return $this->entityTypeManager->getStorage('media')->load($target_id);
    }

    return NULL;
  }

  /**
   * Updates the consent status for a media entity's metadata.
   *
   * @param int $media_id
   *   The media entity ID.
   * @param bool $consent
   *   The consent status.
   *
   * @return bool
   *   TRUE if the update was successful, FALSE otherwise.
   */
  protected function updateConsentStatus($media_id, $consent) {
    try {
      $connection = \Drupal::database();
      $connection->update('skating_video_metadata')
        ->fields([
          'consent_given' => $consent ? 1 : 0,
          'changed' => time(),
        ])
        ->condition('media_id', $media_id)
        ->execute();
      return TRUE;
    }
    catch (Exception $e) {
      $this->loggerFactory->error('Error updating consent status: @error', ['@error' => $e->getMessage()]);
      return FALSE;
    }
  }

  /**
   * Updates the YouTube ID for a media entity's metadata.
   *
   * @param int $media_id
   *   The media entity ID.
   * @param string $youtube_id
   *   The YouTube video ID.
   *
   * @return bool
   *   TRUE if the update was successful, FALSE otherwise.
   */
  protected function updateYouTubeId($media_id, $youtube_id) {
    try {
      $connection = \Drupal::database();
      $connection->update('skating_video_metadata')
        ->fields([
          'youtube_id' => $youtube_id,
          'changed' => time(),
        ])
        ->condition('media_id', $media_id)
        ->execute();
      return TRUE;
    }
    catch (Exception $e) {
      $this->loggerFactory->error('Error updating YouTube ID: @error', ['@error' => $e->getMessage()]);
      return FALSE;
    }
  }

}
