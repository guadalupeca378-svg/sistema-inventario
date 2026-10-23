# Identidad visual · Papelería Los Pingüinos

## Concepto de marca

"Los Pingüinos" es una papelería escolar, cercana y ordenada. La identidad se inspira en el pingüino:

- **Negro pingüino / azul marino** para la estructura (barra de navegación): sobria y estable.
- **Blanco hielo** para las superficies de trabajo: limpio y fácil de leer.
- **Azul polar** como color principal de acción.
- **Naranja del pico** como acento para lo más importante (botón flotante, salidas, KPI destacados).

El tono es amable y escolar, sin dejar de ser una herramienta de trabajo seria. La interfaz sigue Material Design 3 con CSS propio, sin frameworks.

## Paleta (tema claro)

| Rol (token CSS)                         | Hex       | Uso                                      |
|-----------------------------------------|-----------|------------------------------------------|
| `--md-sys-color-primary`                | `#1B5E9E` | Azul polar: botones principales, enlaces |
| `--md-sys-color-on-primary`             | `#FFFFFF` | Texto sobre primario                     |
| `--md-sys-color-primary-container`      | `#D3E4FF` | Hielo azul: chips de entrada, indicador del menú |
| `--md-sys-color-secondary`              | `#3F5675` | Azul marino suave                        |
| `--md-sys-color-secondary-container`    | `#D7E3F8` | Botón tonal, segmentado activo           |
| `--md-sys-color-tertiary`               | `#9A4A00` | Naranja oscuro (texto)                   |
| `--md-sys-color-tertiary-container`     | `#FFB547` | Naranja pico: FAB, chip de salida        |
| `--md-sys-color-error`                  | `#BA1A1A` | Eliminar, bajo mínimo                    |
| `--md-sys-color-error-container`        | `#FFDAD6` | Fondo de alertas                         |
| `--md-sys-color-success-container`      | `#C3EFCB` | Chip "Normal"                            |
| `--md-sys-color-surface` / background   | `#F7F9FC` | Blanco hielo                             |
| `--md-sys-color-surface-container-lowest` | `#FFFFFF` | Tarjetas                               |
| `--md-sys-color-on-surface`             | `#171C24` | Texto principal                          |
| `--md-sys-color-outline`                | `#73777F` | Bordes de campos                         |
| `--app-nav-bg`                          | `#0F1B2D` | Negro pingüino: navegación               |

El modo oscuro se activa solo con `prefers-color-scheme: dark` (primario `#A3C9FF`, superficie `#0E141C`, navegación `#080D15`). Todas las combinaciones texto/fondo cumplen contraste AA.

## Tipografía

- **Nunito** (600–800): títulos, nombre de la marca, cifras de KPI.
- **Roboto** (400–700): texto, tablas, formularios y botones.
- **Material Symbols Rounded**: iconos (`<span class="material-symbols-rounded">inventory_2</span>`).

Las cifras en tablas usan números tabulares (`td.num`).

## Forma, elevación y movimiento

- Radios: 4 px (campos), 8 px (chips, snackbar), 12 px (tablas), 16 px (tarjetas, FAB), 28 px (diálogos), redondo completo (botones, búsqueda).
- Tres niveles de elevación (`--md-sys-elevation-1..3`).
- Capas de estado: hover 8 %, focus 12 %, pressed 12 %.
- Transiciones de 150–250 ms; se desactivan con `prefers-reduced-motion`.

## Componentes (clases)

| Componente      | Clases |
|-----------------|--------|
| Encabezado      | `.md-page-header`, `.md-page-title`, `.md-page-subtitle`, `.md-page-actions` |
| Tarjetas        | `.md-card`, `.md-card--outlined`, `__title`, `__subtitle`, `__actions` |
| Rejillas        | `.md-grid`, `.md-grid--2` |
| KPI             | `.md-stat` (+ `--primary`, `--tertiary`, `--warn`, `--ok`), `__icon`, `__value`, `__label` |
| Botones         | `.md-btn` + `--filled`, `--tonal`, `--outlined`, `--text`, `--danger`, `--icon` |
| FAB             | `.md-fab` (fijo abajo a la derecha en móvil) |
| Formularios     | `.md-form`, `.md-form__actions`, `.md-field`, `__input`, `__label`, `__help`, `--full` |
| Segmentado      | `.md-segmented` |
| Tabla           | `.md-table-wrap`, `.md-table`, `td.num`, `.md-table__actions` |
| Chips           | `.md-chip` + `--ok`, `--warn`, `--entrada`, `--salida`, `--neutral` |
| Vacío           | `.md-empty` |
| Medidor         | `.md-meter` (`--warn`) |
| Búsqueda        | `.md-search` + `input[data-filtrar="#idTabla"]` |
| Confirmación    | `form[data-confirmar]` o `button[data-confirmar]` → diálogo `.md-dialog` |
| Avisos          | `.md-snackbar`, `.md-snackbar--error` |
| Utilidades      | `.md-muted`, `.md-sr-only`, `.md-text-right` |

Navegación: cajón lateral (≥ 1200 px), riel (840–1199 px), barra superior + barra inferior (< 840 px).

## Uso del logo

- `public/assets/img/logo.svg`: pingüino con lápiz dentro de un círculo azul hielo. Úsalo sobre fondos oscuros (navegación) o claros; el círculo le da contraste en ambos.
- `public/assets/img/favicon.svg`: versión simplificada sobre cuadrado azul polar.
- Tamaño mínimo: 24 px. Deja alrededor un margen libre de al menos ¼ del diámetro.
- No deformar, no cambiar los colores del pico ni girar el logotipo. Junto al logo se escribe "Los Pingüinos" en Nunito 800.
