# Pruebas PHPUnit

Pruebas **unitarias** y de **integración** del Sistema de Control de Inventario de la Papelería Los Pingüinos.

| Ejecución | Pruebas | Verificaciones | Tiempo | Resultado |
|---|---|---|---|---|
| 28 de octubre de 2026 · PHPUnit 11.5.56 · PHP 8.2.34 | 20 | 70 | 0.265 s | **20 de 20 aprobadas** |

Evidencias: [página de evidencias por caso](https://guadalupeca378-svg.github.io/sistema-inventario/ejecucion-28-10-2026/index.html) · [rondas en GitHub Actions](https://github.com/guadalupeca378-svg/sistema-inventario/actions/workflows/ci-pruebas.yml)

## Pruebas y resultados

Las claves con enlace son casos del plan de pruebas y abren su evidencia.

| Caso | Qué comprueba | Nivel | Archivo | Resultado |
|---|---|---|---|---|
| [CP-01](https://guadalupeca378-svg.github.io/sistema-inventario/ejecucion-28-10-2026/index.html#cp-01) | Registrar producto con datos válidos | Unitaria | [ProductosTest.php](ProductosTest.php) | Aprobada · 0.000 s |
| [CP-02](https://guadalupeca378-svg.github.io/sistema-inventario/ejecucion-28-10-2026/index.html#cp-02) | Registrar producto con código duplicado muestra error | Unitaria | [ProductosTest.php](ProductosTest.php) | Aprobada · 0.001 s |
| CP-03 | Consultar producto con código existente | Unitaria | [ProductosTest.php](ProductosTest.php) | Aprobada · 0.000 s |
| CP-04 | Consultar producto con código inexistente muestra mensaje | Unitaria | [ProductosTest.php](ProductosTest.php) | Aprobada · 0.000 s |
| CP-05 | Modificar producto con datos válidos | Unitaria | [ProductosTest.php](ProductosTest.php) | Aprobada · 0.000 s |
| CP-06 | Eliminar producto existente | Unitaria | [ProductosTest.php](ProductosTest.php) | Aprobada · 0.000 s |
| CP-15 | Registrar producto con nombre vacío se rechaza | Unitaria | [ProductosTest.php](ProductosTest.php) | Aprobada · 0.000 s |
| CP-16 | Registrar producto con existencia mínima negativa se rechaza | Unitaria | [ProductosTest.php](ProductosTest.php) | Aprobada · 0.000 s |
| CP-17 | Modificar producto inexistente muestra mensaje | Unitaria | [ProductosTest.php](ProductosTest.php) | Aprobada · 0.000 s |
| CP-18 | Una clave eliminada con movimientos no se reutiliza ni altera el reporte | Integración | [ProductosTest.php](ProductosTest.php) | Aprobada · 0.000 s |
| [CP-07](https://guadalupeca378-svg.github.io/sistema-inventario/ejecucion-28-10-2026/index.html#cp-07) | Entrada de producto con cantidad válida incrementa la existencia | Unitaria | [MovimientosTest.php](MovimientosTest.php) | Aprobada · 0.000 s |
| CP-08 | Salida de producto con cantidad disponible reduce la existencia | Unitaria | [MovimientosTest.php](MovimientosTest.php) | Aprobada · 0.000 s |
| [CP-09](https://guadalupeca378-svg.github.io/sistema-inventario/ejecucion-28-10-2026/index.html#cp-09) | Salida mayor a la existencia se rechaza sin modificar la existencia | Unitaria | [MovimientosTest.php](MovimientosTest.php) | Aprobada · 0.001 s |
| CP-10 | Entrada con cantidad en cero o negativa se rechaza | Unitaria | [MovimientosTest.php](MovimientosTest.php) | Aprobada · 0.000 s |
| [CP-11](https://guadalupeca378-svg.github.io/sistema-inventario/ejecucion-28-10-2026/index.html#cp-11) | Consultar existencias indica productos normales y bajo el mínimo | Unitaria | [MovimientosTest.php](MovimientosTest.php) | Aprobada · 0.000 s |
| CP-19 | Salida igual a la existencia deja el producto en cero | Unitaria | [MovimientosTest.php](MovimientosTest.php) | Aprobada · 0.000 s |
| CP-20 | Movimiento sin responsable se rechaza | Unitaria | [MovimientosTest.php](MovimientosTest.php) | Aprobada · 0.000 s |
| [CP-12](https://guadalupeca378-svg.github.io/sistema-inventario/ejecucion-28-10-2026/index.html#cp-12) | Reporte básico: totales iguales a los movimientos registrados | Integración | [ReporteTest.php](ReporteTest.php) | Aprobada · 0.000 s |
| [CP-13](https://guadalupeca378-svg.github.io/sistema-inventario/ejecucion-28-10-2026/index.html#cp-13) | Guardar y volver a cargar conserva productos, existencias y movimientos con fecha | Integración | [AlmacenJsonTest.php](AlmacenJsonTest.php) | Aprobada · 0.250 s |
| CP-14 | El primer arranque crea el inventario con datos de ejemplo de la papelería | Integración | [AlmacenJsonTest.php](AlmacenJsonTest.php) | Aprobada · 0.005 s |

## Cómo las ejecutamos

```bash
composer install
vendor/bin/phpunit --testdox            # las 20 pruebas
vendor/bin/phpunit --group unitaria     # solo las 16 unitarias
vendor/bin/phpunit --group integracion  # solo las 4 de integración
```

En GitHub Actions corren solas con cada push, en las etapas 1 y 2 del flujo [ci-pruebas.yml](../.github/workflows/ci-pruebas.yml).
