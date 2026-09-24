# Cahier des charges fonctionnel — Mon-Activité

## 0. Présentation générale du projet

L'application de gestion commerciale est destinée à un commerçant qui achète des produits auprès de fournisseurs et les revend ensuite à des clients, qu'il s'agisse de particuliers ou d'entreprises. L'idée centrale est que toutes les informations liées à cette activité commerciale soient rassemblées dans une seule base de données : les produits eux-mêmes, les niveaux de stock, les achats effectués, les ventes réalisées, les fournisseurs, les clients, les factures, et surtout l'historique complet de tout ce qui s'est passé depuis le début de l'utilisation de l'application.

Le principe fondamental à retenir est que l'application doit fonctionner comme une mémoire complète et interrogeable à tout moment. Concrètement, cela veut dire que le commerçant doit toujours pouvoir répondre à des questions comme : qu'est-ce que j'ai acheté, chez qui, à quelle date et à quel prix ? Qu'est-ce que j'ai vendu, à qui, quand, à quel prix, et si j'ai fait une remise, pourquoi ? Quel était mon niveau de stock pour tel produit, dans telle unité, à telle date dans le passé, même si aujourd'hui le stock a changé ?

Un point important à comprendre : l'application ne fait aucun calcul de bénéfice, de marge ou de rentabilité. Ce n'est donc pas un logiciel de comptabilité classique — elle ne va jamais dire au commerçant « tu as gagné X FCFA sur ce produit ». C'est avant tout un outil de traçabilité et de suivi d'activité.

Autre détail technique important : aucune facture n'est stockée sous forme de fichier (comme un PDF) sur le serveur. À chaque fois qu'une facture doit être consultée ou téléchargée, elle est reconstruite à la demande à partir des données stockées en base.

Enfin, l'application est conçue comme une PWA (Progressive Web App), ce qui signifie qu'elle peut être installée directement sur un smartphone, une tablette ou un ordinateur, un peu comme une application native, tout en restant une application web.

---

## 1. Authentification et tableau de bord

