# Admin Notifications - Documentación de API

## Tablas de Base de Datos

### admin_notifications

Tabla principal de notificaciones.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | serial | Primary key |
| title | varchar(255) | Título |
| message | text | Mensaje |
| severity | varchar(20) | info, success, warning, error |
| type | varchar(20) | realtime, banner |
| status | varchar(20) | active, inactive, draft |
| created | int | Timestamp de creación |
| created_by | int | FK a users |
| start_date | int | Inicio (banner) |
| end_date | int | Fin (banner) |

### admin_notifications_read

Tracking de notificaciones leídas.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | serial | Primary key |
| notification_id | int | FK a admin_notifications |
| uid | int | FK a users |
| read_timestamp | int | Timestamp de lectura |

**Índices:**
- `notification_id`
- `uid`
- Compuesto: `(notification_id, uid)`

## Endpoints REST

### GET /admin-notifications/poll

Obtiene nuevas notificaciones desde el último check.

**Autenticación:** Requiere sesión activa

**Permisos requeridos:**
- `view admin notifications` O
- `access administration pages`

**Parámetros:**
- `last_check` (query, int): Timestamp del último check

**Respuesta Exitosa (200):**
```json
{
  "notifications": [
    {
      "id": 123,
      "title": "Nueva actualización",
      "message": "El sistema fue actualizado.",
      "severity": "success",
      "created": 1234567890
    }
  ],
  "timestamp": 1234567890,
  "count": 1
}
```

**Respuesta Error (403):**
```json
{
  "notifications": [],
  "error": "Access denied"
}
```

**Ejemplo jQuery:**
```javascript
jQuery.ajax({
  url: '/admin-notifications/poll',
  data: { last_check: 1234567890 },
  success: function(data) {
    data.notifications.forEach(function(notification) {
      console.log(notification.title);
    });
  }
});
```

### POST /admin-notifications/{id}/mark-read

Marca notificación como leída.

**Autenticación:** Requiere sesión activa

**Permisos requeridos:**
- `view admin notifications` O
- `access administration pages`

**Parámetros:**
- `id` (path, int): ID de la notificación

**Respuesta Exitosa (200):**
```json
{
  "success": true
}
```

**Respuesta Error (404):**
```json
{
  "success": false,
  "error": "Notification not found"
}
```

## Uso Programático

### Crear Notificación

```php
// Obtener servicio de base de datos
$database = \Drupal::database();

// Crear notificación en tiempo real
$notification_id = $database->insert('admin_notifications')
  ->fields([
    'title' => 'Nueva actualización disponible',
    'message' => 'Se ha publicado una nueva versión del sistema.',
    'severity' => 'info',
    'type' => 'realtime',
    'status' => 'active',
    'created' => \Drupal::time()->getRequestTime(),
    'created_by' => \Drupal::currentUser()->id(),
  ])
  ->execute();

// Crear notificación banner programada
$notification_id = $database->insert('admin_notifications')
  ->fields([
    'title' => 'Mantenimiento programado',
    'message' => 'El sistema estará en mantenimiento el próximo sábado.',
    'severity' => 'warning',
    'type' => 'banner',
    'status' => 'active',
    'start_date' => strtotime('next Saturday 00:00'),
    'end_date' => strtotime('next Saturday 06:00'),
    'created' => \Drupal::time()->getRequestTime(),
    'created_by' => \Drupal::currentUser()->id(),
  ])
  ->execute();
```

### Obtener Notificaciones Activas

```php
$database = \Drupal::database();
$current_time = \Drupal::time()->getRequestTime();

// Obtener notificaciones en tiempo real
$query = $database->select('admin_notifications', 'an')
  ->fields('an')
  ->condition('an.type', 'realtime')
  ->condition('an.status', 'active')
  ->orderBy('an.created', 'DESC');

$notifications = $query->execute()->fetchAll();

// Obtener banners activos en este momento
$query = $database->select('admin_notifications', 'an')
  ->fields('an')
  ->condition('an.type', 'banner')
  ->condition('an.status', 'active')
  ->condition('an.start_date', $current_time, '<=')
  ->condition('an.end_date', $current_time, '>=')
  ->orderBy('an.created', 'DESC');

$banners = $query->execute()->fetchAll();
```

### Marcar como Leída

```php
$database = \Drupal::database();
$notification_id = 123;
$user_id = \Drupal::currentUser()->id();

// Verificar si ya está leída
$already_read = $database->select('admin_notifications_read', 'anr')
  ->condition('notification_id', $notification_id)
  ->condition('uid', $user_id)
  ->countQuery()
  ->execute()
  ->fetchField();

if (!$already_read) {
  $database->insert('admin_notifications_read')
    ->fields([
      'notification_id' => $notification_id,
      'uid' => $user_id,
      'read_timestamp' => \Drupal::time()->getRequestTime(),
    ])
    ->execute();
}
```

