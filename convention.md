# Mon-Activité — Conventions de développement, sécurité et UI/UX

Ce document est la référence unique et obligatoire pour tout développement sur le projet **Mon-Activité** (application de gestion commerciale — anciennement nommée "MwenBiz"). Il doit être appliqué à l'identique par tout agent ou développeur intervenant sur le projet, à n'importe quel moment, dans n'importe quelle session. Aucune décision présente dans ce document ne doit être réinterprétée, contournée ou remplacée par une préférence ponctuelle : en cas de doute, ce document fait autorité.

---

## 1. Stack technique figée

Ne jamais dévier de ces versions sans mise à jour explicite de ce document.

| Composant | Version figée | Rôle |
|---|---|---|
| PHP | 8.3 minimum | Langage backend |
| Laravel | 13.x (`^13.0`) | Framework monolithique, MVC |
| Moteur de vues | Blade | Rendu serveur (SSR), pas d'API séparée |
| Alpine.js | via CDN (`cdn.jsdelivr.net/npm/alpinejs`) | Toute l'interactivité client : micro-interactions (toggle, modale, onglet) **et** formulaires dynamiques (lignes de vente/achat, sélection d'unité, calculs en temps réel, recherche live) |
| Tailwind CSS | via CDN Play (`cdn.tailwindcss.com`) | Styles utilitaires, design system — aucune étape de build npm |
| Icônes | `blade-ui-kit/blade-heroicons` (`^2.7`) | Voir section 8 |
| Base de données | MySQL 8.x ou PostgreSQL 16.x (à figer dès le démarrage, ne plus changer ensuite) | Persistance |
| Gestionnaire de dépendances | Composer (PHP) uniquement | Plus de npm côté assets front (Tailwind et Alpine chargés en CDN) |

Règle absolue : l'application reste un **monolithe Laravel/Blade**. Pas de framework JS séparé (React/Vue), pas d'API REST exposée sauf besoin explicite futur documenté. Toute interactivité passe par **Alpine.js** côté client. **Livewire n'est pas utilisé sur ce projet.**

