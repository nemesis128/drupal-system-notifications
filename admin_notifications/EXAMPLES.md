# Ejemplos de Uso - Admin Notifications

Este archivo contiene ejemplos prácticos de cómo usar el módulo de notificaciones administrativas.

## Ejemplos básicos desde la interfaz

### 1. Aviso de Mantenimiento Programado

**Escenario:** Necesitas avisar a los administradores que habrá mantenimiento mañana.

**Configuración:**
- **Tipo:** Banner
- **Título:** Mantenimiento Programado
- **Mensaje:** El sistema estará en mantenimiento mañana de 2:00 AM a 4:00 AM. Durante este tiempo el sitio no estará disponible.
- **Severidad:** Warning
- **Fecha inicio:** 2025-01-15 00:00 (un día antes del mantenimiento)
- **Fecha fin:** 2025-01-16 04:00 (cuando termina el mantenimiento)
- **Estado:** Activa

### 2. Alerta Urgente en Tiempo Real

**Escenario:** Se detectó un problema crítico y necesitas avisar inmediatamente a todos los administradores conectados.

**Configuración:**
- **Tipo:** Tiempo Real (Toast)
- **Título:** ¡URGENTE! - Error en el sistema de pagos
- **Mensaje:** Se detectó un problema con el procesador de pagos. No aceptes pedidos hasta nuevo aviso.
- **Severidad:** Error
- **Estado:** Activa

### 3. Anuncio de Nueva Funcionalidad

**Escenario:** Se agregó una nueva funcionalidad y quieres que los administradores la conozcan.

**Configuración:**
- **Tipo:** Banner
- **Título:** Nueva funcionalidad disponible
- **Mensaje:** Ya está disponible el nuevo editor de contenido avanzado. Puedes acceder desde el menú Contenido > Editor Avanzado.
- **Severidad:** Success
- **Fecha inicio:** 2025-01-15 09:00
- **Fecha fin:** 2025-01-22 17:00 (una semana)
- **Estado:** Activa

### 4. Recordatorio Informativo

**Escenario:** Recordar a los administradores sobre una capacitación.

**Configuración:**
- **Tipo:** Banner
- **Título:** Recordatorio: Capacitación de sistema
- **Mensaje:** No olvides asistir a la capacitación del nuevo módulo de reportes, hoy a las 3:00 PM en la sala de juntas virtual.
- **Severidad:** Info
- **Fecha inicio:** 2025-01-15 08:00
- **Fecha fin:** 2025-01-15 15:00
- **Estado:** Activa

## Ejemplos programáticos (PHP)

### 1. Crear una notificación en tiempo real desde código

```php
<?php

/**
 * Crea una notificación urgente en tiempo real.
 */
function mi_modulo_crear_notificacion_urgente() {
  $database = \Drupal::database();
  $current_time = \Drupal::time()->getRequestTime();

  $notification_id = $database->insert('admin_notifications')
    ->fields([
      'title' => 'Sistema restaurado',
      'message' => 'El sistema ha sido restaurado y está funcionando normalmente.',
      'type' => 'realtime',
      'severity' => 'success',
      'status' => 'active',
      'start_date' => $current_time,
      'end_date' => NULL,
      'created' => $current_time,
      'created_by' => 1, // Usuario admin
      'updated' => $current_time,
    ])
    ->execute();

  // Señalar que hay una nueva notificación para polling
  \Drupal::state()->set('admin_notifications.new_notification', [
    'id' => $notification_id,
    'timestamp' => $current_time,
  ]);

  return $notification_id;
}
```

### 2. Crear un banner programado para mañana

```php
<?php

/**
 * Programa un banner para mostrar mañana durante todo el día.
 */
function mi_modulo_programar_banner_manana() {
  $database = \Drupal::database();
  $current_time = \Drupal::time()->getRequestTime();

  // Calcular inicio y fin (todo el día de mañana)
  $manana_inicio = strtotime('tomorrow 00:00:00');
  $manana_fin = strtotime('tomorrow 23:59:59');

  $notification_id = $database->insert('admin_notifications')
    ->fields([
      'title' => 'Actualización de seguridad',
      'message' => 'Se aplicarán actualizaciones de seguridad críticas. El sistema puede experimentar lentitud.',
      'type' => 'banner',
      'severity' => 'warning',
      'status' => 'active',
      'start_date' => $manana_inicio,
      'end_date' => $manana_fin,
      'created' => $current_time,
      'created_by' => \Drupal::currentUser()->id(),
      'updated' => $current_time,
    ])
    ->execute();

  return $notification_id;
}
```

