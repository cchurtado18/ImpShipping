<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $searchTerm = $request->get('search', '');
        
        if (strlen($searchTerm) < 2) {
            return response()->json([]);
        }
        
        $clients = Client::where('full_name', 'like', '%' . $searchTerm . '%')
            ->orWhere('us_phone', 'like', '%' . $searchTerm . '%')
            ->orWhere('email', 'like', '%' . $searchTerm . '%')
            ->limit(10)
            ->get(['id', 'full_name', 'us_phone', 'email', 'us_address', 'us_state']);
        
        return response()->json($clients);
    }

    public function getStates(): JsonResponse
    {
        $states = Client::whereNotNull('us_state')
            ->where('us_state', '!=', '')
            ->distinct()
            ->pluck('us_state')
            ->sort()
            ->values();
        
        return response()->json($states);
    }
}
