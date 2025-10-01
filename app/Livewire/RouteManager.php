<?php

namespace App\Livewire;

use App\Models\Route;
use Livewire\Component;
use Livewire\WithPagination;

class RouteManager extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $month = '';
    public $responsible = '';
    public $route_start_date = '';
    public $route_end_date = '';
    public $selectedStates = [];

    protected $rules = [
        'month' => 'required|string',
        'responsible' => 'required|string|in:Francisco,Elmer,Geovanni',
        'route_start_date' => 'required|date',
        'route_end_date' => 'required|date|after:route_start_date',
        'selectedStates' => 'required|array|min:1',
        'selectedStates.*' => 'required|string|in:AL,AK,AZ,AR,CA,CO,CT,DE,FL,GA,HI,ID,IL,IN,IA,KS,KY,LA,ME,MD,MA,MI,MN,MS,MO,MT,NE,NV,NH,NJ,NM,NY,NC,ND,OH,OK,OR,PA,RI,SC,SD,TN,TX,UT,VT,VA,WA,WV,WI,WY',
    ];

    protected $messages = [
        'month.required' => 'Month is required.',
        'responsible.required' => 'Responsible person is required.',
        'responsible.in' => 'Please select a valid responsible person.',
        'route_start_date.required' => 'Route start date is required.',
        'route_end_date.required' => 'Route end date is required.',
        'route_end_date.after' => 'Route end date must be after route start date.',
        'selectedStates.required' => 'Please select at least one state.',
        'selectedStates.min' => 'Please select at least one state.',
    ];

    public function mount()
    {
        $this->resetForm();
    }

    public function render()
    {
        $routes = Route::orderBy('created_at', 'desc')->paginate(10);
        $responsibleOptions = Route::getResponsibleOptions();
        $usStates = Route::getUSStates();
        
        return view('livewire.route-manager', [
            'routes' => $routes,
            'responsibleOptions' => $responsibleOptions,
            'usStates' => $usStates
        ]);
    }

    public function showCreateForm()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function createRoute()
    {
        $this->validate();

        Route::create([
            'month' => $this->month,
            'responsible' => $this->responsible,
            'route_start_date' => $this->route_start_date,
            'route_end_date' => $this->route_end_date,
            'states' => $this->selectedStates,
            'status' => 'collecting',
            'is_active' => false,
        ]);

        session()->flash('success', 'Route created successfully!');
        $this->closeModal();
    }

    public function closeRoute(Route $route)
    {
        $route->update([
            'status' => 'closed',
            'is_active' => false,
        ]);

        session()->flash('success', 'Route closed successfully!');
    }

    public function activateRoute(Route $route)
    {
        $route->activate();
        session()->flash('success', 'Route activated successfully!');
    }

    public function deactivateRoute(Route $route)
    {
        $route->deactivate();
        session()->flash('success', 'Route deactivated successfully!');
    }

    public function deleteRoute(Route $route)
    {
        // Verificar si la ruta tiene envíos asociados
        if ($route->shipments()->count() > 0) {
            session()->flash('error', 'Cannot delete route with associated shipments. Please remove shipments first.');
            return;
        }

        // Verificar si la ruta tiene gastos asociados
        if ($route->routeExpenses()->count() > 0) {
            session()->flash('error', 'Cannot delete route with associated expenses. Please remove expenses first.');
            return;
        }

        $route->delete();
        session()->flash('success', 'Route deleted successfully!');
    }

    public function viewRouteClients(Route $route)
    {
        return redirect()->route('routes.clients', $route);
    }

    private function resetForm()
    {
        $this->month = now()->format('Y-m');
        $this->responsible = '';
        $this->route_start_date = now()->format('Y-m-d');
        $this->route_end_date = now()->addDays(30)->format('Y-m-d');
        $this->selectedStates = [];
    }
}
