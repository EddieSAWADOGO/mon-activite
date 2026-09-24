# Liste exhaustive des tâches et plan de réalisation

Basé sur le cahier des charges fonctionnel fourni. Stack retenue : Laravel monolithique (vues Blade), organisation modulaire stricte par domaine.

---

## 1. Liste exhaustive des tâches

### 1.1 Socle technique et infrastructure
- Définir l'organisation modulaire par domaine (dossiers par module : Produits, Stock, Achats, Ventes, Fournisseurs, Clients, Facturation, Paiements, Historique, Utilisateurs)
- Configurer l'environnement (local, staging, production), variables `.env`
- Configurer la base de données MySQL

- Mettre en place le rendu PWA : manifest.json, service worker, icônes, installabilité sur mobile/tablette/desktop
- Mettre en place le design system responsive (Mac/iPhone en priorité), charte graphique, composants Blade réutilisables
- Mettre en place la gestion des rôles et permissions côté serveur (middleware, policies), jamais côté front uniquement
- Mettre en place un système de logs d'activité (qui a fait quoi, quand)
- Mettre en place les tests automatisés (unitaires + fonctionnels) au fur et à mesure
- Mettre en place un fichier de suivi d'avancement (rapport de session pour reprise par un nouvel agent)

### 1.2 Authentification et tableau de bord
- Connexion par email/mot de passe
- Réinitialisation de mot de passe gérée uniquement depuis l'administration (pas de flux self-service)
- Tableau de bord : alertes de stock bas (par unité), factures récentes, total dû aux fournisseurs, total dû par les clients, dernières opérations (achats, ventes, retours, pertes)
- Adaptation du contenu du tableau de bord selon le rôle connecté

### 1.3 Gestion des utilisateurs et rôles
- Modèle des rôles : Super administrateur, Administrateur/propriétaire, Caissier
- CRUD utilisateurs (réservé Super admin / Administrateur)
- Attribution des rôles et permissions
- Contrôle serveur strict de chaque permission (policies Laravel), pour chaque action listée dans le cahier des charges
- Traçabilité : chaque vente enregistre l'utilisateur/caissier l'ayant réalisée

