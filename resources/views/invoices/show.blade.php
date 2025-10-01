<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Invoice Preview</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            Invoice #{{ $invoice->invoice_number }}
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('invoices.index') }}" 
                           class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Back to Invoices
                        </a>
                        <a href="{{ route('invoices.edit', $invoice) }}" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Edit Invoice
                        </a>
                        <button onclick="window.print()" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            Print Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Preview -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden print:shadow-none">
            <!-- Invoice Content -->
            <div class="p-8 print:p-6" style="font-family: 'Arial', sans-serif;">
                <!-- Header -->
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">IMPERIO SHIPPING CORP.</h1>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p><strong>Phone:</strong> +1 (305) 890-4018</p>
                            <p><strong>Email:</strong> <a href="mailto:imperioshipping@usa.com" class="text-blue-600 underline">imperioshipping@usa.com</a></p>
                            <p><strong>Address:</strong></p>
                            <p>Residencial Vistas de</p>
                            <p>Equípela, casa A3-11</p>
                            <p>Esquipulas, Managua</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="bg-blue-600 text-white p-4 rounded-lg mb-4">
                            <div class="text-2xl font-bold">#{{ $invoice->invoice_number }}</div>
                        </div>
                        <div class="text-sm text-gray-600">
                            <p><strong>Fecha:</strong> {{ $invoice->invoice_date->format('d/m/Y') }}</p>
                            @if($invoice->due_date)
                                <p><strong>Fecha de Vencimiento:</strong> {{ $invoice->due_date->format('d/m/Y') }}</p>
                            @endif
                            <p><strong>Términos:</strong> {{ $invoice->terms }}</p>
                        </div>
                    </div>
                </div>

                <!-- Sender and Recipient Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Sender (Expedidor) -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Expedidor</h3>
                        <div class="text-sm text-gray-700 space-y-1">
                            <p><strong>Nombre:</strong> {{ $invoice->sender_name }}</p>
                            <p><strong>Teléfono:</strong> {{ $invoice->sender_phone }}</p>
                            <p><strong>Dirección:</strong></p>
                            <p class="whitespace-pre-line">{{ $invoice->sender_address }}</p>
                        </div>
                    </div>

                    <!-- Recipient (Recibir) -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Recibir</h3>
                        <div class="text-sm text-gray-700 space-y-1">
                            <p><strong>Nombre:</strong> {{ $invoice->recipient_name }}</p>
                            <p><strong>Teléfono:</strong> {{ $invoice->recipient_phone }}</p>
                            <p><strong>Dirección:</strong></p>
                            <p class="whitespace-pre-line">{{ $invoice->recipient_address }}</p>
                        </div>
                    </div>
                </div>

                <!-- Service Details Table -->
                <div class="mb-8">
                    <table class="w-full border-collapse border border-gray-300">
                        <thead style="background-color: #0d9488 !important; display: table-header-group !important;">
                            <tr>
                                <th style="border: 1px solid #d1d5db; padding: 12px 16px; text-align: left; font-weight: bold; color: white; text-transform: uppercase;">Descripción del Servicio</th>
                                <th style="border: 1px solid #d1d5db; padding: 12px 16px; text-align: center; font-weight: bold; color: white; text-transform: uppercase;">Cantidad</th>
                                <th style="border: 1px solid #d1d5db; padding: 12px 16px; text-align: center; font-weight: bold; color: white; text-transform: uppercase;">Precio Unitario</th>
                                <th style="border: 1px solid #d1d5db; padding: 12px 16px; text-align: center; font-weight: bold; color: white; text-transform: uppercase;">Costo Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-gray-300 px-4 py-3">{{ $invoice->service_description }}</td>
                                <td class="border border-gray-300 px-4 py-3 text-center">{{ $invoice->quantity }}</td>
                                <td class="border border-gray-300 px-4 py-3 text-center">${{ number_format($invoice->unit_price, 2) }}</td>
                                <td class="border border-gray-300 px-4 py-3 text-center">${{ number_format($invoice->subtotal, 2) }}</td>
                            </tr>
                            @if($invoice->tax_amount > 0)
                            <tr>
                                <td class="border border-gray-300 px-4 py-3" colspan="3">Impuesto</td>
                                <td class="border border-gray-300 px-4 py-3 text-center">${{ number_format($invoice->tax_amount, 2) }}</td>
                            </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-100">
                                <td class="border border-gray-300 px-4 py-3 font-semibold" colspan="3">Subtotal</td>
                                <td class="border border-gray-300 px-4 py-3 text-center font-semibold">${{ number_format($invoice->subtotal, 2) }}</td>
                            </tr>
                            <tr class="bg-gray-100">
                                <td class="border border-gray-300 px-4 py-3 font-semibold" colspan="3">MONTO TOTAL</td>
                                <td class="border border-gray-300 px-4 py-3 text-center font-semibold text-lg text-green-600">${{ number_format($invoice->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Disclaimer and Footer -->
                <div class="mt-8 space-y-4">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <p class="text-sm text-gray-700">
                            <strong>Disclaimer:</strong> Está prohibido enviar armas de fuego, drogas, armas y cualquier sustancia ilegal a través de IMPERIO SHIPPING CORP. y cualquier violación de esta política resultará en posibles acciones legales contra el cliente.
                        </p>
                    </div>
                    
                    <div class="text-center">
                        <p class="text-lg font-semibold text-gray-800">Gracias por su compra,</p>
                        <p class="text-xl font-bold text-gray-800">IMPERIO SHIPPING CORP.</p>
                    </div>
                </div>

                <!-- Status and Notes -->
                @if($invoice->notes)
                <div class="mt-6 bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-blue-800 mb-2">Notas:</h4>
                    <p class="text-blue-700">{{ $invoice->notes }}</p>
                </div>
                @endif

                <!-- Status Badge -->
                <div class="mt-6 text-center">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $invoice->status_color }}">
                        Estado: {{ $invoice->formatted_status }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            .print\\:shadow-none {
                box-shadow: none !important;
            }
            .print\\:p-6 {
                padding: 1.5rem !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</x-app-layout>




