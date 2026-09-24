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

---

## Session 3 — Phase 2 : Mouvements de base (Achats et Stock)

### 1. Tâche réalisée
Réalisation complète de la **Phase 2 — Mouvements de base : Achats et Stock** selon les spécifications de `context.md`, `convention.md` et `tasksandplan.md`.

### 2. Fonctionnalités implémentées
- **Enum `MovementType`** :
  - Cases : `PURCHASE`, `SALE`, `BREAKAGE_OUT`, `BREAKAGE_IN`, `RETURN`, `LOSS`, `REPACKAGING_OUT`, `REPACKAGING_IN`.
  - Méthodes `label()` et `badgeColor()` pour le rendu sémantique des mouvements.
- **Module Fournisseurs (`app/Domain/Fournisseurs/`)** :
  - Migration `create_suppliers_table` (`name`, `phone`, `whatsapp`, `address`, `type`, `contact_person`, `email`, `notes`, `is_active`).
  - Modèle `Supplier` avec méthode `newFactory()` et relation `hasMany(Purchase::class)`.
  - Contrôleur `SupplierController` et vues Blade (`index`, `create`, `edit`, `show`) pour le suivi des fournisseurs et de leurs encours.
  - Seeder de démonstration avec comptes fournisseurs réels.
- **Module Achats (`app/Domain/Achats/`)** :
  - Migration `create_purchases_table` (`purchase_number`, `supplier_id`, `total_amount`, `paid_amount`, `remaining_amount`, `purchase_date`, `created_by_user_id`, `notes`).
  - Migration `create_purchase_lines_table` (`purchase_id`, `product_id`, `stock_unit_id`, `quantity`, `unit_price`, `subtotal`).
  - Modèles `Purchase` et `PurchaseLine`.
  - Service `PurchaseService` :
    - Exécution atomique sous transaction DB (`DB::transaction`).
    - Génération du numéro unique d'achat (`ACH-YYYYMMDD-XXXX`).
    - Recalcul serveur des sous-totaux et du reste à payer.
    - Appel du service de mouvement de stock pour incrémenter le compteur de stock et consigner le mouvement.
  - Policy `PurchasePolicy` enregistrée dans `AppServiceProvider` : restriction de création/consultation d'achats aux seuls Administrateurs et Super Administrateurs (interdit au Caissier).
  - Form Request `StorePurchaseRequest` avec règles de validation et libellés français.
  - Contrôleur `PurchaseController` et vues Blade (`index`, `create`, `show`). Formulaire dynamique Alpine.js permettant l'ajout/suppression de lignes, sélection d'unités associées, calculs en direct.
