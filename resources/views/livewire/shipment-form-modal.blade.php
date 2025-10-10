<div>
    @if($showModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="shipment-modal">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
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

                    <!-- Opción de dimensiones -->
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="customDimensions" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm font-medium text-gray-700">Use Custom Dimensions</span>
                        </label>
                    </div>

                    @if(!$customDimensions)
                    <!-- Caja Predefinida -->
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
                    @else
                    <!-- Dimensiones Personalizadas -->
                    <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <h4 class="text-sm font-semibold text-blue-800 mb-3">📏 Custom Box Dimensions</h4>
                        
                        <div class="grid grid-cols-3 gap-3 mb-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Length (in)</label>
                                <input 
                                    type="number" 
                                    wire:model="boxLength"
                                    step="0.1"
                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="0.0"
                                >
                                @error('boxLength') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Width (in)</label>
                                <input 
                                    type="number" 
                                    wire:model="boxWidth"
                                    step="0.1"
                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="0.0"
                                >
                                @error('boxWidth') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Height (in)</label>
                                <input 
                                    type="number" 
                                    wire:model="boxHeight"
                                    step="0.1"
                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="0.0"
                                >
                                @error('boxHeight') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <!-- Información de cálculo -->
                        @if($boxLength > 0 && $boxWidth > 0 && $boxHeight > 0)
                            @php
                                $cubicFeet = ($boxLength * $boxWidth * $boxHeight) / 1728;
                            @endphp
                            <div class="text-xs text-blue-700 mb-3">
                                <strong>Volume:</strong> {{ number_format($cubicFeet, 2) }} ft³
                                @if($cubicFeet <= 2.99)
                                    <span class="text-orange-600">(Weight-based pricing)</span>
                                @else
                                    <span class="text-green-600">(Volume-based pricing)</span>
                                @endif
                            </div>
                            
                            @if($cubicFeet <= 2.99)
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Weight (lbs)</label>
                                    <input 
                                        type="number" 
                                        wire:model="boxWeight"
                                        step="0.1"
                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        placeholder="0.0"
                                    >
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Rate ($/lb)</label>
                                    <input 
                                        type="number" 
                                        wire:model="boxWeightRate"
                                        step="0.01"
                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        placeholder="0.00"
                                    >
                                </div>
                            </div>
                            @endif
                        @endif
                    </div>
                    @endif

                    <!-- Modo de Precio -->
                    @if($customDimensions && $calculatedPrice > 0)
                    <div class="mb-4 p-3 bg-green-50 rounded-lg border border-green-200">
                        <label class="block text-sm font-medium text-green-800 mb-2">💰 Price Mode</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" wire:model="useCalculatedPrice" value="true" class="text-green-600 focus:ring-green-500">
                                <span class="ml-2 text-sm text-green-700">
                                    <strong>Calculated Price</strong> - ${{ number_format($calculatedPrice, 2) }}
                                </span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" wire:model="useCalculatedPrice" value="false" class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-blue-700">
                                    <strong>Manual Price</strong> - Set your own amount
                                </span>
                            </label>
                        </div>
                    </div>
                    @endif

                    <!-- Precio -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Final Price (USD) *</label>
                        
                        @if($customDimensions && $calculatedPrice > 0 && $useCalculatedPrice)
                            <!-- Mostrar precio calculado (solo lectura) -->
                            <div class="w-full px-3 py-2 border border-green-300 bg-green-50 rounded-md text-green-800 font-semibold">
                                ${{ number_format($calculatedPrice, 2) }} (Auto-calculated)
                            </div>
                            <div class="mt-1 text-xs text-green-600">
                                ✅ Using calculated price based on dimensions
                            </div>
                        @else
                            <!-- Campo editable para precio manual -->
                            <div class="relative">
                                <input 
                                    type="number" 
                                    @if($customDimensions && $calculatedPrice > 0)
                                        wire:model="manualPrice"
                                    @else
                                        wire:model="finalPrice"
                                    @endif
                                    step="0.01"
                                    class="w-full px-3 py-2 border border-blue-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="0.00"
                                >
                                @if($customDimensions && $calculatedPrice > 0)
                                    <div class="absolute right-2 top-2 text-xs text-blue-500">
                                        Manual
                                    </div>
                                @endif
                            </div>
                            @if($customDimensions && $calculatedPrice > 0)
                                <div class="mt-1 text-xs text-blue-600">
                                    💡 Manual override - Calculated was ${{ number_format($calculatedPrice, 2) }}
                                </div>
                            @endif
                        @endif
                        
                        @error('finalPrice') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @error('manualPrice') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
                        📋 Shipment Preview
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
                        <h4 class="text-lg font-semibold text-blue-800 mb-3">👤 Client Information</h4>
                        @if($this->selectedClient)
                            <div class="space-y-2 text-sm">
                                <p><strong>Name:</strong> {{ $this->selectedClient->full_name }}</p>
                                <p><strong>US Phone:</strong> {{ $this->selectedClient->us_phone }}</p>
                                <p><strong>Email:</strong> {{ $this->selectedClient->email }}</p>
                                <p><strong>State:</strong> {{ $this->selectedClient->us_state }}</p>
                            </div>
                        @else
                            <p class="text-red-500">No client selected</p>
                        @endif
                    </div>

                    <!-- Información del Receptor -->
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-green-800 mb-3">📦 Recipient Information</h4>
                        @if($this->selectedRecipient)
                            <div class="space-y-2 text-sm">
                                <p><strong>Name:</strong> {{ $this->selectedRecipient->full_name }}</p>
                                <p><strong>Phone:</strong> {{ $this->selectedRecipient->ni_phone }}</p>
                                <p><strong>Age:</strong> {{ $this->selectedRecipient->age ?? 'Not specified' }}</p>
                                <p><strong>Department:</strong> {{ $this->selectedRecipient->ni_department }}</p>
                                <p><strong>City:</strong> {{ $this->selectedRecipient->ni_city }}</p>
                                <p><strong>Address:</strong> {{ $this->selectedRecipient->ni_address }}</p>
                            </div>
                        @else
                            <p class="text-red-500">No recipient selected</p>
                        @endif
                    </div>

                    <!-- Información de la Caja -->
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-yellow-800 mb-3">📦 Box Information</h4>
                        @if($customDimensions)
                            <div class="space-y-2 text-sm">
                                <p><strong>Type:</strong> Custom Dimensions</p>
                                <p><strong>Dimensions:</strong> {{ $boxLength }}" × {{ $boxWidth }}" × {{ $boxHeight }}"</p>
                                @if($boxLength > 0 && $boxWidth > 0 && $boxHeight > 0)
                                    @php
                                        $cubicFeet = ($boxLength * $boxWidth * $boxHeight) / 1728;
                                    @endphp
                                    <p><strong>Volume:</strong> {{ number_format($cubicFeet, 2) }} ft³</p>
                                @endif
                                @if($boxWeight > 0 && $boxWeightRate > 0)
                                    <p><strong>Weight:</strong> {{ $boxWeight }} lbs @ ${{ number_format($boxWeightRate, 2) }}/lb</p>
                                @endif
                                <p><strong>Calculated Price:</strong> ${{ number_format($calculatedPrice, 2) }}</p>
                                <p class="text-blue-600">
                                    <strong>Price Mode:</strong> 
                                    @if($useCalculatedPrice)
                                        <span class="text-green-600">✅ Calculated Price</span> - ${{ number_format($calculatedPrice, 2) }}
                                    @else
                                        <span class="text-blue-600">✏️ Manual Price</span> - ${{ number_format($manualPrice, 2) }}
                                    @endif
                                </p>
                            </div>
                        @elseif($this->selectedBox)
                            <div class="space-y-2 text-sm">
                                <p><strong>Type:</strong> Predefined Box</p>
                                <p><strong>Code:</strong> {{ $this->selectedBox->code }}</p>
                                <p><strong>Dimensions:</strong> {{ $this->selectedBox->length_in }}" × {{ $this->selectedBox->width_in }}" × {{ $this->selectedBox->height_in }}"</p>
                                <p><strong>Volume:</strong> {{ number_format(($this->selectedBox->length_in * $this->selectedBox->width_in * $this->selectedBox->height_in) / 1728, 2) }} ft³</p>
                                <p><strong>Base Price:</strong> ${{ number_format($this->selectedBox->base_price_usd, 2) }}</p>
                                <p><strong>Final Price:</strong> ${{ number_format($finalPrice, 2) }}</p>
                            </div>
                        @else
                            <p class="text-red-500">No box selected</p>
                        @endif
                    </div>

                    <!-- Información del Envío -->
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-purple-800 mb-3">🚚 Shipment Information</h4>
                        <div class="space-y-2 text-sm">
                            <p><strong>Route:</strong> {{ $route->name ?? 'Current Route' }}</p>
                            <p><strong>Status:</strong> 
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($shipmentStatus === 'por_recepcionar') bg-gray-100 text-gray-800
                                    @elseif($shipmentStatus === 'recepcionado') bg-blue-100 text-blue-800
                                    @elseif($shipmentStatus === 'dejado_almacen') bg-yellow-100 text-yellow-800
                                    @elseif($shipmentStatus === 'en_nicaragua') bg-purple-100 text-purple-800
                                    @elseif($shipmentStatus === 'entregado') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    @switch($shipmentStatus)
                                        @case('por_recepcionar') To Receive @break
                                        @case('recepcionado') Received @break
                                        @case('dejado_almacen') Left at Warehouse @break
                                        @case('en_nicaragua') In Nicaragua @break
                                        @case('entregado') Delivered @break
                                        @default {{ ucfirst($shipmentStatus) }}
                                    @endswitch
                                </span>
                            </p>
                            <p><strong>Final Price:</strong> <span class="text-lg font-bold text-green-600">${{ number_format($finalPrice, 2) }}</span></p>
                            @if($notes)
                                <p><strong>Notes:</strong> {{ $notes }}</p>
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
                            @if($customDimensions && $calculatedPrice > 0)
                                @if($useCalculatedPrice)
                                    <p class="font-bold text-lg text-green-600">${{ number_format($calculatedPrice, 2) }}</p>
                                    <p class="text-xs text-green-600">✅ Calculated</p>
                                @else
                                    <p class="font-bold text-lg text-blue-600">${{ number_format($manualPrice, 2) }}</p>
                                    <p class="text-xs text-blue-600">✏️ Manual</p>
                                @endif
                            @else
                                <p class="font-bold text-lg text-green-600">${{ number_format($finalPrice, 2) }}</p>
                            @endif
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
