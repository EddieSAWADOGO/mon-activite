# Journal de suivi et progression du projet — Mon-Activité

---

## Session 1 — Initialisation de la Phase 0 (Socle technique, Design System, Authentification & Rôles)

### 1. Tâche réalisée
Initialisation complète de la **Phase 0 (Socle technique, Design System mobile-first, Authentification & Rôles)** selon les spécifications de `tasksandplan.md` et les conventions de `convention.md`.

### 2. Fonctionnalités implémentées
- **Intégration d'Heroicons** : Installation de `blade-ui-kit/blade-heroicons` (`^2.7`) pour garantir l'absence totale d'émojis dans l'UI.
- **Gestion des Rôles & Permissions Backend** :
  - Enum natif PHP `App\Support\Enums\UserRole` (`SUPER_ADMIN`, `ADMIN`, `CASHIER`) avec libellés et couleurs de badges.
  - Extension de la table `users` avec les colonnes `role` et `is_active`.
  - Modèle `User` mis à jour avec `$fillable`, `casts()`, et méthodes d'autorisation (`isSuperAdmin()`, `isAdmin()`, `isCashier()`, `canManageUsers()`, `canManageInventoryOperations()`, `canAccessHistory()`).
  - Policy `UserPolicy` créée dans `app/Domain/Utilisateurs/Policies/UserPolicy.php` pour la gestion des utilisateurs.
