<?php

namespace App\Livewire;

use App\Models\Route;
use App\Models\RouteExpense;
use Livewire\Component;

class RouteExpensesTable extends Component
{
    public Route $route;
    public $category = '';
    public $amount = '';
    public $description = '';
    public $vendor = '';

    protected $rules = [
        'category' => 'required',
        'amount' => 'required|numeric|min:0',
        'description' => 'nullable|string',
        'vendor' => 'nullable|string',
    ];

    public function mount(Route $route)
    {
        $this->route = $route;
    }

    public function render()
    {
        $expenses = $this->route->routeExpenses()
            ->orderBy('created_at', 'desc')
            ->get();

        $total = $expenses->sum('amount_usd');

        return view('livewire.route-expenses-table', [
            'expenses' => $expenses,
            'total' => $total
        ]);
    }

    public function addExpense()
    {
        $this->validate();

        $this->route->routeExpenses()->create([
            'category' => $this->category,
            'amount_usd' => $this->amount,
            'description' => $this->description,
            'vendor' => $this->vendor,
            'paid_at' => now(),
        ]);

        $this->reset(['category', 'amount', 'description', 'vendor']);
        $this->dispatch('expenseAdded');
    }

    public function deleteExpense(RouteExpense $expense)
    {
        $expense->delete();
        $this->dispatch('expenseDeleted');
    }
}
