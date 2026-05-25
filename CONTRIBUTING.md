# Guide de contribution — Kaba-Delivery

## Branches (GitFlow)
- `main` : code stable en production
- `develop` : intégration principale
- `feature/*` : nouvelle fonctionnalité
- `fix/*` : correction de bug
- `hotfix/*` : urgence production
- `ci/*` : pipeline et infrastructure

## Commits (Conventional Commits)
Format : `type(portée): description courte en minuscules, impératif, sans point final`

| Type | Usage |
|---|---|
| feat | nouvelle fonctionnalité |
| fix | correction de bug |
| docs | documentation |
| test | ajout/modification de tests |
| refactor | refactoring sans changement fonctionnel |
| chore | dépendances, config |
| ci | pipeline CI/CD |
| style | formatage, style |

## Règles
- Un commit = une seule action (commit atomique)
- Pas de code cassé sur develop ou main
- Tous les merges passent par des Pull Requests
