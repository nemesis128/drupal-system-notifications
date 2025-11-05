# Registro de Logs del Módulo Admin Notifications

El módulo Admin Notifications utiliza el sistema de logs de Drupal (Watchdog/dblog) para registrar eventos importantes, errores y advertencias del lado del servidor.

## 📊 Tipos de Logs Registrados

### 🔴 Errores (Error)

Eventos críticos que requieren atención inmediata:

- **Error en polling endpoint**: Ocurre cuando hay una excepción durante la consulta de notificaciones
  ```
  Error in polling endpoint: [mensaje de error]
  ```

- **Error al marcar como leída**: Fallo al intentar marcar una notificación como leída
  ```
  Error marking notification [ID] as read: [mensaje de error]
  ```

- **Error en limpieza de cron**: Problema durante la ejecución del cron
  ```
  Error during cron cleanup: [mensaje de error]
  ```

### ⚠️ Advertencias (Warning)

Eventos que no son críticos pero deben ser monitoreados:

- **Acceso denegado al polling**: Usuario sin permisos intenta acceder al endpoint
  ```
  Polling access denied for user [UID]
  ```

- **Acceso denegado a mark-read**: Usuario sin permisos intenta marcar como leída
  ```
  Mark read access denied for user [UID]
  ```

- **Notificación no encontrada**: Intento de marcar como leída una notificación inexistente
  ```
  Attempted to mark non-existent notification [ID] as read
  ```

### ℹ️ Información (Info)

Eventos normales del sistema:

- **Registros leídos eliminados**: Limpieza exitosa de registros antiguos
  ```
  Cron: Deleted [N] old read notification records
  ```

- **Notificaciones expiradas eliminadas**: Limpieza exitosa de notificaciones vencidas
  ```
  Cron: Deleted [N] expired notifications
  ```

---

## 🔍 Cómo Ver los Logs

### Opción 1: Interfaz Web (dblog)

1. Ve a **Informes → Registros recientes** (`/admin/reports/dblog`)
2. Filtra por tipo: **admin_notifications**
3. Podrás ver todos los eventos ordenados por fecha

### Opción 2: Usando Drush

**Ver todos los logs del módulo:**
```bash
drush watchdog:show --type=admin_notifications
```

**Ver solo errores:**
```bash
drush watchdog:show --type=admin_notifications --severity=Error
```

**Ver logs en tiempo real (tail):**
```bash
drush watchdog:tail --type=admin_notifications
```

**Ver últimos 50 eventos:**
```bash
drush watchdog:show --type=admin_notifications --count=50
```

**Ver con formato extendido:**
```bash
drush watchdog:show --type=admin_notifications --extended
```

### Opción 3: Consulta SQL Directa

**Ver todos los logs del módulo:**
```bash
drush sqlq "SELECT * FROM watchdog WHERE type = 'admin_notifications' ORDER BY wid DESC LIMIT 20"
```

**Ver solo errores:**
```bash
drush sqlq "SELECT * FROM watchdog WHERE type = 'admin_notifications' AND severity <= 3 ORDER BY wid DESC"
```

---

## 📈 Monitoreo Recomendado

### Para Producción

1. **Configurar alertas para errores críticos**
   - Monitorear logs de severidad Error (3) y Alert (1)
   - Configurar notificaciones por email o Slack

2. **Revisar logs semanalmente**
   - Verificar warnings de acceso denegado
   - Identificar patrones de errores

3. **Limpieza periódica**
   - Los logs se limpian automáticamente según la configuración de dblog
   - Por defecto Drupal mantiene los últimos 1000 registros

### Para Desarrollo

**Habilitar logging detallado:**
```bash
# Ver logs en tiempo real durante desarrollo
drush watchdog:tail
```

**Limpiar logs antiguos:**
```bash
drush watchdog:delete all
```

---

## 🛠️ Severidades de Drupal

El módulo usa las siguientes severidades estándar de Drupal:

| Nivel | Nombre | Descripción | Uso en Admin Notifications |
|-------|--------|-------------|---------------------------|
| 0 | Emergency | Sistema inutilizable | No usado |
| 1 | Alert | Acción inmediata requerida | No usado |
| 2 | Critical | Condiciones críticas | No usado |
| 3 | Error | Errores que requieren atención | Excepciones, fallos de BD |
| 4 | Warning | Advertencias | Acceso denegado, notif no encontrada |
| 5 | Notice | Normal pero significativo | No usado |
| 6 | Info | Mensajes informativos | Limpieza de cron exitosa |
| 7 | Debug | Información de debug | No usado |

---

## 📝 Ejemplos de Uso

### Verificar si hay errores recientes

```bash
drush watchdog:show --type=admin_notifications --severity=Error --count=10
```

### Monitorear problemas de permisos

```bash
drush watchdog:show --type=admin_notifications --severity=Warning | grep "access denied"
```

### Ver actividad del cron

```bash
drush watchdog:show --type=admin_notifications | grep "Cron:"
```

### Exportar logs a archivo

```bash
drush watchdog:show --type=admin_notifications --format=json > admin_notifications_logs.json
```

---

## 🔐 Seguridad y Privacidad

- Los logs **NO** contienen información sensible de usuarios
- Solo se registran IDs de usuario, no datos personales
- Los mensajes de notificaciones **NO** se registran en logs (solo IDs)
- Los logs son accesibles solo para usuarios con permiso "access site reports"

---

## 💡 Tips para Debugging

1. **Problema: Las notificaciones no aparecen**
   - Revisar logs de polling para ver si hay errores
   - Verificar que no haya warnings de "access denied"

2. **Problema: Cron no limpia notificaciones**
   - Buscar errores en logs con grep "Cron"
   - Verificar que el cron se esté ejecutando

3. **Problema: Errores intermitentes**
   - Usar `drush watchdog:tail` en tiempo real
   - Reproducir el problema y observar los logs

---

## 📚 Referencias

- [Drupal Logging API](https://api.drupal.org/api/drupal/core%21core.api.php/group/logging)
- [Watchdog Severity Levels](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Logger%21RfcLogLevel.php)
- [Drush Watchdog Commands](https://www.drush.org/latest/commands/watchdog_show/)