**Implication concrète pour les formulaires multi-lignes (vente, achat)** : la construction dynamique des lignes (ajout/suppression de ligne, calculs de sous-total, sélection d'unité) se fait **entièrement côté client en Alpine.js** (`x-data`, tableau JS local, calculs en JS). La soumission finale se fait en un seul POST vers le Controller (form classique avec champs générés dynamiquement par Alpine, ou `fetch` envoyant un JSON structuré), qui valide via une Form Request et délègue au Service. Il n'y a pas de rendu partiel serveur en va-et-vient à chaque ligne ajoutée — tout le calcul intermédiaire (sous-totaux, total) est fait en JS pour l'affichage, puis recalculé et validé côté serveur dans le Service au moment de l'enregistrement (le serveur ne fait jamais confiance aux totaux envoyés par le client).

---

## 2. Structure des fichiers (organisation modulaire par domaine)

L'arborescence backend est organisée **par domaine métier**, pas par type technique générique. Chaque domaine regroupe ses propres Models, Controllers, Services, Requests, Policies.

```
app/
├── Domain/
│   ├── Produits/
│   │   ├── Models/
│   │   ├── Http/Controllers/
│   │   ├── Http/Requests/
│   │   ├── Services/
│   │   └── Policies/
│   ├── Stock/
│   │   ├── Models/
│   │   └── Services/
│   ├── Achats/
│   ├── Ventes/
│   ├── Retours/
│   ├── Pertes/
│   ├── Reconditionnement/
│   ├── Fournisseurs/
│   ├── Clients/
│   ├── Facturation/
│   ├── Paiements/
│   ├── Historique/
│   └── Utilisateurs/
├── Support/
│   ├── Enums/
│   ├── ValueObjects/
│   └── Traits/
└── Providers/
```

Règles :
- Un fichier = une responsabilité. Jamais deux classes dans un même fichier.
- Un Controller ne connaît jamais directement le Model d'un autre domaine : il passe par le Service ou le Model public de ce domaine.
- Les vues Blade suivent la même logique de dossier : `resources/views/produits/`, `resources/views/ventes/`, etc. — jamais un dossier `views/pages` fourre-tout.
- Aucun fichier ne dépasse ~300 lignes ; au-delà, extraire en Service, Trait ou Action dédiée.
- Le JS Alpine spécifique à un écran complexe est écrit inline dans le Blade concerné (`x-data="{...}"`) pour les cas simples, ou extrait dans `resources/js/components/<nom>.js` (chargé en `<script>` classique, sans bundler) si la logique dépasse ~50 lignes.

---

## 3. Conventions de nommage

Ces conventions s'appliquent **sans exception**, partout dans le code.

### 3.1 Général
- Tout le code (classes, méthodes, variables, commentaires techniques) est écrit **en anglais**, même si le métier et l'interface utilisateur sont en français. Exemple : `class SaleService`, pas `class ServiceVente`. Les libellés affichés à l'utilisateur (labels Blade, messages, validations) sont en français.
- Pas d'abréviations obscures (`qty` toléré car standard, mais pas `prd` pour "produit" → `product`).

### 3.2 Classes
- Models : singulier, PascalCase → `Product`, `StockUnit`, `Sale`, `SaleLine`, `Purchase`, `Return`, `Loss`, `Repackaging`, `Invoice`, `InvoiceLine`, `Payment`, `Supplier`, `Customer`, `StockMovement`, `StockSnapshot`.
- Controllers : PascalCase + suffixe `Controller` → `ProductController`, `SaleController`.
- Services : PascalCase + suffixe `Service` → `StockMovementService`, `SaleService`, `InvoicePdfService`.
- Form Requests : PascalCase + suffixe `Request` → `StoreSaleRequest`, `UpdateProductRequest`.
- Policies : PascalCase + suffixe `Policy` → `SalePolicy`, `ReturnPolicy`.
- Enums : PascalCase, singulier → `InvoiceStatus`, `MovementType`, `UserRole`.
- Pas de composants Livewire : les écrans dynamiques sont de simples vues Blade + Alpine.js, sans classe PHP dédiée à l'interactivité. Si un endpoint AJAX dédié est nécessaire (ex. recherche live produit alimentant un `fetch` Alpine), c'est une méthode supplémentaire du Controller du domaine concerné, nommée explicitement (`searchAjax`, `quickLookup`), qui retourne du JSON via `response()->json()`.

### 3.3 Méthodes et fonctions
- camelCase, verbe d'action explicite → `registerPurchase()`, `applySaleLine()`, `openCarton()`, `mergeIntoBaseUnit()`, `recordPayment()`, `computeStockAtDate()`.
- Les méthodes qui modifient un état (écriture) commencent par un verbe d'action (`register`, `record`, `apply`, `validate`, `cancel`) ; les méthodes de lecture par `get`, `find`, `compute`, `list`.
- Les méthodes booléennes commencent par `is`, `has`, `can` → `isLowStock()`, `hasDiscount()`, `canBeDeleted()`.

### 3.4 Variables
- camelCase, nom explicite en anglais → `$stockUnit`, `$purchasePrice`, `$remainingAmount`, jamais `$x`, `$tmp`, `$data2`.
- Les collections/tableaux sont au pluriel → `$products`, `$saleLines`.
- Les montants financiers sont toujours suffixés par leur nature quand ambigu → `$totalAmount`, `$paidAmount`, `$remainingAmount`.

### 3.5 Base de données
- Tables : snake_case, pluriel → `products`, `stock_units`, `sales`, `sale_lines`, `stock_movements`, `stock_snapshots`, `invoices`, `invoice_lines`, `payments`.
- Colonnes : snake_case → `unit_price`, `remaining_amount`, `created_by_user_id`.
- Clés étrangères : `<singulier_table>_id` → `product_id`, `supplier_id`, `unit_id`.
- Colonnes booléennes : préfixe `is_` ou `has_` → `is_active`, `has_discount`.
- Toujours `created_at` / `updated_at` (timestamps Laravel standards) ; ajouter `deleted_at` (soft delete) uniquement si explicitement justifié par le métier (à valider au cas par cas, pas par défaut — l'immutabilité des factures, par exemple, exclut le soft delete sur `invoices`).

### 3.6 Routes
- snake_case ou kebab-case dans l'URL, jamais de camelCase dans une URL → `/produits/{product}/mouvements`, `/ventes/creer`.
- Noms de route en dot notation par domaine → `produits.index`, `produits.show`, `ventes.store`, `stock.mouvements.index`.

### 3.7 Fichiers Blade
- kebab-case → `product-detail.blade.php`, `sale-form.blade.php`.

---

## 4. Conventions Models (Eloquent)

- Un Model = une table, aucune logique métier lourde dedans. Le Model contient : relations, casts, scopes simples, accesseurs/mutateurs de présentation légers, constantes propres à l'entité.
- Toute règle métier non triviale (calcul, validation croisée, orchestration de plusieurs tables) va dans un **Service**, jamais dans le Model ni dans le Controller.
- `$fillable` explicite systématiquement (jamais `$guarded = []`).
- Casts explicites sur tous les champs numériques, monétaires, dates, enums (`casts()` méthode Laravel 13, pas la propriété `$casts` dépréciée).
- Les relations sont typées et documentées (`hasMany`, `belongsTo`, etc.) avec return type PHP strict.
- Aucune requête SQL brute (`DB::raw`, `whereRaw`) sauf cas de performance justifié et commenté.
- Les montants (prix, totaux) sont stockés en entier (centimes de FCFA, unité indivisible) ou en `decimal` strict en base — jamais en `float`.

---

## 5. Conventions Controllers

- Un Controller ne fait que : valider l'entrée (via Form Request), appeler un Service, retourner une vue/redirection (ou un JSON pour les endpoints AJAX Alpine).
- Un Controller ne contient **jamais** de logique métier (pas de calcul de stock, pas de règle de cassure, pas de génération de facture directement dedans).
- Controllers "resource" standards Laravel (`index`, `create`, `store`, `show`, `edit`, `update`, `destroy`) — pas de méthodes exotiques sauf besoin réel documenté en commentaire (les méthodes AJAX dédiées type `searchAjax` sont un besoin documenté et acceptées).
- Toute autorisation passe par `$this->authorize()` ou une Policy, jamais par un `if ($user->role === ...)` dispersé dans le Controller.
- Pas d'injection de dépendances lourdes inutiles ; injection via constructeur ou méthode selon le besoin.

---

## 6. Conventions Services — quand et comment

Un **Service** est obligatoire dès qu'une des conditions suivantes est vraie :
1. L'opération touche plusieurs tables/Models en une seule transaction logique (ex. une vente touche `sales`, `sale_lines`, `stock_movements`, `invoices`).
2. L'opération applique une règle métier non triviale (ex. calcul de cassure, reconstitution de stock à une date, détection d'écart de prix).
3. L'opération doit être réutilisable depuis plusieurs points d'entrée (Controller web, commande Artisan, job planifié, endpoint AJAX).
4. L'opération doit être testée unitairement de façon isolée.

Si aucune de ces conditions n'est vraie (simple CRUD sans règle métier), un Controller fin appelant directement Eloquent est suffisant — ne pas sur-architecturer un simple `Product::create()`.

Règles d'écriture des Services :
- Un Service par domaine métier cohérent, nommé par son action principale : `SaleService`, `StockMovementService`, `InvoiceGenerationService`, `StockSnapshotService`.
- Chaque méthode publique d'un Service correspond à **une** opération métier complète, englobée dans une transaction DB (`DB::transaction()`) si elle touche plusieurs tables.
- Un Service ne retourne jamais directement une vue Blade ni une redirection HTTP — il retourne des Models, DTO, ou lève une exception métier dédiée.
- Les exceptions métier sont typées (`InsufficientStockException`, `ImmutableInvoiceException`) plutôt que des `Exception` génériques.
- Un Service ne connaît pas la session HTTP ni la requête — il reçoit ses paramètres explicitement (DTO ou tableau validé), pour rester testable hors contexte web.
- Les totaux/sous-totaux envoyés depuis le client (calculés en Alpine.js pour l'affichage) ne sont **jamais** utilisés tels quels par le Service : le Service recalcule systématiquement les montants côté serveur à partir des lignes et des prix en base, et rejette la requête si un écart significatif est détecté.

---

## 7. Autres briques transverses

- **Form Requests** : toute validation d'entrée passe par une Form Request dédiée (jamais de `$request->validate()` inline dans un Controller au-delà d'un prototypage initial).
- **Policies** : une Policy par Model sensible (`Sale`, `Return`, `Loss`, `Repackaging`, `User`, `Invoice`), avec une méthode par action (`create`, `update`, `delete`, `validate` pour les retours). Appliquées côté serveur systématiquement, jamais uniquement masquées côté vue.
- **Enums natifs PHP** (`enum InvoiceStatus: string { ... }`) pour tout statut fermé (statut facture, type de mouvement, rôle utilisateur, motif de perte prédéfini).
- **Jobs / Commands Artisan** : le job de snapshot mensuel de stock est une Command Artisan planifiée (`schedule()`), qui délègue immédiatement à un Service (`StockSnapshotService::generateForMonth()`), jamais de logique inline dans la classe Command.
- **Observers Eloquent** : à éviter pour la logique métier critique (trop implicite, difficile à tracer) — préférer l'appel explicite au Service depuis le Controller. Autorisé uniquement pour des effets de bord secondaires non critiques (ex. invalidation de cache).

---

## 8. Frontend — rendu, icônes, mobile-first

### 8.1 Type de rendu (obligatoire, non négociable)
- Rendu **serveur (SSR) via Blade**.
- **Alpine.js** (chargé via CDN) pour toute l'interactivité : micro-interactions purement visuelles (ouverture/fermeture de menu, modale, onglet) **et** formulaires dynamiques (lignes de vente/achat, sélection d'unité, calculs en temps réel, recherche live).
- **Tailwind CSS chargé via CDN Play** (`<script src="https://cdn.tailwindcss.com"></script>`), pas d'étape de build npm/Vite pour les styles.
- Aucune vue ne doit dépendre d'un framework JS SPA séparé. **Livewire n'est pas utilisé sur ce projet.**
- Quand un aller-retour serveur est nécessaire en dehors de la soumission finale d'un formulaire (ex. recherche live d'un produit), il passe par un `fetch` Alpine vers une méthode Controller dédiée retournant du JSON — jamais par du rendu partiel serveur type Livewire, jamais par du polling.

