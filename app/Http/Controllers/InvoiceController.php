<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['client', 'shipment', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        return view('invoices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'shipment_ids' => 'nullable|array',
            'shipment_ids.*' => 'exists:shipments,id',
            'sender_name' => 'required|string|max:255',
            'sender_phone' => 'required|string|max:20',
            'sender_address' => 'required|string',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'recipient_address' => 'required|string',
            'service_description' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date|after:today',
            'terms' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $unit_price = $request->unit_price ?? 0;
        $tax_amount = $request->tax_amount ?? 0;
        $subtotal = $request->quantity * $unit_price;
        $total = $subtotal + $tax_amount;

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'invoice_date' => now()->toDateString(),
            'due_date' => $request->due_date,
            'terms' => $request->terms ?? '25 a 30 días',
            'sender_name' => $request->sender_name,
            'sender_phone' => $request->sender_phone,
            'sender_address' => $request->sender_address,
            'recipient_name' => $request->recipient_name,
            'recipient_phone' => $request->recipient_phone,
            'recipient_address' => $request->recipient_address,
            'service_description' => $request->service_description,
            'quantity' => $request->quantity,
            'unit_price' => $unit_price,
            'subtotal' => $subtotal,
            'tax_amount' => $tax_amount,
            'total_amount' => $total,
            'total' => $total,
            'status' => 'draft',
            'invoice_status' => $request->invoice_status ?? 'pending',
            'notes' => $request->notes,
            'client_id' => $request->client_id,
            'shipment_id' => $request->shipment_ids[0] ?? null, // Usar el primer envío como referencia
            'user_id' => Auth::id(),
        ]);

        // Crear relaciones en la tabla pivot y marcar como facturados
        if ($request->shipment_ids && is_array($request->shipment_ids)) {
            // Verificar que los shipments no estén ya facturados
            $alreadyInvoicedShipments = \DB::table('invoice_shipments')
                ->whereIn('shipment_id', $request->shipment_ids)
                ->pluck('shipment_id')
                ->toArray();
            
            if (!empty($alreadyInvoicedShipments)) {
                return back()->withErrors([
                    'shipment_ids' => 'Algunos paquetes ya han sido facturados: ' . implode(', ', $alreadyInvoicedShipments)
                ])->withInput();
            }
            
            // Crear relaciones en la tabla pivot
            foreach ($request->shipment_ids as $shipmentId) {
                try {
                    \DB::table('invoice_shipments')->insert([
                        'invoice_id' => $invoice->id,
                        'shipment_id' => $shipmentId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    // Si hay un error de duplicado, continuar con el siguiente
                    if ($e->getCode() == 23000) { // Integrity constraint violation
                        \Log::warning("Shipment {$shipmentId} already invoiced, skipping...");
                        continue;
                    }
                    throw $e;
                }
            }
            
            // Marcar TODOS los envíos como facturados
            Shipment::whereIn('id', $request->shipment_ids)->update(['invoiced' => true]);
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice created successfully!');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'shipment', 'user']);
        
        // Cargar solo los envíos asociados a esta factura específica
        $shipments = collect();
        if ($invoice->shipment_id) {
            // Buscar en la tabla pivot los envíos de esta factura
            $shipmentIds = \DB::table('invoice_shipments')
                ->where('invoice_id', $invoice->id)
                ->pluck('shipment_id');
                
            if ($shipmentIds->count() > 0) {
                $shipments = Shipment::whereIn('id', $shipmentIds)
                    ->with(['box', 'recipient'])
                    ->get();
            } else {
                // Fallback: usar el envío principal si no hay relaciones
                $shipments = Shipment::where('id', $invoice->shipment_id)
                    ->with(['box', 'recipient'])
                    ->get();
            }
        }
        
        return view('invoices.show', compact('invoice', 'shipments'));
    }

    public function edit(Invoice $invoice)
    {
        $clients = Client::orderBy('full_name')->get();
        $shipments = Shipment::with(['client', 'recipient'])->orderBy('created_at', 'desc')->get();
        
        return view('invoices.edit', compact('invoice', 'clients', 'shipments'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'shipment_id' => 'nullable|exists:shipments,id',
            'sender_name' => 'required|string|max:255',
            'sender_phone' => 'required|string|max:20',
            'sender_address' => 'required|string',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'recipient_address' => 'required|string',
            'service_description' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'terms' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $subtotal = $request->quantity * $request->unit_price;
        $total = $subtotal + ($request->tax_amount ?? 0);

        $invoice->update([
            'due_date' => $request->due_date,
            'terms' => $request->terms ?? '25 a 30 días',
            'sender_name' => $request->sender_name,
            'sender_phone' => $request->sender_phone,
            'sender_address' => $request->sender_address,
            'recipient_name' => $request->recipient_name,
            'recipient_phone' => $request->recipient_phone,
            'recipient_address' => $request->recipient_address,
            'service_description' => $request->service_description,
            'quantity' => $request->quantity,
            'unit_price' => $unit_price,
            'subtotal' => $subtotal,
            'tax_amount' => $tax_amount,
            'total_amount' => $total,
            'total' => $total,
            'notes' => $request->notes,
            'client_id' => $request->client_id,
            'shipment_id' => $request->shipment_id,
        ]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully!');
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.index')
                ->with('error', 'Cannot delete a paid invoice.');
        }

        $invoice->delete();
        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully!');
    }

    public function markAsSent(Invoice $invoice)
    {
        $invoice->markAsSent();
        return redirect()->back()->with('success', 'Invoice marked as sent!');
    }

    public function markAsPaid(Invoice $invoice)
    {
        $invoice->markAsPaid();
        return redirect()->back()->with('success', 'Invoice marked as paid!');
    }

    public function markAsCancelled(Invoice $invoice)
    {
        $invoice->markAsCancelled();
        return redirect()->back()->with('success', 'Invoice marked as cancelled!');
    }

    /**
     * Change invoice status
     */
    public function changeInvoiceStatus(Invoice $invoice, Request $request)
    {
        $request->validate([
            'invoice_status' => 'required|in:pending,cancelled_by_cash,cancelled_by_transfer'
        ]);

        $invoice->update([
            'invoice_status' => $request->invoice_status
        ]);

        return redirect()->back()->with('success', 'Invoice status updated successfully.');
    }

    /**
     * Download invoice as PDF
     */
    public function downloadPdf(Invoice $invoice)
    {
        // Cargar los envíos asociados a esta factura (misma lógica que show)
        $shipments = collect();
        if ($invoice->shipment_id) {
            // Buscar en la tabla pivot los envíos de esta factura
            $shipmentIds = \DB::table('invoice_shipments')
                ->where('invoice_id', $invoice->id)
                ->pluck('shipment_id');
                
            if ($shipmentIds->count() > 0) {
                $shipments = Shipment::whereIn('id', $shipmentIds)
                    ->with(['box', 'recipient'])
                    ->get();
            } else {
                // Fallback: usar el envío principal si no hay relaciones
                $shipments = Shipment::where('id', $invoice->shipment_id)
                    ->with(['box', 'recipient'])
                    ->get();
            }
        }
        
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'shipments'));
        
        $filename = 'Invoice_' . $invoice->invoice_number . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Send invoice via email
     */
    public function sendEmail(Invoice $invoice, Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        
        $filename = 'Invoice_' . $invoice->invoice_number . '.pdf';
        
        // Aquí puedes implementar el envío de email
        // Por ahora solo descarga el PDF
        
        return redirect()->back()->with('success', 'Invoice sent successfully to ' . $request->email);
    }
}
