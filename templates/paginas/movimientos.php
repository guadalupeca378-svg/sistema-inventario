<?php
/**
 * Registro de entradas y salidas, e historial (más reciente primero).
 *
 * @var list<\Inventario\Producto> $productos
 * @var list<\Inventario\Movimiento> $movimientos
 * @var array<string, string> $nombres
 * @var string $claveElegida
 * @var string $ultimoResponsable
 * @var array<string, string> $viejo
 */
$tipo = ($viejo['tipo'] ?? 'entrada') === 'salida' ? 'salida' : 'entrada';
$clave = $viejo['clave'] ?? $claveElegida;
?>
<header class="md-page-header">
    <h1 class="md-page-title">Movimientos</h1>
    <p class="md-page-subtitle">Registra entradas de mercancía y salidas por venta o uso.</p>
</header>

<div class="md-grid md-grid--2">
    <section class="md-card">
        <h2 class="md-card__title">Nuevo movimiento</h2>
        <?php if ($productos === []): ?>
            <div class="md-empty">
                <span class="material-symbols-rounded" aria-hidden="true">inventory_2</span>
                <p>Primero registra al menos un producto.</p>
                <a class="md-btn md-btn--tonal" href="<?= e(url('producto-nuevo')) ?>">Nuevo producto</a>
            </div>
        <?php else: ?>
            <form class="md-form" method="post" action="<?= e(url('movimientos')) ?>">
                <?= campoCsrf() ?>
                <input type="hidden" name="accion" value="movimiento">

                <div class="md-segmented" role="radiogroup" aria-label="Tipo de movimiento">
                    <label>
                        <input type="radio" name="tipo" value="entrada"<?= $tipo === 'entrada' ? ' checked' : '' ?>>
                        <span><span class="material-symbols-rounded" aria-hidden="true">south</span>Entrada</span>
                    </label>
                    <label>
                        <input type="radio" name="tipo" value="salida"<?= $tipo === 'salida' ? ' checked' : '' ?>>
                        <span><span class="material-symbols-rounded" aria-hidden="true">north</span>Salida</span>
                    </label>
                </div>

                <div class="md-field md-field--full">
                    <select class="md-field__input" id="clave" name="clave" required>
                        <option value=""<?= $clave === '' ? ' selected' : '' ?> disabled>Selecciona un producto…</option>
                        <?php foreach ($productos as $p): ?>
                            <option value="<?= e($p->clave) ?>"<?= $p->clave === $clave ? ' selected' : '' ?>>
                                <?= e("{$p->clave} · {$p->nombre} (existencia: " . numero($p->existencia) . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label class="md-field__label" for="clave">Producto</label>
                </div>

                <div class="md-field">
                    <input class="md-field__input" id="cantidad" name="cantidad" type="number" min="1" max="<?= MAXIMO_CANTIDAD ?>" step="1" inputmode="numeric" value="<?= e($viejo['cantidad'] ?? '') ?>" placeholder=" " required>
                    <label class="md-field__label" for="cantidad">Cantidad (piezas)</label>
                </div>

                <div class="md-field">
                    <input class="md-field__input" id="responsable" name="responsable" value="<?= e($viejo['responsable'] ?? $ultimoResponsable) ?>" placeholder=" " maxlength="80" required autocomplete="name">
                    <label class="md-field__label" for="responsable">Responsable</label>
                    <small class="md-field__help">Quién entrega o recibe</small>
                </div>

                <div class="md-form__actions">
                    <button type="submit" class="md-btn md-btn--filled">
                        <span class="material-symbols-rounded" aria-hidden="true">check</span>Registrar movimiento
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </section>

    <section class="md-card">
        <h2 class="md-card__title">Historial</h2>
        <p class="md-card__subtitle"><?= e(numero(count($movimientos))) ?> movimientos, del más reciente al más antiguo.</p>
        <?php if ($movimientos === []): ?>
            <div class="md-empty">
                <span class="material-symbols-rounded" aria-hidden="true">history</span>
                <p>Aún no hay movimientos registrados.</p>
            </div>
        <?php else: ?>
            <div class="md-table-wrap">
                <table class="md-table">
                    <thead>
                        <tr>
                            <th scope="col">Fecha</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Producto</th>
                            <th scope="col" class="md-text-right">Cantidad</th>
                            <th scope="col">Responsable</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($movimientos as $m): ?>
                        <tr>
                            <td><time datetime="<?= e($m->fecha->format(DATE_ATOM)) ?>"><?= e(fechaCorta($m->fecha)) ?></time></td>
                            <td>
                                <span class="md-chip md-chip--<?= e($m->tipo) ?>">
                                    <span class="material-symbols-rounded" aria-hidden="true"><?= $m->tipo === 'entrada' ? 'south' : 'north' ?></span><?= $m->tipo === 'entrada' ? 'Entrada' : 'Salida' ?>
                                </span>
                            </td>
                            <td>
                                <?= e($m->clave) ?>
                                <span class="md-muted"><?= e($nombres[$m->clave] ?? '(eliminado)') ?></span>
                            </td>
                            <td class="num"><?= e(numero($m->cantidad)) ?></td>
                            <td><?= e($m->responsable) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</div>
