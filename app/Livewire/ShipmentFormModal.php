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
    
    // Dimensiones personalizadas
    public $customDimensions = false;
    public $boxLength = 0;
    public $boxWidth = 0;
    public $boxHeight = 0;
    public $boxWeight = 0;
    public $boxWeightRate = 0;
    public $calculatedPrice = 0;
    
    // Modo de precio
    public $useCalculatedPrice = true;
    public $manualPrice = 0;
    
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
        
        // Cargar dimensiones personalizadas si existen
        if ($shipment->hasCustomDimensions()) {
            $this->customDimensions = true;
            $this->boxLength = $shipment->custom_length ?? 0;
            $this->boxWidth = $shipment->custom_width ?? 0;
            $this->boxHeight = $shipment->custom_height ?? 0;
            $this->boxWeight = $shipment->custom_weight ?? 0;
            $this->boxWeightRate = $shipment->custom_weight_rate ?? 0;
            $this->calculatedPrice = $shipment->calculated_price ?? 0;
            $this->useCalculatedPrice = $shipment->use_calculated_price ?? true;
            $this->manualPrice = $shipment->manual_price ?? 0;
        }
        
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
                $this->calculatedPrice = $box->base_price_usd;
                // Cargar dimensiones de la caja seleccionada
                $this->boxLength = $box->length_in;
                $this->boxWidth = $box->width_in;
                $this->boxHeight = $box->height_in;
            }
        }
    }
    
    public function updatedCustomDimensions()
    {
        if (!$this->customDimensions) {
            // Resetear dimensiones personalizadas
            $this->boxLength = 0;
            $this->boxWidth = 0;
            $this->boxHeight = 0;
            $this->boxWeight = 0;
            $this->boxWeightRate = 0;
            $this->calculatedPrice = 0;
            $this->finalPrice = 0;
        }
    }
    
    public function calculatePrice()
    {
        if ($this->boxLength > 0 && $this->boxWidth > 0 && $this->boxHeight > 0) {
            $cubicInches = $this->boxLength * $this->boxWidth * $this->boxHeight;
            $cubicFeet = $cubicInches / 1728;
            
            $price = 0;
            
            if ($cubicFeet >= 0.1 && $cubicFeet <= 2.99) {
                // Modo por peso
                if ($this->boxWeight > 0 && $this->boxWeightRate > 0) {
                    $price = $this->boxWeight * $this->boxWeightRate;
                }
            } else {
                // Modo volumétrico - aplicar la misma fórmula que en current.blade.php
                if ($cubicFeet >= 2.90 && $cubicFeet <= 3.89) {
                    $price = $cubicFeet * 55.52;
                } else if ($cubicFeet >= 3.90 && $cubicFeet <= 4.89) {
                    $price = $cubicFeet * 51.52;
                } else if ($cubicFeet >= 4.90 && $cubicFeet <= 5.89) {
                    $price = $cubicFeet * 49.02;
                } else if ($cubicFeet >= 5.90 && $cubicFeet <= 6.89) {
                    $price = $cubicFeet * 45.52;
                } else if ($cubicFeet >= 6.90 && $cubicFeet <= 7.89) {
                    $price = $cubicFeet * 41.52;
                } else if ($cubicFeet >= 7.90 && $cubicFeet <= 8.89) {
                    $price = $cubicFeet * 35.75;
                } else if ($cubicFeet >= 8.90 && $cubicFeet <= 9.89) {
                    $price = $cubicFeet * 34.75;
                } else if ($cubicFeet >= 9.90 && $cubicFeet <= 10.89) {
                    $price = $cubicFeet * 33.25;
                } else if ($cubicFeet >= 10.90 && $cubicFeet <= 11.89) {
                    $price = $cubicFeet * 32.75;
                } else if ($cubicFeet >= 11.90 && $cubicFeet <= 12.89) {
                    $price = $cubicFeet * 31.75;
                } else if ($cubicFeet >= 12.90 && $cubicFeet <= 13.89) {
                    $price = $cubicFeet * 30.25;
                } else if ($cubicFeet >= 13.90 && $cubicFeet <= 14.89) {
                    $price = $cubicFeet * 29.25;
                } else if ($cubicFeet >= 14.90 && $cubicFeet <= 16.99) {
                    $price = $cubicFeet * 28.25;
                } else if ($cubicFeet >= 17 && $cubicFeet <= 19.99) {
                    $price = $cubicFeet * 27.75;
                } else if ($cubicFeet >= 20) {
                    $price = $cubicFeet * 25.75;
                }
                
                $price = round($price);
            }
            
            $this->calculatedPrice = $price;
            
            // Solo actualizar finalPrice si estamos usando precio calculado
            if ($this->useCalculatedPrice) {
                $this->finalPrice = $price;
            }
        }
    }
    
    public function showPreview()
    {
        $rules = [
            'selectedClient' => 'required',
            'selectedRecipient' => 'required',
            'shipmentStatus' => 'required',
        ];
        
        // Validar precio según el modo seleccionado
        if ($this->customDimensions && $this->calculatedPrice > 0 && !$this->useCalculatedPrice) {
            $rules['manualPrice'] = 'required|numeric|min:0';
        } else {
            $rules['finalPrice'] = 'required|numeric|min:0';
        }
        
        if (!$this->customDimensions) {
            $rules['selectedBox'] = 'required';
        } else {
            $rules['boxLength'] = 'required|numeric|min:0.1';
            $rules['boxWidth'] = 'required|numeric|min:0.1';
            $rules['boxHeight'] = 'required|numeric|min:0.1';
        }
        
        $this->validate($rules);
        
        $this->showPreview = true;
    }
    
    public function hidePreview()
    {
        $this->showPreview = false;
    }
    
    public function saveShipment()
    {
        $rules = [
            'selectedClient' => 'required',
            'selectedRecipient' => 'required',
            'shipmentStatus' => 'required',
        ];
        
        // Validar precio según el modo seleccionado
        if ($this->customDimensions && $this->calculatedPrice > 0 && !$this->useCalculatedPrice) {
            $rules['manualPrice'] = 'required|numeric|min:0';
        } else {
            $rules['finalPrice'] = 'required|numeric|min:0';
        }
        
        if (!$this->customDimensions) {
            $rules['selectedBox'] = 'required';
        } else {
            $rules['boxLength'] = 'required|numeric|min:0.1';
            $rules['boxWidth'] = 'required|numeric|min:0.1';
            $rules['boxHeight'] = 'required|numeric|min:0.1';
        }
        
        $this->validate($rules);
        
        // Determinar el precio final a guardar
        $finalPriceToSave = $this->finalPrice;
        if ($this->customDimensions && $this->calculatedPrice > 0 && !$this->useCalculatedPrice) {
            $finalPriceToSave = $this->manualPrice;
        }
        
        // Crear o actualizar envío
        $shipmentData = [
            'client_id' => $this->selectedClient,
            'recipient_id' => $this->selectedRecipient,
            'route_id' => $this->route->id,
            'box_id' => $this->customDimensions ? null : $this->selectedBox,
            'sale_price_usd' => $finalPriceToSave,
            'notes' => $this->notes,
            'shipment_status' => $this->shipmentStatus,
        ];
        
        // Si son dimensiones personalizadas, guardar en los nuevos campos
        if ($this->customDimensions) {
            $shipmentData = array_merge($shipmentData, [
                'custom_length' => $this->boxLength,
                'custom_width' => $this->boxWidth,
                'custom_height' => $this->boxHeight,
                'custom_weight' => $this->boxWeight,
                'custom_weight_rate' => $this->boxWeightRate,
                'calculated_price' => $this->calculatedPrice,
                'use_calculated_price' => $this->useCalculatedPrice,
                'manual_price' => $this->manualPrice,
                'price_mode' => $this->useCalculatedPrice ? 'calculated' : 'manual',
            ]);
        }
        
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
        // Reset dimensiones personalizadas
        $this->customDimensions = false;
        $this->boxLength = 0;
        $this->boxWidth = 0;
        $this->boxHeight = 0;
        $this->boxWeight = 0;
        $this->boxWeightRate = 0;
        $this->calculatedPrice = 0;
        $this->useCalculatedPrice = true;
        $this->manualPrice = 0;
    }
    
    public function updatedBoxLength()
    {
        $this->calculatePrice();
    }
    
    public function updatedBoxWidth()
    {
        $this->calculatePrice();
    }
    
    public function updatedBoxHeight()
    {
        $this->calculatePrice();
    }
    
    public function updatedBoxWeight()
    {
        $this->calculatePrice();
    }
    
    public function updatedBoxWeightRate()
    {
        $this->calculatePrice();
    }
    
    public function updatedUseCalculatedPrice()
    {
        if ($this->useCalculatedPrice) {
            // Cambiar a precio calculado
            $this->finalPrice = $this->calculatedPrice;
        } else {
            // Cambiar a precio manual
            $this->manualPrice = $this->finalPrice;
        }
    }
    
    public function updatedManualPrice()
    {
        if (!$this->useCalculatedPrice) {
            $this->finalPrice = $this->manualPrice;
        }
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
