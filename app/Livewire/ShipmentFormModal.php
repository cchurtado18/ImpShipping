<?php

namespace App\Livewire;

use App\Models\Box;
use App\Models\Client;
use App\Models\Recipient;
use App\Models\Route;
use App\Models\Shipment;
use Livewire\Component;

class ShipmentFormModal extends Component
{
    public $showModal = false;
    public $editingShipment = null;
    public $route;
    public $showPreview = false;
    
    // Selección de cliente
    public $selectedClient = null;
    public $clients = [];
    
    // Selección de receptor
    public $selectedRecipient = null;
    public $recipients = [];
    
    // Selección de caja
    public $selectedBox = null;
    public $boxes = [];
    
    // Precio y estado
    public $finalPrice = 0;
    public $shipmentStatus = 'por_recepcionar';
    public $notes = '';
    
    protected $listeners = ['clientSelected', 'openShipmentModal'];
    
    public function mount($route = null)
    {
        $this->route = $route;
        $this->loadData();
    }
    
    public function loadData()
    {
        $this->clients = Client::all();
        $this->boxes = Box::where('active', true)->get();
    }
    
    public function showShipmentForm($shipmentId = null)
    {
        if ($shipmentId) {
            $this->editingShipment = Shipment::with(['client', 'recipient', 'box'])->find($shipmentId);
            $this->loadShipmentData();
        } else {
            $this->resetForm();
        }
        $this->showModal = true;
    }
    
    public function loadShipmentData()
    {
        $shipment = $this->editingShipment;
        $this->selectedClient = $shipment->client_id;
        $this->selectedRecipient = $shipment->recipient_id;
        $this->selectedBox = $shipment->box_id;
        $this->finalPrice = $shipment->sale_price_usd;
        $this->shipmentStatus = $shipment->shipment_status;
        $this->notes = $shipment->notes;
        $this->loadRecipients();
    }
    
    public function updatedSelectedClient()
    {
        $this->loadRecipients();
        $this->selectedRecipient = null;
    }
    
    public function loadRecipients()
    {
        if ($this->selectedClient) {
            $this->recipients = Recipient::where('client_id', $this->selectedClient)->get();
        } else {
            $this->recipients = [];
        }
    }
    
    public function updatedSelectedBox()
    {
        if ($this->selectedBox) {
            $box = Box::find($this->selectedBox);
            if ($box) {
                $this->finalPrice = $box->base_price_usd;
            }
        }
    }
    
    public function showPreview()
    {
        $this->validate([
            'selectedClient' => 'required',
            'selectedRecipient' => 'required',
            'selectedBox' => 'required',
            'finalPrice' => 'required|numeric|min:0',
            'shipmentStatus' => 'required',
        ]);
        
        $this->showPreview = true;
    }
    
    public function hidePreview()
    {
        $this->showPreview = false;
    }
    
    public function saveShipment()
    {
        $this->validate([
            'selectedClient' => 'required',
            'selectedRecipient' => 'required',
            'selectedBox' => 'required',
            'finalPrice' => 'required|numeric|min:0',
            'shipmentStatus' => 'required',
        ]);
        
        // Crear o actualizar envío
        $shipmentData = [
            'client_id' => $this->selectedClient,
            'recipient_id' => $this->selectedRecipient,
            'route_id' => $this->route->id,
            'box_id' => $this->selectedBox,
            'sale_price_usd' => $this->finalPrice,
            'notes' => $this->notes,
            'shipment_status' => $this->shipmentStatus,
        ];
        
        if ($this->editingShipment) {
            $this->editingShipment->update($shipmentData);
            $shipment = $this->editingShipment;
        } else {
            $shipment = Shipment::create($shipmentData);
        }
        
        $this->showModal = false;
        $this->showPreview = false;
        $this->resetForm();
        
        $this->dispatch('shipmentSaved', $shipment->id);
        session()->flash('message', $this->editingShipment ? 'Envío actualizado correctamente.' : 'Envío creado correctamente.');
    }
    
    public function closeModal()
    {
        $this->showModal = false;
        $this->showPreview = false;
        $this->resetForm();
    }
    
    public function resetForm()
    {
        $this->editingShipment = null;
        $this->selectedClient = null;
        $this->selectedRecipient = null;
        $this->selectedBox = null;
        $this->finalPrice = 0;
        $this->shipmentStatus = 'por_recepcionar';
        $this->notes = '';
        $this->recipients = [];
    }
    
    public function getSelectedClientProperty()
    {
        return $this->selectedClient ? Client::find($this->selectedClient) : null;
    }
    
    public function getSelectedRecipientProperty()
    {
        return $this->selectedRecipient ? Recipient::find($this->selectedRecipient) : null;
    }
    
    public function getSelectedBoxProperty()
    {
        return $this->selectedBox ? Box::find($this->selectedBox) : null;
    }

    public function openShipmentModal($shipmentId = null)
    {
        $this->showShipmentForm($shipmentId);
    }
    
    public function render()
    {
        return view('livewire.shipment-form-modal');
    }
}
