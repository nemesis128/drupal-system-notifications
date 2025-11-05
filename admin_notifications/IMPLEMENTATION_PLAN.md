# Plan de Implementación - Admin Notifications para Drupal.org

## Índice
1. [Análisis del Estado Actual](#análisis-del-estado-actual)
2. [Plan de Implementación por Etapas](#plan-de-implementación-por-etapas)
3. [Lista de Verificación Final](#lista-de-verificación-final)
4. [Proceso de Publicación](#proceso-de-publicación)

---

## Análisis del Estado Actual

### ✅ Lo que Ya Cumple con los Requisitos

#### Estructura Básica
- ✅ Estructura de módulo válida con `.info.yml`
- ✅ Namespace PSR-4 correcto (`Drupal\admin_notifications`)
- ✅ Uso de Dependency Injection en Controllers
- ✅ Uso correcto de Drupal Database API con queries parametrizadas
- ✅ Twig templates con auto-escaping
- ✅ Implementación de Drupal.behaviors para JavaScript
- ✅ Sistema de permisos implementado

#### Funcionalidad
- ✅ Sistema de notificaciones tipo toast funcional
- ✅ Sistema de notificaciones tipo banner funcional
- ✅ Polling en tiempo real implementado
- ✅ Sistema de marcado de leídas/no leídas
- ✅ Configuración administrativa completa
- ✅ Sistema de roles y permisos

#### Seguridad Básica
- ✅ Queries parametrizadas (no concatenación SQL)
- ✅ Verificación de permisos en controladores
- ✅ Uso de `\Drupal::csrfToken()` en formularios
- ✅ Auto-escaping en Twig templates

#### Documentación Básica
- ✅ README.md existente con información del módulo
- ✅ Comentarios docblock en PHP
- ✅ Descripción en `.info.yml`

### ❌ Lo que Necesita Implementación/Corrección

#### Validación de Código
- ❌ No validado contra Drupal Coding Standards (phpcs)
- ❌ No validado contra Drupal deprecations (drupal-check)
- ❌ No validado contra análisis estático (phpstan)
- ❌ Posibles errores de coding standards en indentación, nombres, etc.

#### Configuración de Proyecto
- ❌ No existe `composer.json` propio del módulo
- ❌ No existe `.gitignore` adecuado
- ❌ No existe `LICENSE.txt` (GPL-2.0-or-later)
- ❌ No existe `.gitlab-ci.yml` para CI/CD

#### Testing
- ❌ No existen Unit Tests
- ❌ No existen Kernel Tests
- ❌ No existen Functional Tests
- ❌ No existe directorio `tests/`

#### Documentación
- ❌ README.md no sigue formato estándar de Drupal.org
- ❌ Falta `CHANGELOG.md` con semantic versioning
- ❌ Falta documentación de API para desarrolladores
- ❌ Falta guía de contribución (`CONTRIBUTING.md`)
- ❌ No existe `help/` con páginas de ayuda integradas

#### Seguridad Avanzada
- ❌ No validado contra inyección XSS en todos los puntos
- ❌ No revisado rate limiting en endpoints AJAX
- ❌ No revisado CSRF en todas las acciones
- ❌ No auditada la validación de entrada en formularios

#### Rendimiento
- ❌ No optimizado para sitios con muchas notificaciones
- ❌ No implementado lazy loading en listados
- ❌ No implementada paginación en consultas grandes
- ❌ No optimizados índices de base de datos

#### Accesibilidad
- ❌ No validado contra WCAG 2.1 AA
- ❌ No validado con lectores de pantalla
- ❌ No revisado orden de tabulación
- ❌ No validados atributos ARIA

---

## Plan de Implementación por Etapas

### Etapa 1: Validación y Corrección de Código (Crítico)
**Duración estimada:** 4-6 horas
**Objetivo:** Asegurar que el código cumple con Drupal Coding Standards

#### Tareas:

##### 1.1. Instalar Herramientas de Validación
```bash
# Instalar Coder (incluye phpcs con Drupal standards)
composer require --dev drupal/coder

# Registrar Drupal coding standards
vendor/bin/phpcs --config-set installed_paths vendor/drupal/coder/coder_sniffer

# Instalar Drupal Check
composer require --dev mglaman/drupal-check

# Instalar PHPStan con extensión Drupal
composer require --dev phpstan/phpstan phpstan/extension-installer mglaman/phpstan-drupal
```

##### 1.2. Ejecutar Validaciones
```bash
# Validar coding standards
vendor/bin/phpcs --standard=Drupal,DrupalPractice --extensions=php,module,inc,install,test,profile,theme,info,txt,md,yml web/modules/custom/admin_notifications

# Validar deprecations
vendor/bin/drupal-check web/modules/custom/admin_notifications

# Análisis estático
vendor/bin/phpstan analyse web/modules/custom/admin_notifications
```

##### 1.3. Corregir Errores Identificados

**Errores Comunes Esperados:**
- Indentación incorrecta (2 espacios, no 4)
- Falta de línea en blanco al final de archivos
- Comentarios no siguiendo formato Drupal
- Uso de sintaxis deprecated
- Falta de type hints en PHP 7.4+

**Archivos Probablemente Afectados:**
- Todos los archivos `.php`
- Todos los archivos `.module`
- Archivos YAML
- Archivos JavaScript (usar ESLint)

**Corrección Automática:**
```bash
# Corregir automáticamente lo que sea posible
vendor/bin/phpcbf --standard=Drupal,DrupalPractice --extensions=php,module,inc,install web/modules/custom/admin_notifications
```

**Ejemplo de Correcciones Típicas:**

Antes:
```php
    public function poll(Request $request)
    {
        $user = $this->currentUser();
        // ...
    }
```

Después:
```php
  /**
   * Endpoint de polling para verificar nuevas notificaciones.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   La petición HTTP.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Respuesta JSON con las notificaciones.
   */
  public function poll(Request $request) {
    $user = $this->currentUser();
    // ...
  }
```

##### 1.4. Validar JavaScript con ESLint
```bash
# Instalar ESLint con configuración de Drupal
npm init -y
npm install --save-dev eslint eslint-config-drupal

# Crear .eslintrc.json
```

**.eslintrc.json:**
```json
{
  "extends": "eslint-config-drupal",
  "env": {
    "browser": true
  },
  "globals": {
    "Drupal": true,
    "drupalSettings": true,
    "jQuery": true
  }
}
```

```bash
# Validar JavaScript
npx eslint js/
```

**Correcciones Típicas en JavaScript:**
- Usar `'use strict';`
- Declarar variables con `const`/`let` en lugar de `var`
- Seguir convenciones de nombres de Drupal
- Agregar comentarios JSDoc

##### 1.5. Criterios de Éxito
- ✅ `phpcs` no reporta errores
- ✅ `drupal-check` no reporta deprecations
- ✅ `phpstan` nivel 1 sin errores
- ✅ ESLint sin errores en JavaScript

##### 1.6. Commit
```bash
git add .
git commit -m "Issue #0: Aplicar Drupal Coding Standards

- Corregir indentación a 2 espacios
- Agregar type hints faltantes
- Mejorar comentarios docblock
- Corregir deprecations identificados
- Aplicar ESLint a archivos JavaScript"
```

---

### Etapa 2: Archivos de Configuración del Proyecto (Crítico)
**Duración estimada:** 2-3 horas
**Objetivo:** Crear archivos esenciales para el proyecto

#### Tareas:

##### 2.1. Crear composer.json del Módulo

**Archivo: `composer.json`**
```json
{
  "name": "drupal/admin_notifications",
  "description": "Sistema profesional de notificaciones administrativas para Drupal con soporte para toast, banners y notificaciones en tiempo real.",
  "type": "drupal-module",
  "license": "GPL-2.0-or-later",
  "homepage": "https://www.drupal.org/project/admin_notifications",
  "authors": [
    {
      "name": "Tu Nombre",
      "homepage": "https://www.drupal.org/u/tu-usuario",
      "role": "Maintainer"
    }
  ],
  "support": {
    "issues": "https://www.drupal.org/project/issues/admin_notifications",
    "source": "https://git.drupalcode.org/project/admin_notifications"
  },
  "require": {
    "php": ">=7.4",
    "drupal/core": "^9.3 || ^10"
  },
  "require-dev": {
    "drupal/coder": "^8.3",
    "mglaman/drupal-check": "^1.4",
    "phpstan/phpstan": "^1.10",
    "mglaman/phpstan-drupal": "^1.2"
  },
  "minimum-stability": "dev",
  "prefer-stable": true
}
```

##### 2.2. Crear LICENSE.txt

**Archivo: `LICENSE.txt`**
```text
GNU GENERAL PUBLIC LICENSE
Version 2, June 1991

Copyright (C) 1989, 1991 Free Software Foundation, Inc.
51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA

Everyone is permitted to copy and distribute verbatim copies
of this license document, but changing it is not allowed.

[Copiar el texto completo de GPL-2.0 desde: https://www.gnu.org/licenses/old-licenses/gpl-2.0.txt]

---

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
```

##### 2.3. Crear .gitignore

**Archivo: `.gitignore`**
```gitignore
# Ignorar archivos de dependencias
/vendor/
/node_modules/

# Ignorar archivos de configuración local
/.idea/
/.vscode/
*.swp
*.swo
*~

# Ignorar archivos de sistema
.DS_Store
Thumbs.db

# Ignorar logs
*.log

# Ignorar archivos temporales
/tmp/
/temp/

# Ignorar archivos de coverage
/coverage/
/.phpunit.result.cache
```

##### 2.4. Crear CHANGELOG.md

**Archivo: `CHANGELOG.md`**
```markdown
# Changelog

Todos los cambios notables en este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto adhiere a [Semantic Versioning](https://semver.org/lang/es/).

## [Unreleased]

### Preparando para 1.0.0
- Validación completa contra Drupal Coding Standards
- Implementación de suite de tests
- Documentación completa
- Revisión de seguridad

## [0.1.0] - 2025-01-XX

### Añadido
- Sistema de notificaciones tipo toast (estilo Windows 10/11)
- Sistema de notificaciones tipo banner programables
- Polling en tiempo real cada 30 segundos
- Configuración de duración, posición y sonido de notificaciones
- Sistema de roles y permisos granulares
- Panel administrativo para gestión de notificaciones
- Soporte multiidioma (Español, Inglés, Francés, Portugués, Japonés)
- Sistema de marcado de notificaciones leídas/no leídas
- Logging con Drupal Watchdog

### Corregido
- Visibilidad del botón de eliminar (texto blanco sobre fondo rojo)
- Duración de toast respetando configuración (30 segundos)
- Sistema de polling ejecutándose correctamente cada 30 segundos
- Botón de cerrar visible en banners
- Ancho de banners sin causar scroll horizontal

### Seguridad
- Implementación de queries parametrizadas
- Verificación de permisos en todos los endpoints
- Auto-escaping en templates Twig
- Protección CSRF en formularios

[Unreleased]: https://git.drupalcode.org/project/admin_notifications/-/compare/0.1.0...HEAD
[0.1.0]: https://git.drupalcode.org/project/admin_notifications/-/tags/0.1.0
```

##### 2.5. Actualizar admin_notifications.info.yml

**Archivo: `admin_notifications.info.yml`**
```yaml
name: 'Admin Notifications'
type: module
description: 'Sistema profesional de notificaciones administrativas con soporte para toast, banners y notificaciones en tiempo real.'
package: Administration
core_version_requirement: ^9.3 || ^10

# Información del proyecto
project: 'admin_notifications'
version: '1.0.0-alpha1'

# Dependencias
dependencies:
  - drupal:system (>=9.3)
  - drupal:user

# Configuración
configure: admin_notifications.settings

# Página de ayuda
help: admin_notifications.help
```

##### 2.6. Criterios de Éxito
- ✅ `composer.json` válido (`composer validate`)
- ✅ `LICENSE.txt` con GPL-2.0-or-later completo
- ✅ `.gitignore` apropiado
- ✅ `CHANGELOG.md` siguiendo Keep a Changelog
- ✅ `.info.yml` con `core_version_requirement` correcto

##### 2.7. Commit
```bash
git add composer.json LICENSE.txt .gitignore CHANGELOG.md admin_notifications.info.yml
git commit -m "Issue #0: Agregar archivos de configuración del proyecto

- Agregar composer.json con dependencias
- Agregar LICENSE.txt (GPL-2.0-or-later)
- Agregar .gitignore
- Agregar CHANGELOG.md con historial de versiones
- Actualizar .info.yml con información del proyecto"
```

---

### Etapa 3: Mejorar Documentación (Importante)
**Duración estimada:** 3-4 horas
**Objetivo:** Crear documentación completa siguiendo estándares de Drupal.org

#### Tareas:

##### 3.1. Actualizar README.md Siguiendo Formato Drupal.org

**Archivo: `README.md`**
```markdown
# Admin Notifications

Sistema profesional de notificaciones administrativas para Drupal 10 con soporte
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
```

### Instalación Manual

1. Descargar el módulo desde la página del proyecto
2. Extraer en `modules/contrib/admin_notifications`
3. Habilitar en `admin/modules` o con drush:
   ```bash
   drush en admin_notifications
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

#### Crear Notificación Programáticamente

```php
use Drupal\admin_notifications\Service\AdminNotificationService;

// Obtener el servicio
$notification_service = \Drupal::service('admin_notifications.notification');

// Crear notificación toast
$notification_service->createNotification([
  'title' => 'Nueva actualización',
  'message' => 'El sistema ha sido actualizado exitosamente.',
  'severity' => 'success',
  'type' => 'realtime',
]);

// Crear notificación banner programada
$notification_service->createNotification([
  'title' => 'Mantenimiento programado',
  'message' => 'El sistema estará en mantenimiento mañana de 2-4 AM.',
  'severity' => 'warning',
  'type' => 'scheduled',
  'start_date' => strtotime('tomorrow'),
  'end_date' => strtotime('+7 days'),
]);
```

#### Escuchar Eventos de Notificación

```php
use Drupal\Core\EventSubscriber\EventSubscriberInterface;
use Drupal\admin_notifications\Event\NotificationCreatedEvent;

class MyModuleNotificationSubscriber implements EventSubscriberInterface {

  public static function getSubscribedEvents() {
    return [
      NotificationCreatedEvent::EVENT_NAME => 'onNotificationCreated',
    ];
  }

  public function onNotificationCreated(NotificationCreatedEvent $event) {
    $notification = $event->getNotification();
    // Tu lógica personalizada aquí
  }

}
```

#### Hooks Disponibles

```php
/**
 * Implements hook_admin_notifications_display_alter().
 */
function mymodule_admin_notifications_display_alter(&$notifications) {
  foreach ($notifications as &$notification) {
    // Modificar notificaciones antes de mostrar
    if ($notification['severity'] === 'error') {
      $notification['message'] = t('CRÍTICO: @message', [
        '@message' => $notification['message'],
      ]);
    }
  }
}
```


## API para Desarrolladores

### Servicio Principal

**Servicio:** `admin_notifications.notification`

**Clase:** `Drupal\admin_notifications\Service\AdminNotificationService`

**Métodos:**
- `createNotification(array $data)` - Crear notificación
- `getActiveNotifications($type)` - Obtener notificaciones activas
- `markAsRead($notification_id, $user_id)` - Marcar como leída
- `deleteNotification($notification_id)` - Eliminar notificación

### Rutas API

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


## Mantenedores

- **[Tu Nombre]** - [tu.email@example.com](mailto:tu.email@example.com)

### Mantenedores Anteriores

- N/A


## Contribuyendo

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
```

##### 3.2. Crear CONTRIBUTING.md

**Archivo: `CONTRIBUTING.md`**
```markdown
# Guía de Contribución

Gracias por tu interés en contribuir a Admin Notifications.

## Cómo Contribuir

### Reportar Bugs

1. Verificar que el bug no haya sido reportado en la [cola de issues](https://www.drupal.org/project/issues/admin_notifications)
2. Crear un nuevo issue con:
   - Descripción clara del problema
   - Pasos para reproducir
   - Comportamiento esperado vs. actual
   - Versión de Drupal y PHP
   - Screenshots si aplica

### Solicitar Funcionalidades

1. Verificar que no exista una solicitud similar
2. Crear un issue con tag "Feature request"
3. Explicar:
   - Caso de uso
   - Beneficio para la comunidad
   - Posible implementación

### Enviar Código

#### Prerrequisitos

- Familiaridad con [Drupal Coding Standards](https://www.drupal.org/docs/develop/standards)
- Cuenta en Drupal.org
- Git configurado con tu cuenta

#### Proceso

1. **Fork o Branch:**
   ```bash
   git checkout -b issue-123-descripcion
   ```

2. **Desarrollar:**
   - Seguir Drupal Coding Standards
   - Agregar/actualizar tests
   - Documentar cambios en CHANGELOG.md

3. **Validar Código:**
   ```bash
   # Coding standards
   vendor/bin/phpcs --standard=Drupal,DrupalPractice .

   # Deprecations
   vendor/bin/drupal-check .

   # Tests
   vendor/bin/phpunit
   ```

4. **Commit:**
   ```bash
   git commit -m "Issue #123: Descripción breve

   Descripción detallada de los cambios realizados."
   ```

5. **Crear Patch:**
   ```bash
   git diff > 123-descripcion.patch
   ```

6. **Subir a Drupal.org:**
   - Adjuntar patch al issue
   - Cambiar estado a "Needs review"

#### Estándares de Código

**PHP:**
- Indentación: 2 espacios
- Línea máxima: 80 caracteres
- Docblocks completos
- Type hints en PHP 7.4+

**JavaScript:**
- ESLint con config de Drupal
- Uso de `'use strict';`
- Comentarios JSDoc

**CSS:**
- Seguir [CSS Coding Standards](https://www.drupal.org/docs/develop/standards/css)
- BEM naming cuando aplique

#### Tests

Toda nueva funcionalidad debe incluir:
- Unit tests para lógica de negocio
- Kernel tests para integraciones
- Functional tests para UI

```bash
# Ejecutar tests
vendor/bin/phpunit --group admin_notifications
```

### Revisión de Código

Los maintainers revisarán el código considerando:
- Funcionamiento correcto
- Cumplimiento de standards
- Tests adecuados
- Documentación actualizada
- Compatibilidad con versiones soportadas

### Comunicación

- **Issues:** Para bugs y features
- **Slack:** #admin-notifications en Drupal Slack
- **IRC:** #drupal-contribute en Libera.Chat

## Estándares de Commit

Formato:
```
Issue #[número]: [Descripción breve en imperativo]

[Descripción detallada del cambio, el por qué y cualquier
consideración importante]

[Referencias adicionales si aplican]
```

Ejemplos:
```
Issue #123: Fix toast duration configuration

La configuración de duración no se pasaba correctamente desde PHP
a JavaScript. Ahora se incluye en drupalSettings.

Issue #456: Add pagination to notifications list

Para sitios con muchas notificaciones, la lista se cargaba lentamente.
Se implementa paginación con 50 items por página.

Related to #450.
```

## Código de Conducta

Este proyecto sigue el [Código de Conducta de Drupal](https://www.drupal.org/dcoc).

## Licencia

Al contribuir, aceptas que tu código será licenciado bajo GPL-2.0-or-later.

## Preguntas

Para preguntas, contactar a los maintainers o preguntar en la cola de issues.
```

##### 3.3. Crear Documentación de API

**Archivo: `API.md`**
```markdown
# Admin Notifications - Documentación de API

## Servicios

### admin_notifications.notification

Servicio principal para gestión de notificaciones.

**Clase:** `Drupal\admin_notifications\Service\AdminNotificationService`

#### Métodos

##### createNotification()
```php
/**
 * Crea una nueva notificación.
 *
 * @param array $data
 *   Array asociativo con:
 *   - title: (string) Título de la notificación
 *   - message: (string) Mensaje
 *   - severity: (string) 'info'|'success'|'warning'|'error'
 *   - type: (string) 'realtime'|'scheduled'
 *   - start_date: (int) Timestamp inicio (solo scheduled)
 *   - end_date: (int) Timestamp fin (solo scheduled)
 *   - target_roles: (array) IDs de roles (opcional)
 *
 * @return int|false
 *   ID de la notificación creada o FALSE en error.
 */
public function createNotification(array $data);
```

**Ejemplo:**
```php
$service = \Drupal::service('admin_notifications.notification');
$id = $service->createNotification([
  'title' => 'Mantenimiento programado',
  'message' => 'El sistema estará en mantenimiento mañana.',
  'severity' => 'warning',
  'type' => 'scheduled',
  'start_date' => strtotime('tomorrow'),
  'end_date' => strtotime('+1 day'),
]);
```

##### getActiveNotifications()
```php
/**
 * Obtiene notificaciones activas.
 *
 * @param string $type
 *   Tipo: 'realtime', 'scheduled' o NULL para todos.
 *
 * @return array
 *   Array de objetos de notificación.
 */
public function getActiveNotifications($type = NULL);
```

##### markAsRead()
```php
/**
 * Marca notificación como leída por un usuario.
 *
 * @param int $notification_id
 *   ID de la notificación.
 * @param int $user_id
 *   ID del usuario.
 *
 * @return bool
 *   TRUE si se marcó, FALSE en error.
 */
public function markAsRead($notification_id, $user_id);
```

##### deleteNotification()
```php
/**
 * Elimina una notificación.
 *
 * @param int $notification_id
 *   ID de la notificación.
 *
 * @return bool
 *   TRUE si se eliminó, FALSE en error.
 */
public function deleteNotification($notification_id);
```

## Hooks

### hook_admin_notifications_display_alter()

Modifica notificaciones antes de mostrarlas.

```php
/**
 * Implements hook_admin_notifications_display_alter().
 *
 * @param array $notifications
 *   Array de notificaciones por referencia.
 */
function mymodule_admin_notifications_display_alter(&$notifications) {
  foreach ($notifications as &$notification) {
    // Agregar prefijo a notificaciones críticas
    if ($notification['severity'] === 'error') {
      $notification['message'] = '🚨 ' . $notification['message'];
    }
  }
}
```

### hook_admin_notifications_access_alter()

Modifica el acceso a ver notificaciones.

```php
/**
 * Implements hook_admin_notifications_access_alter().
 *
 * @param bool $access
 *   Acceso por referencia.
 * @param object $notification
 *   Objeto de notificación.
 * @param object $account
 *   Cuenta de usuario.
 */
function mymodule_admin_notifications_access_alter(&$access, $notification, $account) {
  // Permitir a editores ver notificaciones de contenido
  if ($notification->category === 'content' && $account->hasRole('editor')) {
    $access = TRUE;
  }
}
```

## Eventos

### NotificationCreatedEvent

Disparado cuando se crea una notificación.

**Clase:** `Drupal\admin_notifications\Event\NotificationCreatedEvent`

**Evento:** `admin_notifications.notification_created`

```php
use Drupal\Core\EventSubscriber\EventSubscriberInterface;
use Drupal\admin_notifications\Event\NotificationCreatedEvent;

class MySubscriber implements EventSubscriberInterface {

  public static function getSubscribedEvents() {
    return [
      NotificationCreatedEvent::EVENT_NAME => ['onCreated', 100],
    ];
  }

  public function onCreated(NotificationCreatedEvent $event) {
    $notification = $event->getNotification();

    // Enviar email para notificaciones críticas
    if ($notification['severity'] === 'error') {
      \Drupal::service('plugin.manager.mail')->mail(
        'mymodule',
        'critical_notification',
        'admin@example.com',
        'en',
        ['notification' => $notification]
      );
    }
  }

}
```

### NotificationReadEvent

Disparado cuando se marca como leída.

**Evento:** `admin_notifications.notification_read`

```php
public function onRead(NotificationReadEvent $event) {
  $notification_id = $event->getNotificationId();
  $user_id = $event->getUserId();

  // Registrar analytics
  \Drupal::logger('analytics')->info('Notification @id read by user @uid', [
    '@id' => $notification_id,
    '@uid' => $user_id,
  ]);
}
```

## Endpoints REST

### GET /admin-notifications/poll

Obtiene nuevas notificaciones desde el último check.

**Autenticación:** Requiere sesión activa

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

## Tablas de Base de Datos

### admin_notifications

Tabla principal de notificaciones.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | serial | Primary key |
| title | varchar(255) | Título |
| message | text | Mensaje |
| severity | varchar(20) | info, success, warning, error |
| type | varchar(20) | realtime, scheduled |
| status | varchar(20) | active, inactive |
| created | int | Timestamp de creación |
| start_date | int | Inicio (scheduled) |
| end_date | int | Fin (scheduled) |

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

### Rules Integration

Crear notificaciones desde Rules:

```php
// En tu módulo
function mymodule_rules_action_info() {
  return [
    'admin_notifications_create' => [
      'label' => t('Create admin notification'),
      'parameter' => [
        'title' => ['type' => 'text', 'label' => t('Title')],
        'message' => ['type' => 'text', 'label' => t('Message')],
        'severity' => [
          'type' => 'text',
          'label' => t('Severity'),
          'options list' => 'admin_notifications_severity_options',
        ],
      ],
      'group' => t('Admin Notifications'),
    ],
  ];
}

function admin_notifications_create($title, $message, $severity) {
  \Drupal::service('admin_notifications.notification')->createNotification([
    'title' => $title,
    'message' => $message,
    'severity' => $severity,
    'type' => 'realtime',
  ]);
}
```

### Webform Integration

Enviar notificación al completar webform:

```php
/**
 * Implements hook_webform_submission_insert().
 */
function mymodule_webform_submission_insert(WebformSubmissionInterface $submission) {
  $webform = $submission->getWebform();

  if ($webform->id() === 'contact') {
    \Drupal::service('admin_notifications.notification')->createNotification([
      'title' => t('New contact submission'),
      'message' => t('A user submitted the contact form.'),
      'severity' => 'info',
      'type' => 'realtime',
    ]);
  }
}
```

## Testing

### Unit Test Example

```php
namespace Drupal\Tests\admin_notifications\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\admin_notifications\Service\AdminNotificationService;

class AdminNotificationServiceTest extends UnitTestCase {

  public function testCreateNotification() {
    $service = new AdminNotificationService($this->getDatabase());

    $id = $service->createNotification([
      'title' => 'Test',
      'message' => 'Test message',
      'severity' => 'info',
      'type' => 'realtime',
    ]);

    $this->assertGreaterThan(0, $id);
  }

}
```

### Functional Test Example

```php
namespace Drupal\Tests\admin_notifications\Functional;

use Drupal\Tests\BrowserTestBase;

class AdminNotificationListTest extends BrowserTestBase {

  protected static $modules = ['admin_notifications'];

  public function testNotificationList() {
    $admin = $this->createUser(['administer admin notifications']);
    $this->drupalLogin($admin);

    $this->drupalGet('admin/reports/admin-notifications');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Admin Notifications');
  }

}
```
```

##### 3.4. Crear help/ con Páginas de Ayuda

**Archivo: `help/admin_notifications.html.twig`**
```twig
<h2>{{ 'About'|t }}</h2>
<p>{{ 'The Admin Notifications module provides a professional system for administrative notifications with support for toast notifications (Windows 10/11 style), scheduled banners, and real-time polling.'|t }}</p>

<h2>{{ 'Features'|t }}</h2>
<ul>
  <li>{{ 'Toast notifications with real-time polling'|t }}</li>
  <li>{{ 'Scheduled banner notifications'|t }}</li>
  <li>{{ 'Configurable duration, position, and sound'|t }}</li>
  <li>{{ 'Multi-language support'|t }}</li>
  <li>{{ 'Read/unread tracking'|t }}</li>
  <li>{{ 'Role-based permissions'|t }}</li>
</ul>

<h2>{{ 'Usage'|t }}</h2>
<p>{{ 'To create a notification, go to'|t }} <a href="/admin/reports/admin-notifications">{{ 'Administration > Reports > Admin Notifications'|t }}</a>.</p>

<p>{{ 'For configuration options, visit'|t }} <a href="/admin/config/system/admin-notifications">{{ 'Configuration > System > Admin Notifications'|t }}</a>.</p>

<h2>{{ 'Documentation'|t }}</h2>
<p>{{ 'For complete documentation, visit the'|t }} <a href="https://www.drupal.org/docs/contributed-modules/admin-notifications">{{ 'module documentation'|t }}</a>.</p>
```

##### 3.5. Criterios de Éxito
- ✅ README.md sigue formato estándar de Drupal.org
- ✅ CONTRIBUTING.md con guía completa
- ✅ API.md con documentación técnica
- ✅ help/ con páginas de ayuda en Twig
- ✅ Todos los enlaces funcionan

##### 3.6. Commit
```bash
git add README.md CONTRIBUTING.md API.md help/
git commit -m "Issue #0: Mejorar documentación siguiendo estándares Drupal.org

- Actualizar README.md con formato estándar
- Agregar CONTRIBUTING.md con guía de contribución
- Agregar API.md con documentación técnica completa
- Agregar help/ con páginas de ayuda integradas
- Documentar hooks, eventos y endpoints REST"
```

---

### Etapa 4: Implementar Unit Tests (Crítico)
**Duración estimada:** 6-8 horas
**Objetivo:** Crear tests unitarios para lógica de negocio

#### Tareas:

##### 4.1. Crear Estructura de Tests

```bash
mkdir -p tests/src/Unit
mkdir -p tests/src/Kernel
mkdir -p tests/src/Functional
```

##### 4.2. Configurar PHPUnit

**Archivo: `phpunit.xml`**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/9.5/phpunit.xsd"
         bootstrap="../../core/tests/bootstrap.php"
         colors="true">
  <php>
    <ini name="error_reporting" value="32767"/>
    <ini name="memory_limit" value="-1"/>
    <env name="SIMPLETEST_BASE_URL" value="http://localhost"/>
    <env name="SIMPLETEST_DB" value="mysql://user:pass@localhost/drupal"/>
    <env name="BROWSERTEST_OUTPUT_DIRECTORY" value="/path/to/output"/>
  </php>
  <testsuites>
    <testsuite name="unit">
      <directory>./tests/src/Unit</directory>
    </testsuite>
    <testsuite name="kernel">
      <directory>./tests/src/Kernel</directory>
    </testsuite>
    <testsuite name="functional">
      <directory>./tests/src/Functional</directory>
    </testsuite>
  </testsuites>
  <coverage>
    <include>
      <directory>./src</directory>
    </include>
  </coverage>
</phpunit>
```

##### 4.3. Crear Unit Tests

**Archivo: `tests/src/Unit/AdminNotificationServiceTest.php`**
```php
<?php

namespace Drupal\Tests\admin_notifications\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\admin_notifications\Service\AdminNotificationService;
use Drupal\Core\Database\Connection;

/**
 * Tests for AdminNotificationService.
 *
 * @group admin_notifications
 * @coversDefaultClass \Drupal\admin_notifications\Service\AdminNotificationService
 */
class AdminNotificationServiceTest extends UnitTestCase {

  /**
   * The mocked database connection.
   *
   * @var \Drupal\Core\Database\Connection|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $database;

  /**
   * The service under test.
   *
   * @var \Drupal\admin_notifications\Service\AdminNotificationService
   */
  protected $service;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->database = $this->createMock(Connection::class);
    $this->service = new AdminNotificationService($this->database);
  }

  /**
   * Tests notification creation validation.
   *
   * @covers ::createNotification
   */
  public function testCreateNotificationValidation() {
    // Test con datos inválidos
    $result = $this->service->createNotification([]);
    $this->assertFalse($result, 'Creation should fail with empty data');

    // Test sin título
    $result = $this->service->createNotification([
      'message' => 'Test',
      'severity' => 'info',
    ]);
    $this->assertFalse($result, 'Creation should fail without title');

    // Test con severidad inválida
    $result = $this->service->createNotification([
      'title' => 'Test',
      'message' => 'Test',
      'severity' => 'invalid',
    ]);
    $this->assertFalse($result, 'Creation should fail with invalid severity');
  }

  /**
   * Tests severity validation.
   *
   * @covers ::validateSeverity
   * @dataProvider severityProvider
   */
  public function testValidateSeverity($severity, $expected) {
    $result = $this->service->validateSeverity($severity);
    $this->assertEquals($expected, $result);
  }

  /**
   * Data provider for severity tests.
   */
  public function severityProvider() {
    return [
      ['info', TRUE],
      ['success', TRUE],
      ['warning', TRUE],
      ['error', TRUE],
      ['invalid', FALSE],
      ['', FALSE],
      [NULL, FALSE],
    ];
  }

  /**
   * Tests type validation.
   *
   * @covers ::validateType
   * @dataProvider typeProvider
   */
  public function testValidateType($type, $expected) {
    $result = $this->service->validateType($type);
    $this->assertEquals($expected, $result);
  }

  /**
   * Data provider for type tests.
   */
  public function typeProvider() {
    return [
      ['realtime', TRUE],
      ['scheduled', TRUE],
      ['invalid', FALSE],
      ['', FALSE],
      [NULL, FALSE],
    ];
  }

}
```

**Archivo: `tests/src/Unit/NotificationDataTest.php`**
```php
<?php

namespace Drupal\Tests\admin_notifications\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Tests for notification data structures.
 *
 * @group admin_notifications
 */
class NotificationDataTest extends UnitTestCase {

  /**
   * Tests notification array structure.
   */
  public function testNotificationStructure() {
    $notification = [
      'id' => 123,
      'title' => 'Test Notification',
      'message' => 'Test message',
      'severity' => 'info',
      'type' => 'realtime',
      'created' => 1234567890,
    ];

    $this->assertArrayHasKey('id', $notification);
    $this->assertArrayHasKey('title', $notification);
    $this->assertArrayHasKey('message', $notification);
    $this->assertArrayHasKey('severity', $notification);
    $this->assertIsInt($notification['id']);
    $this->assertIsString($notification['title']);
  }

  /**
   * Tests date validation for scheduled notifications.
   */
  public function testScheduledDateValidation() {
    $now = time();

    // Fecha inicio en el pasado
    $start_date = $now - 86400;
    $end_date = $now + 86400;
    $this->assertLessThan($end_date, $start_date);

    // Fecha fin antes de inicio (inválido)
    $start_date = $now + 86400;
    $end_date = $now - 86400;
    $this->assertGreaterThan($end_date, $start_date, 'Start date should not be after end date');
  }

}
```

##### 4.4. Ejecutar Tests

```bash
# Ejecutar todos los unit tests
vendor/bin/phpunit --group admin_notifications --testsuite unit

# Con coverage
vendor/bin/phpunit --group admin_notifications --testsuite unit --coverage-html coverage/

# Test específico
vendor/bin/phpunit tests/src/Unit/AdminNotificationServiceTest.php
```

##### 4.5. Criterios de Éxito
- ✅ Al menos 5 unit tests creados
- ✅ Cobertura de código > 70%
- ✅ Todos los tests pasan
- ✅ Tests documentados con docblocks

##### 4.6. Commit
```bash
git add tests/ phpunit.xml
git commit -m "Issue #0: Agregar Unit Tests

- Crear estructura de tests (Unit, Kernel, Functional)
- Configurar phpunit.xml
- Agregar tests para AdminNotificationService
- Agregar tests para validación de datos
- Tests cubren validación de severidad y tipo
- Configurar code coverage"
```

---

### Etapa 5: Implementar Kernel Tests (Crítico)
**Duración estimada:** 6-8 horas
**Objetivo:** Crear tests de integración con Drupal

#### Tareas:

##### 5.1. Crear Kernel Tests

**Archivo: `tests/src/Kernel/AdminNotificationDatabaseTest.php`**
```php
<?php

namespace Drupal\Tests\admin_notifications\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests database operations for admin notifications.
 *
 * @group admin_notifications
 */
class AdminNotificationDatabaseTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['admin_notifications', 'user', 'system'];

  /**
   * The notification service.
   *
   * @var \Drupal\admin_notifications\Service\AdminNotificationService
   */
  protected $notificationService;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installSchema('admin_notifications', [
      'admin_notifications',
      'admin_notifications_read',
    ]);
    $this->installEntitySchema('user');
    $this->installSchema('system', ['sequences']);

    $this->notificationService = $this->container->get('admin_notifications.notification');
  }

  /**
   * Tests creating a notification in the database.
   */
  public function testCreateNotification() {
    $notification_data = [
      'title' => 'Test Notification',
      'message' => 'This is a test notification.',
      'severity' => 'info',
      'type' => 'realtime',
    ];

    $id = $this->notificationService->createNotification($notification_data);
    $this->assertGreaterThan(0, $id, 'Notification created with valid ID');

    // Verificar en base de datos
    $notification = $this->container->get('database')
      ->select('admin_notifications', 'an')
      ->fields('an')
      ->condition('id', $id)
      ->execute()
      ->fetchObject();

    $this->assertNotNull($notification);
    $this->assertEquals('Test Notification', $notification->title);
    $this->assertEquals('info', $notification->severity);
  }

  /**
   * Tests retrieving active notifications.
   */
  public function testGetActiveNotifications() {
    // Crear notificaciones de prueba
    $this->notificationService->createNotification([
      'title' => 'Active 1',
      'message' => 'Active notification 1',
      'severity' => 'info',
      'type' => 'realtime',
    ]);

    $this->notificationService->createNotification([
      'title' => 'Active 2',
      'message' => 'Active notification 2',
      'severity' => 'success',
      'type' => 'realtime',
    ]);

    $notifications = $this->notificationService->getActiveNotifications('realtime');
    $this->assertCount(2, $notifications);
  }

  /**
   * Tests marking notification as read.
   */
  public function testMarkNotificationAsRead() {
    // Crear notificación
    $id = $this->notificationService->createNotification([
      'title' => 'Test',
      'message' => 'Test message',
      'severity' => 'info',
      'type' => 'realtime',
    ]);

    // Crear usuario de prueba
    $user = $this->createUser();

    // Marcar como leída
    $result = $this->notificationService->markAsRead($id, $user->id());
    $this->assertTrue($result);

    // Verificar en base de datos
    $read = $this->container->get('database')
      ->select('admin_notifications_read', 'anr')
      ->condition('notification_id', $id)
      ->condition('uid', $user->id())
      ->countQuery()
      ->execute()
      ->fetchField();

    $this->assertEquals(1, $read);
  }

  /**
   * Tests deleting a notification.
   */
  public function testDeleteNotification() {
    $id = $this->notificationService->createNotification([
      'title' => 'To Delete',
      'message' => 'This will be deleted',
      'severity' => 'info',
      'type' => 'realtime',
    ]);

    $result = $this->notificationService->deleteNotification($id);
    $this->assertTrue($result);

    // Verificar que no existe
    $notification = $this->container->get('database')
      ->select('admin_notifications', 'an')
      ->condition('id', $id)
      ->countQuery()
      ->execute()
      ->fetchField();

    $this->assertEquals(0, $notification);
  }

}
```

**Archivo: `tests/src/Kernel/AdminNotificationPermissionsTest.php`**
```php
<?php

namespace Drupal\Tests\admin_notifications\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\user\Entity\Role;

/**
 * Tests permissions for admin notifications.
 *
 * @group admin_notifications
 */
class AdminNotificationPermissionsTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['admin_notifications', 'user', 'system'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installSchema('system', ['sequences']);
  }

  /**
   * Tests that permissions are defined correctly.
   */
  public function testPermissionsExist() {
    $permissions = $this->container->get('user.permissions')->getPermissions();

    $this->assertArrayHasKey('administer admin notifications', $permissions);
    $this->assertArrayHasKey('view admin notifications', $permissions);
  }

  /**
   * Tests permission checking.
   */
  public function testUserPermissions() {
    // Usuario sin permisos
    $user = $this->createUser();
    $this->assertFalse($user->hasPermission('administer admin notifications'));

    // Usuario con permisos
    $admin = $this->createUser(['administer admin notifications']);
    $this->assertTrue($admin->hasPermission('administer admin notifications'));
  }

  /**
   * Tests role-based permissions.
   */
  public function testRolePermissions() {
    $role = Role::create([
      'id' => 'notification_admin',
      'label' => 'Notification Administrator',
    ]);
    $role->grantPermission('administer admin notifications');
    $role->save();

    $user = $this->createUser();
    $user->addRole('notification_admin');
    $user->save();

    $this->assertTrue($user->hasPermission('administer admin notifications'));
  }

}
```

##### 5.2. Ejecutar Kernel Tests

```bash
# Ejecutar kernel tests
vendor/bin/phpunit --group admin_notifications --testsuite kernel

