<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Route Management</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            Create and manage routes with responsible persons
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Route Manager Component -->
        @livewire('route-manager')
    </div>
</x-app-layout>




