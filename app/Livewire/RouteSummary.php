<?php

namespace App\Livewire;

use App\Models\Route;
use App\Services\MonthlyRouteService;
use Livewire\Component;

class RouteSummary extends Component
{
    public Route $route;
    public $summary = [];

    public function mount(Route $route)
    {
        $this->route = $route;
        $this->loadSummary();
    }

    public function loadSummary()
    {
        $service = new MonthlyRouteService();
        $this->summary = $service->getRouteSummary($this->route);
    }

    public function render()
    {
        return view('livewire.route-summary', [
            'summary' => $this->summary
        ]);
    }

    public function exportExcel()
    {
        return redirect()->route('routes.export.excel', $this->route);
    }

    public function exportPdf()
    {
        return redirect()->route('routes.export.pdf', $this->route);
    }
}
