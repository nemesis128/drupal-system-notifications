<?php

namespace Drupal\admin_notifications\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controlador para el sistema de polling de notificaciones en tiempo real.
 */
class AdminNotificationPollController extends ControllerBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a new AdminNotificationPollController.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * Endpoint de polling para verificar nuevas notificaciones.
   */
  public function poll(Request $request) {
    $user = $this->currentUser();

    // Verificar permisos
    if (!$user->hasPermission('view admin notifications') &&
        !$user->hasPermission('access administration pages')) {
      \Drupal::logger('admin_notifications')->warning('Polling access denied for user @uid', [
        '@uid' => $user->id(),
      ]);
      return new JsonResponse(['notifications' => []], 403);
    }

    try {
      $last_check = $request->query->get('last_check', 0);
      $current_time = \Drupal::time()->getRequestTime();

      // Buscar notificaciones en tiempo real activas creadas después del último check
      $query = $this->database->select('admin_notifications', 'an')
        ->fields('an')
        ->condition('an.type', 'realtime')
        ->condition('an.status', 'active')
        ->condition('an.created', $last_check, '>')
        ->orderBy('an.created', 'ASC');

      $notifications = $query->execute()->fetchAll();

      // Filtrar las que el usuario no ha leído
      $unread_notifications = [];
      foreach ($notifications as $notification) {
        $read = $this->database->select('admin_notifications_read', 'anr')
          ->condition('notification_id', $notification->id)
          ->condition('uid', $user->id())
          ->countQuery()
          ->execute()
          ->fetchField();

        if (!$read) {
          $unread_notifications[] = [
            'id' => $notification->id,
            'title' => $notification->title,
            'message' => $notification->message,
            'severity' => $notification->severity,
            'created' => $notification->created,
          ];

          // Marcar automáticamente como leída después de enviarla
          $this->database->insert('admin_notifications_read')
            ->fields([
              'notification_id' => $notification->id,
              'uid' => $user->id(),
              'read_timestamp' => $current_time,
            ])
            ->execute();
        }
      }

      return new JsonResponse([
        'notifications' => $unread_notifications,
        'timestamp' => $current_time,
        'count' => count($unread_notifications),
      ]);
    }
    catch (\Exception $e) {
      \Drupal::logger('admin_notifications')->error('Error in polling endpoint: @message', [
        '@message' => $e->getMessage(),
      ]);
      return new JsonResponse([
        'notifications' => [],
        'timestamp' => \Drupal::time()->getRequestTime(),
        'count' => 0,
        'error' => 'An error occurred while fetching notifications',
      ], 500);
    }
  }

  /**
   * Marca una notificación como leída.
   */
  public function markRead(Request $request, $notification_id) {
    $user = $this->currentUser();

    // Verificar permisos
    if (!$user->hasPermission('view admin notifications') &&
        !$user->hasPermission('access administration pages')) {
      \Drupal::logger('admin_notifications')->warning('Mark read access denied for user @uid', [
        '@uid' => $user->id(),
      ]);
      return new JsonResponse(['success' => FALSE, 'error' => 'Access denied'], 403);
    }

    try {
      // Verificar que la notificación existe
      $notification = $this->database->select('admin_notifications', 'an')
        ->fields('an', ['id'])
        ->condition('id', $notification_id)
        ->execute()
        ->fetchField();

      if (!$notification) {
        \Drupal::logger('admin_notifications')->warning('Attempted to mark non-existent notification @id as read', [
          '@id' => $notification_id,
        ]);
        return new JsonResponse(['success' => FALSE, 'error' => 'Notification not found'], 404);
      }

      // Verificar si ya está marcada como leída
      $already_read = $this->database->select('admin_notifications_read', 'anr')
        ->condition('notification_id', $notification_id)
        ->condition('uid', $user->id())
        ->countQuery()
        ->execute()
        ->fetchField();

      if (!$already_read) {
        $this->database->insert('admin_notifications_read')
          ->fields([
            'notification_id' => $notification_id,
            'uid' => $user->id(),
            'read_timestamp' => \Drupal::time()->getRequestTime(),
          ])
          ->execute();
      }

      return new JsonResponse(['success' => TRUE]);
    }
    catch (\Exception $e) {
      \Drupal::logger('admin_notifications')->error('Error marking notification @id as read: @message', [
        '@id' => $notification_id,
        '@message' => $e->getMessage(),
      ]);
      return new JsonResponse([
        'success' => FALSE,
        'error' => 'An error occurred while marking notification as read',
      ], 500);
    }
  }

}
