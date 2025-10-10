<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Shipment;
use App\Models\Route;
use Livewire\Component;

class AutoInvoiceForm extends Component
{
    public $selectedClient = null;
    public $selectedShipments = []; // Cambio: array para múltiples paquetes
    public $client = null;
    public $clients = [];
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

    public function toggleShipmentSelection($shipmentId)
    {
        if (in_array($shipmentId, $this->selectedShipments)) {
            // Remover de la selección
            $this->selectedShipments = array_filter($this->selectedShipments, function($id) use ($shipmentId) {
                return $id != $shipmentId;
            });
            $this->selectedShipments = array_values($this->selectedShipments); // Reindexar array
        } else {
            // Agregar a la selección
            $this->selectedShipments[] = $shipmentId;
        }
        
        // Recargar datos según la selección actual
        if (count($this->selectedShipments) > 0) {
            $this->loadMultipleShipmentsData();
        } else {
            // Limpiar datos si no hay selección
            $this->clearShipmentData();
        }
        
        \Log::info('Selected shipments: ' . implode(',', $this->selectedShipments));
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
                // Obtener IDs de shipments ya facturados
                $invoicedShipmentIds = \DB::table('invoice_shipments')
                    ->pluck('shipment_id')
                    ->toArray();
                
                $this->availableShipments = Shipment::where('client_id', $this->selectedClient)
                    ->where('route_id', $currentRoute->id)
                    ->whereIn('shipment_status', ['por_recepcionar', 'recepcionado', 'dejado_almacen'])
                    ->where('invoiced', false) // Solo envíos no facturados
                    ->whereNotIn('id', $invoicedShipmentIds) // Excluir shipments ya en facturas
                    ->with(['client', 'recipient', 'box'])
                    ->get();
            } else {
                // Si no hay ruta actual, buscar envíos del cliente en cualquier ruta
                $invoicedShipmentIds = \DB::table('invoice_shipments')
                    ->pluck('shipment_id')
                    ->toArray();
                
                $this->availableShipments = Shipment::where('client_id', $this->selectedClient)
                    ->whereIn('shipment_status', ['por_recepcionar', 'recepcionado', 'dejado_almacen'])
                    ->where('invoiced', false) // Solo envíos no facturados
                    ->whereNotIn('id', $invoicedShipmentIds) // Excluir shipments ya en facturas
                    ->with(['client', 'recipient', 'box'])
                    ->get();
            }
            
            // NO auto-seleccionar envíos - permitir selección manual
            $this->selectedShipments = [];
            \Log::info('Available shipments loaded: ' . $this->availableShipments->count() . ' packages');
        } else {
            $this->availableShipments = [];
            $this->selectedShipments = [];
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

    public function loadMultipleShipmentsData()
    {
        if (count($this->selectedShipments) > 0) {
            $shipments = Shipment::whereIn('id', $this->selectedShipments)
                ->with(['client', 'recipient', 'box', 'route'])
                ->get();

            if ($shipments->count() > 0) {
                // Usar el primer envío para datos del receptor (todos son del mismo cliente)
                $firstShipment = $shipments->first();
                
                // Cargar datos del receptor
                if ($firstShipment->recipient) {
                    $this->recipient_name = $firstShipment->recipient->full_name;
                    $this->recipient_phone = $firstShipment->recipient->ni_phone;
                    $this->recipient_address = $firstShipment->recipient->ni_department . ', ' . 
                                             $firstShipment->recipient->ni_city . ', ' . 
                                             $firstShipment->recipient->ni_address;
                }

                // Crear descripción del servicio con múltiples paquetes
                $serviceDescriptions = [];
                $totalPrice = 0;
                
                foreach ($shipments as $shipment) {
                    if ($shipment->box) {
                        $box = $shipment->box;
                        $volume = ($box->length_in * $box->width_in * $box->height_in) / 1728;
                        $serviceDescriptions[] = "Envío " . $box->code . " - Dimensiones: " . 
                                                $box->length_in . "\" × " . 
                                                $box->width_in . "\" × " . 
                                                $box->height_in . "\" (" . 
                                                number_format($volume, 2) . " ft³)";
                    }
                    
                    // Sumar precios
                    $shipmentPrice = $shipment->sale_price_usd ?? 0;
                    if ($shipmentPrice == 0 && $shipment->box) {
                        $shipmentPrice = $shipment->box->base_price_usd ?? 0;
                    }
                    if ($shipment->transport_cost > 0) {
                        $shipmentPrice += $shipment->transport_cost;
                    }
                    $totalPrice += $shipmentPrice;
                }
                
                $this->service_description = implode('; ', $serviceDescriptions);
                $this->unit_price = $totalPrice; // Precio total de todos los paquetes
                $this->quantity = 1; // Cantidad = 1 (una factura con múltiples paquetes)

                // Cargar notas combinadas
                $notes = $shipments->pluck('notes')->filter()->unique()->implode('; ');
                $this->notes = $notes;

                // Establecer fecha de vencimiento (30 días)
                $this->due_date = now()->addDays(30)->format('Y-m-d');
                
                \Log::info('Multiple shipments loaded: ' . count($shipments) . ' packages');
                \Log::info('Total price: ' . $totalPrice);
                \Log::info('Service Description: ' . $this->service_description);
                
                // Forzar actualización de la vista
                $this->dispatch('$refresh');
            }
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

    public function clearShipmentData()
    {
        $this->recipient_name = '';
        $this->recipient_phone = '';
        $this->recipient_address = '';
        $this->service_description = '';
        $this->quantity = 1;
        $this->unit_price = 0;
        $this->tax_amount = 0;
        $this->notes = '';
    }

    public function clearForm()
    {
        $this->selectedClient = null;
        $this->selectedShipment = null;
        $this->selectedShipments = [];
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
