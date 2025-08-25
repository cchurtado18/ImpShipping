<div>
    <!-- Formulario para agregar gasto -->
    <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Agregar Gasto</h3>
        <form wire:submit="addExpense" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                <select wire:model="category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Seleccionar...</option>
                    <option value="fuel">Combustible</option>
                    <option value="freight">Flete</option>
                    <option value="warehouse">Almacén</option>
                    <option value="taxes">Impuestos</option>
                    <option value="toll">Peaje</option>
                    <option value="per_diem">Viáticos</option>
                    <option value="last_mile">Última Milla</option>
                    <option value="other">Otros</option>
                </select>
                @error('category') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Monto (USD)</label>
                <input wire:model="amount" type="number" step="0.01" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <input wire:model="description" type="text" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                <input wire:model="vendor" type="text" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('vendor') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div class="md:col-span-4">
                <button type="submit" 
                        class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    Agregar Gasto
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla de gastos -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Gastos de la Ruta</h3>
            <p class="text-sm text-gray-600">Total: ${{ number_format($total, 2) }}</p>
        </div>
        
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proveedor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($expenses as $expense)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $expense->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ ucfirst(str_replace('_', ' ', $expense->category)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $expense->description ?: 'Sin descripción' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $expense->vendor ?: 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        ${{ number_format($expense->amount_usd, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button wire:click="deleteExpense({{ $expense->id }})" 
                                class="text-red-600 hover:text-red-900"
                                onclick="return confirm('¿Está seguro de eliminar este gasto?')">
                            Eliminar
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                        No hay gastos registrados para esta ruta.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
