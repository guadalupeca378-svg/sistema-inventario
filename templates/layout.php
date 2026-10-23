<?php
/**
 * Plantilla base · Papelería Los Pingüinos (Material Design 3).
 *
 * Variables esperadas (definidas antes del require):
 * @var string      $titulo    Título de la página.
 * @var string      $pagina    Ruta activa: inicio|productos|movimientos|existencias|reporte.
 * @var string      $contenido HTML ya generado de la página (se imprime sin escapar).
 * @var array|null  $flash     ['tipo' => 'exito'|'error', 'mensaje' => string] o null.
 */

declare(strict_types=1);

$titulo    = isset($titulo) ? (string) $titulo : 'Inicio';
$pagina    = isset($pagina) ? (string) $pagina : 'inicio';
$contenido = isset($contenido) ? (string) $contenido : '';
$flash     = isset($flash) && is_array($flash) ? $flash : null;
$version   = getenv('APP_VERSION') ?: '1.0.0';

$e = static fn (string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$navegacion = [
    'inicio'      => ['Inicio', 'dashboard'],
    'productos'   => ['Productos', 'inventory_2'],
    'movimientos' => ['Movimientos', 'swap_vert'],
    'existencias' => ['Existencias', 'warehouse'],
    'reporte'     => ['Reporte', 'assessment'],
];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <meta name="theme-color" content="#0F1B2D">
    <title><?= $e($titulo) ?> · Papelería Los Pingüinos</title>
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800&family=Roboto:wght@400;500;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,400,0..1,0&display=block">
    <link rel="stylesheet" href="assets/css/pinguinos.css">
    <script src="assets/js/app.js" defer></script>
</head>
<body class="app pagina-<?= $e($pagina) ?>">
<a class="app-skip" href="#contenido">Saltar al contenido</a>

<header class="app-topbar">
    <a class="app-brand" href="?p=inicio">
        <img class="app-brand__logo" src="assets/img/logo.svg" alt="" width="40" height="40">
        <span class="app-brand__text">
            <span class="app-brand__name">Los Pingüinos</span>
            <span class="app-brand__tag">Control de inventario</span>
        </span>
    </a>
</header>

<nav class="app-nav" aria-label="Navegación principal">
    <a class="app-brand app-nav__brand" href="?p=inicio">
        <img class="app-brand__logo" src="assets/img/logo.svg" alt="" width="48" height="48">
        <span class="app-brand__text">
            <span class="app-brand__name">Los Pingüinos</span>
            <span class="app-brand__tag">Control de inventario</span>
        </span>
    </a>
    <ul class="app-nav__list">
        <?php foreach ($navegacion as $id => [$etiqueta, $icono]): $activo = $id === $pagina; ?>
        <li>
            <a class="app-nav__item<?= $activo ? ' is-active' : '' ?>" href="?p=<?= $e($id) ?>"<?= $activo ? ' aria-current="page"' : '' ?>>
                <span class="app-nav__indicator"><span class="material-symbols-rounded" aria-hidden="true"><?= $e($icono) ?></span></span>
                <span class="app-nav__label"><?= $e($etiqueta) ?></span>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</nav>

<div class="app-main">
    <main id="contenido" class="app-content" tabindex="-1">
        <?= $contenido ?>
    </main>
    <footer class="app-footer">
        <span>Sistema de Control de Inventario · UTCAM</span>
        <span class="app-footer__version">Versión <?= $e((string) $version) ?></span>
    </footer>
</div>

<?php if ($flash !== null && isset($flash['mensaje'])): $esError = ($flash['tipo'] ?? '') === 'error'; ?>
<div class="md-snackbar<?= $esError ? ' md-snackbar--error' : '' ?>" role="<?= $esError ? 'alert' : 'status' ?>" aria-live="<?= $esError ? 'assertive' : 'polite' ?>">
    <span class="material-symbols-rounded" aria-hidden="true"><?= $esError ? 'error' : 'check_circle' ?></span>
    <span class="md-snackbar__text"><?= $e((string) $flash['mensaje']) ?></span>
    <button type="button" class="md-snackbar__close" aria-label="Cerrar aviso"><span class="material-symbols-rounded" aria-hidden="true">close</span></button>
</div>
<?php endif; ?>
</body>
</html>