### 8.2 Mobile-first — obligatoire sur chaque écran
- Toute vue est conçue et codée **d'abord** pour un écran mobile (~375–414px de large), puis étendue vers tablette et desktop avec les breakpoints Tailwind (`sm:`, `md:`, `lg:`, `xl:`).
- Aucune classe Tailwind de layout desktop ne doit être écrite sans son équivalent mobile par défaut (le style de base — sans préfixe — est TOUJOURS le style mobile).
- Les tableaux de données larges (liste de produits, stock par unité, historique des mouvements) doivent avoir un mode d'affichage adapté au mobile : cartes empilées ou défilement horizontal contrôlé, jamais un tableau HTML brut illisible sur petit écran.
- Les formulaires de vente/achat (potentiellement multi-lignes) doivent rester utilisables au pouce, avec des zones de tape suffisamment grandes (minimum 44×44px).
- Tester systématiquement chaque écran livré en largeur mobile avant de le considérer terminé.

### 8.3 Icônes — obligatoire, jamais d'émoji
- Bibliothèque unique et exclusive : **Heroicons**, via le package `blade-ui-kit/blade-heroicons` (`^2.7`).
- Utilisation en Blade : `<x-heroicon-o-nom-icone class="w-5 h-5" />` (style *outline*, préfixe `o-`) pour les icônes d'interface standard, `<x-heroicon-s-nom-icone />` (style *solid*, préfixe `s-`) réservé aux états actifs/sélectionnés ou boutons pleins.
- **Aucun émoji Unicode** (🟢, ✅, 📦, 💰, etc.) n'est autorisé nulle part dans l'interface, ni dans les messages système, ni dans les notifications, ni dans les commentaires de code. Toute information visuelle (statut payé, stock bas, alerte) passe par une icône Heroicons + une couleur sémantique (section 8.4), jamais par un émoji.
- Correspondance standard à respecter dans tout le projet (ne pas réinventer une icône différente pour le même concept d'un écran à l'autre) :

