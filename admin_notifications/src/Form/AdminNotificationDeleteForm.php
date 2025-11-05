<?php

namespace Drupal\admin_notifications\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Formulario de confirmación para eliminar notificaciones.
 */
class AdminNotificationDeleteForm extends ConfirmFormBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The notification ID.
   *
   * @var int
   */
  protected $notificationId;

  /**
   * The notification object.
   *
   * @var object
   */
  protected $notification;

  /**
   * Constructs a new AdminNotificationDeleteForm.
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
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'admin_notification_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $notification_id = NULL) {
    $this->notificationId = $notification_id;

    // Cargar la notificación.
    $this->notification = $this->database->select('admin_notifications', 'an')
      ->fields('an')
      ->condition('id', $notification_id)
      ->execute()
      ->fetchObject();

    if (!$this->notification) {
      $this->messenger()->addError($this->t('La notificación no existe.'));
      return [];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('¿Está seguro de que desea eliminar la notificación "@title"?', [
      '@title' => $this->notification->title,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('admin_notifications.list');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('Esta acción no se puede deshacer.');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Eliminar');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Eliminar registros de lectura.
    $this->database->delete('admin_notifications_read')
      ->condition('notification_id', $this->notificationId)
      ->execute();

    // Eliminar la notificación.
    $this->database->delete('admin_notifications')
      ->condition('id', $this->notificationId)
      ->execute();

    $this->messenger()->addStatus($this->t('La notificación "@title" ha sido eliminada.', [
      '@title' => $this->notification->title,
    ]));

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