L'utilisateur se connecte avec son email et son mot de passe. S'il oublie son mot de passe, la récupération n'est pas automatisée côté utilisateur : elle est gérée depuis l'administration (c'est-à-dire qu'un administrateur intervient pour réinitialiser l'accès).

Une fois connecté, l'utilisateur arrive directement sur un tableau de bord qui lui donne une vue d'ensemble rapide de la situation : les alertes de stock bas (produits dont la quantité est descendue sous le seuil défini), les factures récentes, le total de ce qui est dû aux fournisseurs et le total de ce que les clients doivent, ainsi que les dernières opérations effectuées (achats, ventes, retours, pertes).

Ce tableau de bord n'affiche pas la même chose selon qui est connecté : son contenu s'adapte au rôle de l'utilisateur (voir la section 8 sur les rôles).

---

## 2. Gestion des produits

Cette partie de l'application est organisée en un menu « Produits » qui contient plusieurs sous-sections : Produits (la fiche produit elle-même), Achats, Ventes, Retours clients, Pertes, et Reconditionnement.

### La fiche produit

Chaque produit possède un nom, une description, un statut qui indique s'il est actif ou inactif (un produit inactif n'est plus proposé à la vente mais reste consultable dans l'historique).

Le seuil d'alerte de stock bas n'est pas un seuil unique par produit : **chaque unité déclarée pour un produit a son propre seuil d'alerte**. Par exemple, pour un produit vendu en bidon, carton de 6 et carton de 12, on peut vouloir être alerté séparément quand il reste moins de 20 bidons en vrac, moins de 5 cartons de 6, et moins de 3 cartons de 12 — ce sont des seuils différents parce que ce sont des usages différents. Le système propose une valeur par défaut à la création d'une unité, mais ce seuil reste modifiable indépendamment pour chaque unité de chaque produit.

### La gestion des unités et le modèle de stock

C'est le point le plus central du projet, donc à bien comprendre en détail, parce qu'il structure tout le reste.

Chaque produit a une unité de base — par exemple le bidon, le kilogramme, le litre, la pièce… Un produit peut ensuite avoir plusieurs autres unités déclarées, chacune valable à la fois pour l'achat et pour la vente : il n'y a pas de distinction entre « unité d'achat » et « unité de vente », parce que dans la réalité, le commerçant achète et revend naturellement dans les mêmes unités. Chaque unité déclarée porte une équivalence exprimée dans l'unité de base — par exemple, pour un pesticide vendu au bidon : un carton de 12 équivaut à 12 bidons, un carton de 6 équivaut à 6 bidons. Pour du maïs vendu au kilogramme : un sac de 25 équivaut à 25 kg, un sac de 50 équivaut à 50 kg. On peut ajouter une nouvelle unité à un produit à n'importe quel moment, dès qu'un format différent apparaît.

Chaque unité déclarée porte aussi un **prix de vente par défaut** (`prix_vente_defaut`), propre à cette unité — le prix normal du bidon n'est pas dérivé automatiquement du prix normal du carton, chacun a son propre prix de référence déclaré. C'est ce prix qui sert de référence pour détecter une remise (voir le sous-module Ventes plus bas).

**Les unités déclarées sur un produit sont immuables une fois utilisées.** Concrètement, on ne peut jamais supprimer une unité qui a déjà servi dans au moins une transaction (achat, vente, retour, perte ou reconditionnement) — on peut seulement la désactiver (statut archivé), ce qui l'empêche d'être choisie pour de nouvelles opérations tout en la gardant visible dans l'historique. De la même façon, si l'équivalence d'une unité doit changer — par exemple si un carton qui contenait 12 bidons n'en contient plus que 10 — on ne modifie jamais l'équivalence de l'unité existante. Il faut créer une nouvelle unité (par exemple « Carton de 10 (2026) ») et désactiver l'ancienne. La raison est simple : si on modifiait l'équivalence d'une unité déjà utilisée, tout l'historique des ventes et des reconstitutions de stock passées se retrouverait faussé rétroactivement, puisque les mouvements anciens seraient réinterprétés avec la nouvelle équivalence.

Contrairement à ce qu'on pourrait imaginer, le stock n'est pas une quantité unique totalisée en unité de base. **Le stock est suivi par unité de stockage réellement détenue** : chaque unité déclarée pour un produit a son propre compteur de stock. Si un lot est acheté en carton de 12, il est stocké tel quel comme des cartons de 12 — le compteur « carton de 12 » augmente. Si un lot est acheté en carton de 6, le compteur « carton de 6 » augmente. Si un lot est acheté directement en unité de base (bidon), le compteur « bidon » (unité de base) augmente. Un même produit peut donc avoir, au même moment, plusieurs compteurs actifs en parallèle — par exemple 5 cartons de 12 en stock, 3 cartons de 6 en stock, et 20 bidons en vrac. Ce ne sont pas des présentations différentes d'un même total : ce sont réellement des quantités séparées, parce que ce sont des unités physiquement distinctes dans l'entrepôt.

Pour la vente, deux cas de figure se présentent :

- Si la vente se fait directement dans une unité pour laquelle il existe du stock disponible (par exemple vendre un carton de 12 entier alors qu'il y a des cartons de 12 en stock), la soustraction se fait simplement sur le compteur correspondant à cette unité.
- Si la vente se fait en unité de base (par exemple vendre 3 bidons) mais qu'il faut ouvrir un carton plus gros pour ça, l'utilisateur doit indiquer explicitement de quel carton il prélève (par exemple « je prends dans un carton de 12 »). Cette sélection est **toujours un choix manuel de l'utilisateur, jamais automatique**. Une fois ce choix fait : le compteur du carton concerné (carton de 12) diminue de 1 unité, la quantité effectivement vendue (3 bidons) apparaît sur la facture, et le reliquat du carton ouvert — c'est-à-dire ce qui n'a pas été vendu (12 − 3 = 9 bidons) — est ajouté au compteur « bidon » (unité de base), puisqu'il ne constitue plus un carton complet.

À l'inverse, il doit être possible de **regrouper manuellement** des unités de base pour reconstituer un carton en vue de la vente — par exemple prendre 12 bidons en vrac et les regrouper en un carton de 12. Cette opération de reconditionnement est explicite (elle ne se produit jamais automatiquement en arrière-plan) et doit être tracée comme les autres opérations : elle diminue le compteur de l'unité de base et augmente le compteur de l'unité groupée concernée.

Deux exigences complémentaires à ne pas oublier : le stock doit pouvoir être consulté et listé unité par unité (voir ce qu'il y a en cartons de 12, en cartons de 6, en bidons, séparément), et sur les ventes comme sur les factures, l'unité réellement utilisée pour la transaction doit toujours être indiquée explicitement, même quand il s'agit simplement de l'unité de base.

### Liste et fiche produit

On retrouve une liste de tous les produits, avec recherche et filtres, et une fiche détail pour chaque produit qui regroupe ses informations générales, son unité de base, la liste de ses unités utilisables avec leurs équivalences, le détail de son stock par unité (le compteur de chaque unité, pas un total unique), et l'historique complet des mouvements (achats, ventes, retours, pertes, reconditionnements) qui le concernent. On peut évidemment aussi modifier un produit existant, y compris ajouter ou modifier ses unités utilisables.

### Le sous-module Achats

Il permet d'enregistrer un achat effectué auprès d'un fournisseur. On y renseigne le produit concerné, l'unité choisie parmi celles déclarées pour ce produit, la quantité dans cette unité, le prix unitaire dans cette unité, le fournisseur, le montant total, le montant déjà payé, le reste à payer, la date, et l'utilisateur qui a réalisé l'opération. Cet achat vient directement augmenter le compteur de stock correspondant à l'unité choisie.

### Le sous-module Ventes

Il permet d'enregistrer une vente faite à un client. Une vente peut contenir un ou plusieurs produits ; pour chacun on précise l'unité choisie, la quantité, et le prix unitaire. Si la vente nécessite une « cassure » (vente en unité de base alors qu'il faut ouvrir un carton), l'unité source doit être précisée comme expliqué plus haut. On y renseigne aussi le client concerné, le montant total, le montant payé, le reste à payer, la date, et l'utilisateur.

Un point important : si le prix facturé pour une ligne de vente diffère du prix normal du produit, l'utilisateur doit obligatoirement indiquer un motif de la remise ou de l'écart, en texte libre — par exemple « remise fidélité », « négociation avec le client », « prix de gros », ou « produit légèrement abîmé ». Cette comparaison est précise : elle se fait entre le prix saisi par le caissier sur la ligne de vente et le `prix_vente_defaut` de **l'unité concernée pour ce produit** — pas un prix générique du produit toutes unités confondues. Si le prix saisi est différent (inférieur ou supérieur) de ce `prix_vente_defaut`, le champ « motif de l'écart » devient obligatoire.

### Le sous-module Retours clients

Il permet d'enregistrer un retour : on y indique le client concerné, la facture d'origine, le produit, la quantité, l'unité dans laquelle le produit est reçu en retour, le motif saisi manuellement (produit défectueux, erreur de commande, produit non conforme, client insatisfait, ou autre), la date, l'utilisateur, et l'état du produit retourné.

Un point à bien retenir : un retour ne réintègre **jamais automatiquement** le stock. Il doit être validé manuellement par un utilisateur habilité pour que le produit revienne au compteur de stock correspondant à l'unité reçue, ou bien être directement déclaré comme une perte si le produit n'est pas récupérable. Cette opération est réservée à l'Administrateur ou à l'Agent — un Caissier ne peut pas enregistrer de retour.

### Le sous-module Pertes

Il permet d'enregistrer la perte d'un produit : le produit concerné, la quantité, l'unité dans laquelle la perte est constatée, la date, le motif saisi manuellement (produit périmé, cassé, volé, détérioré, erreur d'inventaire, ou autre), un commentaire libre, et l'utilisateur. Cette opération retire automatiquement la quantité correspondante du compteur de stock de cette unité, sans passer par une vente. Comme pour les retours, cette opération est réservée à l'Administrateur ou à l'Agent — un Caissier ne peut pas l'effectuer.

### Le sous-module Reconditionnement

Il permet d'enregistrer une opération de regroupement d'unités de base en une unité plus grosse (par exemple 12 bidons regroupés en un carton de 12), en vue de faciliter une vente ou un rangement. On y renseigne le produit, l'unité de départ et la quantité prélevée, l'unité d'arrivée et la quantité obtenue, la date, et l'utilisateur. Cette opération diminue le compteur de l'unité de départ et augmente le compteur de l'unité d'arrivée. Elle doit être tracée dans l'historique des mouvements au même titre que les autres opérations.

### L'historique des mouvements

Chaque opération qui modifie un compteur de stock — un achat, une vente (y compris une cassure), une perte, un retour validé, ou un reconditionnement — génère une ou plusieurs lignes de mouvement qui conservent la date exacte, le type de mouvement, l'unité concernée, la quantité dans cette unité, et le sens (entrée ou sortie) du mouvement sur le compteur de cette unité précise. Une cassure, par exemple, génère à la fois une sortie sur le compteur de l'unité ouverte (le carton) et une entrée sur le compteur de l'unité de base (le reliquat). Cet historique est ce qui permet de reconstituer, à tout moment, l'état de chaque compteur de stock pour un produit donné à une date passée quelconque (voir la section 7.3).

Reconstituer l'état des compteurs de stock à une date passée en rejouant tous les mouvements depuis le tout début poserait un problème de performance une fois l'application utilisée depuis longtemps — avec plusieurs dizaines de milliers de lignes de mouvements après quelques années, ce recalcul deviendrait lent. Pour éviter ça, le système met en place des **snapshots (clôtures) mensuels** : à la fin de chaque mois, l'état exact de tous les compteurs de stock de chaque produit est enregistré tel quel. Quand on demande l'état du stock à une date précise, le système repart du snapshot du mois précédent le plus proche et ne rejoue que les mouvements survenus entre ce snapshot et la date demandée, au lieu de tout rejouer depuis le début.

---

## 3. Gestion des fournisseurs

On peut créer un fournisseur en renseignant son nom, son téléphone, son WhatsApp, son adresse, son type (entreprise ou particulier), une personne de contact, un email, et des notes libres. Il existe une liste des fournisseurs avec recherche et filtres, et une fiche détail pour chacun qui regroupe ses informations générales, l'historique complet des produits achetés chez lui, le montant total acheté, le montant total payé, le montant restant dû, l'historique des paiements effectués, et les dernières transactions. On peut bien sûr modifier un fournisseur existant.

Concernant le suivi des dettes fournisseurs, le système se contente d'indiquer au gérant ce qu'il doit à chaque fournisseur, en montant global et en détail par fournisseur — il n'y a aucune relance automatique.

---

## 4. Gestion des clients

Un client peut être de deux types. Pour un particulier, on renseigne nom, prénom, téléphone, WhatsApp et adresse. Pour une entreprise, on renseigne la raison sociale, le nom du responsable, le téléphone, l'email, l'adresse, et des informations fiscales comme l'IFU et le RCCM. Il existe une liste des clients avec recherche et filtres, et une fiche détail qui regroupe les informations générales, l'historique complet des achats, la liste des factures associées, le montant total acheté, le montant total payé, le montant restant dû, et la date de la dernière commande. On peut modifier un client existant.

De la même façon que pour les fournisseurs, le suivi des créances clients indique simplement au gérant ce qui lui est dû par chaque client pour quelles ventes, en montant global et en détail par client, sans relance automatique.

---

## 5. Facturation

Comme mentionné en introduction, aucun fichier PDF de facture n'est conservé sur le serveur. Toute facture est reconstruite à la demande à partir de deux tables :

- la table **Facture**, qui contient le numéro, le client, la date, le sous-total, une remise éventuelle, une taxe éventuelle, le total, le montant payé, le reste à payer, le statut, le mode de paiement, et l'utilisateur/caissier qui a réalisé la vente ;
- la table **Ligne de facture**, qui contient pour chaque ligne la facture concernée, le produit, la quantité, l'unité utilisée, le prix unitaire, le motif de la remise si applicable, et le montant total de la ligne.

Pour la consultation, le système récupère les données en base et les affiche via un template HTML au moment où on consulte la facture — rien n'est pré-généré. Le téléchargement en PDF ne se fait qu'à la demande : le fichier est généré temporairement, envoyé à l'utilisateur, puis n'est pas conservé sur le serveur. L'envoi de la facture au client peut se faire par WhatsApp, via un lien `wa.me` avec un message prérempli, par exemple : « Bonjour, voici votre facture n°FAC-2026-00125 d'un montant de 42 500 FCFA. » Il n'y a pas d'intégration avec l'API WhatsApp Business dans cette version.

On peut rechercher et filtrer les factures directement depuis la base de données, par client, par date, par numéro, par statut, par montant, par utilisateur, ou par mode de paiement. Chaque facture est automatiquement associée à la vente et au client concernés.

### Immutabilité des factures

Une fois une facture créée, son contenu de fond ne doit plus jamais pouvoir être modifié. Concrètement, dans la table Facture (numéro, client, date, sous-total, remise, taxe, total) et dans la table Ligne de facture (produit, quantité, unité, prix unitaire, motif de remise), aucune de ces informations n'est modifiable après création. C'est ce qui garantit que reconstruire la facture à la demande, dans un an comme dans dix ans, produira toujours exactement le même contenu que celui affiché le jour de la vente — sans qu'on ait besoin de conserver un fichier archivé quelque part pour s'en assurer.

Les seules choses qui peuvent évoluer après la création d'une facture sont liées aux paiements reçus, pas au contenu de la vente elle-même : le statut de la facture (payée, partiellement payée, impayée) et le montant restant dû. Mais même ça, il vaut mieux ne pas se contenter de mettre à jour un simple champ « montant payé » sur la facture à chaque paiement — ça reviendrait à écraser l'information à chaque fois et on perdrait la trace de qui a payé quoi et quand.

La bonne approche est d'avoir une table séparée, **Paiement** (ou Règlement), qui enregistre chaque paiement reçu comme un événement indépendant : la facture concernée, le montant payé à ce moment-là, la date, le mode de paiement, l'utilisateur qui a enregistré le paiement. Le « montant payé » et le « reste à payer » affichés sur la facture sont alors simplement calculés en additionnant tous les paiements liés à cette facture, jamais stockés comme une valeur qu'on écrase. C'est cohérent avec le principe de départ de l'application : tout est une mémoire complète et interrogeable, y compris l'historique des paiements.

Si une erreur est détectée après coup sur une vente déjà facturée (mauvais prix, mauvais produit), on ne corrige jamais la facture d'origine : soit ça passe par le mécanisme de retour client déjà prévu (section 2), soit — si le besoin s'en fait sentir — on introduit plus tard une notion d'avoir/note de correction, qui est elle-même une nouvelle écriture, jamais une modification rétroactive.

Cette approche répond exactement au besoin : pas de fichier de facture à archiver, aucun risque de surcharge du serveur, et l'immuabilité est garantie au niveau des données elles-mêmes plutôt qu'au niveau d'un fichier.

---

## 6. Gestion du stock

Cette section donne une vue d'ensemble de tous les produits en stock, avec pour chacun le détail de ses compteurs par unité — combien de cartons de 12, combien de cartons de 6, combien de bidons en vrac, et ainsi de suite, plutôt qu'un total unique. On y retrouve aussi l'historique complet des mouvements de stock par produit — entrées et sorties liées aux achats, ventes (y compris les cassures), retours validés, pertes et reconditionnements — ainsi que les alertes de stock bas, déclenchées selon le seuil défini sur chaque unité de chaque produit.

---

## 7. Historique et suivi

Ce module ne calcule aucun bénéfice, aucune marge, aucune rentabilité — il donne uniquement accès à l'historique complet de l'activité, avec des filtres par période.

**Pour les achats** : on peut consulter, par produit, la liste de tous les achats de ce produit, filtrable par période (jour, semaine, mois, ou dates personnalisées), avec pour chaque ligne la date, le fournisseur, la quantité, l'unité utilisée et le prix, ainsi que le total du montant acheté pour ce produit sur la période. Il existe aussi une vue globale, tous produits confondus, avec le total général.

**Pour les ventes** : même logique, on peut consulter par produit la liste de toutes les ventes, filtrable par période, avec pour chaque ligne la date, le client, la quantité, l'unité, le prix facturé, et si applicable le motif de la remise. Là aussi, il y a un total par produit sur la période et une vue globale avec le total général.

### 7.3 Reconstitution du stock à une date passée

Un point important, adapté au modèle par compteurs : on peut consulter, pour un produit donné, l'état de chacun de ses compteurs de stock (par unité) à une date précise ou à la fin d'une période choisie dans le passé — pas seulement l'état actuel. Cette information est reconstituée à partir de l'historique des mouvements de stock évoqué en section 2, en rejouant les entrées et sorties unité par unité jusqu'à la date demandée (en s'appuyant sur les snapshots mensuels pour rester performant).

On peut aussi consulter un classement des produits les plus vendus, selon la quantité vendue ou le montant total vendu sur une période donnée.

Enfin, ce module donne accès à la situation actuelle des dettes et créances en temps réel — le total des dettes fournisseurs avec détail par fournisseur, et le total des créances clients avec détail par client — ainsi qu'aux mêmes fonctions de recherche et filtrage des factures que celles décrites en section 5.

---

## 8. Gestion des utilisateurs et des rôles

Il existe trois niveaux d'accès :

- **Super administrateur** : a accès à l'ensemble de l'application, sans restriction.
- **Administrateur/propriétaire** : a accès aux produits, fournisseurs, clients, achats, ventes, stock, retours, pertes, reconditionnements, historique/suivi, et à la gestion des utilisateurs.
- **Caissier** : a un accès restreint.

Concrètement, le Caissier peut : effectuer des ventes (y compris des cassures, en indiquant l'unité source), créer des clients, consulter le stock disponible en lecture seule (par unité), imprimer ou envoyer des factures par WhatsApp, et enregistrer les paiements reçus des clients.

En revanche, le Caissier **ne peut pas** : enregistrer un retour client, enregistrer une perte, effectuer un reconditionnement, supprimer un produit, modifier les prix des produits, consulter le module Historique et suivi, créer d'autres utilisateurs, ou supprimer une facture.

Chaque vente enregistre systématiquement l'utilisateur/caissier qui l'a réalisée, ce qui permet une traçabilité complète par caissier.

Enfin, un point essentiel côté développement : l'ensemble de ces permissions doit être contrôlé **côté serveur (backend)**, et non pas simplement en cachant certains éléments dans l'interface utilisateur.