| Concept | Icône Heroicons (nom) |
|---|---|
| Produit | `cube` |
| Stock / entrepôt | `archive-box` |
| Achat (entrée) | `arrow-down-tray` |
| Vente (sortie) | `arrow-up-tray` |
| Retour client | `arrow-uturn-left` |
| Perte | `x-circle` |
| Reconditionnement | `arrows-right-left` |
| Fournisseur | `truck` |
| Client particulier | `user` |
| Client entreprise | `building-office` |
| Facture | `document-text` |
| Paiement | `banknotes` |
| Alerte stock bas | `exclamation-triangle` |
| Succès / validé | `check-circle` |
| Historique / temps | `clock` |
| Tableau de bord | `squares-2x2` |
| Utilisateurs / rôles | `users` |
| Paramètres | `cog-6-tooth` |
| Recherche | `magnifying-glass` |
| WhatsApp (lien externe) | `chat-bubble-left-right` |
| Supprimer | `trash` |
| Modifier | `pencil-square` |
| Ajouter | `plus` |

### 8.4 Charte de couleurs

Palette fixe, définie une seule fois dans le thème CSS (bloc `tailwind.config` inline, injecté avant le `<script>` du CDN Tailwind, puisqu'il n'y a plus de fichier de config buildé), jamais de couleur arbitraire hors palette dans les vues.

