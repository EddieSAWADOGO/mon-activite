<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #059669;
            padding-bottom: 15px;
        }
        .header table {
            width: 100%;
        }
        .brand {
            font-size: 22px;
            font-weight: bold;
            color: #059669;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
            text-transform: uppercase;
        }
        .invoice-number {
            font-size: 13px;
            font-weight: bold;
            color: #059669;
            text-align: right;
        }
        .details {
            width: 100%;
            margin-bottom: 30px;
        }
        .details table {
            width: 100%;
        }
        .details td {
            vertical-align: top;
            width: 50%;
        }
        .box-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 5px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 10px;
            text-transform: uppercase;
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #cbd5e1;
        }
        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            width: 100%;
            margin-bottom: 40px;
        }
        .totals-table {
            width: 280px;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 5px 8px;
        }
        .total-row {
            font-size: 14px;
            font-weight: bold;
            color: #059669;
            border-top: 2px solid #059669;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="brand">Mon-Activité</div>
                    <div style="font-size: 10px; color: #64748b;">Gestion commerciale & négoce</div>
                </td>
                <td class="text-right">
                    <div class="title">FACTURE</div>
                    <div class="invoice-number">N° {{ $invoice->invoice_number }}</div>
                    <div style="font-size: 10px; color: #64748b;">Date : {{ $invoice->invoice_date->format('d/m/Y H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="details">
        <table>
            <tr>
                <td>
                    <div class="box-title">Émetteur</div>
                    <strong>Mon-Activité Sarl</strong><br>
                    Vendeur : {{ $invoice->createdBy->name }}
                </td>
                <td class="text-right">
                    <div class="box-title">Facturé à</div>
                    <strong>{{ $invoice->customer?->name ?? 'Client de passage' }}</strong><br>
                    @if($invoice->customer)
                        @if($invoice->customer->phone) Tél : {{ $invoice->customer->phone }}<br> @endif
                        @if($invoice->customer->address) Adresse : {{ $invoice->customer->address }}<br> @endif
                        @if($invoice->customer->type === 'entreprise' && $invoice->customer->ifu) IFU : {{ $invoice->customer->ifu }}<br> @endif
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Unité</th>
                <th class="text-right">Qté</th>
                <th class="text-right">P.U.</th>
                <th class="text-right">Total FCFA</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->lines as $line)
                <tr>
                    <td>
                        <strong>{{ $line->product->name }}</strong>
                        @if($line->discount_reason)
                            <div style="font-size: 9px; color: #b45309; font-style: italic;">
                                Motif : {{ $line->discount_reason }}
                            </div>
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

    <div class="totals">
        <table class="totals-table">
            <tr>
                <td>Sous-total :</td>
                <td class="text-right"><strong>{{ number_format($invoice->subtotal_amount, 0, ',', ' ') }} FCFA</strong></td>
            </tr>
            @if($invoice->discount_amount > 0)
                <tr>
                    <td>Remise :</td>
                    <td class="text-right" style="color: #b45309;">- {{ number_format($invoice->discount_amount, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endif
            <tr class="total-row">
                <td>Total Facture :</td>
                <td class="text-right">{{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td>Montant Réglé :</td>
                <td class="text-right" style="color: #059669;">{{ number_format($invoice->paid_amount, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td>Reste à Payer :</td>
                <td class="text-right"><strong>{{ number_format($invoice->remaining_amount, 0, ',', ' ') }} FCFA</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Merci pour votre confiance ! — Document généré à la demande par l'application Mon-Activité.
    </div>

</body>
</html>
