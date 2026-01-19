<?php
// app/Http/Controllers/Api/AuthController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->user()->id,
        ]);

        $request->user()->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $request->user(),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $request->user()->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.'],
            ]);
        }

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
        ]);
    }
}

// app/Http/Controllers/Api/VehicleApiController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use App\Services\VinDecoderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VehicleApiController extends Controller
{
    protected $vinDecoder;

    public function __construct(VinDecoderService $vinDecoder)
    {
        $this->vinDecoder = $vinDecoder;
    }

    public function index(Request $request)
    {
        $vehicles = $request->user()
            ->vehicles()
            ->withCount(['maintenanceSchedules', 'serviceRecords', 'expenses'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $vehicles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vin' => 'required|string|size:17|unique:vehicles,vin',
            'license_plate' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:50',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'current_mileage' => 'nullable|integer|min:0',
            'is_primary' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $vinData = $this->vinDecoder->decode($validated['vin']);

            if ($request->is_primary) {
                $request->user()->vehicles()->update(['is_primary' => false]);
            }

            $vehicle = $request->user()->vehicles()->create(array_merge(
                $validated,
                [
                    'make' => $vinData['make'] ?? null,
                    'model' => $vinData['model'] ?? null,
                    'year' => $vinData['year'] ?? null,
                    'trim' => $vinData['trim'] ?? null,
                    'engine' => $vinData['engine'] ?? null,
                    'transmission' => $vinData['transmission'] ?? null,
                    'fuel_type' => $vinData['fuel_type'] ?? null,
                    'specifications' => $vinData['specifications'] ?? [],
                ]
            ));

            $this->createDefaultMaintenanceSchedules($vehicle);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vehicle added successfully',
                'data' => $vehicle->load(['maintenanceSchedules']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add vehicle: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, Vehicle $vehicle)
    {
        $this->authorize('view', $vehicle);

        $vehicle->load([
            'documents',
            'maintenanceSchedules',
            'serviceRecords.serviceProvider',
            'expenses'
        ]);

        $stats = [
            'total_expenses' => $vehicle->getTotalExpenses(),
            'this_month_expenses' => $vehicle->getTotalExpenses(
                now()->startOfMonth(),
                now()->endOfMonth()
            ),
            'total_services' => $vehicle->serviceRecords()->count(),
            'pending_maintenance' => $vehicle->maintenanceSchedules()
                ->whereIn('status', ['pending', 'overdue'])
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'vehicle' => $vehicle,
                'statistics' => $stats,
            ],
        ]);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $this->authorize('update', $vehicle);

        $validated = $request->validate([
            'license_plate' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:50',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'current_mileage' => 'nullable|integer|min:' . $vehicle->current_mileage,
            'status' => 'in:active,sold,inactive',
        ]);

        $vehicle->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vehicle updated successfully',
            'data' => $vehicle,
        ]);
    }

    public function destroy(Vehicle $vehicle)
    {
        $this->authorize('delete', $vehicle);
        $vehicle->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vehicle deleted successfully',
        ]);
    }

    public function setPrimary(Request $request, Vehicle $vehicle)
    {
        $this->authorize('update', $vehicle);

        DB::transaction(function () use ($request, $vehicle) {
            $request->user()->vehicles()->update(['is_primary' => false]);
            $vehicle->update(['is_primary' => true]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Primary vehicle updated',
        ]);
    }

    public function updateMileage(Request $request, Vehicle $vehicle)
    {
        $this->authorize('update', $vehicle);

        $validated = $request->validate([
            'mileage' => 'required|integer|min:' . $vehicle->current_mileage,
        ]);

        $vehicle->updateMileage($validated['mileage']);

        return response()->json([
            'success' => true,
            'message' => 'Mileage updated successfully',
            'data' => $vehicle,
        ]);
    }

    public function decodeVin(string $vin)
    {
        if (strlen($vin) !== 17) {
            return response()->json([
                'success' => false,
                'message' => 'VIN must be 17 characters',
            ], 400);
        }

        try {
            $data = $this->vinDecoder->decode($vin);
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to decode VIN: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function statistics(Vehicle $vehicle)
    {
        $this->authorize('view', $vehicle);

        $stats = [
            'total_expenses' => $vehicle->getTotalExpenses(),
            'expenses_by_category' => $vehicle->expenses()
                ->selectRaw('category, SUM(amount) as total')
                ->groupBy('category')
                ->get(),
            'monthly_expenses' => $vehicle->expenses()
                ->whereBetween('expense_date', [
                    now()->subMonths(6),
                    now()
                ])
                ->selectRaw('DATE_FORMAT(expense_date, "%Y-%m") as month, SUM(amount) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
            'maintenance_count' => $vehicle->maintenanceSchedules()->count(),
            'service_count' => $vehicle->serviceRecords()->count(),
            'upcoming_maintenance' => $vehicle->getUpcomingMaintenance(),
            'expiring_documents' => $vehicle->getExpiringDocuments(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    public function documents(Vehicle $vehicle)
    {
        $this->authorize('view', $vehicle);

        return response()->json([
            'success' => true,
            'data' => $vehicle->documents,
        ]);
    }

    public function storeDocument(Request $request, Vehicle $vehicle)
    {
        $this->authorize('update', $vehicle);

        $validated = $request->validate([
            'type' => 'required|in:registration,insurance,warranty,inspection,other',
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'notes' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $path = $file->store('documents/' . $vehicle->id, 'public');

        $document = $vehicle->documents()->create([
            'type' => $validated['type'],
            'title' => $validated['title'],
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'issue_date' => $validated['issue_date'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully',
            'data' => $document,
        ], 201);
    }

    protected function createDefaultMaintenanceSchedules(Vehicle $vehicle)
    {
        $schedules = [
            [
                'service_type' => 'Oil Change',
                'description' => 'Regular oil and filter change',
                'due_mileage' => $vehicle->current_mileage + 5000,
                'priority' => 'high',
                'is_recurring' => true,
                'recurrence_mileage' => 5000,
            ],
            [
                'service_type' => 'Tire Rotation',
                'description' => 'Rotate tires for even wear',
                'due_mileage' => $vehicle->current_mileage + 7500,
                'priority' => 'medium',
                'is_recurring' => true,
                'recurrence_mileage' => 7500,
            ],
        ];

        foreach ($schedules as $schedule) {
            $vehicle->maintenanceSchedules()->create($schedule);
        }
    }
}