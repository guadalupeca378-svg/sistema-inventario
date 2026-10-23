<?php

declare(strict_types=1);

namespace Inventario\Tests;

use DateTimeImmutable;
use Inventario\AlmacenJson;
use Inventario\Inventario;
use Inventario\Movimiento;
use Inventario\Producto;
use Inventario\ReporteInventario;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[TestDox('Persistencia del inventario en JSON')]
final class AlmacenJsonTest extends TestCase
{
    private string $directorio;
    private string $ruta;

    protected function setUp(): void
    {
        $this->directorio = sys_get_temp_dir() . '/inventario-prueba-' . bin2hex(random_bytes(4));
        $this->ruta = $this->directorio . '/inventario.json';
    }

    protected function tearDown(): void
    {
        foreach (glob($this->directorio . '/*') ?: [] as $archivo) {
            unlink($archivo);
        }
        if (is_dir($this->directorio)) {
            rmdir($this->directorio);
        }
    }

    #[Group('integracion')]
    #[Group('CP-13')]
    #[TestDox('CP-13 Guardar y volver a cargar conserva productos, existencias y movimientos con fecha')]
    public function testGuardarYCargarConservaLosDatos(): void
    {
        $inventario = new Inventario();
        $inventario->registrarProducto(new Producto('PAP-001', 'Cuaderno profesional', 'Escolar', 10));
        $inventario->registrarEntrada('PAP-001', 20, 'Almacén');
        $salida = $inventario->registrarSalida('PAP-001', 5, 'Mostrador');

        $almacen = new AlmacenJson($this->ruta);
        $almacen->guardar($inventario);
        $cargado = $almacen->cargar();

        $producto = $cargado->consultarProducto('PAP-001');
        $this->assertSame(['Cuaderno profesional', 'Escolar', 10, 15], [
            $producto->nombre, $producto->categoria, $producto->existenciaMinima, $producto->existencia,
        ]);

        $movimientos = $cargado->movimientos();
        $this->assertCount(2, $movimientos);
        $this->assertSame([Movimiento::SALIDA, 5, 'Mostrador'], [
            $movimientos[1]->tipo, $movimientos[1]->cantidad, $movimientos[1]->responsable,
        ]);
        $this->assertSame($salida->fecha->getTimestamp(), $movimientos[1]->fecha->getTimestamp());

        // Cargar no duplica movimientos ni altera existencias.
        $almacen->guardar($cargado);
        $otraVez = $almacen->cargar();
        $this->assertCount(2, $otraVez->movimientos());
        $this->assertSame(15, $otraVez->consultarProducto('PAP-001')->existencia);
        $this->assertSame([], glob($this->directorio . '/*.tmp') ?: []);
    }

    #[Group('integracion')]
    #[Group('CP-14')]
    #[TestDox('CP-14 El primer arranque crea el inventario con datos de ejemplo de la papelería')]
    public function testPrimerArranqueCreaDatosDeEjemplo(): void
    {
        $this->assertFileDoesNotExist($this->ruta);

        $inventario = (new AlmacenJson($this->ruta))->cargar();

        $this->assertFileExists($this->ruta);
        $this->assertGreaterThanOrEqual(10, count($inventario->productos()));
        $this->assertNotEmpty($inventario->movimientos());
        $this->assertSame('Escolar', $inventario->consultarProducto('PAP-001')->categoria);
        $this->assertLessThanOrEqual(new DateTimeImmutable(), $inventario->movimientos()[0]->fecha);

        $reporte = (new ReporteInventario($inventario))->generar();
        $this->assertGreaterThan(0, $reporte['productosBajoMinimo']);
        foreach ($reporte['productos'] as $fila) {
            $this->assertSame($fila['entradas'] - $fila['salidas'], $fila['existencia'], $fila['clave']);
        }
    }
}
