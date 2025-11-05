<?php

namespace Drupal\Tests\admin_notifications\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests the polling endpoint for real-time notifications.
 *
 * @group admin_notifications
 */
class PollingEndpointTest extends BrowserTestBase {

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
   * Tests polling endpoint returns new notifications.
   */
  public function testPollingEndpointReturnsNotifications() {
    // Create user with view permission.
    $user = $this->drupalCreateUser(['view admin notifications']);
    $this->drupalLogin($user);

    // Create a notification.
    $created_time = \Drupal::time()->getRequestTime();
    $this->database->insert('admin_notifications')
      ->fields([
        'title' => 'New Notification',
        'message' => 'You have a new message',
        'severity' => 'info',
        'type' => 'realtime',
        'status' => 'active',
        'created' => $created_time,
        'created_by' => 1,
      ])
      ->execute();

    // Poll with timestamp before the notification.
    $last_check = $created_time - 60;
    $response = $this->drupalGet('admin-notifications/poll', [
      'query' => ['last_check' => $last_check],
    ]);

    // Decode JSON response.
    $data = json_decode($response, TRUE);

    // Verify response structure.
    $this->assertArrayHasKey('notifications', $data);
    $this->assertArrayHasKey('timestamp', $data);
    $this->assertArrayHasKey('count', $data);

    // Verify notification is returned.
    $this->assertEquals(1, $data['count']);
    $this->assertEquals('New Notification', $data['notifications'][0]['title']);
    $this->assertEquals('info', $data['notifications'][0]['severity']);
  }

  /**
   * Tests polling endpoint with no new notifications.
   */
  public function testPollingEndpointNoNewNotifications() {
    $user = $this->drupalCreateUser(['view admin notifications']);
    $this->drupalLogin($user);

    // Create old notification.
    $old_time = \Drupal::time()->getRequestTime() - 3600;
    $this->database->insert('admin_notifications')
      ->fields([
        'title' => 'Old Notification',
        'message' => 'Old message',
        'severity' => 'info',
        'type' => 'realtime',
        'status' => 'active',
        'created' => $old_time,
        'created_by' => 1,
      ])
      ->execute();

    // Poll with current timestamp.
    $last_check = \Drupal::time()->getRequestTime();
    $response = $this->drupalGet('admin-notifications/poll', [
      'query' => ['last_check' => $last_check],
    ]);

    $data = json_decode($response, TRUE);

    // Should return empty notifications.
    $this->assertEquals(0, $data['count']);
    $this->assertEmpty($data['notifications']);
  }

  /**
   * Tests polling endpoint access control.
   */
  public function testPollingEndpointAccessControl() {
    // Anonymous user.
    $this->drupalLogout();
    $this->drupalGet('admin-notifications/poll');
    $this->assertSession()->statusCodeEquals(403);

    // User without permission.
    $user = $this->drupalCreateUser();
    $this->drupalLogin($user);
    $this->drupalGet('admin-notifications/poll');
    $this->assertSession()->statusCodeEquals(403);

    // User with view permission.
    $viewer = $this->drupalCreateUser(['view admin notifications']);
    $this->drupalLogin($viewer);
    $this->drupalGet('admin-notifications/poll');
    $this->assertSession()->statusCodeEquals(200);

    // User with access administration pages.
    $admin = $this->drupalCreateUser(['access administration pages']);
    $this->drupalLogin($admin);
    $this->drupalGet('admin-notifications/poll');
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Tests polling only returns realtime notifications.
   */
  public function testPollingOnlyReturnsRealtime() {
    $user = $this->drupalCreateUser(['view admin notifications']);
    $this->drupalLogin($user);

    $current_time = \Drupal::time()->getRequestTime();

    // Create realtime notification.
    $this->database->insert('admin_notifications')
      ->fields([
        'title' => 'Realtime Notification',
        'message' => 'Realtime',
        'severity' => 'info',
        'type' => 'realtime',
        'status' => 'active',
        'created' => $current_time,
        'created_by' => 1,
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
        'created' => $current_time,
        'created_by' => 1,
      ])
      ->execute();

    // Poll.
    $last_check = $current_time - 60;
    $response = $this->drupalGet('admin-notifications/poll', [
      'query' => ['last_check' => $last_check],
    ]);

    $data = json_decode($response, TRUE);

    // Should only return realtime notification.
    $this->assertEquals(1, $data['count']);
    $this->assertEquals('Realtime Notification', $data['notifications'][0]['title']);
    $this->assertEquals('realtime', $data['notifications'][0]['type']);
  }

  /**
   * Tests polling respects notification status.
   */
  public function testPollingRespectsStatus() {
    $user = $this->drupalCreateUser(['view admin notifications']);
    $this->drupalLogin($user);

    $current_time = \Drupal::time()->getRequestTime();

    // Create active notification.
    $this->database->insert('admin_notifications')
      ->fields([
        'title' => 'Active Notification',
        'message' => 'Active',
        'severity' => 'info',
        'type' => 'realtime',
        'status' => 'active',
        'created' => $current_time,
        'created_by' => 1,
      ])
      ->execute();

    // Create inactive notification.
    $this->database->insert('admin_notifications')
      ->fields([
        'title' => 'Inactive Notification',
        'message' => 'Inactive',
        'severity' => 'info',
        'type' => 'realtime',
        'status' => 'inactive',
        'created' => $current_time,
        'created_by' => 1,
      ])
      ->execute();

    // Poll.
    $last_check = $current_time - 60;
    $response = $this->drupalGet('admin-notifications/poll', [
      'query' => ['last_check' => $last_check],
    ]);

    $data = json_decode($response, TRUE);

    // Should only return active notification.
    $this->assertEquals(1, $data['count']);
    $this->assertEquals('Active Notification', $data['notifications'][0]['title']);
  }

  /**
   * Tests marking notification as read via endpoint.
   */
  public function testMarkAsReadEndpoint() {
    $user = $this->drupalCreateUser(['view admin notifications']);
    $this->drupalLogin($user);

    // Create notification.
    $id = $this->database->insert('admin_notifications')
      ->fields([
        'title' => 'Test Notification',
        'message' => 'Test',
        'severity' => 'info',
        'type' => 'realtime',
        'status' => 'active',
        'created' => \Drupal::time()->getRequestTime(),
        'created_by' => 1,
      ])
      ->execute();

    // Mark as read.
    $response = $this->drupalPost("admin-notifications/{$id}/mark-read", '', []);

    $data = json_decode($response, TRUE);

    // Verify success.
    $this->assertTrue($data['success']);

    // Verify in database.
    $read_count = $this->database->select('admin_notifications_read', 'anr')
      ->condition('notification_id', $id)
      ->condition('uid', $user->id())
      ->countQuery()
      ->execute()
      ->fetchField();

    $this->assertEquals(1, $read_count);
  }

}
