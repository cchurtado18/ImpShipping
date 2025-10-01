<div>
    <!-- Header -->
    <div class="bg-white shadow-sm rounded-lg mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Route Management</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Manage routes and assign responsible persons
                    </p>
                </div>
                <button wire:click="showCreateForm" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create New Route
                </button>
            </div>
        </div>
    </div>

    <!-- Routes Table -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsible</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">States</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($routes as $route)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $route->month }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $route->responsible_name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="text-xs text-gray-600">{{ $route->formatted_states }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div class="text-xs">
                            <div><strong>Start:</strong> {{ $route->route_start_date ? $route->route_start_date->format('M d, Y') : '-' }}</div>
                            <div><strong>End:</strong> {{ $route->route_end_date ? $route->route_end_date->format('M d, Y') : '-' }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col space-y-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($route->status === 'collecting') bg-yellow-100 text-yellow-800
                                @elseif($route->status === 'closed') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($route->status) }}
                            </span>
                            @if($route->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('routes.current') }}" 
                           class="text-blue-600 hover:text-blue-900">View</a>
                        <button wire:click="viewRouteClients({{ $route->id }})" 
                                class="text-purple-600 hover:text-purple-900">Clients</button>
                        @if(!$route->is_active && $route->status !== 'closed')
                            <button wire:click="activateRoute({{ $route->id }})" 
                                    class="text-green-600 hover:text-green-900">Activate</button>
                        @elseif($route->is_active)
                            <button wire:click="deactivateRoute({{ $route->id }})" 
                                    class="text-orange-600 hover:text-orange-900">Deactivate</button>
                        @endif
                        @if($route->status === 'collecting')
                            <button wire:click="closeRoute({{ $route->id }})" 
                                    class="text-red-600 hover:text-red-900"
                                    onclick="return confirm('Are you sure you want to close this route?')">
                                Close
                            </button>
                        @endif
                        <button wire:click="deleteRoute({{ $route->id }})" 
                                class="text-red-600 hover:text-red-900"
                                onclick="return confirm('Are you sure you want to delete this route? This action cannot be undone.')">
                            Delete
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                        No routes found. Create your first route!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $routes->links() }}
    </div>

    <!-- Create Route Modal -->
    @if($showCreateModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="create-route-modal">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Create New Route</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="createRoute">
                    <!-- Month -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Month *</label>
                        <input wire:model="month" type="month" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('month') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Responsible -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsible Person *</label>
                        <select wire:model="responsible" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select responsible person...</option>
                            @foreach($responsibleOptions as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('responsible') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Route Start Date -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Route Start Date *</label>
                        <input wire:model="route_start_date" type="date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('route_start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Route End Date -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Route End Date *</label>
                        <input wire:model="route_end_date" type="date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('route_end_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- States -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">States *</label>
                        <div class="grid grid-cols-3 gap-2 max-h-40 overflow-y-auto border border-gray-300 rounded-md p-2">
                            @foreach($usStates as $code => $name)
                                <label class="flex items-center space-x-2 text-sm">
                                    <input type="checkbox" 
                                           wire:model="selectedStates" 
                                           value="{{ $code }}"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span>{{ $name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('selectedStates') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>


                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Create Route
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>