| Rôle | Couleur | Usage |
|---|---|---|
| Primaire | Vert (`emerald-600` / `#059669` de base, `emerald-700` au survol/actif) | Boutons principaux, liens actifs, éléments de marque, statut "payé"/"validé" |
| Primaire clair | `emerald-50` / `emerald-100` | Fonds de badge, fonds de carte en surbrillance |
| Fond principal | Blanc (`#FFFFFF`) et gris très clair (`slate-50`) | Fonds de page et de carte |
| Texte principal | `slate-900` / `slate-700` | Texte courant |
| Texte secondaire | `slate-500` | Libellés, métadonnées |
| Alerte / danger | `red-600` | Stock bas critique, perte, suppression, facture impayée en retard |
| Avertissement | `amber-500` | Stock proche du seuil, facture partiellement payée |
| Information | `sky-600` | Notes, informations neutres |
| Bordures | `slate-200` | Séparateurs, contours de carte |

Règle : le vert (primaire) est réservé aux actions positives et aux éléments de marque — il ne doit jamais être utilisé pour signaler une alerte ou une erreur. Chaque état fonctionnel (payé/impayé, stock ok/bas, actif/archivé) a une seule couleur associée, appliquée de façon identique sur tout le projet.

### 8.5 Typographie et composants
- Une seule famille de police pour tout le projet (police système par défaut de Tailwind, ou une police Google Fonts unique choisie une fois pour toutes et non modifiée ensuite, chargée via `<link>` dans le layout principal).
- Composants Blade réutilisables obligatoires pour tout élément répété plus de 2 fois : `<x-ui.button>`, `<x-ui.badge>`, `<x-ui.card>`, `<x-ui.table>`, `<x-ui.stat-card>`, `<x-ui.empty-state>` — centralisés dans `resources/views/components/ui/`. Interdiction de dupliquer le HTML/Tailwind d'un bouton ou d'une carte à la main dans chaque vue.

---

## 9. Sécurité backend — obligatoire sur tout le projet

