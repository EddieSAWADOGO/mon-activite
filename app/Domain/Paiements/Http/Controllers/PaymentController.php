<?php

namespace App\Domain\Paiements\Http\Controllers;

use App\Domain\Achats\Models\Purchase;
use App\Domain\Facturation\Models\Invoice;
use App\Domain\Paiements\Http\Requests\StorePaymentRequest;
use App\Domain\Paiements\Http\Requests\StorePurchasePaymentRequest;
use App\Domain\Paiements\Models\Payment;
use App\Domain\Paiements\Services\PaymentService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Payment::class);

        $query = Payment::with(['invoice.customer', 'purchase.supplier', 'createdBy'])->latest('payment_date')->latest('id');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('invoice', function ($iq) use ($search) {
                    $iq->where('invoice_number', 'like', "%{$search}%")
                      ->orWhereHas('customer', function ($cq) use ($search) {
                          $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('contact_person', 'like', "%{$search}%");
                      });
                })->orWhereHas('purchase', function ($pq) use ($search) {
                    $pq->where('purchase_number', 'like', "%{$search}%")
                      ->orWhereHas('supplier', function ($sq) use ($search) {
                          $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                      });
                })->orWhere('reference', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            if ($type === 'customer' || $type === 'client') {
                $query->whereNotNull('invoice_id');
            } elseif ($type === 'supplier' || $type === 'fournisseur') {
                $query->whereNotNull('purchase_id');
            }
        }

        if ($method = $request->input('payment_method')) {
            $query->where('payment_method', $method);
        }

        if ($startDate = $request->input('start_date')) {
            $query->whereDate('payment_date', '>=', $startDate);
        }

        if ($endDate = $request->input('end_date')) {
            $query->whereDate('payment_date', '<=', $endDate);
        }

        $payments = $query->paginate(15)->withQueryString();

        return view('paiements.index', compact('payments'));
    }

    public function create(Invoice $invoice): View|RedirectResponse
    {
        $this->authorize('create', Payment::class);

        if ((float) $invoice->remaining_amount <= 0) {
            return redirect()->route('factures.show', $invoice)
                ->with('error', 'Cette facture est déjà entièrement réglée.');
        }

        return view('paiements.create', compact('invoice'));
    }

    public function store(StorePaymentRequest $request, PaymentService $paymentService): RedirectResponse
    {
        try {
            $payment = $paymentService->recordPayment($request->validated(), $request->user());

            return redirect()->route('factures.show', $payment->invoice_id)
                ->with('success', 'Le règlement de ' . number_format($payment->amount, 0, ',', ' ') . ' FCFA a été enregistré avec succès.');
        } catch (InvalidArgumentException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function createForPurchase(Purchase $purchase): View|RedirectResponse
    {
        $this->authorize('create', Payment::class);
        $this->authorize('create', Purchase::class);

        if ((float) $purchase->remaining_amount <= 0) {
            return redirect()->route('achats.show', $purchase)
                ->with('error', 'Cet achat est déjà entièrement réglé.');
        }

        return view('paiements.create-purchase', compact('purchase'));
    }

    public function storeForPurchase(StorePurchasePaymentRequest $request, PaymentService $paymentService): RedirectResponse
    {
        $this->authorize('create', Payment::class);
        $this->authorize('create', Purchase::class);
        try {
            $payment = $paymentService->recordPurchasePayment($request->validated(), $request->user());

            return redirect()->route('achats.show', $payment->purchase_id)
                ->with('success', 'Le règlement de ' . number_format($payment->amount, 0, ',', ' ') . ' FCFA a été enregistré avec succès.');
        } catch (InvalidArgumentException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}
