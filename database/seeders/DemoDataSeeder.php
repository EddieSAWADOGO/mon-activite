<?php

namespace Database\Seeders;

use App\Domain\Achats\Services\PurchaseService;
use App\Domain\Clients\Models\Customer;
use App\Domain\Fournisseurs\Models\Supplier;
use App\Domain\Paiements\Services\PaymentService;
use App\Domain\Pertes\Services\LossService;
use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use App\Domain\Reconditionnement\Services\RepackagingService;
use App\Domain\Retours\Services\CustomerReturnService;
use App\Domain\Stock\Services\StockSnapshotService;
use App\Domain\Ventes\Services\SaleService;
use App\Models\User;
use App\Support\Enums\UserRole;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ----------------------------------------------------
        // 1. COMPTES UTILISATEURS (40 UTILISATEURS)
        // ----------------------------------------------------
        $users = [];

        // 3 Comptes Principaux de Rôle
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@mon-activite.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => UserRole::SUPER_ADMIN,
                'is_active' => true,
            ]
        );
        $users[] = $superAdmin;

        $admin = User::firstOrCreate(
            ['email' => 'admin@mon-activite.com'],
            [
                'name' => 'Propriétaire Admin',
                'password' => Hash::make('password'),
                'role' => UserRole::ADMIN,
                'is_active' => true,
            ]
        );
        $users[] = $admin;

        $cashier = User::firstOrCreate(
            ['email' => 'cashier@mon-activite.com'],
            [
                'name' => 'Caissier Principal',
                'password' => Hash::make('password'),
                'role' => UserRole::CASHIER,
                'is_active' => true,
            ]
        );
        $users[] = $cashier;

        // 37 Utilisateurs Supplémentaires (Total = 40)
        $additionalUserNames = [
            'Marc Akpakpo', 'Honorine Agbossou', 'Euloge Tossou', 'Blandine Houndégnon',
            'Charbel Gbaguidi', 'Prudence Hounkpatin', 'Ghislain Kpadonou', 'Yvette Salanon',
            'Gildas Lokossou', 'Reine Soglo', 'Serge Boko', 'Clarisse Kouagou',
            'Florentin Bio', 'Nadège Quenum', 'Thierry Allagbé', 'Odile Fanou',
            'Sylvain Assogba', 'Rosine Kindé', 'Blaise Zannou', 'Justine Houessou',
            'Casimir Dègbo', 'Pelagie Gnanhoui', 'Hippolyte Adomou', 'Solange Kiki',
            'Firmin Hounkpè', 'Viviane Tchabi', 'Martial Sègbé', 'Cosme Ahouandjinou',
            'Colette Amoussou', 'Romuald Kpossou', 'Arlette Djossou', 'Vitalis Gbenou',
            'Esperance Chabi', 'Anatole Kpedetin', 'Faustin Deguenon', 'Bernadette Chode',
            'Raymond Tokpanou'
        ];

        foreach ($additionalUserNames as $idx => $userName) {
            $role = match ($idx % 3) {
                0 => UserRole::ADMIN,
                1 => UserRole::CASHIER,
                default => UserRole::SUPER_ADMIN,
            };

            $email = 'agent' . ($idx + 1) . '@mon-activite.com';
            $u = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $userName,
                    'password' => Hash::make('password'),
                    'role' => $role,
                    'is_active' => true,
                ]
            );
            $users[] = $u;
        }

        // ----------------------------------------------------
        // 2. FOURNISSEURS (40 FOURNISSEURS)
        // ----------------------------------------------------
        $suppliersData = [
            ['name' => 'SODEFA Agro-Chimie', 'city' => 'Cotonou', 'type' => 'company'],
            ['name' => 'Etablissements BioPhyto', 'city' => 'Parakou', 'type' => 'company'],
            ['name' => 'Phyto-Bénin Sarl', 'city' => 'Porto-Novo', 'type' => 'company'],
            ['name' => 'Comptoir Agricole du Bénin (CAB)', 'city' => 'Bohicon', 'type' => 'company'],
            ['name' => 'Bénin Semences & Agro-Services', 'city' => 'Natitingou', 'type' => 'company'],
            ['name' => 'Société Béninoise d\'Intrants Agricoles (SBIA)', 'city' => 'Cotonou', 'type' => 'company'],
            ['name' => 'AgriPharma Cotonou', 'city' => 'Cotonou', 'type' => 'company'],
            ['name' => 'Les Trésors de la Terre', 'city' => 'Parakou', 'type' => 'company'],
            ['name' => 'Groupe Agri-Bénin Import', 'city' => 'Abomey-Calavi', 'type' => 'company'],
            ['name' => 'Fertilisants & Equipements du Nord', 'city' => 'Kandi', 'type' => 'company'],
            ['name' => 'Tropica Phytosanitaire', 'city' => 'Bohicon', 'type' => 'company'],
            ['name' => 'Bénin Engrais SA', 'city' => 'Lokossa', 'type' => 'company'],
            ['name' => 'Agro-Bénin Distribution', 'city' => 'Porto-Novo', 'type' => 'company'],
            ['name' => 'Ets Kpanou & Frères Intrants', 'city' => 'Djougou', 'type' => 'company'],
            ['name' => 'Bio-Protection West Africa', 'city' => 'Ouidah', 'type' => 'company'],
            ['name' => 'Comptoir Phyto-Djougou', 'city' => 'Djougou', 'type' => 'company'],
            ['name' => 'Agro-Pro Natitingou', 'city' => 'Natitingou', 'type' => 'company'],
            ['name' => 'Etablissements Houétondji & Cie', 'city' => 'Abomey', 'type' => 'company'],
            ['name' => 'Agence Centrale d\'Outillage Agricole', 'city' => 'Cotonou', 'type' => 'company'],
            ['name' => 'Bénin Pulvérisateurs & Matériel', 'city' => 'Abomey-Calavi', 'type' => 'company'],
            ['name' => 'Ets Soglo Agro-Services', 'city' => 'Bohicon', 'type' => 'company'],
            ['name' => 'Chabi Phytosanitaire', 'city' => 'Parakou', 'type' => 'individual'],
            ['name' => 'Agri-Chimie du Mono', 'city' => 'Lokossa', 'type' => 'company'],
            ['name' => 'Ets Dossou Intrants', 'city' => 'Cotonou', 'type' => 'company'],
            ['name' => 'Coopérative Centrale des Fournisseurs d\'Intrants', 'city' => 'Allada', 'type' => 'company'],
            ['name' => 'Bénin Bio-Fertilisants', 'city' => 'Abomey-Calavi', 'type' => 'company'],
            ['name' => 'Sodechim Bénin Sarl', 'city' => 'Cotonou', 'type' => 'company'],
            ['name' => 'Ets Bio-Culture Malanville', 'city' => 'Malanville', 'type' => 'company'],
            ['name' => 'Agence Béninoise d\'Agro-Equipements', 'city' => 'Porto-Novo', 'type' => 'company'],
            ['name' => 'Ets Agbossou & Fils', 'city' => 'Savé', 'type' => 'company'],
            ['name' => 'Phyto-Conseil & Vente Allada', 'city' => 'Allada', 'type' => 'company'],
            ['name' => 'Comptoir des Semenciers du Zou', 'city' => 'Bohicon', 'type' => 'company'],
            ['name' => 'Tropica Seeds Bénin', 'city' => 'Parakou', 'type' => 'company'],
            ['name' => 'Ets Zannou Intrants Dassa', 'city' => 'Dassa-Zoumè', 'type' => 'company'],
            ['name' => 'Bénin Protection des Cultures (BPC)', 'city' => 'Cotonou', 'type' => 'company'],
            ['name' => 'Ets Fanou Agro Tech', 'city' => 'Sèmè-Kpodji', 'type' => 'company'],
            ['name' => 'Société Malanville Agro-Négoce', 'city' => 'Malanville', 'type' => 'company'],
            ['name' => 'Agri-Equip Dassa-Zoumè', 'city' => 'Dassa-Zoumè', 'type' => 'company'],
            ['name' => 'Ets Kiki & Associés', 'city' => 'Pobè', 'type' => 'company'],
            ['name' => 'Phytosud Bénin Cotonou', 'city' => 'Cotonou', 'type' => 'company'],
        ];

        $suppliers = [];
        foreach ($suppliersData as $i => $sData) {
            $s = Supplier::firstOrCreate(
                ['name' => $sData['name']],
                [
                    'type' => $sData['type'],
                    'phone' => '+229 97 ' . sprintf('%02d', $i + 10) . ' 00 ' . sprintf('%02d', $i + 10),
                    'whatsapp' => '+229 97 ' . sprintf('%02d', $i + 10) . ' 00 ' . sprintf('%02d', $i + 10),
                    'address' => $sData['city'] . ', Quartier Central',
                    'contact_person' => 'Contact ' . $sData['name'],
                    'email' => 'contact@supplier' . ($i + 1) . '.bj',
                    'notes' => 'Fournisseur agréé d\'intrants et matériel agricole',
                    'is_active' => true,
                ]
            );
            $suppliers[] = $s;
        }

        // ----------------------------------------------------
        // 3. CLIENTS (40 CLIENTS)
        // ----------------------------------------------------
        $customersData = [
            ['name' => 'Coopérative Agricole Gbèkon', 'type' => 'company', 'city' => 'Bohicon'],
            ['name' => 'Ferme Avicole & Maraîchère Avrankou', 'type' => 'company', 'city' => 'Avrankou'],
            ['name' => 'Ets Dossou & Fils (Agronomie)', 'type' => 'company', 'city' => 'Cotonou'],
            ['name' => 'M. Kpanou Jean-Baptiste', 'type' => 'individual', 'city' => 'Abomey-Calavi'],
            ['name' => 'Mme Bio Sanni Chantal', 'type' => 'individual', 'city' => 'Parakou'],
            ['name' => 'Union Régionale des Producteurs du Zou', 'type' => 'company', 'city' => 'Abomey'],
            ['name' => 'Coopérative Maraîchère de Houèto', 'type' => 'company', 'city' => 'Abomey-Calavi'],
            ['name' => 'Ferme Agro-Ecologique de Torri', 'type' => 'company', 'city' => 'Torri-Bossito'],
            ['name' => 'Groupement des Maraîchers de Grand-Popo', 'type' => 'company', 'city' => 'Grand-Popo'],
            ['name' => 'M. Agbossou Philippe', 'type' => 'individual', 'city' => 'Allada'],
            ['name' => 'Ets Kouagou & Frères (Ferme Kandi)', 'type' => 'company', 'city' => 'Kandi'],
            ['name' => 'Coopérative des Riziculteurs de Malanville', 'type' => 'company', 'city' => 'Malanville'],
            ['name' => 'Ferme Avicole Sègbé', 'type' => 'company', 'city' => 'Abomey-Calavi'],
            ['name' => 'M. Allagbé Rodrigue', 'type' => 'individual', 'city' => 'Dassa-Zoumè'],
            ['name' => 'Mme Tossou Honorine', 'type' => 'individual', 'city' => 'Porto-Novo'],
            ['name' => 'Groupement Villageois de Coton (GVP Djidja)', 'type' => 'company', 'city' => 'Djidja'],
            ['name' => 'Ferme Horticole de Ouidah', 'type' => 'company', 'city' => 'Ouidah'],
            ['name' => 'M. Gbaguidi François', 'type' => 'individual', 'city' => 'Savalou'],
            ['name' => 'Coopérative Ananas d\'Allada', 'type' => 'company', 'city' => 'Allada'],
            ['name' => 'M. Hounkpatin Dieudonné', 'type' => 'individual', 'city' => 'Lokossa'],
            ['name' => 'Mme Quenum Béatrice', 'type' => 'individual', 'city' => 'Ouidah'],
            ['name' => 'Ets Chabi Agro-Elevage', 'type' => 'company', 'city' => 'Parakou'],
            ['name' => 'M. Lokossou Modeste', 'type' => 'individual', 'city' => 'Kétou'],
            ['name' => 'Ferme Semencière de Dassa', 'type' => 'company', 'city' => 'Dassa-Zoumè'],
            ['name' => 'Mme Fanou Marguerite', 'type' => 'individual', 'city' => 'Cotonou'],
            ['name' => 'M. Zannou Germain', 'type' => 'individual', 'city' => 'Sakété'],
            ['name' => 'Coopérative des Producteurs de Soja', 'type' => 'company', 'city' => 'Savalou'],
            ['name' => 'M. Assogba Christophe', 'type' => 'individual', 'city' => 'Bohicon'],
            ['name' => 'Mme Kindé Prisca', 'type' => 'individual', 'city' => 'Cotonou'],
            ['name' => 'Ferme Piscicole & Maraîchère de Pahou', 'type' => 'company', 'city' => 'Pahou'],
            ['name' => 'M. Houessou Fernand', 'type' => 'individual', 'city' => 'Pobè'],
            ['name' => 'Union Communale des Maraîchers', 'type' => 'company', 'city' => 'Sèmè-Kpodji'],
            ['name' => 'Mme Gnanhoui Delphine', 'type' => 'individual', 'city' => 'Natitingou'],
            ['name' => 'M. Adomou Bienvenu', 'type' => 'individual', 'city' => 'Djougou'],
            ['name' => 'Ferme Verte de Porto-Novo', 'type' => 'company', 'city' => 'Porto-Novo'],
            ['name' => 'M. Tchabi Ignace', 'type' => 'individual', 'city' => 'Tchaourou'],
            ['name' => 'Mme Kiki Véronique', 'type' => 'individual', 'city' => 'Pobè'],
            ['name' => 'Coopérative Anacarde de Bassila', 'type' => 'company', 'city' => 'Bassila'],
            ['name' => 'M. Ahouandjinou Polycarpe', 'type' => 'individual', 'city' => 'Cotonou'],
            ['name' => 'Ferme Semencière du Borgou', 'type' => 'company', 'city' => 'N\'Dali'],
        ];

        $customers = [];
        foreach ($customersData as $i => $cData) {
            $phone = '+229 95 ' . sprintf('%02d', $i + 10) . ' 11 ' . sprintf('%02d', $i + 10);
            $c = Customer::firstOrCreate(
                ['phone' => $phone],
                [
                    'type' => $cData['type'],
                    'name' => $cData['name'],
                    'contact_person' => $cData['type'] === 'company' ? 'Responsable ' . $cData['name'] : null,
                    'whatsapp' => $phone,
                    'email' => 'client' . ($i + 1) . '@gmail.com',
                    'address' => $cData['city'] . ', Secteur Régal',
                    'ifu' => $cData['type'] === 'company' ? '12020' . sprintf('%08d', $i + 1) : null,
                    'notes' => 'Client régulier de la boutique Mon-Activité',
                    'is_active' => true,
                ]
            );
            $customers[] = $c;
        }

        // ----------------------------------------------------
        // 4. PRODUITS ET UNITÉS DE STOCK (40 PRODUITS, 65+ UNITÉS)
        // ----------------------------------------------------
        $productsCatalog = [
            // Fertilisants & Engrais
            ['name' => 'Engrais NPK 15-15-15', 'desc' => 'Fertilisant complet minéral pour céréales et maraîchage', 'base' => 'Sac 50kg', 'base_price' => 22500, 'multi' => 'Tonne (20 sacs)', 'multi_eq' => 20.0, 'multi_price' => 440000],
            ['name' => 'Urée 46% Azote', 'desc' => 'Engrais azoté hautement concentré pour la croissance végétale', 'base' => 'Sac 50kg', 'base_price' => 21000, 'multi' => 'Tonne (20 sacs)', 'multi_eq' => 20.0, 'multi_price' => 410000],
            ['name' => 'Engrais DAP 18-46-0', 'desc' => 'Phosphate diammonique pour le développement racinaire', 'base' => 'Sac 50kg', 'base_price' => 24000, 'multi' => 'Tonne (20 sacs)', 'multi_eq' => 20.0, 'multi_price' => 470000],
            ['name' => 'Engrais KCL Chlorure de Potassium', 'desc' => 'Potasse pour la qualité et la fructification des récoltes', 'base' => 'Sac 50kg', 'base_price' => 23000, 'multi' => 'Tonne (20 sacs)', 'multi_eq' => 20.0, 'multi_price' => 450000],
            ['name' => 'Compost Organique Enrichi 25kg', 'desc' => 'Amendement humique bio certifié', 'base' => 'Sac 25kg', 'base_price' => 8500, 'multi' => null],
            ['name' => 'Fertilisant Liquide Bio-Boost 1L', 'desc' => 'Engrais foliaire bio stimulant', 'base' => 'Flacon 1L', 'base_price' => 6000, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 68000],

            // Herbicides
            ['name' => 'Herbicide Glyphosate 480 SL', 'desc' => 'Herbicide systémique non sélectif désherbage total', 'base' => 'Flacon 1L', 'base_price' => 4500, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 51000],
            ['name' => 'Herbicide Atrazine 500 SC', 'desc' => 'Herbicide sélectif pré-émergence pour le maïs', 'base' => 'Flacon 1L', 'base_price' => 5000, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 57000],
            ['name' => 'Herbicide 2,4-D Amine 720 SL', 'desc' => 'Herbicide sélectif contre les adventices à feuilles larges', 'base' => 'Flacon 1L', 'base_price' => 4800, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 54000],
            ['name' => 'Herbicide Paraquat 200 SL', 'desc' => 'Herbicide de contact à action rapide', 'base' => 'Flacon 1L', 'base_price' => 4200, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 48000],
            ['name' => 'Herbicide Nicosulfuron 40 SC', 'desc' => 'Herbicide sélectif post-émergence maïs', 'base' => 'Flacon 1L', 'base_price' => 7500, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 85000],

            // Insecticides
            ['name' => 'Insecticide Cyperméthrine 100 EC', 'desc' => 'Insecticide pyréthrinoïde polyvalent', 'base' => 'Flacon 1L', 'base_price' => 6500, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 75000],
            ['name' => 'Insecticide Deltaméthrine 25 EC', 'desc' => 'Insecticide à fort effet choc pour maraîchage', 'base' => 'Flacon 1L', 'base_price' => 7000, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 80000],
            ['name' => 'Insecticide Lambda-Cyhalothrine 50 EC', 'desc' => 'Protection globale contre chenilles et thrips', 'base' => 'Flacon 1L', 'base_price' => 6800, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 78000],
            ['name' => 'Insecticide Chlorpyrifos-Ethyl 480 EC', 'desc' => 'Insecticide du sol et des parties aériennes', 'base' => 'Flacon 1L', 'base_price' => 8000, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 90000],
            ['name' => 'Insecticide Acétamipride 20 SP', 'desc' => 'Insecticide systémique contre pucerons et mouches blanches', 'base' => 'Sachet 250g', 'base_price' => 3200, 'multi' => 'Carton de 20 sachets', 'multi_eq' => 20.0, 'multi_price' => 60000],
            ['name' => 'Insecticide Imidaclopride 200 SL', 'desc' => 'Traitement des semences et foliaire systémique', 'base' => 'Flacon 1L', 'base_price' => 9500, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 108000],
            ['name' => 'Insecticide Bio Neem 1L', 'desc' => 'Extrait d\'huile de neem biologique certifié', 'base' => 'Flacon 1L', 'base_price' => 7800, 'multi' => null],

            // Fongicides
            ['name' => 'Fongicide Mancozèbe 80 WP', 'desc' => 'Fongicide préventif à large spectre', 'base' => 'Sachet 1kg', 'base_price' => 3800, 'multi' => 'Carton de 20kg', 'multi_eq' => 20.0, 'multi_price' => 72000],
            ['name' => 'Fongicide Oxychlorure de Cuivre 50 WP', 'desc' => 'Traitement cuprique préventif contre le mildiou', 'base' => 'Sachet 1kg', 'base_price' => 4200, 'multi' => 'Carton de 20kg', 'multi_eq' => 20.0, 'multi_price' => 80000],
            ['name' => 'Fongicide Azoxystrobine 250 SC', 'desc' => 'Fongicide systémique triazole pour légumes', 'base' => 'Flacon 1L', 'base_price' => 14500, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 165000],
            ['name' => 'Fongicide Métalaxyl + Mancozèbe', 'desc' => 'Fongicide bi-actif contre pourriture des racines', 'base' => 'Sachet 1kg', 'base_price' => 5200, 'multi' => 'Carton de 20kg', 'multi_eq' => 20.0, 'multi_price' => 98000],

            // Semences
            ['name' => 'Semences Maïs Hybride TZEE (Sac 10kg)', 'desc' => 'Maïs extra-précoce 90 jours à haut rendement', 'base' => 'Sac 10kg', 'base_price' => 14000, 'multi' => null],
            ['name' => 'Semences Maïs Blanc Certifié (Sac 10kg)', 'desc' => 'Variété sélectionnée pour la consommation locale', 'base' => 'Sac 10kg', 'base_price' => 12000, 'multi' => null],
            ['name' => 'Semences Riz Isorad (Sac 10kg)', 'desc' => 'Semence de riz de bas-fond certifiée', 'base' => 'Sac 10kg', 'base_price' => 11000, 'multi' => null],
            ['name' => 'Semences Tomate Hybride Cobra 50g', 'desc' => 'Variété tolérante aux maladies et très productive', 'base' => 'Boîte 50g', 'base_price' => 18000, 'multi' => null],
            ['name' => 'Semences Piment Piquant Tropimech 50g', 'desc' => 'Piment habanero fort très recherché', 'base' => 'Boîte 50g', 'base_price' => 16500, 'multi' => null],
            ['name' => 'Semences Gombo Clemson Spineless 100g', 'desc' => 'Gombo vert sans épines très tendre', 'base' => 'Boîte 100g', 'base_price' => 9500, 'multi' => null],
            ['name' => 'Semences Oignon Violet de Galmi 100g', 'desc' => 'Variété idéale pour le stockage longue durée', 'base' => 'Boîte 100g', 'base_price' => 22000, 'multi' => null],
            ['name' => 'Semences Soja TGX (Sac 10kg)', 'desc' => 'Semences de soja certifiées riches en protéines', 'base' => 'Sac 10kg', 'base_price' => 13000, 'multi' => null],

            // Matériel & Équipements
            ['name' => 'Pulvérisateur à Dos 16L Manuel', 'desc' => 'Pulvérisateur haute pression lance inox', 'base' => 'Unité', 'base_price' => 18500, 'multi' => null],
            ['name' => 'Pulvérisateur Électrique à Batterie 20L', 'desc' => 'Autonomie 6h avec régulateur de pression', 'base' => 'Unité', 'base_price' => 38000, 'multi' => null],
            ['name' => 'Bottes de Sécurité Agricoles (Paire)', 'desc' => 'Bottes étanches renforcées PVC', 'base' => 'Paire', 'base_price' => 7500, 'multi' => null],
            ['name' => 'Gants de Protection Chimique (Paire)', 'desc' => 'Gants en nitrile résistant aux solvants', 'base' => 'Paire', 'base_price' => 2500, 'multi' => null],
            ['name' => 'Masque à Cartouche Anti-Vapeurs Phyto', 'desc' => 'Masque panoramique double filtres', 'base' => 'Unité', 'base_price' => 12500, 'multi' => null],
            ['name' => 'Tuyau d\'Arrosage Maraîcher 50m', 'desc' => 'Tuyau armé 3 couches anti-torsion', 'base' => 'Rouleau 50m', 'base_price' => 26000, 'multi' => null],
            ['name' => 'Kit d\'Irrigation Goutte-à-Goutte 100m', 'desc' => 'Système complet avec goutteurs autorégulants', 'base' => 'Kit 100m', 'base_price' => 45000, 'multi' => null],
            ['name' => 'Bâche de Séchage et Protection 6x4m', 'desc' => 'Bâche tressée étanche polyéthylène', 'base' => 'Unité', 'base_price' => 15000, 'multi' => null],
            ['name' => 'Sécateur de Taille Professionnel', 'desc' => 'Lame en acier forgé trempé', 'base' => 'Unité', 'base_price' => 6500, 'multi' => null],
            ['name' => 'Arrosoir Plastique Renforcé 12L', 'desc' => 'Arrosoir avec pomme laiton amovible', 'base' => 'Unité', 'base_price' => 4500, 'multi' => null],
        ];

        $products = [];
        $baseUnits = [];
        $multiUnits = [];
        $allUnits = [];

        foreach ($productsCatalog as $pItem) {
            $product = Product::firstOrCreate(
                ['name' => $pItem['name']],
                [
                    'description' => $pItem['desc'],
                    'is_active' => true,
                ]
            );
            $products[] = $product;

            $bUnit = StockUnit::firstOrCreate(
                ['product_id' => $product->id, 'name' => $pItem['base']],
                [
                    'is_base_unit' => true,
                    'base_unit_equivalent' => 1.0,
                    'default_selling_price' => $pItem['base_price'],
                    'low_stock_threshold' => 10,
                    'current_stock' => 0,
                    'is_active' => true,
                ]
            );
            $baseUnits[$product->id] = $bUnit;
            $allUnits[] = $bUnit;

            if (!empty($pItem['multi'])) {
                $mUnit = StockUnit::firstOrCreate(
                    ['product_id' => $product->id, 'name' => $pItem['multi']],
                    [
                        'is_base_unit' => false,
                        'base_unit_equivalent' => $pItem['multi_eq'],
                        'default_selling_price' => $pItem['multi_price'],
                        'low_stock_threshold' => 2,
                        'current_stock' => 0,
                        'is_active' => true,
                    ]
                );
                $multiUnits[$product->id] = $mUnit;
                $allUnits[] = $mUnit;
            }
        }

        // Services
        $purchaseService = app(PurchaseService::class);
        $repackagingService = app(RepackagingService::class);
        $saleService = app(SaleService::class);
        $paymentService = app(PaymentService::class);
        $customerReturnService = app(CustomerReturnService::class);
        $lossService = app(LossService::class);
        $snapshotService = app(StockSnapshotService::class);

        // ----------------------------------------------------
        // 5. ACHATS DE STOCK (45 PURCHASES, 90+ LINES)
        // ----------------------------------------------------
        for ($i = 1; $i <= 45; $i++) {
            $supplier = $suppliers[($i - 1) % count($suppliers)];
            $user = $users[$i % count($users)];
            $daysAgo = 60 - $i; // Étalé sur les 2 derniers mois
            $pDate = Carbon::now()->subDays(max(1, $daysAgo))->toDateString();

            // Choisir 2 à 3 produits distincts
            $prodIndexes = [($i * 2) % count($products), ($i * 2 + 1) % count($products)];
            $lines = [];

            foreach ($prodIndexes as $pIdx) {
                $p = $products[$pIdx];
                // Préférez l'unité multi si elle existe, sinon base
                $u = $multiUnits[$p->id] ?? $baseUnits[$p->id];
                $qty = rand(50, 200); // Quantité généreuse pour approvisionner le stock
                $unitCost = (int) round($u->default_selling_price * 0.82); // Prix d'achat à -18% du prix de vente

                $lines[] = [
                    'product_id' => $p->id,
                    'stock_unit_id' => $u->id,
                    'quantity' => $qty,
                    'unit_price' => $unitCost,
                ];
            }

            // Calculer montant total et simuler paiement (30% à crédit partiel)
            $approxTotal = array_reduce($lines, fn($acc, $l) => $acc + ($l['quantity'] * $l['unit_price']), 0);
            $paid = ($i % 3 === 0) ? (int) round($approxTotal * 0.6) : $approxTotal;

            $purchaseService->registerPurchase([
                'supplier_id' => $supplier->id,
                'purchase_date' => $pDate,
                'paid_amount' => $paid,
                'notes' => "Bon d'approvisionnement N° ACH-DEMO-{$i}",
                'lines' => $lines,
            ], $user->id);
        }

        // ----------------------------------------------------
        // 6. RECONDITIONNEMENT (40 REPACKAGINGS)
        // ----------------------------------------------------
        // Récupérer les produits qui ont une unité multi
        $multiProdIds = array_keys($multiUnits);
        for ($i = 1; $i <= 40; $i++) {
            $pId = $multiProdIds[($i - 1) % count($multiProdIds)];
            $mUnit = $multiUnits[$pId];
            $bUnit = $baseUnits[$pId];
            $user = $users[($i + 2) % count($users)];
            $daysAgo = 50 - $i;

            $srcQty = rand(1, 3);
            $targetQty = $srcQty * (float) $mUnit->base_unit_equivalent;

            $repackagingService->executeRepackaging([
                'product_id' => $pId,
                'source_stock_unit_id' => $mUnit->id,
                'source_quantity' => $srcQty,
                'target_stock_unit_id' => $bUnit->id,
                'target_quantity' => $targetQty,
                'repackaging_date' => Carbon::now()->subDays(max(1, $daysAgo)),
                'notes' => "Reconditionnement de routine N° {$i} : conversion de {$mUnit->name} en {$bUnit->name}",
            ], $user);
        }

        // ----------------------------------------------------
        // 7. VENTES & FACTURES (50 SALES, 100+ LINES, 50 INVOICES)
        // ----------------------------------------------------
        $sales = [];
        for ($i = 1; $i <= 50; $i++) {
            $customer = ($i % 5 === 0) ? null : $customers[($i - 1) % count($customers)];
            $user = $users[($i + 1) % count($users)];
            $daysAgo = 45 - $i;
            $sDate = Carbon::now()->subDays(max(0, $daysAgo));

            // Sélectionner 2 lignes de vente
            $pIdx1 = ($i * 3) % count($products);
            $pIdx2 = ($i * 3 + 1) % count($products);

            $p1 = $products[$pIdx1];
            $u1 = $baseUnits[$p1->id];

            $p2 = $products[$pIdx2];
            $u2 = $baseUnits[$p2->id];

            $lines = [
                [
                    'product_id' => $p1->id,
                    'stock_unit_id' => $u1->id,
                    'quantity' => rand(1, 10),
                    'unit_price' => $u1->default_selling_price,
                ],
                [
                    'product_id' => $p2->id,
                    'stock_unit_id' => $u2->id,
                    'quantity' => rand(1, 5),
                    'unit_price' => $u2->default_selling_price,
                ],
            ];

            $totalAmt = array_reduce($lines, fn($acc, $l) => $acc + ($l['quantity'] * $l['unit_price']), 0);

            // Simuler des états de paiement (comptant, partiel, ou crédit)
            $paidAmt = match ($i % 4) {
                0 => 0, // Unpaid
                1 => (int) round($totalAmt * 0.5), // Partially Paid
                default => $totalAmt, // Paid
            };

            $sale = $saleService->createSale([
                'customer_id' => $customer?->id,
                'sale_date' => $sDate,
                'paid_amount' => $paidAmt,
                'notes' => "Vente comptoir / commande client N° {$i}",
                'lines' => $lines,
            ], $user);

            $sales[] = $sale;
        }

        // ----------------------------------------------------
        // 8. PAIEMENTS DE FACTURES / CRÉANCES (45 PAYMENTS)
        // ----------------------------------------------------
        // Chercher les factures créées qui ne sont pas totalement payées
        $invoicesToPay = \App\Domain\Facturation\Models\Invoice::where('remaining_amount', '>', 0)->get();
        $paymentCount = 0;

        foreach ($invoicesToPay as $inv) {
            if ($paymentCount >= 45) {
                break;
            }

            $payAmt = (int) min((float) $inv->remaining_amount, rand(5000, 50000));
            if ($payAmt <= 0) continue;

            $user = $users[$paymentCount % count($users)];
            $method = match ($paymentCount % 3) {
                0 => 'Espèces',
                1 => 'Mobile Money',
                default => 'Virement bancaire',
            };

            $paymentService->recordPayment([
                'invoice_id' => $inv->id,
                'amount' => $payAmt,
                'payment_date' => Carbon::parse($inv->invoice_date)->addDays(1),
                'payment_method' => $method,
                'reference' => 'PAY-REF-' . sprintf('%04d', $paymentCount + 1),
                'notes' => "Encaissement partiel / solde créance",
            ], $user);

            $paymentCount++;
        }

        // Compléter si le nombre de paiements est inférieur à 45
        while ($paymentCount < 45) {
            $inv = \App\Domain\Facturation\Models\Invoice::inRandomOrder()->first();
            $user = $users[$paymentCount % count($users)];
            $payAmt = min((float) $inv->total_amount, 10000);

            // Petit paiement partiel si zéro restant
            if ($inv->remaining_amount > 0) {
                $payAmt = min((float) $inv->remaining_amount, 15000);
                $paymentService->recordPayment([
                    'invoice_id' => $inv->id,
                    'amount' => $payAmt,
                    'payment_date' => Carbon::now()->subDays(rand(1, 10)),
                    'payment_method' => 'Espèces',
                    'notes' => 'Règlement régularisation',
                ], $user);
            }
            $paymentCount++;
        }

        // ----------------------------------------------------
        // 9. RETOURS CLIENTS (40 CUSTOMER RETURNS)
        // ----------------------------------------------------
        for ($i = 1; $i <= 40; $i++) {
            $customer = $customers[($i - 1) % count($customers)];
            $p = $products[($i * 2) % count($products)];
            $u = $baseUnits[$p->id];
            $user = $users[($i + 3) % count($users)];

            $cr = $customerReturnService->recordReturn([
                'customer_id' => $customer->id,
                'product_id' => $p->id,
                'stock_unit_id' => $u->id,
                'quantity' => rand(1, 3),
                'reason' => match ($i % 3) {
                    0 => 'Sachet percé lors du déchargement',
                    1 => 'Erreur de référence commandée',
                    default => 'Surplus de chantier rapporté intact',
                },
                'return_date' => Carbon::now()->subDays(max(1, 30 - $i)),
                'notes' => "Retour client enregistré N° {$i}",
            ], $user);

            // Traiter le statut du retour
            if ($i <= 20) {
                // Restocké
                $customerReturnService->validateAndRestock($cr, $admin);
            } elseif ($i <= 35) {
                // Mis au rebut / Perte
                $customerReturnService->validateAndDiscard($cr, $admin, "Déclaré non réutilisable lors du contrôle retour N° {$i}");
            }
            // Les 5 derniers restent en statut 'pending'
        }

        // ----------------------------------------------------
        // 10. PERTES EN MAGASIN (40 LOSSES)
        // ----------------------------------------------------
        for ($i = 1; $i <= 40; $i++) {
            $p = $products[($i * 3) % count($products)];
            $u = $baseUnits[$p->id];
            $user = $users[$i % count($users)];

            $lossService->recordLoss([
                'product_id' => $p->id,
                'stock_unit_id' => $u->id,
                'quantity' => rand(1, 2),
                'reason' => match ($i % 4) {
                    0 => 'Sac percé / Infiltration humidité sol',
                    1 => 'Flacon fissuré pendant manutention',
                    2 => 'Date de péremption dépassée',
                    default => 'Avarie constatée lors de l\'inventaire',
                },
                'loss_date' => Carbon::now()->subDays(max(1, 40 - $i)),
                'notes' => "Perte magasin déclarée N° {$i}",
            ], $user);
        }

        // ----------------------------------------------------
        // 11. CLICHÉS DE STOCK (3 MOIS X 65+ UNITÉS DE STOCK = 180+ SNAPSHOTS)
        // ----------------------------------------------------
        $currYear = (int) date('Y');
        $currMonth = (int) date('m');

        // Générer pour les 3 derniers mois
        for ($mOffset = 0; $mOffset < 3; $mOffset++) {
            $dt = Carbon::now()->subMonths($mOffset);
            $snapshotService->generateSnapshotForMonth((int) $dt->year, (int) $dt->month);
        }
    }
}
