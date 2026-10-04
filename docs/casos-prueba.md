# Casos de prueba

**Proyecto:** Sistema de Control de Inventario
**Herramienta de ejecución:** PHPUnit 11 (`composer test`) y GitHub Actions
**Responsable:** Karla Guadalupe Madrazo Can (Tester)

Cada caso está automatizado en la carpeta `tests/` y se identifica con su ID en la salida `--testdox`.

## Resumen de casos

| ID | Funcionalidad | Entrada | Resultado esperado | Resultado obtenido | Estado |
|---|---|---|---|---|---|
| CP-01 | Registrar producto | Datos válidos (clave `P-001`, nombre, categoría, existencia mínima) | Producto registrado | Producto P-002 registrado con existencia 0 | ✅ Aprobado |
| CP-02 | Registrar producto | Código duplicado (`P-001` ya existe) | Mostrar error "La clave ya existe" | Error: "La clave P-001 ya existe" | ✅ Aprobado |
| CP-03 | Consultar producto | Código existente (`P-001`) | Mostrar producto | Se muestran nombre, categoría y mínimo de P-001 | ✅ Aprobado |
| CP-04 | Consultar producto | Código inexistente (`P-999`) | Mostrar mensaje "Producto no encontrado" | Mensaje: "Producto P-999 no encontrado" | ✅ Aprobado |
| CP-05 | Modificar producto | Datos válidos (nuevo nombre y mínimo) | Información actualizada | Nombre y mínimo actualizados | ✅ Aprobado |
| CP-06 | Eliminar producto | Producto existente | Producto eliminado | P-001 eliminado; la consulta lo reporta como no encontrado | ✅ Aprobado |
| CP-07 | Entrada de producto | Cantidad válida (+20) | Incrementar existencia | Existencia 0 → 20; movimiento con fecha y responsable | ✅ Aprobado |
| CP-08 | Salida de producto | Cantidad disponible (−5 de 20) | Reducir existencia | Existencia 20 → 15 | ✅ Aprobado |
| CP-09 | Salida de producto | Cantidad mayor a la existencia (−50 de 20) | Rechazar movimiento, existencia sin cambio | Error "Existencia insuficiente"; la existencia sigue en 20 | ✅ Aprobado |
| CP-10 | Entrada de producto | Cantidad en cero o negativa | Rechazar movimiento | Error "La cantidad debe ser mayor a cero" (0 y −3) | ✅ Aprobado |
| CP-11 | Consultar existencias | Productos con existencia normal y bajo el mínimo | Lista con estado "Normal" / "Bajo mínimo" | P-001 "Bajo mínimo", P-002 "Normal" | ✅ Aprobado |
| CP-12 | Reporte de inventario | Entradas y salidas registradas | Totales del reporte iguales a los movimientos | Entradas 34, salidas 5; P-001 = 30 − 5 = 25 | ✅ Aprobado |

**Fecha de ejecución:** 4 de octubre de 2026 · **Resultado:** 12 de 12 casos aprobados (100 %).

### Salida de la ejecución (`composer test`)

```
Módulo de inventario (entradas, salidas y existencias)
 ✔ CP-07 Entrada de producto con cantidad válida incrementa la existencia
 ✔ CP-08 Salida de producto con cantidad disponible reduce la existencia
 ✔ CP-09 Salida mayor a la existencia se rechaza sin modificar la existencia
 ✔ CP-10 Entrada con cantidad en cero o negativa se rechaza
 ✔ CP-11 Consultar existencias indica productos normales y bajo el mínimo

Módulo de productos
 ✔ CP-01 Registrar producto con datos válidos
 ✔ CP-02 Registrar producto con código duplicado muestra error
 ✔ CP-03 Consultar producto con código existente
 ✔ CP-04 Consultar producto con código inexistente muestra mensaje
 ✔ CP-05 Modificar producto con datos válidos
 ✔ CP-06 Eliminar producto existente

Reporte de inventario (integración)
 ✔ CP-12 Reporte básico: totales iguales a los movimientos registrados

OK (12 tests, 28 assertions)
```

## Detalle de los casos

### CP-01 · Registrar producto con datos válidos
- **Precondición:** el inventario no contiene la clave `P-001`.
- **Pasos:** registrar `P-001`, "Tornillo 1/4", categoría "Ferretería", existencia mínima 10.
- **Resultado esperado:** el producto queda registrado con existencia 0.
- **Tipo:** funcional · **Prioridad:** alta

### CP-02 · Registrar producto con código duplicado
- **Precondición:** existe el producto `P-001`.
- **Pasos:** registrar otro producto con la clave `P-001`.
- **Resultado esperado:** el sistema rechaza el registro con el mensaje "La clave P-001 ya existe".
- **Tipo:** validación de datos · **Prioridad:** alta

### CP-03 · Consultar producto existente
- **Pasos:** consultar la clave `P-001`.
- **Resultado esperado:** se muestran nombre, categoría, existencia y mínimo.
- **Tipo:** funcional · **Prioridad:** alta

### CP-04 · Consultar producto inexistente
- **Pasos:** consultar la clave `P-999`.
- **Resultado esperado:** mensaje "Producto P-999 no encontrado".
- **Tipo:** funcional · **Prioridad:** media

### CP-05 · Modificar producto
- **Pasos:** cambiar el nombre a "Tornillo 1/4 galvanizado" y el mínimo a 15.
- **Resultado esperado:** al consultar se muestran los datos nuevos.
- **Tipo:** funcional · **Prioridad:** alta

### CP-06 · Eliminar producto
- **Pasos:** eliminar `P-001` y volver a consultarlo.
- **Resultado esperado:** el producto ya no existe en el inventario.
- **Tipo:** funcional · **Prioridad:** alta

### CP-07 · Entrada de producto
- **Pasos:** registrar una entrada de 20 piezas de `P-001`.
- **Resultado esperado:** la existencia pasa de 0 a 20 y el movimiento guarda fecha y responsable.
- **Tipo:** funcional · **Prioridad:** alta

### CP-08 · Salida de producto
- **Pasos:** con existencia 20, registrar una salida de 5.
- **Resultado esperado:** la existencia queda en 15.
- **Tipo:** funcional · **Prioridad:** alta

### CP-09 · Salida mayor a la existencia
- **Pasos:** con existencia 20, registrar una salida de 50.
- **Resultado esperado:** el movimiento se rechaza y la existencia sigue en 20.
- **Tipo:** validación de datos / regla del negocio · **Prioridad:** alta

### CP-10 · Entrada con cantidad inválida
- **Pasos:** registrar una entrada de 0 y otra de −3.
- **Resultado esperado:** ambas se rechazan con "La cantidad debe ser mayor a cero".
- **Tipo:** validación de datos · **Prioridad:** media

### CP-11 · Consultar existencias
- **Pasos:** registrar dos productos, uno sobre el mínimo y otro debajo; consultar existencias.
- **Resultado esperado:** cada producto aparece con su estado "Normal" o "Bajo mínimo".
- **Tipo:** funcional · **Prioridad:** media

### CP-12 · Reporte básico de inventario
- **Pasos:** registrar entradas y salidas en varios productos y generar el reporte.
- **Resultado esperado:** total de entradas, total de salidas y existencia de cada producto coinciden con
  los movimientos.
- **Tipo:** integración · **Prioridad:** alta
