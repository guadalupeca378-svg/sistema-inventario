# Casos de prueba

**Proyecto:** Sistema de Control de Inventario
**Herramienta de ejecución:** PHPUnit 11 (`composer test`) y GitHub Actions
**Responsable:** Karla Guadalupe Madrazo Can (Tester)

Cada caso está automatizado en la carpeta `tests/` y se identifica con su ID en la salida `--testdox`.

## Resumen de casos

| ID | Funcionalidad | Entrada | Resultado esperado | Resultado obtenido | Estado |
|---|---|---|---|---|---|
| CP-01 | Registrar producto | Datos válidos (clave `P-001`, nombre, categoría, existencia mínima) | Producto registrado | Pendiente | Pendiente |
| CP-02 | Registrar producto | Código duplicado (`P-001` ya existe) | Mostrar error "La clave ya existe" | Pendiente | Pendiente |
| CP-03 | Consultar producto | Código existente (`P-001`) | Mostrar producto | Pendiente | Pendiente |
| CP-04 | Consultar producto | Código inexistente (`P-999`) | Mostrar mensaje "Producto no encontrado" | Pendiente | Pendiente |
| CP-05 | Modificar producto | Datos válidos (nuevo nombre y mínimo) | Información actualizada | Pendiente | Pendiente |
| CP-06 | Eliminar producto | Producto existente | Producto eliminado | Pendiente | Pendiente |
| CP-07 | Entrada de producto | Cantidad válida (+20) | Incrementar existencia | Pendiente | Pendiente |
| CP-08 | Salida de producto | Cantidad disponible (−5 de 20) | Reducir existencia | Pendiente | Pendiente |
| CP-09 | Salida de producto | Cantidad mayor a la existencia (−50 de 20) | Rechazar movimiento, existencia sin cambio | Pendiente | Pendiente |
| CP-10 | Entrada de producto | Cantidad en cero o negativa | Rechazar movimiento | Pendiente | Pendiente |
| CP-11 | Consultar existencias | Productos con existencia normal y bajo el mínimo | Lista con estado "Normal" / "Bajo mínimo" | Pendiente | Pendiente |
| CP-12 | Reporte de inventario | Entradas y salidas registradas | Totales del reporte iguales a los movimientos | Pendiente | Pendiente |

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
