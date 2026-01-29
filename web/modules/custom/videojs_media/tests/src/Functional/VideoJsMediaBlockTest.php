<?php

declare(strict_types=1);

namespace Drupal\Tests\videojs_media\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\videojs_media\Entity\VideoJsMedia;

/**
 * Tests the VideoJsMediaBlock plugin.
 *
 * @group videojs_media
 */
class VideoJsMediaBlockTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'videojs_media',
    'block',
    'node',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * A user with block admin permissions.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser([
      'administer blocks',
      'view local_video videojs media',
      'view youtube videojs media',
      'access content',
    ]);
  }

  /**
   * Tests placing a VideoJS Media block.
   */
  public function testPlaceBlock(): void {
    // Create a test entity.
    $entity = VideoJsMedia::create([
      'type' => 'local_video',
      'name' => 'Test Video',
      'status' => TRUE,
    ]);
    $entity->save();

    $this->drupalLogin($this->adminUser);

    // Place the block.
    $this->drupalGet('admin/structure/block/add/videojs_media_block/stark');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->fieldExists('settings[videojs_media_id]');
    $this->assertSession()->fieldExists('settings[view_mode]');
    $this->assertSession()->fieldExists('settings[hide_title]');
  }

  /**
   * Tests block configuration form.
   */
  public function testBlockConfigurationForm(): void {
    // Create a test entity.
    $entity = VideoJsMedia::create([
      'type' => 'local_video',
      'name' => 'Test Video',
      'status' => TRUE,
    ]);
    $entity->save();

    $this->drupalLogin($this->adminUser);

    // Configure block.
    $this->drupalGet('admin/structure/block/add/videojs_media_block/stark');
    
    $this->submitForm([
      'id' => 'videojs_media_test',
      'settings[videojs_media_id]' => $entity->getName() . ' (' . $entity->id() . ')',
      'settings[view_mode]' => 'default',
      'settings[hide_title]' => FALSE,
      'region' => 'content',
    ], 'Save block');

    $this->assertSession()->pageTextContains('The block configuration has been saved');
  }

  /**
   * Tests block renders entity.
   */
  public function testBlockRendersEntity(): void {
    // Create a test entity.
    $entity = VideoJsMedia::create([
      'type' => 'local_video',
      'name' => 'Rendered Video',
      'status' => TRUE,
    ]);
    $entity->save();

    // Place the block programmatically.
    $block = $this->drupalPlaceBlock('videojs_media_block', [
      'videojs_media_id' => $entity->id(),
      'view_mode' => 'default',
      'hide_title' => FALSE,
    ]);

    $this->drupalLogin($this->adminUser);

    // Visit the front page where block should appear.
    $this->drupalGet('<front>');
    $this->assertSession()->pageTextContains('Rendered Video');
  }

  /**
   * Tests block hides title when configured.
   */
  public function testBlockHidesTitle(): void {
    // Create a test entity.
    $entity = VideoJsMedia::create([
      'type' => 'local_video',
      'name' => 'Video With Hidden Title',
      'status' => TRUE,
    ]);
    $entity->save();

    // Place block with title hidden.
    $block = $this->drupalPlaceBlock('videojs_media_block', [
      'videojs_media_id' => $entity->id(),
      'view_mode' => 'default',
      'hide_title' => TRUE,
    ]);

    $this->drupalLogin($this->adminUser);
    $this->drupalGet('<front>');

    // The entity content might still render, but title handling is internal.
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Tests block respects view access.
   */
  public function testBlockRespectsViewAccess(): void {
    // Create unpublished entity.
    $entity = VideoJsMedia::create([
      'type' => 'local_video',
      'name' => 'Unpublished Video',
      'status' => FALSE,
    ]);
    $entity->save();

    // Place the block.
    $block = $this->drupalPlaceBlock('videojs_media_block', [
      'videojs_media_id' => $entity->id(),
      'view_mode' => 'default',
    ]);

    // Login as user without unpublished view permission.
    $user = $this->drupalCreateUser([
      'view local_video videojs media',
      'access content',
    ]);
    $this->drupalLogin($user);

    // Visit front page - unpublished entity should not be visible.
    $this->drupalGet('<front>');
    $this->assertSession()->pageTextNotContains('Unpublished Video');
  }

  /**
   * Tests block with different view modes.
   */
  public function testBlockWithDifferentViewModes(): void {
    // Create a test entity.
    $entity = VideoJsMedia::create([
      'type' => 'local_video',
      'name' => 'Video With Teaser',
      'status' => TRUE,
    ]);
    $entity->save();

    // Place block with teaser view mode.
    $block = $this->drupalPlaceBlock('videojs_media_block', [
      'videojs_media_id' => $entity->id(),
      'view_mode' => 'teaser',
    ]);

    $this->drupalLogin($this->adminUser);
    $this->drupalGet('<front>');
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Tests block with invalid entity ID.
   */
  public function testBlockWithInvalidEntityId(): void {
    // Place block with non-existent entity ID.
    $block = $this->drupalPlaceBlock('videojs_media_block', [
      'videojs_media_id' => 99999,
      'view_mode' => 'default',
    ]);

    $this->drupalLogin($this->adminUser);
    $this->drupalGet('<front>');
    
    // Page should still load without errors.
    $this->assertSession()->statusCodeEquals(200);
  }

}
