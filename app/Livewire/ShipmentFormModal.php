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
    
    // Búsqueda de cliente
    public $clientSearch = '';
    public $clients = [];
    public $selectedClient = null;
    
    // Selección de caja
    public $selectedBox = null;
    public $boxLength = '';
    public $boxWidth = '';
    public $boxHeight = '';
    public $suggestedPrice = 0;
    
    // Datos del receptor
    public $recipientName = '';
    public $recipientAge = '';
    public $recipientPhone = '';
    public $recipientDepartment = '';
    public $recipientCity = '';
    public $recipientAddress = '';
    public $notes = '';
    
    // Precio final
    public $finalPrice = 0;
    
    protected $listeners = ['clientSelected'];
    
    public function mount($route = null)
    {
        $this->route = $route;
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
        $this->selectedClient = $shipment->client;
        $this->selectedBox = $shipment->box;
        $this->boxLength = $shipment->box->length_in;
        $this->boxWidth = $shipment->box->width_in;
        $this->boxHeight = $shipment->box->height_in;
        $this->suggestedPrice = $shipment->box->current_price;
        $this->finalPrice = $shipment->sale_price_usd;
        
        $this->recipientName = $shipment->recipient->full_name;
        $this->recipientAge = $shipment->recipient->age ?? '';
        $this->recipientPhone = $shipment->recipient->ni_phone;
        $this->recipientDepartment = $shipment->recipient->ni_department;
        $this->recipientCity = $shipment->recipient->ni_city;
        $this->recipientAddress = $shipment->recipient->ni_address;
        $this->notes = $shipment->notes;
    }
    
    public function updatedClientSearch()
    {
        if (strlen($this->clientSearch) >= 2) {
            $this->clients = Client::where('full_name', 'like', '%' . $this->clientSearch . '%')
                ->orWhere('us_phone', 'like', '%' . $this->clientSearch . '%')
                ->orWhere('email', 'like', '%' . $this->clientSearch . '%')
                ->limit(10)
                ->get();
        } else {
            $this->clients = [];
        }
    }
    
    public function selectClient($clientId)
    {
        $this->selectedClient = Client::find($clientId);
        $this->clientSearch = $this->selectedClient->full_name;
        $this->clients = [];
        $this->dispatch('clientSelected', $this->selectedClient);
    }
    
    public function updatedSelectedBox()
    {
        if ($this->selectedBox) {
            $box = Box::find($this->selectedBox);
            $this->boxLength = $box->length_in;
            $this->boxWidth = $box->width_in;
            $this->boxHeight = $box->height_in;
            $this->suggestedPrice = $box->current_price;
            $this->calculateFinalPrice();
        }
    }
    
    public function calculateFinalPrice()
    {
        // Lógica simple: precio base + 10% de margen
        $this->finalPrice = round($this->suggestedPrice * 1.10, 2);
    }
    
    public function saveShipment()
    {
        $this->validate([
            'selectedClient' => 'required',
            'selectedBox' => 'required',
            'finalPrice' => 'required|numeric|min:0',
            'recipientName' => 'required|string|max:255',
            'recipientAge' => 'nullable|numeric|min:0|max:120',
            'recipientPhone' => 'required|string|max:20',
            'recipientDepartment' => 'required|string|max:100',
            'recipientCity' => 'required|string|max:100',
            'recipientAddress' => 'required|string|max:500',
        ]);
        
        // Crear o actualizar receptor
        $recipient = Recipient::updateOrCreate(
            [
                'client_id' => $this->selectedClient->id,
                'full_name' => $this->recipientName,
            ],
            [
                'age' => $this->recipientAge,
                'ni_phone' => $this->recipientPhone,
                'ni_department' => $this->recipientDepartment,
                'ni_city' => $this->recipientCity,
                'ni_address' => $this->recipientAddress,
            ]
        );
        
        // Crear o actualizar envío
        $shipmentData = [
            'client_id' => $this->selectedClient->id,
            'recipient_id' => $recipient->id,
            'route_id' => $this->route->id,
            'box_id' => $this->selectedBox,
            'sale_price_usd' => $this->finalPrice,
            'notes' => $this->notes,
            'shipment_status' => 'en_almacen',
        ];
        
        if ($this->editingShipment) {
            $this->editingShipment->update($shipmentData);
            $shipment = $this->editingShipment;
        } else {
            $shipment = Shipment::create($shipmentData);
        }
        
        $this->showModal = false;
        $this->resetForm();
        
        $this->dispatch('shipmentSaved', $shipment->id);
        session()->flash('message', $this->editingShipment ? 'Envío actualizado correctamente.' : 'Envío creado correctamente.');
    }
    
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }
    
    public function resetForm()
    {
        $this->editingShipment = null;
        $this->clientSearch = '';
        $this->clients = [];
        $this->selectedClient = null;
        $this->selectedBox = null;
        $this->boxLength = '';
        $this->boxWidth = '';
        $this->boxHeight = '';
        $this->suggestedPrice = 0;
        $this->finalPrice = 0;
        $this->recipientName = '';
        $this->recipientAge = '';
        $this->recipientPhone = '';
        $this->recipientDepartment = '';
        $this->recipientCity = '';
        $this->recipientAddress = '';
        $this->notes = '';
    }
    
    public function render()
    {
        $boxes = Box::where('active', true)->get();
        
        return view('livewire.shipment-form-modal', [
            'boxes' => $boxes,
        ]);
    }
}
