<x-app-layout>
    <div class="space-y-6">
        <!-- Componente Livewire para gestión de recepción -->
        @livewire('reception-manager', ['route' => $route])
    </div>
</x-app-layout>
