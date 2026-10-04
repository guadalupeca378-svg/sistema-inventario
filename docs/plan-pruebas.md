# Plan de pruebas

**Proyecto:** Sistema de Control de Inventario
**Versión del plan:** 1.0
**Responsable del plan:** Karla Guadalupe Madrazo Can (Tester)

## 1. Objetivo

Verificar que las funciones principales del sistema de inventario operen de acuerdo con los requisitos
establecidos por el cliente: que los productos se registren, consulten, modifiquen y eliminen
correctamente, que las entradas y salidas actualicen las existencias sin descuadres y que el reporte
de inventario muestre cifras que coincidan con los movimientos registrados.

## 2. Alcance

**Incluye:**

- Módulo de productos: alta, consulta, modificación y baja.
- Módulo de inventario: consulta de existencias, entradas y salidas.
- Reporte básico de inventario.
- Validaciones de datos y reglas del negocio (no permitir salidas mayores a la existencia,
  claves duplicadas, cantidades en cero o negativas).

**No incluye (fuera del alcance de esta versión):**

- Aplicación móvil y alertas automáticas (gestión de cambio, fase 2).
- Pruebas de carga con más de 50 usuarios simultáneos.
- Pruebas de seguridad avanzadas (pentesting).

## 3. Elementos que serán probados

| Elemento | Clase / componente | Requisito asociado |
|---|---|---|
| Registro de productos | `Inventario::registrarProducto()` | RF-01 Registrar productos |
| Consulta de productos | `Inventario::consultarProducto()` | RF-02 Consultar productos |
| Modificación de productos | `Inventario::modificarProducto()` | RF-03 Modificar información |
| Eliminación de productos | `Inventario::eliminarProducto()` | RF-04 Eliminar productos |
| Consulta de existencias | `Inventario::consultarExistencias()` | RF-05 Consultar existencias |
| Entradas y salidas | `Inventario::registrarEntrada()` / `registrarSalida()` | RF-06 Registrar entradas y salidas |
| Reporte de inventario | `ReporteInventario::generar()` | RF-07 Generar reporte básico |

## 4. Tipos de pruebas

| Tipo | Qué se verifica | Cómo se aplica |
|---|---|---|
| **Pruebas funcionales** | Que cada función haga lo que dice el requisito (registrar, consultar, modificar, eliminar). | Casos CP-01 a CP-08 con PHPUnit. |
| **Pruebas de integración** | Que un movimiento modifique la existencia y se refleje en el reporte. | Caso CP-12 (inventario + reporte). |
| **Pruebas de validación de datos** | Claves duplicadas, cantidades en cero o negativas, salidas mayores a la existencia. | Casos CP-02, CP-09 y CP-10. |
| **Pruebas de regresión** | Que una corrección no descomponga lo que ya funcionaba. | GitHub Actions vuelve a ejecutar **todos** los casos en cada push y Pull Request. |

## 5. Criterios de aceptación

- El 100 % de los casos de prueba de prioridad **alta** resultan *Aprobados*.
- No existen defectos críticos ni altos abiertos.
- Las existencias después de cualquier secuencia de movimientos son iguales a
  `existencia inicial + entradas − salidas`.
- El reporte de inventario coincide con la suma manual de los movimientos.
- El flujo de CI en GitHub Actions termina en verde en la rama `develop` antes de liberar a `main`.

## 6. Criterios de aprobación / rechazo

| Resultado | Criterio |
|---|---|
| **Aprobado** | El resultado obtenido es igual al resultado esperado en el caso de prueba. |
| **No aprobado** | El resultado obtenido difiere del esperado, el sistema muestra un error no controlado o guarda datos inválidos. |
| **Bloqueado** | No se puede ejecutar porque depende de otra función con defecto. |

**Criterio de suspensión:** se suspenden las pruebas si falla la construcción del proyecto o si más del
30 % de los casos de un módulo quedan *No aprobados*. Se reanudan cuando el desarrollador entrega la
corrección en una nueva rama `feature/*` integrada por Pull Request.

## 7. Ambiente de pruebas

| Ambiente | Configuración |
|---|---|
| Local (desarrollo) | Windows 11 Pro, PHP 8.2.34, Composer 2.10, PHPUnit 11.5, Visual Studio Code 1.140 |
| Integración continua | GitHub Actions, `ubuntu-latest`, PHP 8.2 y 8.3 |
| Contenedor | Docker, imagen `php:8.2-apache`, puerto 8080 |
| Base de datos de pruebas | PostgreSQL 16 en contenedor (`docker-compose.yml`), datos de prueba independientes de producción |

## 8. Responsables

| Integrante | Rol | Responsabilidad en las pruebas |
|---|---|---|
| Karla Guadalupe Madrazo Can | Tester | Diseña el plan y los casos, ejecuta las pruebas y registra resultados y defectos. |
| Manuel Alfonso Castro Escalante | Líder / Desarrollador | Escribe las pruebas unitarias junto al código y corrige los defectos. |
| Mirley Madaí Gómez Acosta | Analista | Valida que cada caso corresponda a un requisito. |
| Jorge Pérez Heredia | Diseñador | Revisa los mensajes de error y la usabilidad de las pantallas. |
| Flor Yazmín Cordero Estañol | Cliente | Ejecuta las pruebas de aceptación y firma la conformidad. |

## 9. Herramientas utilizadas

| Herramienta | Uso |
|---|---|
| PHPUnit 11 | Ejecución automatizada de los casos de prueba. |
| GitHub Actions | Pruebas de regresión automáticas en cada push / Pull Request. |
| Docker | Ambiente de pruebas idéntico al de producción. |
| GitHub Pull Requests | Revisión de código antes de integrar. |
| Trello | Registro y seguimiento de defectos (lista *Pruebas*). |
| Markdown | Documentación del plan y de los casos (`docs/`). |
