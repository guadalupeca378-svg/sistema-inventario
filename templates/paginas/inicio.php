<?php
/**
 * Panel de inicio: indicadores, productos bajo mínimo y últimos movimientos.
 *
 * @var int $totalProductos
 * @var int $totalPiezas
 * @var list<\Inventario\Producto> $bajoMinimo
 * @var int $movimientosHoy
 * @var list<\Inventario\Movimiento> $ultimos
 * @var array<string, string> $nombres
 */
?>
<header class="md-page-header">
    <h1 class="md-page-title">Panel</h1>
    <p class="md-page-subtitle">Resumen del inventario de Papelería Los Pingüinos.</p>
    <div class="md-page-actions">
        <a class="md-btn md-btn--filled" href="<?= e(url('movimientos')) ?>">
            <span class="material-symbols-rounded" aria-hidden="true">swap_vert</span>Registrar movimiento
        </a>
        <a class="md-btn md-btn--tonal" href="<?= e(url('producto-nuevo')) ?>">
            <span class="material-symbols-rounded" aria-hidden="true">add</span>Nuevo producto
        </a>
    </div>
</header>

<section class="md-grid" aria-label="Indicadores">
    <div class="md-stat md-stat--primary">
        <span class="md-stat__icon material-symbols-rounded" aria-hidden="true">inventory_2</span>
        <div class="md-stat__value"><?= e(numero($totalProductos)) ?></div>
        <div class="md-stat__label">Productos registrados</div>
    </div>
    <div class="md-stat md-stat--tertiary">
        <span class="md-stat__icon material-symbols-rounded" aria-hidden="true">package_2</span>
        <div class="md-stat__value"><?= e(numero($totalPiezas)) ?></div>
        <div class="md-stat__label">Piezas en existencia</div>
    </div>
    <div class="md-stat <?= $bajoMinimo === [] ? 'md-stat--ok' : 'md-stat--warn' ?>">
        <span class="md-stat__icon material-symbols-rounded" aria-hidden="true"><?= $bajoMinimo === [] ? 'task_alt' : 'warning' ?></span>
        <div class="md-stat__value"><?= e(numero(count($bajoMinimo))) ?></div>
        <div class="md-stat__label">Productos bajo mínimo</div>
    </div>
    <div class="md-stat md-stat--ok">
        <span class="md-stat__icon material-symbols-rounded" aria-hidden="true">today</span>
        <div class="md-stat__value"><?= e(numero($movimientosHoy)) ?></div>
        <div class="md-stat__label">Movimientos de hoy</div>
    </div>
</section>

<div class="md-grid md-grid--2">
    <section class="md-card">
        <h2 class="md-card__title">Bajo mínimo</h2>
        <p class="md-card__subtitle">Productos que conviene resurtir.</p>
        <?php if ($bajoMinimo === []): ?>
            <div class="md-empty">
                <span class="material-symbols-rounded" aria-hidden="true">task_alt</span>
                <p>Todos los productos están por encima de su existencia mínima.</p>
            </div>
        <?php else: ?>
            <div class="md-table-wrap">
                <table class="md-table">
                    <thead>
                        <tr><th scope="col">Clave</th><th scope="col">Producto</th><th scope="col" class="md-text-right">Existencia</th><th scope="col" class="md-text-right">Mínimo</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($bajoMinimo as $producto): ?>
                        <tr>
                            <td><?= e($producto->clave) ?></td>
                            <td><?= e($producto->nombre) ?></td>
                            <td class="num"><?= e(numero($producto->existencia)) ?></td>
                            <td class="num"><?= e(numero($producto->existenciaMinima)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="md-card__actions">
                <a class="md-btn md-btn--text" href="<?= e(url('existencias')) ?>">Ver existencias</a>
            </div>
        <?php endif; ?>
    </section>

    <section class="md-card">
        <h2 class="md-card__title">Últimos movimientos</h2>
        <p class="md-card__subtitle">Las 5 entradas o salidas más recientes.</p>
        <?php if ($ultimos === []): ?>
            <div class="md-empty">
                <span class="material-symbols-rounded" aria-hidden="true">swap_vert</span>
                <p>Aún no hay movimientos registrados.</p>
                <a class="md-btn md-btn--tonal" href="<?= e(url('movimientos')) ?>">Registrar movimiento</a>
            </div>
        <?php else: ?>
            <div class="md-table-wrap">
                <table class="md-table">
                    <thead>
                        <tr><th scope="col">Fecha</th><th scope="col">Tipo</th><th scope="col">Producto</th><th scope="col" class="md-text-right">Cantidad</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($ultimos as $m): ?>
                        <tr>
                            <td><?= e(fechaCorta($m->fecha)) ?></td>
                            <td><span class="md-chip md-chip--<?= e($m->tipo) ?>"><?= $m->tipo === 'entrada' ? 'Entrada' : 'Salida' ?></span></td>
                            <td><?= e($nombres[$m->clave] ?? $m->clave) ?></td>
                            <td class="num"><?= e(numero($m->cantidad)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="md-card__actions">
                <a class="md-btn md-btn--text" href="<?= e(url('movimientos')) ?>">Ver historial</a>
            </div>
        <?php endif; ?>
    </section>
</div>
