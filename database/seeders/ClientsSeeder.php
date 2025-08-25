<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Recipient;
use Illuminate\Database\Seeder;

class ClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = [
            [
                'full_name' => 'María González',
                'us_address' => '123 Main St, Miami, FL 33101',
                'us_phone' => '+1 (305) 555-0101',
                'email' => 'maria.gonzalez@email.com',
                'status' => 'confirmado',
                'notes' => 'Cliente frecuente, siempre paga a tiempo',
                'recipients' => [
                    [
                        'full_name' => 'Carlos González',
                        'ni_phone' => '+505 8888-1234',
                        'ni_department' => 'Managua',
                        'ni_city' => 'Managua',
                        'ni_address' => 'Colonia Centroamérica, Casa #45',
                    ]
                ]
            ],
            [
                'full_name' => 'Juan Pérez',
                'us_address' => '456 Oak Ave, Orlando, FL 32801',
                'us_phone' => '+1 (407) 555-0202',
                'email' => 'juan.perez@email.com',
                'status' => 'en_seguimiento',
                'notes' => 'Interesado en envíos regulares',
                'recipients' => [
                    [
                        'full_name' => 'Ana Pérez',
                        'ni_phone' => '+505 7777-5678',
                        'ni_department' => 'León',
                        'ni_city' => 'León',
                        'ni_address' => 'Barrio San Sebastián, Casa #12',
                    ]
                ]
            ],
            [
                'full_name' => 'Carmen Rodríguez',
                'us_address' => '789 Pine St, Tampa, FL 33601',
                'us_phone' => '+1 (813) 555-0303',
                'email' => 'carmen.rodriguez@email.com',
                'status' => 'proxima_ruta',
                'notes' => 'Lista para la próxima ruta',
                'recipients' => [
                    [
                        'full_name' => 'Roberto Rodríguez',
                        'ni_phone' => '+505 6666-9012',
                        'ni_department' => 'Granada',
                        'ni_city' => 'Granada',
                        'ni_address' => 'Calle La Calzada, Casa #8',
                    ],
                    [
                        'full_name' => 'Sofia Rodríguez',
                        'ni_phone' => '+505 5555-3456',
                        'ni_department' => 'Managua',
                        'ni_city' => 'Managua',
                        'ni_address' => 'Colonia Los Robles, Casa #23',
                    ]
                ]
            ],
            [
                'full_name' => 'Luis Martínez',
                'us_address' => '321 Elm St, Jacksonville, FL 32201',
                'us_phone' => '+1 (904) 555-0404',
                'email' => 'luis.martinez@email.com',
                'status' => 'ruta_cancelada',
                'notes' => 'Canceló por problemas personales',
                'recipients' => [
                    [
                        'full_name' => 'Elena Martínez',
                        'ni_phone' => '+505 4444-7890',
                        'ni_department' => 'Chinandega',
                        'ni_city' => 'Chinandega',
                        'ni_address' => 'Barrio El Calvario, Casa #15',
                    ]
                ]
            ],
            [
                'full_name' => 'Isabel Silva',
                'us_address' => '654 Maple Dr, Fort Lauderdale, FL 33301',
                'us_phone' => '+1 (954) 555-0505',
                'email' => 'isabel.silva@email.com',
                'status' => 'en_seguimiento',
                'notes' => 'Nuevo cliente, necesita información',
                'recipients' => [
                    [
                        'full_name' => 'Miguel Silva',
                        'ni_phone' => '+505 3333-1234',
                        'ni_department' => 'Masaya',
                        'ni_city' => 'Masaya',
                        'ni_address' => 'Barrio Monimbó, Casa #7',
                    ]
                ]
            ]
        ];

        foreach ($clients as $clientData) {
            $recipients = $clientData['recipients'];
            unset($clientData['recipients']);
            
            $client = Client::create($clientData);
            
            foreach ($recipients as $recipientData) {
                $client->recipients()->create($recipientData);
            }
        }
    }
}
