<?php

namespace Drupal\Tests\admin_notifications\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests notification creation through the UI.
 *
 * @group admin_notifications
 */
class NotificationCreationTest extends BrowserTestBase {

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
   * Tests creating a realtime notification through the form.
   */
  public function testCreateRealtimeNotification() {
    // Create admin user with permission.
    $admin = $this->drupalCreateUser(['administer admin notifications']);
    $this->drupalLogin($admin);

    // Navigate to the notification creation form.
    $this->drupalGet('admin/reports/admin-notifications/add');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Add Notification');

    // Fill out the form.
    $edit = [
      'title' => 'Test Realtime Notification',
      'message' => 'This is a test notification message.',
      'severity' => 'info',
      'type' => 'realtime',
      'status' => 'active',
    ];
    $this->submitForm($edit, 'Save');

    // Verify success message.
    $this->assertSession()->pageTextContains('Notification created successfully');

    // Verify in database.
    $notification = $this->database->select('admin_notifications', 'an')
      ->fields('an')
      ->condition('title', 'Test Realtime Notification')
      ->execute()
      ->fetchObject();

    $this->assertNotNull($notification);
    $this->assertEquals('info', $notification->severity);
    $this->assertEquals('realtime', $notification->type);
    $this->assertEquals('active', $notification->status);
  }

  /**
   * Tests creating a banner notification with dates.
   */
  public function testCreateBannerNotification() {
    $admin = $this->drupalCreateUser(['administer admin notifications']);
    $this->drupalLogin($admin);

    $this->drupalGet('admin/reports/admin-notifications/add');

    // Fill out the form with banner type.
    $start_date = date('Y-m-d H:i:s', strtotime('+1 hour'));
    $end_date = date('Y-m-d H:i:s', strtotime('+1 day'));

    $edit = [
      'title' => 'Test Banner Notification',
      'message' => 'This banner will be displayed later.',
      'severity' => 'warning',
      'type' => 'banner',
      'status' => 'active',
      'start_date' => $start_date,
      'end_date' => $end_date,
    ];
    $this->submitForm($edit, 'Save');

    // Verify success.
    $this->assertSession()->pageTextContains('Notification created successfully');

    // Verify in database.
    $notification = $this->database->select('admin_notifications', 'an')
      ->fields('an')
      ->condition('title', 'Test Banner Notification')
      ->execute()
      ->fetchObject();

    $this->assertNotNull($notification);
    $this->assertEquals('warning', $notification->severity);
    $this->assertEquals('banner', $notification->type);
    $this->assertNotNull($notification->start_date);
    $this->assertNotNull($notification->end_date);
  }

  /**
   * Tests validation on the notification form.
   */
  public function testFormValidation() {
    $admin = $this->drupalCreateUser(['administer admin notifications']);
    $this->drupalLogin($admin);

    $this->drupalGet('admin/reports/admin-notifications/add');

    // Submit form without required fields.
    $this->submitForm([], 'Save');

    // Check for validation errors.
    $this->assertSession()->pageTextContains('Title field is required');
    $this->assertSession()->pageTextContains('Message field is required');
  }

  /**
   * Tests access control for notification creation.
   */
  public function testAccessControl() {
    // User without permission.
    $user = $this->drupalCreateUser();
    $this->drupalLogin($user);

    // Try to access the form.
    $this->drupalGet('admin/reports/admin-notifications/add');
    $this->assertSession()->statusCodeEquals(403);

    // User with permission.
    $admin = $this->drupalCreateUser(['administer admin notifications']);
    $this->drupalLogin($admin);

    $this->drupalGet('admin/reports/admin-notifications/add');
    $this->assertSession()->statusCodeEquals(200);
  }

}
