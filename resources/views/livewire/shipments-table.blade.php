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
    <div class="bg-white shadow-sm rounded-lg overflow-x-auto">
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
                        <button wire:click="viewDetails({{ $shipment->id }})" 
                                class="text-green-600 hover:text-green-900" title="View Details">👁️</button>
                        <button wire:click="editShipment({{ $shipment->id }})" 
                                class="text-blue-600 hover:text-blue-900" title="Edit">Edit</button>
                        <button wire:click="changeStatus({{ $shipment->id }})" 
                                class="text-orange-600 hover:text-orange-900" title="Change Status">📋</button>
                        <button wire:click="quickPayment({{ $shipment->id }})" 
                                class="text-purple-600 hover:text-purple-900" title="Payment">💰</button>
                        <button wire:click="showQR({{ $shipment->id }})" 
                                class="text-gray-600 hover:text-gray-900" title="QR Code">QR</button>
                        <button wire:click="deleteShipment({{ $shipment->id }})" 
                                class="text-red-600 hover:text-red-900" title="Delete"
                                onclick="return confirm('Are you sure you want to delete this shipment?')">🗑️</button>
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

    <!-- Modal de Envíos -->
    @livewire('shipment-form-modal', ['route' => $route])

    <!-- Modal de Pagos -->
    @livewire('quick-payment-modal')
    
    <!-- Modal de Estado -->
    @livewire('shipment-status-modal')

    <!-- Modal de Detalles del Envío -->
    @if($showDetailsModal && $selectedShipment)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="details-modal">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">
                        📋 Shipment Details: {{ $selectedShipment->code }}
                    </h3>
                    <button wire:click="closeDetailsModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Contenido de los detalles -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Información del Cliente -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-blue-800 mb-3">👤 Client Information</h4>
                        <div class="space-y-2 text-sm">
                            <p><strong>Name:</strong> {{ $selectedShipment->client->full_name }}</p>
                            <p><strong>US Phone:</strong> {{ $selectedShipment->client->us_phone }}</p>
                            <p><strong>Email:</strong> {{ $selectedShipment->client->email }}</p>
                            <p><strong>State:</strong> {{ $selectedShipment->client->us_state }}</p>
                            <p><strong>US Address:</strong> {{ $selectedShipment->client->us_address }}</p>
                        </div>
                    </div>

                    <!-- Información del Receptor -->
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-green-800 mb-3">📦 Recipient Information</h4>
                        <div class="space-y-2 text-sm">
                            <p><strong>Name:</strong> {{ $selectedShipment->recipient->full_name }}</p>
                            <p><strong>Phone:</strong> {{ $selectedShipment->recipient->ni_phone }}</p>
                            <p><strong>Age:</strong> {{ $selectedShipment->recipient->age ?? 'Not specified' }}</p>
                            <p><strong>Department:</strong> {{ $selectedShipment->recipient->ni_department }}</p>
                            <p><strong>City:</strong> {{ $selectedShipment->recipient->ni_city }}</p>
                            <p><strong>Address:</strong> {{ $selectedShipment->recipient->ni_address }}</p>
                        </div>
                    </div>

                    <!-- Información de la Caja -->
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-yellow-800 mb-3">📦 Box Information</h4>
                        <div class="space-y-2 text-sm">
                            <p><strong>Code:</strong> {{ $selectedShipment->box->code }}</p>
                            <p><strong>Dimensions:</strong> {{ $selectedShipment->box->length_in }}" × {{ $selectedShipment->box->width_in }}" × {{ $selectedShipment->box->height_in }}"</p>
                            <p><strong>Volume:</strong> {{ number_format(($selectedShipment->box->length_in * $selectedShipment->box->width_in * $selectedShipment->box->height_in) / 1728, 2) }} ft³</p>
                            <p><strong>Base Price:</strong> ${{ number_format($selectedShipment->box->base_price_usd, 2) }}</p>
                        </div>
                    </div>

                    <!-- Información del Envío -->
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-purple-800 mb-3">🚚 Shipment Information</h4>
                        <div class="space-y-2 text-sm">
                            <p><strong>Code:</strong> {{ $selectedShipment->code }}</p>
                            <p><strong>Route:</strong> {{ $selectedShipment->route->name ?? 'Current Route' }}</p>
                            <p><strong>Status:</strong> 
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $selectedShipment->shipment_status_color }}">
                                    {{ $selectedShipment->shipment_status_icon }} {{ $selectedShipment->shipment_status_label }}
                                </span>
                            </p>
                            <p><strong>Final Price:</strong> <span class="text-lg font-bold text-green-600">${{ number_format($selectedShipment->sale_price_usd, 2) }}</span></p>
                            <p><strong>Declared Value:</strong> ${{ number_format($selectedShipment->declared_value_usd ?? 0, 2) }}</p>
                            <p><strong>Creation Date:</strong> {{ $selectedShipment->created_at->format('d/m/Y H:i') }}</p>
                            @if($selectedShipment->notes)
                                <p><strong>Notes:</strong> {{ $selectedShipment->notes }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="mt-6 flex justify-end space-x-3">
                    <button wire:click="closeDetailsModal" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Close
                    </button>
                    <button wire:click="editShipment({{ $selectedShipment->id }})" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Edit Shipment
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
