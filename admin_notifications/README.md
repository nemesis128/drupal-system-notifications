# Admin Notifications

Sistema profesional de notificaciones administrativas para Drupal con soporte
para notificaciones toast (estilo Windows 10/11), banners programables y
notificaciones en tiempo real mediante polling.

Para obtener una descripción completa del módulo, visite la
[página del proyecto](https://www.drupal.org/project/admin_notifications).

Para enviar informes de bugs, solicitudes de funcionalidades y parches, visite
[la cola de issues](https://www.drupal.org/project/issues/admin_notifications).


## Tabla de Contenidos

- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Características](#características)
- [Uso](#uso)
- [API para Desarrolladores](#api-para-desarrolladores)
- [Troubleshooting](#troubleshooting)
- [Testing](#testing)
- [Mantenedores](#mantenedores)


## Requisitos

Este módulo requiere:

- Drupal: ^9.3 || ^10
- PHP: >= 7.4


## Instalación

Instalar como lo harías normalmente con cualquier módulo de Drupal. Para más
información, consulta:
[Instalando módulos de Drupal](https://www.drupal.org/node/1897420).

### Usando Composer (Recomendado)

```bash
composer require drupal/admin_notifications
drush en admin_notifications
drush cr
```

### Instalación Manual

1. Descargar el módulo desde la página del proyecto
2. Extraer en `modules/contrib/admin_notifications`
3. Habilitar en `admin/modules` o con drush:
   ```bash
   drush en admin_notifications
   drush cr
   ```


## Configuración

1. Navegar a **Configuración > Sistema > Admin Notifications**
   (`admin/config/system/admin-notifications`)

2. Configurar opciones disponibles:
   - **Intervalo de Polling:** Frecuencia de verificación (milisegundos)
   - **Duración de Toast:** Tiempo de visualización (milisegundos)
   - **Posición de Toast:** Ubicación en pantalla
   - **Sonido:** Activar/desactivar notificación sonora

3. Configurar permisos en **Personas > Permisos**:
   - `administer admin notifications` - Gestionar notificaciones
   - `view admin notifications` - Ver notificaciones

4. Crear notificaciones en **Administración > Notificaciones**
   (`admin/reports/admin-notifications`)


## Características

### Tipos de Notificaciones

#### 1. Toast Notifications (Tiempo Real)
Notificaciones estilo Windows 10/11 que aparecen automáticamente:
- Verificación automática cada 30 segundos (configurable)
- Posiciones personalizables (4 esquinas)
- Sonido opcional
- Auto-cierre configurable
- 4 niveles de severidad (info, success, warning, error)

#### 2. Banner Notifications (Programadas)
Banners persistentes con programación por fecha:
- Fecha de inicio y fin
- Visible en páginas específicas o globalmente
- Botón de cerrar/ocultar
- Estilos según severidad
- Responsive

### Capacidades

- **Sistema de Polling:** Verificación automática en tiempo real
- **Multiusuario:** Notificaciones por usuario, rol o globales
- **Estado de Lectura:** Seguimiento de leídas/no leídas
- **Gestión Completa:** CRUD de notificaciones vía UI
- **Multiidioma:** Soporte para 5 idiomas
- **Accesibilidad:** WCAG 2.1 compatible
- **Logging:** Integración con Drupal Watchdog


## Uso

### Para Administradores

#### Crear Notificación Toast (Tiempo Real)
1. Ir a `admin/reports/admin-notifications`
2. Clic en "Agregar notificación"
3. Seleccionar tipo: **Tiempo Real**
4. Completar título y mensaje
5. Elegir severidad (info, success, warning, error)
6. Guardar

La notificación aparecerá automáticamente a usuarios con permisos en su
próximo ciclo de polling.

#### Crear Notificación Banner (Programada)
1. Ir a `admin/reports/admin-notifications`
2. Clic en "Agregar notificación"
3. Seleccionar tipo: **Programada**
4. Completar título y mensaje
5. Configurar fecha inicio/fin
6. Elegir severidad
7. Guardar

El banner aparecerá entre las fechas configuradas.

### Para Desarrolladores

Ver [API.md](API.md) para documentación completa de la API.

#### Crear Notificación Programáticamente

```php
// Obtener el servicio
$database = \Drupal::database();

// Crear notificación toast
$database->insert('admin_notifications')
  ->fields([
    'title' => 'Nueva actualización',
    'message' => 'El sistema ha sido actualizado exitosamente.',
    'severity' => 'success',
    'type' => 'realtime',
    'status' => 'active',
    'created' => \Drupal::time()->getRequestTime(),
    'created_by' => \Drupal::currentUser()->id(),
  ])
  ->execute();

// Crear notificación banner programada
$database->insert('admin_notifications')
  ->fields([
    'title' => 'Mantenimiento programado',
    'message' => 'El sistema estará en mantenimiento mañana de 2-4 AM.',
    'severity' => 'warning',
    'type' => 'banner',
    'status' => 'active',
    'start_date' => strtotime('tomorrow'),
    'end_date' => strtotime('+7 days'),
    'created' => \Drupal::time()->getRequestTime(),
    'created_by' => \Drupal::currentUser()->id(),
  ])
  ->execute();
```


## API para Desarrolladores

### Endpoints REST

#### GET `/admin-notifications/poll`
Endpoint de polling para obtener nuevas notificaciones.

**Parámetros:**
- `last_check` (int) - Timestamp del último check

**Respuesta:**
```json
{
  "notifications": [
    {
      "id": 123,
      "title": "Título",
      "message": "Mensaje",
      "severity": "info",
      "created": 1234567890
    }
  ],
  "timestamp": 1234567890,
  "count": 1
}
```

#### POST `/admin-notifications/{notification_id}/mark-read`
Marcar notificación como leída.

**Respuesta:**
```json
{
  "success": true
}
```

Ver [API.md](API.md) para documentación completa.


## Troubleshooting

### Las notificaciones toast no aparecen

1. **Verificar permisos:**
   ```bash
   drush user:role:add "view admin notifications" authenticated
   ```

2. **Verificar configuración de polling:**
   - Ir a `admin/config/system/admin-notifications`
   - Confirmar que el intervalo de polling está configurado (ej: 30000ms)

3. **Limpiar caché:**
   ```bash
   drush cr
   ```

4. **Revisar logs:**
   ```bash
   drush watchdog:show --filter=admin_notifications
   ```

### Los banners causan scroll horizontal

Actualizar a la última versión que incluye la corrección de ancho:
```bash
composer update drupal/admin_notifications
drush cr
```

### Problemas de caché con JavaScript

1. Deshabilitar agregación durante desarrollo:
   ```bash
   drush config:set system.performance js.preprocess 0 -y
   drush config:set system.performance css.preprocess 0 -y
   ```

2. Limpiar archivos agregados:
   ```bash
   drush cr
   rm -rf sites/default/files/js/*
   rm -rf sites/default/files/css/*
   ```

### Ver logs detallados

```bash
# Ver todos los logs del módulo
drush watchdog:show --filter=admin_notifications

# Ver solo errores
drush watchdog:show --severity=Error --filter=admin_notifications

# Ver en tiempo real
drush watchdog:tail --filter=admin_notifications
```


## Testing

El módulo incluye una suite completa de tests: Unit, Kernel y Functional.

### Ejecutar Unit Tests

```bash
cd web/modules/push-notification-drupal/admin_notifications
../../../../vendor/bin/phpunit tests/src/Unit/
```

Los Unit tests validan:
- Validación de severidades (info, success, warning, error)
- Validación de tipos (realtime, banner)
- Estructura de notificaciones
- Validación de fechas para notificaciones programadas
- Validación de longitud de títulos

### Kernel Tests

Los Kernel tests validan operaciones de base de datos:

```bash
cd web/core
php ../../vendor/bin/phpunit --group admin_notifications --testsuite kernel
```

Funcionalidad probada:
- Creación de notificaciones en base de datos
- Consulta de notificaciones activas
- Marcado de notificaciones como leídas
- Eliminación de notificaciones
- Filtrado de banners por fecha

### Functional Tests

Los Functional tests validan la interfaz de usuario completa:

```bash
cd web/core
php ../../vendor/bin/phpunit --group admin_notifications --testsuite functional
```

Funcionalidad probada:
- Creación de notificaciones a través del formulario web
- Listado y gestión de notificaciones (editar, eliminar)
- Endpoint de polling en tiempo real
- Formulario de configuración
- Control de acceso y permisos
- Validación de formularios

**Nota:** Los tests Kernel y Functional requieren una instalación completa de Drupal
con base de datos configurada y las dependencias de testing instaladas (Mink, Symfony
PHPUnit Bridge, etc.). Consulta la
[documentación de PHPUnit de Drupal](https://www.drupal.org/docs/automated-testing/phpunit-in-drupal)
para más información.


## Traducciones

El módulo incluye soporte para 5 idiomas:
- Español (es)
- Inglés (en)
- Francés (fr)
- Portugués Brasil (pt-br)
- Japonés (ja)

Ver [translations/README.md](translations/README.md) para instrucciones de importación.


## Mantenedores

- **Admin Notifications Team**

### Contribuyendo

Las contribuciones son bienvenidas. Por favor:

1. Crear un issue antes de trabajar en nuevas características
2. Seguir [Drupal Coding Standards](https://www.drupal.org/docs/develop/standards)
3. Incluir tests para nuevas funcionalidades
4. Actualizar documentación según sea necesario

Ver [CONTRIBUTING.md](CONTRIBUTING.md) para más detalles.


## Licencia

Este proyecto está licenciado bajo GPL-2.0-or-later.
Ver [LICENSE.txt](LICENSE.txt) para más información.


## Enlaces

- **Página del proyecto:** https://www.drupal.org/project/admin_notifications
- **Documentación:** https://www.drupal.org/docs/contributed-modules/admin-notifications
- **Issues:** https://www.drupal.org/project/issues/admin_notifications
- **Git:** https://git.drupalcode.org/project/admin_notifications


## Documentación Adicional

- [API.md](API.md) - Documentación completa de la API
- [CONTRIBUTING.md](CONTRIBUTING.md) - Guía de contribución
- [CHANGELOG.md](CHANGELOG.md) - Historial de cambios
- [LOGGING.md](LOGGING.md) - Sistema de logging
- [EXAMPLES.md](EXAMPLES.md) - Ejemplos de uso
