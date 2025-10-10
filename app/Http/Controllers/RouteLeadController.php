<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\RouteLead;
use Illuminate\Http\Request;

class RouteLeadController extends Controller
{
    /**
     * Display a listing of route leads
     */
    public function index()
    {
        $routeLeads = RouteLead::with('client')
            ->orderBy('created_at', 'desc')
            ->get();

        // Group leads by status
        $pendingLeads = $routeLeads->where('status', 'pending');
        $confirmedLeads = $routeLeads->where('status', 'confirmed');
        $shippedLeads = $routeLeads->where('status', 'shipped');
        $deliveredLeads = $routeLeads->where('status', 'delivered');

        // Get all clients for the form
        $allClients = Client::orderBy('full_name', 'asc')->get();

        return view('route-leads.index', compact(
            'routeLeads',
            'pendingLeads',
            'confirmedLeads', 
            'shippedLeads',
            'deliveredLeads',
            'allClients'
        ));
    }

    /**
     * Create a new route lead
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'box_height' => 'nullable|numeric|min:0.1|max:1000',
            'box_width' => 'nullable|numeric|min:0.1|max:1000',
            'box_length' => 'nullable|numeric|min:0.1|max:1000',
            'nicaragua_address' => 'required|string|max:500',
            'nicaragua_phone' => 'required|string|max:20',
            'box_quantity' => 'required|integer|min:1|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        RouteLead::create([
            'client_id' => $request->client_id,
            'box_height' => $request->box_height,
            'box_width' => $request->box_width,
            'box_length' => $request->box_length,
            'nicaragua_address' => $request->nicaragua_address,
            'nicaragua_phone' => $request->nicaragua_phone,
            'box_quantity' => $request->box_quantity,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return redirect()->route('route-leads.index')
            ->with('success', 'Route lead created successfully');
    }

    /**
     * Update route lead status
     */
    public function updateStatus(RouteLead $routeLead, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered',
        ]);

        $routeLead->update(['status' => $request->status]);

        return redirect()->route('route-leads.index')
            ->with('success', 'Route lead status updated');
    }

    /**
     * Delete route lead
     */
    public function destroy(RouteLead $routeLead)
    {
        $routeLead->delete();

        return redirect()->route('route-leads.index')
            ->with('success', 'Route lead deleted');
    }
}
