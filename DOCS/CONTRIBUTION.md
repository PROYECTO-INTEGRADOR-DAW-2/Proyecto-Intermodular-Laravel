# Contribution Guidelines & Team Organization

## Branch Strategy
- **`main`**: Production-ready branch. Only stable code is merged here.
- **`Albert`**: Main development branch for new features and structural changes.
- **Feature Branches**: `feat/[name]`, `fix/[name]` are used for temporary work before merging into `Albert` or `main`.

## Code Style
### Backend (PHP)
- **Standard**: PSR-12.
- **Tools**: `php-cs-fixer` (recommended).
- **Naming**: CamelCase for methods, snake_case for database columns and variables.

### Frontend (JavaScript/Vue)
- **Standard**: ESLint + Prettier.
- **Composition API**: Use script setup syntax for Vue components.
- **Components**: PascalCase for component filenames.

## Contribution Process
1. Create a branch from `Albert`.
2. Commit changes following [Conventional Commits](https://www.conventionalcommits.org/).
3. Open a Pull Request to `Albert`.
4. Ensure CI/CD tests pass.
5. Request a peer review.
6. Merge after approval.

## Responsibilities
- **Backend Team**: API, Database, Security, N8N Integration.
- **Frontend Team**: UI/UX, Vue Components, Pinia Stores, Validation.
- **DevOps**: Infrastructure (AWS), Docker, CI/CD Pipelines, DNS/HTTPS.
