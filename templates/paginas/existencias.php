<?php
/**
 * Existencias actuales con estado y barra respecto al mínimo.
 *
 * La barra se llena al doble de la existencia mínima, así el mínimo queda a la mitad.
 *
 * @var list<array{clave: string, nombre: string, existencia: int, minimo: int, estado: string}> $existencias
 */
?>
<header class="md-page-header">
    <h1 class="md-page-title">Existencias</h1>
    <p class="md-page-subtitle">Piezas disponibles por producto. La barra llega a la mitad al alcanzar el mínimo.</p>
    <div class="md-page-actions">
        <a class="md-btn md-btn--tonal" href="<?= e(url('movimientos')) ?>">
            <span class="material-symbols-rounded" aria-hidden="true">swap_vert</span>Registrar movimiento
        </a>
    </div>
</header>

<?php if ($existencias === []): ?>
    <div class="md-empty">
        <span class="material-symbols-rounded" aria-hidden="true">warehouse</span>
        <p>No hay productos registrados.</p>
        <a class="md-btn md-btn--filled" href="<?= e(url('producto-nuevo')) ?>">Nuevo producto</a>
    </div>
<?php else: ?>
    <div class="md-table-wrap">
        <table class="md-table">
            <caption class="md-sr-only">Existencias por producto</caption>
            <thead>
                <tr>
                    <th scope="col">Clave</th>
                    <th scope="col">Producto</th>
                    <th scope="col" class="md-text-right">Existencia</th>
                    <th scope="col" class="md-text-right">Mínimo</th>
                    <th scope="col">Nivel</th>
                    <th scope="col">Estado</th>
                    <th scope="col"><span class="md-sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($existencias as $fila):
                $bajo = $fila['estado'] !== 'Normal';
                $porcentaje = $fila['minimo'] > 0
                    ? (int) min(100, round($fila['existencia'] / ($fila['minimo'] * 2) * 100))
                    : ($fila['existencia'] > 0 ? 100 : 0);
            ?>
                <tr>
                    <td><?= e($fila['clave']) ?></td>
                    <td><?= e($fila['nombre']) ?></td>
                    <td class="num"><?= e(numero($fila['existencia'])) ?></td>
                    <td class="num"><?= e(numero($fila['minimo'])) ?></td>
                    <td>
                        <div class="md-meter <?= $bajo ? 'md-meter--warn' : 'md-meter--ok' ?>" role="img" aria-label="<?= e(numero($fila['existencia']) . ' de ' . numero($fila['minimo']) . ' mínimo') ?>">
                            <span style="width:<?= $porcentaje ?>%"></span>
                        </div>
                    </td>
                    <td><span class="md-chip <?= $bajo ? 'md-chip--warn' : 'md-chip--ok' ?>"><?= e($fila['estado']) ?></span></td>
                    <td class="md-table__actions">
                        <a class="md-btn md-btn--icon" href="<?= e(url('movimientos', ['clave' => $fila['clave']])) ?>" title="Registrar movimiento" aria-label="Registrar movimiento de <?= e($fila['clave']) ?>">
                            <span class="material-symbols-rounded" aria-hidden="true">swap_vert</span>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
