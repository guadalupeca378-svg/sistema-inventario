# Flujo de trabajo para control de versiones

El equipo utiliza **Git Flow simplificado** sobre GitHub.

```
main ──────────────────────────────────────────────●── v1.0.0 (producción)
 │                                                 ↑
 ├── develop ──●──────●──────●──────●──────●───────┤
 │      │      ↑      ↑      ↑                     │
 │      ├── feature/productos                      │
 │      ├── feature/inventario                     │
 │      └── feature/reportes                       │
 │                                                 │
 └── release ──────────────────────────────────────┘
```

| Rama | Propósito | Se crea desde | Se integra en |
|---|---|---|---|
| `main` | Código en producción, siempre estable. | — | — |
| `develop` | Integración de funcionalidades terminadas. | `main` | `release` |
| `feature/*` | Una funcionalidad por rama (productos, inventario, reportes). | `develop` | `develop` (Pull Request) |
| `release` | Preparación y verificación de la versión a liberar. | `develop` | `main` (Pull Request) |

## 1. Creación del repositorio

```bash
mkdir sistema-inventario && cd sistema-inventario
git init                       # rama inicial: main
git add .
git commit -m "chore: estructura inicial del repositorio"
git remote add origin https://github.com/guadalupeca378-svg/sistema-inventario.git
git push -u origin main
```

## 2. Creación de ramas

```bash
git switch -c develop
git push -u origin develop
git switch -c feature/productos develop
```

## 3. Desarrollo de funcionalidades

Cada integrante trabaja en su rama `feature/*` sin afectar `develop` ni `main`.
Se programa la funcionalidad y sus pruebas en `tests/`, y se ejecuta `composer test` localmente.

## 4. Commit

Se usan mensajes con el formato *Conventional Commits*:

| Prefijo | Uso |
|---|---|
| `feat:` | Nueva funcionalidad |
| `fix:` | Corrección de un defecto |
| `test:` | Pruebas |
| `docs:` | Documentación |
| `ci:` / `build:` | Integración continua / contenedores |
| `chore:` | Mantenimiento |

```bash
git add src/Producto.php tests/ProductosTest.php
git commit -m "feat(productos): registrar, consultar, modificar y eliminar productos"
```

## 5. Push

```bash
git push -u origin feature/productos
```

Cada push dispara el workflow **CI - Pruebas y construcción** en GitHub Actions.

## 6. Pull Request

En GitHub se abre un Pull Request `feature/productos → develop` con la descripción de los cambios
y los casos de prueba que cubre.

## 7. Revisión del código

- Otro integrante revisa el código y deja comentarios.
- GitHub Actions debe terminar en verde (todas las pruebas aprobadas).
- Si hay observaciones, se corrigen en la misma rama con nuevos commits.

## 8. Integración a develop

Aprobado el Pull Request se hace **Merge** hacia `develop` y se elimina la rama `feature/*`.

```bash
git switch develop
git pull origin develop
```

## 9. Liberación a main

```bash
git switch -c release develop
# ajustes finales: versión, CHANGELOG, resultados de pruebas
git push -u origin release
```

Se abre un Pull Request `release → main`. Al integrarse se crea la etiqueta de versión:

```bash
git switch main && git pull
git tag -a v1.0.0 -m "Versión 1.0.0"
git push origin v1.0.0
```

La integración en `main` dispara el workflow **CD - Publicar imagen (Blue/Green)**.
