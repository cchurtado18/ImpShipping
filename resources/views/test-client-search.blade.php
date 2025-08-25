<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Client Search</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-6">Test Client Search</h1>
        
        <div class="mb-6">
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

        <div class="mt-6">
            <h3 class="text-lg font-medium mb-2">Debug Info:</h3>
            <div id="debugInfo" class="bg-gray-100 p-3 rounded text-sm font-mono"></div>
        </div>
    </div>

    <script>
        let selectedClient = null;
        let searchTimeout = null;

        function log(message) {
            const debugInfo = document.getElementById('debugInfo');
            debugInfo.innerHTML += new Date().toLocaleTimeString() + ': ' + message + '<br>';
            console.log(message);
        }

        function handleClientSearch(event) {
            const searchTerm = event.target.value;
            
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            if (searchTerm.length < 2) {
                hideClientResults();
                return;
            }

            searchTimeout = setTimeout(() => {
                searchClients(searchTerm);
            }, 300);
        }

        function searchClients(searchTerm) {
            log('Searching for: ' + searchTerm);
            
            fetch(`/clients/search?search=${encodeURIComponent(searchTerm)}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(clients => {
                log('Clients received: ' + JSON.stringify(clients));
                displayClientResults(clients);
            })
            .catch(error => {
                log('Error searching clients: ' + error);
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
                    >
                        <div class="font-medium">${client.full_name}</div>
                        <div class="text-sm text-gray-600">${client.us_phone || ''} • ${client.email || ''}</div>
                    </div>
                `).join('');
                
                resultsDiv.querySelectorAll('.client-result-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const clientId = this.getAttribute('data-client-id');
                        const clientName = this.getAttribute('data-client-name');
                        const clientPhone = this.getAttribute('data-client-phone');
                        const clientEmail = this.getAttribute('data-client-email');
                        const clientAddress = this.getAttribute('data-client-address');
                        
                        log('Client clicked: ' + clientName);
                        selectClient(
                            parseInt(clientId), 
                            clientName, 
                            clientPhone, 
                            clientEmail, 
                            clientAddress
                        );
                    });
                });
            }
            
            resultsDiv.classList.remove('hidden');
        }

        function hideClientResults() {
            document.getElementById('clientResults').classList.add('hidden');
        }

        function selectClient(id, name, phone, email, address) {
            log('selectClient called with: ' + JSON.stringify({ id, name, phone, email, address }));
            
            selectedClient = { id, name, phone, email, address };
            
            document.getElementById('selectedClientName').textContent = name;
            document.getElementById('selectedClientContact').textContent = `${phone || ''} • ${email || ''}`;
            document.getElementById('selectedClientAddress').textContent = address || '';
            document.getElementById('selectedClientInfo').classList.remove('hidden');
            
            hideClientResults();
            document.getElementById('clientSearch').value = name;
            
            log('Client selected successfully: ' + JSON.stringify(selectedClient));
        }

        function clearClientSelection() {
            selectedClient = null;
            document.getElementById('selectedClientInfo').classList.add('hidden');
            document.getElementById('clientSearch').value = '';
            log('Client selection cleared');
        }

        // Initialize
        document.getElementById('clientSearch').addEventListener('input', handleClientSearch);

        document.addEventListener('click', function(event) {
            const resultsDiv = document.getElementById('clientResults');
            const searchInput = document.getElementById('clientSearch');
            
            if (!resultsDiv.contains(event.target) && !searchInput.contains(event.target)) {
                hideClientResults();
            }
        });

        log('Test page loaded');
    </script>
</body>
</html>

