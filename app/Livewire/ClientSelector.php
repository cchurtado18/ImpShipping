<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Recipient;
use Livewire\Component;

class ClientSelector extends Component
{
    public $search = '';
    public $selectedClient = null;
    public $selectedRecipient = null;
    public $clients = [];
    public $recipients = [];

    protected $listeners = ['clientSelected' => 'selectClient'];

    public function mount()
    {
        $this->loadClients();
    }

    public function loadClients()
    {
        $this->clients = Client::when($this->search, function ($query) {
            $query->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('us_phone', 'like', '%' . $this->search . '%');
        })->limit(10)->get();
    }

    public function updatedSearch()
    {
        $this->loadClients();
    }

    public function selectClient($clientId)
    {
        $this->selectedClient = Client::with('recipients')->find($clientId);
        $this->recipients = $this->selectedClient->recipients;
        $this->search = '';
        $this->clients = [];
        
        $this->dispatch('clientSelected', client: $this->selectedClient);
    }

    public function selectRecipient($recipientId)
    {
        $this->selectedRecipient = Recipient::find($recipientId);
        $this->dispatch('recipientSelected', recipient: $this->selectedRecipient);
    }

    public function clearSelection()
    {
        $this->selectedClient = null;
        $this->selectedRecipient = null;
        $this->recipients = [];
        $this->dispatch('selectionCleared');
    }

    public function render()
    {
        return view('livewire.client-selector');
    }
}
