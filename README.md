# Drupal System Notifications

[![Drupal](https://img.shields.io/badge/Drupal-10.x-blue.svg)](https://www.drupal.org)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-GPL--2.0-green.svg)](LICENSE)

Sistema completo de notificaciones administrativas para Drupal 10 que permite enviar alertas en tiempo real y programadas a usuarios con permisos administrativos.

![Screenshot](docs/screenshot-placeholder.png)

## 🌟 Características Principales

### Notificaciones en Tiempo Real (Toast)
- ✅ Aparecen automáticamente sin recargar la página
- ✅ Sistema de polling AJAX optimizado (cada 30 segundos, configurable)
- ✅ Diseño estilo Windows 10/11 moderno
- ✅ Auto-cierre configurable (3-60 segundos)
- ✅ Sonido de notificación opcional
- ✅ 4 posiciones disponibles (esquinas de la pantalla)

### Notificaciones Banner (Programadas)
- ✅ Se muestran en la parte superior de todas las páginas
- ✅ Programación con fecha de inicio y expiración
- ✅ Persistencia: no se vuelven a mostrar al usuario que las cerró
- ✅ Ideal para avisos de mantenimiento, actualizaciones, etc.

### Gestión Completa
- ✅ Panel de administración intuitivo con tabla CRUD
- ✅ 4 niveles de severidad con colores distintivos (Info, Success, Warning, Error)
- ✅ 3 estados: Borrador, Activa, Completada
- ✅ Gestión inteligente de zonas horarias
- ✅ Sistema de permisos granulares
- ✅ Limpieza automática vía cron

### Diseño y Accesibilidad
- ✅ Diseño responsive (móviles, tablets, desktop)
- ✅ Soporte completo ARIA para accesibilidad
- ✅ Respeta preferencias `prefers-reduced-motion`
- ✅ Compatible con todos los temas de Drupal

## 📋 Requisitos del Sistema

- **Drupal**: 10.x o superior
- **PHP**: 8.3 o superior
- **Base de datos**: MySQL/MariaDB o PostgreSQL
- **Módulos Drupal Core**: Database, User

## 📦 Instalación

### Método 1: Instalación Manual (Recomendada para desarrollo)

1. **Descarga o clona el repositorio:**
   ```bash
   cd /ruta/a/tu/drupal/web/modules/custom
   git clone https://github.com/nemesis128/drupal-system-notifications.git admin_notifications
   ```

2. **Habilita el módulo:**
   ```bash
   drush en admin_notifications -y
   drush cr
   ```

### Método 2: Composer (Próximamente en Packagist)

```bash
composer require nemesis128/drupal-system-notifications
drush en admin_notifications -y
drush cr
```

### Verificar instalación

Navega a: `Administrar > Extensiones` y verifica que "Admin Notifications" esté habilitado.

## ⚙️ Configuración Inicial

### 1. Configurar Permisos

Ve a: `Administrar > Personas > Permisos` (`/admin/people/permissions`)

Asigna los siguientes permisos al rol deseado (ej: Administrator):

| Permiso | Descripción |
|---------|-------------|
| **Administrar notificaciones del sistema** | Permite crear, editar y eliminar notificaciones |
| **Ver notificaciones administrativas** | Permite recibir y ver las notificaciones |

**Recomendación:** Solo otorgar "Administrar" a usuarios de confianza.

### 2. Acceder al Panel de Administración

Navega a:
- **Menú:** `Administrar > Configuración > Sistema > Notificaciones Administrativas`
- **URL directa:** `/admin/config/system/admin-notifications`

### 3. Configurar Opciones Avanzadas (Opcional)

Ve a: `/admin/config/system/admin-notifications/settings`

| Configuración | Valor por defecto | Descripción |
|---------------|-------------------|-------------|
| Intervalo de polling | 30000 ms (30 seg) | Frecuencia de verificación de nuevas notificaciones |
| Duración del toast | 10000 ms (10 seg) | Tiempo antes de auto-cerrar un toast |
| Posición del toast | Inferior derecha | Ubicación en pantalla (4 opciones) |
| Sonido habilitado | Sí | Reproducir sonido al aparecer toast |

## 🎯 Uso Básico

### Crear Notificación Toast (Tiempo Real)

Ideal para: Alertas urgentes, avisos inmediatos, errores críticos

1. Ve al panel: `/admin/config/system/admin-notifications`
2. Click en **"Crear Nueva Notificación"**
3. Completa el formulario:
   - **Título**: "Mantenimiento en 15 minutos"
   - **Mensaje**: "El sistema entrará en mantenimiento. Guarda tu trabajo."
   - **Tipo**: Selecciona **"Notificación en tiempo real (Toast)"**
   - **Severidad**: Warning
   - **Estado**: Activa
4. Click en **"Crear"**

**Resultado:** Todos los usuarios conectados verán el toast en ~30 segundos (según intervalo de polling).

### Crear Notificación Banner (Programada)

Ideal para: Avisos de mantenimiento programado, información general, anuncios

1. Ve al panel: `/admin/config/system/admin-notifications`
2. Click en **"Crear Nueva Notificación"**
3. Completa el formulario:
   - **Título**: "Mantenimiento programado"
   - **Mensaje**: "Habrá mantenimiento mañana de 2-4 AM"
   - **Tipo**: Selecciona **"Notificación Banner (Programada)"**
   - **Severidad**: Info
   - **Fecha de inicio**: Hoy a las 00:00
   - **Fecha de expiración**: Mañana a las 04:00
   - **Estado**: Activa
4. Click en **"Crear"**

**Resultado:** El banner aparecerá en la parte superior de todas las páginas entre esas fechas.

## 🌍 Gestión de Zonas Horarias

El sistema maneja zonas horarias de forma inteligente:

### Cómo Funciona

1. **Entrada de fechas**: Se muestran en la zona horaria del usuario que crea la notificación
2. **Almacenamiento**: Se guardan como timestamp UTC en la base de datos
3. **Visualización**: Se comparan con UTC, por lo que aparecen **al mismo instante** para todos los usuarios

### Ejemplo Práctico

- **Admin en Ciudad de México (UTC-6)** programa para las 4:00 PM
- **Usuario en Nueva York (UTC-5)** la ve a las 5:00 PM de su reloj
- **Usuario en Los Ángeles (UTC-8)** la ve a las 2:00 PM de su reloj
- ✅ **Todos la ven AL MISMO INSTANTE de tiempo real**

Este comportamiento es el esperado para notificaciones que deben sincronizarse globalmente.

## 🎨 Niveles de Severidad

| Severidad | Color | Uso Recomendado |
|-----------|-------|-----------------|
| **Info** | Azul | Información general, anuncios |
| **Success** | Verde | Confirmaciones, acciones exitosas |
| **Warning** | Naranja | Advertencias, precauciones |
| **Error** | Rojo | Errores críticos, problemas urgentes |

## 📊 Estructura de la Base de Datos

El módulo crea 2 tablas:

### `admin_notifications`
Almacena las notificaciones:
- `id`: ID único
- `title`: Título de la notificación
- `message`: Mensaje (texto)
- `type`: 'realtime' o 'banner'
- `severity`: 'info', 'success', 'warning', 'error'
- `status`: 'draft', 'active', 'completed'
- `start_date`: Timestamp de inicio
- `end_date`: Timestamp de expiración (NULL si no expira)
- `created`, `created_by`, `updated`

### `admin_notifications_read`
Rastrea qué usuarios han visto cada notificación:
- `id`: ID único
- `notification_id`: Referencia a la notificación
- `uid`: ID del usuario
- `read_timestamp`: Cuándo la leyó

## 🔧 Personalización

### Cambiar Colores de Severidad

Sobrescribe en el CSS de tu tema:

```css
/* Toast - Cambiar color de error */
.toast-notification--error::before {
  background-color: #your-color;
}

/* Banner - Cambiar color de warning */
.admin-notification-banner--warning {
  background-color: #your-bg-color;
  border-left-color: #your-border-color;
}
```

### Modificar Templates

1. Copia `templates/admin-notification-banner.html.twig` a tu tema
2. Personaliza según necesites
3. Limpia caché: `drush cr`

### Cambiar Posición de Toasts

Ve a: `/admin/config/system/admin-notifications/settings`

O programáticamente:

```php
\Drupal::configFactory()
  ->getEditable('admin_notifications.settings')
  ->set('toast_position', 'top-right')
  ->save();
```

## 💻 Uso Programático

### Crear Notificación desde Código

```php
$database = \Drupal::database();
$current_time = \Drupal::time()->getRequestTime();

// Notificación Toast en tiempo real
$notification_id = $database->insert('admin_notifications')
  ->fields([
    'title' => 'Error crítico detectado',
    'message' => 'Se detectó un problema en el sistema de archivos.',
    'type' => 'realtime',
    'severity' => 'error',
    'status' => 'active',
    'start_date' => $current_time,
    'created' => $current_time,
    'created_by' => \Drupal::currentUser()->id(),
    'updated' => $current_time,
  ])
  ->execute();

// Notificar al sistema de polling
\Drupal::state()->set('admin_notifications.new_notification', [
  'id' => $notification_id,
  'timestamp' => $current_time,
]);
```

### Crear Notificación Banner

```php
$database = \Drupal::database();
$current_time = \Drupal::time()->getRequestTime();

$notification_id = $database->insert('admin_notifications')
  ->fields([
    'title' => 'Mantenimiento programado',
    'message' => 'El sistema estará en mantenimiento el próximo lunes.',
    'type' => 'banner',
    'severity' => 'warning',
    'status' => 'active',
    'start_date' => strtotime('next monday 2am'),
    'end_date' => strtotime('next monday 4am'),
    'created' => $current_time,
    'created_by' => \Drupal::currentUser()->id(),
    'updated' => $current_time,
  ])
  ->execute();
```

## 🐛 Solución de Problemas

### Las notificaciones toast no aparecen

**Posibles causas:**

1. **Permisos incorrectos**
   - Verifica que el usuario tenga el permiso "Ver notificaciones administrativas"

2. **Notificación en estado incorrecto**
   - Verifica que el estado sea "Activa", no "Borrador"

3. **Error en JavaScript**
   - Abre la consola del navegador (F12)
   - Busca errores en rojo
   - Verifica que `drupalSettings.adminNotifications` esté definido

4. **Caché**
   - Limpia la caché: `drush cr`
   - Recarga con Ctrl+Shift+R

### Los banners no se muestran

**Posibles causas:**

1. **Fechas incorrectas**
   - Verifica que `start_date` sea anterior al momento actual
   - Verifica que `end_date` (si existe) sea posterior al momento actual

2. **Ya fue cerrado**
   - Si el usuario ya cerró ese banner, no volverá a verlo
   - Verifica en la tabla `admin_notifications_read`

3. **Tipo incorrecto**
   - Verifica que el tipo sea 'banner', no 'realtime'

### Ver logs de debug

```bash
# Ver logs recientes
drush watchdog:show --count=20

# Ver solo errores
drush watchdog:show --severity=Error

# Ver logs específicos de PHP
drush watchdog:show --type=php
```

## 🔒 Seguridad

- ✅ Todos los inputs son sanitizados con `htmlspecialchars()` / `escapeHtml()`
- ✅ Sistema de permisos granulares
- ✅ Protección contra XSS en templates
- ✅ Validación de fechas y datos en formularios
- ✅ Solo usuarios autorizados pueden crear notificaciones

## 🧪 Testing

### Prueba Manual - Toast

1. Crea una notificación Toast con estado "Activa"
2. Abre otra pestaña del sitio (mismo usuario)
3. Espera máximo 30 segundos
4. Debería aparecer el toast en la esquina configurada

### Prueba Manual - Banner

1. Crea una notificación Banner con fecha de inicio = ahora
2. Recarga cualquier página del sitio
3. Debería aparecer el banner en la parte superior
4. Click en X para cerrar
5. Recarga la página → el banner NO debe volver a aparecer

### Prueba de Consola del Navegador

```javascript
// Ver configuración
console.log(drupalSettings.adminNotifications);

// Mostrar toast de prueba
Drupal.toastNotifications.show(
  'Prueba',
  'Este es un test',
  'success'
);
```

## 📚 Documentación Adicional

- [GUIA_VISUAL.md](GUIA_VISUAL.md) - Screenshots y ejemplos visuales paso a paso
- [CHECKLIST.md](CHECKLIST.md) - Lista de verificación completa para instalación
- [RESUMEN_INSTALACION.md](RESUMEN_INSTALACION.md) - Documentación técnica detallada

## 🗺️ Roadmap

Características planeadas para futuras versiones:

- [ ] Soporte para WebSocket/Mercure (notificaciones verdaderamente push)
- [ ] Filtrado de notificaciones por roles específicos
- [ ] Plantillas de notificaciones reutilizables
- [ ] Estadísticas de visualización y engagement
- [ ] Notificaciones recurrentes (diarias, semanales, mensuales)
- [ ] Integración con sistema de emails
- [ ] API REST para aplicaciones externas
- [ ] Internacionalización (i18n) completa
- [ ] Soporte para adjuntos e imágenes

## 🤝 Contribuir

¡Las contribuciones son bienvenidas! Para contribuir:

1. Fork el proyecto
2. Crea una rama para tu feature:
   ```bash
   git checkout -b feature/NuevaCaracteristica
   ```
3. Commit tus cambios:
   ```bash
   git commit -m 'feat: agregar nueva característica'
   ```
4. Push a la rama:
   ```bash
   git push origin feature/NuevaCaracteristica
   ```
5. Abre un Pull Request

### Guía de Estilo

- Seguir los estándares de código de Drupal
- Documentar todos los métodos públicos
- Incluir pruebas cuando sea posible
- Actualizar el README si agregas nuevas características

## 📄 Licencia

Este proyecto está licenciado bajo GPL-2.0-or-later. Ver el archivo [LICENSE](LICENSE) para más detalles.

## 👥 Autor

**nemesis128**
- GitHub: [@nemesis128](https://github.com/nemesis128)
- Email: contacto@example.com

## 🙏 Agradecimientos

- Comunidad de Drupal por los recursos y documentación
- Claude Code por asistencia en el desarrollo
- Todos los contribuidores y testers

## 📞 Soporte

- **Issues**: [GitHub Issues](https://github.com/nemesis128/drupal-system-notifications/issues)
- **Documentación**: [Wiki del proyecto](https://github.com/nemesis128/drupal-system-notifications/wiki)
- **Email**: support@example.com

## 📈 Estadísticas del Proyecto

- **Versión actual**: 1.0.0
- **Líneas de código**: ~2,500+
- **Archivos PHP**: 7
- **Archivos JavaScript**: 2
- **Archivos CSS**: 3
- **Templates Twig**: 1

---

**¿Te gusta este módulo? ¡Dale una ⭐ en GitHub!**

Made with ❤️ for the Drupal community
