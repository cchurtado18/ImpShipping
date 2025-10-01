<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\Client;
use App\Models\Box;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Shipment Status Analytics - Datos reales
        $shipmentStatusData = Shipment::select('shipment_status', DB::raw('count(*) as count'))
            ->groupBy('shipment_status')
            ->get()
            ->pluck('count', 'shipment_status');

        // Revenue Analytics - Datos reales de pagos
        $totalRevenue = Payment::sum('amount_usd') ?? 0;
        $monthlyRevenue = Payment::whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount_usd') ?? 0;

        // Client Activity - Datos reales
        $topClients = Client::withCount('shipments')
            ->orderBy('shipments_count', 'desc')
            ->limit(5)
            ->get();

        // Payment Status Analytics - Datos reales
        $paymentStatusData = Shipment::select('payment_status', DB::raw('count(*) as count'))
            ->groupBy('payment_status')
            ->get()
            ->pluck('count', 'payment_status');

        // Monthly Revenue Trend - Datos reales por mes
        $monthlyRevenueData = [];
        for ($i = 1; $i <= 12; $i++) {
            $revenue = Payment::whereMonth('paid_at', $i)
                ->whereYear('paid_at', now()->year)
                ->sum('amount_usd') ?? 0;
            $monthlyRevenueData[] = $revenue;
        }

        // Shipment Status para gráfico - Asegurar que todos los estados estén presentes
        $shipmentStatusForChart = [
            'por_recepcionar' => $shipmentStatusData->get('por_recepcionar', 0),
            'recepcionado' => $shipmentStatusData->get('recepcionado', 0),
            'dejado_almacen' => $shipmentStatusData->get('dejado_almacen', 0),
            'en_nicaragua' => $shipmentStatusData->get('en_nicaragua', 0),
            'entregado' => $shipmentStatusData->get('entregado', 0),
            'cancelled' => $shipmentStatusData->get('cancelled', 0)
        ];

        // Payment Status para gráfico - Asegurar que todos los estados estén presentes
        $paymentStatusForChart = [
            'pending' => $paymentStatusData->get('pending', 0),
            'partial' => $paymentStatusData->get('partial', 0),
            'paid' => $paymentStatusData->get('paid', 0)
        ];

        return view('dashboard', compact(
            'shipmentStatusData',
            'totalRevenue',
            'monthlyRevenue',
            'topClients',
            'paymentStatusData',
            'monthlyRevenueData',
            'shipmentStatusForChart',
            'paymentStatusForChart'
        ));
    }
}