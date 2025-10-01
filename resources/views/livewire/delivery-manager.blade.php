<div>
    <!-- Header -->
    <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Entregas</h2>
                <p class="text-sm text-gray-600">Confirma entregas con evidencia</p>
            </div>
        </div>

        <!-- Búsqueda -->
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input wire:model.live="search" type="text" 
                       placeholder="Buscar por código de tracking, warehouse o cliente..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white shadow-sm rounded-lg mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Paquetes Listos para Entrega
                </button>
                <button class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Entregas Confirmadas
                </button>
            </nav>
        </div>
    </div>

    <!-- Paquetes listos para entrega -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Paquetes Listos para Entrega</h3>
            <p class="text-sm text-gray-600">Paquetes que están listos para ser entregados</p>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paquete</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destinatario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dirección</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($packages as $package)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $package->tracking_code }}</div>
                        @if($package->warehouse_code)
                            <div class="text-sm text-gray-500">{{ $package->warehouse_code }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $package->client->full_name }}</div>
                        <div class="text-sm text-gray-500">{{ $package->client->us_phone }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $package->recipient->name }}</div>
                        <div class="text-sm text-gray-500">{{ $package->recipient->phone }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $package->recipient->address }}</div>
                        <div class="text-sm text-gray-500">{{ $package->recipient->city }}, {{ $package->recipient->department }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button wire:click="showDeliveryForm({{ $package->id }})" 
                                class="text-violet-600 hover:text-violet-900">Confirmar Entrega</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                        No hay paquetes listos para entrega.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Entregas confirmadas -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Entregas Confirmadas</h3>
            <p class="text-sm text-gray-600">Historial de entregas completadas</p>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paquete</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destinatario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Entrega</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recibido por</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evidencia</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($deliveries as $delivery)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $delivery->package->tracking_code }}</div>
                        @if($delivery->package->warehouse_code)
                            <div class="text-sm text-gray-500">{{ $delivery->package->warehouse_code }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $delivery->package->client->full_name }}</div>
                        <div class="text-sm text-gray-500">{{ $delivery->package->client->us_phone }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $delivery->package->recipient->name }}</div>
                        <div class="text-sm text-gray-500">{{ $delivery->package->recipient->phone }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $delivery->delivery_date->format('M d, Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $delivery->received_by }}
                        @if($delivery->delivered_by)
                            <div class="text-sm text-gray-500">Entregado por: {{ $delivery->delivered_by }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($delivery->evidence_type === 'photo') bg-blue-100 text-blue-800
                            @else bg-green-100 text-green-800 @endif">
                            @if($delivery->evidence_type === 'photo') Foto
                            @else Firma @endif
                        </span>
                        @if($delivery->document_number)
                            <div class="text-sm text-gray-500">Doc: {{ $delivery->document_number }}</div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                        No hay entregas confirmadas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $deliveries->links() }}
    </div>

    <!-- Modal de formulario -->
    @if($showForm && $selectedPackage)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="delivery-modal">
        <div class="relative top-10 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Confirmar Entrega</h3>
                    <button wire:click="closeForm" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Información del paquete -->
                <div class="bg-gray-50 p-4 rounded-md mb-4">
                    <div class="text-sm text-gray-600">Paquete: {{ $selectedPackage->tracking_code }}</div>
                    <div class="text-sm text-gray-600">Cliente: {{ $selectedPackage->client->full_name }}</div>
                    <div class="text-sm text-gray-600">Destinatario: {{ $selectedPackage->recipient->name }}</div>
                    <div class="text-sm text-gray-600">Dirección: {{ $selectedPackage->recipient->address }}</div>
                </div>

                <form wire:submit.prevent="saveDelivery">
                    <!-- Fecha y hora -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha y Hora de Entrega *</label>
                        <input wire:model="delivery_date" type="datetime-local" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500">
                        @error('delivery_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Quien recibió -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quien recibió *</label>
                        <input wire:model="received_by" type="text" 
                               placeholder="Nombre de quien recibió el paquete"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500">
                        @error('received_by') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Quien entregó -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quien entregó</label>
                        <input wire:model="delivered_by" type="text" 
                               placeholder="Nombre de quien entregó el paquete"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500">
                        @error('delivered_by') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Número de documento -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número de Documento</label>
                        <input wire:model="document_number" type="text" 
                               placeholder="Número de cédula o documento"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500">
                        @error('document_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tipo de evidencia -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Evidencia *</label>
                        <select wire:model="evidence_type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500">
                            <option value="photo">Foto</option>
                            <option value="signature">Firma</option>
                        </select>
                        @error('evidence_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Ruta de evidencia -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ruta de Evidencia</label>
                        <input wire:model="evidence_path" type="text" 
                               placeholder="Ruta del archivo de evidencia"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500">
                        @error('evidence_path') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Notas -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                        <textarea wire:model="notes" rows="3" 
                                  placeholder="Información adicional sobre la entrega..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500"></textarea>
                        @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="closeForm" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-violet-600 border border-transparent rounded-md hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-violet-500">
                            Confirmar Entrega
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
