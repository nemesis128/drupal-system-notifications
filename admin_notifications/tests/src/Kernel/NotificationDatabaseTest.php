<?php

namespace Drupal\Tests\admin_notifications\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests database operations for admin notifications.
 *
 * @group admin_notifications
 */
class NotificationDatabaseTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['admin_notifications', 'user', 'system'];

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installSchema('admin_notifications', [
      'admin_notifications',
      'admin_notifications_read',
    ]);
    $this->installEntitySchema('user');
    $this->installSchema('system', ['sequences']);

    $this->database = $this->container->get('database');
  }

  /**
   * Tests creating a notification in the database.
   */
  public function testCreateNotification() {
    $notification_data = [
      'title' => 'Test Notification',
      'message' => 'This is a test notification.',
      'severity' => 'info',
      'type' => 'realtime',
      'status' => 'active',
      'created' => \Drupal::time()->getRequestTime(),
      'created_by' => 1,
    ];

    $id = $this->database->insert('admin_notifications')
      ->fields($notification_data)
      ->execute();

    $this->assertGreaterThan(0, $id, 'Notification created with valid ID');

    // Verify in database.
    $notification = $this->database->select('admin_notifications', 'an')
      ->fields('an')
      ->condition('id', $id)
      ->execute()
      ->fetchObject();

    $this->assertNotNull($notification);
    $this->assertEquals('Test Notification', $notification->title);
    $this->assertEquals('info', $notification->severity);
  }

  /**
   * Tests retrieving active notifications.
   */
  public function testGetActiveNotifications() {
    // Create test notifications.
    $this->database->insert('admin_notifications')
      ->fields([
        'title' => 'Active 1',
        'message' => 'Active notification 1',
        'severity' => 'info',
        'type' => 'realtime',
        'status' => 'active',
        'created' => \Drupal::time()->getRequestTime(),
        'created_by' => 1,
      ])
      ->execute();

    $this->database->insert('admin_notifications')
      ->fields([
        'title' => 'Active 2',
        'message' => 'Active notification 2',
        'severity' => 'success',
        'type' => 'realtime',
        'status' => 'active',
        'created' => \Drupal::time()->getRequestTime(),
        'created_by' => 1,
      ])
      ->execute();

    $notifications = $this->database->select('admin_notifications', 'an')
      ->fields('an')
      ->condition('type', 'realtime')
      ->condition('status', 'active')
      ->execute()
      ->fetchAll();

    $this->assertCount(2, $notifications);
  }

  /**
   * Tests marking notification as read.
   */
  public function testMarkNotificationAsRead() {
    // Create notification.
    $id = $this->database->insert('admin_notifications')
      ->fields([
        'title' => 'Test',
        'message' => 'Test message',
        'severity' => 'info',
        'type' => 'realtime',
        'status' => 'active',
        'created' => \Drupal::time()->getRequestTime(),
        'created_by' => 1,
      ])
      ->execute();

    $user_id = 1;

    // Mark as read.
    $this->database->insert('admin_notifications_read')
      ->fields([
        'notification_id' => $id,
        'uid' => $user_id,
        'read_timestamp' => \Drupal::time()->getRequestTime(),
      ])
      ->execute();

    // Verify in database.
    $read = $this->database->select('admin_notifications_read', 'anr')
      ->condition('notification_id', $id)
      ->condition('uid', $user_id)
      ->countQuery()
      ->execute()
      ->fetchField();

    $this->assertEquals(1, $read);
  }

  /**
   * Tests deleting a notification.
   */
  public function testDeleteNotification() {
    $id = $this->database->insert('admin_notifications')
      ->fields([
        'title' => 'To Delete',
        'message' => 'This will be deleted',
        'severity' => 'info',
        'type' => 'realtime',
        'status' => 'active',
        'created' => \Drupal::time()->getRequestTime(),
        'created_by' => 1,
      ])
      ->execute();

    // Delete notification.
    $this->database->delete('admin_notifications')
      ->condition('id', $id)
      ->execute();

    // Verify deletion.
    $notification = $this->database->select('admin_notifications', 'an')
      ->condition('id', $id)
      ->countQuery()
      ->execute()
      ->fetchField();

    $this->assertEquals(0, $notification);
  }

  /**
   * Tests filtering by date range for banners.
   */
  public function testBannerDateFiltering() {
    $now = \Drupal::time()->getRequestTime();
    $past = $now - 86400;
    $future = $now + 86400;

    // Create banner that should be visible.
    $this->database->insert('admin_notifications')
      ->fields([
        'title' => 'Visible Banner',
        'message' => 'This should be visible',
        'severity' => 'info',
        'type' => 'banner',
        'status' => 'active',
        'start_date' => $past,
        'end_date' => $future,
        'created' => $now,
        'created_by' => 1,
      ])
      ->execute();

    // Create banner that should not be visible (expired).
    $this->database->insert('admin_notifications')
      ->fields([
        'title' => 'Expired Banner',
        'message' => 'This should not be visible',
        'severity' => 'info',
        'type' => 'banner',
        'status' => 'active',
        'start_date' => $past - 172800,
        'end_date' => $past,
        'created' => $now,
        'created_by' => 1,
      ])
      ->execute();

    // Query for visible banners.
    $visible = $this->database->select('admin_notifications', 'an')
      ->fields('an')
      ->condition('type', 'banner')
      ->condition('status', 'active')
      ->condition('start_date', $now, '<=')
      ->condition('end_date', $now, '>=')
      ->execute()
      ->fetchAll();

    $this->assertCount(1, $visible);
    $this->assertEquals('Visible Banner', $visible[0]->title);
  }

}