# Test específico
vendor/bin/phpunit tests/src/Kernel/AdminNotificationDatabaseTest.php
```

##### 5.3. Criterios de Éxito
- ✅ Al menos 3 kernel tests creados
- ✅ Tests cubren operaciones CRUD
- ✅ Tests cubren permisos
- ✅ Todos los tests pasan

##### 5.4. Commit
```bash
git add tests/src/Kernel/
git commit -m "Issue #0: Agregar Kernel Tests

- Tests para operaciones de base de datos
- Tests para creación de notificaciones
- Tests para marcado como leído
- Tests para eliminación
- Tests para sistema de permisos
- Tests para roles de usuario"
```

---

### Etapa 6: Implementar Functional Tests (Importante)
**Duración estimada:** 8-10 horas
**Objetivo:** Crear tests end-to-end de UI

#### Tareas:

##### 6.1. Crear Functional Tests

**Archivo: `tests/src/Functional/AdminNotificationListTest.php`**
```php
<?php

namespace Drupal\Tests\admin_notifications\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests the admin notifications list page.
 *
 * @group admin_notifications
 */
class AdminNotificationListTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['admin_notifications'];

  /**
   * Tests that the list page is accessible.
   */
  public function testListPageAccess() {
    // Usuario sin permisos
    $user = $this->drupalCreateUser();
    $this->drupalLogin($user);
    $this->drupalGet('admin/reports/admin-notifications');
    $this->assertSession()->statusCodeEquals(403);

    // Usuario con permisos
    $admin = $this->drupalCreateUser(['administer admin notifications']);
    $this->drupalLogin($admin);
    $this->drupalGet('admin/reports/admin-notifications');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Admin Notifications');
  }

  /**
   * Tests creating a notification through the UI.
   */
  public function testCreateNotificationUI() {
    $admin = $this->drupalCreateUser(['administer admin notifications']);
    $this->drupalLogin($admin);

    // Ir a la página de creación
    $this->drupalGet('admin/reports/admin-notifications/add');
    $this->assertSession()->statusCodeEquals(200);

    // Completar formulario
    $this->submitForm([
      'title' => 'Test Notification UI',
      'message' => 'This is a test notification created through UI.',
      'severity' => 'info',
      'type' => 'realtime',
    ], 'Save');

    // Verificar mensaje de éxito
    $this->assertSession()->pageTextContains('Notification created successfully');

    // Verificar que aparece en la lista
    $this->drupalGet('admin/reports/admin-notifications');
    $this->assertSession()->pageTextContains('Test Notification UI');
  }

  /**
   * Tests deleting a notification through the UI.
   */
  public function testDeleteNotificationUI() {
    $admin = $this->drupalCreateUser(['administer admin notifications']);
    $this->drupalLogin($admin);

    // Crear notificación
    $notification_service = \Drupal::service('admin_notifications.notification');
    $id = $notification_service->createNotification([
      'title' => 'To Delete UI',
      'message' => 'Will be deleted',
      'severity' => 'info',
      'type' => 'realtime',
    ]);

    // Eliminar desde UI
    $this->drupalGet('admin/reports/admin-notifications');
    $this->clickLink('Delete');
    $this->submitForm([], 'Confirm');

    // Verificar eliminación
    $this->assertSession()->pageTextContains('Notification deleted');
    $this->assertSession()->pageTextNotContains('To Delete UI');
  }

}
```

**Archivo: `tests/src/Functional/AdminNotificationConfigTest.php`**
```php
<?php

