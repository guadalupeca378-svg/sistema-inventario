<?php

declare(strict_types=1);

namespace Inventario;

use RuntimeException;

/**
 * Error de negocio del inventario. Su mensaje se muestra tal cual al usuario.
 */
final class InventarioException extends RuntimeException
{
}
