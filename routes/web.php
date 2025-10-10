<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FollowupController;
use App\Http\Controllers\RoutesController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

// Tracking público (sin autenticación)
Route::get('/t/{code}', [TrackingController::class, 'show'])->name('tracking.show');

// Rutas de prueba (sin autenticación)
Route::get('/test-client-search', function () {
    return view('test-client-search');
})->name('test.client.search');

// APIs de clientes (sin autenticación para pruebas)
Route::get('/clients/search', [ClientController::class, 'search'])->name('clients.search');
Route::get('/clients/states', [ClientController::class, 'getStates'])->name('clients.states');

// Rutas de autenticación
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    $credentials = request()->only(['email', 'password']);
    
    if (auth()->attempt($credentials)) {
        return redirect()->intended('/');
    }
    
    return back()->withErrors([
        'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
    ]);
})->name('login.post');

Route::post('/logout', function () {
    auth()->logout();
    return redirect('/login');
})->name('logout');

// Rutas del sistema (requieren autenticación)
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
                // Rutas
            Route::get('/routes/current', function () {
                // Buscar ruta activa primero
                $route = \App\Models\Route::where('is_active', true)->first();
                
                if (!$route) {
                    // Si no hay ruta activa, buscar la más reciente que no esté cerrada
                    $route = \App\Models\Route::where('status', '!=', 'closed')
                        ->orderBy('created_at', 'desc')
                        ->first();
                }
                
                if (!$route) {
                    // Si no hay ninguna ruta, redirigir a la gestión de rutas
                    return redirect()->route('routes.index')
                        ->with('info', 'No active route found. Please create a route first.');
                }
                
                return view('routes.current', compact('route'));
            })->name('routes.current');
        Route::post('/routes/{route}/close', [RoutesController::class, 'close'])->name('routes.close');
    

    
    
    // Gestión de rutas
    Route::get('/routes', function () {
        return view('routes.index');
    })->name('routes.index');
    
    // Clientes de una ruta específica
    Route::get('/routes/{route}/clients', function (\App\Models\Route $route) {
        $eligibleClients = $route->getEligibleClients();
        return view('routes.clients', compact('route', 'eligibleClients'));
    })->name('routes.clients');
    
    // Gastos de una ruta específica
    Route::get('/routes/{route}/expenses', function (\App\Models\Route $route) {
        $expenses = $route->routeExpenses()->orderBy('expense_date', 'desc')->paginate(15);
        return view('routes.expenses', compact('route', 'expenses'));
    })->name('routes.expenses');
    
    // Sistema de recepción de cajas
    Route::get('/routes/{route}/reception', function (\App\Models\Route $route) {
        return view('routes.reception', compact('route'));
    })->name('routes.reception');
    
    // Reportes de una ruta específica
    Route::get('/routes/{route}/reports', function (\App\Models\Route $route) {
        $projections = $route->routeProjections()->orderBy('projection_date', 'desc')->paginate(15);
        $expenses = $route->routeExpenses()->get();
        $shipments = $route->shipments()->get();
        return view('routes.reports', compact('route', 'projections', 'expenses', 'shipments'));
    })->name('routes.reports');
    
    // Gestión de facturas
    Route::resource('invoices', App\Http\Controllers\InvoiceController::class);
    Route::post('/invoices/{invoice}/mark-sent', [App\Http\Controllers\InvoiceController::class, 'markAsSent'])->name('invoices.mark-sent');
    Route::post('/invoices/{invoice}/mark-paid', [App\Http\Controllers\InvoiceController::class, 'markAsPaid'])->name('invoices.mark-paid');
    Route::post('/invoices/{invoice}/mark-cancelled', [App\Http\Controllers\InvoiceController::class, 'markAsCancelled'])->name('invoices.mark-cancelled');
    Route::post('/invoices/{invoice}/change-status', [App\Http\Controllers\InvoiceController::class, 'changeInvoiceStatus'])->name('invoices.change-status');
    Route::get('/invoices/{invoice}/download-pdf', [App\Http\Controllers\InvoiceController::class, 'downloadPdf'])->name('invoices.download-pdf');
    Route::post('/invoices/{invoice}/send-email', [App\Http\Controllers\InvoiceController::class, 'sendEmail'])->name('invoices.send-email');
    
    // Gestión de clientes
    Route::get('/clients', function () {
        return view('clients.index');
    })->name('clients.index');
    
    // Seguimientos de clientes
    Route::get('/followups', [FollowupController::class, 'index'])->name('followups.index');
    Route::post('/followups/schedule', [FollowupController::class, 'scheduleFollowup'])->name('followups.schedule');
    Route::post('/followups/{client}/mark-done', [FollowupController::class, 'markDone'])->name('followups.mark-done');
    Route::post('/followups/{client}/postpone', [FollowupController::class, 'postpone'])->name('followups.postpone');
    
    // Route Leads
    Route::get('/route-leads', [App\Http\Controllers\RouteLeadController::class, 'index'])->name('route-leads.index');
    Route::post('/route-leads', [App\Http\Controllers\RouteLeadController::class, 'store'])->name('route-leads.store');
    Route::post('/route-leads/{routeLead}/status', [App\Http\Controllers\RouteLeadController::class, 'updateStatus'])->name('route-leads.update-status');
    Route::delete('/route-leads/{routeLead}', [App\Http\Controllers\RouteLeadController::class, 'destroy'])->name('route-leads.delete');
    
    // Envíos
    Route::get('/shipments', [App\Http\Controllers\ShipmentController::class, 'index'])->name('shipments.index');
    Route::post('/shipments', [App\Http\Controllers\ShipmentController::class, 'store'])->name('shipments.store');
    Route::put('/shipments/{shipment}', [App\Http\Controllers\ShipmentController::class, 'update'])->name('shipments.update');
    Route::delete('/shipments/{shipment}', [App\Http\Controllers\ShipmentController::class, 'destroy'])->name('shipments.destroy');
    Route::get('/shipments/import', [App\Http\Controllers\ShipmentController::class, 'importForm'])->name('shipments.import.form');
    Route::post('/shipments/import', [App\Http\Controllers\ShipmentController::class, 'import'])->name('shipments.import');
    
    // Pagos
    Route::post('/payments', [App\Http\Controllers\PaymentController::class, 'store'])->name('payments.store');
    
    // Gastos de ruta
    Route::post('/route-expenses', [App\Http\Controllers\RouteExpenseController::class, 'store'])->name('route-expenses.store');
    Route::delete('/route-expenses/{expense}', [App\Http\Controllers\RouteExpenseController::class, 'destroy'])->name('route-expenses.destroy');
    
    // Exportación
    Route::get('/routes/{route}/export/excel', [App\Http\Controllers\RouteController::class, 'exportExcel'])->name('routes.export.excel');
    Route::get('/routes/{route}/export/pdf', [App\Http\Controllers\RouteController::class, 'exportPdf'])->name('routes.export.pdf');
});
