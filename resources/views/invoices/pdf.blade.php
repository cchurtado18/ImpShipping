<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 15mm;
            size: letter;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            margin-bottom: 15px;
        }
        .company-info {
            margin-bottom: 12px;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 6px;
        }
        .invoice-number {
            background-color: #2563eb;
            color: white;
            padding: 8px 16px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 12px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .info-col {
            display: table-cell;
            width: 50%;
            padding: 10px;
            background-color: #f9fafb;
            vertical-align: top;
        }
        .info-col h3 {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 6px;
            color: #1f2937;
        }
        .info-col p {
            margin: 3px 0;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        th {
            background-color: #0d9488;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }
        td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .grand-total {
            font-size: 12px;
            color: #059669;
        }
        .disclaimer {
            background-color: #fef3c7;
            border-left: 3px solid #f59e0b;
            padding: 8px;
            margin-top: 12px;
            font-size: 9px;
            line-height: 1.3;
        }
        .footer {
            text-align: center;
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-info">
            <div class="company-name">IMPERIO SHIPPING CORP.</div>
            <p><strong>Teléfono:</strong> +1 (305) 890-4018</p>
            <p><strong>Email:</strong> imperioshipping@usa.com</p>
            <p><strong>Dirección:</strong> Residencial Vistas de Equípela, casa A3-11, Esquipulas, Managua</p>
        </div>
        
        <div class="company-logo" style="text-align: right; margin-bottom: 10px;">
            <img src="{{ public_path('images/logos/ImpShipping.jpeg') }}" alt="IMPEF Logo" style="height: 50px; width: 50px; border-radius: 6px; object-fit: cover;">
        </div>
        <div class="invoice-number">
            Invoice #{{ $invoice->invoice_number }}
        </div>
        
        <p><strong>Date:</strong> {{ $invoice->invoice_date->format('d/m/Y') }}</p>
        @if($invoice->due_date)
            <p><strong>Due Date:</strong> {{ $invoice->due_date->format('d/m/Y') }}</p>
        @endif
        <p><strong>Delivery Time:</strong> {{ $invoice->terms }}</p>
    </div>

    <!-- Sender and Recipient Information -->
    <div class="info-grid">
        <div class="info-col">
            <h3>Sender</h3>
            <p><strong>Nombre:</strong> {{ $invoice->sender_name }}</p>
            <p><strong>Teléfono:</strong> {{ $invoice->sender_phone }}</p>
            <p><strong>Dirección:</strong> {{ $invoice->sender_address }}</p>
        </div>
        <div class="info-col">
            <h3>Recibir</h3>
            <p><strong>Nombre:</strong> {{ $invoice->recipient_name }}</p>
            <p><strong>Teléfono:</strong> {{ $invoice->recipient_phone }}</p>
            <p><strong>Dirección:</strong> {{ $invoice->recipient_address }}</p>
        </div>
    </div>

    <!-- Service Details Table -->
    <table>
        <thead>
            <tr>
                <th style="text-align: left;">Service Description</th>
                <th style="text-align: center; width: 15%;">Quantity</th>
                <th style="text-align: center; width: 20%;">Unit Price</th>
                <th style="text-align: center; width: 20%;">Total Cost</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $invoice->service_description }}</td>
                <td class="text-center">{{ $invoice->quantity }}</td>
                <td class="text-center">${{ number_format($invoice->unit_price, 2) }}</td>
                <td class="text-center">${{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->tax_amount > 0)
            <tr>
                <td colspan="3">Impuesto</td>
                <td class="text-center">${{ number_format($invoice->tax_amount, 2) }}</td>
            </tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3">Subtotal</td>
                <td class="text-center">${{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="3">MONTO TOTAL</td>
                <td class="text-center grand-total">${{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Disclaimer -->
    <div class="disclaimer">
        <strong>Disclaimer:</strong> Está prohibido enviar armas de fuego, drogas, armas y cualquier sustancia ilegal a través de IMPERIO SHIPPING CORP. y cualquier violación de esta política resultará en posibles acciones legales contra el cliente.
    </div>

    @if($invoice->notes)
    <div style="margin-top: 10px; background-color: #eff6ff; padding: 8px; border-left: 3px solid #3b82f6; font-size: 9px;">
        <strong>Notes:</strong>
        <p style="margin: 4px 0 0 0;">{{ $invoice->notes }}</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Gracias por su compra,</p>
        <p><strong>IMPERIO SHIPPING CORP.</strong></p>
        <p>Este documento fue generado electrónicamente.</p>
    </div>
</body>
</html>

