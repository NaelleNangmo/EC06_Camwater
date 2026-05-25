# Guide de contribution — CamwaterApp

## Branches (GitFlow)

| Branche | Usage |
|---|---|
| `main` | Code stable en production |
| `develop` | Intégration principale |
| `feature/*` | Nouvelle fonctionnalité |
| `fix/*` / `bugfix/*` | Correction de bug sur develop |
| `hotfix/*` | Urgence critique sur production |
| `ci/*` | Pipeline et infrastructure |

## Commits (Conventional Commits)

Format : `type(portée): description courte en minuscules, impératif, sans point final`

| Type | Usage | Exemple |
|---|---|---|
| `feat` | Nouvelle fonctionnalité | `feat(api): ajouter endpoint statistiques` |
| `fix` | Correction de bug | `fix(auth): corriger expiration du token` |
| `docs` | Documentation | `docs(readme): ajouter guide docker` |
| `test` | Tests | `test(facture): ajouter tests calcul montant` |
| `refactor` | Refactoring | `refactor(service): simplifier logique cache` |
| `chore` | Config/dépendances | `chore(deps): mettre a jour phpunit` |
| `ci` | Pipeline CI/CD | `ci(docker): ajouter dockerfile multi-stage` |
| `style` | Formatage | `style(code): corriger issues pint` |

## Règles

- **Un commit = une seule action** (commit atomique)
- **Pas de code cassé** sur `develop` ou `main`
- **Tous les merges passent par des Pull Requests**
- Les branches `feature/*` partent de `develop`
- Les branches `hotfix/*` partent de `main`

## Workflow

```
feature/ma-feature  →  develop  →  main
fix/mon-bug         →  develop
hotfix/urgence      →  main (+ backport develop)
ci/pipeline         →  develop  →  main
```

## Tests avant PR

```bash
php artisan test                          # PHPUnit
./vendor/bin/pint --test                  # Lint
php vendor/atoum/atoum/bin/atoum ...      # atoum
php tests/SimpleTest/FactureSimpleTest.php # SimpleTest
```
