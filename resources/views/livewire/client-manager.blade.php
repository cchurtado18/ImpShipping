<div class="w-full">
    <!-- Contenido principal -->
    <div class="w-full">
        <!-- Header Banner -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Client Management</h2>
                    <p class="text-sm text-gray-600 mt-1">Manage and track all clients in the system</p>
                </div>
                <div class="flex space-x-3">
            <a href="{{ route('followups.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Followups
            </a>
            <a href="{{ route('route-leads.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Route Leads
            </a>
            <button wire:click="addClient" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Client
            </button>
                </div>
            </div>
        </div>

        <!-- Estadísticas por estado -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-2">
                        <p class="text-xs font-medium text-yellow-800">Following Up</p>
                        <p class="text-lg font-bold text-yellow-900">{{ $statusCounts['en_seguimiento'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 p-4 rounded-lg border border-green-200 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-2">
                        <p class="text-xs font-medium text-green-800">Confirmed</p>
                        <p class="text-lg font-bold text-green-900">{{ $statusCounts['confirmado'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="ml-2">
                        <p class="text-xs font-medium text-blue-800">Next Route</p>
                        <p class="text-lg font-bold text-blue-900">{{ $statusCounts['proxima_ruta'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-red-50 p-4 rounded-lg border border-red-200 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="ml-2">
                        <p class="text-xs font-medium text-red-800">Cancelled</p>
                        <p class="text-lg font-bold text-red-900">{{ $statusCounts['ruta_cancelada'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <input wire:model.live="search" type="text" 
                       placeholder="Search by name, phone or email..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="md:w-48">
                <select wire:model.live="stateFilter" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All US States</option>
                    <option value="AL">Alabama</option>
                    <option value="AK">Alaska</option>
                    <option value="AZ">Arizona</option>
                    <option value="AR">Arkansas</option>
                    <option value="CA">California</option>
                    <option value="CO">Colorado</option>
                    <option value="CT">Connecticut</option>
                    <option value="DE">Delaware</option>
                    <option value="FL">Florida</option>
                    <option value="GA">Georgia</option>
                    <option value="HI">Hawaii</option>
                    <option value="ID">Idaho</option>
                    <option value="IL">Illinois</option>
                    <option value="IN">Indiana</option>
                    <option value="IA">Iowa</option>
                    <option value="KS">Kansas</option>
                    <option value="KY">Kentucky</option>
                    <option value="LA">Louisiana</option>
                    <option value="ME">Maine</option>
                    <option value="MD">Maryland</option>
                    <option value="MA">Massachusetts</option>
                    <option value="MI">Michigan</option>
                    <option value="MN">Minnesota</option>
                    <option value="MS">Mississippi</option>
                    <option value="MO">Missouri</option>
                    <option value="MT">Montana</option>
                    <option value="NE">Nebraska</option>
                    <option value="NV">Nevada</option>
                    <option value="NH">New Hampshire</option>
                    <option value="NJ">New Jersey</option>
                    <option value="NM">New Mexico</option>
                    <option value="NY">New York</option>
                    <option value="NC">North Carolina</option>
                    <option value="ND">North Dakota</option>
                    <option value="OH">Ohio</option>
                    <option value="OK">Oklahoma</option>
                    <option value="OR">Oregon</option>
                    <option value="PA">Pennsylvania</option>
                    <option value="RI">Rhode Island</option>
                    <option value="SC">South Carolina</option>
                    <option value="SD">South Dakota</option>
                    <option value="TN">Tennessee</option>
                    <option value="TX">Texas</option>
                    <option value="UT">Utah</option>
                    <option value="VT">Vermont</option>
                    <option value="VA">Virginia</option>
                    <option value="WA">Washington</option>
                    <option value="WV">West Virginia</option>
                    <option value="WI">Wisconsin</option>
                    <option value="WY">Wyoming</option>
                    <option value="DC">District of Columbia</option>
                </select>
            </div>
            <div class="md:w-48">
                <select wire:model.live="statusFilter" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    <option value="en_seguimiento">Following Up</option>
                    <option value="confirmado">Confirmed</option>
                    <option value="proxima_ruta">Next Route</option>
                    <option value="ruta_cancelada">Route Cancelled</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabla de clientes -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipients</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shipments</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($clients as $client)
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $client->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($client->us_address, 50) }}</div>
                            @if($client->us_state)
                            <div class="text-xs text-blue-600 font-medium">{{ $client->us_state }}</div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $client->us_phone }}</div>
                        @if($client->email)
                        <div class="text-sm text-gray-500">{{ $client->email }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <select wire:change="updateStatus({{ $client->id }}, $event.target.value)" 
                                class="text-xs px-2 py-1 rounded-full font-medium border-0 focus:ring-2 focus:ring-blue-500 {{ $client->status_badge }}">
                            <option value="en_seguimiento" {{ $client->status === 'en_seguimiento' ? 'selected' : '' }}>Following Up</option>
                            <option value="confirmado" {{ $client->status === 'confirmado' ? 'selected' : '' }}>Confirmed</option>
                            <option value="proxima_ruta" {{ $client->status === 'proxima_ruta' ? 'selected' : '' }}>Next Route</option>
                            <option value="ruta_cancelada" {{ $client->status === 'ruta_cancelada' ? 'selected' : '' }}>Route Cancelled</option>
                        </select>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $client->recipients_count ?? $client->recipients->count() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $client->shipments_count ?? $client->shipments->count() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <button wire:click="viewClient({{ $client->id }})" 
                                    class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View
                            </button>
                        <button wire:click="editClient({{ $client->id }})" 
                                    class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </button>
                        @if($client->shipments->count() === 0)
                        <button wire:click="deleteClient({{ $client->id }})" 
                                    class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                onclick="return confirm('Are you sure you want to delete this client?')">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            Delete
                        </button>
                        @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                        No clients registered.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $clients->links() }}
    </div>

    <!-- Modal para ver cliente -->
    @if($showViewModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Client Details</h3>
                    <button wire:click="closeViewModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                @if($viewingClient)
                <div class="space-y-6">
                    <!-- Información Básica -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">Basic Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                                <label class="block text-sm font-medium text-gray-700">Full Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $viewingClient->full_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $viewingClient->us_phone }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $viewingClient->email ?: 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Client Type</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    @if($viewingClient->client_type === 'subagency')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            Subagency
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Normal Client
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        </div>
                        
                    <!-- Dirección -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">Address Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">US Address</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $viewingClient->us_address }}</p>
                            </div>
                        <div>
                                <label class="block text-sm font-medium text-gray-700">State</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $viewingClient->us_state ?: 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información de Subagencia -->
                    @if($viewingClient->client_type === 'subagency')
                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                        <h4 class="text-md font-semibold text-purple-900 mb-3">Subagency Pricing</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Maritime Pound Cost</label>
                                <p class="mt-1 text-sm text-gray-900 font-semibold">${{ number_format($viewingClient->maritime_pound_cost, 2) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Air Pound Cost</label>
                                <p class="mt-1 text-sm text-gray-900 font-semibold">${{ number_format($viewingClient->air_pound_cost, 2) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cubic Foot Cost</label>
                                <p class="mt-1 text-sm text-gray-900 font-semibold">${{ number_format($viewingClient->cubic_foot_cost, 2) }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Estado y Notas -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">Status & Notes</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <p class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $viewingClient->status_badge }}">
                                        {{ $viewingClient->status_label }}
                                    </span>
                                </p>
                            </div>
                    <div>
                                <label class="block text-sm font-medium text-gray-700">Notes</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $viewingClient->notes ?: 'No notes' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">Statistics</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $viewingClient->recipients->count() }}</div>
                                <div class="text-sm text-gray-500">Recipients</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $viewingClient->shipments->count() }}</div>
                                <div class="text-sm text-gray-500">Shipments</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">{{ $viewingClient->payments->count() }}</div>
                                <div class="text-sm text-gray-500">Payments</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" wire:click="closeViewModal" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Close
                    </button>
                    <button type="button" wire:click="editClient({{ $viewingClient->id ?? 0 }})" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Edit Client
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal para agregar/editar cliente -->
    @if($showForm)
    <div class="fixed inset-0 bg-black bg-opacity-40 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-0 w-11/12 md:w-3/4 lg:w-1/2 xl:w-2/5 shadow-2xl rounded-2xl bg-white border border-gray-100">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 px-5 py-3 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 p-1.5 rounded-lg mr-2">
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white">
                            {{ $editingClient ? 'Edit Client' : 'Add New Client' }}
                        </h3>
                    </div>
                    <button wire:click="closeForm" class="text-white hover:text-green-100 transition-colors duration-150">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="p-5">
                
                <form wire:submit="saveClient" class="space-y-4">
                    <!-- Basic Information -->
                    <div class="space-y-3">
                        <h4 class="text-base font-semibold text-gray-800 flex items-center">
                            <svg class="h-4 w-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Basic Information
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                    <svg class="h-3.5 w-3.5 text-gray-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="full_name" type="text" 
                                       class="w-full px-2.5 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-150"
                                       placeholder="Enter client's full name">
                                @error('full_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                    <svg class="h-3.5 w-3.5 text-gray-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    Phone <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="us_phone" type="text" 
                                       class="w-full px-2.5 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-150"
                                       placeholder="Enter phone number">
                                @error('us_phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Address Information -->
                    <div class="space-y-3">
                        <h4 class="text-base font-semibold text-gray-800 flex items-center">
                            <svg class="h-4 w-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Address Information
                        </h4>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <svg class="h-3.5 w-3.5 text-gray-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                US Address <span class="text-red-500">*</span>
                            </label>
                            <textarea wire:model="us_address" rows="2" 
                                      class="w-full px-2.5 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-150 resize-none"
                                      placeholder="Enter complete US address"></textarea>
                            @error('us_address') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                    <svg class="h-3.5 w-3.5 text-gray-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                                    </svg>
                                    US State <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                        <select wire:model="us_state" 
                                            class="w-full px-2.5 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-150 bg-white appearance-none">
                            <option value="">Select state...</option>
                            <option value="AL">Alabama</option>
                            <option value="AK">Alaska</option>
                            <option value="AZ">Arizona</option>
                            <option value="AR">Arkansas</option>
                            <option value="CA">California</option>
                            <option value="CO">Colorado</option>
                            <option value="CT">Connecticut</option>
                            <option value="DE">Delaware</option>
                            <option value="FL">Florida</option>
                            <option value="GA">Georgia</option>
                            <option value="HI">Hawaii</option>
                            <option value="ID">Idaho</option>
                            <option value="IL">Illinois</option>
                            <option value="IN">Indiana</option>
                            <option value="IA">Iowa</option>
                            <option value="KS">Kansas</option>
                            <option value="KY">Kentucky</option>
                            <option value="LA">Louisiana</option>
                            <option value="ME">Maine</option>
                            <option value="MD">Maryland</option>
                            <option value="MA">Massachusetts</option>
                            <option value="MI">Michigan</option>
                            <option value="MN">Minnesota</option>
                            <option value="MS">Mississippi</option>
                            <option value="MO">Missouri</option>
                            <option value="MT">Montana</option>
                            <option value="NE">Nebraska</option>
                            <option value="NV">Nevada</option>
                            <option value="NH">New Hampshire</option>
                            <option value="NJ">New Jersey</option>
                            <option value="NM">New Mexico</option>
                            <option value="NY">New York</option>
                            <option value="NC">North Carolina</option>
                            <option value="ND">North Dakota</option>
                            <option value="OH">Ohio</option>
                            <option value="OK">Oklahoma</option>
                            <option value="OR">Oregon</option>
                            <option value="PA">Pennsylvania</option>
                            <option value="RI">Rhode Island</option>
                            <option value="SC">South Carolina</option>
                            <option value="SD">South Dakota</option>
                            <option value="TN">Tennessee</option>
                            <option value="TX">Texas</option>
                            <option value="UT">Utah</option>
                            <option value="VT">Vermont</option>
                            <option value="VA">Virginia</option>
                            <option value="WA">Washington</option>
                            <option value="WV">West Virginia</option>
                            <option value="WI">Wisconsin</option>
                            <option value="WY">Wyoming</option>
                            <option value="DC">District of Columbia</option>
                        </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                                @error('us_state') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                        <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                    <svg class="h-3.5 w-3.5 text-gray-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Email
                                </label>
                            <input wire:model="email" type="email" 
                                       class="w-full px-2.5 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-150"
                                       placeholder="Enter email address">
                                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact & Type Information -->
                    <div class="space-y-3">
                        <h4 class="text-base font-semibold text-gray-800 flex items-center">
                            <svg class="h-4 w-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Contact & Type Information
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                    <svg class="h-3.5 w-3.5 text-gray-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    Client Type <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select wire:model.live="client_type" 
                                            class="w-full px-2.5 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-150 bg-white appearance-none">
                                        <option value="normal">Normal Client</option>
                                        <option value="subagency">Subagency</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                                @error('client_type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                    <svg class="h-3.5 w-3.5 text-gray-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Status
                                </label>
                                <div class="relative">
                            <select wire:model="status" 
                                            class="w-full px-2.5 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-150 bg-white appearance-none">
                                <option value="en_seguimiento">Following Up</option>
                                <option value="confirmado">Confirmed</option>
                                <option value="proxima_ruta">Next Route</option>
                                <option value="ruta_cancelada">Route Cancelled</option>
                            </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                                @error('status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    
                    @if($client_type === 'subagency')
                    <!-- Subagency Pricing Configuration -->
                    <div class="space-y-3">
                        <div class="bg-blue-50 p-3 rounded-md border border-blue-200">
                            <h4 class="text-base font-semibold text-blue-900 mb-3 flex items-center">
                                <svg class="h-4 w-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                Subagency Pricing Configuration
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                        <svg class="h-3.5 w-3.5 text-gray-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        Maritime Pound Cost <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="maritime_pound_cost" type="number" step="0.01" 
                                           class="w-full px-2.5 py-2 text-sm border border-blue-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-150"
                                           placeholder="0.00">
                                    @error('maritime_pound_cost') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                        <svg class="h-3.5 w-3.5 text-gray-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                        Air Pound Cost <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="air_pound_cost" type="number" step="0.01" 
                                           class="w-full px-2.5 py-2 text-sm border border-blue-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-150"
                                           placeholder="0.00">
                                    @error('air_pound_cost') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                        <svg class="h-3.5 w-3.5 text-gray-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        Cubic Foot Cost <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="cubic_foot_cost" type="number" step="0.01" 
                                           class="w-full px-2.5 py-2 text-sm border border-blue-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-150"
                                           placeholder="0.00">
                                    @error('cubic_foot_cost') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Additional Information -->
                    <div class="space-y-3">
                        <h4 class="text-base font-semibold text-gray-800 flex items-center">
                            <svg class="h-4 w-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Additional Information
                        </h4>
                    
                    <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <svg class="h-3.5 w-3.5 text-gray-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Notes
                            </label>
                            <textarea wire:model="notes" rows="2" 
                                      class="w-full px-2.5 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-150 resize-none"
                                  placeholder="Additional information about the client..."></textarea>
                            @error('notes') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3 pt-3">
                        <button type="button" wire:click="closeForm" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-150">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-150 shadow-sm">
                            <svg class="w-4 h-4 inline mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ $editingClient ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

        <!-- Panel Informativo Inferior -->
        <div class="w-full mt-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Métricas Rápidas -->
        <div class="bg-white p-2 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-xs font-semibold text-gray-900 mb-2 flex items-center">
                <svg class="h-3 w-3 text-blue-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Quick Metrics
            </h3>
            <div class="space-y-1">
                <div class="flex justify-between items-center p-1.5 bg-blue-50 rounded">
                    <span class="text-xs font-medium text-gray-700">Total Clients</span>
                    <span class="text-xs font-bold text-blue-600">{{ $clients->count() }}</span>
                </div>
                <div class="flex justify-between items-center p-1.5 bg-green-50 rounded">
                    <span class="text-xs font-medium text-gray-700">Monthly Revenue</span>
                    <span class="text-xs font-bold text-green-600">${{ number_format(rand(15000, 45000), 0) }}</span>
                </div>
                <div class="flex justify-between items-center p-1.5 bg-yellow-50 rounded">
                    <span class="text-xs font-medium text-gray-700">Pending Shipments</span>
                    <span class="text-xs font-bold text-yellow-600">{{ rand(5, 25) }}</span>
                </div>
            </div>
        </div>

        <!-- Actividad Reciente -->
        <div class="bg-white p-2 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-xs font-semibold text-gray-900 mb-2 flex items-center">
                <svg class="h-3 w-3 text-purple-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Recent Activity
            </h3>
            <div class="space-y-1">
                <div class="flex items-center space-x-1 p-1">
                    <div class="w-1 h-1 bg-green-500 rounded-full"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-900">New client added</p>
                        <p class="text-xs text-gray-500">2h ago</p>
                    </div>
                </div>
                <div class="flex items-center space-x-1 p-1">
                    <div class="w-1 h-1 bg-blue-500 rounded-full"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-900">Status updated</p>
                        <p class="text-xs text-gray-500">4h ago</p>
                    </div>
                </div>
                <div class="flex items-center space-x-1 p-1">
                    <div class="w-1 h-1 bg-yellow-500 rounded-full"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-900">Followup scheduled</p>
                        <p class="text-xs text-gray-500">6h ago</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Tendencias -->
        <div class="bg-white p-2 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-xs font-semibold text-gray-900 mb-2 flex items-center">
                <svg class="h-3 w-3 text-indigo-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                Client Trends
            </h3>
            <div class="h-16 bg-gradient-to-r from-blue-50 to-indigo-50 rounded p-1.5 flex items-end justify-between">
                <div class="flex flex-col items-center">
                    <div class="w-2 bg-blue-400 rounded-t" style="height: 60%"></div>
                    <span class="text-xs text-gray-600 mt-0.5">Jan</span>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-2 bg-blue-400 rounded-t" style="height: 80%"></div>
                    <span class="text-xs text-gray-600 mt-0.5">Feb</span>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-2 bg-blue-400 rounded-t" style="height: 45%"></div>
                    <span class="text-xs text-gray-600 mt-0.5">Mar</span>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-2 bg-blue-400 rounded-t" style="height: 90%"></div>
                    <span class="text-xs text-gray-600 mt-0.5">Apr</span>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-2 bg-blue-400 rounded-t" style="height: 70%"></div>
                    <span class="text-xs text-gray-600 mt-0.5">May</span>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-2 bg-indigo-500 rounded-t" style="height: 100%"></div>
                    <span class="text-xs text-gray-600 mt-0.5">Jun</span>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-1 text-center">New clients per month</p>
        </div>

        <!-- Acciones Rápidas -->
        <div class="bg-white p-2 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-xs font-semibold text-gray-900 mb-2 flex items-center">
                <svg class="h-3 w-3 text-green-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Quick Actions
            </h3>
            <div class="space-y-1">
                <button class="w-full text-left p-1.5 bg-blue-50 hover:bg-blue-100 rounded transition-colors">
                    <div class="flex items-center">
                        <svg class="h-3 w-3 text-blue-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-xs font-medium text-blue-700">Generate Report</span>
                    </div>
                </button>
                
                <button class="w-full text-left p-1.5 bg-green-50 hover:bg-green-100 rounded transition-colors">
                    <div class="flex items-center">
                        <svg class="h-3 w-3 text-green-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-xs font-medium text-green-700">Send Newsletter</span>
                    </div>
                </button>
                
                <button class="w-full text-left p-1.5 bg-purple-50 hover:bg-purple-100 rounded transition-colors">
                    <div class="flex items-center">
                        <svg class="h-3 w-3 text-purple-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span class="text-xs font-medium text-purple-700">Export Data</span>
                    </div>
                </button>
            </div>
        </div>
        </div>
    </div>
</div>
