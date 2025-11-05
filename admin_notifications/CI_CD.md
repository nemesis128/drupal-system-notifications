# CI/CD Pipeline Documentation

Este módulo utiliza GitLab CI/CD para automatizar pruebas y validaciones de calidad.

## Pipeline Stages

El pipeline está organizado en 4 etapas:

### 1. Prepare (Preparación)

**Job: install_dependencies**
- Instala Composer y dependencias PHP
- Instala herramientas de desarrollo (Coder, PHPStan, Drupal Check)
- Genera artefactos para los siguientes jobs
- Cache de dependencias para optimizar tiempo

### 2. Validate (Validación)

**Job: validate_composer**
- Valida la estructura del `composer.json`
- Verifica que no haya errores de sintaxis
- Asegura que las dependencias sean válidas

**Job: phpcs (PHP CodeSniffer)**
- Valida estándares de código Drupal
- Revisa archivos en `src/` y archivos del módulo
- Falla si encuentra violaciones a los estándares
- Rulesets: Drupal, DrupalPractice

**Job: eslint (JavaScript Linting)**
- Valida código JavaScript
- Revisa archivos en `js/`
- Asegura consistencia en el código JS

**Job: phpstan (Static Analysis)**
- Análisis estático de PHP
- Detecta errores potenciales sin ejecutar código
- Nivel 2 de análisis
- Permite fallos (allow_failure: true)

**Job: drupal_check**
- Detecta código deprecated de Drupal
- Ayuda a mantener compatibilidad con futuras versiones
- Permite fallos (allow_failure: true)

**Job: yaml_lint**
- Valida sintaxis de archivos YAML
- Revisa archivos de configuración
- Asegura formato consistente

**Job: check_permissions**
- Verifica permisos de archivos
- Detecta archivos PHP ejecutables (problema de seguridad)
- Detecta archivos con permisos de escritura incorrectos

### 3. Test (Pruebas)

**Job: unit_tests**
- Ejecuta tests unitarios con PHPUnit
- Genera reporte de cobertura
- Crea artefactos JUnit para GitLab
- No requiere base de datos

**Job: kernel_tests**
- Ejecuta tests Kernel con PHPUnit
- Requiere MySQL 8.0
- Instala extensiones PHP necesarias (PDO, GD)
- Permite fallos (requiere instalación completa de Drupal)

### 4. Security (Seguridad)

**Job: security_check**
- Audita dependencias de Composer
- Detecta vulnerabilidades conocidas
- Usa `composer audit`
- Permite fallos (allow_failure: true)

## Variables de Entorno

```yaml
COMPOSER_CACHE_DIR: Cache de Composer
MYSQL_DATABASE: Nombre de base de datos para tests
MYSQL_ROOT_PASSWORD: Contraseña de MySQL
SIMPLETEST_BASE_URL: URL base para tests funcionales
SIMPLETEST_DB: Conexión a base de datos para tests
```

## Caché

El pipeline cachea las siguientes carpetas:
- `vendor/` - Dependencias PHP
- `node_modules/` - Dependencias JavaScript
- Clave de caché: `${CI_COMMIT_REF_SLUG}` (única por rama)

## Workflow Rules

El pipeline se ejecuta solo en:
- Merge Requests
- Push a rama `main`
- Tags (releases)

## Requisitos para GitLab

### Runners

El pipeline requiere runners con:
- Docker executor
- Acceso a Docker Hub para imágenes
- Tag: `docker`

### Servicios

Para Kernel tests:
- MySQL 8.0 como servicio

### Variables GitLab CI/CD

No se requieren variables adicionales. Todas las variables están definidas en el archivo `.gitlab-ci.yml`.

## Artefactos Generados

1. **Vendor directory** (1 hora)
   - Dependencias instaladas
   - Compartido entre jobs

2. **PHPUnit Reports** (1 semana)
   - Reporte JUnit XML
   - Cobertura de código
   - Path: `coverage/`

## Badges Disponibles

Puedes agregar badges a tu README.md:

```markdown
[![Pipeline Status](https://gitlab.com/YOUR_GROUP/admin_notifications/badges/main/pipeline.svg)](https://gitlab.com/YOUR_GROUP/admin_notifications/-/commits/main)

[![Coverage](https://gitlab.com/YOUR_GROUP/admin_notifications/badges/main/coverage.svg)](https://gitlab.com/YOUR_GROUP/admin_notifications/-/commits/main)
```

## Comandos Locales

Para ejecutar las mismas validaciones localmente:

### Coding Standards
```bash
vendor/bin/phpcs --standard=Drupal,DrupalPractice src/
vendor/bin/phpcbf --standard=Drupal,DrupalPractice src/
```

### Static Analysis
```bash
vendor/bin/phpstan analyse src/ --level=2
vendor/bin/drupal-check src/
```

### Tests
```bash
vendor/bin/phpunit --testsuite unit
```

### Security Audit
```bash
composer audit
```

### JavaScript Linting
```bash
npx eslint js/*.js
```

## Troubleshooting

### Job falla: "Class not found"

Asegúrate que `install_dependencies` completó correctamente y que los artefactos están disponibles.

### Kernel tests fallan

Los Kernel tests requieren una instalación completa de Drupal. Están configurados con `allow_failure: true` porque no se pueden ejecutar en un entorno CI aislado sin Drupal completo.

### PHPStan reporta muchos errores

PHPStan está en nivel 2. Si es muy estricto, puedes:
- Bajar el nivel: `--level=1`
- Cambiar `allow_failure: false` si quieres que falle el pipeline

### Cache no funciona

Verifica que los runners tengan caché habilitado. Puedes limpiar el caché desde GitLab UI: CI/CD > Pipelines > Clear runner caches.

## Optimizaciones

### Parallel Jobs

Puedes ejecutar algunos jobs en paralelo:

```yaml
phpcs:
  parallel:
    matrix:
      - DIRECTORY: [src/, .]
```

### Reducir tiempo de pipeline

1. Aumentar tamaño de caché
2. Usar imágenes Docker pre-construidas con dependencias
3. Reducir artefactos innecesarios
4. Ejecutar solo jobs relevantes según archivos modificados

## Integración con Drupal.org

Cuando publiques en Drupal.org:

1. El proyecto usará el sistema de CI de Drupal.org
2. Tu `.gitlab-ci.yml` puede servir como referencia
3. Drupal.org ejecuta sus propios tests automáticamente

## Recursos Adicionales

- [GitLab CI/CD Documentation](https://docs.gitlab.com/ee/ci/)
- [Drupal CI Documentation](https://www.drupal.org/docs/develop/git/using-gitlab-to-contribute-to-drupal/gitlab-ci)
- [PHPUnit in Drupal](https://www.drupal.org/docs/automated-testing/phpunit-in-drupal)
