<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            margin-bottom: 30px;
        }
        .company-info {
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }
        .invoice-number {
            background-color: #2563eb;
            color: white;
            padding: 10px 20px;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .info-col {
            display: table-cell;
            width: 50%;
            padding: 15px;
            background-color: #f9fafb;
            vertical-align: top;
        }
        .info-col h3 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1f2937;
        }
        .info-col p {
            margin: 5px 0;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #0d9488;
            color: white;
            padding: 12px 10px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        td {
            border: 1px solid #d1d5db;
            padding: 10px;
            font-size: 11px;
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
            font-size: 14px;
            color: #059669;
        }
        .disclaimer {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 10px;
            margin-top: 20px;
            font-size: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            font-size: 10px;
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
            <p><strong>Dirección:</strong></p>
            <p>Residencial Vistas de Equípela, casa A3-11</p>
            <p>Esquipulas, Managua</p>
        </div>
        
        <div class="invoice-number">
            Factura #{{ $invoice->invoice_number }}
        </div>
        
        <p><strong>Fecha:</strong> {{ $invoice->invoice_date->format('d/m/Y') }}</p>
        @if($invoice->due_date)
            <p><strong>Fecha de Vencimiento:</strong> {{ $invoice->due_date->format('d/m/Y') }}</p>
        @endif
        <p><strong>Términos:</strong> {{ $invoice->terms }}</p>
    </div>

    <!-- Sender and Recipient Information -->
    <div class="info-grid">
        <div class="info-col">
            <h3>Expedidor</h3>
            <p><strong>Nombre:</strong> {{ $invoice->sender_name }}</p>
            <p><strong>Teléfono:</strong> {{ $invoice->sender_phone }}</p>
            <p><strong>Dirección:</strong></p>
            <p>{{ $invoice->sender_address }}</p>
        </div>
        <div class="info-col">
            <h3>Recibir</h3>
            <p><strong>Nombre:</strong> {{ $invoice->recipient_name }}</p>
            <p><strong>Teléfono:</strong> {{ $invoice->recipient_phone }}</p>
            <p><strong>Dirección:</strong></p>
            <p>{{ $invoice->recipient_address }}</p>
        </div>
    </div>

    <!-- Service Details Table -->
    <table>
        <thead>
            <tr>
                <th style="text-align: left;">Descripción del Servicio</th>
                <th style="text-align: center; width: 15%;">Cantidad</th>
                <th style="text-align: center; width: 20%;">Precio Unitario</th>
                <th style="text-align: center; width: 20%;">Costo Total</th>
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
    <div style="margin-top: 20px; background-color: #eff6ff; padding: 10px; border-left: 4px solid #3b82f6;">
        <strong>Notas:</strong>
        <p>{{ $invoice->notes }}</p>
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

