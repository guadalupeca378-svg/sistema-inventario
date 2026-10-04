<?php

declare(strict_types=1);

namespace Inventario\Tests;

use Inventario\Inventario;
use Inventario\InventarioException;
use Inventario\Movimiento;
use Inventario\Producto;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[TestDox('Módulo de inventario (entradas, salidas y existencias)')]
final class MovimientosTest extends TestCase
{
    private Inventario $inventario;

    protected function setUp(): void
    {
        $this->inventario = new Inventario();
        $this->inventario->registrarProducto(new Producto('P-001', 'Tornillo 1/4', 'Ferretería', 10));
    }

    #[TestDox('CP-07 Entrada de producto con cantidad válida incrementa la existencia')]
    public function testEntradaIncrementaExistencia(): void
    {
        $movimiento = $this->inventario->registrarEntrada('P-001', 20, 'Almacén');

        $this->assertSame(20, $this->inventario->consultarProducto('P-001')->existencia);
        $this->assertSame(Movimiento::ENTRADA, $movimiento->tipo);
        $this->assertSame('Almacén', $movimiento->responsable);
    }

    #[TestDox('CP-08 Salida de producto con cantidad disponible reduce la existencia')]
    public function testSalidaReduceExistencia(): void
    {
        $this->inventario->registrarEntrada('P-001', 20, 'Almacén');
        $this->inventario->registrarSalida('P-001', 5, 'Ventas');

        $this->assertSame(15, $this->inventario->consultarProducto('P-001')->existencia);
    }

    #[TestDox('CP-09 Salida mayor a la existencia se rechaza sin modificar la existencia')]
    public function testSalidaMayorALaExistenciaSeRechaza(): void
    {
        $this->inventario->registrarEntrada('P-001', 20, 'Almacén');

        try {
            $this->inventario->registrarSalida('P-001', 50, 'Ventas');
            $this->fail('Se esperaba que la salida fuera rechazada');
        } catch (InventarioException $e) {
            $this->assertStringContainsString('Existencia insuficiente', $e->getMessage());
        }

        $this->assertSame(20, $this->inventario->consultarProducto('P-001')->existencia);
    }

    #[TestDox('CP-10 Entrada con cantidad en cero o negativa se rechaza')]
    public function testEntradaConCantidadInvalida(): void
    {
        foreach ([0, -3] as $cantidad) {
            try {
                $this->inventario->registrarEntrada('P-001', $cantidad, 'Almacén');
                $this->fail("Se esperaba rechazo para la cantidad {$cantidad}");
            } catch (InventarioException $e) {
                $this->assertSame('La cantidad debe ser mayor a cero', $e->getMessage());
            }
        }

        $this->assertSame(0, $this->inventario->consultarProducto('P-001')->existencia);
    }

    #[TestDox('CP-11 Consultar existencias indica productos normales y bajo el mínimo')]
    public function testConsultarExistencias(): void
    {
        $this->inventario->registrarProducto(new Producto('P-002', 'Tuerca 1/4', 'Ferretería', 5));
        $this->inventario->registrarEntrada('P-001', 3, 'Almacén');
        $this->inventario->registrarEntrada('P-002', 30, 'Almacén');

        $existencias = array_column($this->inventario->consultarExistencias(), 'estado', 'clave');

        $this->assertSame(['P-001' => 'Bajo mínimo', 'P-002' => 'Normal'], $existencias);
    }
}
