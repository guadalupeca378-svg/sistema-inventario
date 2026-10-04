<?php

declare(strict_types=1);

namespace Inventario;

/**
 * Servicio principal del inventario.
 *
 * Estructura inicial: guarda los productos en memoria. En la integración con Laravel
 * este servicio se apoyará en los modelos Eloquent y PostgreSQL.
 */
final class Inventario
{
    /** @var array<string, Producto> */
    private array $productos = [];

    /** @var list<Movimiento> */
    private array $movimientos = [];

    public function registrarProducto(Producto $producto): Producto
    {
        if (isset($this->productos[$producto->clave])) {
            throw new InventarioException("La clave {$producto->clave} ya existe");
        }

        return $this->productos[$producto->clave] = $producto;
    }

    public function consultarProducto(string $clave): Producto
    {
        return $this->productos[$clave]
            ?? throw new InventarioException("Producto {$clave} no encontrado");
    }

    public function modificarProducto(string $clave, string $nombre, string $categoria, int $existenciaMinima): Producto
    {
        $producto = $this->consultarProducto($clave);
        $actualizado = new Producto($clave, $nombre, $categoria, $existenciaMinima, $producto->existencia);

        return $this->productos[$clave] = $actualizado;
    }

    public function eliminarProducto(string $clave): void
    {
        $this->consultarProducto($clave);
        unset($this->productos[$clave]);
    }

    /** @return list<Producto> */
    public function productos(): array
    {
        return array_values($this->productos);
    }

    public function registrarEntrada(string $clave, int $cantidad, string $responsable): Movimiento
    {
        $this->validarCantidad($cantidad);
        $producto = $this->consultarProducto($clave);

        $producto->existencia += $cantidad;

        return $this->movimientos[] = new Movimiento(Movimiento::ENTRADA, $clave, $cantidad, $responsable);
    }

    public function registrarSalida(string $clave, int $cantidad, string $responsable): Movimiento
    {
        $this->validarCantidad($cantidad);
        $producto = $this->consultarProducto($clave);

        if ($cantidad > $producto->existencia) {
            throw new InventarioException(
                "Existencia insuficiente de {$clave}: disponible {$producto->existencia}, solicitado {$cantidad}"
            );
        }

        $producto->existencia -= $cantidad;

        return $this->movimientos[] = new Movimiento(Movimiento::SALIDA, $clave, $cantidad, $responsable);
    }

    /**
     * @return list<array{clave: string, nombre: string, existencia: int, minimo: int, estado: string}>
     */
    public function consultarExistencias(): array
    {
        return array_map(static fn (Producto $p): array => [
            'clave' => $p->clave,
            'nombre' => $p->nombre,
            'existencia' => $p->existencia,
            'minimo' => $p->existenciaMinima,
            'estado' => $p->estaBajoMinimo() ? 'Bajo mínimo' : 'Normal',
        ], $this->productos());
    }

    /** @return list<Movimiento> */
    public function movimientos(): array
    {
        return $this->movimientos;
    }

    private function validarCantidad(int $cantidad): void
    {
        if ($cantidad <= 0) {
            throw new InventarioException('La cantidad debe ser mayor a cero');
        }
    }
}
