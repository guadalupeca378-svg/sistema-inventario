<?php
/**
 * Reporte básico de inventario (ReporteInventario::generar()), listo para imprimir.
 *
 * @var array{
 *     productos: list<array{clave: string, nombre: string, entradas: int, salidas: int, existencia: int, estado: string}>,
 *     totalEntradas: int,
 *     totalSalidas: int,
 *     productosBajoMinimo: int
 * } $reporte
 * @var DateTimeImmutable $generado
 */
$totalExistencia = array_sum(array_column($reporte['productos'], 'existencia'));
?>
<header class="md-page-header">
    <h1 class="md-page-title">Reporte de inventario</h1>
    <p class="md-page-subtitle">Generado el <?= e($generado->format('d/m/Y \a \l\a\s H:i')) ?> · Papelería Los Pingüinos</p>
    <div class="md-page-actions">
        <button type="button" class="md-btn md-btn--filled" onclick="window.print()">
            <span class="material-symbols-rounded" aria-hidden="true">print</span>Imprimir
        </button>
    </div>
</header>

<section class="md-grid" aria-label="Totales">
    <div class="md-stat md-stat--primary">
        <span class="md-stat__icon material-symbols-rounded" aria-hidden="true">south</span>
        <div class="md-stat__value"><?= e(numero($reporte['totalEntradas'])) ?></div>
        <div class="md-stat__label">Piezas que entraron</div>
    </div>
    <div class="md-stat md-stat--tertiary">
        <span class="md-stat__icon material-symbols-rounded" aria-hidden="true">north</span>
        <div class="md-stat__value"><?= e(numero($reporte['totalSalidas'])) ?></div>
        <div class="md-stat__label">Piezas que salieron</div>
    </div>
    <div class="md-stat <?= $reporte['productosBajoMinimo'] > 0 ? 'md-stat--warn' : 'md-stat--ok' ?>">
        <span class="md-stat__icon material-symbols-rounded" aria-hidden="true">warning</span>
        <div class="md-stat__value"><?= e(numero($reporte['productosBajoMinimo'])) ?></div>
        <div class="md-stat__label">Productos bajo mínimo</div>
    </div>
</section>

<?php if ($reporte['productos'] === []): ?>
    <div class="md-empty">
        <span class="material-symbols-rounded" aria-hidden="true">assessment</span>
        <p>No hay productos para reportar.</p>
    </div>
<?php else: ?>
    <div class="md-table-wrap">
        <table class="md-table">
            <caption class="md-sr-only">Entradas, salidas y existencia por producto</caption>
            <thead>
                <tr>
                    <th scope="col">Clave</th>
                    <th scope="col">Producto</th>
                    <th scope="col" class="md-text-right">Entradas</th>
                    <th scope="col" class="md-text-right">Salidas</th>
                    <th scope="col" class="md-text-right">Existencia</th>
                    <th scope="col">Estado</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($reporte['productos'] as $fila): ?>
                <tr>
                    <td><?= e($fila['clave']) ?></td>
                    <td><?= e($fila['nombre']) ?></td>
                    <td class="num"><?= e(numero($fila['entradas'])) ?></td>
                    <td class="num"><?= e(numero($fila['salidas'])) ?></td>
                    <td class="num"><?= e(numero($fila['existencia'])) ?></td>
                    <td><span class="md-chip <?= $fila['estado'] === 'Normal' ? 'md-chip--ok' : 'md-chip--warn' ?>"><?= e($fila['estado']) ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th scope="row" colspan="2">Totales</th>
                    <td class="num"><?= e(numero($reporte['totalEntradas'])) ?></td>
                    <td class="num"><?= e(numero($reporte['totalSalidas'])) ?></td>
                    <td class="num"><?= e(numero($totalExistencia)) ?></td>
                    <td><?= e(numero($reporte['productosBajoMinimo'])) ?> bajo mínimo</td>
                </tr>
            </tfoot>
        </table>
    </div>
<?php endif; ?>
