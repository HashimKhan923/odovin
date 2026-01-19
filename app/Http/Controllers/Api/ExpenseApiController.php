<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Vehicle;
use App\Http\Resources\ExpenseResource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExpenseApiController extends Controller
{
    public function index(Request $request)
    {
        $expenses = Expense::whereHas('vehicle', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with('vehicle')
            ->when($request->vehicle_id, function ($query, $vehicleId) {
                return $query->where('vehicle_id', $vehicleId);
            })
            ->when($request->category, function ($query, $category) {
                return $query->where('category', $category);
            })
            ->when($request->start_date, function ($query, $date) {
                return $query->where('expense_date', '>=', $date);
            })
            ->when($request->end_date, function ($query, $date) {
                return $query->where('expense_date', '<=', $date);
            })
            ->latest('expense_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ExpenseResource::collection($expenses),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'category' => 'required|in:fuel,maintenance,insurance,registration,parking,toll,loan,other',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'odometer_reading' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $expense = Expense::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Expense created successfully',
            'data' => new ExpenseResource($expense->load('vehicle')),
        ], 201);
    }

    public function show(Expense $expense)
    {
        $this->authorize('view', $expense->vehicle);

        return response()->json([
            'success' => true,
            'data' => new ExpenseResource($expense->load('vehicle')),
        ]);
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorize('update', $expense->vehicle);

        $validated = $request->validate([
            'category' => 'in:fuel,maintenance,insurance,registration,parking,toll,loan,other',
            'description' => 'string|max:255',
            'amount' => 'numeric|min:0',
            'expense_date' => 'date',
            'odometer_reading' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $expense->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully',
            'data' => new ExpenseResource($expense),
        ]);
    }

    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense->vehicle);

        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully',
        ]);
    }

    public function byCategory(Request $request)
    {
        $expenses = Expense::whereHas('vehicle', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->when($request->vehicle_id, function ($query, $vehicleId) {
                return $query->where('vehicle_id', $vehicleId);
            })
            ->when($request->start_date, function ($query, $date) {
                return $query->where('expense_date', '>=', $date);
            })
            ->when($request->end_date, function ($query, $date) {
                return $query->where('expense_date', '<=', $date);
            })
            ->selectRaw('category, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $expenses,
        ]);
    }

    public function byVehicle(Request $request)
    {
        $expenses = Expense::whereHas('vehicle', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with('vehicle:id,make,model,year')
            ->when($request->start_date, function ($query, $date) {
                return $query->where('expense_date', '>=', $date);
            })
            ->when($request->end_date, function ($query, $date) {
                return $query->where('expense_date', '<=', $date);
            })
            ->selectRaw('vehicle_id, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('vehicle_id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $expenses,
        ]);
    }

    public function byMonth(Request $request)
    {
        $expenses = Expense::whereHas('vehicle', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->when($request->vehicle_id, function ($query, $vehicleId) {
                return $query->where('vehicle_id', $vehicleId);
            })
            ->whereBetween('expense_date', [
                $request->start_date ?? Carbon::now()->subMonths(12),
                $request->end_date ?? Carbon::now()
            ])
            ->selectRaw('DATE_FORMAT(expense_date, "%Y-%m") as month, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $expenses,
        ]);
    }

    public function expenseSummary(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->subMonths(6);
        $endDate = $request->end_date ?? Carbon::now();

        $expenses = Expense::whereHas('vehicle', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->when($request->vehicle_id, function ($query, $vehicleId) {
                return $query->where('vehicle_id', $vehicleId);
            })
            ->get();

        $totalExpenses = $expenses->sum('amount');
        $byCategory = $expenses->groupBy('category')->map(function ($items) {
            return [
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ];
        });

        $byMonth = $expenses->groupBy(function ($expense) {
            return $expense->expense_date->format('Y-m');
        })->map(function ($items) {
            return [
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'total_expenses' => $totalExpenses,
                'expense_count' => $expenses->count(),
                'average_expense' => $expenses->count() > 0 ? $totalExpenses / $expenses->count() : 0,
                'by_category' => $byCategory,
                'by_month' => $byMonth,
                'date_range' => [
                    'start' => $startDate,
                    'end' => $endDate,
                ],
            ],
        ]);
    }
}