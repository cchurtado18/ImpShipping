<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\Component;

class TestAutoInvoice extends Component
{
    public $selectedClient = null;
    public $clients = [];
    public $sender_name = '';
    public $sender_phone = '';
    public $sender_address = '';

    public function mount()
    {
        $this->clients = Client::orderBy('full_name')->get();
    }

    public function updatedSelectedClient()
    {
        if ($this->selectedClient) {
            $client = Client::find($this->selectedClient);
            if ($client) {
                $this->sender_name = $client->full_name;
                $this->sender_phone = $client->us_phone;
                $this->sender_address = $client->us_address ?? '';
            }
        }
    }

    public function render()
    {
        return view('livewire.test-auto-invoice');
    }
}
