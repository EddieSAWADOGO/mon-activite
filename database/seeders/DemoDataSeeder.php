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
     * Run the database seeds for Burkina Faso context (100+ items per entity).
     */
    public function run(): void
    {
        // ====================================================
        // 1. COMPTES UTILISATEURS (100 UTILISATEURS)
        // ====================================================
        $users = [];

        // 3 Comptes Principaux de Rôle
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@suivremoncommerce.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => UserRole::SUPER_ADMIN,
                'is_active' => true,
            ]
        );
        $users[] = $superAdmin;

        $admin = User::firstOrCreate(
            ['email' => 'admin@suivremoncommerce.com'],
            [
                'name' => 'Propriétaire Admin',
                'password' => Hash::make('password'),
                'role' => UserRole::ADMIN,
                'is_active' => true,
            ]
        );
        $users[] = $admin;

        $cashier = User::firstOrCreate(
            ['email' => 'cashier@suivremoncommerce.com'],
            [
                'name' => 'Caissier Principal',
                'password' => Hash::make('password'),
                'role' => UserRole::CASHIER,
                'is_active' => true,
            ]
        );
        $users[] = $cashier;

        // 97 Utilisateurs Burkinabè Supplémentaires (Total = 100)
        $additionalUserNames = [
            'Adama SAWADOGO', 'Chantal OUEDRAOGO', 'Jean-Baptiste KABORE', 'Honorine COMPAORE',
            'Euloge ZONGO', 'Blandine SANOU', 'Charbel BADO', 'Prudence TAPSOBA',
            'Ghislain NIKIEMA', 'Yvette TRAORE', 'Gildas COULIBALY', 'Reine GUIGMA',
            'Serge ILBOUDO', 'Clarisse KINDA', 'Florentin SINKA', 'Nadège GANSORE',
            'Thierry SORE', 'Odile KERE', 'Sylvain KABRE', 'Rosine TONDEDJI',
            'Blaise BARRO', 'Justine SANKARA', 'Casimir KINDO', 'Pelagie DIARRA',
            'Hippolyte DEMBELE', 'Solange KONATE', 'Firmin KONE', 'Viviane OUATTARA',
            'Martial YAMEOGO', 'Cosme NITIEMA', 'Colette ZOUGRANA', 'Romuald TANKOANO',
            'Arlette LANKOANDE', 'Vitalis NATAMA', 'Esperance COMBARY', 'Anatole DIESSONGO',
            'Faustin THIOMBIANO', 'Bernadette DIONGUE', 'Raymond BAZIE', 'Antoinette BADOLO',
            'Bernard ZOMA', 'Célestin NEYA', 'Denise BATIONO', 'Édouard BIZI',
            'Félicité SEYDOU', 'Gabriel DICKO', 'Hortense DIALLO', 'Ignace BARRY',
            'Jacqueline TALL', 'Kévin SOW', 'Léonie CISSE', 'Marcel BAMBARA',
            'Nicole KABORE', 'Olivier SAWADOGO', 'Pascaline COMPAORE', 'Quentin ZONGO',
            'Rachel OUEDRAOGO', 'Samuel BADO', 'Thérèse SANOU', 'Urbain TAPSOBA',
            'Victorine NIKIEMA', 'Wilfred TRAORE', 'Xavier COULIBALY', 'Yvette GUIGMA',
            'Zacharie ILBOUDO', 'Alice KINDA', 'Barthélémy SINKA', 'Caroline GANSORE',
            'David SORE', 'Émilie KERE', 'Ferdinand KABRE', 'Geneviève BARRO',
            'Henri SANKARA', 'Isabelle KINDO', 'Joseph DIARRA', 'Karen DEMBELE',
            'Louis KONATE', 'Madeleine KONE', 'Norbert OUATTARA', 'Onésime YAMEOGO',
            'Patricia NITIEMA', 'Rodolphe ZOUGRANA', 'Sabine TANKOANO', 'Thomas LANKOANDE',
            'Ursule NATAMA', 'Valentin COMBARY', 'Wilfried DIESSONGO', 'Xavier THIOMBIANO',
            'Yvonne DIONGUE', 'Zachary BAZIE', 'Abel BADOLO', 'Béatrice ZOMA',
            'Charles NEYA', 'Delphine BATIONO', 'Émile BIZI', 'Francine SEYDOU', 'Gérard DICKO'
        ];

        foreach ($additionalUserNames as $idx => $userName) {
            $role = match ($idx % 3) {
                0 => UserRole::ADMIN,
                1 => UserRole::CASHIER,
                default => UserRole::SUPER_ADMIN,
            };

            $email = 'agent' . ($idx + 1) . '@suivremoncommerce.com';
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

        // ====================================================
        // 2. FOURNISSEURS (100 FOURNISSEURS BURKINABÈ)
        // ====================================================
        $suppliersData = [
            ['name' => 'SOFITEX Intrants', 'city' => 'Bobo-Dioulasso', 'type' => 'company'],
            ['name' => 'Tropica Agro-Burkina', 'city' => 'Ouagadougou', 'type' => 'company'],
            ['name' => 'Ets SAWADOGO & Frères Intrants', 'city' => 'Ouagadougou', 'type' => 'company'],
            ['name' => 'Comptoir Agricole du Faso (CAF)', 'city' => 'Bobo-Dioulasso', 'type' => 'company'],
            ['name' => 'Burkina Phyto-Services', 'city' => 'Koudougou', 'type' => 'company'],
            ['name' => 'Société Burkinabè d\'Intrants Agricoles (SBIA)', 'city' => 'Ouagadougou', 'type' => 'company'],
            ['name' => 'Agropharma Faso', 'city' => 'Bobo-Dioulasso', 'type' => 'company'],
            ['name' => 'Les Semences du Faso', 'city' => 'Ouahigouya', 'type' => 'company'],
            ['name' => 'Groupe KABORE Agro-Import', 'city' => 'Ouagadougou', 'type' => 'company'],
            ['name' => 'Fertilisants du Soum & Centre', 'city' => 'Kaya', 'type' => 'company'],
            ['name' => 'Tropica Seeds Faso', 'city' => 'Tenkodogo', 'type' => 'company'],
            ['name' => 'Faso Engrais SA', 'city' => 'Banfora', 'type' => 'company'],
            ['name' => 'Agro-Faso Distribution', 'city' => 'Dédougou', 'type' => 'company'],
            ['name' => 'Ets OUEDRAOGO Intrants & Matériel', 'city' => 'Houndé', 'type' => 'company'],
            ['name' => 'Bio-Protection Faso', 'city' => 'Ouagadougou', 'type' => 'company'],
            ['name' => 'Comptoir Phyto-Koudougou', 'city' => 'Koudougou', 'type' => 'company'],
            ['name' => 'Agro-Pro Fada', 'city' => 'Fada N\'Gourma', 'type' => 'company'],
            ['name' => 'Ets ZONGO & Cie Agro', 'city' => 'Reo', 'type' => 'company'],
            ['name' => 'Agence Centrale d\'Outillage du Faso', 'city' => 'Ouagadougou', 'type' => 'company'],
            ['name' => 'Faso Pulvérisateurs & Equipements', 'city' => 'Bobo-Dioulasso', 'type' => 'company'],
            ['name' => 'Ets COMPAORE Agro-Services', 'city' => 'Ouagadougou', 'type' => 'company'],
            ['name' => 'TRAORE Phytosanitaire', 'city' => 'Bobo-Dioulasso', 'type' => 'individual'],
            ['name' => 'Agri-Chimie des Hauts-Bassins', 'city' => 'Bobo-Dioulasso', 'type' => 'company'],
            ['name' => 'Ets SANOU Intrants', 'city' => 'Bobo-Dioulasso', 'type' => 'company'],
            ['name' => 'Coopérative des Intrants du Mouhoun', 'city' => 'Dédougou', 'type' => 'company'],
            ['name' => 'Burkina Bio-Fertilisants', 'city' => 'Ouagadougou', 'type' => 'company'],
            ['name' => 'Sodechim Faso Sarl', 'city' => 'Ouagadougou', 'type' => 'company'],
            ['name' => 'Ets BADO Bio-Culture', 'city' => 'Koudougou', 'type' => 'company'],
            ['name' => 'Agence Burkinabè d\'Agro-Equipements', 'city' => 'Ouagadougou', 'type' => 'company'],
            ['name' => 'Ets TAPSOBA & Fils', 'city' => 'Tenkodogo', 'type' => 'company'],
            ['name' => 'Phyto-Conseil & Vente Koudougou', 'city' => 'Koudougou', 'type' => 'company'],
            ['name' => 'Comptoir des Semenciers du Yatenga', 'city' => 'Ouahigouya', 'type' => 'company'],
            ['name' => 'Tropica Seeds Yatenga', 'city' => 'Ouahigouya', 'type' => 'company'],
            ['name' => 'Ets BARRO Intrants Banfora', 'city' => 'Banfora', 'type' => 'company'],
            ['name' => 'Faso Protection des Cultures', 'city' => 'Ouagadougou', 'type' => 'company'],
            ['name' => 'Ets GUIGMA Agro Tech', 'city' => 'Ziniaré', 'type' => 'company'],
            ['name' => 'Société Agro-Négoce du Sourou', 'city' => 'Tougan', 'type' => 'company'],
            ['name' => 'Agri-Equip Houndé', 'city' => 'Houndé', 'type' => 'company'],
            ['name' => 'Ets NIKIEMA & Associés', 'city' => 'Ouagadougou', 'type' => 'company'],
            ['name' => 'Phytosud Faso', 'city' => 'Bobo-Dioulasso', 'type' => 'company'],
            ['name' => 'Ets SANKARA & Cie Intrants', 'city' => 'Pouytenga', 'type' => 'company'],
            ['name' => 'Comptoir Phytosanitaire de Pouytenga', 'city' => 'Pouytenga', 'type' => 'company'],
            ['name' => 'Agro-Négoce du Nahouri', 'city' => 'Pô', 'type' => 'company'],
            ['name' => 'Ets KINDO Agro-Distribution', 'city' => 'Ouahigouya', 'type' => 'company'],
            ['name' => 'Coopérative des Intrants du Centre-Sud', 'city' => 'Manga', 'type' => 'company'],
            ['name' => 'Burkina Matériel d\'Irrigation', 'city' => 'Ouagadougou', 'type' => 'company'],
            ['name' => 'Ets DIARRA & Frères Agro', 'city' => 'Bobo-Dioulasso', 'type' => 'company'],
            ['name' => 'Phyto-Faso Kadiogo', 'city' => 'Ouagadougou', 'type' => 'company'],
            ['name' => 'Ets DEMBELE Négoce', 'city' => 'Banfora', 'type' => 'company'],
            ['name' => 'Comptoir Agricole du Passoré', 'city' => 'Yako', 'type' => 'company'],
            ['name' => 'Ets KONATE Intrants & Semences', 'city' => 'Orodara', 'type' => 'company'],
            ['name' => 'Faso Bio-Pest Control', 'city' => 'Ouagadougou', 'type' => 'company'],
            ['name' => 'Ets KONE Phyto-Services', 'city' => 'Bobo-Dioulasso', 'type' => 'company'],
            ['name' => 'Société d\'Équipements Agricoles du Gulmu', 'city' => 'Fada N\'Gourma', 'type' => 'company'],
            ['name' => 'Ets OUATTARA Agro', 'city' => 'Banfora', 'type' => 'company'],
            ['name' => 'Comptoir des Intrants du Bam', 'city' => 'Kongoussi', 'type' => 'company'],
            ['name' => 'Ets YAMEOGO & Fils', 'city' => 'Koudougou', 'type' => 'company'],
            ['name' => 'Burkina Agro-Technologie', 'city' => 'Ouagadougou', 'type' => 'company'],
            ['name' => 'Ets NITIEMA Intrants', 'city' => 'Ziniaré', 'type' => 'company'],
            ['name' => 'Phyto-Centre Tenkodogo', 'city' => 'Tenkodogo', 'type' => 'company'],
            ['name' => 'Ets ZOUGRANA Agro-Services', 'city' => 'Koupéla', 'type' => 'company'],
            ['name' => 'Comptoir Agricole du Sanguié', 'city' => 'Reo', 'type' => 'company'],
            ['name' => 'Ets TANKOANO Intrants', 'city' => 'Fada N\'Gourma', 'type' => 'company'],
            ['name' => 'Faso Semences & Fertilisants', 'city' => 'Bobo-Dioulasso', 'type' => 'company'],
            ['name' => 'Ets LANKOANDE & Cie', 'city' => 'Koupéla', 'type' => 'company'],
            ['name' => 'Agence Phyto-Dédougou', 'city' => 'Dédougou', 'type' => 'company'],
            ['name' => 'Ets NATAMA Agro', 'city' => 'Fada N\'Gourma', 'type' => 'company'],
            ['name' => 'Burkina Irrigation & Motopompes', 'city' => 'Ouagadougou', 'type' => 'company'],
            ['name' => 'Ets COMBARY Négoce', 'city' => 'Pama', 'type' => 'company'],
            ['name' => 'Comptoir Agricole de la Comoé', 'city' => 'Banfora', 'type' => 'company'],
            ['name' => 'Ets DIESSONGO & Frères', 'city' => 'Tenkodogo', 'type' => 'company'],
            ['name' => 'Agro-Pro Yatenga', 'city' => 'Ouahigouya', 'type' => 'company'],
            ['name' => 'Ets THIOMBIANO Intrants', 'city' => 'Fada N\'Gourma', 'type' => 'company'],
            ['name' => 'Phyto-Faso Houet', 'city' => 'Bobo-Dioulasso', 'type' => 'company'],
            ['name' => 'Ets DIONGUE Agro', 'city' => 'Ouagadougou', 'type' => 'company'],
            ['name' => 'Comptoir des Semenciers du Boulkiemdé', 'city' => 'Koudougou', 'type' => 'company'],
            ['name' => 'Ets BAZIE & Fils Intrants', 'city' => 'Reo', 'type' => 'company'],
            ['name' => 'Faso Agro-Matériel', 'city' => 'Ouagadougou', 'type' => 'company'],
            ['name' => 'Ets BADOLO Phyto', 'city' => 'Koudougou', 'type' => 'company'],
            ['name' => 'Agence Agricole du Ioba', 'city' => 'Gaoua', 'type' => 'company'],
            ['name' => 'Ets ZOMA Agro-Services', 'city' => 'Koudougou', 'type' => 'company'],
            ['name' => 'Comptoir Phyto-Banfora', 'city' => 'Banfora', 'type' => 'company'],
            ['name' => 'Ets NEYA & Frères', 'city' => 'Leo', 'type' => 'company'],
            ['name' => 'Faso Outillage Agricole', 'city' => 'Bobo-Dioulasso', 'type' => 'company'],
            ['name' => 'Ets BATIONO Intrants', 'city' => 'Reo', 'type' => 'company'],
            ['name' => 'Agro-Négoce du Sissili', 'city' => 'Leo', 'type' => 'company'],
            ['name' => 'Ets BIZI Agro', 'city' => 'Manga', 'type' => 'company'],
            ['name' => 'Comptoir Agricole de l\'Oubritenga', 'city' => 'Ziniaré', 'type' => 'company'],
            ['name' => 'Ets SEYDOU & Cie', 'city' => 'Dori', 'type' => 'company'],
            ['name' => 'Phyto-Nord Dori', 'city' => 'Dori', 'type' => 'company'],
            ['name' => 'Ets DICKO Agro', 'city' => 'Djibo', 'type' => 'company'],
            ['name' => 'Faso Semences Certifiées', 'city' => 'Ouagadougou', 'type' => 'company'],
            ['name' => 'Ets DIALLO Intrants', 'city' => 'Dori', 'type' => 'company'],
            ['name' => 'Agence Agricole du Soum', 'city' => 'Djibo', 'type' => 'company'],
            ['name' => 'Ets BARRY & Frères', 'city' => 'Dori', 'type' => 'company'],
            ['name' => 'Comptoir Phyto-Kaya', 'city' => 'Kaya', 'type' => 'company'],
            ['name' => 'Ets TALL Négoce', 'city' => 'Kaya', 'type' => 'company'],
            ['name' => 'Agro-Faso Sanmatenga', 'city' => 'Kaya', 'type' => 'company'],
            ['name' => 'Ets SOW Intrants', 'city' => 'Ouahigouya', 'type' => 'company'],
            ['name' => 'Ets CISSE Agro-Services', 'city' => 'Ouagadougou', 'type' => 'company'],
        ];

        $suppliers = [];
        foreach ($suppliersData as $i => $sData) {
            $phonePrefixes = ['70', '76', '60', '78', '65', '55'];
            $pref = $phonePrefixes[$i % count($phonePrefixes)];
            $phone = "+226 {$pref} " . sprintf('%02d', ($i % 90) + 10) . ' ' . sprintf('%02d', ($i * 3) % 90 + 10) . ' ' . sprintf('%02d', ($i * 7) % 90 + 10);

            $s = Supplier::firstOrCreate(
                ['name' => $sData['name']],
                [
                    'type' => $sData['type'],
                    'phone' => $phone,
                    'whatsapp' => $phone,
                    'address' => $sData['city'] . ', Secteur Central',
                    'contact_person' => 'Contact ' . $sData['name'],
                    'email' => 'contact@fournisseur' . ($i + 1) . '.bf',
                    'notes' => 'Fournisseur agréé d\'intrants et matériel agricole au Burkina Faso',
                    'is_active' => true,
                ]
            );
            $suppliers[] = $s;
        }

        // ====================================================
        // 3. CLIENTS (100 CLIENTS BURKINABÈ)
        // ====================================================
        $customersData = [
            ['name' => 'Coopérative Agricole Relwendé', 'type' => 'company', 'city' => 'Ouagadougou'],
            ['name' => 'Ferme Avicole & Maraîchère Kadiogo', 'type' => 'company', 'city' => 'Ouagadougou'],
            ['name' => 'Ets SAWADOGO & Fils (Agronomie)', 'type' => 'company', 'city' => 'Bobo-Dioulasso'],
            ['name' => 'M. KABORE Jean-Baptiste', 'type' => 'individual', 'city' => 'Ouagadougou'],
            ['name' => 'Mme OUEDRAOGO Chantal', 'type' => 'individual', 'city' => 'Bobo-Dioulasso'],
            ['name' => 'Union Régionale des Producteurs du Mouhoun', 'type' => 'company', 'city' => 'Dédougou'],
            ['name' => 'Coopérative Maraîchère de Loumbila', 'type' => 'company', 'city' => 'Loumbila'],
            ['name' => 'Ferme Agro-Écologique de Koubri', 'type' => 'company', 'city' => 'Koubri'],
            ['name' => 'Groupement des Maraîchers de Bama', 'type' => 'company', 'city' => 'Bama'],
            ['name' => 'M. ZONGO Philippe', 'type' => 'individual', 'city' => 'Koudougou'],
            ['name' => 'Ets COMPAORE & Frères (Ferme Houndé)', 'type' => 'company', 'city' => 'Houndé'],
            ['name' => 'Coopérative des Riziculteurs de Bagré', 'type' => 'company', 'city' => 'Bagré'],
            ['name' => 'Ferme Avicole Wendkouni', 'type' => 'company', 'city' => 'Koudougou'],
            ['name' => 'M. SANOU Rodrigue', 'type' => 'individual', 'city' => 'Bobo-Dioulasso'],
            ['name' => 'Mme SAWADOGO Honorine', 'type' => 'individual', 'city' => 'Ouahigouya'],
            ['name' => 'Groupement Villageois de Coton (GVC Houndé)', 'type' => 'company', 'city' => 'Houndé'],
            ['name' => 'Ferme Horticole de Bazoulé', 'type' => 'company', 'city' => 'Bazoulé'],
            ['name' => 'M. BADO François', 'type' => 'individual', 'city' => 'Reo'],
            ['name' => 'Coopérative Mangue de Banfora', 'type' => 'company', 'city' => 'Banfora'],
            ['name' => 'M. TAPSOBA Dieudonné', 'type' => 'individual', 'city' => 'Tenkodogo'],
            ['name' => 'Mme NIKIEMA Béatrice', 'type' => 'individual', 'city' => 'Ouagadougou'],
            ['name' => 'Ets TRAORE Agro-Élevage', 'type' => 'company', 'city' => 'Bobo-Dioulasso'],
            ['name' => 'M. COULIBALY Modeste', 'type' => 'individual', 'city' => 'Banfora'],
            ['name' => 'Ferme Semencière du Passoré', 'type' => 'company', 'city' => 'Yako'],
            ['name' => 'Mme BARRO Marguerite', 'type' => 'individual', 'city' => 'Bobo-Dioulasso'],
            ['name' => 'M. GUIGMA Germain', 'type' => 'individual', 'city' => 'Kaya'],
            ['name' => 'Coopérative des Producteurs de Sésame', 'type' => 'company', 'city' => 'Dédougou'],
            ['name' => 'M. KINDA Christophe', 'type' => 'individual', 'city' => 'Ouagadougou'],
            ['name' => 'Mme SINKA Prisca', 'type' => 'individual', 'city' => 'Tenkodogo'],
            ['name' => 'Ferme Piscicole & Maraîchère de Ziniaré', 'type' => 'company', 'city' => 'Ziniaré'],
            ['name' => 'M. ILBOUDO Fernand', 'type' => 'individual', 'city' => 'Ouagadougou'],
            ['name' => 'Union Communale des Maraîchers de Tanghin', 'type' => 'company', 'city' => 'Ouagadougou'],
            ['name' => 'Mme SORE Delphine', 'type' => 'individual', 'city' => 'Ouahigouya'],
            ['name' => 'M. GANSORE Bienvenu', 'type' => 'individual', 'city' => 'Kaya'],
            ['name' => 'Ferme Verte de Koudougou', 'type' => 'company', 'city' => 'Koudougou'],
            ['name' => 'M. KERE Ignace', 'type' => 'individual', 'city' => 'Tenkodogo'],
            ['name' => 'Mme KABRE Véronique', 'type' => 'individual', 'city' => 'Manga'],
            ['name' => 'Coopérative Anacarde de Gaoua', 'type' => 'company', 'city' => 'Gaoua'],
            ['name' => 'M. OUEDRAOGO Polycarpe', 'type' => 'individual', 'city' => 'Ouagadougou'],
            ['name' => 'Ferme Semencière des Hauts-Bassins', 'type' => 'company', 'city' => 'Bobo-Dioulasso'],
            ['name' => 'Union des Riziculteurs de Banzon', 'type' => 'company', 'city' => 'Banzon'],
            ['name' => 'Groupement Maraîcher de Niangoloko', 'type' => 'company', 'city' => 'Niangoloko'],
            ['name' => 'M. SANKARA Paul', 'type' => 'individual', 'city' => 'Pouytenga'],
            ['name' => 'Ferme Avicole de Koupéla', 'type' => 'company', 'city' => 'Koupéla'],
            ['name' => 'Mme KINDO Salimata', 'type' => 'individual', 'city' => 'Ouahigouya'],
            ['name' => 'Coopérative Maraîchère de Pô', 'type' => 'company', 'city' => 'Pô'],
            ['name' => 'M. DIARRA Ousmane', 'type' => 'individual', 'city' => 'Bobo-Dioulasso'],
            ['name' => 'Ferme Écologique de Garango', 'type' => 'company', 'city' => 'Garango'],
            ['name' => 'Mme DEMBELE Mariam', 'type' => 'individual', 'city' => 'Banfora'],
            ['name' => 'Coopérative Oignon de Yako', 'type' => 'company', 'city' => 'Yako'],
            ['name' => 'M. KONATE Bakary', 'type' => 'individual', 'city' => 'Orodara'],
            ['name' => 'Ferme Horticole de Kombissiri', 'type' => 'company', 'city' => 'Kombissiri'],
            ['name' => 'Mme KONE Fatoumata', 'type' => 'individual', 'city' => 'Bobo-Dioulasso'],
            ['name' => 'Union des Producteurs de Sésame de Fada', 'type' => 'company', 'city' => 'Fada N\'Gourma'],
            ['name' => 'M. OUATTARA Lassina', 'type' => 'individual', 'city' => 'Banfora'],
            ['name' => 'Ferme Avicole du Bam', 'type' => 'company', 'city' => 'Kongoussi'],
            ['name' => 'Mme YAMEOGO Pascaline', 'type' => 'individual', 'city' => 'Koudougou'],
            ['name' => 'Coopérative Tomate de Ziniaré', 'type' => 'company', 'city' => 'Ziniaré'],
            ['name' => 'M. NITIEMA Rasmané', 'type' => 'individual', 'city' => 'Ziniaré'],
            ['name' => 'Ferme Semencière de Tenkodogo', 'type' => 'company', 'city' => 'Tenkodogo'],
            ['name' => 'Mme ZOUGRANA Edith', 'type' => 'individual', 'city' => 'Koupéla'],
            ['name' => 'Coopérative Piment de Reo', 'type' => 'company', 'city' => 'Reo'],
            ['name' => 'M. TANKOANO David', 'type' => 'individual', 'city' => 'Fada N\'Gourma'],
            ['name' => 'Ferme Agro-Silvo-Pastorale de Léo', 'type' => 'company', 'city' => 'Leo'],
            ['name' => 'Mme LANKOANDE Sophie', 'type' => 'individual', 'city' => 'Koupéla'],
            ['name' => 'Union des Cotonculteurs du Mouhoun', 'type' => 'company', 'city' => 'Dédougou'],
            ['name' => 'M. NATAMA Yacouba', 'type' => 'individual', 'city' => 'Fada N\'Gourma'],
            ['name' => 'Ferme Avicole de Pabré', 'type' => 'company', 'city' => 'Pabré'],
            ['name' => 'Mme COMBARY Brigitte', 'type' => 'individual', 'city' => 'Pama'],
            ['name' => 'Coopérative Arbo-Culture de Comoé', 'type' => 'company', 'city' => 'Banfora'],
            ['name' => 'M. DIESSONGO Bernard', 'type' => 'individual', 'city' => 'Tenkodogo'],
            ['name' => 'Ferme Semencière du Yatenga', 'type' => 'company', 'city' => 'Ouahigouya'],
            ['name' => 'Mme THIOMBIANO Clarisse', 'type' => 'individual', 'city' => 'Fada N\'Gourma'],
            ['name' => 'Coopérative Rizicole de Kou-Valley', 'type' => 'company', 'city' => 'Bobo-Dioulasso'],
            ['name' => 'M. DIONGUE Hamidou', 'type' => 'individual', 'city' => 'Ouagadougou'],
            ['name' => 'Ferme Maraîchère de Kokologho', 'type' => 'company', 'city' => 'Kokologho'],
            ['name' => 'Mme BAZIE Sabine', 'type' => 'individual', 'city' => 'Reo'],
            ['name' => 'Groupement Agricole de Tanghin-Dassouri', 'type' => 'company', 'city' => 'Tanghin-Dassouri'],
            ['name' => 'M. BADOLO Antoine', 'type' => 'individual', 'city' => 'Koudougou'],
            ['name' => 'Coopérative Anacarde du Ioba', 'type' => 'company', 'city' => 'Gaoua'],
            ['name' => 'Mme ZOMA Bernadette', 'type' => 'individual', 'city' => 'Koudougou'],
            ['name' => 'Ferme Avicole de Banfora', 'type' => 'company', 'city' => 'Banfora'],
            ['name' => 'M. NEYA Simplice', 'type' => 'individual', 'city' => 'Leo'],
            ['name' => 'Groupement Maraîcher de Saaba', 'type' => 'company', 'city' => 'Saaba'],
            ['name' => 'Mme BATIONO Rose', 'type' => 'individual', 'city' => 'Reo'],
            ['name' => 'Ferme Bio de Zabre', 'type' => 'company', 'city' => 'Zabré'],
            ['name' => 'M. BIZI François', 'type' => 'individual', 'city' => 'Manga'],
            ['name' => 'Coopérative Papaye de Loumbila', 'type' => 'company', 'city' => 'Loumbila'],
            ['name' => 'Mme SEYDOU Aminata', 'type' => 'individual', 'city' => 'Dori'],
            ['name' => 'Ferme Élevage du Sahel', 'type' => 'company', 'city' => 'Dori'],
            ['name' => 'M. DICKO Amadou', 'type' => 'individual', 'city' => 'Djibo'],
            ['name' => 'Coopérative Niébé de Ouahigouya', 'type' => 'company', 'city' => 'Ouahigouya'],
            ['name' => 'Mme DIALLO Fatou', 'type' => 'individual', 'city' => 'Dori'],
            ['name' => 'Ferme Avicole du Soum', 'type' => 'company', 'city' => 'Djibo'],
            ['name' => 'M. BARRY Boureima', 'type' => 'individual', 'city' => 'Dori'],
            ['name' => 'Coopérative Maraîchère du Lac Bam', 'type' => 'company', 'city' => 'Kongoussi'],
            ['name' => 'Mme TALL Nafissatou', 'type' => 'individual', 'city' => 'Kaya'],
            ['name' => 'Groupement Agricole de Korsimoro', 'type' => 'company', 'city' => 'Korsimoro'],
            ['name' => 'M. SOW Moussa', 'type' => 'individual', 'city' => 'Ouahigouya'],
            ['name' => 'Ferme Semencière de Komki-Ipala', 'type' => 'company', 'city' => 'Komki-Ipala'],
        ];

        $customers = [];
        foreach ($customersData as $i => $cData) {
            $phonePrefixes = ['70', '76', '60', '78', '65', '55'];
            $pref = $phonePrefixes[($i + 1) % count($phonePrefixes)];
            $phone = "+226 {$pref} " . sprintf('%02d', ($i % 90) + 11) . ' ' . sprintf('%02d', ($i * 5) % 90 + 10) . ' ' . sprintf('%02d', ($i * 9) % 90 + 10);

            $cType = in_array($cData['type'], ['company', 'entreprise']) ? 'entreprise' : 'particulier';

            $c = Customer::firstOrCreate(
                ['phone' => $phone],
                [
                    'type' => $cType,
                    'name' => $cData['name'],
                    'contact_person' => $cType === 'entreprise' ? 'Responsable ' . $cData['name'] : null,
                    'whatsapp' => $phone,
                    'email' => 'client' . ($i + 1) . '@client.bf',
                    'address' => $cData['city'] . ', Secteur Régal',
                    'ifu' => $cType === 'entreprise' ? '0001' . sprintf('%05d', $i + 1) . 'A' : null,
                    'notes' => 'Client régulier de la boutique SuivreMonCommerce',
                    'is_active' => true,
                ]
            );
            $customers[] = $c;
        }

        // ====================================================
        // 4. PRODUITS ET UNITÉS DE STOCK (100 PRODUITS, ~160 UNITÉS)
        // ====================================================
        $productsCatalog = [
            // Fertilisants & Engrais (15 produits)
            ['name' => 'Engrais NPK 15-15-15', 'desc' => 'Fertilisant complet minéral pour céréales et maraîchage', 'base' => 'Sac 50kg', 'base_price' => 22500, 'multi' => 'Tonne (20 sacs)', 'multi_eq' => 20.0, 'multi_price' => 440000],
            ['name' => 'Urée 46% Azote SOFITEX', 'desc' => 'Engrais azoté hautement concentré pour la croissance végétale', 'base' => 'Sac 50kg', 'base_price' => 21000, 'multi' => 'Tonne (20 sacs)', 'multi_eq' => 20.0, 'multi_price' => 410000],
            ['name' => 'Engrais DAP 18-46-0', 'desc' => 'Phosphate diammonique pour le développement racinaire', 'base' => 'Sac 50kg', 'base_price' => 24000, 'multi' => 'Tonne (20 sacs)', 'multi_eq' => 20.0, 'multi_price' => 470000],
            ['name' => 'Engrais KCL Chlorure de Potassium', 'desc' => 'Potasse pour la qualité et la fructification des récoltes', 'base' => 'Sac 50kg', 'base_price' => 23000, 'multi' => 'Tonne (20 sacs)', 'multi_eq' => 20.0, 'multi_price' => 450000],
            ['name' => 'Compost Organique Enrichi 25kg', 'desc' => 'Amendement humique bio certifié', 'base' => 'Sac 25kg', 'base_price' => 8500, 'multi' => 'Palette (40 sacs)', 'multi_eq' => 40.0, 'multi_price' => 320000],
            ['name' => 'Fertilisant Liquide Bio-Boost 1L', 'desc' => 'Engrais foliaire bio stimulant', 'base' => 'Flacon 1L', 'base_price' => 6000, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 68000],
            ['name' => 'Sulfate d\'Ammonium 21% N', 'desc' => 'Engrais acidifiant recommandé pour sols calcaires', 'base' => 'Sac 50kg', 'base_price' => 19500, 'multi' => 'Tonne (20 sacs)', 'multi_eq' => 20.0, 'multi_price' => 380000],
            ['name' => 'Superphosphate Triple TSP 46%', 'desc' => 'Engrais phosphaté soluble à haute assimilation', 'base' => 'Sac 50kg', 'base_price' => 25000, 'multi' => 'Tonne (20 sacs)', 'multi_eq' => 20.0, 'multi_price' => 480000],
            ['name' => 'Nitrate de Calcium Maraîcher 25kg', 'desc' => 'Solfértilisant prévenant la pourriture apicale des tomates', 'base' => 'Sac 25kg', 'base_price' => 14500, 'multi' => null],
            ['name' => 'Nitrate de Potasse NOP 25kg', 'desc' => 'Fertilisant foliaire haute pureté pour goutte-à-goutte', 'base' => 'Sac 25kg', 'base_price' => 18000, 'multi' => null],
            ['name' => 'Sulfate de Magnésium 25kg', 'desc' => 'Correction des carences en magnésium et soufre', 'base' => 'Sac 25kg', 'base_price' => 12000, 'multi' => null],
            ['name' => 'Engrais Coton NPK 14-23-14', 'desc' => 'Formule spécifique recommandation SOFITEX pour le coton', 'base' => 'Sac 50kg', 'base_price' => 22000, 'multi' => 'Tonne (20 sacs)', 'multi_eq' => 20.0, 'multi_price' => 430000],
            ['name' => 'Bio-Fertilisant Humus Liquide 5L', 'desc' => 'Extrait d\'acides humiques et fulviques bio', 'base' => 'Bidon 5L', 'base_price' => 16000, 'multi' => 'Carton de 4 bidons', 'multi_eq' => 4.0, 'multi_price' => 60000],
            ['name' => 'Engrais Soluble Oligo-Éléments 1kg', 'desc' => 'Cocktail de micro-nutriments chélatés EDTA', 'base' => 'Sachet 1kg', 'base_price' => 4500, 'multi' => 'Carton de 20kg', 'multi_eq' => 20.0, 'multi_price' => 85000],
            ['name' => 'Phosphate Naturel de Kodjari 50kg', 'desc' => 'Amendement phosphaté d\'origine locale burkinabè', 'base' => 'Sac 50kg', 'base_price' => 11000, 'multi' => 'Tonne (20 sacs)', 'multi_eq' => 20.0, 'multi_price' => 210000],

            // Herbicides (15 produits)
            ['name' => 'Herbicide Glyphosate 480 SL', 'desc' => 'Herbicide systémique non sélectif désherbage total', 'base' => 'Flacon 1L', 'base_price' => 4500, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 51000],
            ['name' => 'Herbicide Atrazine 500 SC', 'desc' => 'Herbicide sélectif pré-émergence pour le maïs', 'base' => 'Flacon 1L', 'base_price' => 5000, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 57000],
            ['name' => 'Herbicide 2,4-D Amine 720 SL', 'desc' => 'Herbicide sélectif contre les adventices à feuilles larges', 'base' => 'Flacon 1L', 'base_price' => 4800, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 54000],
            ['name' => 'Herbicide Paraquat 200 SL', 'desc' => 'Herbicide de contact à action rapide', 'base' => 'Flacon 1L', 'base_price' => 4200, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 48000],
            ['name' => 'Herbicide Nicosulfuron 40 SC', 'desc' => 'Herbicide sélectif post-émergence maïs', 'base' => 'Flacon 1L', 'base_price' => 7500, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 85000],
            ['name' => 'Herbicide Pendiméthaline 400 EC', 'desc' => 'Herbicide pré-levée pour oignon, coton et légumineuses', 'base' => 'Flacon 1L', 'base_price' => 6200, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 70000],
            ['name' => 'Herbicide Oxadiazon 250 EC', 'desc' => 'Herbicide sélectif riziculture de bas-fond', 'base' => 'Flacon 1L', 'base_price' => 6800, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 78000],
            ['name' => 'Herbicide Haloxyfop-P-Méthyl 108 EC', 'desc' => 'Désherbant anti-graminées sélectif pour cultures à feuilles larges', 'base' => 'Flacon 1L', 'base_price' => 8500, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 96000],
            ['name' => 'Herbicide Diuron 80 WP', 'desc' => 'Herbicide résiduel contre mauvaises herbes vivaces', 'base' => 'Sachet 1kg', 'base_price' => 5500, 'multi' => 'Carton de 20kg', 'multi_eq' => 20.0, 'multi_price' => 100000],
            ['name' => 'Herbicide Triclopyr 480 EC', 'desc' => 'Désherbant sélectif pour débroussaillage et ligneux', 'base' => 'Flacon 1L', 'base_price' => 9800, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 110000],
            ['name' => 'Herbicide Propanil 360 EC', 'desc' => 'Herbicide de contact post-émergence riz', 'base' => 'Flacon 1L', 'base_price' => 5800, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 65000],
            ['name' => 'Herbicide Clomazone 480 EC', 'desc' => 'Herbicide sélectif pré-levée soja et coton', 'base' => 'Flacon 1L', 'base_price' => 10500, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 120000],
            ['name' => 'Herbicide Quizalofop-P-Éthyl 50 EC', 'desc' => 'Herbicide post-émergence graminées annuelles', 'base' => 'Flacon 1L', 'base_price' => 7900, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 90000],
            ['name' => 'Herbicide Oxyfluorfène 240 EC', 'desc' => 'Herbicide pré et post-émergence précoce ail et oignon', 'base' => 'Flacon 1L', 'base_price' => 8800, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 99000],
            ['name' => 'Herbicide Bio Végétal 5L', 'desc' => 'Désherbant biologique à base d\'acide pélargonique', 'base' => 'Bidon 5L', 'base_price' => 18500, 'multi' => null],

            // Insecticides & Acaricides (15 produits)
            ['name' => 'Insecticide Cyperméthrine 100 EC', 'desc' => 'Insecticide pyréthrinoïde polyvalent', 'base' => 'Flacon 1L', 'base_price' => 6500, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 75000],
            ['name' => 'Insecticide Deltaméthrine 25 EC', 'desc' => 'Insecticide à fort effet choc pour maraîchage', 'base' => 'Flacon 1L', 'base_price' => 7000, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 80000],
            ['name' => 'Insecticide Lambda-Cyhalothrine 50 EC', 'desc' => 'Protection globale contre chenilles et thrips', 'base' => 'Flacon 1L', 'base_price' => 6800, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 78000],
            ['name' => 'Insecticide Chlorpyrifos-Ethyl 480 EC', 'desc' => 'Insecticide du sol et des parties aériennes', 'base' => 'Flacon 1L', 'base_price' => 8000, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 90000],
            ['name' => 'Insecticide Acétamipride 20 SP', 'desc' => 'Insecticide systémique contre pucerons et mouches blanches', 'base' => 'Sachet 250g', 'base_price' => 3200, 'multi' => 'Carton de 20 sachets', 'multi_eq' => 20.0, 'multi_price' => 60000],
            ['name' => 'Insecticide Imidaclopride 200 SL', 'desc' => 'Traitement des semences et foliaire systémique', 'base' => 'Flacon 1L', 'base_price' => 9500, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 108000],
            ['name' => 'Insecticide Bio Neem 1L', 'desc' => 'Extrait d\'huile de neem biologique certifié', 'base' => 'Flacon 1L', 'base_price' => 7800, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 88000],
            ['name' => 'Acaricide Abamectine 18 EC', 'desc' => 'Acaricide et insecticide contre acariens et mineuses', 'base' => 'Flacon 1L', 'base_price' => 11000, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 125000],
            ['name' => 'Insecticide Profenofos 500 EC', 'desc' => 'Insecticide organophosphoré contre chenilles légionnaires', 'base' => 'Flacon 1L', 'base_price' => 8500, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 96000],
            ['name' => 'Insecticide Emamectine Benzoate 5 WDG', 'desc' => 'Anti-chenille spécifique à faible dose d\'application', 'base' => 'Sachet 250g', 'base_price' => 4800, 'multi' => 'Carton de 20 sachets', 'multi_eq' => 20.0, 'multi_price' => 90000],
            ['name' => 'Insecticide Bifenthrine 100 EC', 'desc' => 'Pyréthrinoïde à large spectre et action acaricide', 'base' => 'Flacon 1L', 'base_price' => 9200, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 104000],
            ['name' => 'Insecticide Diméthoate 400 EC', 'desc' => 'Insecticide systémique contre pucerons et thrips', 'base' => 'Flacon 1L', 'base_price' => 6200, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 70000],
            ['name' => 'Insecticide Bacillus Thuringiensis (BT)', 'desc' => 'Bio-insecticide bactérien sélectif des chenilles', 'base' => 'Sachet 500g', 'base_price' => 6500, 'multi' => null],
            ['name' => 'Insecticide Thiaméthoxam 25 WG', 'desc' => 'Systémique haute efficacité contre insectes piqueurs-suceurs', 'base' => 'Sachet 250g', 'base_price' => 5200, 'multi' => 'Carton de 20 sachets', 'multi_eq' => 20.0, 'multi_price' => 98000],
            ['name' => 'Insecticide Spinosad 480 SC', 'desc' => 'Bio-insecticide d\'origine naturelle pour maraîchage', 'base' => 'Flacon 250ml', 'base_price' => 8200, 'multi' => null],

            // Fongicides & Traitements (15 produits)
            ['name' => 'Fongicide Mancozèbe 80 WP', 'desc' => 'Fongicide préventif à large spectre', 'base' => 'Sachet 1kg', 'base_price' => 3800, 'multi' => 'Carton de 20kg', 'multi_eq' => 20.0, 'multi_price' => 72000],
            ['name' => 'Fongicide Oxychlorure de Cuivre 50 WP', 'desc' => 'Traitement cuprique préventif contre le mildiou', 'base' => 'Sachet 1kg', 'base_price' => 4200, 'multi' => 'Carton de 20kg', 'multi_eq' => 20.0, 'multi_price' => 80000],
            ['name' => 'Fongicide Azoxystrobine 250 SC', 'desc' => 'Fongicide systémique triazole pour légumes', 'base' => 'Flacon 1L', 'base_price' => 14500, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 165000],
            ['name' => 'Fongicide Métalaxyl + Mancozèbe', 'desc' => 'Fongicide bi-actif contre pourriture des racines', 'base' => 'Sachet 1kg', 'base_price' => 5200, 'multi' => 'Carton de 20kg', 'multi_eq' => 20.0, 'multi_price' => 98000],
            ['name' => 'Fongicide Tébuconazole 250 EC', 'desc' => 'Fongicide systémique contre la rouille et l\'oïdium', 'base' => 'Flacon 1L', 'base_price' => 12000, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 135000],
            ['name' => 'Fongicide Carbendazime 500 SC', 'desc' => 'Fongicide systémique polyvalent', 'base' => 'Flacon 1L', 'base_price' => 6800, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 76000],
            ['name' => 'Fongicide Difénoconazole 250 EC', 'desc' => 'Traitement curatif contre l\'alternariose et l\'anthracnose', 'base' => 'Flacon 1L', 'base_price' => 15500, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 175000],
            ['name' => 'Traitement de Semences Thiram + Imidaclopride', 'desc' => 'Protecteur complet fongicide et insecticide pour semences', 'base' => 'Sachet 100g', 'base_price' => 1800, 'multi' => 'Boîte de 50 sachets', 'multi_eq' => 50.0, 'multi_price' => 82000],
            ['name' => 'Fongicide Hymexazol 300 SL', 'desc' => 'Traitement spécifique de la fonte des semis en pépinière', 'base' => 'Flacon 500ml', 'base_price' => 7500, 'multi' => null],
            ['name' => 'Fongicide Soufre Micronisé 80 WG', 'desc' => 'Traitement anti-oïdium et action acaricide secondaire', 'base' => 'Sac 1kg', 'base_price' => 3200, 'multi' => 'Carton de 20kg', 'multi_eq' => 20.0, 'multi_price' => 60000],
            ['name' => 'Fongicide Propiconazole 250 EC', 'desc' => 'Triazole systémique à action préventive et curative', 'base' => 'Flacon 1L', 'base_price' => 13200, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 150000],
            ['name' => 'Fongicide Cymoxanil + Mancozèbe', 'desc' => 'Association pénétrante contre mildiou de la tomate', 'base' => 'Sachet 1kg', 'base_price' => 5800, 'multi' => 'Carton de 20kg', 'multi_eq' => 20.0, 'multi_price' => 108000],
            ['name' => 'Fongicide Chlorothalonil 720 SC', 'desc' => 'Fongicide de contact résistant au lessivage par les pluies', 'base' => 'Flacon 1L', 'base_price' => 8900, 'multi' => 'Carton de 12L', 'multi_eq' => 12.0, 'multi_price' => 100000],
            ['name' => 'Fongicide Bio Trichoderma Harzianum', 'desc' => 'Bio-fongicide antagoniste des champignons du sol', 'base' => 'Sachet 500g', 'base_price' => 8500, 'multi' => null],
            ['name' => 'Fongicide Fosétyl-Aluminium 80 WP', 'desc' => 'Systémique ascendant et descendant contre phytophthora', 'base' => 'Sachet 1kg', 'base_price' => 9800, 'multi' => 'Carton de 20kg', 'multi_eq' => 20.0, 'multi_price' => 180000],

            // Semences Certifiées (20 produits)
            ['name' => 'Semences Maïs Hybride TZEE (Sac 10kg)', 'desc' => 'Maïs extra-précoce 90 jours à haut rendement', 'base' => 'Sac 10kg', 'base_price' => 14000, 'multi' => null],
            ['name' => 'Semences Maïs Blanc Certifié (Sac 10kg)', 'desc' => 'Variété sélectionnée pour la consommation locale', 'base' => 'Sac 10kg', 'base_price' => 12000, 'multi' => null],
            ['name' => 'Semences Riz Isorad (Sac 10kg)', 'desc' => 'Semence de riz de bas-fond certifiée', 'base' => 'Sac 10kg', 'base_price' => 11000, 'multi' => null],
            ['name' => 'Semences Riz FKR 64 Certifié (Sac 10kg)', 'desc' => 'Riz irrigué haute performance INERA', 'base' => 'Sac 10kg', 'base_price' => 12500, 'multi' => null],
            ['name' => 'Semences Tomate Hybride Cobra 50g', 'desc' => 'Variété tolérante aux maladies et très productive', 'base' => 'Boîte 50g', 'base_price' => 18000, 'multi' => null],
            ['name' => 'Semences Tomate Mongal F1 50g', 'desc' => 'Tomate de saison sèche très ferme', 'base' => 'Boîte 50g', 'base_price' => 19500, 'multi' => null],
            ['name' => 'Semences Piment Piquant Tropimech 50g', 'desc' => 'Piment habanero fort très recherché', 'base' => 'Boîte 50g', 'base_price' => 16500, 'multi' => null],
            ['name' => 'Semences Gombo Clemson Spineless 100g', 'desc' => 'Gombo vert sans épines très tendre', 'base' => 'Boîte 100g', 'base_price' => 9500, 'multi' => null],
            ['name' => 'Semences Oignon Violet de Galmi 100g', 'desc' => 'Variété idéale pour le stockage longue durée', 'base' => 'Boîte 100g', 'base_price' => 22000, 'multi' => null],
            ['name' => 'Semences Oignon Prema F1 100g', 'desc' => 'Oignon hybride de gros calibre à fort rendement', 'base' => 'Boîte 100g', 'base_price' => 28000, 'multi' => null],
            ['name' => 'Semences Soja TGX (Sac 10kg)', 'desc' => 'Semences de soja certifiées riches en protéines', 'base' => 'Sac 10kg', 'base_price' => 13000, 'multi' => null],
            ['name' => 'Semences Sésame Blanc S42 (Sac 5kg)', 'desc' => 'Sésame de variété sélectionnée pour l\'exportation', 'base' => 'Sac 5kg', 'base_price' => 9500, 'multi' => null],
            ['name' => 'Semences Niébé Kpodjèguè (Sac 10kg)', 'desc' => 'Haricot niébé résistant à la sécheresse', 'base' => 'Sac 10kg', 'base_price' => 11500, 'multi' => null],
            ['name' => 'Semences Coton FK37 (Sac 20kg)', 'desc' => 'Semence de coton traitée certifiée SOFITEX', 'base' => 'Sac 20kg', 'base_price' => 16000, 'multi' => null],
            ['name' => 'Semences Pastèque Kaolack 100g', 'desc' => 'Pastèque à chair rouge très sucrée', 'base' => 'Boîte 100g', 'base_price' => 12000, 'multi' => null],
            ['name' => 'Semences Melon Ananas 100g', 'desc' => 'Melon très parfumé résistant au transport', 'base' => 'Boîte 100g', 'base_price' => 13500, 'multi' => null],
            ['name' => 'Semences Aubergine Violette Longue 100g', 'desc' => 'Aubergine de variété locale productive', 'base' => 'Boîte 100g', 'base_price' => 8800, 'multi' => null],
            ['name' => 'Semences Courgette Diamant F1 100g', 'desc' => 'Courgette verte droite précoce', 'base' => 'Boîte 100g', 'base_price' => 15000, 'multi' => null],
            ['name' => 'Semences Carotte Kuroda 100g', 'desc' => 'Carotte orange racine lisse', 'base' => 'Boîte 100g', 'base_price' => 10500, 'multi' => null],
            ['name' => 'Semences Concombre Poinsett 100g', 'desc' => 'Concombre croquant résistant au mildiou', 'base' => 'Boîte 100g', 'base_price' => 9200, 'multi' => null],

            // Matériel, Équipements & Outillage (20 produits)
            ['name' => 'Pulvérisateur à Dos 16L Manuel', 'desc' => 'Pulvérisateur haute pression lance inox', 'base' => 'Unité', 'base_price' => 18500, 'multi' => 'Carton de 6 unités', 'multi_eq' => 6.0, 'multi_price' => 105000],
            ['name' => 'Pulvérisateur Électrique à Batterie 20L', 'desc' => 'Autonomie 6h avec régulateur de pression', 'base' => 'Unité', 'base_price' => 38000, 'multi' => null],
            ['name' => 'Pulvérisateur Thermique à Moteur 25L', 'desc' => 'Moteur 2 temps puissant pour vergers et grands champs', 'base' => 'Unité', 'base_price' => 85000, 'multi' => null],
            ['name' => 'Bottes de Sécurité Agricoles (Paire)', 'desc' => 'Bottes étanches renforcées PVC', 'base' => 'Paire', 'base_price' => 7500, 'multi' => 'Carton de 10 paires', 'multi_eq' => 10.0, 'multi_price' => 70000],
            ['name' => 'Gants de Protection Chimique (Paire)', 'desc' => 'Gants en nitrile résistant aux solvants', 'base' => 'Paire', 'base_price' => 2500, 'multi' => 'Paquet de 12 paires', 'multi_eq' => 12.0, 'multi_price' => 27000],
            ['name' => 'Masque à Cartouche Anti-Vapeurs Phyto', 'desc' => 'Masque panoramique double filtres', 'base' => 'Unité', 'base_price' => 12500, 'multi' => null],
            ['name' => 'Combinaison de Protection Phyto', 'desc' => 'Combinaison étanche lavable réutilisable', 'base' => 'Unité', 'base_price' => 9500, 'multi' => null],
            ['name' => 'Tuyau d\'Arrosage Maraîcher 50m', 'desc' => 'Tuyau armé 3 couches anti-torsion', 'base' => 'Rouleau 50m', 'base_price' => 26000, 'multi' => null],
            ['name' => 'Kit d\'Irrigation Goutte-à-Goutte 100m', 'desc' => 'Système complet avec goutteurs autorégulants', 'base' => 'Kit 100m', 'base_price' => 45000, 'multi' => null],
            ['name' => 'Gaine Goutte-à-Goutte 16mm (Rouleau 500m)', 'desc' => 'Gaine d\'irrigation avec goutteurs espacés de 20cm', 'base' => 'Rouleau 500m', 'base_price' => 38000, 'multi' => null],
            ['name' => 'Motopompe d\'Arrosage 3 Pouces Essence', 'desc' => 'Débit 60m3/h pour irrigation de bas-fond', 'base' => 'Unité', 'base_price' => 135000, 'multi' => null],
            ['name' => 'Bâche de Séchage et Protection 6x4m', 'desc' => 'Bâche tressée étanche polyéthylène', 'base' => 'Unité', 'base_price' => 15000, 'multi' => null],
            ['name' => 'Sécateur de Taille Professionnel', 'desc' => 'Lame en acier forgé trempé', 'base' => 'Unité', 'base_price' => 6500, 'multi' => null],
            ['name' => 'Arrosoir Plastique Renforcé 12L', 'desc' => 'Arrosoir avec pomme laiton amovible', 'base' => 'Unité', 'base_price' => 4500, 'multi' => null],
            ['name me' => 'Coutelas / Machette 22 Pouces', 'desc' => 'Machette acier trempé poignée ergonomique', 'base' => 'Unité', 'base_price' => 3800, 'multi' => 'Carton de 20 unités', 'multi_eq' => 20.0, 'multi_price' => 70000],
            ['name' => 'Houe Armée Triangulaire avec Manche', 'desc' => 'Outil de désherbage et binage manuel traditionnel', 'base' => 'Unité', 'base_price' => 4200, 'multi' => null],
            ['name' => 'Pelle Agricole Creuse emmanchée', 'desc' => 'Pelle en acier pour chargement terre et compost', 'base' => 'Unité', 'base_price' => 4800, 'multi' => null],
            ['name' => 'Râteau Maraîcher 14 Dents', 'desc' => 'Râteau métallique pour préparation des planches', 'base' => 'Unité', 'base_price' => 3500, 'multi' => null],
            ['name' => 'Humidimètre de Sol Portatif', 'desc' => 'Appareil de mesure numérique de l\'humidité des sols', 'base' => 'Unité', 'base_price' => 22000, 'multi' => null],
            ['name' => 'Sacs de Jute Vides 100kg (Lot de 10)', 'desc' => 'Sacs de emballage renforcés pour céréales', 'base' => 'Lot 10 sacs', 'base_price' => 8500, 'multi' => 'Balle de 100 sacs', 'multi_eq' => 10.0, 'multi_price' => 80000],
        ];

        $products = [];
        $baseUnits = [];
        $multiUnits = [];
        $allUnits = [];

        foreach ($productsCatalog as $pItem) {
            $productName = $pItem['name'] ?? $pItem['name me'];
            $product = Product::firstOrCreate(
                ['name' => $productName],
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

        // Instanciation des Services
        $purchaseService = app(PurchaseService::class);
        $repackagingService = app(RepackagingService::class);
        $saleService = app(SaleService::class);
        $paymentService = app(PaymentService::class);
        $customerReturnService = app(CustomerReturnService::class);
        $lossService = app(LossService::class);
        $snapshotService = app(StockSnapshotService::class);

        // ====================================================
        // 5. ACHATS DE STOCK (100 ACHATS, ~250 LIGNES)
        // ====================================================
        for ($i = 1; $i <= 100; $i++) {
            $supplier = $suppliers[($i - 1) % count($suppliers)];
            $user = $users[$i % count($users)];
            $daysAgo = 120 - $i; // Étalé sur 4 mois
            $pDate = Carbon::now()->subDays(max(1, $daysAgo))->toDateString();

            // Choisir 2 à 3 produits distincts
            $pIdx1 = ($i * 2) % count($products);
            $pIdx2 = ($i * 2 + 1) % count($products);
            $pIdx3 = ($i * 2 + 2) % count($products);

            $prodIndexes = [$pIdx1, $pIdx2];
            if ($i % 2 === 0) {
                $prodIndexes[] = $pIdx3;
            }

            $lines = [];
            foreach ($prodIndexes as $pIdx) {
                $p = $products[$pIdx];
                $u = $multiUnits[$p->id] ?? $baseUnits[$p->id];
                $qty = rand(80, 300); // Stock abondant pour alimenter les ventes
                $unitCost = (int) round($u->default_selling_price * 0.80); // Marge 20%

                $lines[] = [
                    'product_id' => $p->id,
                    'stock_unit_id' => $u->id,
                    'quantity' => $qty,
                    'unit_price' => $unitCost,
                ];
            }

            $approxTotal = array_reduce($lines, fn($acc, $l) => $acc + ($l['quantity'] * $l['unit_price']), 0);
            $paid = ($i % 3 === 0) ? (int) round($approxTotal * 0.5) : $approxTotal;

            $purchaseService->registerPurchase([
                'supplier_id' => $supplier->id,
                'purchase_date' => $pDate,
                'paid_amount' => $paid,
                'notes' => "Bon d'approvisionnement N° ACH-BF-{$i}",
                'lines' => $lines,
            ], $user->id);
        }

        // ====================================================
        // 6. RECONDITIONNEMENT (100 OPERATIONS)
        // ====================================================
        $multiProdIds = array_keys($multiUnits);
        for ($i = 1; $i <= 100; $i++) {
            $pId = $multiProdIds[($i - 1) % count($multiProdIds)];
            $mUnit = $multiUnits[$pId];
            $bUnit = $baseUnits[$pId];
            $user = $users[($i + 2) % count($users)];
            $daysAgo = 100 - $i;

            $srcQty = rand(2, 5);
            $targetQty = $srcQty * (float) $mUnit->base_unit_equivalent;

            $repackagingService->executeRepackaging([
                'product_id' => $pId,
                'source_stock_unit_id' => $mUnit->id,
                'source_quantity' => $srcQty,
                'target_stock_unit_id' => $bUnit->id,
                'target_quantity' => $targetQty,
                'repackaging_date' => Carbon::now()->subDays(max(1, $daysAgo)),
                'notes' => "Reconditionnement N° {$i} : conversion de {$mUnit->name} vers {$bUnit->name}",
            ], $user);
        }

        // ====================================================
        // 7. VENTES & FACTURES (100 VENTES, ~250 LIGNES)
        // ====================================================
        $sales = [];
        for ($i = 1; $i <= 100; $i++) {
            $customer = ($i % 10 === 0) ? null : $customers[($i - 1) % count($customers)];
            $user = $users[($i + 1) % count($users)];
            $daysAgo = 90 - $i;
            $sDate = Carbon::now()->subDays(max(0, $daysAgo));

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
                    'quantity' => rand(1, 8),
                    'unit_price' => $u1->default_selling_price,
                ],
                [
                    'product_id' => $p2->id,
                    'stock_unit_id' => $u2->id,
                    'quantity' => rand(1, 4),
                    'unit_price' => $u2->default_selling_price,
                ],
            ];

            $totalAmt = array_reduce($lines, fn($acc, $l) => $acc + ($l['quantity'] * $l['unit_price']), 0);

            $paidAmt = match ($i % 4) {
                0 => 0, // Non payé
                1 => (int) round($totalAmt * 0.4), // Partiellement payé
                default => $totalAmt, // Payé
            };

            $sale = $saleService->createSale([
                'customer_id' => $customer?->id,
                'sale_date' => $sDate,
                'paid_amount' => $paidAmt,
                'notes' => "Vente comptoir / commande N° VNT-BF-{$i}",
                'lines' => $lines,
            ], $user);

            $sales[] = $sale;
        }

        // ====================================================
        // 8. PAIEMENTS DE FACTURES ET ACHATS (100 PAIEMENTS)
        // ====================================================
        // 50 Paiements de factures clients
        $invoicesToPay = \App\Domain\Facturation\Models\Invoice::where('remaining_amount', '>', 0)->get();
        $invoicePaymentCount = 0;

        foreach ($invoicesToPay as $inv) {
            if ($invoicePaymentCount >= 50) {
                break;
            }

            $payAmt = (int) min((float) $inv->remaining_amount, rand(10000, 60000));
            if ($payAmt <= 0) continue;

            $user = $users[$invoicePaymentCount % count($users)];
            $method = match ($invoicePaymentCount % 4) {
                0 => 'Orange Money',
                1 => 'Moov Money',
                2 => 'Espèces',
                default => 'Wave',
            };

            $paymentService->recordPayment([
                'invoice_id' => $inv->id,
                'amount' => $payAmt,
                'payment_date' => Carbon::parse($inv->invoice_date)->addDays(1),
                'payment_method' => $method,
                'reference' => 'OM-BF-' . sprintf('%05d', $invoicePaymentCount + 100),
                'notes' => "Règlement client par {$method}",
            ], $user);

            $invoicePaymentCount++;
        }

        // 50 Paiements de bons d'achat fournisseurs
        $purchasesToPay = \App\Domain\Achats\Models\Purchase::where('remaining_amount', '>', 0)->get();
        $purchasePaymentCount = 0;

        foreach ($purchasesToPay as $purch) {
            if ($purchasePaymentCount >= 50) {
                break;
            }

            $payAmt = (int) min((float) $purch->remaining_amount, rand(50000, 200000));
            if ($payAmt <= 0) continue;

            $user = $users[$purchasePaymentCount % count($users)];
            $method = match ($purchasePaymentCount % 3) {
                0 => 'Virement bancaire',
                1 => 'Chèque bancaire',
                default => 'Orange Money',
            };

            $paymentService->recordPurchasePayment([
                'purchase_id' => $purch->id,
                'amount' => $payAmt,
                'payment_date' => Carbon::parse($purch->purchase_date)->addDays(2),
                'payment_method' => $method,
                'reference' => 'VIR-FOURN-' . sprintf('%05d', $purchasePaymentCount + 500),
                'notes' => "Acompte / solde fournisseur {$purch->supplier->name}",
            ], $user);

            $purchasePaymentCount++;
        }

        // ====================================================
        // 9. RETOURS CLIENTS (100 CUSTOMER RETURNS)
        // ====================================================
        for ($i = 1; $i <= 100; $i++) {
            $customer = $customers[($i - 1) % count($customers)];
            $p = $products[($i * 2) % count($products)];
            $u = $baseUnits[$p->id];
            $user = $users[($i + 3) % count($users)];

            $cr = $customerReturnService->recordReturn([
                'customer_id' => $customer->id,
                'product_id' => $p->id,
                'stock_unit_id' => $u->id,
                'quantity' => rand(1, 3),
                'reason' => match ($i % 4) {
                    0 => 'Sachet percé pendant le transport',
                    1 => 'Erreur de référence lors de la commande',
                    2 => 'Surplus de chantier retourné intact',
                    default => 'Emballage légèrement endommagé',
                },
                'return_date' => Carbon::now()->subDays(max(1, 80 - $i)),
                'notes' => "Retour client enregistré N° RET-BF-{$i}",
            ], $user);

            if ($i <= 50) {
                // Restocké
                $customerReturnService->validateAndRestock($cr, $admin);
            } elseif ($i <= 90) {
                // Mis au rebut / Perte
                $customerReturnService->validateAndDiscard($cr, $admin, "Produit altéré au contrôle retour N° {$i}");
            }
            // Les 10 derniers restent 'pending'
        }

        // ====================================================
        // 10. PERTES EN MAGASIN (100 LOSSES)
        // ====================================================
        for ($i = 1; $i <= 100; $i++) {
            $p = $products[($i * 3) % count($products)];
            $u = $baseUnits[$p->id];
            $user = $users[$i % count($users)];

            $lossService->recordLoss([
                'product_id' => $p->id,
                'stock_unit_id' => $u->id,
                'quantity' => rand(1, 2),
                'reason' => match ($i % 4) {
                    0 => 'Sac percé / Infiltration d\'humidité',
                    1 => 'Flacon fissuré pendant manutention',
                    2 => 'Date de péremption dépassée',
                    default => 'Avarie constatée lors de l\'inventaire',
                },
                'loss_date' => Carbon::now()->subDays(max(1, 90 - $i)),
                'notes' => "Perte magasin déclarée N° PRT-BF-{$i}",
            ], $user);
        }

        // ====================================================
        // 11. CLICHÉS DE STOCK (3 MOIS)
        // ====================================================
        for ($mOffset = 0; $mOffset < 3; $mOffset++) {
            $dt = Carbon::now()->subMonths($mOffset);
            $snapshotService->generateSnapshotForMonth((int) $dt->year, (int) $dt->month);
        }
    }
}