namespace Drupal\Tests\admin_notifications\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests configuration page for admin notifications.
 *
 * @group admin_notifications
 */
class AdminNotificationConfigTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['admin_notifications'];

  /**
   * Tests configuration form access.
   */
  public function testConfigFormAccess() {
    // Sin permisos
    $user = $this->drupalCreateUser();
    $this->drupalLogin($user);
    $this->drupalGet('admin/config/system/admin-notifications');
    $this->assertSession()->statusCodeEquals(403);

    // Con permisos
    $admin = $this->drupalCreateUser(['administer admin notifications']);
    $this->drupalLogin($admin);
    $this->drupalGet('admin/config/system/admin-notifications');
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Tests saving configuration.
   */
  public function testSaveConfiguration() {
    $admin = $this->drupalCreateUser(['administer admin notifications']);
    $this->drupalLogin($admin);

    $this->drupalGet('admin/config/system/admin-notifications');

    // Cambiar configuración
    $this->submitForm([
      'poll_interval' => 60000,
      'toast_duration' => 15000,
      'toast_position' => 'top-right',
      'sound_enabled' => FALSE,
    ], 'Save configuration');

    $this->assertSession()->pageTextContains('The configuration options have been saved');

    // Verificar que se guardó
    $config = $this->config('admin_notifications.settings');
    $this->assertEquals(60000, $config->get('poll_interval'));
    $this->assertEquals(15000, $config->get('toast_duration'));
    $this->assertEquals('top-right', $config->get('toast_position'));
    $this->assertFalse($config->get('sound_enabled'));
  }

}
```

**Archivo: `tests/src/Functional/AdminNotificationPollingTest.php`**
```php
<?php

