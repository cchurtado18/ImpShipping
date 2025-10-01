<div>
    <!-- Header -->
    <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Paquetes</h2>
                <p class="text-sm text-gray-600">Registra paquetes con cálculo automático de ft³</p>
            </div>
            <button wire:click="addPackage" 
                    class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Agregar Paquete
            </button>
        </div>

        <!-- Búsqueda y filtros -->
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input wire:model.live="search" type="text" 
                       placeholder="Buscar por código de tracking, warehouse o cliente..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="sm:w-48">
                <select wire:model.live="statusFilter" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
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
            <div class="sm:w-48">
                <select wire:model.live="clientFilter" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Todos los clientes</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->full_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Tabla de paquetes -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tracking</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destinatario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medidas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peso</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ft³</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recibido por</th>
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $package->length }}" × {{ $package->width }}" × {{ $package->height }}"
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $package->weight }} lb
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $package->cubic_feet }} ft³
                        </span>
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
                        {{ $package->received_by }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <button wire:click="editPackage({{ $package->id }})" 
                                class="text-purple-600 hover:text-purple-900">Editar</button>
                        <button wire:click="deletePackage({{ $package->id }})" 
                                class="text-red-600 hover:text-red-900"
                                onclick="return confirm('¿Estás seguro de que querés eliminar este paquete?')">Eliminar</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">
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
    @if($showForm)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="package-modal">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $editingPackage ? 'Editar Paquete' : 'Agregar Nuevo Paquete' }}
                    </h3>
                    <button wire:click="closeForm" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="savePackage">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Columna izquierda -->
                        <div class="space-y-4">
                            <!-- Cliente -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                                <select wire:model="client_id" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="">Seleccionar cliente...</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->full_name }}</option>
                                    @endforeach
                                </select>
                                @error('client_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Destinatario -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Destinatario *</label>
                                <select wire:model="recipient_id" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="">Seleccionar destinatario...</option>
                                    @foreach($recipients as $recipient)
                                        <option value="{{ $recipient->id }}">{{ $recipient->name }} ({{ $recipient->client->full_name }})</option>
                                    @endforeach
                                </select>
                                @error('recipient_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Tracking Code -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Código de Tracking</label>
                                <input wire:model="tracking_code" type="text" 
                                       placeholder="Se genera automáticamente si se deja vacío"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                @error('tracking_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Warehouse Code -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Código de Warehouse</label>
                                <input wire:model="warehouse_code" type="text" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                @error('warehouse_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Columna derecha -->
                        <div class="space-y-4">
                            <!-- Medidas -->
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Largo (pulgadas) *</label>
                                    <input wire:model="length" type="number" step="0.1" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    @error('length') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ancho (pulgadas) *</label>
                                    <input wire:model="width" type="number" step="0.1" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    @error('width') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alto (pulgadas) *</label>
                                    <input wire:model="height" type="number" step="0.1" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    @error('height') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Peso y ft³ -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Peso (libras) *</label>
                                    <input wire:model="weight" type="number" step="0.1" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    @error('weight') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ft³ (calculado automáticamente)</label>
                                    <input wire:model="cubic_feet" type="text" readonly
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600">
                                </div>
                            </div>

                            <!-- Método de envío y cobro -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Método de envío *</label>
                                    <select wire:model="shipping_method" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                        <option value="air">Aéreo</option>
                                        <option value="sea">Marítimo</option>
                                    </select>
                                    @error('shipping_method') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de cobro *</label>
                                    <select wire:model="billing_type" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                        <option value="volume">Por ft³</option>
                                        <option value="weight">Por peso</option>
                                    </select>
                                    @error('billing_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Recibido por y estado -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Recibido por *</label>
                                    <select wire:model="received_by" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                        <option value="Francisco">Francisco</option>
                                        <option value="Giovanni">Giovanni</option>
                                        <option value="Elmer">Elmer</option>
                                    </select>
                                    @error('received_by') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                                    <select wire:model="status" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
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
                                class="px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            {{ $editingPackage ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
