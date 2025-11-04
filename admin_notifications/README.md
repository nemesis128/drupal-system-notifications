# Módulo de Notificaciones Administrativas para Drupal 10

Sistema completo de notificaciones administrativas para Drupal 10 con soporte para alertas banner y notificaciones en tiempo real estilo Windows.

## Características

### 📢 Dos tipos de notificaciones

1. **Notificaciones en Tiempo Real (Toast)**
   - Aparecen automáticamente en la esquina inferior derecha (configurable)
   - Estilo Windows 10/11
   - Se muestran a usuarios conectados con permisos administrativos
   - Ideal para avisos urgentes de mantenimiento o alertas inmediatas

2. **Notificaciones Banner Programadas**
   - Similar a las alertas nativas de Drupal
   - Se muestran en la parte superior del contenido
   - Pueden programarse con fecha de inicio y fin
   - Perfectas para avisos con anticipación

### ✨ Funcionalidades principales

- **Panel de administración completo** para crear y gestionar notificaciones
- **Sistema de polling automático** para notificaciones en tiempo real (sin necesidad de WebSocket)
  - Polling cada 30 segundos (configurable)
  - Detección automática de nuevas notificaciones sin recargar página
  - Sistema robusto que sobrevive a recargas de página
- **Cuatro niveles de severidad**: Info, Success, Warning, Error
- **Sistema de seguimiento** de notificaciones leídas por usuario
- **Configuración flexible** de intervalos, duración y posición
  - Duración de toast configurable (3-60 segundos)
  - Intervalo de polling configurable (5-300 segundos)
  - 4 posiciones para toasts (esquinas de la pantalla)
- **Botón de cerrar** en notificaciones banner
- **Sonido de notificación** opcional (Web Audio API)
- **Diseño responsive** y accesible
- **Limpieza automática** de notificaciones antiguas vía cron
- **Sistema de logging profesional** con Drupal Watchdog (dblog)
  - Logging de errores, advertencias y eventos
  - Accesible vía interfaz web o Drush
  - Ver `LOGGING.md` para más detalles
- **🌍 Soporte multiidioma**: Inglés, Español, Francés, Portugués (Brasil), Japonés
  - Archivos de traducción incluidos
  - Scripts de importación automática
  - Ver sección de Traducciones más abajo

## Requisitos

- Drupal 10.x
- PHP 8.3+
- Módulos core: user, system, datetime

## Instalación

### 1. Copiar el módulo

Copia la carpeta `admin_notifications` a uno de estos directorios:
- `modules/custom/admin_notifications` (recomendado)
- `sites/all/modules/admin_notifications`

### 2. Habilitar el módulo

**Vía interfaz:**
1. Ve a `Administrar > Extensiones` (admin/modules)
2. Busca "Admin Notifications" en la sección "Custom"
3. Marca la casilla y haz clic en "Instalar"

**Vía Drush:**
```bash
drush en admin_notifications -y
drush cr
```

### 3. Configurar permisos

Ve a `Administrar > Personas > Permisos` (admin/people/permissions) y asigna:

- **"Administrar notificaciones del sistema"**: Para usuarios que pueden crear/editar notificaciones
- **"Ver notificaciones administrativas"**: Para usuarios que deben recibir las notificaciones

**Recomendado:** Asignar ambos permisos al rol de "Administrador"

## Uso

### Crear una notificación

1. Ve a `Administrar > Configuración > Sistema > Notificaciones Administrativas`
   - URL: `/admin/config/system/admin-notifications`

2. Haz clic en "Crear Nueva Notificación"

3. Completa el formulario:
   - **Título**: Título corto y descriptivo
   - **Mensaje**: Contenido completo de la notificación
   - **Tipo de notificación**:
     - **Tiempo Real (Toast)**: Se muestra inmediatamente al guardar
     - **Banner**: Se muestra según la programación
   - **Severidad**: Info, Success, Warning o Error
   - **Programación** (solo para Banner):
     - Fecha de inicio
     - Fecha de fin (opcional)
   - **Estado**:
     - **Borrador**: No se muestra
     - **Activa**: Se muestra a los usuarios
     - **Completada**: Archivada

4. Guarda la notificación

### Ejemplos de uso

#### Aviso de mantenimiento inmediato

