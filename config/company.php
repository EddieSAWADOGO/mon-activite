<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuration de l'Entreprise Émettrice des Factures
    |--------------------------------------------------------------------------
    |
    | Informations sur l'entreprise cliente utilisant le logiciel.
    | Ces données sont affichées sur les factures, bons de livraison et reçus.
    |
    */

    'name' => env('COMPANY_NAME', 'AGRO-DISTRIBUTION DU FASO SARL'),

    'tagline' => env('COMPANY_TAGLINE', 'Commerce Général & Négoce d\'Intrants Agricoles'),

    'ifu' => env('COMPANY_IFU', '3202415987612'),

    'rccm' => env('COMPANY_RCCM', 'BF OUA 2024 B 4589'),

    'address' => env('COMPANY_ADDRESS', 'Avenue Kadiogo, Zone Industrielle, Ouagadougou'),

    'country' => env('COMPANY_COUNTRY', 'Burkina Faso'),

    'phone' => env('COMPANY_PHONE', '+226 25 31 00 00 / +226 70 20 00 11'),

    'email' => env('COMPANY_EMAIL', 'contact@agro-distro.bf'),

    'logo_path' => env('COMPANY_LOGO_PATH', '/images/company-logo.png'),

    'bank_details' => env('COMPANY_BANK_DETAILS', 'BOA Burkina: BF084 01001 012345678901 45'),

];
