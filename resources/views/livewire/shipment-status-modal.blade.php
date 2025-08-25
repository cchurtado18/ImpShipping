<div>
    @if($showModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="status-modal">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" style="width: 384px; max-width: 384px;">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Cambiar Estado del Envío</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                @if($shipment)
                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                    <div class="text-sm text-gray-600">
                        <div><strong>Código:</strong> {{ $shipment->code }}</div>
                        <div><strong>Cliente:</strong> {{ $shipment->client->full_name }}</div>
                        <div><strong>Receptor:</strong> {{ $shipment->recipient->full_name }}</div>
                        <div><strong>Estado Actual:</strong> 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $shipment->shipment_status_color }}">
                                {{ $shipment->shipment_status_icon }} {{ $shipment->shipment_status_label }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                <form wire:submit.prevent="updateStatus">
                    <div class="mb-4">
                        <label for="newStatus" class="block text-sm font-medium text-gray-700 mb-1">Nuevo Estado *</label>
                        <select wire:model="newStatus" 
                                id="newStatus"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach($this->getStatusOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('newStatus') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                wire:click="closeModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Guardar
                        </button>
                    </div>
                </form>

                <!-- Mensajes de éxito -->
                @if(session()->has('message'))
                <div class="mt-4 p-2 bg-green-100 border border-green-400 text-green-700 rounded text-sm">
                    {{ session('message') }}
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
