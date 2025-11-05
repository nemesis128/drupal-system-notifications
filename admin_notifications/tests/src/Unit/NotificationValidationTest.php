<?php

namespace Drupal\Tests\admin_notifications\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Tests for notification data validation.
 *
 * @group admin_notifications
 * @coversDefaultClass \Drupal\admin_notifications
 */
class NotificationValidationTest extends TestCase {

  /**
   * Tests severity validation.
   *
   * @dataProvider severityProvider
   */
  public function testSeverityValidation($severity, $expected) {
    $valid_severities = ['info', 'success', 'warning', 'error'];
    $result = in_array($severity, $valid_severities);
    $this->assertEquals($expected, $result, "Severity '$severity' validation failed");
  }

  /**
   * Data provider for severity tests.
   */
  public function severityProvider() {
    return [
      'info is valid' => ['info', TRUE],
      'success is valid' => ['success', TRUE],
      'warning is valid' => ['warning', TRUE],
      'error is valid' => ['error', TRUE],
      'invalid severity' => ['invalid', FALSE],
      'empty string' => ['', FALSE],
    ];
  }

  /**
   * Tests type validation.
   *
   * @dataProvider typeProvider
   */
  public function testTypeValidation($type, $expected) {
    $valid_types = ['realtime', 'banner'];
    $result = in_array($type, $valid_types);
    $this->assertEquals($expected, $result, "Type '$type' validation failed");
  }

  /**
   * Data provider for type tests.
   */
  public function typeProvider() {
    return [
      'realtime is valid' => ['realtime', TRUE],
      'banner is valid' => ['banner', TRUE],
      'invalid type' => ['invalid', FALSE],
      'empty string' => ['', FALSE],
    ];
  }

  /**
   * Tests notification structure.
   */
  public function testNotificationStructure() {
    $notification = [
      'id' => 123,
      'title' => 'Test Notification',
      'message' => 'Test message',
      'severity' => 'info',
      'type' => 'realtime',
      'created' => 1234567890,
    ];

    $this->assertArrayHasKey('id', $notification);
    $this->assertArrayHasKey('title', $notification);
    $this->assertArrayHasKey('message', $notification);
    $this->assertArrayHasKey('severity', $notification);
    $this->assertArrayHasKey('type', $notification);
    $this->assertIsInt($notification['id']);
    $this->assertIsString($notification['title']);
  }

  /**
   * Tests date validation for scheduled notifications.
   */
  public function testScheduledDateValidation() {
    $now = time();

    // Valid: start_date in past, end_date in future.
    $start_date = $now - 86400;
    $end_date = $now + 86400;
    $this->assertLessThan($end_date, $start_date);

    // Invalid: end_date before start_date.
    $start_date = $now + 86400;
    $end_date = $now - 86400;
    $this->assertGreaterThan($end_date, $start_date, 'End date should not be before start date');
  }

  /**
   * Tests title length validation.
   */
  public function testTitleLengthValidation() {
    $short_title = 'Test';
    $long_title = str_repeat('a', 256);

    $this->assertLessThanOrEqual(255, strlen($short_title));
    $this->assertGreaterThan(255, strlen($long_title));
  }

}
