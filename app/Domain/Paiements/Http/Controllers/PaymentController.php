<?php

namespace App\Domain\Paiements\Http\Controllers;

use App\Domain\Facturation\Models\Invoice;
use App\Domain\Paiements\Http\Requests\StorePaymentRequest;
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

        $query = Payment::with(['invoice.customer', 'createdBy'])->latest('payment_date');

        if ($search = $request->input('search')) {
            $query->whereHas('invoice', function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
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
}