```
Tipo: Tiempo Real (Toast)
Severidad: Warning
Título: Mantenimiento programado
Mensaje: El sistema entrará en mantenimiento en 15 minutos. Por favor, guarda tu trabajo.
Estado: Activa
```

#### Aviso de nueva funcionalidad

```
Tipo: Banner
Severidad: Success
Título: Nueva funcionalidad disponible
Mensaje: Ya está disponible el nuevo módulo de reportes avanzados en el menú principal.
Fecha inicio: 2025-01-15 09:00
Fecha fin: 2025-01-22 17:00
Estado: Activa
```

#### Alerta de error crítico

```
Tipo: Tiempo Real (Toast)
Severidad: Error
Título: Error en el sistema de archivos
Mensaje: Se detectó un problema con el almacenamiento. Contacta al equipo técnico.
Estado: Activa
```

## Configuración

### Ajustes del sistema

Ve a `Administrar > Configuración > Sistema > Notificaciones Administrativas > Configuración`
- URL: `/admin/config/system/admin-notifications/settings`

**Opciones disponibles:**

- **Intervalo de polling** (5000-300000 ms)
  - Por defecto: 30000 ms (30 segundos)
  - Frecuencia de verificación de nuevas notificaciones en tiempo real

- **Duración del toast** (3000-60000 ms)
  - Por defecto: 10000 ms (10 segundos)
  - Tiempo que permanece visible la notificación toast

- **Posición del toast**
  - Superior izquierda
  - Superior derecha
  - Inferior izquierda
  - **Inferior derecha** (por defecto)

- **Habilitar sonido**
  - Reproduce un tono cuando aparece una notificación en tiempo real

## Arquitectura técnica

### Sistema de polling

El módulo utiliza un sistema de polling (en lugar de WebSocket) para verificar nuevas notificaciones:

1. JavaScript hace peticiones AJAX al endpoint `/admin-notifications/poll` cada X segundos
2. El servidor devuelve notificaciones nuevas desde el último check
3. Las notificaciones se muestran automáticamente como toast
4. Se marcan como leídas automáticamente

**Ventajas:**
- No requiere infraestructura adicional (WebSocket, Mercure, etc.)
- Funciona en cualquier servidor web estándar
- Fácil de configurar y mantener

### Base de datos

El módulo crea dos tablas:

**`admin_notifications`**
- Almacena todas las notificaciones
- Campos: id, title, message, type, severity, status, start_date, end_date, created, created_by, updated

**`admin_notifications_read`**
- Rastrea qué usuarios han leído qué notificaciones
- Campos: id, notification_id, uid, read_timestamp

### Limpieza automática (Cron)

El hook `hook_cron()` ejecuta automáticamente:
- Elimina registros de lectura mayores a 30 días
- Elimina notificaciones expiradas (con end_date pasado)

## Personalización

### Cambiar estilos CSS

Los estilos se pueden sobrescribir en tu tema:

```css
/* Cambiar el color de las notificaciones de error */
.toast-notification--error::before {
  background-color: #your-color;
}

/* Cambiar la posición del contenedor */
.toast-notifications-container--bottom-right {
  bottom: 20px;
  right: 20px;
}
```

### Modificar el intervalo de polling programáticamente

```php
$config = \Drupal::configFactory()->getEditable('admin_notifications.settings');
$config->set('poll_interval', 60000); // 60 segundos
$config->save();
```

### Crear notificaciones programáticamente

```php
$database = \Drupal::database();

$notification_id = $database->insert('admin_notifications')
  ->fields([
    'title' => 'Mi notificación',
    'message' => 'Mensaje de la notificación',
    'type' => 'realtime', // o 'banner'
    'severity' => 'warning', // info, success, warning, error
    'status' => 'active',
    'start_date' => time(),
    'end_date' => NULL,
    'created' => time(),
    'created_by' => \Drupal::currentUser()->id(),
    'updated' => time(),
  ])
  ->execute();

// Para notificaciones en tiempo real, actualizar el estado
if ($type === 'realtime') {
  \Drupal::state()->set('admin_notifications.new_notification', [
    'id' => $notification_id,
    'timestamp' => time(),
  ]);
}
```

## Solución de problemas

