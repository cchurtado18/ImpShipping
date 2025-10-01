<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Shipment;
use App\Models\Route;
use Livewire\Component;

class AutoInvoiceForm extends Component
{
    public $selectedClient = null;
    public $selectedShipment = null;
    public $client = null;
    public $shipment = null;
    public $clients = [];
    public $shipments = [];
    public $availableShipments = [];
    public $invoice_status = 'pending';
    
    // Datos del formulario
    public $sender_name = '';
    public $sender_phone = '';
    public $sender_address = '';
    public $recipient_name = '';
    public $recipient_phone = '';
    public $recipient_address = '';
    public $service_description = '';
    public $quantity = 1;
    public $unit_price = 0;
    public $tax = 0;
    public $tax_amount = 0;
    public $due_date = '';
    public $terms = '25 a 30 días';
    public $notes = '';

    public function mount()
    {
        $this->clients = Client::orderBy('full_name')->get();
        $this->loadAvailableShipments();
    }

    public function updatedSelectedClient()
    {
        if ($this->selectedClient) {
            $this->client = Client::find($this->selectedClient);
            $this->loadClientData();
            $this->loadClientShipments();
            
            // Debug: Log para verificar que se está ejecutando
            \Log::info('Client selected: ' . $this->selectedClient);
            if ($this->client) {
                \Log::info('Client data loaded: ' . $this->client->full_name);
            }
            
            // Forzar actualización de la vista
            $this->dispatch('$refresh');
        } else {
            $this->client = null;
            $this->availableShipments = [];
            $this->selectedShipment = null;
            $this->shipment = null;
        }
    }

    public function updatedSelectedShipment()
    {
        if ($this->selectedShipment) {
            $this->shipment = Shipment::with(['client', 'recipient', 'box', 'route'])->find($this->selectedShipment);
            $this->loadShipmentData();
        } else {
            $this->shipment = null;
        }
    }

    public function loadClientData()
    {
        if ($this->client) {
            $this->sender_name = $this->client->full_name;
            $this->sender_phone = $this->client->us_phone;
            $this->sender_address = $this->client->us_address ?? '';
            
            // Establecer fecha de vencimiento por defecto
            if (empty($this->due_date)) {
                $this->due_date = now()->addDays(30)->format('Y-m-d');
            }
            
            // Debug: Log para verificar que se están cargando los datos
            \Log::info('Client data loaded - Name: ' . $this->sender_name . ', Phone: ' . $this->sender_phone);
        }
    }

    public function loadClientShipments()
    {
        if ($this->selectedClient) {
            // Buscar envíos del cliente en la ruta actual que estén pendientes
            $currentRoute = Route::where('status', '!=', 'closed')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($currentRoute) {
                $this->availableShipments = Shipment::where('client_id', $this->selectedClient)
                    ->where('route_id', $currentRoute->id)
                    ->whereIn('shipment_status', ['por_recepcionar', 'recepcionado', 'dejado_almacen'])
                    ->where('invoiced', false) // Solo envíos no facturados
                    ->with(['client', 'recipient', 'box'])
                    ->get();
            } else {
                // Si no hay ruta actual, buscar envíos del cliente en cualquier ruta
                $this->availableShipments = Shipment::where('client_id', $this->selectedClient)
                    ->whereIn('shipment_status', ['por_recepcionar', 'recepcionado', 'dejado_almacen'])
                    ->where('invoiced', false) // Solo envíos no facturados
                    ->with(['client', 'recipient', 'box'])
                    ->get();
            }
            
            // Si hay envíos disponibles, seleccionar automáticamente el primero
            if ($this->availableShipments->count() > 0) {
                $this->selectedShipment = $this->availableShipments->first()->id;
                $this->shipment = Shipment::with(['client', 'recipient', 'box', 'route'])->find($this->selectedShipment);
                $this->loadShipmentData();
                \Log::info('Auto-selected shipment: ' . $this->selectedShipment);
                \Log::info('Shipment data: ' . $this->shipment->code);
            }
        } else {
            $this->availableShipments = [];
        }
    }

    public function loadShipmentData()
    {
        if ($this->shipment) {
            // Cargar datos del receptor
            if ($this->shipment->recipient) {
                $this->recipient_name = $this->shipment->recipient->full_name;
                $this->recipient_phone = $this->shipment->recipient->ni_phone;
                $this->recipient_address = $this->shipment->recipient->ni_department . ', ' . 
                                         $this->shipment->recipient->ni_city . ', ' . 
                                         $this->shipment->recipient->ni_address;
            }

            // Cargar descripción del servicio con dimensiones de la caja
            if ($this->shipment->box) {
                $box = $this->shipment->box;
                $volume = ($box->length_in * $box->width_in * $box->height_in) / 1728; // Convertir a ft³
                $this->service_description = "Envío " . $box->code . " - Dimensiones: " . 
                                            $box->length_in . "\" × " . 
                                            $box->width_in . "\" × " . 
                                            $box->height_in . "\" (" . 
                                            number_format($volume, 2) . " ft³) - " . 
                                            ($this->shipment->recipient ? $this->shipment->recipient->ni_city : 'Destino');
            }

            // Cargar precio del envío (costo de la caja)
            $this->unit_price = $this->shipment->sale_price_usd ?? 0;
            
            // Si no hay precio en el envío, usar el precio de la caja
            if ($this->unit_price == 0 && $this->shipment->box) {
                $this->unit_price = $this->shipment->box->base_price_usd ?? 0;
            }
            
            // Agregar costo de transporte si existe
            if ($this->shipment->transport_cost > 0) {
                $this->unit_price += $this->shipment->transport_cost;
            }
            
            // Debug: Log para verificar el precio
            \Log::info('Unit price loaded: ' . $this->unit_price);
            \Log::info('Declared value: ' . ($this->shipment->declared_value ?? 0));
            \Log::info('Transport cost: ' . ($this->shipment->transport_cost ?? 0));
            $this->quantity = 1;

            // Cargar notas del envío
            $this->notes = $this->shipment->notes ?? '';

            // Establecer fecha de vencimiento (30 días)
            $this->due_date = now()->addDays(30)->format('Y-m-d');
            
            // Debug: Log para verificar que se está ejecutando
            \Log::info('Shipment data loaded: ' . $this->shipment->code);
            \Log::info('Recipient: ' . $this->recipient_name . ', Price: ' . $this->unit_price);
            \Log::info('Service Description: ' . $this->service_description);
            
            // Forzar actualización de la vista
            $this->dispatch('$refresh');
        }
    }

    public function loadAvailableShipments()
    {
        // Cargar todos los envíos disponibles para selección manual
        $this->shipments = Shipment::with(['client', 'recipient', 'box'])
            ->whereIn('shipment_status', ['por_recepcionar', 'recepcionado', 'dejado_almacen'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function clearForm()
    {
        $this->selectedClient = null;
        $this->selectedShipment = null;
        $this->client = null;
        $this->shipment = null;
        $this->availableShipments = [];
        
        $this->sender_name = '';
        $this->sender_phone = '';
        $this->sender_address = '';
        $this->recipient_name = '';
        $this->recipient_phone = '';
        $this->recipient_address = '';
        $this->service_description = '';
        $this->quantity = 1;
        $this->unit_price = 0;
        $this->tax_amount = 0;
        $this->due_date = '';
        $this->terms = '30 Days';
        $this->notes = '';
    }

    public function getSubtotalProperty()
    {
        return $this->quantity * $this->unit_price;
    }

    public function getTotalProperty()
    {
        return $this->subtotal + $this->tax_amount;
    }

    public function render()
    {
        return view('livewire.auto-invoice-form');
    }
}
