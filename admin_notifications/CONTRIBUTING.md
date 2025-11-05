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
- **Slack:** #drupal-contribute en Drupal Slack
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
