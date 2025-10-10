<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;

class SyncInvoiceStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:invoice-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize the invoiced status of shipments with the invoice_shipments table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Synchronizing invoice status...');
        
        // Obtener todos los shipment IDs que están en la tabla invoice_shipments
        $invoicedShipmentIds = DB::table('invoice_shipments')
            ->pluck('shipment_id')
            ->toArray();
        
        // Actualizar todos los shipments que están en facturas para marcarlos como facturados
        $updatedCount = Shipment::whereIn('id', $invoicedShipmentIds)
            ->update(['invoiced' => true]);
        
        $this->info("Updated {$updatedCount} shipments to invoiced = true");
        
        // También actualizar shipments que NO están en facturas para marcarlos como no facturados
        $notInvoicedCount = Shipment::whereNotIn('id', $invoicedShipmentIds)
            ->where('invoiced', true)
            ->update(['invoiced' => false]);
        
        $this->info("Updated {$notInvoicedCount} shipments to invoiced = false");
        
        $this->info('Invoice status synchronization completed!');
        
        return 0;
    }
}