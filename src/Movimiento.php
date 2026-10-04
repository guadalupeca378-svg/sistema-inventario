<?php

declare(strict_types=1);

namespace Inventario;

use DateTimeImmutable;

/**
 * Entrada o salida de un producto. Guarda fecha y responsable.
 */
final class Movimiento
{
    public const ENTRADA = 'entrada';
    public const SALIDA = 'salida';

    public readonly DateTimeImmutable $fecha;

    public function __construct(
        public readonly string $tipo,
        public readonly string $clave,
        public readonly int $cantidad,
        public readonly string $responsable,
    ) {
        $this->fecha = new DateTimeImmutable();
    }
}
