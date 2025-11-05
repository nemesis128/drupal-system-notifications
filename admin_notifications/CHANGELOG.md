# Changelog

Todos los cambios notables en este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto adhiere a [Semantic Versioning](https://semver.org/lang/es/).

## [Unreleased]

### Preparando para 1.0.0
- Validación completa contra Drupal Coding Standards
- Implementación de Dependency Injection
- Implementación de suite de tests
- Documentación completa
- Revisión de seguridad

## [0.1.0] - 2025-01-04

### Añadido
- Sistema de notificaciones tipo toast (estilo Windows 10/11)
- Sistema de notificaciones tipo banner programables
- Polling en tiempo real cada 30 segundos (configurable)
- Configuración de duración, posición y sonido de notificaciones
- Sistema de roles y permisos granulares
- Panel administrativo para gestión de notificaciones
- Soporte multiidioma (Español, Inglés, Francés, Portugués, Japonés)
- Sistema de marcado de notificaciones leídas/no leídas
- Logging con Drupal Watchdog
- Dependency Injection en todos los Controllers y Forms
- Validación con phpcs, drupal-check, phpstan y eslint
- Configuración de ESLint para JavaScript
- Documentación completa (README, LOGGING, DRUPAL_ORG_REQUIREMENTS, IMPLEMENTATION_PLAN)

### Corregido
- Visibilidad del botón de eliminar (texto blanco sobre fondo rojo)
- Duración de toast respetando configuración (30 segundos)
- Sistema de polling ejecutándose correctamente cada 30 segundos
- Botón de cerrar visible en banners
- Ancho de banners sin causar scroll horizontal
- Aplicados Drupal Coding Standards en todo el código PHP
- Eliminados console.log de archivos JavaScript

### Seguridad
- Implementación de queries parametrizadas
- Verificación de permisos en todos los endpoints
- Auto-escaping en templates Twig
- Protección CSRF en formularios
- Dependency Injection implementada (sin uso de \Drupal:: estático)
- Validación de entrada en formularios

### Técnico
- Cumple con Drupal Coding Standards
- Sin deprecaciones (validado con drupal-check)
- Pasa análisis estático PHPStan nivel 1
- JavaScript validado con ESLint
- Arquitectura lista para Drupal.org

[Unreleased]: https://git.drupalcode.org/project/admin_notifications/-/compare/0.1.0...HEAD
[0.1.0]: https://git.drupalcode.org/project/admin_notifications/-/tags/0.1.0
