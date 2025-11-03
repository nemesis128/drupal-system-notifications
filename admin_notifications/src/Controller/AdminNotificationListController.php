<?php

namespace Drupal\admin_notifications\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controlador para listar notificaciones administrativas.
 */
class AdminNotificationListController extends ControllerBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a new AdminNotificationListController.
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
   * Lista todas las notificaciones.
   */
  public function list() {
    $build = [];

    // Botón para crear nueva notificación
    $build['create'] = [
      '#type' => 'link',
      '#title' => $this->t('Crear Nueva Notificación'),
      '#url' => Url::fromRoute('admin_notifications.add'),
      '#attributes' => [
        'class' => ['button', 'button--primary', 'button--small'],
      ],
      '#prefix' => '<div class="admin-notifications-actions">',
      '#suffix' => '</div>',
    ];

    // Obtener todas las notificaciones
    $query = $this->database->select('admin_notifications', 'an')
      ->fields('an')
      ->orderBy('created', 'DESC');

    $notifications = $query->execute()->fetchAll();

    if (empty($notifications)) {
      $build['empty'] = [
        '#markup' => '<p>' . $this->t('No hay notificaciones creadas. <a href="@link">Crear la primera notificación</a>.', [
          '@link' => Url::fromRoute('admin_notifications.add')->toString(),
        ]) . '</p>',
      ];
      return $build;
    }

    // Crear tabla
    $header = [
      $this->t('ID'),
      $this->t('Título'),
      $this->t('Tipo'),
      $this->t('Severidad'),
      $this->t('Estado'),
      $this->t('Fecha de inicio'),
      $this->t('Fecha de expiración'),
      $this->t('Creado por'),
      $this->t('Operaciones'),
    ];

    $rows = [];
    foreach ($notifications as $notification) {
      $type_labels = [
        'realtime' => $this->t('Tiempo Real'),
        'banner' => $this->t('Banner'),
      ];

      $severity_labels = [
        'info' => $this->t('Info'),
        'success' => $this->t('Éxito'),
        'warning' => $this->t('Advertencia'),
        'error' => $this->t('Error'),
      ];

      $status_labels = [
        'draft' => $this->t('Borrador'),
        'active' => $this->t('Activa'),
        'completed' => $this->t('Completada'),
      ];

      $user = \Drupal\user\Entity\User::load($notification->created_by);
      $username = $user ? $user->getDisplayName() : $this->t('Desconocido');

      $operations = [
        'edit' => [
          '#type' => 'link',
          '#title' => $this->t('Editar'),
          '#url' => Url::fromRoute('admin_notifications.edit', ['notification_id' => $notification->id]),
          '#attributes' => ['class' => ['button', 'button--small']],
        ],
        'delete' => [
          '#type' => 'link',
          '#title' => $this->t('Eliminar'),
          '#url' => Url::fromRoute('admin_notifications.delete', ['notification_id' => $notification->id]),
          '#attributes' => ['class' => ['button', 'button--small', 'button--danger']],
        ],
      ];

      // Formatear fecha de expiración
      $end_date_formatted = $notification->end_date
        ? \Drupal::service('date.formatter')->format($notification->end_date, 'short')
        : $this->t('Sin límite');

      $rows[] = [
        $notification->id,
        $notification->title,
        $type_labels[$notification->type] ?? $notification->type,
        $severity_labels[$notification->severity] ?? $notification->severity,
        $status_labels[$notification->status] ?? $notification->status,
        \Drupal::service('date.formatter')->format($notification->start_date, 'short'),
        $end_date_formatted,
        $username,
        ['data' => $operations],
      ];
    }

    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No hay notificaciones disponibles.'),
      '#attributes' => ['class' => ['admin-notifications-list']],
    ];

    $build['#attached']['library'][] = 'admin_notifications/notifications';

    return $build;
  }

}
