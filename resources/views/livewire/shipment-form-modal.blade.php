<div>
    @if($showModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="shipment-modal">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" style="width: 384px; max-width: 384px;">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $editingShipment ? 'Edit Shipment' : 'Add New Shipment' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveShipment">
                    <!-- Cliente -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Client *</label>
                        <select 
                            wire:model="selectedClient"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Select client...</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->full_name }} - {{ $client->us_phone }}</option>
                            @endforeach
                        </select>
                        @error('selectedClient') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Receptor -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recipient *</label>
                        <select 
                            wire:model="selectedRecipient"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Select recipient...</option>
                            @foreach($recipients as $recipient)
                            <option value="{{ $recipient->id }}">{{ $recipient->full_name }} - {{ $recipient->ni_phone }}</option>
                            @endforeach
                        </select>
                        @error('selectedRecipient') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Caja -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Box Type *</label>
                        <select 
                            wire:model="selectedBox"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Select box...</option>
                            @foreach($boxes as $box)
                            <option value="{{ $box->id }}">{{ $box->code }} ({{ $box->cubic_feet }} ft³)</option>
                            @endforeach
                        </select>
                        @error('selectedBox') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Precio -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Final Price (USD) *</label>
                        <input 
                            type="number" 
                            wire:model="finalPrice"
                            step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="0.00"
                        >
                        @error('finalPrice') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Estado -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                        <select 
                            wire:model="shipmentStatus"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="por_recepcionar">📦 To Receive</option>
                            <option value="recepcionado">✅ Received</option>
                            <option value="dejado_almacen">🏪 Left at Warehouse</option>
                            <option value="en_nicaragua">🇳🇮 In Nicaragua</option>
                            <option value="entregado">🎉 Delivered</option>
                        </select>
                        @error('shipmentStatus') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Notas -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea 
                            wire:model="notes"
                            rows="2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Additional information about the shipment..."
                        ></textarea>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3">
                        <button 
                            type="button" 
                            wire:click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500"
                        >
                            Cancel
                        </button>
                        <button 
                            type="button"
                            wire:click="showPreview"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                        >
                            Preview
                        </button>
                        <button 
                            type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            {{ $editingShipment ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal de Previsualización -->
    @if($showPreview)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="preview-modal">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">
                        📋 Previsualización del Envío
                    </h3>
                    <button wire:click="hidePreview" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Contenido de la previsualización -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Información del Cliente -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-blue-800 mb-3">👤 Información del Cliente</h4>
                        @if($this->selectedClient)
                            <div class="space-y-2 text-sm">
                                <p><strong>Nombre:</strong> {{ $this->selectedClient->full_name }}</p>
                                <p><strong>Teléfono US:</strong> {{ $this->selectedClient->us_phone }}</p>
                                <p><strong>Email:</strong> {{ $this->selectedClient->email }}</p>
                                <p><strong>Estado:</strong> {{ $this->selectedClient->us_state }}</p>
                            </div>
                        @else
                            <p class="text-red-500">No se ha seleccionado un cliente</p>
                        @endif
                    </div>

                    <!-- Información del Receptor -->
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-green-800 mb-3">📦 Información del Receptor</h4>
                        @if($this->selectedRecipient)
                            <div class="space-y-2 text-sm">
                                <p><strong>Nombre:</strong> {{ $this->selectedRecipient->full_name }}</p>
                                <p><strong>Teléfono:</strong> {{ $this->selectedRecipient->ni_phone }}</p>
                                <p><strong>Edad:</strong> {{ $this->selectedRecipient->age ?? 'No especificada' }}</p>
                                <p><strong>Departamento:</strong> {{ $this->selectedRecipient->ni_department }}</p>
                                <p><strong>Ciudad:</strong> {{ $this->selectedRecipient->ni_city }}</p>
                                <p><strong>Dirección:</strong> {{ $this->selectedRecipient->ni_address }}</p>
                            </div>
                        @else
                            <p class="text-red-500">No se ha seleccionado un receptor</p>
                        @endif
                    </div>

                    <!-- Información de la Caja -->
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-yellow-800 mb-3">📦 Información de la Caja</h4>
                        @if($this->selectedBox)
                            <div class="space-y-2 text-sm">
                                <p><strong>Código:</strong> {{ $this->selectedBox->code }}</p>
                                <p><strong>Dimensiones:</strong> {{ $this->selectedBox->length_in }}" × {{ $this->selectedBox->width_in }}" × {{ $this->selectedBox->height_in }}"</p>
                                <p><strong>Volumen:</strong> {{ number_format(($this->selectedBox->length_in * $this->selectedBox->width_in * $this->selectedBox->height_in) / 1728, 2) }} ft³</p>
                                <p><strong>Precio Base:</strong> ${{ number_format($this->selectedBox->base_price_usd, 2) }}</p>
                            </div>
                        @else
                            <p class="text-red-500">No se ha seleccionado una caja</p>
                        @endif
                    </div>

                    <!-- Información del Envío -->
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-purple-800 mb-3">🚚 Información del Envío</h4>
                        <div class="space-y-2 text-sm">
                            <p><strong>Ruta:</strong> {{ $route->name ?? 'Ruta Actual' }}</p>
                            <p><strong>Estado:</strong> 
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($shipmentStatus === 'por_recepcionar') bg-gray-100 text-gray-800
                                    @elseif($shipmentStatus === 'recepcionado') bg-blue-100 text-blue-800
                                    @elseif($shipmentStatus === 'dejado_almacen') bg-yellow-100 text-yellow-800
                                    @elseif($shipmentStatus === 'en_nicaragua') bg-purple-100 text-purple-800
                                    @elseif($shipmentStatus === 'entregado') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    @switch($shipmentStatus)
                                        @case('por_recepcionar') Por Recepcionar @break
                                        @case('recepcionado') Recepcionado @break
                                        @case('dejado_almacen') Dejado en Almacén @break
                                        @case('en_nicaragua') En Nicaragua @break
                                        @case('entregado') Entregado @break
                                        @default {{ ucfirst($shipmentStatus) }}
                                    @endswitch
                                </span>
                            </p>
                            <p><strong>Precio Final:</strong> <span class="text-lg font-bold text-green-600">${{ number_format($finalPrice, 2) }}</span></p>
                            @if($notes)
                                <p><strong>Notas:</strong> {{ $notes }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Resumen Final -->
                <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-lg font-semibold text-gray-800 mb-3">📊 Resumen Final</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div class="text-center">
                            <p class="text-gray-600">Cliente</p>
                            <p class="font-semibold">{{ $this->selectedClient->full_name ?? 'No seleccionado' }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-600">Receptor</p>
                            <p class="font-semibold">{{ $this->selectedRecipient->full_name ?? 'No seleccionado' }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-600">Precio Total</p>
                            <p class="font-bold text-lg text-green-600">${{ number_format($finalPrice, 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end space-x-3 mt-6">
                    <button 
                        wire:click="hidePreview"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500"
                    >
                        Volver a Editar
                    </button>
                    <button 
                        wire:click="saveShipment"
                        class="px-6 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                    >
                        ✅ Confirmar y Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