namespace Drupal\Tests\admin_notifications\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests polling endpoint for admin notifications.
 *
 * @group admin_notifications
 */
class AdminNotificationPollingTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['admin_notifications'];

  /**
   * Tests polling endpoint access.
   */
  public function testPollingAccess() {
    // Sin permisos
    $user = $this->drupalCreateUser();
    $this->drupalLogin($user);
    $this->drupalGet('admin-notifications/poll', ['query' => ['last_check' => 0]]);
    $this->assertSession()->statusCodeEquals(403);

    // Con permisos
    $viewer = $this->drupalCreateUser(['view admin notifications']);
    $this->drupalLogin($viewer);
    $this->drupalGet('admin-notifications/poll', ['query' => ['last_check' => 0]]);
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Tests polling returns correct JSON structure.
   */
  public function testPollingResponse() {
    $user = $this->drupalCreateUser(['view admin notifications']);
    $this->drupalLogin($user);

    // Crear notificación de prueba
    $notification_service = \Drupal::service('admin_notifications.notification');
    $notification_service->createNotification([
      'title' => 'Polling Test',
      'message' => 'Test polling notification',
      'severity' => 'info',
      'type' => 'realtime',
    ]);

    // Hacer request de polling
    $this->drupalGet('admin-notifications/poll', ['query' => ['last_check' => 0]]);

    // Verificar estructura JSON
    $response = json_decode($this->getSession()->getPage()->getContent(), TRUE);

    $this->assertArrayHasKey('notifications', $response);
    $this->assertArrayHasKey('timestamp', $response);
    $this->assertArrayHasKey('count', $response);
    $this->assertGreaterThan(0, $response['count']);
  }

}
```

##### 6.2. Ejecutar Functional Tests

```bash
# Configurar SIMPLETEST_BASE_URL y SIMPLETEST_DB en phpunit.xml primero

