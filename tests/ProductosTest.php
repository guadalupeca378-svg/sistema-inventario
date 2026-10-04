<?php

declare(strict_types=1);

namespace Inventario\Tests;

use Inventario\Inventario;
use Inventario\InventarioException;
use Inventario\Producto;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[TestDox('Módulo de productos')]
final class ProductosTest extends TestCase
{
    private Inventario $inventario;

    protected function setUp(): void
    {
        $this->inventario = new Inventario();
        $this->inventario->registrarProducto(new Producto('P-001', 'Tornillo 1/4', 'Ferretería', 10));
    }

    #[TestDox('CP-01 Registrar producto con datos válidos')]
    public function testRegistrarProductoConDatosValidos(): void
    {
        $producto = $this->inventario->registrarProducto(new Producto('P-002', 'Tuerca 1/4', 'Ferretería', 5));

        $this->assertSame('P-002', $producto->clave);
        $this->assertSame(0, $producto->existencia);
        $this->assertCount(2, $this->inventario->productos());
    }

    #[TestDox('CP-02 Registrar producto con código duplicado muestra error')]
    public function testRegistrarProductoDuplicado(): void
    {
        $this->expectException(InventarioException::class);
        $this->expectExceptionMessage('La clave P-001 ya existe');

        $this->inventario->registrarProducto(new Producto('P-001', 'Otro', 'Ferretería'));
    }

    #[TestDox('CP-03 Consultar producto con código existente')]
    public function testConsultarProductoExistente(): void
    {
        $producto = $this->inventario->consultarProducto('P-001');

        $this->assertSame('Tornillo 1/4', $producto->nombre);
        $this->assertSame(10, $producto->existenciaMinima);
    }

    #[TestDox('CP-04 Consultar producto con código inexistente muestra mensaje')]
    public function testConsultarProductoInexistente(): void
    {
        $this->expectException(InventarioException::class);
        $this->expectExceptionMessage('Producto P-999 no encontrado');

        $this->inventario->consultarProducto('P-999');
    }

    #[TestDox('CP-05 Modificar producto con datos válidos')]
    public function testModificarProducto(): void
    {
        $this->inventario->modificarProducto('P-001', 'Tornillo 1/4 galvanizado', 'Ferretería', 15);

        $producto = $this->inventario->consultarProducto('P-001');
        $this->assertSame('Tornillo 1/4 galvanizado', $producto->nombre);
        $this->assertSame(15, $producto->existenciaMinima);
    }

    #[TestDox('CP-06 Eliminar producto existente')]
    public function testEliminarProducto(): void
    {
        $this->inventario->eliminarProducto('P-001');

        $this->assertCount(0, $this->inventario->productos());
        $this->expectException(InventarioException::class);
        $this->inventario->consultarProducto('P-001');
    }
}
