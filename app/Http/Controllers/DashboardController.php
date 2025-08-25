<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Services\MonthlyRouteService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private MonthlyRouteService $routeService
    ) {}

    public function index()
    {
        $currentRoute = Route::where('month', now()->format('Y-m'))->first();
        
        if ($currentRoute) {
            $summary = $this->routeService->getRouteSummary($currentRoute);
        } else {
            $summary = [
                'projected_income' => 0,
                'real_income' => 0,
                'expenses' => 0,
                'debts' => 0,
                'profit' => 0,
                'margin' => 0,
                'shipments_count' => 0,
                'delivered_count' => 0,
            ];
        }

        return view('dashboard', compact('currentRoute', 'summary'));
    }
}
