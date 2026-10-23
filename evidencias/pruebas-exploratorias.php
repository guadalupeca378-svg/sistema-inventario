<?php
declare(strict_types=1);
// Pruebas exploratorias de la Unidad II (CP-15 a CP-20). Ejecutar: php pruebas-exploratorias.php
require __DIR__ . "/../../../sistema-inventario/vendor/autoload.php";
use Inventario\{Inventario, Producto, ReporteInventario, InventarioException};

function caso(string $id, callable $f): void {
    try { $r = $f(); echo $id, ' => ', $r, PHP_EOL; }
    catch (Throwable $e) { echo $id, ' => EXCEPCION ', get_class($e), ': ', $e->getMessage(), PHP_EOL; }
}

caso('CP-15 nombre vacío', fn () => (new Inventario())->registrarProducto(new Producto('PAP-100', '   ', 'Escolar'))->clave);
caso('CP-16 mínimo negativo', fn () => (new Inventario())->registrarProducto(new Producto('PAP-100', 'Lapiz', 'Escolar', -1))->clave);
caso('CP-17 modificar inexistente', fn () => (new Inventario())->modificarProducto('X-1', 'a', 'b', 1)->clave);
caso('Extra salida negativa', function () { $i = new Inventario(); $i->registrarProducto(new Producto('A', 'a', 'c')); $i->registrarEntrada('A', 5, 'K'); return $i->registrarSalida('A', -2, 'K')->cantidad; });
caso('CP-19 salida igual a la existencia', function () { $i = new Inventario(); $i->registrarProducto(new Producto('A', 'a', 'c')); $i->registrarEntrada('A', 5, 'K'); $i->registrarSalida('A', 5, 'K'); return 'existencia ' . $i->consultarProducto('A')->existencia; });
caso('Extra movimiento a clave inexistente', fn () => (new Inventario())->registrarEntrada('NOPE', 3, 'K')->cantidad);
caso('CP-20 responsable vacío', function () { $i = new Inventario(); $i->registrarProducto(new Producto('A', 'a', 'c')); return 'aceptado, responsable="' . $i->registrarEntrada('A', 5, '')->responsable . '"'; });
caso('CP-18 eliminar y volver a registrar', function () {
    $i = new Inventario();
    $i->registrarProducto(new Producto('PAP-001', 'Cuaderno', 'Escolar', 5));
    $i->registrarEntrada('PAP-001', 20, 'Karla');
    $i->registrarSalida('PAP-001', 5, 'Karla');
    $i->eliminarProducto('PAP-001');
    $movs = count($i->movimientos());
    $r1 = (new ReporteInventario($i))->generar();
    $i->registrarProducto(new Producto('PAP-001', 'Cuaderno nuevo', 'Escolar', 5));
    $r = (new ReporteInventario($i))->generar();
    $f = $r['productos'][0];
    return "tras eliminar: movimientos en historial=$movs, reporte entradas={$r1['totalEntradas']}; re-registrado: entradas={$f['entradas']} salidas={$f['salidas']} existencia={$f['existencia']}";
});
