<?php

namespace App\Domain\Facturation\Services;

use App\Domain\Facturation\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class InvoicePdfService
{
    /**
     * Génère et retourne un PDF temporaire en téléchargement direct/stream sans persistance serveur.
     */
    public function downloadPdf(Invoice $invoice): Response
    {
        $invoice->load(['sale', 'customer', 'createdBy', 'lines.product', 'lines.stockUnit']);

        $pdf = Pdf::loadView('factures.pdf', [
            'invoice' => $invoice,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("Facture_{$invoice->invoice_number}.pdf");
    }

    /**
     * Génère un lien wa.me prérempli pour envoyer la facture au client via WhatsApp.
     */
    public function generateWhatsAppLink(Invoice $invoice): string
    {
        $invoice->load('customer');
        $phone = $invoice->customer?->whatsapp ?? $invoice->customer?->phone ?? '';

        // Nettoyer le numéro de téléphone (enlever espaces et caractères non numériques)
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        $formattedTotal = number_format($invoice->total_amount, 0, ',', ' ');
        $message = "Bonjour " . ($invoice->customer?->name ?? 'Client') . ",\n\n";
        $message .= "Voici le récapitulatif de votre facture n° {$invoice->invoice_number} du " . $invoice->invoice_date->format('d/m/Y') . " :\n";
        $message .= "- Montant total : {$formattedTotal} FCFA\n";
        $message .= "- Statut : " . $invoice->status->label() . "\n\n";
        $message .= "Merci de votre confiance !";

        $encodedMessage = urlencode($message);

        if (! empty($cleanPhone)) {
            return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
        }

        return "https://wa.me/?text={$encodedMessage}";
    }
}
