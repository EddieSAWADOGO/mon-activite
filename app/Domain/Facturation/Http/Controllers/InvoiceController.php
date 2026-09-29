<?php

namespace App\Domain\Facturation\Http\Controllers;

use App\Domain\Facturation\Models\Invoice;
use App\Domain\Settings\Models\CompanySetting;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Invoice::class);

        $query = Invoice::with(['customer', 'createdBy'])->latest('invoice_date');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($startDate = $request->input('start_date')) {
            $query->whereDate('invoice_date', '>=', $startDate);
        }

        if ($endDate = $request->input('end_date')) {
            $query->whereDate('invoice_date', '<=', $endDate);
        }

        $invoices = $query->paginate(15)->withQueryString();

        return view('factures.index', compact('invoices'));
    }

    public function show(Invoice $invoice): View
    {
        $this->authorize('view', $invoice);

        $invoice->load(['customer', 'createdBy', 'sale', 'lines.product', 'lines.stockUnit']);
        $company = CompanySetting::getSettings();

        return view('factures.show', compact('invoice', 'company'));
    }

    public function pdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $invoice->load(['customer', 'createdBy', 'lines.product', 'lines.stockUnit']);
        $company = CompanySetting::getSettings();

        $pdf = Pdf::loadView('factures.pdf', compact('invoice', 'company'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("Facture_{$invoice->invoice_number}.pdf");
    }

    public function whatsapp(Invoice $invoice): RedirectResponse
    {
        $this->authorize('view', $invoice);

        $customerPhone = $invoice->customer?->whatsapp ?? $invoice->customer?->phone;
        $cleanPhone = $customerPhone ? preg_replace('/[^0-9]/', '', $customerPhone) : '';

        $company = CompanySetting::getSettings();
        $message = sprintf(
            "Bonjour %s, voici votre facture n° %s du %s émise par %s d'un montant total de %s FCFA (Reste à payer : %s FCFA). Merci pour votre confiance !",
            $invoice->customer?->name ?? 'Client',
            $invoice->invoice_number,
            $invoice->invoice_date->format('d/m/Y'),
            $company->name,
            number_format($invoice->total_amount, 0, ',', ' '),
            number_format($invoice->remaining_amount, 0, ',', ' ')
        );

        $url = "https://wa.me/{$cleanPhone}?text=" . urlencode($message);

        return redirect()->away($url);
    }
}
