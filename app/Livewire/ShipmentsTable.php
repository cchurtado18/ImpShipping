<?php

namespace App\Livewire;

use App\Models\Route;
use App\Models\Shipment;
use Livewire\Component;
use Livewire\WithPagination;

class ShipmentsTable extends Component
{
    use WithPagination;

    public Route $route;
    public $search = '';
    public $stateFilter = '';
    public $showForm = false;
    public $editingShipment = null;
    public $showDetailsModal = false;
    public $selectedShipment = null;

    protected $listeners = ['shipmentAdded' => '$refresh', 'shipmentSaved' => '$refresh'];

    public function mount(Route $route)
    {
        $this->route = $route;
    }

    public function render()
    {
        $shipments = $this->route->shipments()
            ->with(['client', 'recipient', 'box'])
            ->when($this->search, function ($query) {
                $query->whereHas('client', function ($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%')
                      ->orWhere('us_phone', 'like', '%' . $this->search . '%');
                })->orWhereHas('recipient', function ($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%')
                      ->orWhere('ni_phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->stateFilter, function ($query) {
                $query->whereHas('client', function ($q) {
                    $q->where('us_state', $this->stateFilter);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Obtener estados únicos para el filtro
        $states = \App\Models\Client::whereNotNull('us_state')
            ->where('us_state', '!=', '')
            ->distinct()
            ->pluck('us_state')
            ->sort()
            ->values();

        return view('livewire.shipments-table', [
            'shipments' => $shipments,
            'states' => $states
        ]);
    }

    public function addShipment()
    {
        $this->dispatch('openShipmentModal');
    }

    public function editShipment(Shipment $shipment)
    {
        $this->dispatch('openShipmentModal', shipmentId: $shipment->id);
    }

    public function changeStatus(Shipment $shipment)
    {
        $this->dispatch('openStatusModal', shipmentId: $shipment->id);
    }

    public function quickPayment(Shipment $shipment)
    {
        $this->dispatch('openPaymentModal', shipmentId: $shipment->id);
    }

    public function showQR(Shipment $shipment)
    {
        $this->dispatch('showQR', code: $shipment->code);
    }

    public function viewDetails(Shipment $shipment)
    {
        $this->selectedShipment = $shipment->load(['client', 'recipient', 'box', 'route', 'payments']);
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedShipment = null;
    }

    public function deleteShipment(Shipment $shipment)
    {
        try {
            // Eliminar pagos relacionados primero
            $shipment->payments()->delete();
            
            // Eliminar el envío
            $shipment->delete();
            
            session()->flash('success', 'Shipment deleted successfully.');
            $this->dispatch('shipmentDeleted');
            
            // Refrescar la tabla
            $this->render();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting shipment: ' . $e->getMessage());
        }
    }
}
