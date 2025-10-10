<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Client;
use App\Models\Recipient;
use App\Models\Route;
use App\Models\Box;
use App\Services\PricingService;
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
                'boxes.*.price' => 'required|numeric|min:0',
                'boxes.*.priceMode' => 'required|in:calculated,manual',
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
                
                // Usar el precio que viene del frontend (ya sea calculado o manual)
                $boxPrice = $boxData['price'] ?? 0;
                
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
                    'shipment_status' => 'por_recepcionar',
                    'sale_price_usd' => $totalPrice,
                    'declared_value' => 0,
                    'notes' => $request->input('notes') . "\nDimensiones: {$boxData['length']}\" × {$boxData['width']}\" × {$boxData['height']}\" ({$cubicFeet} ft³)" . 
                               ($boxData['weight'] ? "\nPeso: {$boxData['weight']['lbs']} lb × \${$boxData['weight']['rate']}/lb" : '') .
                               "\nPrecio: \${$boxPrice} USD (" . ($boxData['priceMode'] ?? 'calculado') . ")" .
                               ($boxData['priceMode'] === 'manual' && isset($boxData['calculatedPrice']) ? 
                                   "\nPrecio calculado: \${$boxData['calculatedPrice']} USD" : ''),
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
        return PricingService::calculateBoxPrice($cubicFeet, $boxData['weight'] ?? null);
    }
}
