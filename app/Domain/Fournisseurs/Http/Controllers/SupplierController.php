<?php

namespace App\Domain\Fournisseurs\Http\Controllers;

use App\Domain\Fournisseurs\Models\Supplier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query()->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->paginate(15)->withQueryString();

        return view('fournisseurs.index', compact('suppliers'));
    }

    public function create()
    {
        return view('fournisseurs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'type' => 'required|string|in:company,individual',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ], [], [
            'name' => 'nom du fournisseur',
            'type' => 'type de fournisseur',
        ]);

        $supplier = Supplier::create($validated);

        return redirect()->route('fournisseurs.index')
            ->with('success', "Le fournisseur '{$supplier->name}' a été créé avec succès.");
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['purchases' => function ($q) {
            $q->latest()->limit(10);
        }]);

        $totalPurchased = $supplier->purchases()->sum('total_amount');
        $totalPaid = $supplier->purchases()->sum('paid_amount');
        $totalRemaining = $supplier->purchases()->sum('remaining_amount');

        return view('fournisseurs.show', compact('supplier', 'totalPurchased', 'totalPaid', 'totalRemaining'));
    }

    public function edit(Supplier $supplier)
    {
        return view('fournisseurs.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'type' => 'required|string|in:company,individual',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], [], [
            'name' => 'nom du fournisseur',
            'type' => 'type de fournisseur',
        ]);

        $supplier->update($validated);

        return redirect()->route('fournisseurs.index')
            ->with('success', "Le fournisseur '{$supplier->name}' a été mis à jour avec succès.");
    }
}
