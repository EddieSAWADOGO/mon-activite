<?php

namespace App\Domain\Settings\Http\Controllers;

use App\Domain\Settings\Http\Requests\UpdateCompanySettingRequest;
use App\Domain\Settings\Models\CompanySetting;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CompanySettingController extends Controller
{
    public function edit(): View
    {
        if (! auth()->user()?->canManageUsers()) {
            abort(403, 'Accès réservé aux administrateurs.');
        }

        $setting = CompanySetting::getSettings();

        return view('settings.company', compact('setting'));
    }

    public function update(UpdateCompanySettingRequest $request): RedirectResponse
    {
        if (! auth()->user()?->canManageUsers()) {
            abort(403, 'Accès réservé aux administrateurs.');
        }

        $setting = CompanySetting::getSettings();
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'company_logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $data['logo_path'] = '/uploads/' . $filename;
        }

        $setting->update($data);

        return redirect()->route('settings.company.edit')
            ->with('success', 'Les informations de l\'entreprise émettrice ont été mises à jour avec succès.');
    }
}
