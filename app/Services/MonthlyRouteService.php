<?php

namespace App\Services;

use App\Models\Route;
use Illuminate\Support\Facades\Cache;

class MonthlyRouteService
{
    public function projectedIncome(Route $route): float
    {
        return Cache::remember("route_{$route->id}_projected_income", 60, function () use ($route) {
            return $route->shipments()
                ->where('shipment_status', '!=', 'cancelled')
                ->sum('sale_price_usd') ?? 0;
        });
    }

    public function realIncome(Route $route): float
    {
        return Cache::remember("route_{$route->id}_real_income", 60, function () use ($route) {
            return $route->payments()->sum('amount_usd') ?? 0;
        });
    }

    public function expenses(Route $route): float
    {
        return Cache::remember("route_{$route->id}_expenses", 60, function () use ($route) {
            return $route->routeExpenses()->sum('amount_usd') ?? 0;
        });
    }

    public function debts(Route $route): float
    {
        return Cache::remember("route_{$route->id}_debts", 60, function () use ($route) {
            $totalDebt = 0;
            
            foreach ($route->shipments as $shipment) {
                if ($shipment->shipment_status !== 'cancelled') {
                    $salePrice = $shipment->sale_price_usd ?? 0;
                    $totalPaid = $shipment->total_paid;
                    $debt = max(0, $salePrice - $totalPaid);
                    $totalDebt += $debt;
                }
            }
            
            return $totalDebt;
        });
    }

    public function profit(Route $route): float
    {
        return $this->realIncome($route) - $this->expenses($route);
    }

    public function margin(Route $route): float
    {
        $realIncome = $this->realIncome($route);
        
        if ($realIncome > 0) {
            return ($this->profit($route) / $realIncome) * 100;
        }
        
        return 0;
    }

    public function getRouteSummary(Route $route): array
    {
        return [
            'projected_income' => $this->projectedIncome($route),
            'real_income' => $this->realIncome($route),
            'expenses' => $this->expenses($route),
            'debts' => $this->debts($route),
            'profit' => $this->profit($route),
            'margin' => $this->margin($route),
            'shipments_count' => $route->shipments()->where('shipment_status', '!=', 'cancelled')->count(),
            'delivered_count' => $route->shipments()->where('shipment_status', 'entregado')->count(),
        ];
    }

    public function clearCache(Route $route): void
    {
        Cache::forget("route_{$route->id}_projected_income");
        Cache::forget("route_{$route->id}_real_income");
        Cache::forget("route_{$route->id}_expenses");
        Cache::forget("route_{$route->id}_debts");
    }
} 