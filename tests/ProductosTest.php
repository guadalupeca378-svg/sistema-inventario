<?php

declare(strict_types=1);

namespace Inventario\Tests;

use Inventario\Inventario;
use Inventario\InventarioException;
use Inventario\Producto;
use Inventario\ReporteInventario;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[TestDox('Módulo de productos')]
final class ProductosTest extends TestCase
{
    private Inventario $inventario;

    protected function setUp(): void
    {
        $this->inventario = new Inventario();
        $this->inventario->registrarProducto(new Producto('P-001', 'Cuaderno profesional raya', 'Escolar', 10));
    }

    #[Group('unitaria')]
    #[Group('CP-01')]
    #[TestDox('CP-01 Registrar producto con datos válidos')]
    public function testRegistrarProductoConDatosValidos(): void
    {
        $producto = $this->inventario->registrarProducto(new Producto('P-002', 'Lápiz grafito HB', 'Escolar', 5));

        $this->assertSame('P-002', $producto->clave);
        $this->assertSame(0, $producto->existencia);
        $this->assertCount(2, $this->inventario->productos());
    }

    #[Group('unitaria')]
    #[Group('CP-02')]
    #[TestDox('CP-02 Registrar producto con código duplicado muestra error')]
    public function testRegistrarProductoDuplicado(): void
    {
        $this->expectException(InventarioException::class);
        $this->expectExceptionMessage('La clave P-001 ya existe');

        $this->inventario->registrarProducto(new Producto('P-001', 'Otro', 'Escolar'));
    }

    #[Group('unitaria')]
    #[Group('CP-03')]
    #[TestDox('CP-03 Consultar producto con código existente')]
    public function testConsultarProductoExistente(): void
    {
        $producto = $this->inventario->consultarProducto('P-001');

        $this->assertSame('Cuaderno profesional raya', $producto->nombre);
        $this->assertSame(10, $producto->existenciaMinima);
    }

    #[Group('unitaria')]
    #[Group('CP-04')]
    #[TestDox('CP-04 Consultar producto con código inexistente muestra mensaje')]
    public function testConsultarProductoInexistente(): void
    {
        $this->expectException(InventarioException::class);
        $this->expectExceptionMessage('Producto P-999 no encontrado');

        $this->inventario->consultarProducto('P-999');
    }

    #[Group('unitaria')]
    #[Group('CP-05')]
    #[TestDox('CP-05 Modificar producto con datos válidos')]
    public function testModificarProducto(): void
    {
        $this->inventario->modificarProducto('P-001', 'Cuaderno profesional cuadro chico', 'Escolar', 15);

        $producto = $this->inventario->consultarProducto('P-001');
        $this->assertSame('Cuaderno profesional cuadro chico', $producto->nombre);
        $this->assertSame(15, $producto->existenciaMinima);
    }

    #[Group('unitaria')]
    #[Group('CP-06')]
    #[TestDox('CP-06 Eliminar producto existente')]
    public function testEliminarProducto(): void
    {
        $this->inventario->eliminarProducto('P-001');

        $this->assertCount(0, $this->inventario->productos());
        $this->expectException(InventarioException::class);
        $this->inventario->consultarProducto('P-001');
    }

    #[Group('unitaria')]
    #[Group('CP-15')]
    #[TestDox('CP-15 Registrar producto con nombre vacío se rechaza')]
    public function testRegistrarProductoSinNombre(): void
    {
        $this->expectException(InventarioException::class);
        $this->expectExceptionMessage('La clave y el nombre son obligatorios');

        $this->inventario->registrarProducto(new Producto('P-100', '   ', 'Escolar'));
    }

    #[Group('unitaria')]
    #[Group('CP-16')]
    #[TestDox('CP-16 Registrar producto con existencia mínima negativa se rechaza')]
    public function testRegistrarProductoConMinimoNegativo(): void
    {
        $this->expectException(InventarioException::class);
        $this->expectExceptionMessage('La existencia mínima no puede ser negativa');

        $this->inventario->registrarProducto(new Producto('P-100', 'Pluma azul punto fino', 'Escolar', -1));
    }

    #[Group('unitaria')]
    #[Group('CP-17')]
    #[TestDox('CP-17 Modificar producto inexistente muestra mensaje')]
    public function testModificarProductoInexistente(): void
    {
        $this->expectException(InventarioException::class);
        $this->expectExceptionMessage('Producto X-1 no encontrado');

        $this->inventario->modificarProducto('X-1', 'Otro', 'Escolar', 1);
    }

    #[Group('integracion')]
    #[Group('CP-18')]
    #[TestDox('CP-18 Una clave eliminada con movimientos no se reutiliza ni altera el reporte')]
    public function testClaveEliminadaConMovimientosNoSeReutiliza(): void
    {
        $this->inventario->registrarEntrada('P-001', 20, 'Almacén');
        $this->inventario->registrarSalida('P-001', 5, 'Ventas');
        $this->inventario->eliminarProducto('P-001');

        try {
            $this->inventario->registrarProducto(new Producto('P-001', 'Cuaderno profesional nuevo', 'Escolar', 10));
            $this->fail('Se esperaba rechazo de la clave P-001');
        } catch (InventarioException $e) {
            $this->assertStringContainsString('perteneció a un producto eliminado', $e->getMessage());
        }

        $this->assertCount(2, $this->inventario->movimientos(), 'El historial se conserva');
        $this->assertSame(0, (new ReporteInventario($this->inventario))->generar()['totalEntradas']);
    }
}
