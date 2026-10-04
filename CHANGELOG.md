# Historial de cambios

Formato basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.1.0/) y
[Versionado Semántico](https://semver.org/lang/es/).

## [1.0.0] - 2026-10-04

### Agregado
- Estructura inicial del repositorio (`docs/`, `src/`, `tests/`, `.github/workflows/`).
- Módulo de productos: registrar, consultar, modificar y eliminar (PR #1).
- Módulo de inventario: entradas, salidas y consulta de existencias (PR #2).
- Reporte básico de inventario (PR #3).
- 12 casos de prueba automatizados con PHPUnit (CP-01 a CP-12).
- Integración continua con GitHub Actions (PHP 8.2 y 8.3) y construcción de imagen Docker.
- Publicación de la imagen en GitHub Container Registry al integrar en `main`.
- Documentación: configuración, plan de pruebas, casos de prueba, flujo Git y despliegue Blue/Green.
