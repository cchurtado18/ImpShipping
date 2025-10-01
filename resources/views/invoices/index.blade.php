<x-app-layout>
    <div class="space-y-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Invoice Management</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            Create and manage shipping invoices
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('invoices.create') }}" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create Invoice
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="bg-white shadow-sm rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sender</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $invoice->invoice_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $invoice->invoice_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="max-w-xs truncate" title="{{ $invoice->sender_name }}">
                                {{ $invoice->sender_name }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $invoice->sender_phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="max-w-xs truncate" title="{{ $invoice->recipient_name }}">
                                {{ $invoice->recipient_name }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $invoice->recipient_phone }}</div>
                        </td>
                        <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="max-w-xs truncate" title="{{ $invoice->service_description }}">
                                {{ $invoice->service_description }}
                            </div>
                            <div class="text-xs text-gray-500">Qty: {{ $invoice->quantity }}</div>
                        </td> -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="font-semibold">${{ number_format($invoice->total_amount, 2) }}</div>
                            @if($invoice->tax_amount > 0)
                                <div class="text-xs text-gray-500">Tax: ${{ number_format($invoice->tax_amount, 2) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $invoice->status_color }}">
                                {{ $invoice->formatted_status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="relative inline-block">
                                <select onchange="changeInvoiceStatus({{ $invoice->id }}, this.value)" 
                                        class="appearance-none inline-flex items-center px-3 py-1 rounded-full text-xs font-medium cursor-pointer {{ $invoice->invoice_status_color }} focus:outline-none focus:ring-2 focus:ring-blue-500 border-0 bg-transparent pr-8">
                                    <option value="pending" {{ $invoice->invoice_status === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                    <option value="cancelled_by_cash" {{ $invoice->invoice_status === 'cancelled_by_cash' ? 'selected' : '' }}>💰 Cancelled by Cash</option>
                                    <option value="cancelled_by_transfer" {{ $invoice->invoice_status === 'cancelled_by_transfer' ? 'selected' : '' }}>🏦 Cancelled by Transfer</option>
                                </select>
                                <!-- Flecha personalizada -->
                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                    <svg class="w-3 h-3 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex flex-wrap gap-2">
                                <!-- View Button -->
                                <a href="{{ route('invoices.show', $invoice) }}" 
                                   class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200 transition-colors">
                                    👁️ View
                                </a>
                                
                                <!-- Download PDF Button -->
                                <a href="{{ route('invoices.download-pdf', $invoice) }}" 
                                   class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors">
                                    📄 PDF
                                </a>
                                
                                <!-- Edit Button -->
                                @if($invoice->invoice_status === 'pending')
                                    <a href="{{ route('invoices.edit', $invoice) }}" 
                                       class="inline-flex items-center px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-md hover:bg-green-200 transition-colors">
                                        ✏️ Edit
                                    </a>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-medium text-gray-500 bg-gray-100 rounded-md">
                                        🔒 Locked
                                    </span>
                                @endif
                                
                                <!-- Delete Button -->
                                @if($invoice->invoice_status === 'pending')
                                    <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" class="inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this invoice?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200 transition-colors">
                                            🗑️ Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                            No invoices found. Create your first invoice!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4 px-4 sm:px-6 lg:px-8">
            {{ $invoices->links() }}
        </div>
    </div>

    <script>
        function changeInvoiceStatus(invoiceId, newStatus) {
            if (confirm('Are you sure you want to change the invoice status?')) {
                // Crear un formulario temporal para enviar la petición
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/invoices/${invoiceId}/change-status`;
                
                // Agregar token CSRF
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Agregar el nuevo estado
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'invoice_status';
                statusInput.value = newStatus;
                form.appendChild(statusInput);
                
                // Agregar al DOM y enviar
                document.body.appendChild(form);
                form.submit();
            } else {
                // Si cancela, recargar la página para resetear el select
                location.reload();
            }
        }
    </script>
</x-app-layout>