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
}
