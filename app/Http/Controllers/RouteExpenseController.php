<?php

namespace App\Http\Controllers;

use App\Models\RouteExpense;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RouteExpenseController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'route_id' => 'required|exists:routes,id',
                'expense_type' => 'required|in:fuel,freight,warehouse,taxes,toll,per_diem,last_mile,other',
                'amount' => 'required|numeric|min:0.01',
                'description' => 'required|string|max:500',
                'expense_date' => 'required|date',
                'status' => 'nullable|in:pending,approved,rejected'
            ]);

            $expense = RouteExpense::create([
                'route_id' => $request->route_id,
                'expense_type' => $request->expense_type,
                'amount_usd' => $request->amount,
                'description' => $request->description,
                'expense_date' => $request->expense_date,
                'status' => $request->status ?? 'pending',
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Expense added successfully',
                'expense' => $expense
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding expense: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, RouteExpense $routeExpense)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,approved,rejected'
            ]);

            $routeExpense->update([
                'status' => $request->status,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Expense status updated successfully',
                'expense' => $routeExpense
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating expense: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(RouteExpense $routeExpense)
    {
        try {
            $routeExpense->delete();

            return response()->json([
                'success' => true,
                'message' => 'Expense deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting expense: ' . $e->getMessage()
            ], 500);
        }
    }
}