# Ejecutar functional tests
vendor/bin/phpunit --group admin_notifications --testsuite functional

# Test específico
vendor/bin/phpunit tests/src/Functional/AdminNotificationListTest.php
```

##### 6.3. Criterios de Éxito
- ✅ Al menos 3 functional tests creados
- ✅ Tests cubren UI principal
- ✅ Tests cubren endpoints AJAX
- ✅ Todos los tests pasan

##### 6.4. Commit
```bash
git add tests/src/Functional/
git commit -m "Issue #0: Agregar Functional Tests

- Tests para lista de notificaciones
- Tests para formulario de configuración
- Tests para endpoints de polling
- Tests para creación desde UI
- Tests para eliminación desde UI
- Tests de permisos en UI"
```

---

### Etapa 7: Configurar GitLab CI (Importante)
**Duración estimada:** 3-4 horas
**Objetivo:** Automatizar validación y tests en CI/CD

#### Tareas:

##### 7.1. Crear .gitlab-ci.yml

**Archivo: `.gitlab-ci.yml`**
```yaml
variables:
  DRUPAL_VERSION: "10.2.x"
  PHP_VERSION: "8.1"

stages:
  - validate
  - test

# Template para jobs de PHP
.php-job:
  image: php:${PHP_VERSION}
  before_script:
    - apt-get update -qq
    - apt-get install -y -qq git unzip libzip-dev
    - docker-php-ext-install zip
    - curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Validar coding standards
phpcs:
  extends: .php-job
  stage: validate
  script:
    - composer require --dev drupal/coder
    - vendor/bin/phpcs --config-set installed_paths vendor/drupal/coder/coder_sniffer
    - vendor/bin/phpcs --standard=Drupal,DrupalPractice --extensions=php,module,inc,install,test --ignore=node_modules,vendor .
  allow_failure: false

# Validar deprecations
drupal-check:
  extends: .php-job
  stage: validate
  script:
    - composer require --dev mglaman/drupal-check
    - vendor/bin/drupal-check .
  allow_failure: false

# PHPStan análisis estático
phpstan:
  extends: .php-job
  stage: validate
  script:
    - composer require --dev phpstan/phpstan mglaman/phpstan-drupal
    - vendor/bin/phpstan analyse src/
  allow_failure: true

# ESLint para JavaScript
eslint:
  image: node:16
  stage: validate
  script:
    - npm install
    - npx eslint js/
  allow_failure: true

# Unit tests
unit-tests:
  extends: .php-job
  stage: test
  services:
    - mysql:5.7
  variables:
    MYSQL_ROOT_PASSWORD: root
    MYSQL_DATABASE: drupal
  script:
    - composer install
    - vendor/bin/phpunit --group admin_notifications --testsuite unit
  coverage: '/^\s*Lines:\s*\d+.\d+\%/'
  artifacts:
    reports:
      junit: coverage/junit.xml
    paths:
      - coverage/

# Kernel tests
kernel-tests:
  extends: .php-job
  stage: test
  services:
    - mysql:5.7
  variables:
    MYSQL_ROOT_PASSWORD: root
    MYSQL_DATABASE: drupal
    SIMPLETEST_DB: mysql://root:root@mysql/drupal
  script:
    - composer install
    - vendor/bin/phpunit --group admin_notifications --testsuite kernel
  artifacts:
    reports:
      junit: coverage/junit.xml

# Functional tests
functional-tests:
  extends: .php-job
  stage: test
  services:
    - mysql:5.7
  variables:
    MYSQL_ROOT_PASSWORD: root
    MYSQL_DATABASE: drupal
    SIMPLETEST_DB: mysql://root:root@mysql/drupal
    SIMPLETEST_BASE_URL: http://localhost
  script:
    - composer install
    - vendor/bin/phpunit --group admin_notifications --testsuite functional
  artifacts:
    reports:
      junit: coverage/junit.xml
```

##### 7.2. Crear Archivo de Package para NPM

**Archivo: `package.json`**
```json
{
  "name": "admin-notifications",
  "version": "1.0.0",
  "description": "Admin Notifications module for Drupal",
  "scripts": {
    "lint": "eslint js/",
    "lint:fix": "eslint js/ --fix"
  },
  "devDependencies": {
    "eslint": "^8.0.0",
    "eslint-config-drupal": "^6.0.0"
  },
  "repository": {
    "type": "git",
    "url": "https://git.drupalcode.org/project/admin_notifications.git"
  },
  "license": "GPL-2.0-or-later"
}
```

##### 7.3. Probar CI Localmente (Opcional)

```bash
# Instalar gitlab-runner
# En Ubuntu/Debian:
curl -L https://packages.gitlab.com/install/repositories/runner/gitlab-runner/script.deb.sh | sudo bash
sudo apt-get install gitlab-runner

# Ejecutar job localmente
gitlab-runner exec docker phpcs
gitlab-runner exec docker unit-tests
```

##### 7.4. Criterios de Éxito
- ✅ `.gitlab-ci.yml` configurado correctamente
- ✅ Todos los jobs definidos (validate + test)
- ✅ Pipeline pasa en GitLab
- ✅ Badges de pipeline agregados al README

##### 7.5. Commit
```bash
git add .gitlab-ci.yml package.json
git commit -m "Issue #0: Configurar GitLab CI/CD

- Agregar .gitlab-ci.yml con pipeline completo
- Jobs de validación (phpcs, drupal-check, phpstan, eslint)
- Jobs de testing (unit, kernel, functional)
- Configurar artifacts y coverage reports
- Agregar package.json para ESLint"
```

---

### Etapa 8: Optimizaciones de Rendimiento (Importante)
**Duración estimada:** 4-6 horas
**Objetivo:** Mejorar rendimiento para sitios con muchas notificaciones

#### Tareas:

##### 8.1. Agregar Índices de Base de Datos

**Modificar:** `admin_notifications.install`
```php
/**
 * Implements hook_schema().
 */
function admin_notifications_schema() {
  $schema['admin_notifications'] = [
    'description' => 'Stores admin notifications.',
    'fields' => [
      'id' => [
        'type' => 'serial',
        'not null' => TRUE,
        'description' => 'Primary Key: Unique notification ID.',
      ],
      'title' => [
        'type' => 'varchar',
        'length' => 255,
        'not null' => TRUE,
        'description' => 'The notification title.',
      ],
      'message' => [
        'type' => 'text',
        'size' => 'normal',
        'not null' => TRUE,
        'description' => 'The notification message.',
      ],
      'severity' => [
        'type' => 'varchar',
        'length' => 20,
        'not null' => TRUE,
        'default' => 'info',
        'description' => 'Severity level: info, success, warning, error.',
      ],
      'type' => [
        'type' => 'varchar',
        'length' => 20,
        'not null' => TRUE,
        'default' => 'realtime',
        'description' => 'Type: realtime or scheduled.',
      ],
      'status' => [
        'type' => 'varchar',
        'length' => 20,
        'not null' => TRUE,
        'default' => 'active',
        'description' => 'Status: active or inactive.',
      ],
      'created' => [
        'type' => 'int',
        'not null' => TRUE,
        'default' => 0,
        'description' => 'Unix timestamp of when created.',
      ],
      'start_date' => [
        'type' => 'int',
        'not null' => FALSE,
        'description' => 'Unix timestamp of start date (scheduled only).',
      ],
      'end_date' => [
        'type' => 'int',
        'not null' => FALSE,
        'description' => 'Unix timestamp of end date (scheduled only).',
      ],
    ],
    'primary key' => ['id'],
    'indexes' => [
      'type' => ['type'],
      'status' => ['status'],
      'created' => ['created'],
      'type_status' => ['type', 'status'],
      'type_status_created' => ['type', 'status', 'created'],
      'scheduled_dates' => ['start_date', 'end_date'],
    ],
  ];

  $schema['admin_notifications_read'] = [
    'description' => 'Tracks which users have read which notifications.',
    'fields' => [
      'id' => [
        'type' => 'serial',
        'not null' => TRUE,
      ],
      'notification_id' => [
        'type' => 'int',
        'not null' => TRUE,
        'description' => 'References {admin_notifications}.id.',
      ],
      'uid' => [
        'type' => 'int',
        'not null' => TRUE,
        'description' => 'References {users}.uid.',
      ],
      'read_timestamp' => [
        'type' => 'int',
        'not null' => TRUE,
        'default' => 0,
        'description' => 'Unix timestamp of when read.',
      ],
    ],
    'primary key' => ['id'],
    'indexes' => [
      'notification_id' => ['notification_id'],
      'uid' => ['uid'],
      'notification_uid' => ['notification_id', 'uid'],
    ],
  ];

  return $schema;
}

/**
 * Add database indexes for performance.
 */
