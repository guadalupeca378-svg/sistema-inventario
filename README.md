# Sistema de Control de Inventario

Repositorio del proyecto **Sistema de Control de Inventario**, preparado para recibir el código fuente
siguiendo un flujo de trabajo DevOps (control de versiones, pruebas automatizadas, integración continua
y despliegue con contenedores).

Universidad Tecnológica de Campeche · Ingeniería en Desarrollo y Gestión de Software · Décimo A

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
├── public/              # Punto de entrada web (index.php) y recursos estáticos
├── templates/           # Layout y vistas de cada pantalla
├── data/                # inventario.json (se crea al arrancar; no se versiona)
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

## Ejecutar la aplicación

Interfaz web de **Papelería Los Pingüinos** (panel, productos, movimientos, existencias y reporte):

```bash
composer install
composer serve        # equivale a: php -S localhost:8000 -t public
# http://localhost:8000
```

- Los datos se guardan en `data/inventario.json` (ignorado por Git). En el primer arranque se crea
  con productos y movimientos de ejemplo; bórralo para volver a empezar.
- Otra ruta de datos: variable de entorno `INVENTARIO_DATOS=/ruta/inventario.json`.
- Zona horaria por defecto `America/Merida` (cambiar con `APP_TIMEZONE`).
- Salud del servicio: `GET /?p=salud` responde `OK`.

## Ejecución con Docker

```bash
docker compose up -d --build
# http://localhost:8080
```

## Flujo de trabajo

Se utiliza **Git Flow simplificado**: `main` (producción), `develop` (integración),
`feature/*` (funcionalidades) y `release` (preparación de versión).
Consulte [docs/flujo-git.md](docs/flujo-git.md).

## Pruebas funcionales en el navegador (Playwright)

Un navegador Edge recorre los casos CP-01 a CP-12 como lo haría un usuario, y la lista de
aceptación de la cliente (CP-23). Usa su propio
inventario de prueba (`data/demo-e2e.json`), así que no modifica los datos reales.

```bash
npm install          # solo la primera vez
npm run demo         # navegador visible y en cámara lenta (para presentar)
npm run pruebas:e2e  # sin ventana, rápido
npm run reporte      # abre el reporte HTML con una captura de cada caso
```

Velocidad de la demostración (milisegundos entre acciones), en PowerShell: `$env:LENTO=800; npm run demo`.

### Presentación: los 5 tipos de prueba con un solo comando

```bash
npm run presentacion          # unitarias → integración → sistema → aceptación → regresión
npm run presentacion:pausas   # igual, pero espera Enter entre cada tipo para explicarlo
```

Para un tipo o un caso suelto: `vendor/bin/phpunit --testdox --group unitaria`, `--group integracion`
o `--group CP-20`.