### Las notificaciones en tiempo real no aparecen

1. Verifica que el usuario tenga el permiso "Ver notificaciones administrativas"
2. Abre la consola del navegador (F12) y busca errores JavaScript
3. Verifica que el intervalo de polling esté configurado correctamente
4. Asegúrate de que la notificación esté en estado "Activa"

### Los banners no se muestran

1. Verifica que la fecha de inicio sea anterior a la fecha actual
2. Verifica que la fecha de fin (si existe) sea posterior a la fecha actual
3. Asegúrate de que el estado sea "Activa"
4. Limpia la caché de Drupal: `drush cr`

### Problemas de rendimiento

Si tienes muchos usuarios conectados:
1. Aumenta el intervalo de polling (ej: 60000 ms = 1 minuto)
2. Considera implementar caché en el endpoint de polling
3. Limita el número de notificaciones activas simultáneas

## Mejoras futuras

Posibles mejoras que se pueden implementar:

- [ ] Soporte para WebSocket/Mercure (notificaciones verdaderamente push)
- [ ] Filtrado de notificaciones por roles específicos
- [ ] Plantillas personalizables desde la UI
- [ ] Exportación/importación de notificaciones
- [ ] Estadísticas de visualización
- [ ] Integración con el sistema de mensajes de Drupal
- [ ] Soporte para adjuntar archivos o enlaces
- [ ] Notificaciones recurrentes (diarias, semanales)

## 🌍 Traducciones

El módulo incluye soporte completo para múltiples idiomas. Todas las cadenas de texto están preparadas para traducción usando el sistema de internacionalización de Drupal.

### Idiomas Disponibles

El módulo incluye traducciones para los siguientes idiomas:

- 🇬🇧 **Inglés** (en)
- 🇪🇸 **Español** (es)
- 🇫🇷 **Francés** (fr)
- 🇧🇷 **Portugués (Brasil)** (pt-br)
- 🇯🇵 **Japonés** (ja)

### Importación Automática de Traducciones

#### Para Windows:
```bash
cd modules/custom/admin_notifications/translations
import-all.bat
```

#### Para Linux/Mac:
```bash
cd modules/custom/admin_notifications/translations
bash import-all.sh
```

### Importación Manual

**Vía Drush (recomendado):**
```bash
# Agregar idioma e importar traducciones
drush language:add es
drush locale:import es modules/custom/admin_notifications/translations/es.po --type=customized --override=all -y

# Cambiar idioma predeterminado del sitio
drush config:set system.site default_langcode es -y

# Limpiar caché
drush cr
```

**Vía interfaz web:**
1. Ve a `Configuración > Regional e idioma > Idiomas` (`/admin/config/regional/language`)
2. Haz clic en "Agregar idioma" y selecciona el idioma deseado
3. Ve a `Traducir interfaz > Importar` (`/admin/config/regional/translate/import`)
4. Selecciona el idioma
5. Sube el archivo `.po` correspondiente desde `translations/`
6. Haz clic en "Importar"

### Verificación

Después de importar las traducciones:

1. Cambia el idioma del sitio o del usuario
2. Ve a `Configuración > Sistema > Admin Notifications`
3. Todos los textos deberían estar en el idioma seleccionado

### Agregar Nuevos Idiomas

Para contribuir con traducciones a otros idiomas:

1. Copia el archivo `translations/en.po`
2. Renómbralo con el código de idioma ISO (ej: `de.po` para alemán)
3. Traduce todas las cadenas `msgstr`
4. Importa el archivo usando los métodos anteriores

Para más detalles, consulta `translations/README.md`

## 🐛 Historial de Bugfixes

Esta sección documenta los problemas resueltos y mejoras implementadas durante el desarrollo.

### Versión 1.3 (Última)

#### 🔧 Bugfix: Sistema de Polling no se Ejecutaba Periódicamente

**Problema:**
- El sistema de polling solo se ejecutaba una vez al cargar la página
- Las notificaciones toast NO aparecían automáticamente sin recargar
- El `setInterval` no se estaba ejecutando cada 30 segundos

**Causa Raíz:**
- La condición `if (context !== document)` era demasiado estricta
- Drupal.behaviors puede llamar a `attach()` con diferentes contextos (no siempre `document`)
- El código retornaba inmediatamente sin inicializar el polling