### 3. Notificar cuando falla un proceso crítico

```php
<?php

/**
 * Envía notificación automática cuando falla la importación.
 */
function mi_modulo_manejar_error_importacion($error_message) {
  $database = \Drupal::database();
  $current_time = \Drupal::time()->getRequestTime();

  $notification_id = $database->insert('admin_notifications')
    ->fields([
      'title' => 'Error en importación de datos',
      'message' => 'La importación programada ha fallado: ' . $error_message,
      'type' => 'realtime',
      'severity' => 'error',
      'status' => 'active',
      'start_date' => $current_time,
      'end_date' => NULL,
      'created' => $current_time,
      'created_by' => 1,
      'updated' => $current_time,
    ])
    ->execute();

  \Drupal::state()->set('admin_notifications.new_notification', [
    'id' => $notification_id,
    'timestamp' => $current_time,
  ]);

  // También registrar en watchdog
  \Drupal::logger('mi_modulo')->error('Importación fallida: @error', [
    '@error' => $error_message,
  ]);
}
```

### 4. Actualizar una notificación existente

```php
<?php

/**
 * Actualiza el mensaje de una notificación existente.
 */
function mi_modulo_actualizar_notificacion($notification_id, $nuevo_mensaje) {
  $database = \Drupal::database();

  $database->update('admin_notifications')
    ->fields([
      'message' => $nuevo_mensaje,
      'updated' => \Drupal::time()->getRequestTime(),
    ])
    ->condition('id', $notification_id)
    ->execute();

  \Drupal::messenger()->addStatus('Notificación actualizada correctamente.');
}
```

### 5. Marcar notificación como completada

```php
<?php

/**
 * Marca una notificación como completada.
 */
function mi_modulo_completar_notificacion($notification_id) {
  $database = \Drupal::database();

  $database->update('admin_notifications')
    ->fields([
      'status' => 'completed',
      'updated' => \Drupal::time()->getRequestTime(),
    ])
    ->condition('id', $notification_id)
    ->execute();
}
```

### 6. Obtener todas las notificaciones activas

```php
<?php

/**
 * Obtiene todas las notificaciones activas.
 */
function mi_modulo_obtener_notificaciones_activas() {
  $database = \Drupal::database();

  $query = $database->select('admin_notifications', 'an')
    ->fields('an')
    ->condition('status', 'active')
    ->orderBy('created', 'DESC');

  $results = $query->execute()->fetchAll();

  return $results;
}
```

### 7. Verificar si un usuario ha leído una notificación

```php
<?php

/**
 * Verifica si un usuario ha leído una notificación específica.
 */
function mi_modulo_usuario_leyo_notificacion($notification_id, $uid = NULL) {
  if ($uid === NULL) {
    $uid = \Drupal::currentUser()->id();
  }

  $database = \Drupal::database();

  $count = $database->select('admin_notifications_read', 'anr')
    ->condition('notification_id', $notification_id)
    ->condition('uid', $uid)
    ->countQuery()
    ->execute()
    ->fetchField();

  return $count > 0;
}
```

## Integración con hooks de Drupal

### 1. Notificar cuando se crea contenido

```php
<?php

/**
 * Implements hook_ENTITY_TYPE_insert().
 */
function mi_modulo_node_insert(\Drupal\node\NodeInterface $node) {
  // Solo para tipo de contenido 'articulo'
  if ($node->bundle() === 'articulo') {
    $database = \Drupal::database();
    $current_time = \Drupal::time()->getRequestTime();

    $notification_id = $database->insert('admin_notifications')
      ->fields([
        'title' => 'Nuevo artículo publicado',
        'message' => 'Se ha publicado el artículo: ' . $node->getTitle(),
        'type' => 'realtime',
        'severity' => 'info',
        'status' => 'active',
        'start_date' => $current_time,
        'created' => $current_time,
        'created_by' => $node->getOwnerId(),
        'updated' => $current_time,
      ])
      ->execute();

    \Drupal::state()->set('admin_notifications.new_notification', [
      'id' => $notification_id,
      'timestamp' => $current_time,
    ]);
  }
}
```

### 2. Notificar cuando hay actualizaciones de módulos

