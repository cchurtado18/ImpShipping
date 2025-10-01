<div>
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Simple Test</h3>
    
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Select Client</label>
        <select wire:model.live="selectedClient" class="w-full px-3 py-2 border border-gray-300 rounded-md">
            <option value="">Choose a client...</option>
            @foreach($clients as $client)
                <option value="{{ $client->id }}">{{ $client->full_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Sender Name</label>
        <input type="text" wire:model.defer="sender_name" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="{{ $sender_name }}">
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Sender Phone</label>
        <input type="text" wire:model.defer="sender_phone" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="{{ $sender_phone }}">
    </div>

    <div class="p-4 bg-gray-50 rounded">
        <h4 class="font-semibold">Debug:</h4>
        <p>Selected: {{ $selectedClient }}</p>
        <p>Name: {{ $sender_name }}</p>
        <p>Phone: {{ $sender_phone }}</p>
    </div>
</div>