### 1.4 Produits — fiche produit
- CRUD produit (nom, description, statut actif/inactif, seuil d'alerte — désormais par unité)
- Liste des produits avec recherche et filtres
- Fiche détail produit : infos générales, unité de base, unités déclarées avec équivalences, stock détaillé par unité, historique complet des mouvements

### 1.5 Produits — gestion des unités et du stock
- Modèle de données : unité de base + unités déclarées (équivalence en unité de base)
- Compteur de stock indépendant par unité déclarée (pas de total unique converti)
- Champ `prix_vente_defaut` par unité
- Seuil d'alerte de stock bas par unité (valeur par défaut + modifiable)
- Règle d'immutabilité : interdiction de suppression d'une unité déjà utilisée dans une transaction → statut archivé/désactivé à la place
- Règle d'immutabilité : changement d'équivalence = création d'une nouvelle unité, jamais modification de l'existante
- Vue de consultation du stock unité par unité (pas de total agrégé)
- Affichage de l'unité réellement utilisée sur chaque vente et chaque facture

### 1.6 Sous-module Achats
- Formulaire d'enregistrement d'un achat (produit, unité, quantité, prix unitaire, fournisseur, montant total, montant payé, reste à payer, date, utilisateur)
- Incrémentation automatique du compteur de stock de l'unité concernée
- Génération de la ligne de mouvement correspondante (entrée)
- Liste/historique des achats avec filtres

### 1.7 Sous-module Ventes
- Formulaire de vente multi-produits (unité, quantité, prix unitaire par ligne)
- Détection automatique d'écart de prix vs `prix_vente_defaut` de l'unité → champ motif obligatoire si écart
- Gestion de la "cassure" : sélection manuelle du carton/unité source à ouvrir, décrément de ce compteur, ajout du reliquat au compteur de l'unité de base
- Association client (particulier ou entreprise)
- Calcul montant total, montant payé, reste à payer
- Génération des lignes de mouvement (sortie sur unité ouverte + entrée sur reliquat en cas de cassure)
- Génération automatique de la facture associée
- Liste/historique des ventes avec filtres

### 1.8 Sous-module Retours clients
- Formulaire de retour (client, facture d'origine, produit, quantité, unité de réception, motif en texte libre/liste, date, utilisateur, état du produit)
- Validation manuelle obligatoire avant réintégration du compteur de stock
- Option de déclarer directement le retour comme perte si non récupérable
- Restriction d'accès : Administrateur et Agent uniquement (pas le Caissier)
- Génération de la ligne de mouvement une fois validé

### 1.9 Sous-module Pertes
- Formulaire de perte (produit, quantité, unité, date, motif, commentaire libre, utilisateur)
- Décrément automatique du compteur de stock de l'unité concernée
- Restriction d'accès : Administrateur et Agent uniquement
- Génération de la ligne de mouvement

### 1.10 Sous-module Reconditionnement
- Formulaire de reconditionnement (produit, unité de départ + quantité prélevée, unité d'arrivée + quantité obtenue, date, utilisateur)
- Décrément du compteur de l'unité de départ, incrément du compteur de l'unité d'arrivée
- Génération de la ligne de mouvement

### 1.11 Historique des mouvements de stock
- Modèle de données unifié des mouvements (type, unité, quantité, sens, date, référence à l'opération d'origine)
- Génération systématique des lignes de mouvement pour : achats, ventes (y compris cassures = 2 lignes), retours validés, pertes, reconditionnements
- Vue de consultation de l'historique par produit

### 1.12 Snapshots mensuels (clôtures de stock)
- Job planifié (scheduler Laravel) de génération d'un snapshot en fin de mois : état exact de tous les compteurs par unité, par produit
- Fonction de reconstitution de l'état du stock à une date donnée : repartir du snapshot le plus proche, rejouer uniquement les mouvements postérieurs
- Vue de consultation du stock à une date/période passée, par produit et par unité

### 1.13 Fournisseurs
- CRUD fournisseur (nom, téléphone, WhatsApp, adresse, type entreprise/particulier, contact, email, notes)
- Liste avec recherche et filtres
- Fiche détail : infos générales, historique des produits achetés, montant total acheté, montant total payé, montant restant dû, historique des paiements, dernières transactions
- Vue globale et détaillée des dettes fournisseurs (sans relance automatique)

### 1.14 Clients
- CRUD client particulier (nom, prénom, téléphone, WhatsApp, adresse)
- CRUD client entreprise (raison sociale, responsable, téléphone, email, adresse, IFU, RCCM)
- Liste avec recherche et filtres
- Fiche détail : infos générales, historique des achats, factures associées, montant total acheté, montant payé, reste dû, date de dernière commande
- Vue globale et détaillée des créances clients (sans relance automatique)
- Création de client accessible aussi au Caissier

### 1.15 Facturation
- Modèle de données Facture (numéro, client, date, sous-total, remise, taxe, total, montant payé, reste à payer, statut, mode de paiement, utilisateur/caissier)
- Modèle de données Ligne de facture (facture, produit, quantité, unité, prix unitaire, motif de remise, montant ligne)
- Immutabilité stricte : aucune modification possible du contenu après création
- Génération à la demande du rendu HTML de la facture (template, pas de fichier stocké)
- Génération à la demande d'un export PDF temporaire (non conservé sur le serveur)
- Génération du lien WhatsApp (wa.me) avec message prérempli
- Recherche et filtres des factures (client, date, numéro, statut, montant, utilisateur, mode de paiement)
- Association automatique facture ↔ vente ↔ client

### 1.16 Paiements / Règlements
- Modèle de données Paiement (facture, montant, date, mode de paiement, utilisateur)
- Enregistrement de chaque paiement comme événement indépendant (jamais d'écrasement d'un champ "montant payé")
- Calcul dynamique du montant payé / reste à payer par agrégation des paiements liés
- Mise à jour du statut de la facture (payée / partiellement payée / impayée) en fonction des paiements

### 1.17 Module Stock (vue d'ensemble)
- Vue d'ensemble de tous les produits avec compteurs détaillés par unité
- Historique global des mouvements filtrable
- Alertes de stock bas (par unité, selon seuil défini)
- Accès en lecture seule pour le Caissier

### 1.18 Module Historique et suivi
- Vue achats par produit, filtrable par période (jour/semaine/mois/dates personnalisées), avec total par produit et total global
- Vue ventes par produit, filtrable par période, avec motif de remise si applicable, total par produit et total global
- Consultation de l'état des compteurs de stock à une date/période passée (via snapshots + rejeu)
- Classement des produits les plus vendus (par quantité ou par montant)
- Vue consolidée des dettes fournisseurs et créances clients en temps réel
- Recherche/filtrage des factures (reprise des mêmes filtres que le module Facturation)
- Restriction d'accès : non accessible au Caissier

### 1.19 Sécurité et permissions transverses
- Matrice complète des permissions par rôle (Super admin / Administrateur / Caissier) pour chaque action du cahier des charges
- Application de policies/middlewares sur toutes les routes sensibles
- Tests de non-régression sur les permissions (un Caissier ne doit jamais pouvoir accéder aux actions interdites, même via URL directe)

### 1.20 Design, responsive et PWA
- Charte graphique (couleurs, typographies, composants)
- Adaptation desktop / tablette / mobile pour chaque module
- Installation PWA testée sur iPhone/Mac et Android/Windows
- Icônes, splash screens, manifest

### 1.21 Tests et qualité
- Tests unitaires sur les règles métier critiques (calcul des compteurs par unité, cassures, reconditionnement, snapshots, immutabilité des factures)
- Tests fonctionnels par module (produits, achats, ventes, retours, pertes, reconditionnement, facturation, paiements, historique)
- Tests de permissions par rôle
- Recette utilisateur (Chris) sur chaque module livré

### 1.22 Déploiement
- Choix et configuration de l'hébergement (VPS existant chez Chris — cf. infra centralisée)
- Mise en place CI/CD ou procédure de déploiement manuelle documentée
- Sauvegardes automatiques de la base de données
- Mise en production progressive par phase

### 1.23 Documentation et transmission
- Documentation technique de l'architecture modulaire
- Documentation des règles métier sensibles (unités, cassures, snapshots, immutabilité)
- Fichier de suivi d'avancement mis à jour à chaque session/phase
- Prompt réutilisable pour agents IA successifs, avec contexte complet à chaque reprise

---

## 2. Plan de réalisation par phases

### Phase 0 — Cadrage et socle
- Initialisation du projet Laravel, structure modulaire, environnements
- Design system de base (responsive, PWA de base)
- Authentification, rôles (Super admin / Administrateur / Caissier), policies de base
- Mise en place du fichier de suivi d'avancement et du prompt réutilisable

### Phase 1 — Produits et unités (fondation du modèle de stock)
- CRUD produit + fiche produit
- Modèle des unités : unité de base, unités déclarées, équivalences, `prix_vente_defaut`, seuil d'alerte par unité
- Compteurs de stock par unité (sans mouvements encore)
- Règles d'immutabilité des unités

### Phase 2 — Mouvements de base : Achats et Stock
- Sous-module Achats (incrémentation des compteurs)
- Historique des mouvements (modèle unifié)
- Module Stock — vue d'ensemble par unité, alertes de stock bas

### Phase 3 — Ventes, cassures et facturation
- Sous-module Ventes (multi-lignes, détection d'écart de prix, motif obligatoire)
- Mécanisme de cassure (sélection manuelle, décrément/reliquat)
- Génération de facture (modèle immuable), rendu HTML à la demande
- Export PDF temporaire, lien WhatsApp

### Phase 4 — Paiements, fournisseurs, clients
- Modèle Paiement/Règlement (facture, montant, date, mode, utilisateur)
- CRUD Fournisseurs + fiche détail + dettes
- CRUD Clients (particulier/entreprise) + fiche détail + créances
- Création client par le Caissier

### Phase 5 — Retours, pertes, reconditionnement
- Sous-module Retours clients (validation manuelle, restriction de rôle)
- Sous-module Pertes (restriction de rôle)
- Sous-module Reconditionnement
- Intégration complète dans l'historique des mouvements

### Phase 6 — Snapshots et historique/suivi avancé
- Job de snapshot mensuel des compteurs de stock
- Reconstitution de l'état du stock à une date passée
- Module Historique et suivi : vues achats/ventes par produit et par période, classement des produits les plus vendus, vue consolidée dettes/créances, filtres de factures

### Phase 7 — Permissions, tests, polish
- Revue complète de la matrice des permissions et policies
- Tests unitaires et fonctionnels sur l'ensemble des modules
- Finalisation du design responsive et de l'installabilité PWA
- Recette utilisateur complète

### Phase 8 — Déploiement et mise en production
- Configuration de l'hébergement, sauvegardes automatiques
- Déploiement progressif par module ou en bloc
- Documentation finale et passation