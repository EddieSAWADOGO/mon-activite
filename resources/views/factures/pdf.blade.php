<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin-top: 165px;
            margin-bottom: 70px;
            margin-left: 30px;
            margin-right: 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #0f172a;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            position: relative;
        }

        /* Logo en fond : très grand, centré, incliné */
        .pdf-watermark {
            position: absolute;
            top: 12%;
            left: -15%;
            width: 130%;
            text-align: center;
            opacity: 0.09;
            transform: rotate(-15deg);
            z-index: -1;
        }

        /* En-tête fixe */
        .pdf-header {
            position: fixed;
            top: -150px;
            left: 0;
            right: 0;
            height: 130px;
            text-align: center;
            border-bottom: 3px solid #0f172a;
            padding-bottom: 8px;
        }
        .pdf-header .doc-line {
            font-size: 36px;
            font-weight: 900;
            letter-spacing: 3px;
            color: #0f172a;
            text-transform: uppercase;
            margin-top: 10px;
            white-space: nowrap;
        }
        .pdf-header .doc-line .doc-ref {
            font-family: monospace;
            letter-spacing: 1px;
            margin-left: 14px;
        }
        .pdf-header .doc-date {
            font-size: 16px;
            font-weight: 700;
            font-style: italic;
            color: #334155;
            margin-top: 10px;
        }

        /* Pied de page fixe */
        .pdf-footer {
            position: fixed;
            bottom: -50px;
            left: 0;
            right: 0;
            height: 42px;
            border-top: 2px solid #0f172a;
            padding-top: 8px;
            font-size: 12px;
            font-weight: 700;
            color: #334155;
        }
        .pdf-footer table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Bloc émetteur */
        .company-section {
            width: 100%;
            border-bottom: 1.5px solid #cbd5e1;
            padding-bottom: 16px;
            margin-bottom: 18px;
        }
        .brand-name {
            font-size: 28px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: -0.5px;
        }
        .brand-tagline {
            font-size: 15px;
            font-weight: 900;
            color: #047857;
            text-transform: uppercase;
            margin-top: 3px;
            margin-bottom: 8px;
        }
        .company-detail-item {
            font-size: 14.5px;
            font-weight: 700;
            color: #1e293b;
            margin-top: 4px;
        }

        /* Bloc client */
        .client-section {
            width: 100%;
            border-bottom: 1.5px solid #cbd5e1;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .client-title {
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            color: #475569;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .customer-name {
            font-size: 21px;
            font-weight: 900;
            color: #0f172a;
        }
        .customer-details {
            font-size: 14.5px;
            font-weight: 700;
            color: #1e293b;
            margin-top: 5px;
        }

        /* Tableau des lignes */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
        }
        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            padding: 12px 10px;
            text-align: left;
        }
        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #cbd5e1;
            font-size: 14.5px;
            font-weight: 700;
        }
        .text-right {
            text-align: right;
        }

        /* Totaux */
        .totals-section {
            width: 100%;
            margin-top: 18px;
        }
        .totals-table {
            width: 360px;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 8px 0;
            font-size: 15px;
        }
        .total-row {
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
            border-top: 3px solid #0f172a;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <!-- Logo / Filigrane de fond (très grand) -->
    <div class="pdf-watermark">
        @if($company->logo_path && file_exists(public_path($company->logo_path)))
            <img src="{{ public_path($company->logo_path) }}" style="width: 900px; max-width: 100%; height: auto;">
        @else
            <div style="font-size: 230px; font-weight: 900; color: #0f172a; border: 16px solid #0f172a; padding: 35px 70px; border-radius: 40px; display: inline-block;">
                {{ strtoupper(substr($company->name, 0, 4)) }}
            </div>
            <div style="font-size: 44px; font-weight: 900; color: #0f172a; margin-top: 20px; text-transform: uppercase;">
                {{ $company->name }}
            </div>
        @endif
    </div>

    <!-- En-tête fixe : FACTURE + N° sur la même ligne, date en italique dessous -->
    <div class="pdf-header">
        <div class="doc-line">
            FACTURE <span class="doc-ref">N° {{ $invoice->invoice_number }}</span>
        </div>
        <div class="doc-date">Date d'émission : {{ $invoice->invoice_date->format('d/m/Y à H:i') }}</div>
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
                    Facturé par : <strong>{{ $invoice->createdBy->name }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <!-- Bloc émetteur -->
    <div class="company-section">
        <table style="width: 100%;">
            <tr>
                <td style="vertical-align: top;">
                    <div class="brand-name">{{ $company->name }}</div>
                    @if($company->tagline)
                        <div class="brand-tagline">{{ $company->tagline }}</div>
                    @endif
                    <div class="company-info">
                        @if($company->address)
                            <div class="company-detail-item">Adresse : <strong>{{ $company->address }}</strong></div>
                        @endif
                        @if($company->phone)
                            <div class="company-detail-item">Téléphone : <strong>{{ $company->phone }}</strong></div>
                        @endif
                        @if($company->email)
                            <div class="company-detail-item">E-mail : <strong>{{ $company->email }}</strong></div>
                        @endif
                        @if($company->ifu)
                            <div class="company-detail-item">N° IFU : <strong>{{ $company->ifu }}</strong></div>
                        @endif
                        @if($company->rccm)
                            <div class="company-detail-item">N° RCCM : <strong>{{ $company->rccm }}</strong></div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Bloc client -->
    <div class="client-section">
        <table style="width: 100%;">
            <tr>
                <td style="vertical-align: top;">
                    <div class="client-title">FACTURÉ À :</div>
                    <div class="customer-name">{{ $invoice->customer?->name ?? 'Client au comptoir' }}</div>
                    @if($invoice->customer)
                        <div class="customer-details">
                            @if($invoice->customer->phone)
                                <div>Téléphone : <strong>{{ $invoice->customer->phone }}</strong></div>
                            @endif
                            @if($invoice->customer->address)
                                <div>Adresse : <strong>{{ $invoice->customer->address }}</strong></div>
                            @endif
                            @if($invoice->customer->type === 'entreprise')
                                @if($invoice->customer->contact_person)
                                    <div>Représentant : <strong>{{ $invoice->customer->contact_person }}</strong></div>
                                @endif
                                @if($invoice->customer->ifu)
                                    <div style="font-weight: 900; font-family: monospace; margin-top: 4px; font-size: 15px;">N° IFU Client : {{ $invoice->customer->ifu }}</div>
                                @endif
                                @if($invoice->customer->rccm)
                                    <div style="font-family: monospace; font-size: 14px;">N° RCCM Client : {{ $invoice->customer->rccm }}</div>
                                @endif
                            @endif
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Tableau des lignes -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 37%;">Désignation</th>
                <th style="width: 14%;">Unité</th>
                <th class="text-right" style="width: 10%;">Qté</th>
                <th class="text-right" style="width: 16%;">P.U. (FCFA)</th>
                <th class="text-right" style="width: 18%;">Total FCFA</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->lines as $index => $line)
                <tr>
                    <td style="text-align: center; color: #64748b; font-weight: 800;">{{ $index + 1 }}</td>
                    <td>
                        <strong style="font-size: 15.5px; color: #0f172a;">{{ $line->product->name }}</strong>
                    </td>
                    <td style="font-weight: 800;">{{ $line->stockUnit->name }}</td>
                    <td class="text-right"><strong style="font-size: 15.5px;">{{ number_format($line->quantity, 2, ',', ' ') }}</strong></td>
                    <td class="text-right">{{ number_format($line->unit_price, 0, ',', ' ') }}</td>
                    <td class="text-right"><strong style="font-size: 16px; color: #0f172a;">{{ number_format($line->subtotal, 0, ',', ' ') }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totaux & Situation de Crédit du Client -->
    <div class="totals-section">
        <table class="totals-table">
            @if($invoice->discount_amount > 0)
                <tr>
                    <td style="font-size: 15px; font-weight: 800;">Remise :</td>
                    <td class="text-right" style="font-size: 15px; font-weight: 900; color: #b45309;">- {{ number_format($invoice->discount_amount, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endif
            <tr class="total-row">
                <td style="font-size: 18px; font-weight: 900;">Montant Total Facture :</td>
                <td class="text-right" style="font-size: 21px; font-weight: 900;">{{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>

    <!-- Point de Crédit / Situation Financière du Client -->
    <div style="margin-top: 20px; border: 2px solid #0f172a; border-radius: 8px; overflow: hidden; page-break-inside: avoid;">
        <div style="background-color: #0f172a; color: #ffffff; padding: 7px 12px; font-size: 13px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px;">
            SITUATION DE CRÉDIT & COMPTE CLIENT
        </div>
        <table style="width: 100%; border-collapse: collapse; font-size: 13px; font-weight: 700; background-color: #f8fafc;">
            <tr>
                <td style="width: 35%; padding: 10px 12px; border-right: 1px solid #cbd5e1; vertical-align: top;">
                    <div style="color: #64748b; font-size: 10.5px; font-weight: 800; text-transform: uppercase;">Reliquat cette facture (N° {{ $invoice->invoice_number }}) :</div>
                    <div style="font-size: 15px; font-weight: 900; color: #0f172a; margin-top: 2px;">{{ number_format($invoice->remaining_amount, 0, ',', ' ') }} FCFA</div>
                </td>
                <td style="width: 35%; padding: 10px 12px; border-right: 1px solid #cbd5e1; vertical-align: top;">
                    <div style="color: #64748b; font-size: 10.5px; font-weight: 800; text-transform: uppercase;">Anciens crédits (Factures précédentes) :</div>
                    <div style="font-size: 15px; font-weight: 900; color: #0f172a; margin-top: 2px;">{{ number_format($previousCustomerDebt, 0, ',', ' ') }} FCFA</div>
                </td>
                <td style="width: 30%; padding: 10px 12px; vertical-align: top; text-align: right; background-color: #f1f5f9;">
                    <div style="color: #0f172a; font-size: 10.5px; font-weight: 900; text-transform: uppercase;">TOTAL DÛ GLOBAL :</div>
                    <div style="font-size: 16px; font-weight: 900; color: #0f172a; margin-top: 2px;">{{ number_format($invoice->remaining_amount + $previousCustomerDebt, 0, ',', ' ') }} FCFA</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
