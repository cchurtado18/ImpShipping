<div>
    <!-- Header -->
    <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Recolecciones</h2>
                <p class="text-sm text-gray-600">Programa recolecciones con ventana horaria</p>
            </div>
            <button wire:click="addPickup" 
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Agregar Recolección
            </button>
        </div>

        <!-- Búsqueda y filtros -->
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input wire:model.live="search" type="text" 
                       placeholder="Buscar por cliente o destinatario..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="sm:w-48">
                <select wire:model.live="routeFilter" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Todas las rutas</option>
                    @foreach($routes as $route)
                        <option value="{{ $route->id }}">{{ $route->month }} - {{ $route->responsible }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:w-48">
                <select wire:model.live="statusFilter" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Todos los estados</option>
                    <option value="scheduled">Programada</option>
                    <option value="completed">Completada</option>
                    <option value="cancelled">Cancelada</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabla de recolecciones -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destinatario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ruta</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ventana Horaria</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pickups as $pickup)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $pickup->client->full_name }}</div>
                        <div class="text-sm text-gray-500">{{ $pickup->client->us_phone }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $pickup->recipient->name }}</div>
                        <div class="text-sm text-gray-500">{{ $pickup->recipient->city }}, {{ $pickup->recipient->department }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $pickup->route->month }}</div>
                        <div class="text-sm text-gray-500">{{ $pickup->route->responsible }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $pickup->pickup_date->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $pickup->time_window_start->format('H:i') }} - {{ $pickup->time_window_end->format('H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($pickup->status === 'scheduled') bg-blue-100 text-blue-800
                            @elseif($pickup->status === 'completed') bg-green-100 text-green-800
                            @elseif($pickup->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            @if($pickup->status === 'scheduled') Programada
                            @elseif($pickup->status === 'completed') Completada
                            @elseif($pickup->status === 'cancelled') Cancelada
                            @else {{ $pickup->status }} @endif
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <button wire:click="editPickup({{ $pickup->id }})" 
                                class="text-indigo-600 hover:text-indigo-900">Editar</button>
                        <button wire:click="deletePickup({{ $pickup->id }})" 
                                class="text-red-600 hover:text-red-900"
                                onclick="return confirm('¿Estás seguro de que querés eliminar esta recolección?')">Eliminar</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                        No hay recolecciones registradas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $pickups->links() }}
    </div>

    <!-- Modal de formulario -->
    @if($showForm)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="pickup-modal">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $editingPickup ? 'Editar Recolección' : 'Agregar Nueva Recolección' }}
                    </h3>
                    <button wire:click="closeForm" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="savePickup">
                    <!-- Cliente -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                        <select wire:model="client_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Seleccionar cliente...</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->full_name }}</option>
                            @endforeach
                        </select>
                        @error('client_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Destinatario -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Destinatario *</label>
                        <select wire:model="recipient_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Seleccionar destinatario...</option>
                            @foreach($recipients as $recipient)
                                <option value="{{ $recipient->id }}">{{ $recipient->name }} ({{ $recipient->client->full_name }})</option>
                            @endforeach
                        </select>
                        @error('recipient_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Ruta -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ruta *</label>
                        <select wire:model="route_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Seleccionar ruta...</option>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}">{{ $route->month }} - {{ $route->responsible }}</option>
                            @endforeach
                        </select>
                        @error('route_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Fecha -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Recolección *</label>
                        <input wire:model="pickup_date" type="date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('pickup_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Ventana Horaria -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hora de inicio *</label>
                            <input wire:model="time_window_start" type="time" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @error('time_window_start') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hora de fin *</label>
                            <input wire:model="time_window_end" type="time" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @error('time_window_end') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                        <select wire:model="status" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="scheduled">Programada</option>
                            <option value="completed">Completada</option>
                            <option value="cancelled">Cancelada</option>
                        </select>
                        @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Notas -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                        <textarea wire:model="notes" rows="3" 
                                  placeholder="Información adicional sobre la recolección..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                        @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="closeForm" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            {{ $editingPickup ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
