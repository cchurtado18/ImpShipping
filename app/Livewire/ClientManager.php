<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;

class ClientManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $stateFilter = '';
    public $showForm = false;
    public $showViewModal = false;
    public $editingClient = null;
    public $viewingClient = null;
    
    // Form fields
    public $full_name = '';
    public $us_address = '';
    public $us_state = '';
    public $us_phone = '';
    public $email = '';
    public $notes = '';
    public $status = 'en_seguimiento';
    public $client_type = 'normal';
    public $maritime_pound_cost = '';
    public $air_pound_cost = '';
    public $cubic_foot_cost = '';

    protected $rules = [
        'full_name' => 'required|string|max:255',
        'us_address' => 'required|string',
        'us_state' => 'required|string|max:2',
        'us_phone' => 'required|string|max:20',
        'email' => 'nullable|email|max:255',
        'notes' => 'nullable|string',
        'status' => 'required|in:en_seguimiento,confirmado,proxima_ruta,ruta_cancelada',
        'client_type' => 'required|in:normal,subagency',
        'maritime_pound_cost' => 'required_if:client_type,subagency|nullable|numeric|min:0',
        'air_pound_cost' => 'required_if:client_type,subagency|nullable|numeric|min:0',
        'cubic_foot_cost' => 'required_if:client_type,subagency|nullable|numeric|min:0',
    ];

    public function render()
    {
        $clients = Client::with(['recipients', 'shipments'])
            ->when($this->search, function ($query) {
                $query->where('full_name', 'like', '%' . $this->search . '%')
                      ->orWhere('us_phone', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->stateFilter, function ($query) {
                $query->where('us_state', $this->stateFilter);
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        $statusCounts = Client::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('livewire.client-manager', [
            'clients' => $clients,
            'statusCounts' => $statusCounts
        ]);
    }

    public function addClient()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function editClient(Client $client)
    {
        // Close view modal if open
        $this->closeViewModal();
        
        $this->editingClient = $client;
        $this->full_name = $client->full_name;
        $this->us_address = $client->us_address;
        $this->us_state = $client->us_state;
        $this->us_phone = $client->us_phone;
        $this->email = $client->email;
        $this->notes = $client->notes;
        $this->status = $client->status;
        $this->client_type = $client->client_type ?? 'normal';
        $this->maritime_pound_cost = $client->maritime_pound_cost ?? '';
        $this->air_pound_cost = $client->air_pound_cost ?? '';
        $this->cubic_foot_cost = $client->cubic_foot_cost ?? '';
        $this->showForm = true;
    }

    public function saveClient()
    {
        $this->validate();

        $data = [
            'full_name' => $this->full_name,
            'us_address' => $this->us_address,
            'us_state' => $this->us_state,
            'us_phone' => $this->us_phone,
            'email' => $this->email,
            'notes' => $this->notes,
            'status' => $this->status,
            'client_type' => $this->client_type,
        ];

        // Only add subagency costs if client type is subagency
        if ($this->client_type === 'subagency') {
            $data['maritime_pound_cost'] = $this->maritime_pound_cost;
            $data['air_pound_cost'] = $this->air_pound_cost;
            $data['cubic_foot_cost'] = $this->cubic_foot_cost;
        }

        if ($this->editingClient) {
            $this->editingClient->update($data);
            $this->dispatch('clientUpdated');
        } else {
            Client::create($data);
            $this->dispatch('clientAdded');
        }

        $this->closeForm();
    }

    public function updateStatus(Client $client, $status)
    {
        $client->update(['status' => $status]);
        $this->dispatch('clientUpdated');
    }

    public function deleteClient(Client $client)
    {
        if ($client->shipments()->count() > 0) {
            $this->dispatch('error', message: 'No se puede eliminar un cliente que tiene envíos registrados.');
            return;
        }

        $client->recipients()->delete();
        $client->delete();
        $this->dispatch('clientDeleted');
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->editingClient = null;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->full_name = '';
        $this->us_address = '';
        $this->us_state = '';
        $this->us_phone = '';
        $this->email = '';
        $this->notes = '';
        $this->status = 'en_seguimiento';
        $this->client_type = 'normal';
        $this->maritime_pound_cost = '';
        $this->air_pound_cost = '';
        $this->cubic_foot_cost = '';
        $this->stateFilter = '';
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedStateFilter()
    {
        $this->resetPage();
    }

    public function updatedClientType()
    {
        // Reset subagency costs when switching to normal client
        if ($this->client_type === 'normal') {
            $this->maritime_pound_cost = '';
            $this->air_pound_cost = '';
            $this->cubic_foot_cost = '';
        }
    }

    public function viewClient(Client $client)
    {
        $this->viewingClient = $client->load(['recipients', 'shipments', 'payments']);
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewingClient = null;
    }
}