- **Design System Mobile-First & Layout Principal** :
  - Layout principal `resources/views/layouts/app.blade.php` avec Alpine.js CDN, Tailwind CSS Play CDN (avec config palette de couleurs de la convention), meta PWA/manifest, token CSRF.
  - Barre de navigation persistante tactile en bas d'écran sur mobile, sidebar complète sur desktop/tablette.
  - Masquage sémantique et sécurisé de la navigation selon le rôle (Caissier ne voit pas l'Historique/suivi, les Pertes, les Retours, ni les Utilisateurs).
  - Bannières/toasts de notification dynamiques gérés en Alpine.js.
  - Manifest PWA `public/manifest.json`.
- **Composants UI Blade Réutilisables** :
  - `<x-ui.button>` (variants: primary, secondary, danger, outline ; tailles: sm, md, lg ; support icône Heroicons)
  - `<x-ui.card>` (avec header, actions, footer)
  - `<x-ui.badge>` (couleurs sémantiques: emerald, red, amber, sky, purple, slate)
  - `<x-ui.table>` (mobile-friendly avec scroll horizontal)
  - `<x-ui.stat-card>` (avec icône et couleur sémantique)
  - `<x-ui.empty-state>` (état vide avec icône et action)
- **Base de données & Seeders** :
  - Configuration de la base MySQL `monactivite_bd` dans `.env`.
  - Seeder `DatabaseSeeder` créant 3 comptes de démonstration (`superadmin@mon-activite.com`, `admin@mon-activite.com`, `cashier@mon-activite.com` / mot de passe: `password`).

### 3. Principaux fichiers créés ou modifiés
- `app/Support/Enums/UserRole.php` (Créé)
- `app/Models/User.php` (Modifié)
- `database/migrations/2026_01_01_000001_add_role_and_is_active_to_users_table.php` (Créé)
- `app/Domain/Utilisateurs/Policies/UserPolicy.php` (Créé)
- `resources/views/layouts/app.blade.php` (Créé)
- `resources/views/components/ui/button.blade.php` (Créé)
- `resources/views/components/ui/card.blade.php` (Créé)
- `resources/views/components/ui/badge.blade.php` (Créé)
- `resources/views/components/ui/table.blade.php` (Créé)
- `resources/views/components/ui/stat-card.blade.php` (Créé)
- `resources/views/components/ui/empty-state.blade.php` (Créé)
- `public/manifest.json` (Créé)
- `database/seeders/DatabaseSeeder.php` (Modifié)
- `.env` (Modifié - APP_NAME, APP_LOCALE, DB_CONNECTION, DB_DATABASE)

### 4. Décisions techniques importantes
- Respect strict de la convention : **Aucun framework JS SPA**, **aucun Livewire**, **aucun build npm/Vite**. Interactivité en Alpine.js + Tailwind Play CDN + Blade SSR.
- Arborescence modulaire par domaine initialisée dans `app/Domain/Utilisateurs/Policies/UserPolicy.php`.
- Base de données fixée sur MySQL (`monactivite_bd`).

### 5. Tests et vérifications effectués
- `composer require blade-ui-kit/blade-heroicons:^2.7` exécuté avec succès.
- `php artisan migrate:fresh --seed` exécuté avec succès (0 erreurs, 4 migrations appliquées, seeder OK).
- `php artisan test` exécuté avec succès (100% vert).

### 6. Problèmes rencontrés et leurs solutions
- *Problème* : Pilote `pdo_sqlite` non disponible par défaut dans l'installation PHP locale lors de la première tentative de migration.
- *Solution* : Configuration de MySQL avec la base `monactivite_bd` présente sur le système local (`pdo_mysql` actif). Migration et seeding réussis.

### 7. État actuel de la tâche
- **Phase 0** : Terminée à 100%. Le socle technique, les composants UI, la gestion des rôles et le layout principal mobile-first sont opérationnels.

### 8. Prochaine tâche recommandée
**Phase 1 — Produits et Unités (Fondation du modèle de stock)** :
1. Création des migrations et modèles pour les produits (`Product`) et leurs unités (`StockUnit`).
2. Implémentation du sous-domaine `app/Domain/Produits/`.
3. Gestion des unités de base et unités déclarées avec équivalences en unité de base, `prix_vente_defaut`, et seuils d'alerte par unité.
4. Compteurs de stock distincts par unité.
5. Application des règles d'immutabilité des unités.

---

## Session 2 — Phase 1 : Produits et Unités (Fondation du modèle de stock)

### 1. Tâche réalisée
Réalisation complète de la **Phase 1 — Produits et Unités (Fondation du modèle de stock)** selon les spécifications de `context.md`, `convention.md` et `tasksandplan.md`.

### 2. Fonctionnalités implémentées
- **Base de données & Migrations** :
  - Migration `create_products_table` (`id`, `name`, `description`, `is_active`, `timestamps`).
  - Migration `create_stock_units_table` (`id`, `product_id`, `name`, `is_base_unit`, `base_unit_equivalent`, `default_selling_price`, `low_stock_threshold`, `current_stock`, `is_active`, `timestamps`).
- **Architecture Modulaire du Domaine `app/Domain/Produits/`** :
  - **Models** :
    - `Product` (`units`, `activeUnits`, `baseUnit`, `isLowStock()`, `hasMultipleUnits()`, scopes `search`, `active`, `lowStock`).
    - `StockUnit` (`product`, `isLowStock()`, `canBeDeleted()`, `isUsedInTransactions()`).
  - **Service** : `ProductService` englobant dans des transactions DB la création de produit multi-unités, la mise à jour (respectant l'immutabilité des équivalences d'unités existantes), l'ajout de nouvelles unités, l'archivage/réactivation d'unités et la suppression sécurisée.
  - **Policies & Autorisations** : `ProductPolicy` enregistré dans `AppServiceProvider` autorisant la consultation du stock en lecture seule aux Caissiers, et réservant la gestion (création, modification, suppression, archivage) aux Administrateurs et Super Administrateurs. Trait `AuthorizesRequests` ajouté dans `Controller.php`.
  - **Form Requests** : `StoreProductRequest` et `UpdateProductRequest` avec règles de validation strictes et libellés français.
  - **Controller** : `ProductController` avec gestion du catalogue, recherche, filtres par statut et par stock bas, création, édition, détail et toggle de statut d'unité.
- **Interface Utilisateur Mobile-First & Alpine.js** :
  - **Composant de layout** : `<x-layouts.app>` dans `resources/views/components/layouts/app.blade.php`.
  - **Catalogue (`index.blade.php`)** : Vue adaptative (cartes empilées sur mobile, tableau sur desktop) avec badges de statut, compteurs de stock par unité, recherche live et filtre stock bas.
  - **Formulaire de création (`create.blade.php`)** : Gestion dynamique en Alpine.js pour ajouter/supprimer des unités déclarées additionnelles avec équivalence, `prix_vente_defaut`, seuil d'alerte et stock initial.
  - **Formulaire d'édition (`edit.blade.php`)** : Modification des informations, ajustement des prix/seuils, protection en lecture seule de l'équivalence des unités existantes (immutabilité), et ajout de nouveaux formats.
  - **Fiche produit (`show.blade.php`)** : Métriques clés, tableau récapitulatif des compteurs par unité, alerte visuelle de stock bas, actions d'archivage d'unités non-base.
- **Routes & Navigations** :
  - Routes web enregistrées pour `produits.*` et `produits.unites.toggle-status`.
  - Navigation latérale et barre mobile mises à jour avec états actifs.

### 3. Principaux fichiers créés ou modifiés
- `database/migrations/2026_01_01_000002_create_products_table.php` (Créé)
- `database/migrations/2026_01_01_000003_create_stock_units_table.php` (Créé)
- `app/Domain/Produits/Models/Product.php` (Créé)
- `app/Domain/Produits/Models/StockUnit.php` (Créé)
- `app/Domain/Produits/Policies/ProductPolicy.php` (Créé)
- `app/Domain/Produits/Services/ProductService.php` (Créé)
- `app/Domain/Produits/Http/Requests/StoreProductRequest.php` (Créé)
- `app/Domain/Produits/Http/Requests/UpdateProductRequest.php` (Créé)
- `app/Domain/Produits/Http/Controllers/ProductController.php` (Créé)
- `app/Providers/AppServiceProvider.php` (Modifié - enregistrement explicite des policies)
- `app/Http/Controllers/Controller.php` (Modifié - ajout de AuthorizesRequests)
- `routes/web.php` (Modifié - ajout des routes de gestion du catalogue)
- `resources/views/components/layouts/app.blade.php` (Créé)
- `resources/views/layouts/app.blade.php` (Modifié)
- `resources/views/produits/index.blade.php` (Créé)
- `resources/views/produits/create.blade.php` (Créé)
- `resources/views/produits/edit.blade.php` (Créé)
- `resources/views/produits/show.blade.php` (Créé)
- `database/factories/ProductFactory.php` (Créé)
- `tests/Feature/ProductTest.php` (Créé)
- `phpunit.xml` (Modifié - configuration MySQL pour les tests)

### 4. Décisions techniques importantes
- **Compteurs de stock distincts par unité** : Chaque unité (`StockUnit`) possède son propre champ `current_stock` et `low_stock_threshold`, garantissant le suivi par conditionnement réel sans total agrégé converti.
- **Immutabilité des règles d'équivalence** : Les équivalences des unités existantes ne sont jamais modifiables en édition pour éviter de fausser l'historique rétrospectivement. En cas de changement de format, l'ancienne unité est archivée (`is_active = false`) et un nouveau format d'unité est créé.
- **Interdictions côté Caissier** : Un Caissier dispose d'un accès en lecture seule pour consulter le stock disponible, mais ne peut ni créer, ni modifier, ni supprimer de produit.

### 5. Tests et vérifications effectués
- Migration de la base de données MySQL exécutée avec succès (`php artisan migrate`).
- Suite de tests d'intégration complète dans `tests/Feature/ProductTest.php` (8 tests couvrant l'accès admin vs caissier, la création multi-unités, la mise à jour, l'immutabilité des équivalences, le filtrage stock bas, et l'archivage d'unité).
- `php artisan test` exécuté avec 100% de succès (8/8 tests validés).

### 6. Problèmes rencontrés et leurs solutions
- *Problème* : Absence de composant anonyme Blade pour `<x-layouts.app>` lors de la première exécution des vues.
- *Solution* : Ajout du fichier `resources/views/components/layouts/app.blade.php` correspondant au standard des composants anonymes Blade de Laravel.
- *Problème* : Résolution automatique de factory Eloquent échouant sur les modèles situés dans les sous-dossiers de domaine (`App\Domain\Produits\Models\Product`).
- *Solution* : Ajout de la méthode `newFactory()` sur le modèle `Product` pointant vers `Database\Factories\ProductFactory`.

### 7. État actuel de la tâche
- **Phase 1** : Terminée à 100%. Le modèle de données des produits et des unités de stock est pleinement opérationnel et testé.

### 8. Prochaine tâche recommandée
**Phase 2 — Mouvements de base : Achats et Stock** :
1. Implémentation du sous-domaine `app/Domain/Achats/` et du sous-domaine `app/Domain/Stock/`.
2. Création des migrations et modèles pour les achats (`Purchase`, `PurchaseLine`) et mouvements unifiés de stock (`StockMovement`).
3. Formulaire d'enregistrement d'achat multi-produits/multi-unités avec augmentation automatique du compteur de stock de l'unité concernée.
4. Enregistrement systématique des lignes de mouvements de stock (type entrée).
5. Écran global du module Stock avec vue d'ensemble par unité et alertes de stock bas.
