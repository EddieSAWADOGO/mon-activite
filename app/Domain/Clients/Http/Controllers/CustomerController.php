<?php

namespace App\Domain\Clients\Http\Controllers;

use App\Domain\Clients\Http\Requests\StoreCustomerRequest;
use App\Domain\Clients\Http\Requests\UpdateCustomerRequest;
use App\Domain\Clients\Models\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Customer::class);

        $query = Customer::query()->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            if ($type === 'particulier') {
                $query->whereIn('type', ['particulier', 'individual']);
            } elseif ($type === 'entreprise') {
                $query->whereIn('type', ['entreprise', 'company']);
            } else {
                $query->where('type', $type);
            }
        }

        $customers = $query->paginate(15)->withQueryString();

        return view('clients.index', compact('customers'));
    }

    public function create(): View
    {
        $this->authorize('create', Customer::class);

        return view('clients.create');
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $data['is_active'] ?? true;

        $customer = Customer::create($data);

        return redirect()->route('clients.show', $customer)
            ->with('success', 'Client enregistré avec succès.');
    }

    public function show(Customer $customer): View
    {
        $this->authorize('view', $customer);

        $customer->load([
            'sales' => fn ($q) => $q->latest('sale_date')->latest('id'),
            'sales.createdBy',
            'invoices' => fn ($q) => $q->latest('invoice_date')->latest('id'),
        ]);

        $totalPurchased = $customer->sales->sum('total_amount');
        $totalPaid = $customer->sales->sum('paid_amount');
        $remainingDue = $customer->sales->sum('remaining_amount');
        $lastOrderDate = $customer->sales->max('sale_date');

        return view('clients.show', compact('customer', 'totalPurchased', 'totalPaid', 'remainingDue', 'lastOrderDate'));
    }

    public function statementPdf(Customer $customer)
    {
        $this->authorize('view', $customer);

        $customer->load([
            'sales' => fn ($q) => $q->latest('sale_date')->latest('id')->with(['invoice', 'lines.product', 'lines.stockUnit']),
            'invoices' => fn ($q) => $q->latest('invoice_date')->latest('id')->with('payments'),
        ]);

        $company = \App\Domain\Settings\Models\CompanySetting::getSettings();

        $totalPurchased = (float) $customer->sales->sum('total_amount');
        $totalPaid = (float) $customer->sales->sum('paid_amount');
        $remainingDue = (float) $customer->sales->sum('remaining_amount');

        $payments = \App\Domain\Paiements\Models\Payment::whereHas('invoice', function ($q) use ($customer) {
            $q->where('customer_id', $customer->id);
        })->with(['invoice', 'createdBy'])->latest('payment_date')->latest('id')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('clients.statement-pdf', compact(
            'customer',
            'company',
            'totalPurchased',
            'totalPaid',
            'remainingDue',
            'payments'
        ))->setPaper('a4', 'portrait');

        $cleanName = preg_replace('/[^A-Za-z0-9_]/', '_', $customer->name);
        return $pdf->download("Releve_compte_{$cleanName}.pdf");
    }

    public function edit(Customer $customer): View
    {
        $this->authorize('update', $customer);

        return view('clients.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return redirect()->route('clients.show', $customer)
            ->with('success', 'Informations du client mises à jour.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $this->authorize('delete', $customer);

        if ($customer->sales()->exists()) {
            return back()->with('error', 'Impossible de supprimer un client qui possède déjà un historique de transactions.');
        }

        $customer->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client supprimé avec succès.');
    }
}
