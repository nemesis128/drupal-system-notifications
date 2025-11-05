# Requisitos Oficiales para Publicar Módulos en Drupal.org

**Documento de Referencia Completo**
**Versión:** 1.0
**Fecha:** Enero 2025
**Última actualización:** 2025-01-04

---

## 📋 Tabla de Contenidos

1. [Introducción](#introducción)
2. [Requisitos de Documentación](#requisitos-de-documentación)
3. [Estándares de Código](#estándares-de-código)
4. [Estructura de Archivos](#estructura-de-archivos)
5. [Requisitos de Seguridad](#requisitos-de-seguridad)
6. [Requisitos de Testing](#requisitos-de-testing)
7. [Página del Proyecto](#página-del-proyecto)
8. [Repositorio Git](#repositorio-git)
9. [Requisitos de Comunidad](#requisitos-de-comunidad)
10. [Herramientas de Validación](#herramientas-de-validación)
11. [Razones Comunes de Rechazo](#razones-comunes-de-rechazo)
12. [Checklist Completo](#checklist-completo)
13. [Comandos y Herramientas](#comandos-y-herramientas)
14. [Enlaces de Referencia](#enlaces-de-referencia)

---

## Introducción

Este documento contiene TODOS los requisitos oficiales para publicar un módulo
en Drupal.org, basado en la investigación exhaustiva de la documentación
oficial de Drupal.org, incluyendo:

- Requisitos obligatorios (sin estos, rechazo automático)
- Requisitos recomendados (aumentan probabilidad de aprobación)
- Mejores prácticas de la comunidad
- Herramientas de validación automatizada
- Proceso completo de aplicación

**IMPORTANTE:** Drupal.org tiene un proceso de revisión estricto. Seguir estos
requisitos no garantiza aprobación inmediata, pero minimiza rechazos y acelera
el proceso.

---

## Requisitos de Documentación

### 1. README.md (OBLIGATORIO)

**Ubicación:** Raíz del módulo
**Formato:** Markdown (.md) preferido sobre .txt
**Line Wrap:** Hard-wrap a 80 caracteres
**Line Endings:** Unix-style (\n) únicamente

#### Estructura Requerida:

```markdown
# [Nombre del Proyecto]

## Introduction

Breve descripción del módulo (repetir el synopsis de la página del proyecto
en drupal.org). 2-3 párrafos máximo.

## Requirements

Esta sección lista los módulos requeridos fuera de Drupal core.

* Module1 (https://www.drupal.org/project/module1)
* Module2 (https://www.drupal.org/project/module2)

SI NO HAY DEPENDENCIAS:
"This module requires no modules outside of Drupal core."

## Installation

Install as you would normally install a contributed Drupal module. Visit
https://www.drupal.org/node/1897420 for further information.

## Configuration

1. Navigate to Administration > Configuration > [Section] > [Module Settings]
2. Configure [specific settings]
3. Assign permissions at Administration > People > Permissions

[Instrucciones detalladas de configuración]

## Maintainers

* Current maintainer: [username] - https://www.drupal.org/u/[username]
* Original author: [username] - https://www.drupal.org/u/[username]
```

#### Secciones Opcionales (pero recomendadas):

- **Table of Contents** - Para README largos (>500 líneas)
- **Recommended modules** - Módulos que mejoran funcionalidad
- **Troubleshooting & FAQ** - Problemas comunes
- **Features** - Lista detallada de funcionalidades
- **Known Issues** - Problemas conocidos sin solución
- **Roadmap** - Planes futuros de desarrollo

**Plantilla Oficial:**
https://www.drupal.org/docs/develop/managing-a-drupalorg-theme-module-or-distribution-project/documenting-your-project/readmemd-template

---

### 2. {module_name}.info.yml (OBLIGATORIO)

**Ubicación:** Raíz del módulo
**Nombre:** Debe coincidir exactamente con el machine name del módulo

#### Campos Requeridos:

```yaml
name: 'Human Readable Module Name'
type: module
core_version_requirement: ^9 || ^10 || ^11
```

#### Campos Importantes:

```yaml
description: 'Brief description shown in admin UI (max 255 characters)'
package: 'Category Name'
dependencies:
  - drupal:module_name
  - project_name:module_name
configure: module_name.settings
```

#### Reglas de Formato:

- Sin espacios antes de dos puntos, un espacio después
- Machine names en minúsculas
- Debe comenzar con letra
- Solo alfanuméricos y guiones bajos

#### IMPORTANTE - Campo "version":

**❌ NO INCLUIR** el campo `version:` en .info.yml

El sistema de packaging de Drupal.org lo agrega automáticamente. Si lo
incluyes, causará conflictos.

**Referencia:**
https://www.drupal.org/docs/develop/coding-standards/drupal-coding-standards#s-infofiles

---

### 3. LICENSE.txt (AUTOMÁTICO)

**NO INCLUIR** en tu repositorio. Drupal.org lo agrega automáticamente.

- **Licencia obligatoria:** GPL-2.0-or-later
- TODO el código derivado debe ser GPL-2.0-or-later
- Ninguna parte puede usar licencias incompatibles con GPL

**Nota:** Si usas librerías de terceros vía Composer, verifica que sean
compatibles con GPL.

---

### 4. INSTALL.txt o INSTALL.md (OPCIONAL)

Usar si las instrucciones de instalación son muy extensas para README.

**Contenido sugerido:**
- Requisitos del sistema
- Dependencias especiales
- Pasos de instalación detallados
- Configuración inicial obligatoria
- Tareas post-instalación
- FAQ de instalación

---

### 5. CHANGELOG.txt (OPCIONAL pero recomendado)

Documenta cambios entre versiones.

**Formato sugerido:**

```
Module Name 2.0.0, 2025-01-15
-----------------------------
- [Feature] Added new notification types
- [Improvement] Optimized database queries
- [Fix] Fixed XSS vulnerability in message display
- [Breaking] Removed deprecated API functions

Module Name 1.5.0, 2024-12-01
-----------------------------
- [Feature] Added multi-language support
- [Fix] Fixed polling issue in IE11
```

---

## Estándares de Código

### 1. PHP Coding Standards (ESTRICTOS)

**Estándar:** Drupal Coding Standards
**Herramienta:** PHP_CodeSniffer con ruleset Drupal y DrupalPractice

#### Reglas Clave:

**Indentación:**
- 2 espacios (NO tabs)
- Sin espacios al final de líneas

**Naming Conventions:**
- Variables: `$snake_case`
- Funciones: `snake_case()`
- Clases: `PascalCase`
- Constants: `SCREAMING_SNAKE_CASE`
- Namespaces: PSR-4 `Drupal\module_name\`

**DocBlocks:**
```php
/**
 * Brief description (one line).
 *
 * Detailed description (if needed).
 *
 * @param string $parameter
 *   Description of parameter.
 * @param int $another
 *   Another parameter description.
 *
 * @return bool
 *   Description of return value.
 *
 * @throws \Exception
 *   When X condition occurs.
 */
public function myFunction($parameter, $another) {
  // Implementation.
}
```

**Arrays:**
```php
// Short array syntax (NOT array())
$items = [
  'key1' => 'value1',
  'key2' => 'value2',
];
```

**Strings:**
```php
// Use single quotes when possible
$text = 'Simple string';
// Double quotes only for variables
$text = "Hello $name";
// Complex: use concatenation
$text = 'Hello ' . $name . '!';
```

**Control Structures:**
```php
// Space after keyword, brace on same line
if ($condition) {
  // Code.
}
elseif ($other_condition) {
  // Code.
}
else {
  // Code.
}

// Same for loops
foreach ($items as $key => $value) {
  // Code.
}
```

**Referencia Completa:**
https://www.drupal.org/docs/develop/standards/php

---

### 2. CSS Coding Standards

**Metodología:** BEM (Block Element Modifier) recomendada

**Reglas:**
- Selectores en minúsculas con guiones
- 2 espacios de indentación
- Una declaración por línea
- Espacio después de dos puntos
- Punto y coma al final

**Ejemplo:**
```css
.block-name {
  property: value;
  another-property: value;
}

.block-name__element {
  property: value;
}

.block-name--modifier {
  property: value;
}
```

**Referencia:**
https://www.drupal.org/docs/develop/standards/css

---

### 3. JavaScript Coding Standards

**Estándar:** Airbnb JavaScript Style Guide (desde Drupal 8.4+)
**Herramienta:** ESLint

**Reglas Clave:**
- Use strict mode
- Declaraciones con const/let (NO var)
- Arrow functions cuando sea apropiado
- Template literals para strings con variables
- Drupal.behaviors para inicialización

**Ejemplo:**
```javascript
(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.myModule = {
    attach: function (context, settings) {
      // Use once() to ensure code runs only once
      $(context).find('.my-selector').once('myModule').each(function () {
        // Your code here
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
```

**Referencia:**
https://www.drupal.org/docs/develop/standards/javascript

---

### 4. Mejores Prácticas de Código

#### Dependency Injection (OBLIGATORIO en clases)

**❌ MAL:**
```php
public function myMethod() {
  $database = \Drupal::database();
  $config = \Drupal::config('my_module.settings');
}
```

**✅ BIEN:**
```php
public function __construct(Connection $database, ConfigFactoryInterface $config_factory) {
  $this->database = $database;
  $this->configFactory = $config_factory;
}

public static function create(ContainerInterface $container) {
  return new static(
    $container->get('database'),
    $container->get('config.factory')
  );
}
```

#### API Pública vs Privada

- Solo usar clases públicas de API
- NO usar clases marcadas como `@internal`
- NO depender de implementación interna

#### Código Deprecado

**PROHIBIDO** usar funciones/clases deprecadas.

**Herramienta:** drupal-check identifica uso de código deprecado

---

## Estructura de Archivos

### Estructura Típica de Módulo:

```
module_name/
├── .gitignore
├── .gitlab-ci.yml
├── .phpcs.xml.dist
├── composer.json
├── phpstan.neon
├── README.md
├── CHANGELOG.txt
├── module_name.info.yml
├── module_name.module
├── module_name.install
├── module_name.routing.yml
├── module_name.services.yml
├── module_name.permissions.yml
├── module_name.links.menu.yml
├── module_name.libraries.yml
├── config/
│   ├── install/
│   │   └── module_name.settings.yml
│   └── schema/
│       └── module_name.schema.yml
├── src/
│   ├── Controller/
│   │   └── MyController.php
│   ├── Form/
│   │   ├── SettingsForm.php
│   │   └── ContentForm.php
│   ├── Plugin/
│   │   └── Block/
│   │       └── MyBlock.php
│   └── Service/
│       └── MyService.php
├── templates/
│   └── my-template.html.twig
├── css/
│   └── module-name.css
├── js/
│   └── module-name.js
├── tests/
│   └── src/
│       ├── Unit/
│       │   └── MyUnitTest.php
│       ├── Kernel/
│       │   └── MyKernelTest.php
│       ├── Functional/
│       │   └── MyFunctionalTest.php
│       └── FunctionalJavascript/
│           └── MyJsTest.php
└── translations/
    ├── es.po
    └── fr.po
```

### Convenciones de Nombres

**Machine Name del Módulo:**
- Solo letras minúsculas
- Debe comenzar con letra
- Solo alfanuméricos y guiones bajos
- Sin guiones
- Ejemplos: `admin_notify`, `custom_alerts`

**Archivos de Clase:**
- PSR-4: Clase `MyController` en archivo `MyController.php`
- Ubicación según namespace
- Ejemplo: `Drupal\module_name\Controller\MyController`
  → `src/Controller/MyController.php`

---

### composer.json (OBLIGATORIO si tienes dependencias)

```json
{
  "name": "drupal/module_name",
  "description": "Brief module description",
  "type": "drupal-module",
  "license": "GPL-2.0-or-later",
  "minimum-stability": "dev",
  "require": {
    "drupal/core": "^9.5 || ^10 || ^11"
  },
  "require-dev": {
    "drupal/coder": "^8.3",
    "mglaman/drupal-check": "^1.4",
    "phpstan/phpstan": "^1.10"
  }
}
```

**Reglas:**
- `name` DEBE ser `drupal/{machine_name}`
- `type` DEBE ser `drupal-module`
- `license` DEBE ser `GPL-2.0-or-later`
- Declarar TODAS las dependencias de módulos contrib

---

## Requisitos de Seguridad

### 1. Security Advisory Coverage

**Pre-requisitos para aplicar:**
- Proyecto completo (NO sandbox)
- Mantenedor verificado con acceso VCS
- Sin problemas de seguridad abiertos en issue queue
- Esperar 10 días después de crear proyecto
- Código suficiente para demostrar competencia

**Proceso:**
1. Crear issue en https://www.drupal.org/project/issues/projectapplications
2. Enlazar a tu proyecto
3. Establecer status "Needs review"
4. Esperar revisión (puede tardar semanas/meses)
5. Responder a feedback de revisores

**Review Bonus Program (RECOMENDADO):**
- Revisar 3 aplicaciones de otros desarrolladores
- Agregar enlaces a tus revisiones en tu aplicación
- Tag: "PAreview: review bonus"
- Resultado: Proceso MÁS RÁPIDO (semanas en lugar de meses)

---

### 2. Vulnerabilidades Comunes (PROHIBIDAS)

#### SQL Injection

**❌ NUNCA hacer esto:**
```php
$query = "SELECT * FROM {table} WHERE id = " . $_GET['id'];
$result = db_query($query);
```

**✅ SIEMPRE hacer esto:**
```php
$query = $database->select('table', 't')
  ->fields('t')
  ->condition('id', $request->query->get('id'))
  ->execute();
```

**Reglas:**
- SIEMPRE usar Database API
- SIEMPRE usar placeholders con nombre (`:placeholder`)
- NUNCA concatenar input de usuario en SQL
- NUNCA confiar en entrada de usuario

---

#### Cross-Site Scripting (XSS)

**❌ NUNCA hacer esto:**
```php
print $_GET['name'];
echo '<div>' . $user_input . '</div>';
```

**✅ SIEMPRE hacer esto:**
```php
// En templates Twig (auto-escape)
{{ user_input }}

// En PHP
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Xss;

// Para texto plano
$safe = Html::escape($user_input);

// Para HTML permitido
$safe = Xss::filter($user_input);

// En t()
$this->t('Hello @name', ['@name' => $user_input]); // @ escapa
$this->t('Welcome %name', ['%name' => $user_input]); // % placeholder
```

**Reglas:**
- Twig auto-escapa por defecto (¡usar Twig!)
- Usar `Html::escape()` para texto plano
- Usar `Xss::filter()` para HTML permitido
- Usar placeholders @ o % en `t()`
- NUNCA `|raw` en Twig sin validación

---

#### Cross-Site Request Forgery (CSRF)

**✅ Form API protege automáticamente:**
```php
// Form API incluye token CSRF automáticamente
$form['submit'] = [
  '#type' => 'submit',
  '#value' => $this->t('Submit'),
];
```

**Para URLs no-form:**
```php
use Drupal\Core\Url;

$url = Url::fromRoute('mymodule.action', [], [
  'query' => [
    'token' => \Drupal::csrfToken()->get('mymodule_action'),
  ],
]);

// Validar en controller:
if (!\Drupal::csrfToken()->validate($request->query->get('token'), 'mymodule_action')) {
  throw new AccessDeniedHttpException();
}
```

---

#### Validación de URLs

**❌ NUNCA confiar en URLs de usuario:**
```php
header('Location: ' . $_GET['destination']);
```

**✅ SIEMPRE validar:**
```php
use Drupal\Component\Utility\UrlHelper;

$url = $request->query->get('destination');
$url = UrlHelper::stripDangerousProtocols($url);
// Verificar que sea URL interna o permitida
```

---

#### File Uploads

**✅ Validar siempre:**
```php
// Usar File API de Drupal
$validators = [
  'file_validate_extensions' => ['jpg png gif'],
  'file_validate_size' => [5 * 1024 * 1024], // 5MB
];

$file = file_save_upload('file_field', $validators);
```

**Reglas:**
- Validar extensiones
- Validar tamaño
- NO confiar en MIME type del cliente
- Guardar fuera de webroot si es posible

---

### 3. Documentación de Seguridad

**Referencia Oficial:**
https://www.drupal.org/docs/administering-a-drupal-site/security-in-drupal/writing-secure-code-for-drupal

**Checklist de Seguridad:**
- [ ] Sin SQL injection
- [ ] Sin XSS
- [ ] CSRF protegido
- [ ] URLs validadas
- [ ] Archivos subidos validados
- [ ] Permisos verificados en todas las rutas
- [ ] Inputs sanitizados
- [ ] Outputs escapados
- [ ] Secrets no committeados
- [ ] Sin eval() o funciones peligrosas

---

## Requisitos de Testing

### 1. Tipos de Tests

Testing NO es obligatorio, pero es ALTAMENTE RECOMENDADO.

#### Unit Tests

**Base Class:** `Drupal\Tests\UnitTestCase`
**Ubicación:** `tests/src/Unit/`
**Propósito:** Probar lógica aislada sin Drupal

**Ejemplo:**
```php
namespace Drupal\mymodule\Tests\Unit;

use Drupal\Tests\UnitTestCase;

class MyClassTest extends UnitTestCase {

  public function testMyMethod() {
    $obj = new MyClass();
    $result = $obj->myMethod('input');
    $this->assertEquals('expected', $result);
  }

}
```

---

#### Kernel Tests

**Base Class:** `Drupal\KernelTests\KernelTestBase`
**Ubicación:** `tests/src/Kernel/`
**Propósito:** Probar con kernel de Drupal bootstrapped

**Ejemplo:**
```php
namespace Drupal\mymodule\Tests\Kernel;

use Drupal\KernelTests\KernelTestBase;

class MyKernelTest extends KernelTestBase {

  protected static $modules = ['mymodule', 'system'];

  protected function setUp(): void {
    parent::setUp();
    $this->installSchema('mymodule', ['mymodule_table']);
  }

  public function testDatabaseOperation() {
    // Test database operations
  }

}
```

---

#### Functional Tests (MUY RECOMENDADO)

**Base Class:** `Drupal\Tests\BrowserTestBase`
**Ubicación:** `tests/src/Functional/`
**Propósito:** Probar con Drupal completo + navegador simulado

**Ejemplo:**
```php
namespace Drupal\mymodule\Tests\Functional;

use Drupal\Tests\BrowserTestBase;

class MyFunctionalTest extends BrowserTestBase {

  protected static $modules = ['mymodule'];

  protected $defaultTheme = 'stark';

  public function testModuleInstallation() {
    $this->drupalLogin($this->rootUser);
    $this->drupalGet('/admin/config/mymodule');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('My Module Settings');
  }

}
```

---

#### FunctionalJavascript Tests

**Base Class:** `Drupal\FunctionalJavascriptTests\WebDriverTestBase`
**Ubicación:** `tests/src/FunctionalJavascript/`
**Propósito:** Probar AJAX y JavaScript

**Requiere:** ChromeDriver o PhantomJS

---

### 2. Configuración de Testing

**Instalar dependencias:**
```bash
composer require --dev drupal/core-dev
```

**Variables de entorno:**
```bash
export SIMPLETEST_BASE_URL=http://localhost
export SIMPLETEST_DB=mysql://user:pass@localhost/dbname
export BROWSERTEST_OUTPUT_DIRECTORY=/path/to/output
```

**Ejecutar tests:**
```bash
# Test específico
./vendor/bin/phpunit modules/custom/mymodule/tests/src/Unit/MyTest.php

# Todos los tests del módulo
./vendor/bin/phpunit modules/custom/mymodule

# Con coverage
./vendor/bin/phpunit --coverage-html reports modules/custom/mymodule
```

---

### 3. GitLab CI (ALTAMENTE RECOMENDADO)

Drupal.org usa GitLab CI (DrupalCI fue deprecado julio 2024).

**Crear `.gitlab-ci.yml`:**
```yaml
include:
  - project: $_GITLAB_TEMPLATES_REPO
    ref: $_GITLAB_TEMPLATES_REF
    file:
      - '/includes/include.drupalci.main.yml'
      - '/includes/include.drupalci.variables.yml'
      - '/includes/include.drupalci.workflows.yml'

variables:
  _TARGET_PHP: "8.3"
  _TARGET_CORE: "10.x"

phpcs:
  stage: validate
  extends: .phpcs-standards

phpunit:
  stage: test
  extends: .phpunit-tests
```

**Referencia:**
https://www.drupal.org/docs/develop/git/using-gitlab-to-contribute-to-drupal/gitlab-ci

---

## Página del Proyecto

### Información Obligatoria:

1. **Project Name** - Claro y descriptivo
2. **Short Description** - 1-2 líneas (aparece en búsquedas)
3. **Full Description** - 3-4 párrafos detallados
4. **Maintenance Status:**
   - Actively maintained
   - Minimally maintained
   - Seeking new maintainer
   - Unsupported

5. **Development Status:**
   - Under active development
   - Maintenance fixes only
   - Obsolete

6. **Module Categories** (máximo 3)
7. **Supported Drupal Versions**

---

### Información Recomendada:

- **Screenshots** - Mostrar UI y funcionalidad
- **Documentation Links** - Enlazar a docs externas
- **Demo Site** - Si está disponible
- **Similar Modules** - Reconocer alternativas, explicar diferencias
- **Use Cases** - Cuándo usar este módulo
- **Roadmap** - Planes futuros
- **Issue Queue** - Activar y monitorear

---

## Repositorio Git

### 1. Branch Naming

**Formato Moderno (Drupal 9+):**
```
1.x          - Desarrollo de versión major 1
2.x          - Desarrollo de versión major 2
2.0.x        - Desarrollo de versión minor 2.0
{major}.x    - Patrón general
```

**Formato Legacy (Drupal 8-):**
```
8.x-1.x      - Drupal 8, major version 1
7.x-1.x      - Drupal 7, major version 1
{core}-{major}.x
```

**IMPORTANTE:**
- ❌ NO usar "master" como branch principal
- ✅ Usar branches de versión

---

### 2. Version Numbering

**Semantic Versioning (recomendado):**
```
{major}.{minor}.{patch}[-{stability}{number}]

Ejemplos:
1.0.0
2.1.3
3.0.0-alpha1
1.5.0-beta2
2.0.0-rc1
```

**Stability Suffixes:**
- `-alpha{N}` - Alpha (empezar en 1)
- `-beta{N}` - Beta (empezar en 1)
- `-rc{N}` - Release candidate (empezar en 1)
- Sin suffix = Stable release

---

### 3. Release Tags

**Crear release:**
```bash
# Crear tag
git tag -a 1.0.0 -m "Release 1.0.0 - Initial stable release"

# Push a drupal.org
git push origin 1.0.0
```

**Reglas:**
- Tag debe tener los 3 componentes: `{major}.{minor}.{patch}`
- Tag name debe coincidir con version string
- Drupal.org crea el release automáticamente del tag

---

### 4. Commit Messages

**Formato recomendado:**
```
Issue #1234567: Brief description of change

More detailed explanation if needed. Explain WHY the change was made,
not just WHAT changed.

This can span multiple paragraphs.
```

**Referencias:**
- Siempre referenciar issue number si existe
- Ser descriptivo pero conciso
- Explicar el "por qué" no solo el "qué"

---

### 5. .gitignore

**Ejemplo recomendado:**
```gitignore
# Composer
/vendor/

# IDE
/.idea/
/.vscode/
*.swp
*.swo
*~

# OS
.DS_Store
Thumbs.db

# Testing
/test-reports/
/.phpunit.result.cache

# Build
/node_modules/
/dist/
```

---

## Requisitos de Comunidad

### 1. Issue Queue

**Responsabilidades:**
- Responder en tiempo razonable
- Ser profesional y cortés
- Verificar que issues sean válidos
- Triage apropiado (tags, status, priority)
- Mover a status correctos

**Etiqueta:**
- NO publicar en issues cerrados
- Buscar antes de crear duplicados
- Probar en versión más reciente primero
- Proveer pasos claros de reproducción
- Incluir información del ambiente

---

### 2. Documentación

**Mantener actualizado:**
- README.md
- Página del proyecto
- Documentar cambios de configuración
- Proveer upgrade paths
- Documentar cambios de API

---

### 3. Soporte

- Animar support requests en issue queue
- Ayudar a usuarios con troubleshooting
- Mensajes de error claros
- Documentar issues comunes en FAQ

---

### 4. Mantenedor

**Responsabilidades:**
- Monitorear issue queue regularmente
- Revisar y commitear patches
- Crear releases
- Responder a security issues INMEDIATAMENTE
- Considerar co-mantenedores para proyectos grandes

---

### 5. Co-Maintaining

**Cómo contribuir:**
- Participar activamente en issue queue
- Proveer patches y reviews
- Ayudar con support requests
- Discusiones técnicas constructivas

**Ganar commit access:**
- Contribuciones consistentes y de calidad
- Demostrar conocimiento del módulo
- Ser miembro activo de la comunidad

---

## Herramientas de Validación

### 1. PHP_CodeSniffer + Coder

**Instalación:**
```bash
composer require --dev drupal/coder
composer require --dev dealerdirect/phpcodesniffer-composer-installer
```

**Uso:**
```bash
# Verificar Drupal standards
./vendor/bin/phpcs --standard=Drupal \
  --extensions=php,module,inc,install,test,profile,theme,css,info,txt,md,yml \
  modules/custom/mymodule

# Verificar best practices
./vendor/bin/phpcs --standard=DrupalPractice \
  --extensions=php,module,inc,install,test,profile,theme \
  modules/custom/mymodule

# Auto-fix
./vendor/bin/phpcbf --standard=Drupal \
  --extensions=php,module,inc,install \
  modules/custom/mymodule
```

**Configuración (.phpcs.xml.dist):**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<ruleset name="mymodule">
  <description>PHP_CodeSniffer configuration</description>
  <file>.</file>
  <arg name="extensions" value="php,module,inc,install,test,profile,theme,css,info,txt,md,yml"/>
  <exclude-pattern>*/vendor/*</exclude-pattern>
  <exclude-pattern>*/node_modules/*</exclude-pattern>
  <rule ref="Drupal"/>
  <rule ref="DrupalPractice"/>
</ruleset>
```

**Documentación:**
https://www.drupal.org/project/coder

---

### 2. Drupal-Check

**Instalación:**
```bash
composer require --dev mglaman/drupal-check
```

**Uso:**
```bash
# Verificar módulo completo
./vendor/bin/drupal-check modules/custom/mymodule

# Verificar archivo específico
./vendor/bin/drupal-check modules/custom/mymodule/src/MyClass.php

# Con deprecation messages
./vendor/bin/drupal-check -d modules/custom/mymodule
```

**Propósito:**
- Detecta uso de código deprecado
- Basado en PHPStan
- Esencial para upgrades de Drupal
- Identifica problemas de compatibilidad

**Documentación:**
https://github.com/mglaman/drupal-check

---

### 3. PHPStan

**Instalación:**
```bash
composer require --dev phpstan/phpstan
composer require --dev mglaman/phpstan-drupal
composer require --dev phpstan/extension-installer
```

**Configuración (phpstan.neon):**
```yaml
parameters:
  level: 6
  paths:
    - src
    - tests
  excludePaths:
    - */vendor/*
    - */node_modules/*
  scanDirectories:
    - web/core
    - web/modules/contrib
```

**Uso:**
```bash
./vendor/bin/phpstan analyse
```

**Levels:** 0-9 (9 es el más estricto)

---

### 4. ESLint (JavaScript)

**Instalación:**
```bash
npm install --save-dev eslint
npm install --save-dev eslint-config-airbnb
```

**Configuración (.eslintrc.json):**
```json
{
  "extends": "airbnb",
  "env": {
    "browser": true,
    "jquery": true
  },
  "globals": {
    "Drupal": true,
    "drupalSettings": true
  }
}
```

**Uso:**
```bash
npx eslint js/**/*.js
```

---

### 5. Security Review Module

**NO es para validación pre-publicación, pero útil en desarrollo.**

**Instalación:**
```bash
composer require drupal/security_review
drush en security_review
```

**Uso:**
```bash
drush secrev-run
```

**Checks:**
- File permissions
- Database configuration
- Module security
- Input validation
- Error reporting

---

### 6. Comandos de Validación Completos

**Script completo para validar módulo:**

```bash
#!/bin/bash

MODULE_PATH="modules/custom/mymodule"

echo "=== Running PHP CodeSniffer (Drupal) ==="
./vendor/bin/phpcs --standard=Drupal \
  --extensions=php,module,inc,install,test,profile,theme,css,info,txt,md,yml \
  $MODULE_PATH

echo ""
echo "=== Running PHP CodeSniffer (DrupalPractice) ==="
./vendor/bin/phpcs --standard=DrupalPractice \
  --extensions=php,module,inc,install,test,profile,theme \
  $MODULE_PATH

echo ""
echo "=== Running Drupal Check ==="
./vendor/bin/drupal-check $MODULE_PATH

echo ""
echo "=== Running PHPStan ==="
./vendor/bin/phpstan analyse $MODULE_PATH

echo ""
echo "=== Running PHPUnit ==="
./vendor/bin/phpunit $MODULE_PATH

echo ""
echo "=== Validation Complete ==="
```

---

## Razones Comunes de Rechazo

### Account Issues

1. ❌ **Shared Accounts**
   - Violates Terms of Service
   - Automatic rejection
   - Each person needs individual account

2. ❌ **Multiple Applications**
   - Only one application at a time
   - Wait for first to be processed

3. ❌ **Already Vetted**
   - Check if you already have vetted role
   - Don't need another application

---

### Code Issues

4. ❌ **Insufficient Code**
   - Too little code to demonstrate competency
   - Need substantial functionality

5. ❌ **Unsupported Drupal Version**
   - Must target currently supported versions
   - Drupal 8 EOL: November 2021
   - Drupal 9 EOL: November 2023

6. ❌ **Security Vulnerabilities**
   - SQL injection
   - XSS
   - CSRF issues
   - Unsanitized inputs

7. ❌ **Coding Standards Violations**
   - Must pass phpcs --standard=Drupal
   - Must pass phpcs --standard=DrupalPractice
   - NO exceptions

8. ❌ **Improper API Usage**
   - Using private/internal APIs
   - Using deprecated code
   - Not using Dependency Injection

9. ❌ **No DocBlocks**
   - Missing class documentation
   - Missing method documentation
   - Poor code comments

---

### Repository Issues

10. ❌ **Wrong Branch Name**
    - Using "master" instead of version branch
    - Use "1.x", "2.x", etc.

11. ❌ **Licensing Issues**
    - Wrong license
    - Non-GPL code included
    - GPL-incompatible dependencies

12. ❌ **Third-party Libraries Committed**
    - Libraries in repository instead of Composer
    - node_modules committed
    - vendor directory committed

---

### Documentation Issues

13. ❌ **Missing README**
    - No README.md file
    - README doesn't follow template
    - Missing required sections

14. ❌ **Poor Project Page**
    - Insufficient description
    - No use cases
    - No configuration instructions

15. ❌ **Invalid .info.yml**
    - Missing required fields
    - Includes version field (prohibited)
    - Wrong format

---

### Process Issues

16. ❌ **Wrong Status**
    - Application not set to "Needs review"
    - Wrong queue

17. ❌ **No Project Link**
    - Application doesn't link to project
    - Link is broken

18. ❌ **Sandbox Project**
    - Trying to get coverage for sandbox
    - Sandboxes deprecated

---

### Best Practices Violations

19. ❌ **Not Using Dependency Injection**
    - Over-reliance on \Drupal::service()
    - Static calls in classes

20. ❌ **Hard-coded Strings**
    - Not using t() for translations
    - English-only without translation support

21. ❌ **No Tests**
    - While not required, shows lower quality
    - Professional modules have tests

22. ❌ **Duplicate Functionality**
    - Module duplicates existing solution
    - No improvement over alternatives
    - No justification for duplication

---

## Checklist Completo

### Pre-Submission Checklist

#### Repository Setup
- [ ] Git repository on Drupal.org
- [ ] Proper branch naming (e.g., `1.x`, not `master`)
- [ ] No third-party libraries committed (use Composer)
- [ ] .gitignore configured
- [ ] LICENSE.txt NOT in repository (added by drupal.org)

#### Required Files
- [ ] `{module_name}.info.yml` with required keys
- [ ] NO `version:` field in .info.yml
- [ ] `README.md` with all required sections
- [ ] `README.md` hard-wrapped at 80 characters
- [ ] `composer.json` (if you have dependencies)
- [ ] `.gitlab-ci.yml` (highly recommended)
- [ ] `.phpcs.xml.dist` (recommended)
- [ ] `phpstan.neon` (recommended)

#### Code Quality
- [ ] Passes `phpcs --standard=Drupal`
- [ ] Passes `phpcs --standard=DrupalPractice`
- [ ] Passes `drupal-check` (no deprecated code)
- [ ] Passes `phpstan` analysis (level 6+)
- [ ] JavaScript passes ESLint
- [ ] Sufficient code volume
- [ ] Well-commented code
- [ ] Complete DocBlocks on all classes and public methods

#### Security
- [ ] No SQL injection vulnerabilities
- [ ] No XSS vulnerabilities
- [ ] CSRF protection implemented
- [ ] User input sanitized
- [ ] Output escaped
- [ ] File uploads validated
- [ ] URLs validated
- [ ] Permissions checked on all routes
- [ ] No secrets committed

#### Testing
- [ ] Unit tests written (if applicable)
- [ ] Kernel tests written (recommended)
- [ ] Functional tests written (highly recommended)
- [ ] Tests pass locally
- [ ] Tests pass in GitLab CI

#### Documentation
- [ ] README complete and accurate
- [ ] Project page detailed
- [ ] Code comments thorough
- [ ] Configuration documented
- [ ] CHANGELOG.txt (recommended)

#### Drupal Standards
- [ ] Proper namespacing (PSR-4)
- [ ] Dependency injection in classes
- [ ] Services defined in .services.yml
- [ ] Routes defined in .routing.yml
- [ ] Config schema defined
- [ ] Permissions defined in .permissions.yml
- [ ] Translation wrappers used (t(), @t)
- [ ] No use of deprecated code

---

### Security Advisory Coverage Application

#### Pre-Application
- [ ] Wait 10 days after creating full project
- [ ] Project is "full project" (not sandbox)
- [ ] No open security issues
- [ ] All pre-submission checklist complete
- [ ] Code demonstrates competency

#### Application Process
- [ ] Create issue in project applications queue
- [ ] Link to project
- [ ] Set status "Needs review"
- [ ] Consider Review Bonus (highly recommended)

#### Review Bonus (Optional)
- [ ] Review 3 applications with "Needs review" status
- [ ] Manual code review (not just automated)
- [ ] Add links to reviews in application
- [ ] Add tag "PAreview: review bonus"

---

## Comandos y Herramientas

### Setup Complete

```bash
# Development dependencies
composer require --dev drupal/core-dev
composer require --dev drupal/coder
composer require --dev mglaman/drupal-check
composer require --dev phpstan/phpstan
composer require --dev mglaman/phpstan-drupal
composer require --dev phpstan/extension-installer
composer require --dev dealerdirect/phpcodesniffer-composer-installer

# Testing setup
export SIMPLETEST_BASE_URL=http://localhost
export SIMPLETEST_DB=mysql://user:pass@localhost/db
export BROWSERTEST_OUTPUT_DIRECTORY=/tmp/browser_output
```

### Validation Commands

```bash
# Coding standards
./vendor/bin/phpcs --standard=Drupal \
  --extensions=php,module,inc,install,test,profile,theme,css,info,txt,md,yml \
  modules/custom/mymodule

./vendor/bin/phpcs --standard=DrupalPractice \
  --extensions=php,module,inc,install,test,profile,theme \
  modules/custom/mymodule

# Auto-fix
./vendor/bin/phpcbf --standard=Drupal \
  --extensions=php,module,inc,install \
  modules/custom/mymodule

# Deprecated code
./vendor/bin/drupal-check modules/custom/mymodule

# Static analysis
./vendor/bin/phpstan analyse modules/custom/mymodule

# Tests
./vendor/bin/phpunit modules/custom/mymodule

# With coverage
./vendor/bin/phpunit --coverage-html reports modules/custom/mymodule
```

### Git Commands

```bash
# Create version branch
git checkout -b 1.x

# Create release tag
git tag -a 1.0.0 -m "Release 1.0.0"
git push origin 1.0.0

# Semantic versioning migration
# If transitioning from 8.x-3.5, must become 4.0.0
```

---

## Enlaces de Referencia

### Official Drupal.org Documentation

1. **Project Management**
   https://www.drupal.org/node/239830

2. **Security Advisory Application**
   https://www.drupal.org/docs/develop/managing-a-drupalorg-theme-module-or-distribution-project/security-coverage/opting-into/application-checklist

3. **README Template**
   https://www.drupal.org/docs/develop/managing-a-drupalorg-theme-module-or-distribution-project/documenting-your-project/readmemd-template

4. **PHP Coding Standards**
   https://www.drupal.org/docs/develop/standards/php

5. **CSS Coding Standards**
   https://www.drupal.org/docs/develop/standards/css

6. **JavaScript Coding Standards**
   https://www.drupal.org/docs/develop/standards/javascript

7. **Writing Secure Code**
   https://www.drupal.org/docs/administering-a-drupal-site/security-in-drupal/writing-secure-code-for-drupal

8. **Release Naming Conventions**
   https://www.drupal.org/docs/develop/git/git-for-drupal-project-maintainers/release-naming-conventions

9. **PHPUnit Testing**
   https://www.drupal.org/docs/automated-testing/phpunit-in-drupal

10. **GitLab CI**
    https://www.drupal.org/docs/develop/git/using-gitlab-to-contribute-to-drupal/gitlab-ci

11. **Issue Queue Etiquette**
    https://www.drupal.org/docs/develop/issues/issue-procedures-and-etiquette

12. **Project Applications Queue**
    https://www.drupal.org/project/issues/projectapplications

### Community Resources

13. **Coder Module**
    https://www.drupal.org/project/coder

14. **Drupal-Check**
    https://github.com/mglaman/drupal-check

15. **Security Review Module**
    https://www.drupal.org/project/security_review

16. **GitLab CI Templates**
    https://gitlab.com/mog33/gitlab-ci-drupal

17. **Drupal API Reference**
    https://api.drupal.org/

---

## Notas Finales

### Tiempo Esperado

- **Sin Review Bonus:** 6-12 meses
- **Con Review Bonus:** 2-8 semanas
- **Review Bonus altamente recomendado**

### Tips de Éxito

1. **Calidad sobre velocidad** - Tómate el tiempo necesario
2. **Sigue los estándares religiosamente** - Sin excepciones
3. **Seguridad primero** - Es el #1 motivo de rechazo
4. **Documenta todo** - Mejor sobre-documentado
5. **Participa en la comunidad** - Ayuda a otros antes de aplicar
6. **Usa herramientas automatizadas** - Pero también revisa manual
7. **Escribe tests** - Demuestra profesionalismo
8. **Sé paciente** - El proceso toma tiempo
9. **Responde rápido** - A feedback de revisores
10. **Considera Review Bonus** - Acelera dramáticamente

### Recursos Adicionales

- **Drupal Slack:** https://drupal.slack.com
- **Drupal IRC:** #drupal-contribute en irc.libera.chat
- **Stack Overflow:** Tag `drupal`
- **Drupal Answers:** https://drupal.stackexchange.com

---

**FIN DEL DOCUMENTO**

Este documento contiene toda la información necesaria para preparar un módulo
para publicación en Drupal.org. Mantener como referencia durante todo el
proceso de desarrollo y aplicación.

**Última actualización:** 2025-01-04
**Versión:** 1.0
**Autor:** Investigación exhaustiva de documentación oficial Drupal.org
