<?php

declare(strict_types=1);

/**
 * Front controller de Papelería Los Pingüinos.
 *
 * Rutas por ?p= (GET) y acciones por POST (campo "accion") con patrón PRG, mensajes flash
 * y token CSRF en sesión. Los datos se guardan con AlmacenJson.
 */

use Inventario\AlmacenJson;
use Inventario\Inventario;
use Inventario\InventarioException;
use Inventario\Movimiento;
use Inventario\Producto;
use Inventario\ReporteInventario;

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'America/Merida');

const MAXIMO_CANTIDAD = 1_000_000;

$pagina = is_string($_GET['p'] ?? null) ? $_GET['p'] : 'inicio';

// Endpoint de salud (sin sesión ni datos).
if ($pagina === 'salud') {
    header('Content-Type: text/plain; charset=utf-8');
    echo 'OK';
    exit;
}

header('Content-Type: text/html; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: same-origin');

session_name('pinguinos');
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
]);
$_SESSION['csrf'] ??= bin2hex(random_bytes(32));

// Errores no previstos (p. ej. archivo de datos dañado): se registran y se muestra un 500 genérico.
set_exception_handler(static function (Throwable $error): void {
    error_log('[inventario] ' . $error);
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/plain; charset=utf-8');
    }
    echo 'Error interno del sistema de inventario. Inténtalo más tarde.';
});

$almacen = new AlmacenJson();

/* ---------- Funciones de apoyo para el controlador y las vistas ---------- */

/** Escapa texto para HTML. */
function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * URL relativa de una pantalla (sin escapar; usar con e() en atributos).
 *
 * @param array<string, string|int> $parametros
 */
function url(string $pagina, array $parametros = []): string
{
    return '?' . http_build_query(['p' => $pagina] + $parametros);
}

/** Campo oculto con el token CSRF de la sesión. */
function campoCsrf(): string
{
    return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">';
}

function numero(int $valor): string
{
    return number_format($valor, 0, '.', ',');
}

/** Fecha legible: "Hoy, 10:30", "Ayer, 18:05" o "04/10/2026 09:30". */
function fechaCorta(DateTimeImmutable $fecha): string
{
    $fecha = $fecha->setTimezone(new DateTimeZone(date_default_timezone_get()));
    $dia = $fecha->format('Y-m-d');

    return match ($dia) {
        date('Y-m-d') => 'Hoy, ' . $fecha->format('H:i'),
        date('Y-m-d', strtotime('-1 day')) => 'Ayer, ' . $fecha->format('H:i'),
        default => $fecha->format('d/m/Y H:i'),
    };
}

/** @param array<string, string|int> $parametros */
function redirigir(string $pagina, array $parametros = []): never
{
    header('Location: ' . url($pagina, $parametros), true, 303);
    exit;
}

function flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

/**
 * Renderiza una vista dentro del layout y termina la petición.
 *
 * @param array<string, mixed> $datos
 */
function render(string $vista, string $titulo, string $pagina, array $datos = [], int $estado = 200): never
{
    http_response_code($estado);
    $flash = $_SESSION['flash'] ?? null;
    $viejo = $_SESSION['viejo'] ?? [];
    unset($_SESSION['flash'], $_SESSION['viejo']);

    extract($datos, EXTR_SKIP);
    ob_start();
    require __DIR__ . '/../templates/paginas/' . $vista . '.php';
    $contenido = (string) ob_get_clean();

    $layout = __DIR__ . '/../templates/layout.php';
    if (is_file($layout)) {
        require $layout;
    } else {
        echo '<!doctype html><html lang="es"><meta charset="utf-8"><title>', e($titulo), '</title>';
        if ($flash !== null) {
            echo '<p role="status">', e($flash['mensaje']), '</p>';
        }
        echo $contenido, '</html>';
    }
    exit;
}