```php
<?php

/**
 * Implements hook_cron().
 */
function mi_modulo_cron() {
  // Verificar si hay actualizaciones disponibles
  $available = update_get_available(TRUE);
  $project_data = update_calculate_project_data($available);

  $updates_needed = FALSE;
  foreach ($project_data as $project) {
    if (isset($project['status']) && $project['status'] < UPDATE_CURRENT) {
      $updates_needed = TRUE;
      break;
    }
  }

  if ($updates_needed) {
    $database = \Drupal::database();
    $current_time = \Drupal::time()->getRequestTime();

    // Verificar si ya existe una notificación reciente
    $recent = $database->select('admin_notifications', 'an')
      ->condition('title', 'Actualizaciones disponibles')
      ->condition('created', $current_time - 86400, '>') // Últimas 24 horas
      ->countQuery()
      ->execute()
      ->fetchField();

    if (!$recent) {
      $database->insert('admin_notifications')
        ->fields([
          'title' => 'Actualizaciones disponibles',
          'message' => 'Hay actualizaciones de módulos disponibles. Revisa el gestor de actualizaciones.',
          'type' => 'banner',
          'severity' => 'warning',
          'status' => 'active',
          'start_date' => $current_time,
          'created' => $current_time,
          'created_by' => 1,
          'updated' => $current_time,
        ])
        ->execute();
    }
  }
}
```

## Personalización de estilos

### 1. Cambiar colores del tema

Agrega esto a tu archivo CSS del tema:

```css
/* Personalizar notificación de error */
.toast-notification--error::before {
  background-color: #c62828; /* Rojo más oscuro */
}

.toast-notification--error .toast-notification__icon {
  background-color: #ffcdd2;
  color: #c62828;
}

/* Personalizar posición */
.toast-notifications-container--bottom-right {
  bottom: 80px; /* Más arriba para evitar cookies banner */
  right: 30px;
}

/* Hacer los toasts más anchos */
.toast-notifications-container {
  max-width: 500px;
}
```

### 2. Agregar animaciones personalizadas

```css
/* Animación de rebote al aparecer */
@keyframes bounceIn {
  0% {
    opacity: 0;
    transform: scale(0.3);
  }
  50% {
    transform: scale(1.05);
  }
  70% {
    transform: scale(0.9);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

.toast-notification--visible {
  animation: bounceIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}
```

## Casos de uso avanzados

### 1. Sistema de alertas por roles

Aunque el módulo base no incluye filtrado por roles, puedes extenderlo:

```php
<?php

/**
 * Envía notificación solo a administradores.
 */
function mi_modulo_notificar_solo_admins($titulo, $mensaje) {
  // El sistema de permisos de Drupal ya maneja esto
  // Solo los usuarios con 'view admin notifications' verán las notificaciones

  $database = \Drupal::database();
  $current_time = \Drupal::time()->getRequestTime();

  $notification_id = $database->insert('admin_notifications')
    ->fields([
      'title' => $titulo,
      'message' => $mensaje,
      'type' => 'realtime',
      'severity' => 'info',
      'status' => 'active',
      'start_date' => $current_time,
      'created' => $current_time,
      'created_by' => \Drupal::currentUser()->id(),
      'updated' => $current_time,
    ])
    ->execute();

  \Drupal::state()->set('admin_notifications.new_notification', [
    'id' => $notification_id,
    'timestamp' => $current_time,
  ]);
}
```

### 2. Limpieza manual de notificaciones antiguas

```php
<?php

/**
 * Limpia notificaciones completadas de hace más de 30 días.
 */
function mi_modulo_limpiar_notificaciones_antiguas() {
  $database = \Drupal::database();
  $expiration = strtotime('-30 days');

  // Eliminar notificaciones antiguas completadas
  $deleted = $database->delete('admin_notifications')
    ->condition('status', 'completed')
    ->condition('updated', $expiration, '<')
    ->execute();

  \Drupal::messenger()->addStatus("Se eliminaron {$deleted} notificaciones antiguas.");
}
```

## Testing

### Probar el sistema de polling manualmente

Abre la consola del navegador y ejecuta:

```javascript
// Verificar configuración
console.log(drupalSettings.adminNotifications);

// Forzar un check manual
jQuery.ajax({
  url: '/admin-notifications/poll',
  method: 'GET',
  data: { last_check: 0 },
  success: function(response) {
    console.log('Notificaciones:', response);
  }
});

// Mostrar toast manual
Drupal.toastNotifications.show(
  'Prueba',
  'Este es un mensaje de prueba',
  'success'
);
```

---

¿Necesitas más ejemplos específicos? Revisa el código fuente en `admin_notifications.module` y los controladores en `src/Controller/`.
