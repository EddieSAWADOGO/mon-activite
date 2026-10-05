# SuivreMonCommerce — Application de Gestion Commerciale & Traçabilité

**SuivreMonCommerce** (`suivremoncommerce.com`) est une application web PWA (Progressive Web App) monolithique développée avec **Laravel 13 (Blade)**, **Alpine.js** et **Tailwind CSS**, conçue pour la gestion commerciale et la traçabilité complète de l'activité d'un commerçant (négoce de produits auprès de fournisseurs et revente aux clients).

---

## 🚀 Principes Fondamentaux & Règles Métier

1. **Mémoire complète & traçabilité historique** : L'application conserve l'historique exhaustif de tous les événements (achats, ventes, règlements, retours, pertes, reconditionnements).
2. **Aucun calcul de bénéfice ou de marge** : L'application n'est pas un logiciel comptable. Elle ne calcule aucune marge ni rentabilité ; son rôle est la traçabilité stricte des mouvements et des encours.
3. **Modèle de stock par unité réellement détenue** : Le stock n'est pas agrégé en un total unique. Chaque unité déclarée pour un produit (ex: bidon, carton de 6, carton de 12) possède son propre compteur de stock, son seuil d'alerte bas et son prix de vente par défaut (`prix_vente_defaut`).
4. **Factures Immuables reconstruites à la demande** : Aucun fichier PDF n'est conservé de manière permanente sur le serveur. Toute facture est reconstruite à la demande depuis la base de données. Les données de fond d'une facture sont immuables (interdiction de modification et suppression).
5. **Non-réintégration automatique des retours clients** : L'enregistrement d'un retour client le place en attente (`pending`). Sa réintégration en stock ou sa déclaration en perte nécessite une validation manuelle explicite par un Administrateur.
6. **Contrôle d'accès strict côté serveur** : Matrice de rôle (`Super Administrateur`, `Administrateur`, `Caissier`) appliquée via les Policies Laravel. Le Caissier effectue les ventes et enregistre les règlements, mais n'a pas accès à l'historique, aux retours, aux pertes ou au reconditionnement.

---

## 🛠️ Stack Technique

| Composant | Technologie / Version | Description |
|---|---|---|
| Backend | PHP 8.3 / Laravel 13.x | Monolithe MVC, architecture modulaire par domaine |
| Frontend | Blade SSR + Alpine.js CDN | Rendu serveur + interactivité dynamique côté client (aucun framework SPA séparé) |
| Styles & Design | Tailwind CSS (Play CDN) | Design system mobile-first responsive |
| Icônes | Heroicons (`blade-ui-kit/blade-heroicons`) | Icônes SVG sématiques (aucun émoji Unicode dans l'UI) |
| Base de données | MySQL 8.x | Persistance relationnelle sous transactions DB |

---

## 📂 Architecture Modulaire par Domaine (`app/Domain/`)

Le code backend est structuré par domaines métier :

```
app/Domain/
├── Produits/          # Produits & Unités de stock (StockUnit)
├── Stock/             # Compteurs, Mouvements unifiés & Snapshots mensuels
├── Achats/            # Approvisionnement auprès des fournisseurs
├── Ventes/            # Ventes multi-lignes, détection d'écarts de prix & cassures
├── Facturation/       # Factures immuables, rendu HTML, export PDF temporaire
├── Paiements/         # Règlements des factures, calcul des soldes & créances/dettes
├── Clients/           # Fiches clients particuliers & entreprises
├── Fournisseurs/      # Fiches fournisseurs & encours
├── Retours/           # Retours clients & validation manuelle
├── Pertes/            # Déclarations de perte
├── Reconditionnement/ # Regroupement/éclatement d'unités de stock
├── Historique/        # Module de suivi avancé, Palmarès & reconstitution de stock
└── Utilisateurs/      # Rôles, permissions & utilisateurs
```

---

## 💻 Installation & Initialisation Locale

### Prerequisites
- PHP >= 8.3
- Composer
- MySQL >= 8.0

### Étapes d'installation

1. **Cloner le dépôt et installer les dépendances composer** :
   ```bash
   git clone <repository_url> mon-activite
   cd mon-activite
   composer install
   ```

2. **Configurer les variables d'environnement** :
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Configurer la connexion MySQL dans `.env` :
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=monactivite_bd
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Exécuter les migrations et alimenter les données de démonstration** :
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Lancer le serveur local** :
   ```bash
   php artisan serve
   ```
   Accéder à l'application sur `http://127.0.0.1:8000`.

---

## 🔑 Comptes de Démonstration (Seeder)

Le seeder `DatabaseSeeder` génère les comptes suivants (Mot de passe commun : `password`) :

| Rôle | Email | Responsabilités |
|---|---|---|
| **Super Administrateur** | `superadmin@suivremoncommerce.com` | Accès complet, gestion des utilisateurs & système |
| **Administrateur** | `admin@suivremoncommerce.com` | Produits, stocks, achats, ventes, retours, pertes, historique & finances |
| **Caissier** | `cashier@suivremoncommerce.com` | Ventes, création de clients, consultation stock, enregistrement des règlements |

---

## ⏱️ Commandes & Tâches Planifiées

### Clôture mensuelle des compteurs de stock (Snapshot)

Pour capturer l'état exact des compteurs de stock à la fin de chaque mois (permettant la reconstitution rapide du stock à une date passée) :

```bash
# Générer le snapshot pour le mois en cours
php artisan stock:snapshot

# Générer le snapshot pour une période spécifique
php artisan stock:snapshot --year=2026 --month=1
```

La commande est automatique au 1er de chaque mois à minuit via `routes/console.php` :
```php
Schedule::command('stock:snapshot')->monthlyOn(1, '00:00');
```

---

## 🧪 Exécution de la Suite de Tests

L'application comprend une suite complète de tests fonctionnels et d'intégration :

```bash
php artisan test
```

Classes de tests principales :
- `ProductTest` : CRUD produits, unités, immutabilité des équivalences, alertes stock bas.
- `PurchaseTest` : Achats multi-lignes, incrémentation du stock, mouvements d'entrée.
- `SaleTest` : Ventes multi-lignes, cassures de cartons, motif d'écart de prix obligatoire.
- `InvoiceTest` : Factures immuables (exception sur modification/suppression), PDF et WhatsApp.
- `PaymentTest` : Règlements partiels/totaux, mise à jour des soldes et statuts.
- `CustomerReturnTest` : Validation manuelle des retours (réintégration vs perte).
- `LossTest` : Déclarations de perte et décrémentation du stock.
- `RepackagingTest` : Reconditionnement d'unités et vérification des équivalences.
- `StockSnapshotTest` : Reconstitution du stock à une date passée.
- `HistoryTest` : Contrôle strict des autorisations par rôle (Admin vs Caissier).

---

## 📱 Fonctionnalités PWA

L'application intègre un manifeste PWA (`public/manifest.json`) et est optimisée pour être installée directement sur l'écran d'accueil d'un smartphone, d'une tablette ou d'un ordinateur.

---

## 📜 Licence

Ce projet est un logiciel propriétaire développé pour la gestion commerciale de **SuivreMonCommerce** (`suivremoncommerce.com`). Tous droits réservés.