/** Texto obligatorio u opcional recortado y con longitud máxima. */
function texto(string $campo, string $etiqueta, int $maximo, bool $obligatorio = true): string
{
    $valor = trim(is_string($_POST[$campo] ?? null) ? $_POST[$campo] : '');
    if (!mb_check_encoding($valor, 'UTF-8') || preg_match('/[\x00-\x1F\x7F]/', $valor) === 1) {
        throw new InventarioException("{$etiqueta} contiene caracteres no válidos");
    }
    if ($obligatorio && $valor === '') {
        throw new InventarioException("{$etiqueta} es obligatorio");
    }
    if (mb_strlen($valor) > $maximo) {
        throw new InventarioException("{$etiqueta} admite como máximo {$maximo} caracteres");
    }

    return $valor;
}

function entero(string $campo, string $etiqueta): int
{
    $valor = filter_var(trim((string) ($_POST[$campo] ?? '')), FILTER_VALIDATE_INT, [
        'options' => ['max_range' => MAXIMO_CANTIDAD],
    ]);
    if ($valor === false) {
        throw new InventarioException("{$etiqueta} debe ser un número entero (máximo " . numero(MAXIMO_CANTIDAD) . ')');
    }

    return $valor;
}

function clave(): string
{
    return mb_strtoupper(texto('clave', 'La clave', 20));
}

/** @return list<string> Categorías existentes, sin repetir y ordenadas. */
function categorias(Inventario $inventario): array
{
    $categorias = array_unique(array_map(static fn (Producto $p): string => $p->categoria, $inventario->productos()));
    sort($categorias, SORT_LOCALE_STRING);

    return $categorias;
}

/* ---------- Acciones (POST) ---------- */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = is_string($_POST['accion'] ?? null) ? $_POST['accion'] : '';

    if (!hash_equals($_SESSION['csrf'], is_string($_POST['csrf'] ?? null) ? $_POST['csrf'] : '')) {
        flash('error', 'La sesión expiró o el formulario no es válido. Inténtalo de nuevo.');
        redirigir($pagina);
    }

    // Pantalla a la que se regresa si la acción falla.
    $volver = match ($accion) {
        'crear' => ['producto-nuevo', []],
        'modificar' => ['producto-editar', ['clave' => (string) ($_POST['clave'] ?? '')]],
        'movimiento' => ['movimientos', []],
        default => ['productos', []],
    };

    try {
        switch ($accion) {
            case 'crear':
                $producto = new Producto(
                    clave(),
                    texto('nombre', 'El nombre', 100),
                    texto('categoria', 'La categoría', 40),
                    entero('existenciaMinima', 'La existencia mínima'),
                );
                $almacen->actualizar(static fn (Inventario $i) => $i->registrarProducto($producto));
                flash('exito', "Producto {$producto->clave} registrado.");
                redirigir('productos');

            case 'modificar':
                $clave = clave();
                $nombre = texto('nombre', 'El nombre', 100);
                $categoria = texto('categoria', 'La categoría', 40);
                $minimo = entero('existenciaMinima', 'La existencia mínima');
                $almacen->actualizar(
                    static fn (Inventario $i) => $i->modificarProducto($clave, $nombre, $categoria, $minimo)
                );
                flash('exito', "Producto {$clave} actualizado.");
                redirigir('productos');

            case 'eliminar':
                $clave = clave();
                $almacen->actualizar(static fn (Inventario $i) => $i->eliminarProducto($clave));
                flash('exito', "Producto {$clave} eliminado.");
                redirigir('productos');

            case 'movimiento':
                $tipo = $_POST['tipo'] ?? '';
                if ($tipo !== Movimiento::ENTRADA && $tipo !== Movimiento::SALIDA) {
                    throw new InventarioException('Selecciona si el movimiento es entrada o salida');
                }
                $clave = clave();
                $cantidad = entero('cantidad', 'La cantidad');
                $responsable = texto('responsable', 'El responsable', 80);
                $existencia = $almacen->actualizar(static function (Inventario $i) use ($tipo, $clave, $cantidad, $responsable): int {
                    $tipo === Movimiento::ENTRADA
                        ? $i->registrarEntrada($clave, $cantidad, $responsable)
                        : $i->registrarSalida($clave, $cantidad, $responsable);

                    return $i->consultarProducto($clave)->existencia;
                });
                $_SESSION['ultimoResponsable'] = $responsable;
                flash('exito', sprintf(
                    '%s de %s pza. de %s registrada. Existencia actual: %s.',
                    $tipo === Movimiento::ENTRADA ? 'Entrada' : 'Salida',
                    numero($cantidad),
                    $clave,
                    numero($existencia),
                ));
                redirigir('movimientos');

            default:
                throw new InventarioException('Acción no válida');
        }
    } catch (InventarioException $error) {
        flash('error', $error->getMessage());
        $_SESSION['viejo'] = array_map(
            static fn (mixed $v): string => is_string($v) ? $v : '',
            array_diff_key($_POST, ['csrf' => true]),
        );
        redirigir(...$volver);
    }
}

