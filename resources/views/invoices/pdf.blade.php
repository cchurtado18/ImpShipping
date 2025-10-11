<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #000;
            background: #fff;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        .company-info h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .company-info .contact {
            font-size: 12px;
            margin-bottom: 5px;
        }
        .invoice-header {
            text-align: right;
        }
        .invoice-number {
            background: #2563eb;
            color: white;
            padding: 8px 16px;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 10px;
            display: inline-block;
        }
        .invoice-details {
            font-size: 12px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .info-box {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 15px;
        }
        .info-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info-content {
            font-size: 12px;
        }
        .info-content div {
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: #0d9488;
            color: white;
            font-weight: bold;
            padding: 12px 8px;
            text-align: left;
        }
        th.text-center {
            text-align: center;
        }
        th.text-right {
            text-align: right;
        }
        td {
            padding: 12px 8px;
            border-bottom: 1px solid #d1d5db;
        }
        td.text-center {
            text-align: center;
        }
        td.text-right {
            text-align: right;
        }
        .package-code {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .package-dimensions {
            font-size: 11px;
            color: #6b7280;
        }
        .disclaimer-box {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 15px;
            margin-bottom: 20px;
        }
        .disclaimer-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .disclaimer-text {
            font-size: 12px;
        }
        .footer {
            text-align: center;
            margin-bottom: 20px;
        }
        .footer-text {
            font-size: 14px;
            margin-bottom: 10px;
        }
        .notes-box {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 15px;
            margin-bottom: 20px;
        }
        .notes-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .notes-content {
            font-size: 12px;
        }
        .status-badge {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 12px;
            display: inline-block;
            float: right;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>IMPERIO SHIPPING CORP.</h1>
                <div class="contact">Phone: +1 (305) 890-4018</div>
                <div class="contact">Email: imperioshipping@usa.com</div>
                <div class="contact">Address: 8551 NW 72nd St Miami, FL 33166</div>
            </div>
            <div class="invoice-header">
                <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
                <div class="invoice-details">
                    <div>Fecha: {{ $invoice->invoice_date }}</div>
                    <div>Tiempo de Entrega: {{ $invoice->terms }}</div>
                </div>
            </div>
        </div>

        <!-- Sender and Recipient Info -->
        <div class="info-grid">
            <div class="info-box">
                <div class="info-title">Expedidor</div>
                <div class="info-content">
                    <div>Nombre: {{ $invoice->sender_name }}</div>
                    <div>Teléfono: {{ $invoice->sender_phone }}</div>
                    <div>Dirección: {{ $invoice->sender_address }}</div>
                </div>
            </div>
            
            <div class="info-box">
                <div class="info-title">Recibir</div>
                <div class="info-content">
                    <div>Nombre: {{ $invoice->recipient_name }}</div>
                    <div>Teléfono: {{ $invoice->recipient_phone }}</div>
                    <div>Dirección: {{ $invoice->recipient_address }}</div>
                </div>
            </div>
        </div>

        <!-- Service Details Table -->
        <table>
            <thead>
                <tr>
                    <th>DESCRIPCIÓN DEL SERVICIO</th>
                    <th class="text-center">CANTIDAD</th>
                    <th class="text-right">PRECIO UNITARIO</th>
                    <th class="text-right">COSTO TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @if($shipments->count() > 1)
                    {{-- Múltiples paquetes - una fila por paquete --}}
                    @foreach($shipments as $shipment)
                    <tr>
                        <td>
                            <div class="package-code">{{ $shipment->code }}</div>
                            @if($shipment->hasCustomDimensions())
                            <div class="package-dimensions">
                                Dimensiones: {{ $shipment->formatted_custom_dimensions }}
                                @if($shipment->custom_weight > 0)
                                    | Peso: {{ $shipment->custom_weight }} lbs
                                @endif
                                | Modo: {{ $shipment->price_mode_label }}
                            </div>
                            @elseif($shipment->box)
                            <div class="package-dimensions">
                                Dimensiones: {{ $shipment->box->length_in }}" x {{ $shipment->box->width_in }}" x {{ $shipment->box->height_in }}"
                            </div>
                            @endif
                        </td>
                        <td class="text-center">1</td>
                        <td class="text-right">${{ number_format($shipment->sale_price_usd, 2) }}</td>
                        <td class="text-right">${{ number_format($shipment->sale_price_usd, 2) }}</td>
                    </tr>
                    @endforeach
                @else
                    {{-- Un solo paquete --}}
                    <tr>
                        <td>
                            <div class="package-code">{{ $shipments->first()->code ?? 'N/A' }}</div>
                            @if($shipments->first() && $shipments->first()->hasCustomDimensions())
                            <div class="package-dimensions">
                                Dimensiones: {{ $shipments->first()->formatted_custom_dimensions }}
                                @if($shipments->first()->custom_weight > 0)
                                    | Peso: {{ $shipments->first()->custom_weight }} lbs
                                @endif
                                | Modo: {{ $shipments->first()->price_mode_label }}
                            </div>
                            @elseif($shipments->first() && $shipments->first()->box)
                            <div class="package-dimensions">
                                Dimensiones: {{ $shipments->first()->box->length_in }}" x {{ $shipments->first()->box->width_in }}" x {{ $shipments->first()->box->height_in }}"
                            </div>
                            @endif
                        </td>
                        <td class="text-center">{{ $invoice->quantity }}</td>
                        <td class="text-right">${{ number_format($invoice->unit_price, 2) }}</td>
                        <td class="text-right">${{ number_format($invoice->total_amount, 2) }}</td>
                    </tr>
                @endif
                
                <!-- Summary Rows -->
                <tr>
                    <td></td>
                    <td></td>
                    <td class="text-right"><strong>Subtotal</strong></td>
                    <td class="text-right"><strong>${{ number_format($invoice->subtotal, 2) }}</strong></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td class="text-right"><strong>MONTO TOTAL</strong></td>
                    <td class="text-right"><strong style="color: #059669;">${{ number_format($invoice->total_amount, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Disclaimer -->
        <div class="disclaimer-box">
            <div class="disclaimer-title">Disclaimer:</div>
            <div class="disclaimer-text">
                Está prohibido enviar armas de fuego, drogas, armas y cualquier sustancia ilegal a través de IMPERIO SHIPPING CORP. y cualquier violación de esta política resultará en posibles acciones legales contra el cliente.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-text">Gracias por su compra, <strong>IMPERIO SHIPPING CORP.</strong></div>
        </div>

        <!-- Notes -->
        <div class="notes-box">
            <div class="notes-title">Notas:</div>
            <div class="notes-content">Pendiente ${{ number_format($invoice->total_amount, 0) }}</div>
        </div>

    </div>
</body>
</html>