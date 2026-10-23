<?php

declare(strict_types=1);

namespace Inventario;

use DateTimeImmutable;
use DateTimeZone;
use RuntimeException;

/**
 * Persistencia del inventario en un archivo JSON.
 *
 * Ruta por defecto: <proyecto>/data/inventario.json; se puede cambiar por constructor o con la
 * variable de entorno INVENTARIO_DATOS. La escritura es atómica (archivo temporal + rename) y
 * se protege con un candado exclusivo sobre "<ruta>.lock".
 */
final class AlmacenJson
{
    private const VERSION = 1;

    private readonly string $ruta;

    public function __construct(?string $ruta = null)
    {
        $this->ruta = $ruta
            ?? (getenv('INVENTARIO_DATOS') ?: dirname(__DIR__) . '/data/inventario.json');
    }

    public function ruta(): string
    {
        return $this->ruta;
    }

    /**
     * Lee el inventario guardado. Si el archivo no existe, crea uno con datos de ejemplo.
     */
    public function cargar(): Inventario
    {
        return $this->conCandado(fn (): Inventario => $this->leer());
    }

    public function guardar(Inventario $inventario): void
    {
        $this->conCandado(function () use ($inventario): void {
            $this->escribir($inventario);
        });
    }

    /**
     * Carga, aplica el cambio y guarda bajo un mismo candado para no perder cambios
     * concurrentes. Si el cambio lanza una excepción no se guarda nada.
     *
     * @template T
     * @param callable(Inventario): T $cambio
     * @return T
     */
    public function actualizar(callable $cambio): mixed
    {
        return $this->conCandado(function () use ($cambio): mixed {
            $inventario = $this->leer();
            $resultado = $cambio($inventario);
            $this->escribir($inventario);

            return $resultado;
        });
    }

    /**
     * Inventario inicial de la papelería, usado en el primer arranque.
     */
    public static function datosDeEjemplo(): Inventario
    {
        $inventario = new Inventario();
        $productos = [
            ['PAP-001', 'Cuaderno profesional raya 100 hojas', 'Escolar', 20],
            ['PAP-002', 'Lápiz grafito HB (caja con 12)', 'Escolar', 10],
            ['PAP-003', 'Pluma tinta azul punto mediano', 'Oficina', 30],
            ['PAP-004', 'Borrador blanco de migajón', 'Escolar', 15],
            ['PAP-005', 'Sacapuntas metálico', 'Escolar', 10],
            ['PAP-006', 'Colores de madera (caja con 12)', 'Arte', 8],
            ['PAP-007', 'Hojas blancas carta (paquete 500)', 'Papel', 10],
            ['PAP-008', 'Folder tamaño carta color crema', 'Oficina', 50],
            ['PAP-009', 'Pegamento en barra 21 g', 'Escolar', 12],
            ['PAP-010', 'Tijeras escolares punta roma', 'Escolar', 6],
            ['PAP-011', 'Cartulina blanca', 'Papel', 25],
            ['PAP-012', 'Marcatextos amarillo', 'Oficina', 15],
        ];
        foreach ($productos as [$clave, $nombre, $categoria, $minimo]) {
            $inventario->registrarProducto(new Producto($clave, $nombre, $categoria, $minimo));
        }

        $almacen = 'Manuel Alfonso Castro Escalante (Almacén)';
        $mostrador = 'Flor Yazmín Cordero Estañol (Mostrador)';
        $recepcion = 'Karla Guadalupe Madrazo Can (Recepción)';
        $e = Movimiento::ENTRADA;
        $s = Movimiento::SALIDA;

        // [días atrás, hora (o minutos atrás si es hoy), tipo, clave, cantidad, responsable]
        $movimientos = [
            [10, '09:00', $e, 'PAP-001', 60, $almacen],
            [10, '09:05', $e, 'PAP-002', 25, $almacen],
            [10, '09:10', $e, 'PAP-003', 80, $almacen],
            [10, '09:15', $e, 'PAP-004', 20, $almacen],
            [10, '09:20', $e, 'PAP-005', 20, $almacen],
            [10, '09:25', $e, 'PAP-006', 15, $almacen],
            [10, '09:30', $e, 'PAP-007', 12, $recepcion],
            [10, '09:35', $e, 'PAP-008', 100, $recepcion],
            [10, '09:40', $e, 'PAP-009', 30, $recepcion],
            [10, '09:45', $e, 'PAP-010', 10, $recepcion],
            [10, '09:50', $e, 'PAP-011', 40, $recepcion],
            [10, '09:55', $e, 'PAP-012', 30, $recepcion],
            [7, '11:20', $s, 'PAP-001', 15, $mostrador],
            [7, '12:45', $s, 'PAP-003', 20, $mostrador],
            [7, '17:10', $s, 'PAP-007', 4, $mostrador],
            [5, '10:30', $s, 'PAP-004', 12, $mostrador],
            [5, '13:15', $s, 'PAP-010', 6, $mostrador],
            [5, '16:40', $s, 'PAP-011', 10, $mostrador],
            [3, '09:50', $s, 'PAP-008', 35, $mostrador],
            [3, '12:05', $s, 'PAP-012', 18, $mostrador],
            [3, '18:30', $s, 'PAP-002', 8, $mostrador],
            [1, '10:10', $s, 'PAP-007', 5, $mostrador],
            [1, '15:25', $s, 'PAP-006', 4, $mostrador],
            [1, '17:55', $s, 'PAP-009', 10, $mostrador],
            [0, 90, $e, 'PAP-011', 20, $recepcion],
            [0, 45, $s, 'PAP-001', 6, $mostrador],
            [0, 20, $s, 'PAP-005', 3, $mostrador],
        ];
        $hoy = new DateTimeImmutable('today');
        $ahora = new DateTimeImmutable();
        foreach ($movimientos as [$dias, $hora, $tipo, $clave, $cantidad, $responsable]) {
            $fecha = $dias === 0
                ? $ahora->modify("-{$hora} minutes")
                : $hoy->modify("-{$dias} days {$hora}");
            $producto = $inventario->consultarProducto($clave);
            $producto->existencia += $tipo === $e ? $cantidad : -$cantidad;
            $inventario->restaurarMovimiento(new Movimiento($tipo, $clave, $cantidad, $responsable, $fecha));
        }

        return $inventario;
    }

