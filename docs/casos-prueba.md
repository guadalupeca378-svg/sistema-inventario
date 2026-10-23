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
| CP-13 | Persistencia de datos | Inventario con productos, entradas y salidas guardado en JSON | Al volver a cargar se conservan productos, existencias y movimientos con fecha, sin duplicarlos | PAP-001 con existencia 15, 2 movimientos y la misma fecha; sin archivos temporales | ✅ Aprobado |
| CP-14 | Primer arranque | Archivo de datos inexistente | Se crea el inventario con datos de ejemplo de la papelería | 12 productos PAP-001…PAP-012, movimientos de ejemplo y productos bajo mínimo; existencia = entradas − salidas | ✅ Aprobado |
| CP-15 | Registrar producto | Nombre formado solo por espacios | Rechazar el registro | Error "La clave y el nombre son obligatorios" | ✅ Aprobado |
| CP-16 | Registrar producto | Existencia mínima −1 | Rechazar el registro | Error "La existencia mínima no puede ser negativa" | ✅ Aprobado |
| CP-17 | Modificar producto | Clave inexistente `X-1` | Mensaje de producto no encontrado | Error "Producto X-1 no encontrado" | ✅ Aprobado |
| CP-18 | Eliminar producto | Eliminar un producto con movimientos y registrar otro con la misma clave | El reporte no mezcla los movimientos del producto eliminado | 1.ª ejecución falló (DEF-01); tras la corrección la clave se rechaza y el historial se conserva | ✅ Reprueba aprobada |
| CP-19 | Salida de producto | Salida igual a la existencia (5 de 5) | Aceptar y dejar existencia 0 | Existencia 0 | ✅ Aprobado |
| CP-20 | Entrada de producto | Responsable vacío | Rechazar el movimiento | 1.ª ejecución falló (DEF-02); tras la corrección: "El responsable es obligatorio" | ✅ Reprueba aprobada |
| CP-23 | Aceptación (navegador) | Lista de la cliente: registrar, entrada 25, ventas 8 y 10, intento de vender 30, existencias y reporte | Cada paso se cumple; reporte con entradas 25, salidas 18 y existencia 7 | 6 de 6 pasos aprobados en Edge (`e2e/aceptacion.spec.js`) | ✅ Aprobado |

**Fecha de ejecución:** 14 de octubre de 2026 · **Resultado:** 20 de 20 casos aprobados (100 %), incluidas las repruebas de DEF-01 y DEF-02. Pruebas en navegador (Playwright): 12 de 12 aprobadas.

### Salida de la ejecución (`composer test`)

```
Módulo de inventario (entradas, salidas y existencias)
 ✔ CP-07 Entrada de producto con cantidad válida incrementa la existencia
 ✔ CP-08 Salida de producto con cantidad disponible reduce la existencia
 ✔ CP-09 Salida mayor a la existencia se rechaza sin modificar la existencia
 ✔ CP-10 Entrada con cantidad en cero o negativa se rechaza
 ✔ CP-11 Consultar existencias indica productos normales y bajo el mínimo
 ✔ CP-19 Salida igual a la existencia deja el producto en cero
 ✔ CP-20 Movimiento sin responsable se rechaza

Módulo de productos
 ✔ CP-01 Registrar producto con datos válidos
 ✔ CP-02 Registrar producto con código duplicado muestra error
 ✔ CP-03 Consultar producto con código existente
 ✔ CP-04 Consultar producto con código inexistente muestra mensaje
 ✔ CP-05 Modificar producto con datos válidos
 ✔ CP-06 Eliminar producto existente
 ✔ CP-15 Registrar producto con nombre vacío se rechaza
 ✔ CP-16 Registrar producto con existencia mínima negativa se rechaza
 ✔ CP-17 Modificar producto inexistente muestra mensaje
 ✔ CP-18 Una clave eliminada con movimientos no se reutiliza ni altera el reporte

Persistencia del inventario en JSON
 ✔ CP-13 Guardar y volver a cargar conserva productos, existencias y movimientos con fecha
 ✔ CP-14 El primer arranque crea el inventario con datos de ejemplo de la papelería

Reporte de inventario (integración)
 ✔ CP-12 Reporte básico: totales iguales a los movimientos registrados

OK (20 tests, 70 assertions)
```

## Detalle de los casos

### CP-01 · Registrar producto con datos válidos
- **Precondición:** el inventario no contiene la clave `P-001`.
- **Pasos:** registrar `P-001`, "Cuaderno profesional raya", categoría "Escolar", existencia mínima 10.
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
- **Pasos:** cambiar el nombre a "Cuaderno profesional cuadro chico" y el mínimo a 15.
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

### CP-13 · Guardar y volver a cargar el inventario
- **Precondición:** archivo de datos temporal vacío.
- **Pasos:** registrar `PAP-001`, una entrada de 20 y una salida de 5; guardar con `AlmacenJson`, volver a
  cargar, guardar y cargar otra vez.
- **Resultado esperado:** el producto conserva nombre, categoría, mínimo y existencia 15; hay exactamente
  2 movimientos con su tipo, responsable y fecha original; no quedan archivos temporales.
- **Tipo:** integración (persistencia) · **Prioridad:** alta

### CP-14 · Primer arranque con datos de ejemplo
- **Precondición:** el archivo de datos no existe.
- **Pasos:** cargar el inventario con `AlmacenJson`.
- **Resultado esperado:** se crea el archivo con los productos de ejemplo de Papelería Los Pingüinos
  (categorías Escolar, Oficina, Papel y Arte), movimientos con responsables y al menos un producto bajo
  el mínimo; en el reporte, la existencia de cada producto es igual a entradas − salidas.
- **Tipo:** funcional · **Prioridad:** media
