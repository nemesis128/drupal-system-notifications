<?php

namespace Drupal\Tests\admin_notifications\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests the module configuration form.
 *
 * @group admin_notifications
 */
class ConfigurationFormTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['admin_notifications', 'user', 'system'];

  /**
   * Tests accessing the configuration form.
   */
  public function testConfigurationFormAccess() {
    // User without permission.
    $user = $this->drupalCreateUser();
    $this->drupalLogin($user);

    $this->drupalGet('admin/config/system/admin-notifications');
    $this->assertSession()->statusCodeEquals(403);

    // User with permission.
    $admin = $this->drupalCreateUser(['administer admin notifications']);
    $this->drupalLogin($admin);

    $this->drupalGet('admin/config/system/admin-notifications');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Admin Notifications Settings');
  }

  /**
   * Tests updating configuration values.
   */
  public function testUpdateConfiguration() {
    $admin = $this->drupalCreateUser(['administer admin notifications']);
    $this->drupalLogin($admin);

    $this->drupalGet('admin/config/system/admin-notifications');

    // Update configuration.
    $edit = [
      'poll_interval' => 60000,
      'toast_duration' => 15000,
      'toast_position' => 'top-right',
      'sound_enabled' => FALSE,
    ];
    $this->submitForm($edit, 'Save configuration');

    // Verify success message.
    $this->assertSession()->pageTextContains('The configuration options have been saved');

    // Verify values in config.
    $config = \Drupal::config('admin_notifications.settings');
    $this->assertEquals(60000, $config->get('poll_interval'));
    $this->assertEquals(15000, $config->get('toast_duration'));
    $this->assertEquals('top-right', $config->get('toast_position'));
    $this->assertFalse($config->get('sound_enabled'));
  }

  /**
   * Tests default configuration values.
   */
  public function testDefaultConfigurationValues() {
    $admin = $this->drupalCreateUser(['administer admin notifications']);
    $this->drupalLogin($admin);

    $this->drupalGet('admin/config/system/admin-notifications');

    // Check default values are displayed.
    $this->assertSession()->fieldValueEquals('poll_interval', 30000);
    $this->assertSession()->fieldValueEquals('toast_duration', 10000);
    $this->assertSession()->fieldValueEquals('toast_position', 'bottom-right');
    $this->assertSession()->checkboxChecked('sound_enabled');
  }

  /**
   * Tests poll interval validation.
   */
  public function testPollIntervalValidation() {
    $admin = $this->drupalCreateUser(['administer admin notifications']);
    $this->drupalLogin($admin);

    $this->drupalGet('admin/config/system/admin-notifications');

    // Try to set invalid poll interval (too low).
    $edit = [
      'poll_interval' => 5000,
    ];
    $this->submitForm($edit, 'Save configuration');

    // Should show error.
    $this->assertSession()->pageTextContains('Poll interval must be at least 10000 milliseconds');

    // Try valid value.
    $edit = [
      'poll_interval' => 15000,
    ];
    $this->submitForm($edit, 'Save configuration');

    // Should succeed.
    $this->assertSession()->pageTextContains('The configuration options have been saved');
  }

  /**
   * Tests toast duration validation.
   */
  public function testToastDurationValidation() {
    $admin = $this->drupalCreateUser(['administer admin notifications']);
    $this->drupalLogin($admin);

    $this->drupalGet('admin/config/system/admin-notifications');

    // Try negative duration.
    $edit = [
      'toast_duration' => -1000,
    ];
    $this->submitForm($edit, 'Save configuration');

    // Should show error.
    $this->assertSession()->pageTextContains('Toast duration must be a positive number');

    // Try valid value.
    $edit = [
      'toast_duration' => 20000,
    ];
    $this->submitForm($edit, 'Save configuration');

    // Should succeed.
    $this->assertSession()->pageTextContains('The configuration options have been saved');
  }

  /**
   * Tests toast position options.
   */
  public function testToastPositionOptions() {
    $admin = $this->drupalCreateUser(['administer admin notifications']);
    $this->drupalLogin($admin);

    $this->drupalGet('admin/config/system/admin-notifications');

    // Verify all position options are available.
    $this->assertSession()->optionExists('toast_position', 'top-left');
    $this->assertSession()->optionExists('toast_position', 'top-right');
    $this->assertSession()->optionExists('toast_position', 'bottom-left');
    $this->assertSession()->optionExists('toast_position', 'bottom-right');

    // Test each position.
    $positions = ['top-left', 'top-right', 'bottom-left', 'bottom-right'];
    foreach ($positions as $position) {
      $edit = [
        'toast_position' => $position,
      ];
      $this->submitForm($edit, 'Save configuration');
      $this->assertSession()->pageTextContains('The configuration options have been saved');

      $config = \Drupal::config('admin_notifications.settings');
      $this->assertEquals($position, $config->get('toast_position'));
    }
  }

  /**
   * Tests sound enable/disable functionality.
   */
  public function testSoundConfiguration() {
    $admin = $this->drupalCreateUser(['administer admin notifications']);
    $this->drupalLogin($admin);

    $this->drupalGet('admin/config/system/admin-notifications');

    // Disable sound.
    $edit = [
      'sound_enabled' => FALSE,
    ];
    $this->submitForm($edit, 'Save configuration');

    $config = \Drupal::config('admin_notifications.settings');
    $this->assertFalse($config->get('sound_enabled'));

    // Enable sound.
    $this->drupalGet('admin/config/system/admin-notifications');
    $edit = [
      'sound_enabled' => TRUE,
    ];
    $this->submitForm($edit, 'Save configuration');

    $config = \Drupal::config('admin_notifications.settings');
    $this->assertTrue($config->get('sound_enabled'));
  }

  /**
   * Tests that configuration changes affect JavaScript settings.
   */
  public function testConfigurationAffectsJavaScript() {
    $admin = $this->drupalCreateUser(['administer admin notifications', 'view admin notifications']);
    $this->drupalLogin($admin);

    // Update configuration.
    $this->drupalGet('admin/config/system/admin-notifications');
    $edit = [
      'poll_interval' => 45000,
      'toast_duration' => 12000,
    ];
    $this->submitForm($edit, 'Save configuration');

    // Visit a page where JavaScript settings are loaded.
    $this->drupalGet('<front>');

    // Check if settings are in drupalSettings.
    $settings = $this->getDrupalSettings();
    $this->assertArrayHasKey('adminNotifications', $settings);
    $this->assertEquals(45000, $settings['adminNotifications']['pollInterval']);
    $this->assertEquals(12000, $settings['adminNotifications']['toastDuration']);
  }

}
