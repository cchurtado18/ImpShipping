<div>
    <!-- Búsqueda de clientes -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Cliente Existente</label>
        <div class="relative">
            <input wire:model.live="search" type="text" 
                   placeholder="Buscar por nombre o teléfono..." 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            
            @if($search && count($clients) > 0)
            <div class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                @foreach($clients as $client)
                <div wire:click="selectClient({{ $client->id }})" 
                     class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0">
                    <div class="font-medium text-gray-900">{{ $client->full_name }}</div>
                    <div class="text-sm text-gray-500">{{ $client->us_phone }}</div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <!-- Cliente seleccionado -->
    @if($selectedClient)
    <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-lg font-medium text-green-900">Cliente Seleccionado</h3>
                <div class="mt-2 space-y-1">
                    <p class="text-sm text-green-800"><strong>Nombre:</strong> {{ $selectedClient->full_name }}</p>
                    <p class="text-sm text-green-800"><strong>Teléfono:</strong> {{ $selectedClient->us_phone }}</p>
                    <p class="text-sm text-green-800"><strong>Dirección:</strong> {{ $selectedClient->us_address }}</p>
                    @if($selectedClient->email)
                    <p class="text-sm text-green-800"><strong>Email:</strong> {{ $selectedClient->email }}</p>
                    @endif
                </div>
            </div>
            <button wire:click="clearSelection" 
                    class="text-green-600 hover:text-green-800 text-sm">
                Cambiar Cliente
            </button>
        </div>

        <!-- Receptores del cliente -->
        @if(count($recipients) > 0)
        <div class="mt-4">
            <h4 class="text-sm font-medium text-green-900 mb-2">Receptores Disponibles</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                @foreach($recipients as $recipient)
                <div wire:click="selectRecipient({{ $recipient->id }})" 
                     class="p-3 border border-green-200 rounded-md hover:bg-green-100 cursor-pointer
                            @if($selectedRecipient && $selectedRecipient->id === $recipient->id) bg-green-100 @endif">
                    <div class="font-medium text-sm text-green-900">{{ $recipient->full_name }}</div>
                    <div class="text-xs text-green-700">{{ $recipient->ni_phone }}</div>
                    <div class="text-xs text-green-700">{{ $recipient->ni_department }}, {{ $recipient->ni_city }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
            <p class="text-sm text-yellow-800">Este cliente no tiene receptores registrados. Deberás crear uno nuevo.</p>
        </div>
        @endif
    </div>
    @endif

    <!-- Receptor seleccionado -->
    @if($selectedRecipient)
    <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-lg font-medium text-blue-900">Receptor Seleccionado</h3>
                <div class="mt-2 space-y-1">
                    <p class="text-sm text-blue-800"><strong>Nombre:</strong> {{ $selectedRecipient->full_name }}</p>
                    <p class="text-sm text-blue-800"><strong>Teléfono:</strong> {{ $selectedRecipient->ni_phone }}</p>
                    <p class="text-sm text-blue-800"><strong>Departamento:</strong> {{ $selectedRecipient->ni_department }}</p>
                    <p class="text-sm text-blue-800"><strong>Ciudad:</strong> {{ $selectedRecipient->ni_city }}</p>
                    <p class="text-sm text-blue-800"><strong>Dirección:</strong> {{ $selectedRecipient->ni_address }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
