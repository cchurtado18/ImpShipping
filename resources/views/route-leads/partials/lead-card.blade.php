<div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
    <div class="flex justify-between items-start mb-3">
        <div>
            <h4 class="font-semibold text-gray-900">{{ $lead->client->full_name }}</h4>
            <p class="text-sm text-gray-600">{{ $lead->client->us_phone }}</p>
            @if($lead->client->email)
                <p class="text-sm text-gray-600">{{ $lead->client->email }}</p>
            @endif
        </div>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $lead->status_badge }}">
            {{ $lead->status_label }}
        </span>
    </div>
    
    <div class="mb-3 space-y-2">
        <div class="text-sm">
            <span class="font-medium text-gray-700">Box Dimensions:</span>
            <span class="text-gray-900">{{ $lead->box_dimensions }}</span>
        </div>
        <div class="text-sm">
            <span class="font-medium text-gray-700">Nicaragua Address:</span>
            <span class="text-gray-900">{{ $lead->nicaragua_address }}</span>
        </div>
        <div class="text-sm">
            <span class="font-medium text-gray-700">Nicaragua Phone:</span>
            <span class="text-gray-900">{{ $lead->nicaragua_phone }}</span>
        </div>
        <div class="text-sm">
            <span class="font-medium text-gray-700">Box Quantity:</span>
            <span class="text-gray-900">{{ $lead->box_quantity }}</span>
        </div>
        @if($lead->notes)
            <div class="text-sm">
                <span class="font-medium text-gray-700">Notes:</span>
                <span class="text-gray-900">{{ $lead->notes }}</span>
            </div>
        @endif
        <div class="text-sm text-gray-500">
            <span class="font-medium">Created:</span>
            {{ $lead->created_at->format('M d, Y H:i') }}
        </div>
    </div>
    
    <div class="flex space-x-2">
        <select onchange="updateLeadStatus({{ $lead->id }}, this.value)" 
                class="text-xs px-2 py-1 rounded border-0 focus:ring-2 focus:ring-indigo-500 {{ $lead->status_badge }}">
            <option value="pending" {{ $lead->status === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="confirmed" {{ $lead->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="shipped" {{ $lead->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
            <option value="delivered" {{ $lead->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
        </select>
        <button onclick="deleteLead({{ $lead->id }})" 
                class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            Delete
        </button>
    </div>
</div>
