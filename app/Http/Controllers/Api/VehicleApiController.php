<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use App\Services\VinDecoderService;
use App\Http\Resources\VehicleResource;
use App\Http\Resources\VehicleDocumentResource;
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
            'data' => VehicleResource::collection($vehicles),
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
                'data' => new VehicleResource($vehicle->load(['maintenanceSchedules'])),
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
                'vehicle' => new VehicleResource($vehicle),
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
            'data' => new VehicleResource($vehicle),
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
            'data' => new VehicleResource($vehicle),
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
            'data' => VehicleDocumentResource::collection($vehicle->documents),
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
            'data' => new VehicleDocumentResource($document),
        ], 201);
    }

    public function showDocument(Vehicle $vehicle, VehicleDocument $document)
    {
        $this->authorize('view', $vehicle);

        return response()->json([
            'success' => true,
            'data' => new VehicleDocumentResource($document),
        ]);
    }

    public function updateDocument(Request $request, Vehicle $vehicle, VehicleDocument $document)
    {
        $this->authorize('update', $vehicle);

        $validated = $request->validate([
            'type' => 'in:registration,insurance,warranty,inspection,other',
            'title' => 'string|max:255',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }
            $file = $request->file('file');
            $validated['file_path'] = $file->store('documents/' . $vehicle->id, 'public');
            $validated['file_type'] = $file->getClientMimeType();
        }

        $document->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Document updated successfully',
            'data' => new VehicleDocumentResource($document),
        ]);
    }

    public function deleteDocument(Vehicle $vehicle, VehicleDocument $document)
    {
        $this->authorize('delete', $vehicle);

        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully',
        ]);
    }

    public function analytics(Vehicle $vehicle)
    {
        $this->authorize('view', $vehicle);

        $totalExpenses = $vehicle->getTotalExpenses();
        $expensesByCategory = $vehicle->expenses()
            ->selectRaw('category, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category')
            ->get();

        $monthlyExpenses = $vehicle->expenses()
            ->whereBetween('expense_date', [
                now()->subMonths(12),
                now()
            ])
            ->selectRaw('DATE_FORMAT(expense_date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_expenses' => $totalExpenses,
                'expenses_by_category' => $expensesByCategory,
                'monthly_expenses' => $monthlyExpenses,
                'maintenance_count' => $vehicle->maintenanceSchedules()->count(),
                'service_count' => $vehicle->serviceRecords()->count(),
                'average_monthly_cost' => $monthlyExpenses->avg('total') ?? 0,
                'cost_per_mile' => $vehicle->current_mileage > 0 ? $totalExpenses / $vehicle->current_mileage : 0,
            ],
        ]);
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
            [
                'service_type' => 'Air Filter Replacement',
                'description' => 'Replace engine air filter',
                'due_mileage' => $vehicle->current_mileage + 15000,
                'priority' => 'medium',
                'is_recurring' => true,
                'recurrence_mileage' => 15000,
            ],
            [
                'service_type' => 'Brake Inspection',
                'description' => 'Inspect brake pads and rotors',
                'due_mileage' => $vehicle->current_mileage + 20000,
                'priority' => 'high',
                'is_recurring' => true,
                'recurrence_mileage' => 20000,
            ],
        ];

        foreach ($schedules as $schedule) {
            $vehicle->maintenanceSchedules()->create($schedule);
        }
    }
}