<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tracking - IMPEF</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">IMPEF</h1>
                    <p class="text-sm text-gray-600 mb-6">Shipment Tracking</p>
                </div>

                <div class="space-y-4">
                    <div class="border-b border-gray-200 pb-4">
                        <div class="text-sm font-medium text-gray-500">Tracking Code</div>
                        <div class="mt-1 text-lg font-semibold text-gray-900">{{ $publicData['code'] }}</div>
                    </div>

                    <div class="border-b border-gray-200 pb-4">
                        <div class="text-sm font-medium text-gray-500">Status</div>
                        <div class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($publicData['status'] === 'por_recepcionar') bg-gray-100 text-gray-800
                                @elseif($publicData['status'] === 'recepcionado') bg-blue-100 text-blue-800
                                @elseif($publicData['status'] === 'dejado_almacen') bg-yellow-100 text-yellow-800
                                @elseif($publicData['status'] === 'en_nicaragua') bg-purple-100 text-purple-800
                                @elseif($publicData['status'] === 'entregado') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800
                                @endif">
                                @switch($publicData['status'])
                                    @case('por_recepcionar')
                                        To Receive
                                        @break
                                    @case('recepcionado')
                                        Received
                                        @break
                                    @case('dejado_almacen')
                                        Left at Warehouse
                                        @break
                                    @case('en_nicaragua')
                                        In Nicaragua
                                        @break
                                    @case('entregado')
                                        Delivered
                                        @break
                                    @case('cancelled')
                                        Cancelled
                                        @break
                                    @default
                                        {{ ucfirst($publicData['status']) }}
                                @endswitch
                            </span>
                        </div>
                    </div>

                    <div class="border-b border-gray-200 pb-4">
                        <div class="text-sm font-medium text-gray-500">Delivery Department</div>
                        <div class="mt-1 text-sm text-gray-900">{{ $publicData['department'] }}</div>
                    </div>

                    <div class="border-b border-gray-200 pb-4">
                        <div class="text-sm font-medium text-gray-500">Delivery City</div>
                        <div class="mt-1 text-sm text-gray-900">{{ $publicData['city'] }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Route Month</div>
                        <div class="mt-1 text-sm text-gray-900">{{ $publicData['route_month'] }}</div>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">
                        For more information, contact your shipping agent.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 