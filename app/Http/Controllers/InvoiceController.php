<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'shipment_id' => 'nullable|exists:shipments,id',
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
            'terms' => $request->terms ?? '30 Days',
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
            'shipment_id' => $request->shipment_id,
            'user_id' => Auth::id(),
        ]);

        // Marcar el envío como facturado
        if ($request->shipment_id) {
            Shipment::where('id', $request->shipment_id)->update(['invoiced' => true]);
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice created successfully!');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'shipment', 'user']);
        return view('invoices.show', compact('invoice'));
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
            'terms' => $request->terms ?? '30 Days',
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
}
