<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Relevé de Compte — {{ $customer->name }}</title>
    <style>
        @page {
            margin-top: 150px;
            margin-bottom: 60px;
            margin-left: 30px;
            margin-right: 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #0f172a;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* En-tête fixe */
        .pdf-header {
            position: fixed;
            top: -130px;
            left: 0;
            right: 0;
            height: 110px;
            text-align: center;
            border-bottom: 3px solid #0f172a;
            padding-bottom: 8px;
        }
        .pdf-header .doc-title {
            font-size: 26px;
            font-weight: 900;
            letter-spacing: 1px;
            color: #0f172a;
            text-transform: uppercase;
            margin-top: 8px;
        }
        .pdf-header .doc-subtitle {
            font-size: 14px;
            font-weight: 700;
            color: #475569;
            margin-top: 5px;
        }

        /* Pied de page fixe */
        .pdf-footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            height: 35px;
            border-top: 2px solid #0f172a;
            padding-top: 6px;
            font-size: 11px;
            font-weight: 700;
            color: #475569;
        }
        .pdf-footer table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Company & Client Header Grid */
        .info-grid {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 15px;
        }
        .info-grid td {
            vertical-align: top;
        }
        .company-name {
            font-size: 22px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
        }
        .company-detail {
            font-size: 12px;
            font-weight: 700;
            color: #334155;
            margin-top: 2px;
        }
        .customer-title {
            font-size: 11px;
            font-weight: 900;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .customer-name {
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
            margin-top: 2px;
        }
        .customer-detail {
            font-size: 12px;
            font-weight: 700;
            color: #1e293b;
            margin-top: 2px;
        }

        /* Financial Metrics Summary Box */
        .metrics-box {
            width: 100%;
            border: 2px solid #0f172a;
            border-radius: 8px;
            background-color: #f8fafc;
            margin-bottom: 22px;
            padding: 12px;
        }
        .metrics-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        .metrics-table td {
            width: 33.33%;
            padding: 4px 8px;
        }
        .metric-label {
            font-size: 11px;
            font-weight: 900;
            color: #64748b;
            text-transform: uppercase;
        }
        .metric-value {
            font-size: 18px;
            font-weight: 900;
            color: #0f172a;
            margin-top: 2px;
        }
        .metric-value.due {
            color: #991b1b;
        }

        /* Section Headings */
        .section-title {
            font-size: 14px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 4px;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            padding: 8px;
            text-align: left;
        }
        .data-table td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
            font-weight: 700;
        }
        .text-right {
            text-align: right;
        }
        .badge-paid {
            color: #047857;
            font-weight: 900;
        }
        .badge-partial {
            color: #b45309;
            font-weight: 900;
        }
        .badge-unpaid {
            color: #b91c1c;
            font-weight: 900;
        }
    </style>