**Solución:**
- Eliminada validación estricta de `context !== document`
- Agregado flag `initialized` para prevenir múltiples inicializaciones
- Validación solo de `settings.adminNotifications` en lugar del contexto
- Archivo: `js/admin-notifications.js:15-30`

**Commit:** [Pendiente]

---

#### 🔧 Bugfix: Duración de Toast No Respetaba Configuración

**Problema:**
- La configuración de duración de toast (ej: 30000ms) no se aplicaba
- Los toasts siempre duraban 10 segundos por defecto

**Causa Raíz:**
- La función `showToastNotification()` no pasaba el parámetro `duration` a `Drupal.toastNotifications.show()`
- El backend no estaba pasando `toast_duration` a `drupalSettings`

**Solución:**
1. **Backend** (`admin_notifications.module:44-52`):
   - Agregadas todas las configuraciones a `drupalSettings`:
     - `toast_duration`
     - `toast_position`
     - `sound_enabled`

2. **Frontend** (`js/admin-notifications.js:88-98`):
   - Modificada función para pasar el parámetro `duration` correctamente:
     ```javascript
     const duration = Drupal.toastNotifications.toastDuration;
     Drupal.toastNotifications.show(title, message, severity, duration);
     ```

**Commit:** [Pendiente]

---

#### 🎨 Mejora: Botón Eliminar con Texto Invisible

**Problema:**
- En algunos temas de Drupal, el botón "Eliminar" en la tabla de notificaciones tenía texto rojo sobre fondo rojo
- El texto solo se hacía visible al hacer hover

**Solución:**
- Forzado color blanco del texto con `!important` en estados normal y hover
- Archivo: `css/admin-notifications.css:37-45`

**Commit:** [Pendiente]

---

#### ✨ Mejora: Botón de Cerrar en Banners

**Problema:**
- Las notificaciones banner no tenían botón de cerrar
- Los usuarios no podían ocultar banners manualmente

**Solución:**
- Mejorados estilos CSS del botón de cerrar existente
- Agregado fondo semitransparente en hover
- Agregado estado de focus para accesibilidad con teclado
- Archivo: `css/banner-notifications.css:59-84`

**Commit:** [Pendiente]

---

#### 📊 Mejora: Sistema de Logging del Servidor

**Implementación:**
- Agregado sistema completo de logging con Drupal Watchdog
- Try-catch en todos los endpoints críticos
- Logging de errores, advertencias y eventos informativos
- Documentación en `LOGGING.md`

**Archivos modificados:**
- `src/Controller/AdminNotificationPollController.php`
- `admin_notifications.module` (hook_cron)

**Tipos de logs:**
- 🔴 **Error**: Excepciones en polling, mark-read, cron
- ⚠️ **Warning**: Accesos denegados, notificaciones no encontradas
- ℹ️ **Info**: Limpieza exitosa de cron

**Ver logs:**
```bash
drush watchdog:show --type=admin_notifications
```

**Commit:** [Pendiente]

---

#### 🌍 Mejora: Soporte Multiidioma

**Implementación:**
- Agregadas traducciones completas para 5 idiomas
- Scripts de importación automática (Windows y Linux)
- Documentación completa en `translations/README.md`

**Idiomas soportados:**
- 🇬🇧 Inglés
- 🇪🇸 Español
- 🇫🇷 Francés
- 🇧🇷 Portugués (Brasil)
- 🇯🇵 Japonés

**Commit:** [Pendiente]

---

### Problemas Conocidos

Actualmente no hay problemas conocidos. El módulo está en estado estable.

### Reportar Bugs

Si encuentras un problema:

1. Verifica los logs: `drush watchdog:show --type=admin_notifications --severity=Error`
2. Revisa la consola del navegador (F12)
3. Incluye la siguiente información:
   - Versión de Drupal
   - Versión de PHP
   - Navegador y versión
   - Pasos para reproducir el error
   - Logs relevantes

## Licencia

Este módulo es de código abierto y está disponible bajo la licencia GPL-2.0+

## Soporte

Para reportar problemas o solicitar nuevas funcionalidades, contacta al equipo de desarrollo.

## Créditos

Desarrollado para Drupal 10 con PHP 8.3
