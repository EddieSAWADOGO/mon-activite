<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #0f172a;
            line-height: 1.4;
            margin: 0;
            padding: 24px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #059669;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .brand {
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.5px;
        }
        .subtitle {
            font-size: 9px;
            font-weight: 700;
            color: #059669;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
        }
        .company-info {
            font-size: 9px;
            color: #64748b;
            margin-top: 4px;
        }
        .invoice-box {
            text-align: right;
        }
        .invoice-tag {
            background-color: #d1fae5;
            color: #065f46;
            font-size: 9px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 10px;
            display: inline-block;
            text-transform: uppercase;
        }
        .invoice-title {
            font-size: 15px;
            font-weight: 800;
            margin-top: 4px;
            color: #0f172a;
        }
        .invoice-meta {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }
        .details-table {
            width: 100%;
            margin-bottom: 24px;
        }
        .details-table td {
            vertical-align: top;
            width: 50%;
            padding: 10px;
            background-color: #f8fafc;
            border-radius: 8px;
        }
        .details-title {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        .customer-name {
            font-size: 12px;
            font-weight: 800;
            color: #0f172a;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 8px;
            text-align: left;
            letter-spacing: 0.5px;
        }
        .items-table td {
            padding: 9px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .discount-note {
            font-size: 8.5px;
            color: #b45309;
            font-style: italic;
            margin-top: 2px;
        }
        .summary-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .totals-card {
            width: 260px;
            margin-left: auto;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
        }
        .totals-card table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-card td {
            padding: 4px 0;
            font-size: 10px;
        }
        .total-row {
            font-size: 12px;
            font-weight: 900;
            color: #047857;
            border-top: 1px solid #cbd5e1;
            padding-top: 6px;
        }
        .remaining-row {
            font-size: 11px;
            font-weight: 800;
            color: #991b1b;
            border-top: 1px dashed #cbd5e1;
            padding-top: 4px;
        }
        .footer {
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="vertical-align: top;">
                <div class="brand">MON-ACTIVITÉ SARL</div>
                <div class="subtitle">COMMERCE GÉNÉRAL & NÉGOCE D'INTRANTS</div>
                <div class="company-info">
                    Cotonou, Bénin &bull; Tél: +229 97 00 11 22<br>
                    IFU: 3202112345678 &bull; RCCM: RB/COT/21 B 12345
                </div>
            </td>
            <td class="invoice-box" style="vertical-align: top;">
                <div class="invoice-tag">FACTURE OFFICIELLE</div>
                <div class="invoice-title">N° {{ $invoice->invoice_number }}</div>
                <div class="invoice-meta">Date: {{ $invoice->invoice_date->format('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

    <table class="details-table">
        <tr>
            <td style="margin-right: 10px;">
                <div class="details-title">Émetteur / Vendeur</div>
                <strong>MON-ACTIVITÉ SARL</strong><br>
                Vendeur : {{ $invoice->createdBy->name }}<br>
                Email : {{ $invoice->createdBy->email }}
            </td>
            <td style="text-align: right;">
                <div class="details-title">Facturé à (Client)</div>
                <div class="customer-name">{{ $invoice->customer?->name ?? 'Client de passage' }}</div>
                @if($invoice->customer)
                    @if($invoice->customer->phone) Tél : {{ $invoice->customer->phone }}<br> @endif
                    @if($invoice->customer->address) Adresse : {{ $invoice->customer->address }}<br> @endif
                    @if($invoice->customer->type === 'entreprise' && $invoice->customer->ifu) IFU : {{ $invoice->customer->ifu }}<br> @endif
                @endif
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 40%;">Désignation</th>
                <th style="width: 20%;">Unité</th>
                <th class="text-right" style="width: 10%;">Qté</th>
                <th class="text-right" style="width: 15%;">P.U.</th>
                <th class="text-right" style="width: 15%;">Total FCFA</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->lines as $line)
                <tr>
                    <td>
                        <strong>{{ $line->product->name }}</strong>
                        @if($line->discount_reason)
                            <div class="discount-note">Motif : {{ $line->discount_reason }}</div>
                        @endif
                    </td>
                    <td>{{ $line->stockUnit->name }}</td>
                    <td class="text-right">{{ number_format($line->quantity, 2, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($line->unit_price, 0, ',', ' ') }}</td>
                    <td class="text-right"><strong>{{ number_format($line->subtotal, 0, ',', ' ') }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <td style="vertical-align: top; font-size: 9px; color: #64748b;">
                <strong>Note :</strong> Facture immuable émise à la vente et conservée en base de données.<br>
                Merci pour votre confiance !
            </td>
            <td style="width: 280px; text-align: right;">
                <div class="totals-card">
                    <table>
                        <tr>
                            <td>Sous-total HT :</td>
                            <td class="text-right"><strong>{{ number_format($invoice->subtotal_amount, 0, ',', ' ') }} FCFA</strong></td>
                        </tr>
                        @if($invoice->discount_amount > 0)
                            <tr>
                                <td>Remise :</td>
                                <td class="text-right" style="color: #b45309;">- {{ number_format($invoice->discount_amount, 0, ',', ' ') }} FCFA</td>
                            </tr>
                        @endif
                        <tr class="total-row">
                            <td>NET À PAYER :</td>
                            <td class="text-right">{{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        <tr>
                            <td>Montant Réglé :</td>
                            <td class="text-right" style="color: #059669;">{{ number_format($invoice->paid_amount, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        <tr class="remaining-row">
                            <td>SOLDE DÛ :</td>
                            <td class="text-right">{{ number_format($invoice->remaining_amount, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        MON-ACTIVITÉ SARL — Cotonou, Bénin &bull; Document officiel généré par le système d'information.
    </div>

</body>
</html>
