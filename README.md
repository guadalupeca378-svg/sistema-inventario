# Sistema de Control de Inventario

Repositorio del proyecto **Sistema de Control de Inventario**, preparado para recibir el código fuente
siguiendo un flujo de trabajo DevOps (control de versiones, pruebas automatizadas, integración continua
y despliegue con contenedores).

Universidad Tecnológica de Campeche · Ingeniería en Desarrollo y Gestión de Software · Décimo A

## Resultados de pruebas

Ejecución documentada: **28 de octubre de 2026**. Cada ronda posterior del flujo en GitHub Actions también terminó en verde.

| Conjunto | Pruebas | Resultado | Evidencia |
|---|---|---|---|
| PHPUnit (unitarias e integración) | 20 | **20 de 20 aprobadas** · 70 verificaciones · 0.265 s | [Evidencias por caso](https://guadalupeca378-svg.github.io/sistema-inventario/ejecucion-28-10-2026/index.html) |
| Playwright (sistema y aceptación) | 18 | **18 de 18 aprobadas** · Microsoft Edge · 1.1 min | [Reporte con capturas](https://guadalupeca378-svg.github.io/sistema-inventario/reporte-28-10-2026/index.html) |
| Integración continua (`ci-pruebas.yml`) | Una ronda por cada push | **Todas en verde** | [Rondas en GitHub Actions](https://github.com/guadalupeca378-svg/sistema-inventario/actions/workflows/ci-pruebas.yml) |
| Pull requests | 4 | **4 integrados** con CI aprobado | [Pull requests](https://github.com/guadalupeca378-svg/sistema-inventario/pulls?q=is%3Apr) |

## Funcionalidades del sistema

| Módulo | Funciones |
|---|---|
| Productos | Registrar, consultar, modificar y eliminar productos |
| Inventario | Consultar existencias, registrar entradas y salidas |
| Reportes | Generar el reporte básico de inventario |

## Equipo

| Integrante | Rol |
|---|---|
| Manuel Alfonso Castro Escalante | Líder de proyecto / Desarrollador |
| Mirley Madaí Gómez Acosta | Analista |
| Jorge Pérez Heredia | Diseñador |
| Karla Guadalupe Madrazo Can | Tester / Responsable de calidad |
| Flor Yazmín Cordero Estañol | Cliente (usuario clave) |

## Estructura del repositorio

```
sistema-inventario/
├── README.md
├── .gitignore
├── docs/
│   ├── configuracion.md
│   ├── plan-pruebas.md
│   ├── casos-prueba.md
│   ├── flujo-git.md
│   └── despliegue.md
├── src/                 # Código fuente (estructura inicial PSR-4)
├── tests/               # Pruebas automatizadas con PHPUnit
├── public/              # Punto de entrada web
├── Dockerfile
├── docker-compose.yml
└── .github/
    └── workflows/       # CI/CD con GitHub Actions
```

## Requisitos

- PHP 8.2 y Composer 2
- Git 2.4x
- Docker (opcional, para ejecutar en contenedor)

## Instalación y pruebas

```bash
git clone https://github.com/guadalupeca378-svg/sistema-inventario.git
cd sistema-inventario
composer install
composer test
```

## Ejecución con Docker

```bash
docker compose up -d --build
# http://localhost:8080
```

## Flujo de trabajo

Se utiliza **Git Flow simplificado**: `main` (producción), `develop` (integración),
`feature/*` (funcionalidades) y `release` (preparación de versión).
Consulte [docs/flujo-git.md](docs/flujo-git.md).