function admin_notifications_update_8001() {
  $schema = Database::getConnection()->schema();

  // Agregar índices si no existen
  if (!$schema->indexExists('admin_notifications', 'type_status_created')) {
    $schema->addIndex('admin_notifications', 'type_status_created',
      ['type', 'status', 'created'],
      ['fields' => [
        'type' => ['type' => 'varchar', 'length' => 20],
        'status' => ['type' => 'varchar', 'length' => 20],
        'created' => ['type' => 'int'],
      ]]
    );
  }

  if (!$schema->indexExists('admin_notifications_read', 'notification_uid')) {
    $schema->addIndex('admin_notifications_read', 'notification_uid',
      ['notification_id', 'uid'],
      ['fields' => [
        'notification_id' => ['type' => 'int'],
        'uid' => ['type' => 'int'],
      ]]
    );
  }

  return t('Added performance indexes to admin_notifications tables.');
}
```

##### 8.2. Implementar Paginación en Controller

**Modificar:** `src/Controller/AdminNotificationController.php`
```php
/**
 * Lista las notificaciones con paginación.
 */
public function listNotifications() {
  // Obtener página actual
  $page = \Drupal::request()->query->get('page', 0);
  $items_per_page = 50;

  // Query con paginación
  $query = $this->database->select('admin_notifications', 'an')
    ->extend('Drupal\Core\Database\Query\PagerSelectExtender')
    ->fields('an')
    ->orderBy('an.created', 'DESC')
    ->limit($items_per_page);

  $notifications = $query->execute()->fetchAll();

  $build = [
    '#theme' => 'admin_notifications_list',
    '#notifications' => $notifications,
    '#attached' => [
      'library' => [
        'admin_notifications/notifications',
      ],
    ],
  ];

  // Agregar pager
  $build['pager'] = [
    '#type' => 'pager',
  ];

  return $build;
}
```

##### 8.3. Agregar Caché a Configuración

**Modificar:** `src/Form/AdminNotificationsSettingsForm.php`
```php
/**
 * {@inheritdoc}
 */
public function submitForm(array &$form, FormStateInterface $form_state) {
  parent::submitForm($form, $form_state);

  $this->config('admin_notifications.settings')
    ->set('poll_interval', $form_state->getValue('poll_interval'))
    ->set('toast_duration', $form_state->getValue('toast_duration'))
    ->set('toast_position', $form_state->getValue('toast_position'))
    ->set('sound_enabled', $form_state->getValue('sound_enabled'))
    ->save();

  // Invalidar caché para que los cambios se reflejen inmediatamente
  \Drupal::service('cache.render')->invalidateAll();
  \Drupal::service('cache.page')->deleteAll();
}
```

##### 8.4. Lazy Loading de Notificaciones en JavaScript

**Modificar:** `js/admin-notifications.js`
```javascript
(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.adminNotifications = {
    pollTimer: null,
    lastCheck: Math.floor(Date.now() / 1000),
    pollInterval: 30000,
    initialized: false,
    errorCount: 0,
    maxErrors: 5,

    attach: function (context, settings) {
      // Evitar múltiples inicializaciones
      if (this.initialized) {
        return;
      }

      // Solo ejecutar si tenemos los settings
      if (!settings.adminNotifications) {
        return;
      }

      this.initialized = true;
      this.pollInterval = settings.adminNotifications.pollInterval || 30000;

      // Iniciar polling solo si está habilitado
      if (settings.adminNotifications.enabled) {
        this.startPolling();
      }
    },

    startPolling: function () {
      var self = this;

      // Hacer primera verificación inmediata
      self.checkNotifications();

      // Configurar intervalo
      self.pollTimer = setInterval(function () {
        // Si hubo muchos errores, detener polling
        if (self.errorCount >= self.maxErrors) {
          clearInterval(self.pollTimer);
          console.error('Admin Notifications: Polling detenido por demasiados errores');
          return;
        }

        self.checkNotifications();
      }, self.pollInterval);
    },

    checkNotifications: function () {
      var self = this;

      $.ajax({
        url: '/admin-notifications/poll',
        method: 'GET',
        data: {
          last_check: self.lastCheck
        },
        dataType: 'json',
        timeout: 10000, // Timeout de 10 segundos
        success: function (data) {
          // Reset error count en éxito
          self.errorCount = 0;

          if (data.notifications && data.notifications.length > 0) {
            data.notifications.forEach(function (notification) {
              self.showToastNotification(notification);
            });
          }

          // Actualizar lastCheck
          if (data.timestamp) {
            self.lastCheck = data.timestamp;
          }
        },
        error: function (xhr, status, error) {
          self.errorCount++;

          // Solo loggear si no superamos el máximo
          if (self.errorCount < self.maxErrors) {
            console.error('Admin Notifications: Error en polling', {
              status: status,
              error: error,
              count: self.errorCount
            });
          }
        }
      });
    },

    showToastNotification: function (notification) {
      // Verificar que el módulo toast esté cargado (lazy)
      if (typeof Drupal.toastNotifications === 'undefined') {
        // Cargar dinámicamente si no está cargado
        this.loadToastLibrary(function() {
          this.displayToast(notification);
        }.bind(this));
      } else {
        this.displayToast(notification);
      }
    },

    loadToastLibrary: function(callback) {
      // Cargar library toast dinámicamente
      var script = document.createElement('script');
      script.src = '/modules/custom/admin_notifications/js/toast-notifications.js';
      script.onload = callback;
      document.head.appendChild(script);
    },

    displayToast: function(notification) {
      const duration = drupalSettings.adminNotifications.toast_duration || 10000;
      Drupal.toastNotifications.show(
        notification.title,
        notification.message,
        notification.severity,
        duration
      );
    }
  };

})(jQuery, Drupal, drupalSettings);
```

##### 8.5. Criterios de Éxito
- ✅ Índices de base de datos optimizados
- ✅ Paginación implementada
- ✅ Caché configurada correctamente
- ✅ Lazy loading en JavaScript
- ✅ Timeouts y manejo de errores

##### 8.6. Commit
```bash
git add admin_notifications.install src/Controller/ js/
git commit -m "Issue #0: Optimizar rendimiento del módulo

- Agregar índices compuestos para queries comunes
- Implementar paginación en listado (50 items/página)
- Agregar invalidación de caché en configuración
- Implementar lazy loading de toast notifications
- Agregar timeouts y manejo de errores en polling
- Agregar límite de errores consecutivos (5 máximo)"
```

---

### Etapa 9: Revisión de Seguridad (Crítico)
**Duración estimada:** 4-6 horas
**Objetivo:** Auditar y asegurar el módulo contra vulnerabilidades comunes

#### Tareas:

##### 9.1. Auditoría de XSS

**Revisar todos los templates Twig:**
- Verificar que `{{ variable }}` se usa (auto-escaping)
- Cambiar `{{ variable|raw }}` a `{{ variable }}` donde sea posible
- Usar `|t` en vez de `|trans` para traducciones

**Ejemplo de corrección en `templates/admin-notifications-list.html.twig`:**
```twig
{# ANTES - Potencial XSS #}
<h2>{{ notification.title|raw }}</h2>
<div>{{ notification.message|raw }}</div>

{# DESPUÉS - Seguro #}
<h2>{{ notification.title }}</h2>
<div>{{ notification.message }}</div>
```

##### 9.2. Auditoría de SQL Injection

**Revisar todos los queries:**
- Verificar uso de placeholders (`:placeholder`)
- Nunca usar concatenación de strings en SQL

**Ejemplo de verificación en `AdminNotificationPollController.php`:**
```php
// ✅ CORRECTO - Usa placeholders
$query = $this->database->select('admin_notifications', 'an')
  ->fields('an')
  ->condition('an.type', 'realtime')
  ->condition('an.status', 'active')
  ->condition('an.created', $last_check, '>');

// ❌ INCORRECTO - Vulnerable a SQL injection
// $query = "SELECT * FROM admin_notifications WHERE created > " . $last_check;
```

##### 9.3. Auditoría de CSRF

**Verificar tokens CSRF en todas las acciones POST:**

**Añadir a `AdminNotificationController.php`:**
```php
/**
 * Elimina una notificación con verificación CSRF.
 */
public function delete($notification_id, Request $request) {
  // Verificar token CSRF
  $token = $request->query->get('token');
  if (!\Drupal::csrfToken()->validate($token, 'delete_notification_' . $notification_id)) {
    throw new AccessDeniedHttpException('Invalid CSRF token');
  }

  // Proceder con eliminación
  $this->database->delete('admin_notifications')
    ->condition('id', $notification_id)
    ->execute();

  $this->messenger()->addMessage($this->t('Notification deleted successfully.'));
  return $this->redirect('admin_notifications.list');
}
```

**Actualizar links de eliminación en template:**
```twig
<a href="{{ path('admin_notifications.delete', {'notification_id': notification.id}, {
  'query': {'token': csrf_token('delete_notification_' ~ notification.id)}
}) }}">{{ 'Delete'|t }}</a>
```

##### 9.4. Auditoría de Permisos

**Verificar todos los endpoints:**

**Añadir a `AdminNotificationPollController.php`:**
```php
/**
 * {@inheritdoc}
 */
public function access(AccountInterface $account) {
  // Verificar permisos
  return AccessResult::allowedIfHasPermissions($account, [
    'view admin notifications',
    'access administration pages',
  ], 'OR');
}
```

**Añadir a `admin_notifications.routing.yml`:**
```yaml
admin_notifications.poll:
  path: '/admin-notifications/poll'
  defaults:
    _controller: '\Drupal\admin_notifications\Controller\AdminNotificationPollController::poll'
    _title: 'Notification Poll'
  requirements:
    _permission: 'view admin notifications+access administration pages'
  options:
    no_cache: 'TRUE'

admin_notifications.mark_read:
  path: '/admin-notifications/{notification_id}/mark-read'
  defaults:
    _controller: '\Drupal\admin_notifications\Controller\AdminNotificationPollController::markRead'
  requirements:
    _permission: 'view admin notifications+access administration pages'
    notification_id: \d+
  options:
    no_cache: 'TRUE'
```

##### 9.5. Validación de Entrada

**Añadir validación estricta en forms:**

**Actualizar `src/Form/AdminNotificationForm.php`:**
```php
/**
 * {@inheritdoc}
 */
public function validateForm(array &$form, FormStateInterface $form_state) {
  parent::validateForm($form, $form_state);

  // Validar título
  $title = $form_state->getValue('title');
  if (strlen($title) > 255) {
    $form_state->setErrorByName('title', $this->t('Title must not exceed 255 characters.'));
  }
  if (trim($title) === '') {
    $form_state->setErrorByName('title', $this->t('Title cannot be empty.'));
  }

  // Validar severidad
  $severity = $form_state->getValue('severity');
  $valid_severities = ['info', 'success', 'warning', 'error'];
  if (!in_array($severity, $valid_severities)) {
    $form_state->setErrorByName('severity', $this->t('Invalid severity level.'));
  }

  // Validar tipo
  $type = $form_state->getValue('type');
  $valid_types = ['realtime', 'scheduled'];
  if (!in_array($type, $valid_types)) {
    $form_state->setErrorByName('type', $this->t('Invalid notification type.'));
  }

  // Validar fechas para scheduled
  if ($type === 'scheduled') {
    $start_date = $form_state->getValue('start_date');
    $end_date = $form_state->getValue('end_date');

    if (empty($start_date) || empty($end_date)) {
      $form_state->setErrorByName('start_date', $this->t('Start and end dates are required for scheduled notifications.'));
    }

    if ($start_date && $end_date && $end_date <= $start_date) {
      $form_state->setErrorByName('end_date', $this->t('End date must be after start date.'));
    }
  }

  // Validar longitud del mensaje
  $message = $form_state->getValue('message');
  if (strlen($message) > 10000) {
    $form_state->setErrorByName('message', $this->t('Message must not exceed 10,000 characters.'));
  }
}
```

##### 9.6. Sanitización en JavaScript

**Actualizar `js/toast-notifications.js`:**
```javascript
showToastNotification: function (notification) {
  // Sanitizar HTML en título y mensaje
  const sanitizedTitle = this.sanitizeHTML(notification.title);
  const sanitizedMessage = this.sanitizeHTML(notification.message);

  Drupal.toastNotifications.show(
    sanitizedTitle,
    sanitizedMessage,
    notification.severity,
    duration
  );
},

