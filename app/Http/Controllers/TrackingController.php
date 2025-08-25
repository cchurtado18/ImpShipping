<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function show(string $code)
    {
        $shipment = Shipment::where('code', $code)->first();

        if (!$shipment) {
            abort(404, 'Envío no encontrado');
        }

        // Solo mostrar información pública (sin PII)
        $publicData = [
            'code' => $shipment->code,
            'status' => $shipment->shipment_status,
            'department' => $shipment->recipient->ni_department,
            'city' => $shipment->recipient->ni_city,
            'route_month' => $shipment->route->month,
        ];

        return view('tracking.show', compact('publicData'));
    }
}
