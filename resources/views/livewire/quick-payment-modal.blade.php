<div>
    @if($showModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="payment-modal">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        @if($editingPayment)
                            Edit Payment
                        @else
                            Payment Management
                        @endif
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                @if($shipment)
                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                    <div class="text-sm text-gray-600">
                        <div><strong>Client:</strong> {{ $shipment->client->full_name }}</div>
                        <div><strong>Recipient:</strong> {{ $shipment->recipient->full_name }}</div>
                        <div><strong>Code:</strong> {{ $shipment->code }}</div>
                        <div><strong>Total:</strong> ${{ number_format($shipment->sale_price_usd, 2) }}</div>
                        <div><strong>Paid:</strong> ${{ number_format($shipment->total_paid, 2) }}</div>
                        <div><strong>Remaining:</strong> ${{ number_format($shipment->remaining_amount, 2) }}</div>
                        <div><strong>Status:</strong> 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($shipment->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($shipment->payment_status === 'partial') bg-purple-100 text-purple-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst($shipment->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Botón para mostrar/ocultar historial -->
                <div class="mb-4">
                    <button wire:click="togglePaymentHistory" 
                            class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-100 border border-blue-300 rounded-md hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        {{ $showPaymentHistory ? 'Hide' : 'Show' }} Payment History
                    </button>
                </div>

                <!-- Historial de pagos -->
                @if($showPaymentHistory && count($payments) > 0)
                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-2">Historial de Pagos</h4>
                    <div class="space-y-2">
                        @foreach($payments as $payment)
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <div class="text-sm">
                                <div><strong>${{ number_format($payment->amount_usd, 2) }}</strong> - {{ ucfirst($payment->method) }}</div>
                                <div class="text-gray-500">{{ $payment->paid_at->format('d/m/Y H:i') }}</div>
                                @if($payment->reference)
                                <div class="text-gray-500">Ref: {{ $payment->reference }}</div>
                                @endif
                            </div>
                            <div class="flex space-x-2">
                                <button wire:click="editPayment({{ $payment->id }})" 
                                        class="text-blue-600 hover:text-blue-900 text-sm">Editar</button>
                                <button wire:click="deletePayment({{ $payment->id }})" 
                                        onclick="return confirm('¿Estás seguro de eliminar este pago?')"
                                        class="text-red-600 hover:text-red-900 text-sm">Eliminar</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Formulario de pago -->
                @if(!$editingPayment || $editingPayment)
                <form wire:submit.prevent="{{ $editingPayment ? 'updatePayment' : 'processPayment' }}">
                    <div class="mb-4">
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            @if($editingPayment) Monto del Pago @else Monto a Pagar @endif
                        </label>
                        <input type="number" 
                               wire:model="amount" 
                               id="amount"
                               step="0.01" 
                               min="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="0.00">
                        @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="paymentMethod" class="block text-sm font-medium text-gray-700 mb-2">Método de Pago</label>
                        <select wire:model="paymentMethod" 
                                id="paymentMethod"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="cash">Efectivo</option>
                            <option value="card">Tarjeta</option>
                            <option value="transfer">Transferencia</option>
                        </select>
                        @error('paymentMethod') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="reference" class="block text-sm font-medium text-gray-700 mb-2">Referencia (Opcional)</label>
                        <input type="text" 
                               wire:model="reference" 
                               id="reference"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Número de referencia...">
                    </div>

                    <div class="flex justify-end space-x-3">
                        @if($editingPayment)
                        <button type="button" 
                                wire:click="cancelEdit"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancelar Edición
                        </button>
                        @endif
                        <button type="button" 
                                wire:click="closeModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cerrar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            {{ $editingPayment ? 'Actualizar Pago' : 'Procesar Pago' }}
                        </button>
                    </div>
                </form>
                @endif

                <!-- Mensajes de éxito -->
                @if(session()->has('message'))
                <div class="mt-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('message') }}
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
