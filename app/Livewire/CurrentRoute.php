<?php

namespace App\Livewire;

use App\Models\Route;
use Livewire\Component;

class CurrentRoute extends Component
{
    public Route $route;
    public $activeTab = 'shipments';
    public $showShipmentModal = false;

    public function mount(Route $route)
    {
        $this->route = $route;
    }

    public function showTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function showShipmentForm()
    {
        $this->showShipmentModal = true;
    }

    public function closeShipmentModal()
    {
        $this->showShipmentModal = false;
    }

    public function render()
    {
        return view('livewire.current-route');
    }
}

