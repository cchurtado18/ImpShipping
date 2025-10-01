<div>
    <form method="POST" action="{{ route('invoices.store') }}" class="space-y-6">
        @csrf

        <!-- Client and Shipment Selection -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Client *</label>
                <select wire:model.live="selectedClient" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Choose a client...</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->full_name }} - {{ $client->us_phone }}</option>
                    @endforeach
                </select>
                @error('selectedClient') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Shipment (Auto-filled if client has pending shipments)</label>
                <select wire:model.live="selectedShipment" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Choose a shipment...</option>
                    @if($selectedClient && count($availableShipments) > 0)
                        <optgroup label="Client's Pending Shipments">
                            @foreach($availableShipments as $shipment)
                                <option value="{{ $shipment->id }}">
                                    {{ $shipment->code }} - {{ $shipment->recipient?->full_name ?? 'No recipient' }} ({{ $shipment->box?->code ?? 'No box' }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                    <optgroup label="All Available Shipments">
                        @foreach($shipments as $shipment)
                            <option value="{{ $shipment->id }}">
                                {{ $shipment->code }} - {{ $shipment->client?->full_name ?? 'No client' }} - {{ $shipment->recipient?->full_name ?? 'No recipient' }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
                @error('selectedShipment') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Auto-fill Status -->
        @if($selectedClient && count($availableShipments) > 0)
            <div class="bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">
                            Auto-fill Available!
                        </h3>
                        <div class="mt-2 text-sm text-green-700">
                            <p>This client has {{ count($availableShipments) }} pending shipment(s). Select one to auto-fill the invoice.</p>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($selectedClient && count($availableShipments) == 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            No Pending Shipments
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>This client has no pending shipments. You can still create an invoice manually or select from all available shipments.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Hidden fields for form submission -->
        <input type="hidden" name="client_id" value="{{ $selectedClient }}">
        <input type="hidden" name="shipment_id" value="{{ $selectedShipment }}">

        <!-- Sender Information -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Sender Information (Expedidor)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                    <input type="text" name="sender_name" wire:model.live="sender_name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $sender_name }}">
                    @error('sender_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                    <input type="text" name="sender_phone" wire:model.live="sender_phone" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $sender_phone }}">
                    @error('sender_phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                <textarea name="sender_address" wire:model.live="sender_address" rows="3" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $sender_address }}</textarea>
                @error('sender_address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Recipient Information -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recipient Information (Recibir)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                    <input type="text" name="recipient_name" wire:model.live="recipient_name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $recipient_name }}">
                    @error('recipient_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                    <input type="text" name="recipient_phone" wire:model.live="recipient_phone" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $recipient_phone }}">
                    @error('recipient_phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                <textarea name="recipient_address" wire:model.live="recipient_address" rows="3" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $recipient_address }}</textarea>
                @error('recipient_address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Service Details -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Service Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Description *</label>
                    <input type="text" name="service_description" wire:model.live="service_description" required
                           placeholder="e.g., Envio a Managua 41x28x26"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $service_description }}">
                    @error('service_description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                    <input type="number" name="quantity" wire:model.live="quantity" min="1" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $quantity }}">
                    @error('quantity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <!-- Hidden fields for automatic price calculation -->
            <input type="hidden" name="unit_price" value="{{ $unit_price }}">
            <input type="hidden" name="tax_amount" value="{{ $tax_amount }}">
        </div>

        <!-- Price Summary - Automatic Calculation -->
        @if($unit_price > 0)
        <div class="bg-green-50 border border-green-200 rounded-md p-4">
            <h4 class="text-sm font-medium text-green-900 mb-3">Automatic Price Calculation</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Box Cost:</span>
                        <span class="font-semibold">${{ number_format($unit_price, 2) }}</span>
                    </div>
                    @if($shipment && $shipment->declared_value > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Declared Value:</span>
                        <span class="font-semibold">${{ number_format($shipment->declared_value, 2) }}</span>
                    </div>
                    @endif
                    @if($shipment && $shipment->transport_cost > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Transport Cost:</span>
                        <span class="font-semibold">${{ number_format($shipment->transport_cost, 2) }}</span>
                    </div>
                    @endif
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-semibold">${{ number_format($this->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax ({{ $tax }}%):</span>
                        <span class="font-semibold">${{ number_format($tax_amount, 2) }}</span>
                    </div>
                    <div class="border-t pt-2">
                        <div class="flex justify-between">
                            <span class="text-gray-800 font-medium">TOTAL:</span>
                            <span class="font-bold text-lg text-green-600">${{ number_format($this->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 p-2 bg-green-100 border border-green-200 rounded text-xs text-green-800">
                ✅ Total calculated automatically from package data
            </div>
        </div>
        @endif

        <!-- Additional Information -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Status</label>
                    <select name="invoice_status" wire:model.live="invoice_status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="pending">⏳ Pending</option>
                        <option value="cancelled_by_cash">💰 Cancelled by Cash</option>
                        <option value="cancelled_by_transfer">🏦 Cancelled by Transfer</option>
                    </select>
                    @error('invoice_status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" wire:model.live="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $notes }}</textarea>
                    @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="border-t pt-6 flex justify-end space-x-3">
            <button type="button" wire:click="clearForm"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                Clear Form
            </button>
            <a href="{{ route('invoices.index') }}" 
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                Cancel
            </a>
            <button type="submit" 
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Create Invoice
            </button>
        </div>
    </form>
</div>
