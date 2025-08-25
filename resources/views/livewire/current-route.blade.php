<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Ruta: {{ $route->month }}</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            Estado: 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($route->status === 'collecting') bg-blue-100 text-blue-800
                                @elseif($route->status === 'in_transit') bg-yellow-100 text-yellow-800
                                @elseif($route->status === 'arrived') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $route->status)) }}
                            </span>
                        </p>
                    </div>
                    @if($route->status !== 'closed')
                    <div class="flex space-x-3">
                        <button type="button" 
                                wire:click="showShipmentForm"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Agregar Cliente a la Ruta
                        </button>
                        <button type="button" 
                                wire:click="showTab('expenses')"
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Agregar Gasto
                        </button>
                        @if(auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('routes.close', $route) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    onclick="return confirm('¿Está seguro de cerrar esta ruta?')">
                                Cerrar Ruta
                            </button>
                        </form>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button class="@if($activeTab === 'shipments') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" 
                            wire:click="showTab('shipments')">
                        Envíos
                    </button>
                    <button class="@if($activeTab === 'expenses') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" 
                            wire:click="showTab('expenses')">
                        Gastos
                    </button>
                    <button class="@if($activeTab === 'summary') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" 
                            wire:click="showTab('summary')">
                        Resumen
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <!-- Tab Content -->
                @if($activeTab === 'shipments')
                <div>
                    @livewire('shipments-table', ['route' => $route])
                </div>
                @endif

                @if($activeTab === 'expenses')
                <div>
                    @livewire('route-expenses-table', ['route' => $route])
                </div>
                @endif

                @if($activeTab === 'summary')
                <div>
                    @livewire('route-summary', ['route' => $route])
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal para agregar envío -->
    @if($showShipmentModal)
    @livewire('shipment-form-modal', ['route' => $route])
    @endif
</x-app-layout>

