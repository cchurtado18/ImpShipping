<div>
    <!-- Header con búsqueda, filtros y botón agregar -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex space-x-4 flex-1">
            <div class="max-w-sm">
                <input wire:model.live="search" type="text" 
                       placeholder="Search by name or phone..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="max-w-xs">
                <select wire:model.live="stateFilter" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All States</option>
                    @foreach($states as $state)
                        <option value="{{ $state }}">{{ $state }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button wire:click="addShipment" 
                class="ml-4 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
            Add Shipment
        </button>
    </div>

    <!-- Tabla de envíos -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Box</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($shipments as $shipment)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $shipment->code }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div>{{ $shipment->client->full_name }}</div>
                        <div class="text-xs text-gray-500">{{ $shipment->client->us_phone }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div>{{ $shipment->recipient->full_name }}</div>
                        <div class="text-xs text-gray-500">{{ $shipment->recipient->ni_phone }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div class="text-xs text-gray-500">{{ $shipment->box->cubic_feet }} ft³</div>
                        <div class="text-xs text-gray-500">${{ number_format($shipment->sale_price_usd ?? 0, 2) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $shipment->shipment_status_color }}">
                            {{ $shipment->shipment_status_icon }} {{ $shipment->shipment_status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($shipment->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($shipment->payment_status === 'partial') bg-purple-100 text-purple-800
                                @else bg-green-100 text-green-800
                                @endif">
                                @if($shipment->payment_status === 'pending')
                                    ❌ Pendiente
                                @elseif($shipment->payment_status === 'partial')
                                    💰 Parcial
                                @else
                                    ✅ Pagado
                                @endif
                            </span>
                            @if($shipment->payment_status === 'partial')
                                <span class="text-xs text-gray-500 mt-1">
                                    Resta: ${{ number_format($shipment->remaining_amount, 2) }}
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${{ number_format($shipment->sale_price_usd ?? 0, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <button wire:click="editShipment({{ $shipment->id }})" 
                                class="text-blue-600 hover:text-blue-900">Edit</button>
                        <button wire:click="changeStatus({{ $shipment->id }})" 
                                class="text-orange-600 hover:text-orange-900">📋 Status</button>
                        <button wire:click="quickPayment({{ $shipment->id }})" 
                                class="text-purple-600 hover:text-purple-900">💰 Payment</button>
                        <button wire:click="showQR({{ $shipment->id }})" 
                                class="text-gray-600 hover:text-gray-900">QR</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                        No shipments registered for this route.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $shipments->links() }}
    </div>

    <!-- Modal de Pagos -->
    @livewire('quick-payment-modal')
    
    <!-- Modal de Estado -->
    @livewire('shipment-status-modal')
</div>
