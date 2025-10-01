<div>
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Test Auto Invoice</h3>
    
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Select Client</label>
        <select wire:model.live="selectedClient" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Choose a client...</option>
            @foreach($clients as $client)
                <option value="{{ $client->id }}">{{ $client->full_name }} - {{ $client->us_phone }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Sender Name</label>
            <input type="text" wire:model.live="sender_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Sender Phone</label>
            <input type="text" wire:model.live="sender_phone" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    </div>
    
    <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Sender Address</label>
        <textarea wire:model.live="sender_address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
    </div>

    <div class="mt-4 p-4 bg-gray-50 rounded">
        <h4 class="font-semibold">Debug Info:</h4>
        <p>Selected Client: {{ $selectedClient }}</p>
        <p>Sender Name: {{ $sender_name }}</p>
        <p>Sender Phone: {{ $sender_phone }}</p>
    </div>
</div>
