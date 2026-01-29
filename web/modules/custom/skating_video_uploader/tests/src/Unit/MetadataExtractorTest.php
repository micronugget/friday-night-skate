<?php

declare(strict_types=1);

namespace Drupal\Tests\skating_video_uploader\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\skating_video_uploader\Service\MetadataExtractor;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\videojs_media\VideoJsMediaInterface;
use Drupal\file\FileInterface;

/**
 * Tests the MetadataExtractor service.
 *
 * @group skating_video_uploader
 * @coversDefaultClass \Drupal\skating_video_uploader\Service\MetadataExtractor
 */
class MetadataExtractorTest extends UnitTestCase {

  /**
   * The metadata extractor service.
   *
   * @var \Drupal\skating_video_uploader\Service\MetadataExtractor
   */
  protected $metadataExtractor;

  /**
   * Mock entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $entityTypeManager;

  /**
   * Mock file system.
   *
   * @var \Drupal\Core\File\FileSystemInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $fileSystem;

  /**
   * Mock logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $loggerFactory;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $this->fileSystem = $this->createMock(FileSystemInterface::class);
    $this->loggerFactory = $this->createMock(LoggerChannelFactoryInterface::class);

    $logger = $this->createMock(LoggerChannelInterface::class);
    $this->loggerFactory->method('get')->willReturn($logger);

    $this->metadataExtractor = new MetadataExtractor(
      $this->entityTypeManager,
      $this->loggerFactory,
      $this->fileSystem
    );
  }

  /**
   * Tests GPS location parsing.
   *
   * @covers ::parseGpsLocation
   * @dataProvider gpsLocationProvider
   */
  public function testParseGpsLocation($location, $expected) {
    $method = new \ReflectionMethod($this->metadataExtractor, 'parseGpsLocation');
    $method->setAccessible(TRUE);
    $result = $method->invoke($this->metadataExtractor, $location);
    $this->assertEquals($expected, $result);
  }

  /**
   * Data provider for GPS location parsing tests.
   */
  public function gpsLocationProvider() {
    return [
      'valid with altitude' => [
        '+37.7749-122.4194+100.5/',
        [
          'latitude' => 37.7749,
          'longitude' => -122.4194,
          'altitude' => 100.5,
        ],
      ],
      'valid without altitude' => [
        '+37.7749-122.4194/',
        [
          'latitude' => 37.7749,
          'longitude' => -122.4194,
        ],
      ],
      'negative coordinates' => [
        '-33.8688+151.2093/',
        [
          'latitude' => -33.8688,
          'longitude' => 151.2093,
        ],
      ],
      'invalid format' => [
        'invalid',
        [],
      ],
      'empty string' => [
        '',
        [],
      ],
    ];
  }

  /**
   * Tests extraction with missing file.
   *
   * @covers ::extractMetadata
   */
  public function testExtractMetadataWithMissingFile() {
    $videojs_media = $this->createMock(VideoJsMediaInterface::class);
    $videojs_media->method('id')->willReturn(1);
    $videojs_media->method('bundle')->willReturn('local_video');
    $videojs_media->method('hasField')->willReturn(FALSE);

    $result = $this->metadataExtractor->extractMetadata($videojs_media);
    $this->assertNull($result);
  }

  /**
   * Tests extraction with wrong bundle type.
   *
   * @covers ::extractMetadata
   */
  public function testExtractMetadataWithWrongBundle() {
    $videojs_media = $this->createMock(VideoJsMediaInterface::class);
    $videojs_media->method('id')->willReturn(1);
    $videojs_media->method('bundle')->willReturn('youtube');

    $result = $this->metadataExtractor->extractMetadata($videojs_media);
    $this->assertNull($result);
  }

  /**
   * Tests getting file from VideoJS Media entity.
   *
   * @covers ::getFileFromVideoJsMedia
   */
  public function testGetFileFromVideoJsMediaWithLocalVideo() {
    $file = $this->createMock(FileInterface::class);
    
    $storage = $this->createMock(\Drupal\Core\Entity\EntityStorageInterface::class);
    $storage->method('load')->with(123)->willReturn($file);
    
    $this->entityTypeManager->method('getStorage')->with('file')->willReturn($storage);

    $field_item_list = $this->createMock(\Drupal\Core\Field\FieldItemListInterface::class);
    $field_item_list->method('isEmpty')->willReturn(FALSE);
    $field_item_list->target_id = 123;

    $videojs_media = $this->createMock(VideoJsMediaInterface::class);
    $videojs_media->method('bundle')->willReturn('local_video');
    $videojs_media->method('hasField')->with('field_media_file')->willReturn(TRUE);
    $videojs_media->method('get')->with('field_media_file')->willReturn($field_item_list);

    $method = new \ReflectionMethod($this->metadataExtractor, 'getFileFromVideoJsMedia');
    $method->setAccessible(TRUE);
    $result = $method->invoke($this->metadataExtractor, $videojs_media);
    
    $this->assertInstanceOf(FileInterface::class, $result);
  }

}
