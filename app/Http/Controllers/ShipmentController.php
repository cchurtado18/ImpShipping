<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Client;
use App\Models\Recipient;
use App\Models\Route;
use App\Models\Box;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShipmentController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'client.id' => 'required|integer|exists:clients,id',
                'boxes' => 'required|array|min:1',
                'boxes.*.length' => 'required|numeric|min:0.1',
                'boxes.*.width' => 'required|numeric|min:0.1',
                'boxes.*.height' => 'required|numeric|min:0.1',
                'recipient.name' => 'required|string|max:255',
                'recipient.phone' => 'required|string|max:20',
                'recipient.department' => 'required|string|max:100',
                'recipient.city' => 'required|string|max:100',
                'recipient.address' => 'required|string',
                'price_total_usd' => 'required|numeric|min:0',
            ]);

            // Obtener la ruta actual
            $route = Route::where('status', '!=', 'closed')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$route) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay una ruta activa'
                ], 400);
            }

            // Crear o actualizar el cliente
            $client = Client::find($request->input('client.id'));

            // Crear el receptor
            $recipient = Recipient::create([
                'client_id' => $client->id,
                'full_name' => $request->input('recipient.name'),
                'age' => $request->input('recipient.age'),
                'ni_phone' => $request->input('recipient.phone'),
                'ni_department' => $request->input('recipient.department'),
                'ni_city' => $request->input('recipient.city'),
                'ni_address' => $request->input('recipient.address'),
            ]);

            // Crear un envío por cada caja
            $shipments = [];
            foreach ($request->input('boxes') as $boxData) {
                // Generar código único para el envío
                $code = 'IMPEF-' . strtoupper(Str::random(8));
                
                // Determinar el box_id basado en las dimensiones (usar el más cercano)
                $cubicFeet = ($boxData['length'] * $boxData['width'] * $boxData['height']) / 1728;
                $box = $this->findClosestBox($cubicFeet);
                
                // Calcular precio individual de la caja
                $boxPrice = $this->calculateBoxPrice($cubicFeet, $boxData);
                
                // Agregar costo de transporte si está habilitado
                $transportCost = $request->input('transport.enabled') ? 
                    $request->input('transport.amount_per_box', 0) : 0;
                
                $totalPrice = $boxPrice + $transportCost;

                $shipment = Shipment::create([
                    'client_id' => $client->id,
                    'recipient_id' => $recipient->id,
                    'route_id' => $route->id,
                    'box_id' => $box ? $box->id : null,
                    'code' => $code,
                    'shipment_status' => 'lead',
                    'sale_price_usd' => $totalPrice,
                    'declared_value' => 0,
                    'notes' => $request->input('notes') . "\nDimensiones: {$boxData['length']}\" × {$boxData['width']}\" × {$boxData['height']}\" ({$cubicFeet} ft³)" . 
                               ($boxData['weight'] ? "\nPeso: {$boxData['weight']['lbs']} lb × \${$boxData['weight']['rate']}/lb" : ''),
                ]);

                $shipments[] = $shipment;
            }

            return response()->json([
                'success' => true,
                'message' => 'Envío creado exitosamente',
                'shipments' => $shipments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el envío: ' . $e->getMessage()
            ], 500);
        }
    }

    private function findClosestBox($cubicFeet)
    {
        // Buscar el box más cercano en tamaño
        $boxes = Box::where('active', true)->get();
        $closestBox = null;
        $minDifference = PHP_FLOAT_MAX;

        foreach ($boxes as $box) {
            $boxCubicFeet = ($box->length_in * $box->width_in * $box->height_in) / 1728;
            $difference = abs($cubicFeet - $boxCubicFeet);
            
            if ($difference < $minDifference) {
                $minDifference = $difference;
                $closestBox = $box;
            }
        }

        return $closestBox;
    }

    private function calculateBoxPrice($cubicFeet, $boxData)
    {
        // Si es caja pequeña, usar cálculo por peso
        if ($cubicFeet >= 0.1 && $cubicFeet <= 2.99 && isset($boxData['weight'])) {
            return $boxData['weight']['lbs'] * $boxData['weight']['rate'];
        }

        // Usar la fórmula volumétrica
        if ($cubicFeet >= 2.90 && $cubicFeet <= 3.89) {
            return round($cubicFeet * 49);
        } elseif ($cubicFeet >= 3.90 && $cubicFeet <= 4.89) {
            return round($cubicFeet * 45);
        } elseif ($cubicFeet >= 4.90 && $cubicFeet <= 5.89) {
            return round($cubicFeet * 42.5);
        } elseif ($cubicFeet >= 5.90 && $cubicFeet <= 6.89) {
            return round($cubicFeet * 39);
        } elseif ($cubicFeet >= 6.90 && $cubicFeet <= 7.89) {
            return round($cubicFeet * 35);
        } elseif ($cubicFeet >= 7.90 && $cubicFeet <= 8.89) {
            return round($cubicFeet * 32);
        } elseif ($cubicFeet >= 8.90 && $cubicFeet <= 9.89) {
            return round($cubicFeet * 31);
        } elseif ($cubicFeet >= 9.90 && $cubicFeet <= 10.89) {
            return round($cubicFeet * 29.5);
        } elseif ($cubicFeet >= 10.90 && $cubicFeet <= 11.89) {
            return round($cubicFeet * 29);
        } elseif ($cubicFeet >= 11.90 && $cubicFeet <= 12.89) {
            return round($cubicFeet * 28);
        } elseif ($cubicFeet >= 12.90 && $cubicFeet <= 13.89) {
            return round($cubicFeet * 26.5);
        } elseif ($cubicFeet >= 13.90 && $cubicFeet <= 14.89) {
            return round($cubicFeet * 25.5);
        } elseif ($cubicFeet >= 14.90 && $cubicFeet <= 16.99) {
            return round($cubicFeet * 24.5);
        } elseif ($cubicFeet >= 17 && $cubicFeet <= 19.99) {
            return round($cubicFeet * 24);
        } elseif ($cubicFeet >= 20) {
            return round($cubicFeet * 22.75);
        }

        return 0;
    }
}
