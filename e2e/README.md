# Pruebas Playwright

Pruebas de **sistema** y de **aceptación**: Playwright abre Microsoft Edge y recorre el inventario como lo haría una persona en la papelería.

| Ejecución | Pruebas | Navegador | Tiempo | Resultado |
|---|---|---|---|---|
| 28 de octubre de 2026 · Playwright 1.48.2 | 18 | Microsoft Edge | 1.1 min | **18 de 18 aprobadas** |

Evidencias: [reporte completo con capturas](https://guadalupeca378-svg.github.io/sistema-inventario/reporte-28-10-2026/index.html) · [página de evidencias por caso](https://guadalupeca378-svg.github.io/sistema-inventario/ejecucion-28-10-2026/index.html)

## Pruebas y resultados

Cada clave abre la prueba en el reporte, con sus pasos y la pantalla final.

| Caso | Qué comprueba | Nivel | Archivo | Resultado |
|---|---|---|---|---|
| [CP‑01](https://guadalupeca378-svg.github.io/sistema-inventario/reporte-28-10-2026/index.html#?testId=f4cf5e785899667a54bc-8c9b34fe66b84fa9f582) | Registrar producto con datos válidos | Sistema | [inventario.spec.js](inventario.spec.js) | Aprobada · 5.8 s |
| [CP‑02](https://guadalupeca378-svg.github.io/sistema-inventario/reporte-28-10-2026/index.html#?testId=f4cf5e785899667a54bc-cefe00c28f5e74e597b5) | Registrar producto con código duplicado muestra error | Sistema | [inventario.spec.js](inventario.spec.js) | Aprobada · 4.0 s |
| [CP‑03](https://guadalupeca378-svg.github.io/sistema-inventario/reporte-28-10-2026/index.html#?testId=f4cf5e785899667a54bc-d2d3dc03338203ae0a78) | Consultar producto existente con el buscador | Sistema | [inventario.spec.js](inventario.spec.js) | Aprobada · 2.3 s |
| [CP‑04](https://guadalupeca378-svg.github.io/sistema-inventario/reporte-28-10-2026/index.html#?testId=f4cf5e785899667a54bc-9645a078e3893156bf14) | Consultar producto inexistente muestra mensaje | Sistema | [inventario.spec.js](inventario.spec.js) | Aprobada · 1.4 s |
| [CP‑05](https://guadalupeca378-svg.github.io/sistema-inventario/reporte-28-10-2026/index.html#?testId=f4cf5e785899667a54bc-489dcf058bf927faf91e) | Modificar producto con datos válidos | Sistema | [inventario.spec.js](inventario.spec.js) | Aprobada · 4.3 s |
| [CP‑06](https://guadalupeca378-svg.github.io/sistema-inventario/reporte-28-10-2026/index.html#?testId=f4cf5e785899667a54bc-000a93fb3269b2844f93) | Eliminar producto existente | Sistema | [inventario.spec.js](inventario.spec.js) | Aprobada · 2.5 s |
| [CP‑07](https://guadalupeca378-svg.github.io/sistema-inventario/reporte-28-10-2026/index.html#?testId=f4cf5e785899667a54bc-ad4a0375468e23018a16) | Entrada de producto incrementa la existencia | Sistema | [inventario.spec.js](inventario.spec.js) | Aprobada · 3.5 s |
| [CP‑08](https://guadalupeca378-svg.github.io/sistema-inventario/reporte-28-10-2026/index.html#?testId=f4cf5e785899667a54bc-4311bff81f95b4f8d93e) | Salida de producto reduce la existencia | Sistema | [inventario.spec.js](inventario.spec.js) | Aprobada · 1.5 s |
| [CP‑09](https://guadalupeca378-svg.github.io/sistema-inventario/reporte-28-10-2026/index.html#?testId=f4cf5e785899667a54bc-8a0bba7e5757887a134b) | Salida mayor a la existencia se rechaza | Sistema | [inventario.spec.js](inventario.spec.js) | Aprobada · 1.6 s |
| [CP‑10](https://guadalupeca378-svg.github.io/sistema-inventario/reporte-28-10-2026/index.html#?testId=f4cf5e785899667a54bc-c6aec625dbf80fa8fcc0) | Cantidad en cero no se acepta | Sistema | [inventario.spec.js](inventario.spec.js) | Aprobada · 1.8 s |
| [CP‑11](https://guadalupeca378-svg.github.io/sistema-inventario/reporte-28-10-2026/index.html#?testId=f4cf5e785899667a54bc-7f2802d15bee5fce5e38) | Consultar existencias indica el estado | Sistema | [inventario.spec.js](inventario.spec.js) | Aprobada · 1.7 s |
| [CP‑12](https://guadalupeca378-svg.github.io/sistema-inventario/reporte-28-10-2026/index.html#?testId=f4cf5e785899667a54bc-a6fad965ff76a51ce57f) | Reporte coincide con los movimientos | Sistema | [inventario.spec.js](inventario.spec.js) | Aprobada · 1.7 s |
| [CP‑23 · 1](https://guadalupeca378-svg.github.io/sistema-inventario/reporte-28-10-2026/index.html#?testId=f22e5abee46294e4ad8c-c9277ddc91181d143171) | La cliente registra un producto nuevo | Aceptación | [aceptacion.spec.js](aceptacion.spec.js) | Aprobada · 6.1 s |
| [CP‑23 · 2](https://guadalupeca378-svg.github.io/sistema-inventario/reporte-28-10-2026/index.html#?testId=f22e5abee46294e4ad8c-a53d4279c032a24fc27e) | Llega mercancía: entrada de 25 piezas | Aceptación | [aceptacion.spec.js](aceptacion.spec.js) | Aprobada · 4.7 s |
| [CP‑23 · 3](https://guadalupeca378-svg.github.io/sistema-inventario/reporte-28-10-2026/index.html#?testId=f22e5abee46294e4ad8c-2980c8e3908326c6713d) | Venta: salida de 8 piezas | Aceptación | [aceptacion.spec.js](aceptacion.spec.js) | Aprobada · 4.7 s |
| [CP‑23 · 4](https://guadalupeca378-svg.github.io/sistema-inventario/reporte-28-10-2026/index.html#?testId=f22e5abee46294e4ad8c-b12eafb45b192fd7a968) | No se puede vender más de lo que hay | Aceptación | [aceptacion.spec.js](aceptacion.spec.js) | Aprobada · 4.7 s |
| [CP‑23 · 5](https://guadalupeca378-svg.github.io/sistema-inventario/reporte-28-10-2026/index.html#?testId=f22e5abee46294e4ad8c-af0fc23142337437d024) | Existencias avisan cuando un producto queda bajo el mínimo | Aceptación | [aceptacion.spec.js](aceptacion.spec.js) | Aprobada · 6.3 s |
| [CP‑23 · 6](https://guadalupeca378-svg.github.io/sistema-inventario/reporte-28-10-2026/index.html#?testId=f22e5abee46294e4ad8c-7e949af4ea723e407116) | El reporte cuadra con lo que se movió | Aceptación | [aceptacion.spec.js](aceptacion.spec.js) | Aprobada · 1.7 s |

## Cómo las ejecutamos

```bash
npm ci
npx playwright install msedge
npx playwright test                        # las 18 pruebas
npx playwright show-report reporte-e2e     # abre el reporte HTML
npm run prueba:regresion                   # PHPUnit y Playwright juntos
```

Los datos de prueba son ficticios y se cargan desde un archivo aparte, sin tocar el inventario real. En GitHub Actions corren solas en la etapa 3 del flujo [ci-pruebas.yml](../.github/workflows/ci-pruebas.yml).
