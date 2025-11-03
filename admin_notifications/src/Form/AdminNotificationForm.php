<?php

namespace Drupal\admin_notifications\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Formulario para crear/editar notificaciones administrativas.
 */
class AdminNotificationForm extends FormBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a new AdminNotificationForm.
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
    return 'admin_notification_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $notification_id = NULL) {
    $notification = NULL;

    // Si estamos editando, cargar la notificación
    if ($notification_id) {
      $notification = $this->database->select('admin_notifications', 'an')
        ->fields('an')
        ->condition('id', $notification_id)
        ->execute()
        ->fetchObject();

      if (!$notification) {
        $this->messenger()->addError($this->t('La notificación no existe.'));
        return $form;
      }

      $form_state->set('notification_id', $notification_id);
    }

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Título'),
      '#required' => TRUE,
      '#maxlength' => 255,
      '#default_value' => $notification ? $notification->title : '',
      '#description' => $this->t('Título de la notificación.'),
    ];

    $form['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Mensaje'),
      '#required' => TRUE,
      '#rows' => 5,
      '#default_value' => $notification ? $notification->message : '',
      '#description' => $this->t('Contenido del mensaje de la notificación.'),
    ];

    $form['type'] = [
      '#type' => 'radios',
      '#title' => $this->t('Tipo de notificación'),
      '#required' => TRUE,
      '#options' => [
        'realtime' => $this->t('Notificación en tiempo real (Toast)'),
        'banner' => $this->t('Notificación Banner (Programada)'),
      ],
      '#default_value' => $notification ? $notification->type : 'realtime',
      '#description' => $this->t('Las notificaciones en tiempo real aparecen inmediatamente como popup. Las banner se muestran en la parte superior según la programación.'),
    ];

    $form['severity'] = [
      '#type' => 'select',
      '#title' => $this->t('Severidad'),
      '#required' => TRUE,
      '#options' => [
        'info' => $this->t('Información'),
        'success' => $this->t('Éxito'),
        'warning' => $this->t('Advertencia'),
        'error' => $this->t('Error'),
      ],
      '#default_value' => $notification ? $notification->severity : 'info',
      '#description' => $this->t('Nivel de severidad de la notificación.'),
    ];

    $form['scheduling'] = [
      '#type' => 'details',
      '#title' => $this->t('Programación'),
      '#open' => TRUE,
      '#description' => $this->t('<strong>ℹ️ Información sobre zonas horarias:</strong> Las fechas y horas que configure se guardan en tiempo universal (UTC). Esto garantiza que todos los usuarios vean la notificación al mismo instante de tiempo real, independientemente de su ubicación geográfica.'),
    ];

    // Obtener zona horaria del usuario actual
    $user_timezone = \Drupal::currentUser()->getTimeZone();

    $form['scheduling']['start_date'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Fecha de inicio'),
      '#required' => TRUE,
      '#default_value' => $notification && $notification->start_date
        ? \Drupal\Core\Datetime\DrupalDateTime::createFromTimestamp($notification->start_date, $user_timezone)
        : new \Drupal\Core\Datetime\DrupalDateTime('now', $user_timezone),
      '#description' => $this->t('Fecha y hora en que la notificación se activará. <strong>Su zona horaria: @timezone</strong><br><em>La notificación se mostrará a TODOS los usuarios al mismo instante de tiempo real, sin importar en qué zona horaria se encuentren. Por ejemplo: si programa para las 4:00 PM, un usuario en EST la verá a las 4:00 PM de su reloj, mientras que un usuario en CST la verá a las 2:00 PM de su reloj (mismo momento, diferente hora local).</em>', [
        '@timezone' => $user_timezone,
      ]),
      '#states' => [
        'visible' => [
          ':input[name="type"]' => ['value' => 'banner'],
        ],
      ],
    ];

    $form['scheduling']['end_date'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Fecha de fin (opcional)'),
      '#default_value' => $notification && $notification->end_date
        ? \Drupal\Core\Datetime\DrupalDateTime::createFromTimestamp($notification->end_date, $user_timezone)
        : NULL,
      '#description' => $this->t('Fecha y hora en que la notificación dejará de mostrarse. Dejar vacío para que no expire. <strong>Su zona horaria: @timezone</strong><br><em>Al igual que la fecha de inicio, esta hora se aplicará como instante absoluto para todos los usuarios.</em>', [
        '@timezone' => $user_timezone,
      ]),
      '#states' => [
        'visible' => [
          ':input[name="type"]' => ['value' => 'banner'],
        ],
      ],
    ];

    $form['status'] = [
      '#type' => 'select',
      '#title' => $this->t('Estado'),
      '#required' => TRUE,
      '#options' => [
        'draft' => $this->t('Borrador'),
        'active' => $this->t('Activa'),
        'completed' => $this->t('Completada'),
      ],
      '#default_value' => $notification ? $notification->status : 'draft',
      '#description' => $this->t('Estado de la notificación. Solo las notificaciones activas se mostrarán.'),
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $notification ? $this->t('Actualizar') : $this->t('Crear'),
      '#button_type' => 'primary',
    ];

    $form['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Cancelar'),
      '#url' => \Drupal\Core\Url::fromRoute('admin_notifications.list'),
      '#attributes' => ['class' => ['button']],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Validar fechas solo para notificaciones banner
    if ($form_state->getValue('type') === 'banner') {
      $start_date = $form_state->getValue('start_date');
      $end_date = $form_state->getValue('end_date');

      // Verificar que las fechas sean objetos DrupalDateTime válidos
      if ($end_date && $start_date &&
          is_object($start_date) && method_exists($start_date, 'getTimestamp') &&
          is_object($end_date) && method_exists($end_date, 'getTimestamp')) {
        $start_timestamp = $start_date->getTimestamp();
        $end_timestamp = $end_date->getTimestamp();

        if ($end_timestamp <= $start_timestamp) {
          $form_state->setErrorByName('end_date', $this->t('La fecha de fin debe ser posterior a la fecha de inicio.'));
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $current_user = $this->currentUser();
    $current_time = \Drupal::time()->getRequestTime();

    $start_date = $form_state->getValue('start_date');
    $end_date = $form_state->getValue('end_date');

    // Debug: Log para verificar qué está llegando
    \Drupal::logger('admin_notifications')->info('Start date value: @start, End date value: @end, End date type: @type', [
      '@start' => print_r($start_date, TRUE),
      '@end' => print_r($end_date, TRUE),
      '@type' => gettype($end_date),
    ]);

    // Procesar fechas
    $start_timestamp = $current_time;
    if ($form_state->getValue('type') === 'banner' && $start_date && is_object($start_date)) {
      if (method_exists($start_date, 'getTimestamp')) {
        $start_timestamp = $start_date->getTimestamp();
      } elseif (method_exists($start_date, 'format')) {
        $start_timestamp = (int) $start_date->format('U');
      }
    }

    $end_timestamp = NULL;
    if ($end_date && is_object($end_date)) {
      // DrupalDateTime puede usar getTimestamp() o format('U')
      if (method_exists($end_date, 'getTimestamp')) {
        $end_timestamp = $end_date->getTimestamp();
      } elseif (method_exists($end_date, 'format')) {
        $end_timestamp = (int) $end_date->format('U');
      }
      \Drupal::logger('admin_notifications')->info('End timestamp calculated: @ts', ['@ts' => $end_timestamp]);
    }

    $notification_data = [
      'title' => $form_state->getValue('title'),
      'message' => $form_state->getValue('message'),
      'type' => $form_state->getValue('type'),
      'severity' => $form_state->getValue('severity'),
      'status' => $form_state->getValue('status'),
      'start_date' => $start_timestamp,
      'end_date' => $end_timestamp,
      'updated' => $current_time,
    ];

    $notification_id = $form_state->get('notification_id');

    if ($notification_id) {
      // Actualizar notificación existente
      $this->database->update('admin_notifications')
        ->fields($notification_data)
        ->condition('id', $notification_id)
        ->execute();

      $this->messenger()->addStatus($this->t('La notificación ha sido actualizada.'));
    }
    else {
      // Crear nueva notificación
      $notification_data['created'] = $current_time;
      $notification_data['created_by'] = $current_user->id();

      $notification_id = $this->database->insert('admin_notifications')
        ->fields($notification_data)
        ->execute();

      $this->messenger()->addStatus($this->t('La notificación ha sido creada.'));

      // Si es notificación en tiempo real y está activa, crear un estado para polling
      if ($notification_data['type'] === 'realtime' && $notification_data['status'] === 'active') {
        \Drupal::state()->set('admin_notifications.new_notification', [
          'id' => $notification_id,
          'timestamp' => $current_time,
        ]);
      }
    }

    $form_state->setRedirect('admin_notifications.list');
  }

}
