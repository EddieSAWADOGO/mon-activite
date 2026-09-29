<?php

namespace App\Domain\Paiements\Services;

use App\Domain\Achats\Models\Purchase;
use App\Domain\Facturation\Models\Invoice;
use App\Domain\Paiements\Models\Payment;
use App\Models\User;
use App\Support\Enums\InvoiceStatus;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PaymentService
{
    public function recordPayment(array $data, User $user): Payment
    {
        return DB::transaction(function () use ($data, $user) {
            /** @var Invoice $invoice */
            $invoice = Invoice::where('id', $data['invoice_id'])->lockForUpdate()->firstOrFail();

            $amount = (float) $data['amount'];

            if ($amount <= 0) {
                throw new InvalidArgumentException("Le montant du règlement doit être supérieur à zéro.");
            }

            if ($amount > (float) $invoice->remaining_amount) {
                $formattedAmount = number_format($amount, 0, ',', ' ');
                $formattedRemaining = number_format((float) $invoice->remaining_amount, 0, ',', ' ');
                throw new InvalidArgumentException("Le montant du règlement ({$formattedAmount} FCFA) dépasse le reste à payer de la facture ({$formattedRemaining} FCFA).");
            }

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $amount,
                'payment_date' => $data['payment_date'] ?? now(),
                'payment_method' => $data['payment_method'],
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by_user_id' => $user->id,
            ]);

            $totalPaid = (float) $invoice->payments()->sum('amount');
            $newPaidAmount = min($totalPaid, (float) $invoice->total_amount);
            $newRemainingAmount = max(0, (float) $invoice->total_amount - $newPaidAmount);

            $newStatus = InvoiceStatus::UNPAID;
            if ($newRemainingAmount <= 0) {
                $newStatus = InvoiceStatus::PAID;
            } elseif ($newPaidAmount > 0) {
                $newStatus = InvoiceStatus::PARTIALLY_PAID;
            }

            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'remaining_amount' => $newRemainingAmount,
                'status' => $newStatus,
                'payment_method' => $data['payment_method'],
            ]);

            if ($invoice->sale) {
                $invoice->sale->update([
                    'paid_amount' => $newPaidAmount,
                    'remaining_amount' => $newRemainingAmount,
                ]);
            }

            return $payment;
        });
    }

    public function recordPurchasePayment(array $data, User $user): Payment
    {
        return DB::transaction(function () use ($data, $user) {
            /** @var Purchase $purchase */
            $purchase = Purchase::where('id', $data['purchase_id'])->lockForUpdate()->firstOrFail();

            $amount = (float) $data['amount'];

            if ($amount <= 0) {
                throw new InvalidArgumentException("Le montant du règlement doit être supérieur à zéro.");
            }

            if ($amount > (float) $purchase->remaining_amount) {
                $formattedAmount = number_format($amount, 0, ',', ' ');
                $formattedRemaining = number_format((float) $purchase->remaining_amount, 0, ',', ' ');
                throw new InvalidArgumentException("Le montant du règlement ({$formattedAmount} FCFA) dépasse le reste à payer sur cet achat ({$formattedRemaining} FCFA).");
            }

            $payment = Payment::create([
                'purchase_id' => $purchase->id,
                'invoice_id' => null,
                'amount' => $amount,
                'payment_date' => $data['payment_date'] ?? now(),
                'payment_method' => $data['payment_method'],
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by_user_id' => $user->id,
            ]);

            $newPaidAmount = (float) $purchase->paid_amount + $amount;
            $newRemainingAmount = max(0, (float) $purchase->total_amount - $newPaidAmount);

            $purchase->update([
                'paid_amount' => $newPaidAmount,
                'remaining_amount' => $newRemainingAmount,
            ]);

            return $payment;
        });
    }
}