### Eliminar Notificación

```php
$database = \Drupal::database();
$notification_id = 123;

// Eliminar registros de lectura primero
$database->delete('admin_notifications_read')
  ->condition('notification_id', $notification_id)
  ->execute();

// Eliminar notificación
$database->delete('admin_notifications')
  ->condition('id', $notification_id)
  ->execute();
```

## Hooks

Aunque el módulo actualmente no implementa hooks personalizados, puedes usar
hooks estándar de Drupal para interactuar con el módulo:

### hook_cron()

Ejemplo de limpieza de notificaciones antiguas:

```php
/**
 * Implements hook_cron().
 */
function mymodule_cron() {
  $database = \Drupal::database();
  $seven_days_ago = \Drupal::time()->getRequestTime() - (7 * 24 * 60 * 60);

  // Eliminar notificaciones completadas con más de 7 días
  $old_notifications = $database->select('admin_notifications', 'an')
    ->fields('an', ['id'])
    ->condition('status', 'completed')
    ->condition('created', $seven_days_ago, '<')
    ->execute()
    ->fetchCol();

  if (!empty($old_notifications)) {
    // Eliminar registros de lectura
    $database->delete('admin_notifications_read')
      ->condition('notification_id', $old_notifications, 'IN')
      ->execute();

    // Eliminar notificaciones
    $database->delete('admin_notifications')
      ->condition('id', $old_notifications, 'IN')
      ->execute();

    \Drupal::logger('admin_notifications')
      ->info('Deleted @count old notifications', ['@count' => count($old_notifications)]);
  }
}
```

## Permisos

### administer admin notifications

Permite:
- Crear/editar/eliminar notificaciones
- Acceder al panel administrativo
- Configurar opciones del módulo

### view admin notifications

Permite:
- Ver notificaciones toast y banner
- Marcar notificaciones como leídas
- Acceso al endpoint de polling

## Configuración

**Config Object:** `admin_notifications.settings`

```php
$config = \Drupal::config('admin_notifications.settings');

// Obtener valores
$poll_interval = $config->get('poll_interval'); // default: 30000
$toast_duration = $config->get('toast_duration'); // default: 10000
$toast_position = $config->get('toast_position'); // default: 'bottom-right'
$sound_enabled = $config->get('sound_enabled'); // default: TRUE

// Establecer valores
\Drupal::configFactory()->getEditable('admin_notifications.settings')
  ->set('poll_interval', 60000)
  ->save();
```

## Integración con Otros Módulos

### Webform

Enviar notificación al completar webform:

```php
/**
 * Implements hook_webform_submission_insert().
 */
function mymodule_webform_submission_insert(WebformSubmissionInterface $submission) {
  $webform = $submission->getWebform();

  if ($webform->id() === 'contact') {
    \Drupal::database()->insert('admin_notifications')
      ->fields([
        'title' => 'Nuevo mensaje de contacto',
        'message' => 'Se ha recibido un nuevo mensaje del formulario de contacto.',
        'severity' => 'info',
        'type' => 'realtime',
        'status' => 'active',
        'created' => \Drupal::time()->getRequestTime(),
        'created_by' => 1,
      ])
      ->execute();
  }
}
```

### Rules

Si usas el módulo Rules, puedes crear notificaciones como acciones.

### Custom Modules

Para módulos personalizados, simplemente usa las queries de base de datos
mostradas arriba para crear, actualizar o eliminar notificaciones según
tus necesidades.

## Testing

### Ejemplo de Test Funcional

```php
namespace Drupal\Tests\admin_notifications\Functional;

use Drupal\Tests\BrowserTestBase;

class NotificationTest extends BrowserTestBase {

  protected static $modules = ['admin_notifications'];

  public function testCreateNotification() {
    $admin = $this->drupalCreateUser(['administer admin notifications']);
    $this->drupalLogin($admin);

    $this->drupalGet('admin/reports/admin-notifications/add');
    $this->submitForm([
      'title' => 'Test Notification',
      'message' => 'Test message',
      'severity' => 'info',
      'type' => 'realtime',
    ], 'Save');

    $this->assertSession()->pageTextContains('Notification created');
  }
}
```

## Logging

El módulo usa el sistema de logging de Drupal (Watchdog):

```bash
# Ver logs del módulo
drush watchdog:show --filter=admin_notifications

# Ver solo errores
drush watchdog:show --severity=Error --filter=admin_notifications
```

Ver [LOGGING.md](LOGGING.md) para más detalles.

## Ejemplos Adicionales

Ver [EXAMPLES.md](EXAMPLES.md) para más ejemplos de uso.
