<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Route Clients</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            Clients eligible for pickup by {{ $route->responsible }} in {{ $route->month }}
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('routes.index') }}" 
                           class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Back to Routes
                        </a>
                        <a href="{{ route('routes.current') }}" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Current Route
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Route Information -->
        <div class="bg-white shadow-sm rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Route Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-blue-800">Month</h3>
                        <p class="text-lg font-semibold text-blue-900">{{ $route->month }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-green-800">Responsible</h3>
                        <p class="text-lg font-semibold text-green-900">{{ $route->responsible }}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-purple-800">States</h3>
                        <p class="text-sm font-semibold text-purple-900">{{ $route->formatted_states }}</p>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-yellow-800">Period</h3>
                        <p class="text-sm font-semibold text-yellow-900">
                            {{ $route->route_start_date->format('M d, Y') }} - {{ $route->route_end_date->format('M d, Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Eligible Clients -->
        <div class="bg-white shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Eligible Clients</h2>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                        {{ $eligibleClients->count() }} clients
                    </span>
                </div>

                @if($eligibleClients->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">State</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipients</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shipments</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($eligibleClients as $client)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-blue-800">
                                                        {{ substr($client->full_name, 0, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $client->full_name }}</div>
                                                <div class="text-sm text-gray-500">{{ $client->status ?? 'Active' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $client->us_state }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $client->us_phone }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $client->email ?? 'No email' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="max-w-xs truncate" title="{{ $client->us_address }}">
                                            {{ $client->us_address }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                            {{ $client->recipients->count() }} recipients
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">
                                            {{ $client->shipments->count() }} shipments
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-5.523-4.477-10-10-10S-3 12.477-3 18v2m20 0v-2a3 3 0 00-5.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-5.523 4.477-10 10-10s10 4.477 10 10v2m-10 0a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No eligible clients</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            No clients found in the states covered by this route: {{ $route->formatted_states }}
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('clients.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Clients
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Summary -->
        @if($eligibleClients->count() > 0)
        <div class="bg-white shadow-sm rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Pickup Summary</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-blue-800">Total Clients</h3>
                        <p class="text-2xl font-bold text-blue-900">{{ $eligibleClients->count() }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-green-800">Total Recipients</h3>
                        <p class="text-2xl font-bold text-green-900">{{ $eligibleClients->sum(function($client) { return $client->recipients->count(); }) }}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-purple-800">Total Shipments</h3>
                        <p class="text-2xl font-bold text-purple-900">{{ $eligibleClients->sum(function($client) { return $client->shipments->count(); }) }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>




