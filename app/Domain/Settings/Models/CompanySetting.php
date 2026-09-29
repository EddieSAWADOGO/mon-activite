<?php

namespace App\Domain\Settings\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $table = 'company_settings';

    protected $fillable = [
        'name',
        'tagline',
        'ifu',
        'rccm',
        'address',
        'phone',
        'email',
        'logo_path',
        'bank_details',
    ];

    /**
     * Récupère la configuration unique de l'entreprise ou crée la ligne par défaut.
     */
    public static function getSettings(): self
    {
        $setting = self::first();

        if (! $setting) {
            $setting = self::create([
                'name' => config('company.name', 'AGRO-DISTRIBUTION DU FASO SARL'),
                'tagline' => config('company.tagline', 'Commerce Général & Négoce d\'Intrants Agricoles'),
                'ifu' => config('company.ifu', '3202415987612'),
                'rccm' => config('company.rccm', 'BF OUA 2024 B 4589'),
                'address' => config('company.address', 'Avenue Kadiogo, Zone Industrielle, Ouagadougou'),
                'phone' => config('company.phone', '+226 25 31 00 00 / +226 70 20 00 11'),
                'email' => config('company.email', 'contact@agro-distro.bf'),
                'logo_path' => config('company.logo_path', null),
                'bank_details' => config('company.bank_details', 'BOA Burkina: BF084 01001 012345678901 45'),
            ]);
        }

        return $setting;
    }
}
