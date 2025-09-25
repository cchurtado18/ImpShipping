<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Route: {{ $route->month }}</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            Status: 
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
                                onclick="openShipmentModal()"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Add Client to Route
                        </button>
                        <button type="button" 
                                onclick="showAddExpenseModal()"
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Add Expense
                        </button>
                        @if(auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('routes.close', $route) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    onclick="return confirm('Are you sure you want to close this route?')">
                                Close Route
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
                    <button class="border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" 
                            onclick="showTab('shipments')">
                        Shipments
                    </button>
                    <button class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" 
                            onclick="showTab('expenses')">
                        Expenses
                    </button>
                    <button class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" 
                            onclick="showTab('summary')">
                        Summary
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <!-- Tab Content -->
                <div id="shipments-tab" class="tab-content">
                    @livewire('shipments-table', ['route' => $route])
                </div>

                <div id="expenses-tab" class="tab-content hidden">
                    @livewire('route-expenses-table', ['route' => $route])
                </div>

                <div id="summary-tab" class="tab-content hidden">
                    @livewire('route-summary', ['route' => $route])
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar envío -->
    <div id="shipmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Add New Shipment</h3>
                    <button onclick="closeShipmentModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Contenido del modal -->
                <form id="shipmentForm" class="space-y-8">
                    <!-- Paso 1: Selección de Cliente -->
                    <div>
                        <h4 class="text-md font-semibold text-gray-700 mb-4 flex items-center">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full mr-2">1</span>
                            Seleccionar Cliente
                        </h4>
                        
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Cliente</label>
                            <input 
                                type="text" 
                                id="clientSearch"
                                placeholder="Buscar por nombre, teléfono o email..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                            
                            <div id="clientResults" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto hidden">
                                <!-- Los resultados se cargarán aquí -->
                            </div>
                        </div>

                        <div id="selectedClientInfo" class="mt-4 p-4 bg-green-50 border border-green-200 rounded-md hidden">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h5 id="selectedClientName" class="font-medium text-green-800"></h5>
                                    <p id="selectedClientContact" class="text-sm text-green-600"></p>
                                    <p id="selectedClientAddress" class="text-sm text-green-600"></p>
                                    <p id="selectedClientState" class="text-sm text-green-600"></p>
                                </div>
                                <button 
                                    type="button" 
                                    onclick="clearClientSelection()"
                                    class="text-green-600 hover:text-green-800"
                                >
                                    Cambiar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 2: Dimensiones y Precio -->
                    <div>
                        <h4 class="text-md font-semibold text-gray-700 mb-4 flex items-center">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full mr-2">2</span>
                            Dimensiones y Precio
                        </h4>
                        
                        <!-- Gestión de múltiples cajas -->
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                            <h5 class="text-sm font-medium text-yellow-800 mb-3">Cajas a enviar</h5>

                            <!-- Controles de cajas -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="text-sm font-medium text-gray-700">Agregar cajas con diferentes dimensiones</label>
                                    <button 
                                        type="button"
                                        onclick="addBox()"
                                        class="px-3 py-1 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        + Agregar Caja
                                    </button>
                                </div>
                                
                                <div id="boxesContainer">
                                    <!-- Las cajas se agregarán aquí dinámicamente -->
                                </div>
                                
                                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-blue-800">Total de cajas:</span>
                                        <span id="totalBoxes" class="text-lg font-bold text-blue-600">0</span>
                                    </div>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-sm font-medium text-blue-800">Precio total:</span>
                                        <span id="totalPrice" class="text-lg font-bold text-blue-600">$0.00 USD</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Template para nueva caja (oculto) -->
                            <template id="boxTemplate">
                                <div class="box-item mb-4 p-4 bg-gray-50 border border-gray-200 rounded-md" data-box-id="">
                                    <div class="flex items-center justify-between mb-3">
                                        <h6 class="text-sm font-medium text-gray-700">Caja #<span class="box-number"></span></h6>
                                        <button 
                                            type="button"
                                            onclick="removeBox(this)"
                                            class="text-red-600 hover:text-red-800 text-sm"
                                        >
                                            Eliminar
                                        </button>
                                    </div>
                                    
                                    <div class="grid grid-cols-3 gap-4 mb-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Largo (in)</label>
                                            <input 
                                                type="number" 
                                                class="box-length w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                min="1" max="200"
                                                placeholder="0"
                                            >
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Ancho (in)</label>
                                            <input 
                                                type="number" 
                                                class="box-width w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                min="1" max="200"
                                                placeholder="0"
                                            >
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Alto (in)</label>
                                            <input 
                                                type="number" 
                                                class="box-height w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                min="1" max="200"
                                                placeholder="0"
                                            >
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio por caja</label>
                                            <div class="box-price px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm">
                                                $0.00 USD
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Volumen</label>
                                            <div class="box-volume px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm">
                                                0.00 ft³
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Sección por peso para esta caja -->
                                    <div class="box-weight-section mt-3 p-3 bg-gray-50 border border-gray-200 rounded-md hidden">
                                        <h6 class="text-sm font-medium text-gray-700 mb-2">Calcular por peso</h6>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Peso (lb)</label>
                                                <input type="number" class="box-weight-lbs w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="0.1" step="0.1" placeholder="0.0">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Tarifa por libra (USD)</label>
                                                <input type="number" class="box-weight-rate w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="0.1" step="0.1" placeholder="Ej. 4.00">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>



                        <!-- Costo de transporte -->
                        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                            <div class="flex items-center space-x-3 mb-3">
                                <input type="checkbox" id="transportCost" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="transportCost" class="text-sm font-medium text-gray-700">Incluir costo de transporte</label>
                            </div>
                            <div id="transportCostInput" class="hidden">
                                <label for="transportAmount" class="block text-sm font-medium text-gray-700 mb-1">Costo de transporte (USD)</label>
                                <input type="number" id="transportAmount" min="20" step="0.01" value="20" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Mínimo: $20 USD</p>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 3: Datos del Receptor -->
                    <div>
                        <h4 class="text-md font-semibold text-gray-700 mb-4 flex items-center">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full mr-2">3</span>
                            Datos del Receptor en Nicaragua
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo *</label>
                                <input 
                                    type="text" 
                                    id="recipientName"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Nombre completo del receptor"
                                >
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Edad</label>
                                <input 
                                    type="number" 
                                    id="recipientAge"
                                    min="0" max="120"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Edad del receptor"
                                >
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono *</label>
                                <input 
                                    type="text" 
                                    id="recipientPhone"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Número de teléfono"
                                >
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Departamento *</label>
                                <select 
                                    id="recipientDepartment"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">Seleccionar departamento...</option>
                                    <option value="Managua">Managua</option>
                                    <option value="León">León</option>
                                    <option value="Chinandega">Chinandega</option>
                                    <option value="Masaya">Masaya</option>
                                    <option value="Granada">Granada</option>
                                    <option value="Carazo">Carazo</option>
                                    <option value="Rivas">Rivas</option>
                                    <option value="Boaco">Boaco</option>
                                    <option value="Chontales">Chontales</option>
                                    <option value="Jinotega">Jinotega</option>
                                    <option value="Matagalpa">Matagalpa</option>
                                    <option value="Estelí">Estelí</option>
                                    <option value="Madriz">Madriz</option>
                                    <option value="Nueva Segovia">Nueva Segovia</option>
                                    <option value="Río San Juan">Río San Juan</option>
                                    <option value="RAAN">RAAN</option>
                                    <option value="RAAS">RAAS</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ciudad *</label>
                                <input 
                                    type="text" 
                                    id="recipientCity"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Ciudad o municipio"
                                >
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dirección Exacta *</label>
                            <textarea 
                                id="recipientAddress"
                                required
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Dirección completa del receptor"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Paso 4: Precio Final y Notas -->
                    <div>
                        <h4 class="text-md font-semibold text-gray-700 mb-4 flex items-center">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full mr-2">4</span>
                            Precio Final y Notas
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Precio Final (USD) *</label>
                                <input 
                                    type="number" 
                                    id="finalPrice"
                                    required
                                    step="0.01"
                                    min="0"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="0.00"
                                >
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Notas Adicionales</label>
                                <textarea 
                                    id="notes"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Información adicional sobre el envío..."
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button 
                            type="button" 
                            onclick="closeShipmentModal()"
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            Crear Envío
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Variables globales
        let selectedClient = null;
        let searchTimeout = null;
        let boxCounter = 0;
        let boxes = [];

        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active class from all tab buttons
            document.querySelectorAll('nav button').forEach(button => {
                button.classList.remove('border-blue-500', 'text-blue-600');
                button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            });
            
            // Show selected tab content
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            
            // Add active class to selected tab button
            event.target.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            event.target.classList.add('border-blue-500', 'text-blue-600');
        }

        function showAddExpenseModal() {
            // Cambiar al tab de gastos
            showTab('expenses');
        }

        function openShipmentModal() {
            document.getElementById('shipmentModal').classList.remove('hidden');
            initializeForm();
        }

        function closeShipmentModal() {
            document.getElementById('shipmentModal').classList.add('hidden');
            resetForm();
        }

        function initializeForm() {
            // Configurar búsqueda de clientes
            const clientSearch = document.getElementById('clientSearch');
            clientSearch.addEventListener('input', handleClientSearch);

            // Configurar envío del formulario
            const form = document.getElementById('shipmentForm');
            form.addEventListener('submit', handleFormSubmit);

            // Agregar primera caja automáticamente
            addBox();
            
            // Configurar event listener para transporte
            const transportCost = document.getElementById('transportCost');
            const transportAmount = document.getElementById('transportAmount');
            
            transportCost.addEventListener('change', handleTransportCostChange);
            transportAmount.addEventListener('input', calculateTotalPrice);
        }

        function handleClientSearch(event) {
            const searchTerm = event.target.value;
            
            // Limpiar timeout anterior
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            // Si el término de búsqueda es muy corto, ocultar resultados
            if (searchTerm.length < 2) {
                hideClientResults();
                return;
            }

            // Esperar 300ms antes de buscar para evitar muchas peticiones
            searchTimeout = setTimeout(() => {
                searchClients(searchTerm);
            }, 300);
        }

        function searchClients(searchTerm) {
            // Realizar búsqueda de clientes en el backend
            fetch(`{{ route('clients.search') }}?search=${encodeURIComponent(searchTerm)}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(clients => {
                displayClientResults(clients);
            })
            .catch(error => {
                console.error('Error searching clients:', error);
                displayClientResults([]);
            });
        }

        function displayClientResults(clients) {
            const resultsDiv = document.getElementById('clientResults');
            
            if (clients.length === 0) {
                resultsDiv.innerHTML = '<div class="px-4 py-2 text-gray-500">No se encontraron clientes</div>';
            } else {
                resultsDiv.innerHTML = clients.map(client => `
                    <div 
                        class="client-result-item px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0"
                        style="cursor: pointer;"
                        data-client-id="${client.id}"
                        data-client-name="${client.full_name}"
                        data-client-phone="${client.us_phone || ''}"
                        data-client-email="${client.email || ''}"
                        data-client-address="${client.us_address || ''}"
                        data-client-state="${client.us_state || ''}"
                    >
                        <div class="font-medium">${client.full_name}</div>
                        <div class="text-sm text-gray-600">${client.us_phone || ''} • ${client.email || ''}</div>
                        <div class="text-xs text-gray-500">${client.us_state || ''}</div>
                    </div>
                `).join('');
                
                // Agregar event listeners a los elementos de resultado
                resultsDiv.querySelectorAll('.client-result-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const clientId = this.getAttribute('data-client-id');
                        const clientName = this.getAttribute('data-client-name');
                        const clientPhone = this.getAttribute('data-client-phone');
                        const clientEmail = this.getAttribute('data-client-email');
                        const clientAddress = this.getAttribute('data-client-address');
                        const clientState = this.getAttribute('data-client-state');
                        
                        selectClient(
                            parseInt(clientId), 
                            clientName, 
                            clientPhone, 
                            clientEmail, 
                            clientAddress,
                            clientState
                        );
                    });
                });
            }
            
            resultsDiv.classList.remove('hidden');
        }

        function hideClientResults() {
            document.getElementById('clientResults').classList.add('hidden');
        }

        function selectClient(id, name, phone, email, address, state) {
            selectedClient = { id, name, phone, email, address, state };
            
            // Mostrar información del cliente seleccionado
            document.getElementById('selectedClientName').textContent = name;
            document.getElementById('selectedClientContact').textContent = `${phone || ''} • ${email || ''}`;
            document.getElementById('selectedClientAddress').textContent = address || '';
            document.getElementById('selectedClientState').textContent = state || '';
            document.getElementById('selectedClientInfo').classList.remove('hidden');
            
            // Ocultar resultados de búsqueda
            hideClientResults();
            
            // Limpiar campo de búsqueda
            document.getElementById('clientSearch').value = name;
        }

        function clearClientSelection() {
            selectedClient = null;
            document.getElementById('selectedClientInfo').classList.add('hidden');
            document.getElementById('clientSearch').value = '';
        }

        // Funciones para manejar múltiples cajas
        function addBox() {
            boxCounter++;
            const template = document.getElementById('boxTemplate');
            const container = document.getElementById('boxesContainer');
            
            // Clonar el template
            const boxElement = template.content.cloneNode(true);
            const boxDiv = boxElement.querySelector('.box-item');
            boxDiv.setAttribute('data-box-id', boxCounter);
            boxDiv.querySelector('.box-number').textContent = boxCounter;
            
            // Agregar al contenedor
            container.appendChild(boxElement);
            
            // Configurar event listeners para esta caja
            setupBoxListeners(boxDiv);
            
            // Actualizar contadores
            updateBoxCounters();
            calculateTotalPrice();
        }

        function removeBox(button) {
            const boxDiv = button.closest('.box-item');
            const boxId = parseInt(boxDiv.getAttribute('data-box-id'));
            
            // Remover de la lista de cajas
            boxes = boxes.filter(box => box.id !== boxId);
            
            // Remover del DOM
            boxDiv.remove();
            
            // Actualizar contadores
            updateBoxCounters();
            calculateTotalPrice();
        }

        function setupBoxListeners(boxDiv) {
            const lengthInput = boxDiv.querySelector('.box-length');
            const widthInput = boxDiv.querySelector('.box-width');
            const heightInput = boxDiv.querySelector('.box-height');
            const weightLbsInput = boxDiv.querySelector('.box-weight-lbs');
            const weightRateInput = boxDiv.querySelector('.box-weight-rate');
            
            // Event listeners para dimensiones
            [lengthInput, widthInput, heightInput].forEach(input => {
                input.addEventListener('input', () => calculateBoxPrice(boxDiv));
            });
            
            // Event listeners para peso
            [weightLbsInput, weightRateInput].forEach(input => {
                input.addEventListener('input', () => calculateBoxPrice(boxDiv));
            });
        }

        function calculateBoxPrice(boxDiv) {
            const length = parseFloat(boxDiv.querySelector('.box-length').value) || 0;
            const width = parseFloat(boxDiv.querySelector('.box-width').value) || 0;
            const height = parseFloat(boxDiv.querySelector('.box-height').value) || 0;
            const weightSection = boxDiv.querySelector('.box-weight-section');
            
            if (length > 0 && width > 0 && height > 0) {
                // Calcular pies cúbicos
                const cubicInches = length * width * height;
                const cubicFeet = cubicInches / 1728;
                boxDiv.querySelector('.box-volume').textContent = `${cubicFeet.toFixed(2)} ft³`;
                
                let price = 0;
                
                if (cubicFeet >= 0.1 && cubicFeet <= 2.99) {
                    // Modo por peso
                    weightSection.classList.remove('hidden');
                    const weight = parseFloat(boxDiv.querySelector('.box-weight-lbs').value) || 0;
                    const rate = parseFloat(boxDiv.querySelector('.box-weight-rate').value) || 0;
                    price = weight * rate;
                    boxDiv.querySelector('.box-price').textContent = price > 0 ? `$${price.toFixed(2)} USD` : 'Por peso';
                } else {
                    // Modo volumétrico
                    weightSection.classList.add('hidden');
                    
                    // Aplicar tu fórmula de precios
                    if (cubicFeet >= 2.90 && cubicFeet <= 3.89) {
                        price = cubicFeet * 49;
                    } else if (cubicFeet >= 3.90 && cubicFeet <= 4.89) {
                        price = cubicFeet * 45;
                    } else if (cubicFeet >= 4.90 && cubicFeet <= 5.89) {
                        price = cubicFeet * 42.5;
                    } else if (cubicFeet >= 5.90 && cubicFeet <= 6.89) {
                        price = cubicFeet * 39;
                    } else if (cubicFeet >= 6.90 && cubicFeet <= 7.89) {
                        price = cubicFeet * 35;
                    } else if (cubicFeet >= 7.90 && cubicFeet <= 8.89) {
                        price = cubicFeet * 32;
                    } else if (cubicFeet >= 8.90 && cubicFeet <= 9.89) {
                        price = cubicFeet * 31;
                    } else if (cubicFeet >= 9.90 && cubicFeet <= 10.89) {
                        price = cubicFeet * 29.5;
                    } else if (cubicFeet >= 10.90 && cubicFeet <= 11.89) {
                        price = cubicFeet * 29;
                    } else if (cubicFeet >= 11.90 && cubicFeet <= 12.89) {
                        price = cubicFeet * 28;
                    } else if (cubicFeet >= 12.90 && cubicFeet <= 13.89) {
                        price = cubicFeet * 26.5;
                    } else if (cubicFeet >= 13.90 && cubicFeet <= 14.89) {
                        price = cubicFeet * 25.5;
                    } else if (cubicFeet >= 14.90 && cubicFeet <= 16.99) {
                        price = cubicFeet * 24.5;
                    } else if (cubicFeet >= 17 && cubicFeet <= 19.99) {
                        price = cubicFeet * 24;
                    } else if (cubicFeet >= 20) {
                        price = cubicFeet * 22.75;
                    }
                    
                    price = Math.round(price);
                    boxDiv.querySelector('.box-price').textContent = `$${price} USD`;
                }
                
                // Guardar datos de la caja
                const boxId = parseInt(boxDiv.getAttribute('data-box-id'));
                const boxData = {
                    id: boxId,
                    length,
                    width,
                    height,
                    cubicFeet,
                    price,
                    weight: cubicFeet <= 2.99 ? {
                        lbs: parseFloat(boxDiv.querySelector('.box-weight-lbs').value) || 0,
                        rate: parseFloat(boxDiv.querySelector('.box-weight-rate').value) || 0
                    } : null
                };
                
                // Actualizar en el array
                const existingIndex = boxes.findIndex(box => box.id === boxId);
                if (existingIndex >= 0) {
                    boxes[existingIndex] = boxData;
                } else {
                    boxes.push(boxData);
                }
            }
            
            calculateTotalPrice();
        }

        function updateBoxCounters() {
            const totalBoxes = document.querySelectorAll('.box-item').length;
            document.getElementById('totalBoxes').textContent = totalBoxes;
        }

        function calculateTotalPrice() {
            // Sumar precios de todas las cajas
            const totalBoxPrice = boxes.reduce((sum, box) => sum + (box.price || 0), 0);
            
            // Costo de transporte por caja
            const transportCost = document.getElementById('transportCost').checked ?
                (parseFloat(document.getElementById('transportAmount').value) || 0) : 0;
            
            const totalTransport = transportCost * boxes.length;
            const totalPrice = totalBoxPrice + totalTransport;
            
            // Actualizar display
            document.getElementById('totalPrice').textContent = `$${totalPrice.toFixed(2)} USD`;
            document.getElementById('finalPrice').value = totalPrice > 0 ? totalPrice : '';
        }

        function handleTransportCostChange() {
            const transportCost = document.getElementById('transportCost');
            const transportCostInput = document.getElementById('transportCostInput');
            
            if (transportCost.checked) {
                transportCostInput.classList.remove('hidden');
                calculateTotalPrice();
            } else {
                transportCostInput.classList.add('hidden');
                calculateTotalPrice();
            }
        }

        function handleFormSubmit(event) {
            event.preventDefault();

            // Validar que se haya seleccionado un cliente
            if (!selectedClient) {
                alert('Por favor selecciona un cliente');
                return;
            }

            // Validar que haya al menos una caja
            if (boxes.length === 0) {
                alert('Debe agregar al menos una caja');
                return;
            }

            // Validar que todas las cajas tengan dimensiones completas
            for (const box of boxes) {
                if (!box.length || !box.width || !box.height) {
                    alert(`La caja #${box.id} debe tener dimensiones completas (largo, ancho, alto)`);
                    return;
                }
                
                // Si es caja pequeña, validar peso y tarifa
                if (box.cubicFeet <= 2.99 && (!box.weight || !box.weight.lbs || !box.weight.rate)) {
                    alert(`La caja #${box.id} es pequeña y requiere peso y tarifa por libra`);
                    return;
                }
            }

            // Validar campos requeridos del receptor
            const requiredFields = ['recipientName', 'recipientPhone', 'recipientDepartment', 'recipientCity', 'recipientAddress', 'finalPrice'];
            for (const fieldId of requiredFields) {
                const field = document.getElementById(fieldId);
                if (!field.value.trim()) {
                    alert(`Por favor completa el campo: ${field.placeholder || field.id}`);
                    field.focus();
                    return;
                }
            }

            // Recopilar datos del formulario
            const formData = {
                client: selectedClient,
                boxes: boxes,
                recipient: {
                    name: document.getElementById('recipientName').value,
                    age: document.getElementById('recipientAge').value,
                    phone: document.getElementById('recipientPhone').value,
                    department: document.getElementById('recipientDepartment').value,
                    city: document.getElementById('recipientCity').value,
                    address: document.getElementById('recipientAddress').value
                },
                price_total_usd: document.getElementById('finalPrice').value,
                transport: {
                    enabled: document.getElementById('transportCost').checked,
                    amount_per_box: document.getElementById('transportCost').checked ? (parseFloat(document.getElementById('transportAmount').value) || 0) : 0,
                },
                notes: document.getElementById('notes').value
            };

            // Enviar datos al backend
            fetch('{{ route("shipments.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Envío creado exitosamente');
                    closeShipmentModal();
                    // Recargar la tabla de envíos si existe
                    if (typeof window.Livewire !== 'undefined') {
                        window.Livewire.dispatch('shipmentSaved');
                    }
                } else {
                    alert('Error al crear el envío: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al crear el envío. Por favor, intente nuevamente.');
            });
        }

        function resetForm() {
            document.getElementById('shipmentForm').reset();
            selectedClient = null;
            document.getElementById('selectedClientInfo').classList.add('hidden');
            hideClientResults();
            document.getElementById('finalPrice').value = '';
            
            // Limpiar cajas
            document.getElementById('boxesContainer').innerHTML = '';
            boxes = [];
            boxCounter = 0;
            updateBoxCounters();
            calculateTotalPrice();
        }

        // Cerrar resultados de búsqueda al hacer click fuera
        document.addEventListener('click', function(event) {
            const resultsDiv = document.getElementById('clientResults');
            const searchInput = document.getElementById('clientSearch');
            
            if (!resultsDiv.contains(event.target) && !searchInput.contains(event.target)) {
                hideClientResults();
            }
        });
    </script>
</x-app-layout> 