/* ---------- Pantallas (GET) ---------- */

switch ($pagina) {
    case 'inicio':
        $inventario = $almacen->cargar();
        $productos = $inventario->productos();
        $movimientos = $inventario->movimientos();
        $hoy = date('Y-m-d');
        render('inicio', 'Panel', 'inicio', [
            'totalProductos' => count($productos),
            'totalPiezas' => array_sum(array_map(static fn (Producto $p): int => $p->existencia, $productos)),
            'bajoMinimo' => array_values(array_filter($productos, static fn (Producto $p): bool => $p->estaBajoMinimo())),
            'movimientosHoy' => count(array_filter(
                $movimientos,
                static fn (Movimiento $m): bool => $m->fecha->format('Y-m-d') === $hoy,
            )),
            'ultimos' => array_slice(array_reverse($movimientos), 0, 5),
            'nombres' => array_column(array_map(static fn (Producto $p): array => [$p->clave, $p->nombre], $productos), 1, 0),
        ]);

    case 'productos':
        $busqueda = trim(is_string($_GET['q'] ?? null) ? $_GET['q'] : '');
        $productos = $almacen->cargar()->productos();
        if ($busqueda !== '') {
            $productos = array_values(array_filter(
                $productos,
                static fn (Producto $p): bool => mb_stripos("{$p->clave} {$p->nombre} {$p->categoria}", $busqueda) !== false,
            ));
        }
        render('productos', 'Productos', 'productos', ['productos' => $productos, 'busqueda' => $busqueda]);

    case 'producto-nuevo':
        render('producto-form', 'Nuevo producto', 'productos', [
            'producto' => null,
            'categorias' => categorias($almacen->cargar()),
        ]);

    case 'producto-editar':
        $inventario = $almacen->cargar();
        try {
            $producto = $inventario->consultarProducto(is_string($_GET['clave'] ?? null) ? $_GET['clave'] : '');
        } catch (InventarioException $error) {
            flash('error', $error->getMessage());
            redirigir('productos');
        }
        render('producto-form', "Editar {$producto->clave}", 'productos', [
            'producto' => $producto,
            'categorias' => categorias($inventario),
        ]);

    case 'movimientos':
        $inventario = $almacen->cargar();
        $productos = $inventario->productos();
        render('movimientos', 'Movimientos', 'movimientos', [
            'productos' => $productos,
            'movimientos' => array_reverse($inventario->movimientos()),
            'nombres' => array_column(array_map(static fn (Producto $p): array => [$p->clave, $p->nombre], $productos), 1, 0),
            'claveElegida' => is_string($_GET['clave'] ?? null) ? $_GET['clave'] : '',
            'ultimoResponsable' => $_SESSION['ultimoResponsable'] ?? '',
        ]);

    case 'existencias':
        render('existencias', 'Existencias', 'existencias', [
            'existencias' => $almacen->cargar()->consultarExistencias(),
        ]);

    case 'reporte':
        render('reporte', 'Reporte de inventario', 'reporte', [
            'reporte' => (new ReporteInventario($almacen->cargar()))->generar(),
            'generado' => new DateTimeImmutable(),
        ]);

    default:
        render('404', 'Página no encontrada', '', [], 404);
}
