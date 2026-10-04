<?php

declare(strict_types=1);

namespace Inventario;

/**
 * Reporte básico de inventario: existencias actuales y totales de movimientos por producto.
 */
final class ReporteInventario
{
    public function __construct(private readonly Inventario $inventario)
    {
    }

    /**
     * @return array{
     *     productos: list<array{clave: string, nombre: string, entradas: int, salidas: int, existencia: int, estado: string}>,
     *     totalEntradas: int,
     *     totalSalidas: int,
     *     productosBajoMinimo: int
     * }
     */
    public function generar(): array
    {
        $entradas = [];
        $salidas = [];
        foreach ($this->inventario->movimientos() as $m) {
            if ($m->tipo === Movimiento::ENTRADA) {
                $entradas[$m->clave] = ($entradas[$m->clave] ?? 0) + $m->cantidad;
            } else {
                $salidas[$m->clave] = ($salidas[$m->clave] ?? 0) + $m->cantidad;
            }
        }

        $filas = array_map(static fn (array $e): array => [
            'clave' => $e['clave'],
            'nombre' => $e['nombre'],
            'entradas' => $entradas[$e['clave']] ?? 0,
            'salidas' => $salidas[$e['clave']] ?? 0,
            'existencia' => $e['existencia'],
            'estado' => $e['estado'],
        ], $this->inventario->consultarExistencias());

        return [
            'productos' => $filas,
            'totalEntradas' => array_sum($entradas),
            'totalSalidas' => array_sum($salidas),
            'productosBajoMinimo' => count(array_filter($filas, static fn (array $f): bool => $f['estado'] !== 'Normal')),
        ];
    }

    public function comoTexto(): string
    {
        $reporte = $this->generar();
        $lineas = [sprintf('%-8s %-25s %8s %8s %10s  %s', 'Clave', 'Producto', 'Entradas', 'Salidas', 'Existencia', 'Estado')];
        foreach ($reporte['productos'] as $f) {
            $lineas[] = sprintf(
                '%-8s %-25s %8d %8d %10d  %s',
                $f['clave'], $f['nombre'], $f['entradas'], $f['salidas'], $f['existencia'], $f['estado']
            );
        }
        $lineas[] = sprintf(
            'Total entradas: %d | Total salidas: %d | Bajo mínimo: %d',
            $reporte['totalEntradas'], $reporte['totalSalidas'], $reporte['productosBajoMinimo']
        );

        return implode(PHP_EOL, $lineas);
    }
}