    private function leer(): Inventario
    {
        if (!is_file($this->ruta)) {
            $inventario = self::datosDeEjemplo();
            $this->escribir($inventario);

            return $inventario;
        }

        $json = file_get_contents($this->ruta);
        $datos = $json === false ? null : json_decode($json, true);
        if (!is_array($datos)) {
            throw new RuntimeException("El archivo de datos {$this->ruta} está dañado o no se puede leer");
        }

        $inventario = new Inventario();
        foreach ($datos['productos'] ?? [] as $p) {
            $inventario->registrarProducto(new Producto(
                (string) $p['clave'],
                (string) $p['nombre'],
                (string) $p['categoria'],
                (int) $p['existenciaMinima'],
                (int) $p['existencia'],
            ));
        }
        foreach ($datos['movimientos'] ?? [] as $m) {
            $inventario->restaurarMovimiento(new Movimiento(
                (string) $m['tipo'],
                (string) $m['clave'],
                (int) $m['cantidad'],
                (string) $m['responsable'],
                (new DateTimeImmutable((string) $m['fecha']))
                    ->setTimezone(new DateTimeZone(date_default_timezone_get())),
            ));
        }

        return $inventario;
    }

    private function escribir(Inventario $inventario): void
    {
        $datos = [
            'version' => self::VERSION,
            'productos' => array_map(static fn (Producto $p): array => [
                'clave' => $p->clave,
                'nombre' => $p->nombre,
                'categoria' => $p->categoria,
                'existenciaMinima' => $p->existenciaMinima,
                'existencia' => $p->existencia,
            ], $inventario->productos()),
            'movimientos' => array_map(static fn (Movimiento $m): array => [
                'tipo' => $m->tipo,
                'clave' => $m->clave,
                'cantidad' => $m->cantidad,
                'responsable' => $m->responsable,
                'fecha' => $m->fecha->format(DATE_ATOM),
            ], $inventario->movimientos()),
        ];
        $json = json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

        $temporal = $this->ruta . '.' . bin2hex(random_bytes(6)) . '.tmp';
        if (file_put_contents($temporal, $json . "\n") === false || !rename($temporal, $this->ruta)) {
            @unlink($temporal);
            throw new RuntimeException("No se pudo guardar el archivo de datos {$this->ruta}");
        }
    }

    /**
     * @template T
     * @param callable(): T $operacion
     * @return T
     */
    private function conCandado(callable $operacion): mixed
    {
        $directorio = dirname($this->ruta);
        if (!is_dir($directorio) && !mkdir($directorio, 0775, true) && !is_dir($directorio)) {
            throw new RuntimeException("No se pudo crear el directorio de datos {$directorio}");
        }

        $candado = fopen($this->ruta . '.lock', 'c');
        if ($candado === false || !flock($candado, LOCK_EX)) {
            throw new RuntimeException("No se pudo bloquear el archivo de datos {$this->ruta}");
        }

        try {
            return $operacion();
        } finally {
            flock($candado, LOCK_UN);
            fclose($candado);
        }
    }
}
