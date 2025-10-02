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
        $overdueClients = Client::whereNotNull('next_followup_at')
            ->where('next_followup_at', '<', $today)
            ->orderBy('next_followup_at', 'asc')
            ->get();

        // Clientes para hoy
        $todayClients = Client::whereNotNull('next_followup_at')
            ->whereDate('next_followup_at', $today)
            ->orderBy('next_followup_at', 'asc')
            ->get();

        // Clientes próximos 7 días
        $upcomingClients = Client::whereNotNull('next_followup_at')
            ->where('next_followup_at', '>', $today)
            ->where('next_followup_at', '<=', $nextWeek)
            ->orderBy('next_followup_at', 'asc')
            ->get();

        return view('followups.index', compact(
            'overdueClients',
            'todayClients', 
            'upcomingClients'
        ));
    }

    /**
     * Mark followup as done (remove followup date)
     */
    public function markDone(Client $client)
    {
        $client->update([
            'next_followup_at' => null,
            'followup_note' => null
        ]);

        return redirect()->route('followups.index')
            ->with('success', 'Followup marked as done for ' . $client->full_name);
    }

    /**
     * Postpone followup by 7 days
     */
    public function postpone(Client $client)
    {
        $currentDate = $client->next_followup_at ?? Carbon::now();
        $newDate = Carbon::parse($currentDate)->addDays(7);

        $client->update([
            'next_followup_at' => $newDate
        ]);

        return redirect()->route('followups.index')
            ->with('success', 'Followup postponed for ' . $client->full_name . ' to ' . $newDate->format('M d, Y'));
    }
}