<?php

declare(strict_types=1);

namespace Drupal\Tests\videojs_media\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\user\Entity\Role;
use Drupal\videojs_media\Entity\VideoJsMedia;

/**
 * Tests permissions for VideoJsMedia entities per bundle.
 *
 * @group videojs_media
 */
class VideoJsMediaPermissionsTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'videojs_media',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests create permission per bundle.
   *
   * @dataProvider bundleProvider
   */
  public function testCreatePermission(string $bundle): void {
    // Create user with specific create permission.
    $user = $this->drupalCreateUser([
      "create {$bundle} videojs media",
      'access content',
    ]);

    $this->drupalLogin($user);

    // User should be able to access the add form.
    $this->drupalGet("/videojs-media/add/{$bundle}");
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Tests view published permission per bundle.
   *
   * @dataProvider bundleProvider
   */
  public function testViewPublishedPermission(string $bundle): void {
    // Create published entity.
    $entity = VideoJsMedia::create([
      'type' => $bundle,
      'name' => "Published {$bundle}",
      'status' => TRUE,
    ]);
    $entity->save();

    // User with view permission.
    $user = $this->drupalCreateUser([
      "view {$bundle} videojs media",
      'access content',
    ]);

    $this->drupalLogin($user);
    $this->drupalGet("/videojs-media/{$entity->id()}");
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Tests view unpublished permission per bundle.
   *
   * @dataProvider bundleProvider
   */
  public function testViewUnpublishedPermission(string $bundle): void {
    // Create unpublished entity.
    $entity = VideoJsMedia::create([
      'type' => $bundle,
      'name' => "Unpublished {$bundle}",
      'status' => FALSE,
    ]);
    $entity->save();

    // User without unpublished view permission.
    $user_without = $this->drupalCreateUser([
      "view {$bundle} videojs media",
      'access content',
    ]);

    $this->drupalLogin($user_without);
    $this->drupalGet("/videojs-media/{$entity->id()}");
    $this->assertSession()->statusCodeEquals(403);

    // User with unpublished view permission.
    $user_with = $this->drupalCreateUser([
      "view unpublished {$bundle} videojs media",
      'access content',
    ]);

    $this->drupalLogin($user_with);
    $this->drupalGet("/videojs-media/{$entity->id()}");
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Tests edit own permission per bundle.
   *
   * @dataProvider bundleProvider
   */
  public function testEditOwnPermission(string $bundle): void {
    // Create owner.
    $owner = $this->drupalCreateUser([
      "create {$bundle} videojs media",
      "edit own {$bundle} videojs media",
      'access content',
    ]);

    // Create entity owned by owner.
    $entity = VideoJsMedia::create([
      'type' => $bundle,
      'name' => "Owned {$bundle}",
      'status' => TRUE,
      'uid' => $owner->id(),
    ]);
    $entity->save();

    // Owner can edit.
    $this->drupalLogin($owner);
    $this->drupalGet("/videojs-media/{$entity->id()}/edit");
    $this->assertSession()->statusCodeEquals(200);

    // Other user cannot edit.
    $other = $this->drupalCreateUser([
      "edit own {$bundle} videojs media",
      'access content',
    ]);
    $this->drupalLogin($other);
    $this->drupalGet("/videojs-media/{$entity->id()}/edit");
    $this->assertSession()->statusCodeEquals(403);
  }

  /**
   * Tests edit any permission per bundle.
   *
   * @dataProvider bundleProvider
   */
  public function testEditAnyPermission(string $bundle): void {
    // Create owner.
    $owner = $this->drupalCreateUser();

    // Create entity owned by owner.
    $entity = VideoJsMedia::create([
      'type' => $bundle,
      'name' => "Any {$bundle}",
      'status' => TRUE,
      'uid' => $owner->id(),
    ]);
    $entity->save();

    // Editor can edit any entity.
    $editor = $this->drupalCreateUser([
      "edit any {$bundle} videojs media",
      'access content',
    ]);

    $this->drupalLogin($editor);
    $this->drupalGet("/videojs-media/{$entity->id()}/edit");
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Tests delete own permission per bundle.
   *
   * @dataProvider bundleProvider
   */
  public function testDeleteOwnPermission(string $bundle): void {
    // Create owner.
    $owner = $this->drupalCreateUser([
      "create {$bundle} videojs media",
      "delete own {$bundle} videojs media",
      'access content',
    ]);

    // Create entity owned by owner.
    $entity = VideoJsMedia::create([
      'type' => $bundle,
      'name' => "Delete Own {$bundle}",
      'status' => TRUE,
      'uid' => $owner->id(),
    ]);
    $entity->save();

    // Owner can delete.
    $this->drupalLogin($owner);
    $this->drupalGet("/videojs-media/{$entity->id()}/delete");
    $this->assertSession()->statusCodeEquals(200);

    // Other user cannot delete.
    $other = $this->drupalCreateUser([
      "delete own {$bundle} videojs media",
      'access content',
    ]);
    
    $entity2 = VideoJsMedia::create([
      'type' => $bundle,
      'name' => "Another {$bundle}",
      'status' => TRUE,
      'uid' => $owner->id(),
    ]);
    $entity2->save();

    $this->drupalLogin($other);
    $this->drupalGet("/videojs-media/{$entity2->id()}/delete");
    $this->assertSession()->statusCodeEquals(403);
  }

  /**
   * Tests delete any permission per bundle.
   *
   * @dataProvider bundleProvider
   */
  public function testDeleteAnyPermission(string $bundle): void {
    // Create owner.
    $owner = $this->drupalCreateUser();

    // Create entity owned by owner.
    $entity = VideoJsMedia::create([
      'type' => $bundle,
      'name' => "Delete Any {$bundle}",
      'status' => TRUE,
      'uid' => $owner->id(),
    ]);
    $entity->save();

    // Deleter can delete any entity.
    $deleter = $this->drupalCreateUser([
      "delete any {$bundle} videojs media",
      'access content',
    ]);

    $this->drupalLogin($deleter);
    $this->drupalGet("/videojs-media/{$entity->id()}/delete");
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Tests administer permission grants all access.
   */
  public function testAdministerPermission(): void {
    // Create admin user.
    $admin = $this->drupalCreateUser([
      'administer videojs media',
      'access content',
    ]);

    // Create entity of any bundle.
    $entity = VideoJsMedia::create([
      'type' => 'local_video',
      'name' => 'Admin Test',
      'status' => FALSE,
    ]);
    $entity->save();

    $this->drupalLogin($admin);

    // Admin can view unpublished.
    $this->drupalGet("/videojs-media/{$entity->id()}");
    $this->assertSession()->statusCodeEquals(200);

    // Admin can edit any.
    $this->drupalGet("/videojs-media/{$entity->id()}/edit");
    $this->assertSession()->statusCodeEquals(200);

    // Admin can delete any.
    $this->drupalGet("/videojs-media/{$entity->id()}/delete");
    $this->assertSession()->statusCodeEquals(200);

    // Admin can create any bundle.
    $this->drupalGet('/videojs-media/add/youtube');
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Tests permission isolation between bundles.
   */
  public function testPermissionIsolationBetweenBundles(): void {
    // Create user with permission for local_video only.
    $user = $this->drupalCreateUser([
      'create local_video videojs media',
      'view local_video videojs media',
      'access content',
    ]);

    $this->drupalLogin($user);

    // Can access local_video add form.
    $this->drupalGet('/videojs-media/add/local_video');
    $this->assertSession()->statusCodeEquals(200);

    // Cannot access youtube add form.
    $this->drupalGet('/videojs-media/add/youtube');
    $this->assertSession()->statusCodeEquals(403);

    // Create entities of different types.
    $local_video = VideoJsMedia::create([
      'type' => 'local_video',
      'name' => 'Local Video',
      'status' => TRUE,
    ]);
    $local_video->save();

    $youtube = VideoJsMedia::create([
      'type' => 'youtube',
      'name' => 'YouTube Video',
      'status' => TRUE,
    ]);
    $youtube->save();

    // Can view local_video.
    $this->drupalGet("/videojs-media/{$local_video->id()}");
    $this->assertSession()->statusCodeEquals(200);

    // Cannot view youtube.
    $this->drupalGet("/videojs-media/{$youtube->id()}");
    $this->assertSession()->statusCodeEquals(403);
  }

  /**
   * Data provider for bundle types.
   */
  public static function bundleProvider(): array {
    return [
      'local_video' => ['local_video'],
      'local_audio' => ['local_audio'],
      'remote_video' => ['remote_video'],
      'remote_audio' => ['remote_audio'],
      'youtube' => ['youtube'],
    ];
  }

}
