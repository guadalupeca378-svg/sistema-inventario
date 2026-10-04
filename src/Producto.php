<?php

declare(strict_types=1);

namespace Inventario;

/**
 * Producto del almacén.
 */
final class Producto
{
    public function __construct(
        public readonly string $clave,
        public string $nombre,
        public string $categoria,
        public int $existenciaMinima = 0,
        public int $existencia = 0,
    ) {
        if (trim($clave) === '' || trim($nombre) === '') {
            throw new InventarioException('La clave y el nombre son obligatorios');
        }
        if ($existenciaMinima < 0) {
            throw new InventarioException('La existencia mínima no puede ser negativa');
        }
    }

    public function estaBajoMinimo(): bool
    {
        return $this->existencia < $this->existenciaMinima;
    }
}
