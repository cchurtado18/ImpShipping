<div>
    <!-- Header -->
    <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Envíos</h2>
                <p class="text-sm text-gray-600">Consolida paquetes en envíos (guías aéreas o contenedores)</p>
            </div>
            <button wire:click="addShipment" 
                    class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 focus:bg-teal-700 active:bg-teal-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Crear Envío
            </button>
        </div>

        <!-- Búsqueda y filtros -->
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input wire:model.live="search" type="text" 
                       placeholder="Buscar por nombre o número de referencia..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
            </div>
            <div class="sm:w-48">
                <select wire:model.live="statusFilter" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="">Todos los estados</option>
                    <option value="open">Abierto</option>
                    <option value="in_transit">En tránsito</option>
                    <option value="received_ni">Recibido NI</option>
                    <option value="closed">Cerrado</option>
                </select>
            </div>
            <div class="sm:w-48">
                <select wire:model.live="typeFilter" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="">Todos los tipos</option>
                    <option value="air">Aéreo</option>
                    <option value="sea">Marítimo</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabla de envíos -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Envío</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paquetes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fechas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($shipments as $shipment)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $shipment->name }}</div>
                        @if($shipment->reference_number)
                            <div class="text-sm text-gray-500">{{ $shipment->reference_number }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($shipment->type === 'air') bg-blue-100 text-blue-800
                            @else bg-green-100 text-green-800 @endif">
                            @if($shipment->type === 'air') Aéreo
                            @else Marítimo @endif
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $shipment->packages->count() }} paquete(s)
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($shipment->departure_date)
                            <div>Salida: {{ $shipment->departure_date->format('M d, Y') }}</div>
                        @endif
                        @if($shipment->arrival_date)
                            <div>Llegada: {{ $shipment->arrival_date->format('M d, Y') }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($shipment->status === 'open') bg-yellow-100 text-yellow-800
                            @elseif($shipment->status === 'in_transit') bg-blue-100 text-blue-800
                            @elseif($shipment->status === 'received_ni') bg-green-100 text-green-800
                            @elseif($shipment->status === 'closed') bg-gray-100 text-gray-800
                            @else bg-gray-100 text-gray-800 @endif">
                            @if($shipment->status === 'open') Abierto
                            @elseif($shipment->status === 'in_transit') En tránsito
                            @elseif($shipment->status === 'received_ni') Recibido NI
                            @elseif($shipment->status === 'closed') Cerrado
                            @else {{ $shipment->status }} @endif
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <button wire:click="editShipment({{ $shipment->id }})" 
                                class="text-teal-600 hover:text-teal-900">Editar</button>
                        <button wire:click="deleteShipment({{ $shipment->id }})" 
                                class="text-red-600 hover:text-red-900"
                                onclick="return confirm('¿Estás seguro de que querés eliminar este envío?')">Eliminar</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                        No hay envíos registrados.
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

    <!-- Modal de formulario -->
    @if($showForm)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="shipment-modal">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $editingShipment ? 'Editar Envío' : 'Crear Nuevo Envío' }}
                    </h3>
                    <button wire:click="closeForm" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveShipment">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Columna izquierda -->
                        <div class="space-y-4">
                            <!-- Nombre -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Envío *</label>
                                <input wire:model="name" type="text" 
                                       placeholder="Ej: Guía Aérea 001, Contenedor ABC123"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Número de referencia -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Referencia</label>
                                <input wire:model="reference_number" type="text" 
                                       placeholder="Número de guía o contenedor"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                                @error('reference_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Tipo -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Envío *</label>
                                <select wire:model="type" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                                    <option value="air">Aéreo</option>
                                    <option value="sea">Marítimo</option>
                                </select>
                                @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Estado -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                                <select wire:model="status" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                                    <option value="open">Abierto</option>
                                    <option value="in_transit">En tránsito</option>
                                    <option value="received_ni">Recibido NI</option>
                                    <option value="closed">Cerrado</option>
                                </select>
                                @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Columna derecha -->
                        <div class="space-y-4">
                            <!-- Fechas -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Salida</label>
                                <input wire:model="departure_date" type="date" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                                @error('departure_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Llegada</label>
                                <input wire:model="arrival_date" type="date" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                                @error('arrival_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Paquetes disponibles -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Paquetes Disponibles</label>
                                <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-md p-3">
                                    @forelse($availablePackages as $package)
                                        <label class="flex items-center space-x-2 py-2 border-b border-gray-100 last:border-b-0">
                                            <input wire:model="selectedPackages" type="checkbox" value="{{ $package->id }}" 
                                                   class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-gray-900">{{ $package->tracking_code }}</div>
                                                <div class="text-sm text-gray-500">{{ $package->client->full_name }} → {{ $package->recipient->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $package->cubic_feet }} ft³, {{ $package->weight }} lb</div>
                                            </div>
                                        </label>
                                    @empty
                                        <div class="text-sm text-gray-500 text-center py-4">No hay paquetes disponibles</div>
                                    @endforelse
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
                                class="px-4 py-2 text-sm font-medium text-white bg-teal-600 border border-transparent rounded-md hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
                            {{ $editingShipment ? 'Actualizar' : 'Crear Envío' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
