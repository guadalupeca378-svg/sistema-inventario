<?php
/**
 * Catálogo de productos con búsqueda y acciones de editar / eliminar.
 *
 * @var list<\Inventario\Producto> $productos
 * @var string $busqueda
 */
?>
<header class="md-page-header">
    <h1 class="md-page-title">Productos</h1>
    <p class="md-page-subtitle">Catálogo de artículos de la papelería.</p>
    <div class="md-page-actions">
        <form method="get" role="search">
            <input type="hidden" name="p" value="productos">
            <label class="md-search">
                <span class="md-sr-only">Buscar</span>
                <input type="search" name="q" value="<?= e($busqueda) ?>" data-filtrar="#tablaProductos" placeholder="Buscar…">
            </label>
        </form>
    </div>
</header>

<?php if ($productos === []): ?>
    <div class="md-empty">
        <span class="material-symbols-rounded" aria-hidden="true"><?= $busqueda === '' ? 'inventory_2' : 'search_off' ?></span>
        <?php if ($busqueda === ''): ?>
            <p>Todavía no hay productos registrados.</p>
            <a class="md-btn md-btn--filled" href="<?= e(url('producto-nuevo')) ?>">
                <span class="material-symbols-rounded" aria-hidden="true">add</span>Registrar el primero
            </a>
        <?php else: ?>
            <p>No hay productos que coincidan con «<?= e($busqueda) ?>».</p>
            <a class="md-btn md-btn--text" href="<?= e(url('productos')) ?>">Ver todos</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="md-table-wrap">
        <table class="md-table" id="tablaProductos">
            <caption class="md-sr-only">Lista de productos</caption>
            <thead>
                <tr>
                    <th scope="col">Clave</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Categoría</th>
                    <th scope="col" class="md-text-right">Existencia</th>
                    <th scope="col" class="md-text-right">Mínimo</th>
                    <th scope="col"><span class="md-sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($productos as $p): ?>
                <tr>
                    <td><?= e($p->clave) ?></td>
                    <td><?= e($p->nombre) ?></td>
                    <td><span class="md-chip md-chip--neutral"><?= e($p->categoria) ?></span></td>
                    <td class="num"><?= e(numero($p->existencia)) ?></td>
                    <td class="num"><?= e(numero($p->existenciaMinima)) ?></td>
                    <td class="md-table__actions">
                        <a class="md-btn md-btn--icon" href="<?= e(url('producto-editar', ['clave' => $p->clave])) ?>" title="Editar" aria-label="Editar <?= e($p->clave) ?>">
                            <span class="material-symbols-rounded" aria-hidden="true">edit</span>
                        </a>
                        <form method="post" action="<?= e(url('productos')) ?>" data-confirmar="¿Eliminar el producto <?= e($p->clave) ?>?">
                            <?= campoCsrf() ?>
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="clave" value="<?= e($p->clave) ?>">
                            <button type="submit" class="md-btn md-btn--icon md-btn--danger" title="Eliminar" aria-label="Eliminar <?= e($p->clave) ?>">
                                <span class="material-symbols-rounded" aria-hidden="true">delete</span>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<a class="md-fab" href="<?= e(url('producto-nuevo')) ?>">
    <span class="material-symbols-rounded" aria-hidden="true">add</span>Nuevo producto
</a>
