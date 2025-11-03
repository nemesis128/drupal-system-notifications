<?php

namespace Drupal\admin_notifications\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Formulario de configuración del módulo.
 */
class AdminNotificationSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['admin_notifications.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'admin_notifications_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('admin_notifications.settings');

    $form['polling'] = [
      '#type' => 'details',
      '#title' => $this->t('Configuración de Polling'),
      '#open' => TRUE,
    ];

    $form['polling']['poll_interval'] = [
      '#type' => 'number',
      '#title' => $this->t('Intervalo de polling (milisegundos)'),
      '#default_value' => $config->get('poll_interval') ?? 30000,
      '#min' => 5000,
      '#max' => 300000,
      '#step' => 1000,
      '#description' => $this->t('Frecuencia con la que se verifican nuevas notificaciones en tiempo real. Valor por defecto: 30000 (30 segundos).'),
    ];

    $form['toast'] = [
      '#type' => 'details',
      '#title' => $this->t('Configuración de Notificaciones Toast'),
      '#open' => TRUE,
    ];

    $form['toast']['toast_duration'] = [
      '#type' => 'number',
      '#title' => $this->t('Duración del toast (milisegundos)'),
      '#default_value' => $config->get('toast_duration') ?? 10000,
      '#min' => 3000,
      '#max' => 60000,
      '#step' => 1000,
      '#description' => $this->t('Tiempo que permanece visible la notificación toast antes de ocultarse automáticamente.'),
    ];

    $form['toast']['toast_position'] = [
      '#type' => 'select',
      '#title' => $this->t('Posición del toast'),
      '#default_value' => $config->get('toast_position') ?? 'bottom-right',
      '#options' => [
        'top-left' => $this->t('Superior izquierda'),
        'top-right' => $this->t('Superior derecha'),
        'bottom-left' => $this->t('Inferior izquierda'),
        'bottom-right' => $this->t('Inferior derecha'),
      ],
      '#description' => $this->t('Posición en la pantalla donde aparecerán las notificaciones toast.'),
    ];

    $form['toast']['sound_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Habilitar sonido de notificación'),
      '#default_value' => $config->get('sound_enabled') ?? TRUE,
      '#description' => $this->t('Reproducir un sonido cuando aparece una notificación en tiempo real.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('admin_notifications.settings')
      ->set('poll_interval', $form_state->getValue('poll_interval'))
      ->set('toast_duration', $form_state->getValue('toast_duration'))
      ->set('toast_position', $form_state->getValue('toast_position'))
      ->set('sound_enabled', $form_state->getValue('sound_enabled'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
