<div>
    <!-- Header -->
    <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Tracking</h2>
                <p class="text-sm text-gray-600">Historial de seguimiento por paquete</p>
            </div>
        </div>

        <!-- Búsqueda y filtros -->
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input wire:model.live="search" type="text" 
                       placeholder="Buscar por código de tracking, warehouse o cliente..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
            </div>
            <div class="sm:w-48">
                <select wire:model.live="statusFilter" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    <option value="">Todos los estados</option>
                    <option value="requested">Solicitado</option>
                    <option value="scheduled">Programado</option>
                    <option value="collected">Recolectado</option>
                    <option value="warehouse_usa">En bodega USA</option>
                    <option value="in_transit">En tránsito</option>
                    <option value="customs_ni">En aduana NI</option>
                    <option value="warehouse_ni">En bodega NI</option>
                    <option value="ready_delivery">Listo para entrega</option>
                    <option value="delivered">Entregado</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabla de paquetes con tracking -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paquete</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destinatario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado Actual</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Último Evento</th>
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
                        <div class="text-sm text-gray-500">{{ $package->recipient->city }}, {{ $package->recipient->department }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($package->status === 'requested') bg-yellow-100 text-yellow-800
                            @elseif($package->status === 'scheduled') bg-blue-100 text-blue-800
                            @elseif($package->status === 'collected') bg-green-100 text-green-800
                            @elseif($package->status === 'warehouse_usa') bg-purple-100 text-purple-800
                            @elseif($package->status === 'in_transit') bg-indigo-100 text-indigo-800
                            @elseif($package->status === 'customs_ni') bg-orange-100 text-orange-800
                            @elseif($package->status === 'warehouse_ni') bg-pink-100 text-pink-800
                            @elseif($package->status === 'ready_delivery') bg-teal-100 text-teal-800
                            @elseif($package->status === 'delivered') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $package->status_in_spanish }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($package->trackingEvents->count() > 0)
                            @php $lastEvent = $package->trackingEvents->sortByDesc('event_date')->first(); @endphp
                            <div class="text-sm">{{ $lastEvent->description }}</div>
                            <div class="text-xs text-gray-500">{{ $lastEvent->event_date->format('M d, Y H:i') }}</div>
                            @if($lastEvent->location)
                                <div class="text-xs text-gray-400">{{ $lastEvent->location }}</div>
                            @endif
                        @else
                            <div class="text-sm text-gray-500">Sin eventos</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <button wire:click="showTrackingForm({{ $package->id }})" 
                                class="text-cyan-600 hover:text-cyan-900">Agregar Evento</button>
                        <a href="{{ route('tracking.show', $package->tracking_code) }}" 
                           class="text-blue-600 hover:text-blue-900" target="_blank">Ver Historial</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                        No hay paquetes registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $packages->links() }}
    </div>

    <!-- Modal de formulario -->
    @if($showForm && $selectedPackage)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="tracking-modal">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Agregar Evento de Tracking</h3>
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
                    <div class="text-sm text-gray-600">Estado actual: {{ $selectedPackage->status_in_spanish }}</div>
                </div>

                <form wire:submit.prevent="saveTrackingEvent">
                    <!-- Estado -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nuevo Estado *</label>
                        <select wire:model="status" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <option value="requested">Solicitado</option>
                            <option value="scheduled">Programado</option>
                            <option value="collected">Recolectado</option>
                            <option value="warehouse_usa">En bodega USA</option>
                            <option value="in_transit">En tránsito</option>
                            <option value="customs_ni">En aduana NI</option>
                            <option value="warehouse_ni">En bodega NI</option>
                            <option value="ready_delivery">Listo para entrega</option>
                            <option value="delivered">Entregado</option>
                        </select>
                        @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Ubicación -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                        <input wire:model="location" type="text" 
                               placeholder="Ej: Miami, FL - Bodega Principal"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        @error('location') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Descripción -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción *</label>
                        <textarea wire:model="description" rows="3" 
                                  placeholder="Describe el evento de tracking..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"></textarea>
                        @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Fecha y hora -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha y Hora *</label>
                        <input wire:model="event_date" type="datetime-local" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        @error('event_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Registrado por -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Registrado por *</label>
                        <input wire:model="created_by" type="text" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        @error('created_by') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="closeForm" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-cyan-600 border border-transparent rounded-md hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            Agregar Evento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
