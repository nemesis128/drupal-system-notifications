<?php

namespace Drupal\Tests\admin_notifications\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests notification list page.
 *
 * @group admin_notifications
 */
class NotificationListTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

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

    $this->database = $this->container->get('database');

    // Install the admin_notifications schema.
    $this->installSchema('admin_notifications', [
      'admin_notifications',
      'admin_notifications_read',
    ]);
  }

  /**
   * Tests the notification list displays correctly.
   */
  public function testNotificationList() {
    // Create admin user.
    $admin = $this->drupalCreateUser(['administer admin notifications']);
    $this->drupalLogin($admin);

    // Create test notifications.
    $this->database->insert('admin_notifications')
      ->fields([
        'title' => 'First Notification',
        'message' => 'First message',
        'severity' => 'info',
        'type' => 'realtime',
        'status' => 'active',
        'created' => \Drupal::time()->getRequestTime(),
        'created_by' => $admin->id(),
      ])
      ->execute();

    $this->database->insert('admin_notifications')
      ->fields([
        'title' => 'Second Notification',
        'message' => 'Second message',
        'severity' => 'success',
        'type' => 'banner',
        'status' => 'active',
        'created' => \Drupal::time()->getRequestTime(),
        'created_by' => $admin->id(),
      ])
      ->execute();

    // Visit the list page.
    $this->drupalGet('admin/reports/admin-notifications');
    $this->assertSession()->statusCodeEquals(200);

    // Verify both notifications appear.
    $this->assertSession()->pageTextContains('First Notification');
    $this->assertSession()->pageTextContains('Second Notification');
    $this->assertSession()->pageTextContains('info');
    $this->assertSession()->pageTextContains('success');
  }

  /**
   * Tests deleting a notification from the list.
   */
  public function testDeleteNotification() {
    $admin = $this->drupalCreateUser(['administer admin notifications']);
    $this->drupalLogin($admin);

    // Create a notification.
    $id = $this->database->insert('admin_notifications')
      ->fields([
        'title' => 'To Delete',
        'message' => 'This will be deleted',
        'severity' => 'warning',
        'type' => 'realtime',
        'status' => 'active',
        'created' => \Drupal::time()->getRequestTime(),
        'created_by' => $admin->id(),
      ])
      ->execute();

    // Visit list page.
    $this->drupalGet('admin/reports/admin-notifications');
    $this->assertSession()->pageTextContains('To Delete');

    // Click delete button.
    $this->clickLink('Delete');
    $this->assertSession()->pageTextContains('Are you sure you want to delete this notification?');

    // Confirm deletion.
    $this->submitForm([], 'Delete');
    $this->assertSession()->pageTextContains('Notification deleted successfully');

    // Verify it's gone from database.
    $count = $this->database->select('admin_notifications', 'an')
      ->condition('id', $id)
      ->countQuery()
      ->execute()
      ->fetchField();

    $this->assertEquals(0, $count);
  }

  /**
   * Tests editing a notification.
   */
  public function testEditNotification() {
    $admin = $this->drupalCreateUser(['administer admin notifications']);
    $this->drupalLogin($admin);

    // Create a notification.
    $id = $this->database->insert('admin_notifications')
      ->fields([
        'title' => 'Original Title',
        'message' => 'Original message',
        'severity' => 'info',
        'type' => 'realtime',
        'status' => 'active',
        'created' => \Drupal::time()->getRequestTime(),
        'created_by' => $admin->id(),
      ])
      ->execute();

    // Visit list and click edit.
    $this->drupalGet('admin/reports/admin-notifications');
    $this->clickLink('Edit');

    // Update the notification.
    $edit = [
      'title' => 'Updated Title',
      'severity' => 'success',
    ];
    $this->submitForm($edit, 'Save');

    // Verify update.
    $this->assertSession()->pageTextContains('Notification updated successfully');

    $notification = $this->database->select('admin_notifications', 'an')
      ->fields('an')
      ->condition('id', $id)
      ->execute()
      ->fetchObject();

    $this->assertEquals('Updated Title', $notification->title);
    $this->assertEquals('success', $notification->severity);
  }

  /**
   * Tests filtering notifications by type.
   */
  public function testFilterByType() {
    $admin = $this->drupalCreateUser(['administer admin notifications']);
    $this->drupalLogin($admin);

    // Create realtime notification.
    $this->database->insert('admin_notifications')
      ->fields([
        'title' => 'Realtime Notification',
        'message' => 'Realtime',
        'severity' => 'info',
        'type' => 'realtime',
        'status' => 'active',
        'created' => \Drupal::time()->getRequestTime(),
        'created_by' => $admin->id(),
      ])
      ->execute();

    // Create banner notification.
    $this->database->insert('admin_notifications')
      ->fields([
        'title' => 'Banner Notification',
        'message' => 'Banner',
        'severity' => 'info',
        'type' => 'banner',
        'status' => 'active',
        'created' => \Drupal::time()->getRequestTime(),
        'created_by' => $admin->id(),
      ])
      ->execute();

    // Visit list page.
    $this->drupalGet('admin/reports/admin-notifications');

    // Both should be visible.
    $this->assertSession()->pageTextContains('Realtime Notification');
    $this->assertSession()->pageTextContains('Banner Notification');

    // Filter by realtime.
    $this->drupalGet('admin/reports/admin-notifications', ['query' => ['type' => 'realtime']]);
    $this->assertSession()->pageTextContains('Realtime Notification');
    $this->assertSession()->pageTextNotContains('Banner Notification');

    // Filter by banner.
    $this->drupalGet('admin/reports/admin-notifications', ['query' => ['type' => 'banner']]);
    $this->assertSession()->pageTextNotContains('Realtime Notification');
    $this->assertSession()->pageTextContains('Banner Notification');
  }

  /**
   * Tests access control for notification list.
   */
  public function testListAccessControl() {
    // User without permission.
    $user = $this->drupalCreateUser();
    $this->drupalLogin($user);

    $this->drupalGet('admin/reports/admin-notifications');
    $this->assertSession()->statusCodeEquals(403);

    // User with view permission.
    $viewer = $this->drupalCreateUser(['view admin notifications']);
    $this->drupalLogin($viewer);

    $this->drupalGet('admin/reports/admin-notifications');
    $this->assertSession()->statusCodeEquals(200);
  }

}
