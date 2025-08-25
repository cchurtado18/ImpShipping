<div>
    @if($showModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="shipment-modal">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" style="width: 384px; max-width: 384px;">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $editingShipment ? 'Editar Envío' : 'Agregar Nuevo Envío' }}
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                        <select 
                            wire:model="selectedClient"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Seleccionar cliente...</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->full_name }} - {{ $client->us_phone }}</option>
                            @endforeach
                        </select>
                        @error('selectedClient') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Receptor -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Receptor *</label>
                        <select 
                            wire:model="selectedRecipient"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Seleccionar receptor...</option>
                            @foreach($recipients as $recipient)
                            <option value="{{ $recipient->id }}">{{ $recipient->full_name }} - {{ $recipient->ni_phone }}</option>
                            @endforeach
                        </select>
                        @error('selectedRecipient') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Caja -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Caja *</label>
                        <select 
                            wire:model="selectedBox"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Seleccionar caja...</option>
                            @foreach($boxes as $box)
                            <option value="{{ $box->id }}">{{ $box->code }} ({{ $box->cubic_feet }} ft³)</option>
                            @endforeach
                        </select>
                        @error('selectedBox') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Precio -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio Final (USD) *</label>
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                        <select 
                            wire:model="shipmentStatus"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="por_recepcionar">📦 Por Recepcionar</option>
                            <option value="recepcionado">✅ Recepcionado</option>
                            <option value="dejado_almacen">🏪 Dejado en Almacén</option>
                            <option value="en_nicaragua">🇳🇮 En Nicaragua</option>
                            <option value="entregado">🎉 Entregado</option>
                        </select>
                        @error('shipmentStatus') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Notas -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                        <textarea 
                            wire:model="notes"
                            rows="2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Información adicional sobre el envío..."
                        ></textarea>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3">
                        <button 
                            type="button" 
                            wire:click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            {{ $editingShipment ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