</head>
<body>

    <!-- En-tête fixe -->
    <div class="pdf-header">
        <div class="doc-title">RELEVÉ DE COMPTE CLIENT</div>
        <div class="doc-subtitle">Émis le {{ now()->format('d/m/Y à H:i') }} par {{ $company->name }}</div>
    </div>

    <!-- Pied de page fixe -->
    <div class="pdf-footer">
        <table>
            <tr>
                <td style="text-align: left;">
                    <strong>{{ $company->name }}</strong>
                    @if($company->ifu) &bull; IFU : {{ $company->ifu }} @endif
                    @if($company->rccm) &bull; RCCM : {{ $company->rccm }} @endif
                </td>
                <td style="text-align: right;">
                    Document officiel de synthèse client
                </td>
            </tr>
        </table>
    </div>

    <!-- Info Grid: Émetteur & Client -->
    <table class="info-grid">
        <tr>
            <td style="width: 50%;">
                <div class="company-name">{{ $company->name }}</div>
                @if($company->address)<div class="company-detail">Adresse : {{ $company->address }}</div>@endif
                @if($company->phone)<div class="company-detail">Téléphone : {{ $company->phone }}</div>@endif
                @if($company->email)<div class="company-detail">E-mail : {{ $company->email }}</div>@endif
                @if($company->ifu)<div class="company-detail">N° IFU : {{ $company->ifu }}</div>@endif
            </td>
            <td style="width: 50%; text-align: right;">
                <div class="customer-title">RELEVÉ DE COMPTE POUR :</div>
                <div class="customer-name">{{ $customer->name }}</div>
                @if($customer->phone)<div class="customer-detail">Tél : {{ $customer->phone }}</div>@endif
                @if($customer->address)<div class="customer-detail">Adresse : {{ $customer->address }}</div>@endif
                @if($customer->ifu)<div class="customer-detail">N° IFU Client : {{ $customer->ifu }}</div>@endif
            </td>
        </tr>
    </table>

    <!-- Synthèse financière globale -->
    <div class="metrics-box">
        <table class="metrics-table">
            <tr>
                <td style="border-right: 1px solid #cbd5e1;">
                    <div class="metric-label">TOTAL ACHATS EFFECTUÉS</div>
                    <div class="metric-value">{{ number_format($totalPurchased, 0, ',', ' ') }} FCFA</div>
                </td>
                <td style="border-right: 1px solid #cbd5e1;">
                    <div class="metric-label">TOTAL MONTANT RÉGLÉ</div>
                    <div class="metric-value" style="color: #047857;">{{ number_format($totalPaid, 0, ',', ' ') }} FCFA</div>
                </td>
                <td>
                    <div class="metric-label">SOLDE TOTAL RESTANT DÛ</div>
                    <div class="metric-value {{ $remainingDue > 0 ? 'due' : '' }}">{{ number_format($remainingDue, 0, ',', ' ') }} FCFA</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Section 1: Historique des Ventes & Factures -->
    <div class="section-title">1. HISTORIQUE DES VENTES ET FACTURES ({{ $customer->sales->count() }})</div>
    @if($customer->sales->isEmpty())
        <p style="font-style: italic; color: #64748b;">Aucune transaction enregistrée pour ce client.</p>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 15%;">Date</th>
                    <th style="width: 20%;">N° Vente / Facture</th>
                    <th class="text-right" style="width: 22%;">Montant Total</th>
                    <th class="text-right" style="width: 22%;">Montant Réglé</th>
                    <th class="text-right" style="width: 21%;">Reliquat Dû</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customer->sales as $sale)
                    <tr>
                        <td>{{ $sale->sale_date->format('d/m/Y H:i') }}</td>
                        <td>
                            <strong>{{ $sale->sale_number }}</strong>
                            @if($sale->invoice)
                                <div style="font-size: 10px; color: #64748b;">Fact. {{ $sale->invoice->invoice_number }}</div>
                            @endif
                        </td>
                        <td class="text-right"><strong>{{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA</strong></td>
                        <td class="text-right" style="color: #047857;">{{ number_format($sale->paid_amount, 0, ',', ' ') }} FCFA</td>
                        <td class="text-right">
                            @if($sale->remaining_amount > 0)
                                <span class="badge-unpaid">{{ number_format($sale->remaining_amount, 0, ',', ' ') }} FCFA</span>
                            @else
                                <span class="badge-paid">RÉGLÉ (0 FCFA)</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Section 2: Historique des Règlements Effectués -->
    <div class="section-title">2. HISTORIQUE DES ENCAISSEMENTS & RÈGLEMENTS ({{ $payments->count() }})</div>
    @if($payments->isEmpty())
        <p style="font-style: italic; color: #64748b;">Aucun règlement enregistré pour le moment.</p>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 18%;">Date Règlement</th>
                    <th style="width: 22%;">N° Facture</th>
                    <th style="width: 20%;">Mode de Paiement</th>
                    <th style="width: 20%;">Référence</th>
                    <th class="text-right" style="width: 20%;">Montant Versé</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_date->format('d/m/Y H:i') }}</td>
                        <td><strong>{{ $payment->invoice?->invoice_number }}</strong></td>
                        <td>{{ $payment->payment_method }}</td>
                        <td style="font-family: monospace;">{{ $payment->reference ?: '-' }}</td>
                        <td class="text-right" style="color: #047857; font-weight: 900;">
                            + {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</body>
</html>