- **Autorisations** : chaque action sensible (créer/modifier/supprimer produit, retour, perte, reconditionnement, utilisateur, facture) est protégée par une Policy Laravel appliquée côté serveur (`$this->authorize()` ou middleware `can:`). Ne jamais se reposer uniquement sur le masquage d'un bouton côté vue.
- **Validation** : toute entrée utilisateur passe par une Form Request avec règles strictes (types, bornes, existence en base via `exists:`, unicité via `unique:`).
- **Mass assignment** : `$fillable` explicite sur tous les Models, jamais `$guarded = []`.
- **Requêtes SQL** : Eloquent/Query Builder exclusivement ; toute requête brute doit être paramétrée (jamais de concaténation de chaîne dans une requête SQL).
- **CSRF** : `@csrf` systématique sur tous les formulaires classiques. Pour les appels `fetch`/AJAX déclenchés par Alpine.js, le token CSRF est lu depuis une balise `<meta name="csrf-token" content="{{ csrf_token() }}">` dans le layout, et envoyé en header `X-CSRF-TOKEN` sur chaque requête POST/PUT/DELETE — géré explicitement dans un petit helper JS partagé, puisqu'il n'y a plus de protection CSRF native automatique comme avec Livewire.
- **Authentification** : hachage des mots de passe via `bcrypt`/`Hash::make` (défaut Laravel), jamais de mot de passe en clair stocké ou loggé.
- **Sessions** : cookies de session en `httponly` et `secure` en production (HTTPS obligatoire en production, pas d'exception).
- **Journalisation** : toute action sensible (vente, retour, perte, reconditionnement, modification d'utilisateur, paiement) enregistre l'utilisateur auteur et l'horodatage en base — pas seulement dans un log applicatif volatile.
- **Immutabilité des factures** : aucune route, aucun Controller, aucun Service ne doit permettre une mise à jour (`UPDATE`) des tables `invoices`/`invoice_lines` après création, à l'exception des colonnes de statut calculées automatiquement. Toute tentative de modification directe doit être bloquée au niveau du Model (méthode `update()` surchargée ou événement `updating` qui lève une exception si les colonnes protégées changent).
- **Fichiers générés (PDF)** : le PDF de facture est généré dans un fichier temporaire (`storage/app/tmp/` ou flux mémoire), envoyé au navigateur, puis supprimé immédiatement après réponse — jamais conservé durablement sur le serveur.
- **Rate limiting** : throttle Laravel standard sur les routes d'authentification, sur les endpoints AJAX publics et sur toute route exposée publiquement.
- **Dépendances** : `composer audit` exécuté avant chaque mise en production ; aucune dépendance avec vulnérabilité connue non corrigée n'est déployée. (Plus d'`npm audit` puisque Tailwind/Alpine sont chargés en CDN — vérifier ponctuellement que les versions CDN pointées restent à jour et sans faille connue.)
- **Intégrité des scripts CDN** : les balises `<script>` chargeant Tailwind et Alpine depuis un CDN incluent un attribut `integrity` (SRI) quand le CDN le permet, pour éviter qu'un script tiers compromis soit injecté silencieusement.
- **Variables sensibles** : toutes les clés, secrets, identifiants de base de données uniquement en variables d'environnement (`.env`), jamais en dur dans le code, `.env` jamais commité.
- **Sauvegardes** : sauvegarde automatique quotidienne de la base de données, testée régulièrement en restauration.

---

## 10. UX — règles d'ergonomie transverses

- Toute action destructive ou irréversible (suppression, validation définitive d'un retour, déclaration de perte) demande une confirmation explicite (modale Alpine de confirmation), jamais d'action en un clic sans retour arrière possible.
- Tout formulaire affiche un état de chargement pendant le traitement AJAX : géré en Alpine.js (`x-data="{ loading: false }"`, toggle à `true` avant le `fetch`, bouton désactivé + spinner, retour à `false` en fin de requête) — jamais d'écran figé sans retour visuel.
- Tout message de succès/erreur utilise une bannière/toast standardisée (même composant Blade partout, piloté en Alpine), avec icône Heroicons + couleur sémantique (section 8.4) — jamais de `alert()` JavaScript natif.
- Les listes longues (produits, factures, mouvements) sont systématiquement paginées et dotées d'une recherche/filtre visible en haut de liste, y compris sur mobile.
- Les montants sont toujours affichés avec le séparateur de milliers et le suffixe "FCFA", jamais un nombre brut ambigu.
- Toute unité de mesure (bidon, carton de 12, kg…) est affichée explicitement à chaque fois qu'une quantité est montrée — jamais un nombre seul sans son unité.
- Les écrans réservés à certains rôles (Historique/suivi, gestion des utilisateurs) ne sont même pas visibles dans la navigation pour un Caissier — pas seulement bloqués à l'accès.

---

## 11. Tests obligatoires

- Chaque Service métier critique (calcul de cassure, reconstitution de stock à une date via snapshot, détection d'écart de prix, immutabilité de facture) a un test unitaire (Pest ou PHPUnit, à choisir une fois et garder pour tout le projet) couvrant au minimum le cas nominal et un cas limite.
- Chaque Policy a un test vérifiant qu'un Caissier ne peut pas exécuter les actions qui lui sont interdites, y compris par accès direct à la route.
- Les endpoints AJAX (`searchAjax`, etc.) ont un test de base vérifiant l'authentification requise et le format de réponse JSON.
- Les tests s'exécutent avant chaque déploiement ; aucun déploiement ne se fait avec une suite de tests rouge.

---

## 12. Règle de constance inter-sessions

Ce document est la seule source de vérité pour les conventions de développement de Mon-Activité. Tout agent (humain ou IA) qui reprend le projet doit :
1. Relire ce document avant d'écrire la moindre ligne de code.
2. Appliquer exactement les noms, versions, couleurs et icônes listés ici — ne jamais introduire une variante "équivalente" (une autre bibliothèque d'icônes, une autre couleur primaire, un autre nom de méthode pour un concept déjà nommé ici, et surtout ne jamais réintroduire Livewire ou un build npm pour Tailwind).
3. En cas de besoin non couvert par ce document (nouvelle brique technique, nouveau concept UI), proposer l'ajout ici même avant de l'implémenter, pour que la prochaine session en hérite automatiquement.