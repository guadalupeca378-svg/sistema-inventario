# Configuración de herramientas

Este documento registra las herramientas seleccionadas para preparar el entorno de trabajo del
**Sistema de Control de Inventario** y los parámetros con los que se configuró cada una.

## 1. Selección de herramientas

| Necesidad | Herramienta seleccionada | Justificación |
|---|---|---|
| Editor / IDE | Visual Studio Code | Gratuito, ligero, con extensiones para PHP, Git, Docker y GitHub Actions. |
| Lenguaje y framework | PHP 8.2 + Laravel 11 | Definido en el diseño del sistema (patrón MVC). |
| Gestor de dependencias | Composer 2 | Instala PHPUnit y las librerías con versiones controladas. |
| Control de versiones | Git | Historial de cambios, ramas y trabajo en paralelo del equipo. |
| Repositorio remoto | GitHub | Almacena el código, Pull Requests, revisión y GitHub Actions integrados. |
| Gestión del proyecto | Trello | Tablero Kanban con tareas, responsables, fechas y avance. |
| Pruebas | PHPUnit 11 | Herramienta estándar de pruebas para PHP (incluida en Laravel). |
| Base de datos | PostgreSQL 16 | Manejo estricto de tipos de dato y transacciones. |
| Contenedores | Docker + Docker Compose | Mismo entorno en desarrollo, pruebas y producción. |
| CI/CD | GitHub Actions | Ejecuta las pruebas en cada push / Pull Request y publica la imagen. |
| Documentación | Markdown | Documentación versionada junto al código en `docs/`. |
| Cronograma | Excel (diagrama de Gantt) | Programación de actividades por días y semanas. |

## 2. Parámetros de configuración

| Herramienta | Versión | Configuración | Propósito |
|---|---|---|---|
| Git | 2.55.0 | `user.name`, `user.email`, `init.defaultBranch=main`, `core.autocrlf=true`, `pull.rebase=false` | Control de versiones |
| GitHub | Web (2026) | Repositorio **público** `sistema-inventario`, ramas `main`, `develop`, `feature/*`, `release`; Pull Requests obligatorios hacia `main` y `develop` | Almacenamiento remoto y colaboración |
| VS Code | 1.140.0 | Extensiones: PHP Intelephense, GitLens, Docker, GitHub Actions, Markdown All in One, EditorConfig. `formatOnSave`, fin de línea LF | Desarrollo |
| PHP | 8.2.34 | Extensiones `mbstring`, `openssl`, `curl`, `zip`, `pdo_pgsql` | Lenguaje del sistema |
| Composer | 2.10.3 | `composer.json` con autoload PSR-4 `Inventario\\` → `src/` y script `composer test` | Dependencias |
| PHPUnit | 11.5 | `phpunit.xml`, suite *Sistema de Inventario* sobre `tests/`, salida `--testdox` | Pruebas automatizadas |
| Docker | Imagen `php:8.2-apache` | `Dockerfile` multietapa, puerto **80** del contenedor → **8080** del equipo; `docker-compose.yml` con `app` + `postgres:16` | Contenedorización |
| CI/CD | GitHub Actions | `ci.yml`: pruebas en PHP 8.2 y 8.3 + construcción de imagen. `cd.yml`: publica la imagen en GHCR al integrar en `main` | Integración y entrega continua |
| Trello | Web | Tablero *Sistema de Control de Inventario – DevOps* con listas Backlog, Por hacer, En progreso, En revisión, Pruebas y Terminado | Gestión del proyecto |

## 3. Comandos de configuración utilizados

### Git

```bash
git config --global user.name "Karla Guadalupe Madrazo Can"
git config --global user.email "<usuario>@users.noreply.github.com"
git config --global init.defaultBranch main
git config --global core.autocrlf true
git config --global pull.rebase false
git config --global core.editor "code --wait"
git config --global --list
```

### PHP y Composer

```bash
php -v
composer --version
composer install
composer test
```

### Docker

```bash
docker build -t sistema-inventario .
docker run -d -p 8080:80 --name inventario sistema-inventario
docker compose up -d --build
```

## 4. Variables de entorno

| Variable | Ejemplo | Descripción |
|---|---|---|
| `DB_HOST` | `db` | Servidor de PostgreSQL |
| `DB_PORT` | `5432` | Puerto de PostgreSQL |
| `DB_DATABASE` | `inventario` | Nombre de la base de datos |
| `DB_USERNAME` | `inventario` | Usuario de la base de datos |
| `DB_PASSWORD` | *(secreto)* | Se define en `.env`, nunca se sube al repositorio |
| `APP_VERSION` | `1.0.0` | Versión mostrada por la aplicación |
