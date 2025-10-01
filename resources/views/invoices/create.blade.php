<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Create Invoice</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            Create a new shipping invoice
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('invoices.index') }}" 
                           class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Back to Invoices
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auto Invoice Form -->
        <div class="bg-white shadow-sm rounded-lg">
            <div class="p-6">
                @livewire('auto-invoice-form')
            </div>
        </div>
    </div>
</x-app-layout>




