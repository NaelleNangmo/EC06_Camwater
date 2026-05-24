#  Documentation Swagger, Rôles, CORS et Rate Limiting


## 📖 1. Documentation Swagger

### Accès à la documentation

La documentation interactive Swagger est accessible à l'URL :

```
http://localhost:8000/api/documentation
```

### Fonctionnalités

- ✅ Documentation interactive de tous les endpoints
- ✅ Possibilité de tester les requêtes directement depuis l'interface
- ✅ Authentification Bearer Token intégrée
- ✅ Exemples de requêtes et réponses
- ✅ Schémas de validation

### Utilisation

1. **Ouvrir la documentation**
   ```
   http://localhost:8000/api/documentation
   ```

2. **S'authentifier**
   - Cliquer sur le bouton "Authorize" en haut à droite
   - Entrer le token : `Bearer YOUR_TOKEN`
   - Cliquer sur "Authorize"

3. **Tester un endpoint**
   - Sélectionner un endpoint
   - Cliquer sur "Try it out"
   - Remplir les paramètres
   - Cliquer sur "Execute"

### Régénérer la documentation

```bash
php artisan l5-swagger:generate
```

### Configuration

Fichier : `config/l5-swagger.php`

Variables d'environnement :
```env
L5_SWAGGER_GENERATE_ALWAYS=false
L5_SWAGGER_CONST_HOST=http://localhost:8000
```

---

##  2. Système de rôles et permissions

### Rôles disponibles

| Rôle | Description | Permissions |
|------|-------------|-------------|
| **admin** | Administrateur système | Toutes les permissions |
| **operateur** | Opérateur CAMWATER | Création, modification, consultation |
| **consultant** | Consultant externe | Consultation uniquement |

### Permissions disponibles

#### Abonnés
- `view_abonnes` - Consulter les abonnés
- `create_abonnes` - Créer des abonnés
- `edit_abonnes` - Modifier des abonnés
- `delete_abonnes` - Supprimer des abonnés

#### Factures
- `view_factures` - Consulter les factures
- `create_factures` - Créer des factures
- `edit_factures` - Modifier des factures
- `delete_factures` - Supprimer des factures

#### Réclamations
- `view_reclamations` - Consulter les réclamations
- `create_reclamations` - Créer des réclamations
- `edit_reclamations` - Modifier des réclamations
- `delete_reclamations` - Supprimer des réclamations

#### Administration
- `manage_users` - Gérer les utilisateurs
- `view_logs` - Consulter les logs

