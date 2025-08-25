<?php

namespace App\Livewire;

use App\Models\Shipment;
use Livewire\Component;

class ShipmentStatusModal extends Component
{
    public $showModal = false;
    public $shipmentId;
    public $shipment;
    public $newStatus = '';

    protected $listeners = ['openStatusModal'];

    public function openStatusModal($shipmentId)
    {
        $this->shipmentId = $shipmentId;
        $this->shipment = Shipment::with(['client', 'recipient'])->find($shipmentId);
        
        if ($this->shipment) {
            $this->newStatus = $this->shipment->shipment_status;
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['shipmentId', 'shipment', 'newStatus']);
    }

    public function updateStatus()
    {
        $this->validate([
            'newStatus' => 'required|in:por_recepcionar,recepcionado,dejado_almacen,en_nicaragua,entregado,cancelled',
        ]);

        if (!$this->shipment) {
            return;
        }

        $this->shipment->update(['shipment_status' => $this->newStatus]);
        
        $this->closeModal();
        $this->dispatch('shipmentUpdated');
        session()->flash('message', 'Estado del envío actualizado exitosamente.');
    }

    public function getStatusOptions()
    {
        return [
            'por_recepcionar' => '📦 Por Recepcionar',
            'recepcionado' => '✅ Recepcionado',
            'dejado_almacen' => '🏪 Dejado en Almacén',
            'en_nicaragua' => '🇳🇮 En Nicaragua',
            'entregado' => '🎉 Entregado',
            'cancelled' => '❌ Cancelado',
        ];
    }

    public function render()
    {
        return view('livewire.shipment-status-modal');
    }
}