/**
 * Sanitiza HTML para prevenir XSS.
 */
sanitizeHTML: function(html) {
  const temp = document.createElement('div');
  temp.textContent = html; // textContent automáticamente escapa HTML
  return temp.innerHTML;
}
```

##### 9.7. Rate Limiting en Polling

**Añadir a `AdminNotificationPollController.php`:**
```php
/**
 * Endpoint de polling con rate limiting.
 */
public function poll(Request $request) {
  $user = $this->currentUser();

  // Verificar permisos
  if (!$user->hasPermission('view admin notifications') &&
      !$user->hasPermission('access administration pages')) {
    \Drupal::logger('admin_notifications')->warning('Polling access denied for user @uid', [
      '@uid' => $user->id(),
    ]);
    return new JsonResponse(['notifications' => []], 403);
  }

  // Rate limiting: Máximo 1 request cada 10 segundos por usuario
  $cache_key = 'admin_notifications:poll:' . $user->id();
  $cache = \Drupal::cache()->get($cache_key);

  if ($cache && $cache->data > (time() - 10)) {
    return new JsonResponse([
      'notifications' => [],
      'error' => 'Rate limit exceeded',
    ], 429);
  }

  // Establecer caché para rate limiting
  \Drupal::cache()->set($cache_key, time(), time() + 10);

  try {
    // ... resto del código de polling ...
  }
  catch (\Exception $e) {
    // ... manejo de errores ...
  }
}
```

##### 9.8. Crear Documento de Seguridad

**Archivo: `SECURITY.md`**
```markdown
# Security Policy

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 1.x     | :white_check_mark: |
| < 1.0   | :x:                |

## Reporting a Vulnerability

If you discover a security vulnerability within Admin Notifications, please send an email to [security@example.com](mailto:security@example.com). All security vulnerabilities will be promptly addressed.

Please do NOT open a public issue for security vulnerabilities.

## Security Measures Implemented

### XSS Prevention
- All templates use Twig auto-escaping
- User input is sanitized in JavaScript
- No use of `|raw` filter without proper sanitization

### SQL Injection Prevention
- All database queries use parameterized placeholders
- No string concatenation in SQL
- Drupal Database API used throughout

### CSRF Protection
- All POST/DELETE actions require valid CSRF tokens
- Tokens validated on server-side
- Links include token in query string

### Authentication & Authorization
- All endpoints verify user permissions
- Role-based access control implemented
- Session management handled by Drupal core

### Rate Limiting
- Polling endpoint limited to 1 request per 10 seconds per user
- Prevents abuse and DoS attacks

### Input Validation
- All form inputs validated server-side
- Length limits enforced
- Type checking for all parameters

### Output Encoding
- All output properly escaped
- HTML entities encoded where necessary
- JSON responses properly structured

## Security Best Practices for Site Builders

1. **Keep Module Updated:**
   ```bash
   composer update drupal/admin_notifications
   ```

2. **Configure Permissions Carefully:**
   - Only grant "administer admin notifications" to trusted roles
   - Grant "view admin notifications" only to authenticated users

3. **Monitor Logs:**
   ```bash
   drush watchdog:show --filter=admin_notifications --severity=Error
   ```

4. **Use HTTPS:**
   - Always use HTTPS in production
   - Enable secure cookies in Drupal

5. **Regular Audits:**
   - Review notification content regularly
   - Monitor for suspicious activity in logs

## Security Audit History

- **2025-01**: Initial security audit completed
  - XSS vulnerabilities addressed
  - CSRF protection implemented
  - Rate limiting added
  - Input validation strengthened

## Contact

For security concerns, contact: security@example.com
```

##### 9.9. Criterios de Éxito
- ✅ Todos los templates usan auto-escaping
- ✅ Todos los queries usan placeholders
- ✅ CSRF tokens en todas las acciones POST/DELETE
- ✅ Validación de entrada en todos los formularios
- ✅ Rate limiting implementado
- ✅ SECURITY.md creado

##### 9.10. Commit
```bash
git add src/ templates/ js/ SECURITY.md
git commit -m "Issue #0: Realizar auditoría y mejoras de seguridad

- Auditar y corregir potenciales vulnerabilidades XSS
- Verificar protección contra SQL injection
- Implementar tokens CSRF en todas las acciones
- Agregar validación estricta de entrada
- Implementar rate limiting en endpoint de polling
- Sanitizar salida en JavaScript
- Agregar documento SECURITY.md con políticas"
```

---

### Etapa 10: Preparación Final y Publicación (Crítico)
**Duración estimada:** 3-4 horas
**Objetivo:** Finalizar preparación y publicar en Drupal.org

#### Tareas:

##### 10.1. Verificación Final de Checklist

```bash
# Ejecutar todas las validaciones
vendor/bin/phpcs --standard=Drupal,DrupalPractice .
vendor/bin/drupal-check .
vendor/bin/phpstan analyse src/
npx eslint js/

# Ejecutar todos los tests
vendor/bin/phpunit --group admin_notifications

# Validar composer.json
composer validate

# Verificar que todos los archivos necesarios existen
ls -la LICENSE.txt README.md CHANGELOG.md CONTRIBUTING.md API.md SECURITY.md
```

##### 10.2. Actualizar CHANGELOG.md con Versión Final

```markdown
# Changelog

## [1.0.0] - 2025-01-XX

### Added
- Sistema de notificaciones tipo toast (estilo Windows 10/11)
- Sistema de notificaciones tipo banner programables
- Polling en tiempo real cada 30 segundos (configurable)
- Configuración de duración, posición y sonido
- Sistema de roles y permisos granulares
- Panel administrativo para gestión
- Soporte multiidioma (ES, EN, FR, PT-BR, JA)
- Sistema de marcado leídas/no leídas
- Logging con Drupal Watchdog
- Suite completa de tests (Unit, Kernel, Functional)
- GitLab CI/CD pipeline
- Documentación completa (README, API, CONTRIBUTING)
- Rate limiting en endpoints
- Optimizaciones de rendimiento con índices y paginación

### Fixed
- Visibilidad del botón de eliminar
- Duración de toast respetando configuración
- Sistema de polling ejecutándose correctamente
- Botón de cerrar visible en banners
- Ancho de banners sin causar scroll horizontal

### Security
- Implementación de queries parametrizadas
- Verificación de permisos en todos los endpoints
- Auto-escaping en templates Twig
- Protección CSRF en formularios
- Rate limiting para prevenir abuso
- Validación estricta de entrada
- Sanitización de salida en JavaScript
- Auditoría de seguridad completa

[1.0.0]: https://git.drupalcode.org/project/admin_notifications/-/tags/1.0.0
```

##### 10.3. Actualizar admin_notifications.info.yml

```yaml
name: 'Admin Notifications'
type: module
description: 'Sistema profesional de notificaciones administrativas con soporte para toast, banners y notificaciones en tiempo real.'
package: Administration
core_version_requirement: ^9.3 || ^10

project: 'admin_notifications'
version: '1.0.0'

dependencies:
  - drupal:system (>=9.3)
  - drupal:user

configure: admin_notifications.settings
help: admin_notifications.help

# Información para drupal.org
project_status: 'published'
date: '1234567890'
```

##### 10.4. Crear Release en Git

```bash
# Asegurar que todos los cambios estén commiteados
git status

# Crear tag de versión
git tag -a 1.0.0 -m "Release 1.0.0

- Initial stable release
- Full feature set implemented
- Complete test coverage
- Security audited
- Documentation complete"

# Push de commits y tags
git push origin main
git push origin --tags
```

##### 10.5. Crear Proyecto en Drupal.org

**Pasos manuales en Drupal.org:**

1. **Crear Cuenta y Solicitar Git Access:**
   - Registrarse en https://www.drupal.org
   - Ir a perfil > Edit > Get Git Access
   - Completar formulario y esperar aprobación

2. **Crear Proyecto:**
   - Ir a https://www.drupal.org/node/add/project-module
   - Completar formulario:
     - Project title: Admin Notifications
     - Machine name: admin_notifications
     - Description: (Copiar de README.md)
     - Categories: Administration, User Interface
     - Maintenance status: Actively maintained
     - Development status: Stable releases available

3. **Configurar Repositorio Git:**
   ```bash
   # Agregar remote de Drupal.org
   git remote add drupal https://git.drupalcode.org/project/admin_notifications.git

   # Push inicial
   git push drupal main
   git push drupal --tags
   ```

4. **Crear Release en Drupal.org:**
   - Ir a proyecto > Releases > Add new release
   - Seleccionar tag 1.0.0
   - Completar Release notes (copiar de CHANGELOG.md)
   - Publicar release

5. **Completar Información del Proyecto:**
   - Agregar screenshots
   - Configurar issue queue
   - Agregar mantenedores
   - Habilitar automated testing

##### 10.6. Verificar en Packagist/Composer

```bash
# Verificar que está disponible en Packagist
composer search admin_notifications

# Instalar desde Drupal.org
composer require drupal/admin_notifications

# Verificar instalación
drush en admin_notifications
drush cr
```

##### 10.7. Publicar Anuncio

**Crear entrada en drupal.org/planet:**
```markdown
# Admin Notifications 1.0.0 Released

I'm excited to announce the first stable release of Admin Notifications, a professional notification system for Drupal 10 with support for toast notifications (Windows 10/11 style), scheduled banners, and real-time polling.

## Features

- Toast notifications with real-time polling
- Scheduled banner notifications
- Configurable duration, position, and sound
- Multi-language support (5 languages)
- Complete role-based permissions
- Full test coverage
- Security audited

## Installation

```bash
composer require drupal/admin_notifications
drush en admin_notifications
```

## Links

- Project page: https://www.drupal.org/project/admin_notifications
- Documentation: https://www.drupal.org/docs/contributed-modules/admin-notifications
- Issue queue: https://www.drupal.org/project/issues/admin_notifications

Feedback and contributions welcome!
```

##### 10.8. Actualizar README con Badges

**Actualizar README.md:**
```markdown
# Admin Notifications

