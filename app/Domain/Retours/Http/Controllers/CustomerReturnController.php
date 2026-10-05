<?php

namespace App\Domain\Retours\Http\Controllers;

use App\Domain\Clients\Models\Customer;
use App\Domain\Facturation\Models\Invoice;
use App\Domain\Produits\Models\Product;
use App\Domain\Retours\Http\Requests\StoreCustomerReturnRequest;
use App\Domain\Retours\Models\CustomerReturn;
use App\Domain\Retours\Services\CustomerReturnService;
use App\Http\Controllers\Controller;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerReturnController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', CustomerReturn::class);

        $query = CustomerReturn::with(['product', 'stockUnit', 'customer', 'invoice', 'createdBy', 'validatedBy'])->latest('return_date')->latest('id');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('return_number', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $returns = $query->paginate(15)->withQueryString();

        return view('retours.index', compact('returns'));
    }

    public function create(): View
    {
        $this->authorize('create', CustomerReturn::class);

        $products = Product::with('activeUnits')->where('is_active', true)->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $recentInvoices = Invoice::with('customer')->latest()->limit(50)->get();

        return view('retours.create', compact('products', 'customers', 'recentInvoices'));
    }

    public function store(StoreCustomerReturnRequest $request, CustomerReturnService $service): RedirectResponse
    {
        $return = $service->recordReturn($request->validated(), $request->user());

        return redirect()->route('retours.show', $return)
            ->with('success', "Le retour client N° {$return->return_number} a été enregistré. Il est en attente de validation.");
    }

    public function show(CustomerReturn $customerReturn): View
    {
        $this->authorize('view', $customerReturn);

        $customerReturn->load(['product', 'stockUnit', 'customer', 'invoice', 'createdBy', 'validatedBy']);

        return view('retours.show', compact('customerReturn'));
    }

    public function restock(CustomerReturn $customerReturn, CustomerReturnService $service, Request $request): RedirectResponse
    {
        $this->authorize('validate', $customerReturn);

        try {
            $service->validateAndRestock($customerReturn, $request->user());

            return redirect()->route('retours.show', $customerReturn)
                ->with('success', "Le retour N° {$customerReturn->return_number} a été validé et le stock a été réintégré avec succès.");
        } catch (DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function discard(CustomerReturn $customerReturn, CustomerReturnService $service, Request $request): RedirectResponse
    {
        $this->authorize('validate', $customerReturn);

        try {
            $service->validateAndDiscard($customerReturn, $request->user(), $request->input('notes'));

            return redirect()->route('retours.show', $customerReturn)
                ->with('success', "Le retour N° {$customerReturn->return_number} a été validé et déclaré en perte (non réintégré en stock).");
        } catch (DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
