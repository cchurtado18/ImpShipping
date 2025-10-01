<div>
    <!-- Header -->
    <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Facturas</h2>
                <p class="text-sm text-gray-600">Genera facturas y registra pagos</p>
            </div>
            <button wire:click="addInvoice" 
                    class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Generar Factura
            </button>
        </div>

        <!-- Búsqueda y filtros -->
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input wire:model.live="search" type="text" 
                       placeholder="Buscar por número de factura o cliente..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div class="sm:w-48">
                <select wire:model.live="statusFilter" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Todos los estados</option>
                    <option value="draft">Borrador</option>
                    <option value="issued">Emitida</option>
                    <option value="paid">Pagada</option>
                    <option value="cancelled">Anulada</option>
                </select>
            </div>
            <div class="sm:w-48">
                <select wire:model.live="clientFilter" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Todos los clientes</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->full_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Tabla de facturas -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Factura</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pagado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($invoices as $invoice)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</div>
                        <div class="text-sm text-gray-500">{{ $invoice->invoiceLines->count() }} línea(s)</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $invoice->client->full_name }}</div>
                        <div class="text-sm text-gray-500">{{ $invoice->client->us_phone }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $invoice->invoice_date->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${{ number_format($invoice->total, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${{ number_format($invoice->total_paid, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($invoice->status === 'draft') bg-gray-100 text-gray-800
                            @elseif($invoice->status === 'issued') bg-yellow-100 text-yellow-800
                            @elseif($invoice->status === 'paid') bg-green-100 text-green-800
                            @elseif($invoice->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $invoice->status_in_spanish }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
                            <button wire:click="showPaymentForm({{ $invoice->id }})" 
                                    class="text-green-600 hover:text-green-900">Pagar</button>
                        @endif
                        <button wire:click="editInvoice({{ $invoice->id }})" 
                                class="text-emerald-600 hover:text-emerald-900">Editar</button>
                        <button wire:click="deleteInvoice({{ $invoice->id }})" 
                                class="text-red-600 hover:text-red-900"
                                onclick="return confirm('¿Estás seguro de que querés eliminar esta factura?')">Eliminar</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                        No hay facturas registradas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $invoices->links() }}
    </div>

    <!-- Modal de formulario -->
    @if($showForm)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="invoice-modal">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $editingInvoice ? 'Editar Factura' : 'Generar Nueva Factura' }}
                    </h3>
                    <button wire:click="closeForm" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveInvoice">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Columna izquierda -->
                        <div class="space-y-4">
                            <!-- Cliente -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                                <select wire:model="client_id" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <option value="">Seleccionar cliente...</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->full_name }}</option>
                                    @endforeach
                                </select>
                                @error('client_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Fecha -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Factura *</label>
                                <input wire:model="invoice_date" type="date" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                @error('invoice_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Notas -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                                <textarea wire:model="notes" rows="3" 
                                          placeholder="Información adicional sobre la factura..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                                @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Columna derecha -->
                        <div class="space-y-4">
                            <!-- Paquetes disponibles -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Paquetes Disponibles *</label>
                                <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-md p-3">
                                    @forelse($availablePackages as $package)
                                        <label class="flex items-center space-x-2 py-2 border-b border-gray-100 last:border-b-0">
                                            <input wire:model="selectedPackages" type="checkbox" value="{{ $package->id }}" 
                                                   class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-gray-900">{{ $package->tracking_code }}</div>
                                                <div class="text-sm text-gray-500">{{ $package->client->full_name }} → {{ $package->recipient->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $package->cubic_feet }} ft³, {{ $package->weight }} lb - ${{ number_format($package->billing_type === 'weight' ? $package->weight * 2.5 : $package->cubic_feet * 50, 2) }}</div>
                                            </div>
                                        </label>
                                    @empty
                                        <div class="text-sm text-gray-500 text-center py-4">No hay paquetes disponibles</div>
                                    @endforelse
                                </div>
                                @error('selectedPackages') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Totales -->
                            <div class="bg-gray-50 p-4 rounded-md">
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span>Subtotal:</span>
                                        <span>${{ number_format($subtotal, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span>Impuesto (15%):</span>
                                        <span>${{ number_format($tax, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-lg font-semibold border-t pt-2">
                                        <span>Total:</span>
                                        <span>${{ number_format($total, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeForm" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 border border-transparent rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            {{ $editingInvoice ? 'Actualizar' : 'Generar Factura' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal de pago -->
    @if($showPaymentModal && $selectedInvoice)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="payment-modal">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Registrar Pago</h3>
                    <button wire:click="closePaymentModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="savePayment">
                    <!-- Información de la factura -->
                    <div class="bg-gray-50 p-4 rounded-md mb-4">
                        <div class="text-sm text-gray-600">Factura: {{ $selectedInvoice->invoice_number }}</div>
                        <div class="text-sm text-gray-600">Cliente: {{ $selectedInvoice->client->full_name }}</div>
                        <div class="text-sm text-gray-600">Total: ${{ number_format($selectedInvoice->total, 2) }}</div>
                        <div class="text-sm text-gray-600">Pagado: ${{ number_format($selectedInvoice->total_paid, 2) }}</div>
                        <div class="text-sm font-semibold text-gray-900">Restante: ${{ number_format($selectedInvoice->remaining_amount, 2) }}</div>
                    </div>

                    <!-- Monto -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Monto *</label>
                        <input wire:model="payment_amount" type="number" step="0.01" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        @error('payment_amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Fecha -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Pago *</label>
                        <input wire:model="payment_date" type="date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        @error('payment_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Método -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pago *</label>
                        <select wire:model="payment_method" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="cash">Efectivo</option>
                            <option value="zelle">Zelle</option>
                            <option value="card">Tarjeta</option>
                            <option value="bank_transfer">Transferencia Bancaria</option>
                            <option value="other">Otro</option>
                        </select>
                        @error('payment_method') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Referencia -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Referencia</label>
                        <input wire:model="payment_reference" type="text" 
                               placeholder="Número de referencia del pago"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        @error('payment_reference') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Notas -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                        <textarea wire:model="payment_notes" rows="2" 
                                  placeholder="Información adicional sobre el pago..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                        @error('payment_notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="closePaymentModal" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 border border-transparent rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            Registrar Pago
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