- **Module Stock & Mouvements Unifiés (`app/Domain/Stock/`)** :
  - Migration `create_stock_movements_table` (`product_id`, `stock_unit_id`, `type`, `quantity`, `direction`, `reference_type`, `reference_id`, `created_by_user_id`, `movement_date`, `notes`).
  - Modèle `StockMovement`.
  - Service `StockMovementService` : mise à jour sécurisée par `lockForUpdate()` du champ `current_stock` de l'unité et journalisation de l'événement.
  - Contrôleur `StockController` et vues Blade (`index.blade.php` pour la consultation par unité avec alerte visuelle de stock bas, `movements.blade.php` pour l'historique complet filtrable par produit, type et période). Accessible au Caissier en consultation.

### 3. Principaux fichiers créés ou modifiés
- `app/Support/Enums/MovementType.php` (Créé)
- `database/migrations/2026_01_01_000004_create_suppliers_table.php` (Créé)
- `database/migrations/2026_01_01_000005_create_purchases_table.php` (Créé)
- `database/migrations/2026_01_01_000006_create_purchase_lines_table.php` (Créé)
- `database/migrations/2026_01_01_000007_create_stock_movements_table.php` (Créé)
- `app/Domain/Fournisseurs/Models/Supplier.php` (Créé)
- `app/Domain/Fournisseurs/Http/Controllers/SupplierController.php` (Créé)
- `app/Domain/Achats/Models/Purchase.php` (Créé)
- `app/Domain/Achats/Models/PurchaseLine.php` (Créé)
- `app/Domain/Achats/Services/PurchaseService.php` (Créé)
- `app/Domain/Achats/Policies/PurchasePolicy.php` (Créé)
- `app/Domain/Achats/Http/Requests/StorePurchaseRequest.php` (Créé)
- `app/Domain/Achats/Http/Controllers/PurchaseController.php` (Créé)
- `app/Domain/Stock/Models/StockMovement.php` (Créé)
- `app/Domain/Stock/Services/StockMovementService.php` (Créé)
- `app/Domain/Stock/Http/Controllers/StockController.php` (Créé)
- `app/Providers/AppServiceProvider.php` (Modifié - enregistrement de `PurchasePolicy`)
- `routes/web.php` (Modifié - ajout des routes d'achats, stock et fournisseurs)
- `resources/views/layouts/app.blade.php` (Modifié - mise à jour de la navigation)
- `resources/views/achats/index.blade.php` (Créé)
- `resources/views/achats/create.blade.php` (Créé)
- `resources/views/achats/show.blade.php` (Créé)
- `resources/views/stock/index.blade.php` (Créé)
- `resources/views/stock/movements.blade.php` (Créé)
- `resources/views/fournisseurs/index.blade.php` (Créé)
- `resources/views/fournisseurs/create.blade.php` (Créé)
- `resources/views/fournisseurs/edit.blade.php` (Créé)
- `resources/views/fournisseurs/show.blade.php` (Créé)
- `database/factories/SupplierFactory.php` (Créé)
- `database/seeders/DatabaseSeeder.php` (Modifié)
- `tests/Feature/PurchaseTest.php` (Créé)
- `tests/Feature/StockTest.php` (Créé)

### 4. Décisions techniques importantes
- **Transactions DB & Atomicité** : Tout enregistrement d'achat verrouille les compteurs des unités concernées, met à jour le stock et consigne les mouvements dans une seule transaction `DB::transaction`.
- **Accès restreint aux Achats** : Un Caissier ne peut pas accéder aux routes `/achats` (bloqué par `PurchasePolicy`), mais peut consulter l'état du stock `/stock`.

### 5. Tests et vérifications effectués
- `php artisan migrate:fresh --seed` exécuté avec succès.
- Suite de tests d'intégration complète exécutée avec 100% de succès (`php artisan test` : 13 tests validés, 51 assertions).

### 6. Problèmes rencontrés et leurs solutions
- *Problème* : Fautes de frappe dans le séparateur de namespace (`/` au lieu de `\`) dans `Supplier.php` et `StorePurchaseRequest.php`.
- *Solution* : Normalisation des déclarations de namespace en syntaxe PHP valide.
- *Problème* : Factory pour le modèle de domaine `Supplier` introuvable par Eloquent.
- *Solution* : Déclaration de la méthode statique `newFactory()` sur `Supplier` pointant vers `Database\Factories\SupplierFactory`.

### 7. État actuel de la tâche
- **Phase 2** : Terminée à 100%. L'approvisionnement des stocks par achat, le suivi des fournisseurs, les mouvements unifiés et la vue globale du stock sont pleinement opérationnels et testés.

### 8. Prochaine tâche recommandée
**Phase 3 — Ventes, cassures et facturation** :
1. Implémentation du sous-domaine `app/Domain/Ventes/` et `app/Domain/Facturation/`.
2. Formulaire de vente multi-lignes.
3. Détection d'écart vs `prix_vente_defaut` de l'unité avec saisie obligatoire d'un motif de remise/écart.
4. Mécanisme de cassure (sélection manuelle de l'unité source à ouvrir, décrément de l'unité source, vente en unité de base, réintégration du reliquat non vendu dans l'unité de base).
5. Génération automatique de la facture immuable et du reçu commercial.

---

## Session 4 — Phase 3 : Ventes, cassures, facturation immuable et gestion des clients

### 1. Tâche réalisée
Réalisation complète de la **Phase 3 — Ventes, cassures, facturation immuable et gestion des clients** selon les spécifications de `context.md`, `convention.md` et `tasksandplan.md`.

### 2. Fonctionnalités implémentées
- **Module Clients (`app/Domain/Clients/`)** :
  - Correction de la faute de frappe du namespace d'importation dans `Customer.php`.
  - `CustomerFactory` pour les tests et seeders (`particulier` et `entreprise`).
  - Policy `CustomerPolicy` : Autorisation de création et consultation pour le rôle Caissier, modification/suppression réservée aux Administrateurs et Super Administrateurs.
  - Form Requests `StoreCustomerRequest` et `UpdateCustomerRequest` avec règles strictes.
  - Contrôleur `CustomerController` avec recherche multi-critères et filtre par type.
  - Vues Blade mobile-first (`index`, `create`, `edit`, `show`) avec bascule dynamique Alpine.js selon le type de client (IFU, RCCM, personne de contact pour les entreprises).
- **Module Ventes & Mécanisme de Cassure (`app/Domain/Ventes/`)** :
  - Model `Sale` et `SaleLine`.
  - Policy `SalePolicy` : Création et consultation autorisées aux Caissiers.
  - Form Request `StoreSaleRequest` :
    - Règle stricte d'écart de prix : Saisie obligatoire d'un `discount_reason` si `unit_price != default_selling_price`.
    - Validation du stock et de la cohérence de l'unité source lors d'une cassure.
  - Service `SaleService` :
    - Transaction atomique `DB::transaction`.
    - Génération du numéro de vente unique (`VNT-YYYYMMDD-XXXX`).
    - Traitement du **mécanisme de cassure (breakage)** :
      - Ouverture de l'unité source (ex. carton) : décrément de 1 sur le compteur source et enregistrement du mouvement `BREAKAGE_OUT`.
      - Réintégration de l'équivalence complète sur l'unité de base et enregistrement du mouvement `BREAKAGE_IN`.
      - Déduction de la quantité vendue sur l'unité de base et enregistrement du mouvement `SALE`.
    - Vente directe sans cassure : décrément direct et enregistrement du mouvement `SALE`.
    - Génération automatique de la facture immuable associée.
  - Contrôleur `SaleController` et vues Blade (`index`, `create`, `show`). Formulaire dynamique Alpine.js gérant le calcul automatique des sous-totaux, la détection d'écart de prix en direct et la sélection du carton source en cas de stock vrac insuffisant.
- **Module Facturation Immuable (`app/Domain/Facturation/`)** :
  - Models `Invoice` et `InvoiceLine` avec événements de modèle (`booted()`) empêchant toute modification des colonnes protégées ou suppression.
  - Service/Méthodes `InvoiceController` :
    - Reconstitution en direct du document HTML depuis la base de données (`show`).
    - Téléchargement PDF à la demande avec DomPDF (`pdf`).
    - Génération de lien de partage WhatsApp avec message prérempli (`whatsapp`).
  - Policy `InvoicePolicy` interdisant modification et suppression.
  - Vues Blade (`index`, `show`, `pdf`).
- **Mise à jour des Routes & de la Navigation** :
  - Routes enregistrées dans `routes/web.php` pour `clients.*`, `ventes.*`, `factures.*`, `factures.pdf`, `factures.whatsapp`.
  - Enregistrement des Policies dans `AppServiceProvider`.
  - Liens de navigation mis à jour sur sidebar desktop, menu slide-over mobile et barre tactile mobile.

### 3. Principaux fichiers créés ou modifiés
- `app/Domain/Clients/Models/Customer.php` (Modifié - correction namespace)
- `app/Domain/Facturation/Models/InvoiceLine.php` (Modifié - correction namespace)
- `database/factories/CustomerFactory.php` (Créé)
- `app/Domain/Clients/Policies/CustomerPolicy.php` (Créé)
- `app/Domain/Clients/Http/Requests/StoreCustomerRequest.php` (Créé)
- `app/Domain/Clients/Http/Requests/UpdateCustomerRequest.php` (Créé)
- `app/Domain/Clients/Http/Controllers/CustomerController.php` (Créé)
- `resources/views/clients/index.blade.php` (Créé)
- `resources/views/clients/create.blade.php` (Créé)
- `resources/views/clients/edit.blade.php` (Créé)
- `resources/views/clients/show.blade.php` (Créé)
- `app/Domain/Ventes/Policies/SalePolicy.php` (Créé)
- `app/Domain/Ventes/Http/Requests/StoreSaleRequest.php` (Créé)
- `app/Domain/Ventes/Services/SaleService.php` (Créé)
- `app/Domain/Ventes/Http/Controllers/SaleController.php` (Créé)
- `resources/views/ventes/index.blade.php` (Créé)
- `resources/views/ventes/create.blade.php` (Créé)
- `resources/views/ventes/show.blade.php` (Créé)
- `app/Domain/Facturation/Policies/InvoicePolicy.php` (Créé)
- `app/Domain/Facturation/Http/Controllers/InvoiceController.php` (Créé)
- `resources/views/factures/index.blade.php` (Créé)
- `resources/views/factures/show.blade.php` (Créé)
- `resources/views/factures/pdf.blade.php` (Créé)
- `routes/web.php` (Modifié - routes clients, ventes, factures)
- `app/Providers/AppServiceProvider.php` (Modifié - enregistrement de `CustomerPolicy`)
- `resources/views/layouts/app.blade.php` (Modifié - navigation web & mobile)
- `tests/Feature/CustomerTest.php` (Créé)
- `tests/Feature/SaleTest.php` (Créé)
- `tests/Feature/InvoiceTest.php` (Créé)

### 4. Décisions techniques importantes
- **Cassure de stock atomique** : Lors d'une vente en unité de base nécessitant d'ouvrir un carton, l'unité source est décrémentée de 1 (`BREAKAGE_OUT`), la totalité de l'équivalence est réintégrée sur l'unité de base (`BREAKAGE_IN`), puis la quantité vendue est prélevée (`SALE`). Le reliquat non vendu reste disponible en unité de base dans le stock.
- **Immutabilité des Factures** : Toute tentative de mise à jour des données de fond d'une facture ou de suppression déclenche une `DomainException` au niveau d'Eloquent. Les factures sont reconstruites à la demande à partir de la base sans stockage de fichier PDF permanent sur le serveur.
- **Motif d'écart obligatoire** : La Form Request vérifie côté serveur que si le prix saisi diffère de `default_selling_price`, un motif est obligatoirement fourni.

### 5. Tests et vérifications effectués
- Tests fonctionnels complets créés dans `tests/Feature/CustomerTest.php`, `tests/Feature/SaleTest.php` et `tests/Feature/InvoiceTest.php` couvrant :
  - Création de clients et gestion des rôles (Caissier vs Admin).
  - Validation obligatoire du motif d'écart de prix.
  - Vente directe avec décrémentation de stock et émission de facture.
  - Vente avec cassure (décrément du carton, réintégration de l'équivalence sur l'unité de base, mouvement de vente).
  - Immutabilité de la facture (exception levée lors de modification/suppression).
  - Génération de PDF et lien de partage WhatsApp.

### 6. Problèmes rencontrés et leurs solutions
- *Problème* : Fautes de frappe dans les namespaces de `Customer.php` et `InvoiceLine.php` (`/` au lieu de `\`).
- *Solution* : Correction des séparateurs de namespace.

### 7. État actuel de la tâche
- **Phase 3** : Terminée à 100%. Les sous-modules Ventes, Cassures, Facturation immuable et Clients sont totalement implémentés et testés.

### 8. Prochaine tâche recommandée
**Phase 4 — Paiements, Fournisseurs et Clients (Gestion des règlements et créances/dettes)** :
1. Implémentation du sous-domaine `app/Domain/Paiements/`.
2. Création du modèle `Payment` (enregistrement des règlements partiels ou totaux des factures).
3. Recalcul dynamique du montant payé / reste à payer et mise à jour du statut de la facture.
4. Finalisation des vues de suivi des dettes fournisseurs et des créances clients.

---

## Session 5 — Phase 4 : Paiements, Fournisseurs et Clients (Gestion des règlements et créances/dettes)

### 1. Tâche réalisée
Réalisation complète de la **Phase 4 — Paiements, Fournisseurs et Clients (Gestion des règlements et créances/dettes)** selon les spécifications de `context.md`, `convention.md` et `tasksandplan.md`.

### 2. Fonctionnalités implémentées
- **Sous-domaine `app/Domain/Paiements/`** :
  - **Migration** `2026_01_01_000013_create_payments_table.php` (`id`, `invoice_id`, `amount`, `payment_date`, `payment_method`, `reference`, `notes`, `created_by_user_id`, `timestamps`).
  - **Model `Payment`** (`invoice`, `createdBy`, casts sur `amount` et `payment_date`).
  - **Service `PaymentService`** :
    - Transaction atomique DB (`DB::transaction`) avec verrouillage `lockForUpdate()`.
    - Validation du montant (interdiction des montants <= 0 ou dépassant le reste à payer de la facture).
    - Calcul dynamique des agrégats (`paid_amount`, `remaining_amount`) et du statut de la facture (`UNPAID`, `PARTIALLY_PAID`, `PAID`).
    - Mise à jour autorisée des colonnes financières de `Invoice` et de la `Sale` correspondante.
  - **Policy `PaymentPolicy`** :
    - Autorisation d'accès et d'enregistrement des règlements pour les rôles Caissier, Administrateur et Super Administrateur (conforme à la section 8 du cahier des charges).
    - Immutabilité des paiements enregistrés (modification et suppression désactivées).
  - **Form Request `StorePaymentRequest`** avec règles de validation et messages français.
  - **Controller `PaymentController`** (`index`, `create`, `store`).
  - **Vues Blade mobile-first** :
    - Formulaire d'enregistrement d'un règlement (`paiements/create.blade.php`).
    - Historique des règlements avec recherche et pagination (`paiements/index.blade.php`).
    - Section d'historique des règlements intégrée sur la fiche détail d'une facture (`factures/show.blade.php`) et bouton d'action direct "Enregistrer un règlement".
- **Vues Suivi Dettes et Créances** :
  - Vues `clients/show.blade.php` et `fournisseurs/show.blade.php` pour la consultation consolidée des créances clients et dettes fournisseurs.
- **Routes & Navigation** :
  - Routes web enregistrées pour `paiements.index`, `paiements.create` (`factures/{invoice}/regler`), `paiements.store`.
  - Intégration du lien "Règlements" dans le menu de navigation de `layouts/app.blade.php`.
  - Enregistrement de `PaymentPolicy` dans `AppServiceProvider`.

### 3. Principaux fichiers créés ou modifiés
- `database/migrations/2026_01_01_000013_create_payments_table.php` (Créé)
- `app/Domain/Paiements/Models/Payment.php` (Créé)
- `app/Domain/Paiements/Services/PaymentService.php` (Créé)
- `app/Domain/Paiements/Policies/PaymentPolicy.php` (Créé)
- `app/Domain/Paiements/Http/Requests/StorePaymentRequest.php` (Créé)
- `app/Domain/Paiements/Http/Controllers/PaymentController.php` (Créé)
- `app/Domain/Facturation/Models/Invoice.php` (Modifié - relation `payments()`)
- `resources/views/paiements/create.blade.php` (Créé)
- `resources/views/paiements/index.blade.php` (Créé)
- `resources/views/factures/show.blade.php` (Modifié - bouton règlement & table historique)
- `app/Providers/AppServiceProvider.php` (Modifié - enregistrement `PaymentPolicy`)
- `routes/web.php` (Modifié - routes paiements)
- `resources/views/layouts/app.blade.php` (Modifié - navigation sidebar)
- `tests/Feature/PaymentTest.php` (Créé)

### 4. Décisions techniques importantes
- **Respect de l'immutabilité des factures** : Les paiements modifient uniquement le statut et les compteurs financiers (`paid_amount`, `remaining_amount`, `payment_method`) via l'événement `booted()` d'Eloquent qui autorise spécifiquement ces colonnes.
- **Événement immuable du règlement** : Un paiement enregistré dans la table `payments` constitue un événement historique inaltérable (Policy refuse `update` et `delete`).
- **Permissions du Caissier** : Le rôle Caissier a la permission explicite d'enregistrer des règlements pour les clients, tout en restant restreint sur l'accès aux autres modules d'administration.

### 5. Tests et vérifications effectués
- Suite de tests fonctionnels et d'intégration créée dans `tests/Feature/PaymentTest.php` couvrant :
  - Accès du Caissier au formulaire de règlement.
  - Règlement partiel avec mise à jour du statut `PARTIALLY_PAID` et des restes à payer sur `Invoice` et `Sale`.
  - Règlement total avec passage au statut `PAID`.
  - Rejet d'un paiement dont le montant dépasse le reste à payer.
  - Cumul correct de plusieurs règlements successifs sur une même facture.

### 6. Problèmes rencontrés et leurs solutions
- Aucun problème bloquant rencontré.

### 7. État actuel de la tâche
- **Phase 4** : Terminée à 100%. Le sous-module Paiements, l'enregistrement des règlements, la mise à jour des factures/ventes et le suivi des créances/dettes sont totalement opérationnels.

### 8. Prochaine tâche recommandée
**Phase 5 — Retours clients, pertes et reconditionnement** :
1. Implémentation du sous-domaine `app/Domain/Retours/` (formulaire de retour, validation manuelle obligatoire avant réintégration du stock, restriction Administrateur/Agent).
2. Implémentation du sous-domaine `app/Domain/Pertes/` (formulaire de déclaration de perte, décrémentation directe du stock par unité, restriction Administrateur/Agent).
3. Implémentation du sous-domaine `app/Domain/Reconditionnement/` (formulaire de regroupement d'unités de base en unité supérieure, décrémentation de l'unité source et incrémentation de l'unité cible).
4. Intégration systématique de ces 3 opérations dans le journal unifié des mouvements de stock (`StockMovement`).

---

## Session 6 — Phase 5 : Retours clients, pertes et reconditionnement

### 1. Tâche réalisée
Réalisation complète de la **Phase 5 — Retours clients, pertes et reconditionnement** selon les spécifications de `context.md`, `convention.md` et `tasksandplan.md`.

### 2. Fonctionnalités implémentées
- **Sous-domaine Retours Clients (`app/Domain/Retours/`)** :
  - **Migration** `2026_01_01_000014_create_customer_returns_table.php` (`return_number`, `customer_id`, `invoice_id`, `product_id`, `stock_unit_id`, `quantity`, `reason`, `status`, `return_date`, `created_by_user_id`, `validated_by_user_id`, `validated_at`, `notes`).
  - **Model `CustomerReturn`** (`isPending()`, `isRestocked()`, `isDiscarded()`).
  - **Service `CustomerReturnService`** :
    - `recordReturn()` : Enregistrement d'un retour client à l'état `pending`. **Le stock n'est PAS réintégré à la création** (conforme à la règle métier 2.76 du cahier des charges).
    - `validateAndRestock()` : Validation manuelle réservée à l'Admin, passage au statut `restocked`, incrémentation du compteur de l'unité reçue et consignation d'un mouvement `RETURN` (entrée).
    - `validateAndDiscard()` : Validation manuelle avec passage au statut `discarded`, création automatique d'une `Loss` et consignation d'un mouvement `LOSS` (sortie) sans réintégration au stock.
  - **Policy `CustomerReturnPolicy`** : Accès et validation réservés aux Administrateurs/Super-Admins (`canManageInventoryOperations()`). Accès interdit aux Caissiers.
  - **Controller `CustomerReturnController`** & **Vues Blade** (`retours/index`, `create`, `show` avec boutons de validation manuelle).
- **Sous-domaine Pertes (`app/Domain/Pertes/`)** :
  - **Migration** `2026_01_01_000015_create_losses_table.php` (`loss_number`, `product_id`, `stock_unit_id`, `quantity`, `reason`, `loss_date`, `notes`, `customer_return_id`, `created_by_user_id`).
  - **Model `Loss`**.
  - **Service `LossService`** : Décrémentation directe du compteur de l'unité concernée sous transaction DB et consignation d'un mouvement `LOSS` (sortie).
  - **Policy `LossPolicy`** : Accès réservé aux Administrateurs/Super-Admins.
  - **Controller `LossController`** & **Vues Blade** (`pertes/index`, `create`, `show`).
- **Sous-domaine Reconditionnement (`app/Domain/Reconditionnement/`)** :
  - **Migration** `2026_01_01_000016_create_repackagings_table.php` (`repackaging_number`, `product_id`, `source_stock_unit_id`, `source_quantity`, `target_stock_unit_id`, `target_quantity`, `repackaging_date`, `notes`, `created_by_user_id`).
  - **Model `Repackaging`**.
  - **Service `RepackagingService`** :
    - Contrôle de cohérence d'équivalence entre l'unité source et l'unité cible (ex: 12 bidons d'équivalence 1 = 1 carton d'équivalence 12).
    - Décrémentation de l'unité source avec mouvement `REPACKAGING_OUT` (sortie).
    - Incrémentation de l'unité cible avec mouvement `REPACKAGING_IN` (entrée).
  - **Policy `RepackagingPolicy`** : Accès réservé aux Administrateurs/Super-Admins.
  - **Controller `RepackagingController`** & **Vues Blade** (`reconditionnement/index`, `create`, `show`). Formulaire Alpine.js calculant en temps réel les volumes de base et vérifiant l'égalité des équivalences.
- **Routes & Navigation** :
  - Routes web enregistrées pour `retours.*`, `retours.restock`, `retours.discard`, `pertes.*`, `reconditionnement.*`.
  - Policies enregistrées dans `AppServiceProvider`.
  - Liens actifs ajoutés dans la sidebar sous la condition `@if(auth()->user()->canManageInventoryOperations())`.

### 3. Principaux fichiers créés ou modifiés
- `database/migrations/2026_01_01_000014_create_customer_returns_table.php` (Créé)
- `database/migrations/2026_01_01_000015_create_losses_table.php` (Créé)
- `database/migrations/2026_01_01_000016_create_repackagings_table.php` (Créé)
- `app/Domain/Retours/Models/CustomerReturn.php` (Créé)
- `app/Domain/Retours/Services/CustomerReturnService.php` (Créé)
- `app/Domain/Retours/Policies/CustomerReturnPolicy.php` (Créé)
- `app/Domain/Retours/Http/Requests/StoreCustomerReturnRequest.php` (Créé)
- `app/Domain/Retours/Http/Controllers/CustomerReturnController.php` (Créé)
- `resources/views/retours/index.blade.php`, `create.blade.php`, `show.blade.php` (Créés)
- `app/Domain/Pertes/Models/Loss.php` (Créé)
- `app/Domain/Pertes/Services/LossService.php` (Créé)
- `app/Domain/Pertes/Policies/LossPolicy.php` (Créé)
- `app/Domain/Pertes/Http/Requests/StoreLossRequest.php` (Créé)
- `app/Domain/Pertes/Http/Controllers/LossController.php` (Créé)
- `resources/views/pertes/index.blade.php`, `create.blade.php`, `show.blade.php` (Créés)
- `app/Domain/Reconditionnement/Models/Repackaging.php` (Créé)
- `app/Domain/Reconditionnement/Services/RepackagingService.php` (Créé)
- `app/Domain/Reconditionnement/Policies/RepackagingPolicy.php` (Créé)
- `app/Domain/Reconditionnement/Http/Requests/StoreRepackagingRequest.php` (Créé)
- `app/Domain/Reconditionnement/Http/Controllers/RepackagingController.php` (Créé)
- `resources/views/reconditionnement/index.blade.php`, `create.blade.php`, `show.blade.php` (Créés)
- `app/Providers/AppServiceProvider.php` (Modifié - enregistrement policies)
- `routes/web.php` (Modifié - routes retours, pertes, reconditionnement)
- `resources/views/layouts/app.blade.php` (Modifié - liens navigation conditionnels)
- `tests/Feature/CustomerReturnTest.php`, `LossTest.php`, `RepackagingTest.php` (Créés)

### 4. Décisions techniques importantes
- **Non-réintégration automatique des retours** : Conforme au cahier des charges, créer un retour n'affecte pas le stock tant qu'une validation explicite n'a pas été effectuée (`restock` ou `discard`).
- **Verrouillage et vérification d'équivalence** : Le service de reconditionnement contrôle l'égalité exacte du nombre d'unités de base entre les quantités prélevées et obtenues.
- **Interdictions Caissier** : Les Policies et le masquage dans la navigation bloquent l'accès à ces 3 modules pour le rôle Caissier.

### 5. Tests et vérifications effectués
- `tests/Feature/CustomerReturnTest.php` : Vérification du statut initial `pending`, de la non-altération du stock à l'enregistrement, de la réintégration de stock lors de la validation `restock`, de la création de perte lors de `discard`, et du blocage du Caissier.
- `tests/Feature/LossTest.php` : Décrémentation du stock lors de la déclaration de perte et enregistrement de `StockMovement` (sortie).
- `tests/Feature/RepackagingTest.php` : Exécution d'un reconditionnement avec mise à jour exacte des compteurs source et cible, et rejet si les équivalences ne correspondent pas.

### 6. Problèmes rencontrés et leurs solutions
- Aucun problème rencontré.

### 7. État actuel de la tâche
- **Phase 5** : Terminée à 100%. Les sous-modules Retours clients, Pertes et Reconditionnement sont complètement implémentés, testés et reliés au journal des mouvements de stock.

### 8. Prochaine tâche recommandée
**Phase 6 — Snapshots et historique/suivi avancé** :
1. Implémentation du sous-domaine `app/Domain/Historique/`.
2. Job/Command Artisan planifié pour les snapshots mensuels de stock (`StockSnapshot`).
3. Algorithme de reconstitution du stock à une date passée (repartir du snapshot mensuel le plus proche et rejouer les mouvements).
4. Vues d'historique et suivi : achats/ventes par produit et par période, classement des produits les plus vendus, vue consolidée créances/dettes.

---

## Session 7 — Phase 6 & 7 : Snapshots mensuels, Historique & Suivi avancé et Finalisation

### 1. Tâche réalisée
Réalisation complète de la **Phase 6 & Phase 7 — Snapshots mensuels, Historique & Suivi avancé et Finalisation** selon les spécifications de `context.md`, `convention.md` et `tasksandplan.md`.

### 2. Fonctionnalités implémentées
- **Sous-domaine Stock Snapshots (`app/Domain/Stock/`)** :
  - **Migration** `2026_01_01_000017_create_stock_snapshots_table.php` (`year`, `month`, `product_id`, `stock_unit_id`, `quantity`, `snapshot_date`, contrainte d'unicité mensuelle sur `year`, `month`, `stock_unit_id`).
  - **Model `StockSnapshot`**.
  - **Service `StockSnapshotService`** :
    - `generateSnapshotForMonth()` : Enregistre l'état exact des compteurs de stock de toutes les unités à la fin d'un mois sous transaction DB.
    - `computeStockAtDate()` : Reconstitue l'état du stock par unité d'un produit à une date passée en recherchant le snapshot le plus proche puis en rejouant les mouvements `StockMovement` postérieurs jusqu'à la date ciblée.
  - **Commande Artisan `GenerateMonthlyStockSnapshot`** (`php artisan stock:snapshot`).
- **Sous-domaine Historique & Suivi (`app/Domain/Historique/`)** :
  - **Policy `HistoryPolicy`** : Accès réservé aux Administrateurs/Super-Admins (`canAccessHistory()`). Accès interdit aux Caissiers.
  - **Controller `HistoryController`** :
    - `index()` : Hub de navigation du module Historique & Suivi.
    - `purchases()` : Consultation des achats par produit et par période (Aujourd'hui, Semaine, Mois, Dates sur mesure) avec total cumulé.
    - `sales()` : Consultation des ventes par produit et par période avec motifs de remise et total cumulé.
    - `stockAtDate()` : Reconstitution de l'état du stock par unité à une date passée via `StockSnapshotService`.
    - `topProducts()` : Classement des produits les plus vendus par chiffre d'affaires ou quantité.
    - `financialOverview()` : Situation globale et consolidée des créances clients et dettes fournisseurs.
  - **Vues Blade mobile-first** : `historique/index.blade.php`, `purchases.blade.php`, `sales.blade.php`, `stock-at-date.blade.php`, `top-products.blade.php`, `financial-overview.blade.php`.
- **Navigation & Sécurité** :
  - Routes web enregistrées pour toutes les vues d'historique.
  - `HistoryPolicy` enregistrée dans `AppServiceProvider`.
  - Lien "Historique & Suivi" activé dans la navigation sous le contrôle `@if (auth()->user()->canAccessHistory())`.

### 3. Principaux fichiers créés ou modifiés
- `database/migrations/2026_01_01_000017_create_stock_snapshots_table.php` (Créé)
- `app/Domain/Stock/Models/StockSnapshot.php` (Créé)
- `app/Domain/Stock/Services/StockSnapshotService.php` (Créé)
- `app/Console/Commands/GenerateMonthlyStockSnapshot.php` (Créé)
- `app/Domain/Historique/Policies/HistoryPolicy.php` (Créé)
- `app/Domain/Historique/Http/Controllers/HistoryController.php` (Créé)
- `resources/views/historique/index.blade.php`, `purchases.blade.php`, `sales.blade.php`, `stock-at-date.blade.php`, `top-products.blade.php`, `financial-overview.blade.php` (Créés)
- `app/Providers/AppServiceProvider.php` (Modifié - enregistrement `HistoryPolicy`)
- `routes/web.php` (Modifié - routes de l'historique)
- `resources/views/layouts/app.blade.php` (Modifié - lien navigation historique)
- `tests/Feature/StockSnapshotTest.php`, `HistoryTest.php` (Créés)

### 4. Décisions techniques importantes
- **Reconstitution performante par snapshots** : Évite de rejouer des dizaines de milliers de lignes depuis l'origine de la base en démarrant depuis la dernière clôture mensuelle.
- **Strict respect de l'absence de calcul de marge** : L'historique fournit uniquement la traçabilité des achats, ventes, mouvements et encours financiers sans calcul de rentabilité ou de marge.
- **Accès restreint à la direction** : Les Caissiers sont totalement bloqués par la Policy et ne voient pas le module d'historique.

### 5. Tests et vérifications effectués
- `tests/Feature/StockSnapshotTest.php` : Génération des snapshots mensuels et vérification de l'exactitude du recalcul de stock à une date passée.
- `tests/Feature/HistoryTest.php` : Vérification de l'accès Admin à toutes les pages de l'historique et du rejet (403 Forbidden) pour les Caissiers.

### 6. Problèmes rencontrés et leurs solutions
- *Problème* : Séparateurs de namespace incorrects (`/` au lieu de `\`) dans les déclarations de `Repackaging.php` et `StoreCustomerReturnRequest.php`.
- *Solution* : Normalisation des séparateurs en syntaxe PHP valide `\`.

### 7. État actuel de la tâche
- **Phases 0 à 7** : Terminées à 100%. L'ensemble des fonctionnalités fonctionnelles, techniques, ergonomiques et de sécurité définies dans le cahier des charges (`context.md`) et le plan de réalisation (`tasksandplan.md`) sont intégralement développées et testées.

### 8. Prochaine tâche recommandée
**Phase 8 — Déploiement et mise en production** :
1. Exécution des migrations et seeders en environnement d'homologation/production (`php artisan migrate --seed`).
2. Configuration de la commande planifiée Artisan `php artisan stock:snapshot` (cron mensuel).
3. Sauvegardes automatiques de la base de données et livraison finale.


