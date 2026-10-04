# Estrategia de despliegue

## ¿Qué estrategia utilizaríamos para desplegar el sistema de inventario y por qué?

Se selecciona **Blue/Green Deployment**.

El inventario se usa todos los días en el almacén: si el sistema se detiene durante una actualización,
el personal no puede registrar entradas ni salidas y las existencias se descuadran. Blue/Green mantiene
dos ambientes de producción idénticos; mientras uno atiende a los usuarios (*Blue*), la nueva versión
se instala y se prueba en el otro (*Green*). Cuando la versión nueva pasa las pruebas, el tráfico se
cambia de un ambiente a otro en segundos. Si algo falla, se regresa al ambiente anterior de inmediato.

Como el sistema ya se ejecuta en contenedores Docker, tener dos ambientes cuesta poco: son dos
contenedores de la misma imagen en el mismo servidor, detrás de un proxy Nginx.

### Estrategias consideradas

| Estrategia | Motivo por el que no se eligió |
|---|---|
| Production Cutover | Requiere detener el sistema durante la actualización y no tiene regreso rápido. |
| Rolling / Continuous | Pensada para muchos servidores; aquí hay un solo servidor. |
| Canary | Útil con miles de usuarios; el almacén tiene pocos usuarios y todos deben ver los mismos datos. |
| Feature Flags | Se puede usar después para activar módulos nuevos, pero no resuelve la actualización del servidor. |
| A/B Testing | Sirve para comparar diseños con usuarios, no para liberar versiones de un sistema interno. |

## Tabla de análisis

| Elemento | Descripción |
|---|---|
| **Estrategia seleccionada** | Blue/Green Deployment con contenedores Docker y proxy inverso Nginx. |
| **Ambiente** | **Desarrollo:** equipo local con VS Code, PHP 8.2 y Docker. **Pruebas:** GitHub Actions + contenedor *Green* con base de datos de pruebas. **Producción:** contenedor *Blue* activo, PostgreSQL 16 con respaldos. |
| **Proceso** | 1) Se integra `release` en `main`. 2) GitHub Actions ejecuta las pruebas y publica la imagen en GHCR. 3) Se descarga la imagen y se levanta en el ambiente *Green*. 4) Se ejecutan migraciones y pruebas de humo. 5) Nginx cambia el tráfico de *Blue* a *Green*. 6) *Blue* queda en espera como respaldo. |
| **Riesgos** | Cambios en la base de datos incompatibles entre versiones; doble consumo de recursos del servidor; diferencias de configuración entre ambientes; pérdida de movimientos registrados durante el cambio. |
| **Ventajas** | Sin tiempo fuera de servicio; regreso a la versión anterior en segundos; se prueba en un ambiente idéntico a producción; el almacén no detiene su operación. |
| **Plan de recuperación** | Si las pruebas de humo fallan o hay errores después del cambio: 1) Nginx regresa el tráfico a *Blue* cambiando el upstream y recargando (`nginx -s reload`). 2) Se restaura el respaldo de PostgreSQL tomado antes de la migración si hubo cambios de datos. 3) Se abre un *issue* con el defecto y la corrección entra por una rama `fix/*`. 4) Se repite el proceso desde la rama `release`. |

## Diagrama

```
                 ┌──────────────────────┐
 Usuarios ─────► │   Nginx (proxy)      │
                 └─────────┬────────────┘
                           │ tráfico activo
             ┌─────────────┴──────────────┐
             ▼                            ▼
   ┌───────────────────┐        ┌───────────────────┐
   │  BLUE  v1.0.0     │        │  GREEN  v1.1.0    │
   │  (producción)     │        │  (nueva versión)  │
   └─────────┬─────────┘        └─────────┬─────────┘
             └──────────────┬─────────────┘
                            ▼
                  ┌───────────────────┐
                  │  PostgreSQL 16    │
                  │  + respaldos      │
                  └───────────────────┘
```

## Comandos de referencia

```bash
# Levantar la nueva versión en Green
docker pull ghcr.io/guadalupeca378-svg/sistema-inventario:1.1.0
docker run -d --name inventario-green -p 8082:80 ghcr.io/guadalupeca378-svg/sistema-inventario:1.1.0

# Prueba de humo
curl --fail http://localhost:8082/

# Cambiar el tráfico (upstream de Nginx) y recargar
sed -i 's/8081/8082/' /etc/nginx/conf.d/inventario.conf && nginx -s reload

# Recuperación: regresar a Blue
sed -i 's/8082/8081/' /etc/nginx/conf.d/inventario.conf && nginx -s reload
```
