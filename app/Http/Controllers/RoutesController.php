<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Setting;
use App\Services\MonthlyRouteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoutesController extends Controller
{
    public function __construct(
        private MonthlyRouteService $routeService
    ) {}

    public function current()
    {
        $currentMonth = now()->format('Y-m');
        $route = Route::where('month', $currentMonth)->first();

        if (!$route) {
            // Crear nueva ruta del mes
            $route = $this->createCurrentRoute($currentMonth);
        }

        return view('routes.current', compact('route'));
    }

    public function close(Request $request, Route $route)
    {
        $this->authorize('close', $route);

        DB::transaction(function () use ($route) {
            $route->update([
                'status' => 'closed',
                'arrival_at' => now(),
            ]);

            // Limpiar cache
            $this->routeService->clearCache($route);
        });

        return redirect()->route('routes.current')
            ->with('success', 'Ruta cerrada exitosamente');
    }

    private function createCurrentRoute(string $month): Route
    {
        $cutoffDay = Setting::get('cutoff_day', 12);
        $departureDay = Setting::get('departure_day', 13);
        $defaultTimes = Setting::getJson('default_times', [
            'collection' => '08:00',
            'cutoff' => '18:00',
            'departure' => '09:00',
        ]);

        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);

        $collectionStart = now()->startOfMonth();
        $cutoffAt = now()->setDate($year, $monthNum, $cutoffDay)->setTimeFromTimeString($defaultTimes['cutoff']);
        $departureAt = now()->setDate($year, $monthNum, $departureDay)->setTimeFromTimeString($defaultTimes['departure']);

        return Route::create([
            'month' => $month,
            'collection_start_at' => $collectionStart,
            'cutoff_at' => $cutoffAt,
            'departure_at' => $departureAt,
            'status' => 'collecting',
        ]);
    }
}
