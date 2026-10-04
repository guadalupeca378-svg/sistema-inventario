<?php

declare(strict_types=1);

namespace Inventario\Tests;

use Inventario\Inventario;
use Inventario\Producto;
use Inventario\ReporteInventario;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[TestDox('Reporte de inventario (integración)')]
final class ReporteTest extends TestCase
{
    #[TestDox('CP-12 Reporte básico: totales iguales a los movimientos registrados')]
    public function testReporteCoincideConMovimientos(): void
    {
        $inventario = new Inventario();
        $inventario->registrarProducto(new Producto('P-001', 'Tornillo 1/4', 'Ferretería', 10));
        $inventario->registrarProducto(new Producto('P-002', 'Tuerca 1/4', 'Ferretería', 5));

        $inventario->registrarEntrada('P-001', 20, 'Almacén');
        $inventario->registrarEntrada('P-002', 4, 'Almacén');
        $inventario->registrarSalida('P-001', 5, 'Ventas');
        $inventario->registrarEntrada('P-001', 10, 'Almacén');

        $reporte = (new ReporteInventario($inventario))->generar();

        $this->assertSame(34, $reporte['totalEntradas']);
        $this->assertSame(5, $reporte['totalSalidas']);
        $this->assertSame(1, $reporte['productosBajoMinimo']);

        $p001 = $reporte['productos'][0];
        $this->assertSame([30, 5, 25], [$p001['entradas'], $p001['salidas'], $p001['existencia']]);
        $this->assertSame($p001['entradas'] - $p001['salidas'], $p001['existencia']);
    }
}
