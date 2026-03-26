# Pautas de Contribución y Organización del Equipo

## Estrategia de Ramas (Branching)
- **`main`**: Rama lista para producción. Solo se fusiona código estable aquí.
- **`Albert`**: Rama principal de desarrollo para nuevas características y cambios estructurales.
- **Ramas de Características**: `feat/[nombre]`, `fix/[nombre]` se utilizan para trabajo temporal antes de fusionarse en `Albert` o `main`.

## Estilo de Código
### Backend (PHP)
- **Estándar**: PSR-12.
- **Herramientas**: `php-cs-fixer` (recomendado).
- **Nomenclatura**: CamelCase para métodos, snake_case para columnas de base de datos y variables.

### Frontend (JavaScript/Vue)
- **Estándar**: ESLint + Prettier.
- **Composition API**: Usar la sintaxis script setup para los componentes de Vue.
- **Componentes**: PascalCase para los nombres de archivo de los componentes.

## Proceso de Contribución
1. Crea una rama desde `Albert`.
2. Confirma los cambios (commit) siguiendo los [Commits Convencionales](https://www.conventionalcommits.org/).
3. Abre una Pull Request hacia `Albert`.
4. Asegúrate de que las pruebas de CI/CD pasen.
5. Solicita una revisión por pares (peer review).
6. Fusiona después de la aprobación.

## Responsabilidades
- **Equipo de Backend**: API, Base de datos, Seguridad, Integración con N8N.
- **Equipo de Frontend**: IU/UX, Componentes de Vue, Almacenes de Pinia, Validación.
- **DevOps**: Infraestructura (AWS), Docker, Pipelines de CI/CD, DNS/HTTPS.
