<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExpenseController extends Controller
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
            ->paginate(20);

        $vehicles = $request->user()->vehicles;
        
        $categories = [
            'fuel' => 'Fuel',
            'maintenance' => 'Maintenance',
            'insurance' => 'Insurance',
            'registration' => 'Registration',
            'parking' => 'Parking',
            'toll' => 'Toll',
            'loan' => 'Loan Payment',
            'other' => 'Other',
        ];

        // Calculate totals
        $totalExpenses = Expense::whereHas('vehicle', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->sum('amount');

        $monthExpenses = Expense::whereHas('vehicle', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })
        ->whereMonth('expense_date', Carbon::now()->month)
        ->whereYear('expense_date', Carbon::now()->year)
        ->sum('amount');

        return view('expenses.index', compact('expenses', 'vehicles', 'categories', 'totalExpenses', 'monthExpenses'));
    }

    public function create()
    {
        $vehicles = auth()->user()->vehicles()->active()->get();
        
        $categories = [
            'fuel' => 'Fuel',
            'maintenance' => 'Maintenance',
            'insurance' => 'Insurance',
            'registration' => 'Registration',
            'parking' => 'Parking',
            'toll' => 'Toll',
            'loan' => 'Loan Payment',
            'other' => 'Other',
        ];

        return view('expenses.create', compact('vehicles', 'categories'));
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
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string',
        ]);

        // Verify vehicle belongs to user
        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($request->hasFile('receipt_file')) {
            $validated['receipt_file'] = $request->file('receipt_file')->store('receipts/' . $vehicle->id, 'public');
        }

        $expense = Expense::create($validated);

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense added successfully!');
    }

    public function show(Expense $expense)
    {
        $this->authorize('view', $expense->vehicle);

        $expense->load('vehicle');

        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $this->authorize('update', $expense->vehicle);

        $vehicles = auth()->user()->vehicles()->active()->get();
        
        $categories = [
            'fuel' => 'Fuel',
            'maintenance' => 'Maintenance',
            'insurance' => 'Insurance',
            'registration' => 'Registration',
            'parking' => 'Parking',
            'toll' => 'Toll',
            'loan' => 'Loan Payment',
            'other' => 'Other',
        ];

        return view('expenses.edit', compact('expense', 'vehicles', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorize('update', $expense->vehicle);

        $validated = $request->validate([
            'category' => 'required|in:fuel,maintenance,insurance,registration,parking,toll,loan,other',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'odometer_reading' => 'nullable|integer|min:0',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('receipt_file')) {
            $validated['receipt_file'] = $request->file('receipt_file')->store('receipts/' . $expense->vehicle_id, 'public');
        }

        $expense->update($validated);

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense updated successfully!');
    }

    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense->vehicle);

        $expense->delete();

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense deleted successfully!');
    }

    public function byCategory(Request $request, $category)
    {
        $expenses = Expense::whereHas('vehicle', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->where('category', $category)
            ->with('vehicle')
            ->latest('expense_date')
            ->paginate(20);

        $vehicles = $request->user()->vehicles;

        return view('expenses.by-category', compact('expenses', 'category', 'vehicles'));
    }
}