[![License](https://img.shields.io/badge/license-GPL--2.0--or--later-blue.svg)](LICENSE.txt)
[![Drupal](https://img.shields.io/badge/drupal-9.3%20%7C%2010-blue.svg)](https://www.drupal.org)
[![Build Status](https://git.drupalcode.org/project/admin_notifications/badges/main/pipeline.svg)](https://git.drupalcode.org/project/admin_notifications/-/pipelines)
[![Coverage](https://git.drupalcode.org/project/admin_notifications/badges/main/coverage.svg)](https://git.drupalcode.org/project/admin_notifications/-/coverage)

Sistema profesional de notificaciones administrativas para Drupal 10 con soporte
para notificaciones toast (estilo Windows 10/11), banners programables y
notificaciones en tiempo real mediante polling.

[Rest of README...]
```

##### 10.9. Criterios de Éxito
- ✅ Todas las validaciones pasan
- ✅ CHANGELOG actualizado con versión 1.0.0
- ✅ Tag de git creado
- ✅ Proyecto creado en Drupal.org
- ✅ Release publicado
- ✅ Disponible via Composer
- ✅ Badges agregados al README

##### 10.10. Commit Final
```bash
git add CHANGELOG.md README.md admin_notifications.info.yml
git commit -m "Issue #0: Preparar release 1.0.0

- Actualizar CHANGELOG con versión final
- Actualizar .info.yml con version 1.0.0
- Agregar badges al README
- Preparar para publicación en Drupal.org"

git tag -a 1.0.0 -m "Release 1.0.0"
git push origin main
git push origin --tags
git push drupal main
git push drupal --tags
```

---

## Lista de Verificación Final

### Archivos Requeridos
- [x] `admin_notifications.info.yml` con core_version_requirement
- [x] `composer.json` válido
- [x] `LICENSE.txt` (GPL-2.0-or-later)
- [x] `README.md` con formato Drupal.org
- [x] `CHANGELOG.md` con semantic versioning
- [x] `CONTRIBUTING.md`
- [x] `API.md`
- [x] `SECURITY.md`
- [x] `.gitignore`
- [x] `.gitlab-ci.yml`

### Validación de Código
- [x] phpcs sin errores (Drupal, DrupalPractice)
- [x] drupal-check sin deprecations
- [x] phpstan nivel 1 sin errores
- [x] ESLint sin errores

### Tests
- [x] Unit tests > 70% coverage
- [x] Kernel tests para operaciones CRUD
- [x] Functional tests para UI
- [x] Todos los tests pasan

### Seguridad
- [x] No hay concatenación SQL
- [x] Todos los queries usan placeholders
- [x] Templates usan auto-escaping
- [x] CSRF tokens en acciones POST/DELETE
- [x] Validación de entrada en formularios
- [x] Rate limiting implementado
- [x] Auditoría de seguridad completa

### Rendimiento
- [x] Índices de base de datos optimizados
- [x] Paginación implementada
- [x] Caché configurada
- [x] Lazy loading donde aplica

### Documentación
- [x] README completo y actualizado
- [x] API documentation completa
- [x] Help pages en `help/`
- [x] Docblocks en todos los métodos
- [x] CHANGELOG actualizado

### GitLab CI
- [x] Pipeline configurado
- [x] Todos los jobs pasan
- [x] Artifacts configurados
- [x] Coverage reports habilitados

### Drupal.org
- [x] Proyecto creado
- [x] Repositorio configurado
- [x] Release publicado
- [x] Issue queue habilitado
- [x] Automated testing configurado

---

## Proceso de Publicación en Drupal.org

### Paso 1: Prerrequisitos (30 minutos)

1. **Crear cuenta en Drupal.org:**
   - Registrarse en https://www.drupal.org/user/register
   - Configurar perfil con información completa
   - Agregar foto de perfil

2. **Solicitar Git Access:**
   - Ir a https://www.drupal.org/node/1001698
   - Leer y entender las políticas de Git
   - Ir a perfil > Edit > Get Git Access
   - Completar formulario explicando el proyecto
   - Esperar aprobación (usualmente 1-3 días)

3. **Configurar SSH Keys:**
   ```bash
   # Generar clave SSH si no tienes una
   ssh-keygen -t ed25519 -C "tu.email@example.com"

   # Copiar clave pública
   cat ~/.ssh/id_ed25519.pub

   # Agregar en Drupal.org: perfil > Edit > SSH Keys
   ```

### Paso 2: Crear Proyecto (30 minutos)

1. **Solicitar nuevo proyecto:**
   - Ir a https://www.drupal.org/node/add/project-module
   - Completar formulario:

   ```
   Project title: Admin Notifications
   Machine name: admin_notifications

   Description:
   Sistema profesional de notificaciones administrativas para Drupal con soporte
   para notificaciones toast (estilo Windows 10/11), banners programables y
   notificaciones en tiempo real mediante polling.

   Features:
   - Toast notifications with real-time polling
   - Scheduled banner notifications
   - Configurable duration, position, and sound
   - Multi-language support (5 languages)
   - Role-based permissions
   - Full test coverage

   Project type: Full project
   Maintenance status: Actively maintained
   Development status: Stable releases available

   Categories:
   - Administration
   - User Interface

   License: GPL-2.0-or-later
   ```

2. **Esperar aprobación del proyecto** (usualmente 1-2 días)

### Paso 3: Configurar Repositorio (15 minutos)

```bash
# Una vez aprobado, configurar remote
git remote add drupal https://git.drupalcode.org/project/admin_notifications.git

# Verificar remotes
git remote -v

# Push inicial
git push drupal main --force

# Push tags
git push drupal --tags
```

### Paso 4: Crear Release (30 minutos)

1. **En Drupal.org, ir al proyecto > Releases > Add new release**

2. **Configurar release:**
   ```
   Release type: Official release
   Version: 1.0.0
   Release tag: 1.0.0

   Release notes:
   [Copiar contenido relevante de CHANGELOG.md]

   Recommended: Yes
   Supported: Yes
   Security update: No
   ```

3. **Publicar release**

### Paso 5: Configuración Post-Publicación (1 hora)

1. **Agregar Screenshots:**
   - Tomar screenshots del módulo en acción
   - Ir a proyecto > Edit > Screenshots
   - Subir imágenes (PNG, 1200x800px recomendado)

2. **Configurar Issue Queue:**
   - Habilitar issue tracking
   - Configurar componentes:
     - Code
     - Documentation
     - Feature request
     - Bug report
     - Support request

3. **Habilitar Automated Testing:**
   - Ir a proyecto > Edit > Testing
   - Habilitar DrupalCI
   - Configurar para ejecutar en cada commit

4. **Actualizar Descripción del Proyecto:**
   - Agregar más detalles
   - Links a documentación
   - Ejemplos de uso
   - Screenshots

5. **Crear Página de Documentación:**
   - Ir a https://www.drupal.org/docs/contributed-modules
   - Crear estructura de documentación
   - Enlazar desde proyecto

### Paso 6: Promoción (30 minutos)

1. **Publicar en Planet Drupal:**
   - Crear post en tu blog
   - Agregar tags: drupal, module, release
   - Automáticamente se agrega a Planet Drupal

2. **Compartir en Redes Sociales:**
   - Twitter: @drupal hashtag
   - LinkedIn: Grupos de Drupal
   - Reddit: r/drupal

3. **Notificar en Slack:**
   - Canal #module-development en Drupal Slack
   - Anunciar release

### Paso 7: Mantenimiento Continuo

1. **Monitorear Issue Queue:**
   ```bash
   # Revisar issues diariamente
   # Responder dentro de 48 horas
   # Priorizar bugs de seguridad
   ```

2. **Releases Regulares:**
   - Bugfixes: 1.0.1, 1.0.2 (cada 2-4 semanas)
   - Features: 1.1.0, 1.2.0 (cada 2-3 meses)
   - Mayor: 2.0.0 (anualmente o con breaking changes)

3. **Actualizar Documentación:**
   - Mantener README actualizado
   - Actualizar CHANGELOG en cada release
   - Documentar breaking changes

4. **Seguridad:**
   - Revisar Security Advisory periodicamente
   - Aplicar parches de Drupal core
   - Realizar auditorías regulares

---

## Comandos Útiles Post-Publicación

```bash
# Crear nueva rama para desarrollo
git checkout -b 2.0.x

# Crear hotfix branch
git checkout -b 1.0-hotfix

# Crear release tag
git tag -a 1.0.1 -m "Release 1.0.1 - Bugfixes"
git push drupal --tags

# Actualizar composer.json en Drupal.org
# (Automático al hacer push, pero verificar)
composer require drupal/admin_notifications:^1.0

# Ver estadísticas del módulo
# En Drupal.org: proyecto > Usage statistics

# Exportar issues para análisis
# En Drupal.org: proyecto > Issues > Export

# Verificar automated tests
# En Drupal.org: proyecto > Testing > Test results
```

---

## Timeline Estimado Total

| Etapa | Duración | Acumulado |
|-------|----------|-----------|
| 1. Validación y Corrección | 4-6 horas | 5 horas |
| 2. Archivos de Configuración | 2-3 horas | 8 horas |
| 3. Documentación | 3-4 horas | 11.5 horas |
| 4. Unit Tests | 6-8 horas | 18.5 horas |
| 5. Kernel Tests | 6-8 horas | 25.5 horas |
| 6. Functional Tests | 8-10 horas | 35 horas |
| 7. GitLab CI | 3-4 horas | 38.5 horas |
| 8. Optimizaciones | 4-6 horas | 43.5 horas |
| 9. Seguridad | 4-6 horas | 48.5 horas |
| 10. Publicación | 3-4 horas | 52 horas |
| **TOTAL** | **43-59 horas** | **~52 horas promedio** |

**Distribución recomendada:**
- Semana 1: Etapas 1-3 (Validación, Config, Docs) - 12 horas
- Semana 2: Etapas 4-5 (Unit y Kernel Tests) - 14 horas
- Semana 3: Etapa 6 (Functional Tests) - 9 horas
- Semana 4: Etapas 7-8 (CI y Optimizaciones) - 8 horas
- Semana 5: Etapas 9-10 (Seguridad y Publicación) - 9 horas

**Total: 5 semanas trabajando ~10 horas/semana**

---

## Notas Importantes

1. **No Omitir Etapas Críticas:**
   - Etapas marcadas como "Crítico" son obligatorias para Drupal.org
   - Etapas "Importante" son altamente recomendadas

2. **Testing es Obligatorio:**
   - Drupal.org requiere al menos tests básicos
   - Automated testing debe pasar antes de release

3. **Documentación es Clave:**
   - README.md es lo primero que ven los usuarios
   - API.md ayuda a desarrolladores a extender el módulo

4. **Seguridad es Prioridad #1:**
   - Cualquier vulnerabilidad puede resultar en retiro del módulo
   - Seguir Security Policy estrictamente

5. **Semantic Versioning:**
   - MAJOR.MINOR.PATCH
   - MAJOR: Breaking changes
   - MINOR: New features, backwards compatible
   - PATCH: Bugfixes, backwards compatible

6. **Soporte de la Comunidad:**
   - Responder issues rápidamente
   - Ser cortés y profesional
   - Agradecer contribuciones

---

## Recursos Adicionales

- **Drupal.org Documentation:**
  - https://www.drupal.org/docs/develop/git
  - https://www.drupal.org/docs/develop/coding-standards
  - https://www.drupal.org/docs/develop/automated-testing

- **Herramientas:**
  - Coder: https://www.drupal.org/project/coder
  - Drupal Check: https://github.com/mglaman/drupal-check
  - PHPStan Drupal: https://github.com/mglaman/phpstan-drupal

- **Comunidad:**
  - Drupal Slack: https://www.drupal.org/slack
  - IRC: #drupal-contribute en Libera.Chat
  - Grupos Locales: https://www.drupal.org/community/user-groups

---

**Última actualización:** 2025-01-04
**Versión del plan:** 1.0
**Módulo:** Admin Notifications
**Target:** Drupal.org Official Repository
