<?php
/**
 * Alta y edición de producto. Si hubo error, $viejo conserva lo que se había escrito.
 *
 * @var \Inventario\Producto|null $producto  null al registrar uno nuevo.
 * @var list<string> $categorias
 * @var array<string, string> $viejo
 */
$editando = $producto !== null;
$valor = static fn (string $campo, string $actual = ''): string => $viejo[$campo] ?? $actual;
?>
<header class="md-page-header">
    <h1 class="md-page-title"><?= $editando ? 'Editar producto ' . e($producto->clave) : 'Nuevo producto' ?></h1>
    <p class="md-page-subtitle">
        <?= $editando
            ? 'La existencia solo cambia con entradas y salidas.'
            : 'La existencia inicia en 0; regístrala después como entrada.' ?>
    </p>
</header>

<section class="md-card">
    <form class="md-form" method="post" action="<?= e($editando ? url('producto-editar', ['clave' => $producto->clave]) : url('producto-nuevo')) ?>">
        <?= campoCsrf() ?>
        <input type="hidden" name="accion" value="<?= $editando ? 'modificar' : 'crear' ?>">

        <div class="md-field">
            <?php if ($editando): ?>
                <input class="md-field__input" id="clave" value="<?= e($producto->clave) ?>" placeholder=" " disabled>
                <input type="hidden" name="clave" value="<?= e($producto->clave) ?>">
            <?php else: ?>
                <input class="md-field__input" id="clave" name="clave" value="<?= e($valor('clave')) ?>" placeholder=" " maxlength="20" required autofocus autocomplete="off">
            <?php endif; ?>
            <label class="md-field__label" for="clave">Clave</label>
            <small class="md-field__help"><?= $editando ? 'La clave no se puede cambiar' : 'Ejemplo: PAP-013' ?></small>
        </div>

        <div class="md-field">
            <input class="md-field__input" id="nombre" name="nombre" value="<?= e($valor('nombre', $producto?->nombre ?? '')) ?>" placeholder=" " maxlength="100" required<?= $editando ? ' autofocus' : '' ?>>
            <label class="md-field__label" for="nombre">Nombre</label>
        </div>

        <div class="md-field">
            <input class="md-field__input" id="categoria" name="categoria" list="listaCategorias" value="<?= e($valor('categoria', $producto?->categoria ?? '')) ?>" placeholder=" " maxlength="40" required>
            <label class="md-field__label" for="categoria">Categoría</label>
            <small class="md-field__help">Escolar, Oficina, Papel, Arte…</small>
            <datalist id="listaCategorias">
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= e($categoria) ?>">
                <?php endforeach; ?>
            </datalist>
        </div>

        <div class="md-field">
            <input class="md-field__input" id="existenciaMinima" name="existenciaMinima" type="number" min="0" max="<?= MAXIMO_CANTIDAD ?>" step="1" inputmode="numeric" value="<?= e($valor('existenciaMinima', (string) ($producto?->existenciaMinima ?? 0))) ?>" placeholder=" " required>
            <label class="md-field__label" for="existenciaMinima">Existencia mínima</label>
            <small class="md-field__help">Por debajo de esta cantidad se marca como “Bajo mínimo”</small>
        </div>

        <div class="md-form__actions">
            <a class="md-btn md-btn--text" href="<?= e(url('productos')) ?>">Cancelar</a>
            <button type="submit" class="md-btn md-btn--filled">
                <span class="material-symbols-rounded" aria-hidden="true">save</span><?= $editando ? 'Guardar cambios' : 'Registrar producto' ?>
            </button>
        </div>
    </form>
</section>
