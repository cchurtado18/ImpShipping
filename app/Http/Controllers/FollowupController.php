<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class FollowupController extends Controller
{
    /**
     * Display the followups dashboard
     */
    public function index()
    {
        $today = Carbon::today();
        $nextWeek = Carbon::today()->addDays(7);

        // Clientes atrasados (fecha menor a hoy)
        $overdueClients = Client::where('is_followup_enabled', true)
            ->whereNotNull('next_followup_at')
            ->where('next_followup_at', '<', $today)
            ->orderBy('next_followup_at', 'asc')
            ->get();

        // Clientes para hoy
        $todayClients = Client::where('is_followup_enabled', true)
            ->whereNotNull('next_followup_at')
            ->whereDate('next_followup_at', $today)
            ->orderBy('next_followup_at', 'asc')
            ->get();

        // Clientes próximos 7 días
        $upcomingClients = Client::where('is_followup_enabled', true)
            ->whereNotNull('next_followup_at')
            ->where('next_followup_at', '>', $today)
            ->where('next_followup_at', '<=', $nextWeek)
            ->orderBy('next_followup_at', 'asc')
            ->get();

        // Clientes completados (marcados como contactados)
        $completedClients = Client::where('is_followup_enabled', false)
            ->whereNotNull('last_contacted_at')
            ->orderBy('last_contacted_at', 'desc')
            ->get();

        // Todos los clientes para selección (que no tienen follow-ups activos)
        $allClients = Client::where('is_followup_enabled', false)
            ->whereNull('last_contacted_at')
            ->orWhere(function($query) {
                $query->whereNull('is_followup_enabled')
                      ->whereNull('last_contacted_at');
            })
            ->orderBy('full_name', 'asc')
            ->get();

        return view('followups.index', compact(
            'overdueClients',
            'todayClients', 
            'upcomingClients',
            'completedClients',
            'allClients'
        ));
    }

    /**
     * Schedule a followup for a client
     */
    public function scheduleFollowup(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'hours' => 'required|integer|min:1|max:168', // Max 1 week
            'notes' => 'nullable|string|max:500',
        ]);

        $client = Client::findOrFail($request->client_id);
        $client->scheduleFollowup(
            $request->hours,
            null, // No label
            $request->notes
        );

        return redirect()->route('followups.index')
            ->with('success', 'Followup scheduled for ' . $client->full_name);
    }

    /**
     * Mark followup as done (mark as contacted)
     */
    public function markDone(Client $client)
    {
        $client->markAsContacted();

        return redirect()->route('followups.index')
            ->with('success', 'Followup marked as done for ' . $client->full_name);
    }

    /**
     * Postpone followup by specified hours
     */
    public function postpone(Client $client, Request $request)
    {
        $hours = (int) $request->input('hours', 24); // Default 24 hours, ensure integer
        $currentDate = $client->next_followup_at ?? Carbon::now();
        $newDate = Carbon::parse($currentDate)->addHours($hours);

        $client->update([
            'next_followup_at' => $newDate
        ]);

        return redirect()->route('followups.index')
            ->with('success', 'Followup postponed for ' . $client->full_name . ' to ' . $newDate->format('M d, Y H:i'));
    }

}