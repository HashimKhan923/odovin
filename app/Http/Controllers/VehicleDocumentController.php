<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleDocumentController extends Controller
{
    public function index(Vehicle $vehicle)
    {
        // $this->authorize('view', $vehicle);

        $vehicle->load('documents');

        return view('vehicles.documents.index', compact('vehicle'));
    }

    public function create(Vehicle $vehicle)
    {
        // $this->authorize('update', $vehicle);

        $documentTypes = [
            'registration' => 'Registration',
            'insurance' => 'Insurance',
            'warranty' => 'Warranty',
            'inspection' => 'Inspection',
            'other' => 'Other',
        ];

        return view('vehicles.documents.create', compact('vehicle', 'documentTypes'));
    }

    public function store(Request $request, Vehicle $vehicle)
    {
        // $this->authorize('update', $vehicle);

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

        // Create reminder for expiring documents
        if ($document->expiry_date) {
            $vehicle->reminders()->create([
                'type' => $validated['type'],
                'title' => $validated['title'] . ' Expiration',
                'description' => 'Your ' . $validated['title'] . ' will expire soon',
                'due_date' => $document->expiry_date,
                'reminder_date' => $document->expiry_date->subDays(30),
                'priority' => 'high',
            ]);
        }

        return redirect()
            ->route('vehicles.documents.index', $vehicle)
            ->with('success', 'Document uploaded successfully!');
    }

    public function show(Vehicle $vehicle, VehicleDocument $document)
    {
        // $this->authorize('view', $vehicle);

        return view('vehicles.documents.show', compact('vehicle', 'document'));
    }

    public function edit(Vehicle $vehicle, VehicleDocument $document)
    {
        // $this->authorize('update', $vehicle);

        $documentTypes = [
            'registration' => 'Registration',
            'insurance' => 'Insurance',
            'warranty' => 'Warranty',
            'inspection' => 'Inspection',
            'other' => 'Other',
        ];

        return view('vehicles.documents.edit', compact('vehicle', 'document', 'documentTypes'));
    }

    public function update(Request $request, Vehicle $vehicle, VehicleDocument $document)
    {
        // $this->authorize('update', $vehicle);

        $validated = $request->validate([
            'type' => 'required|in:registration,insurance,warranty,inspection,other',
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            // Delete old file
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $validated['file_path'] = $file->store('documents/' . $vehicle->id, 'public');
            $validated['file_type'] = $file->getClientMimeType();
        }

        $document->update($validated);

        return redirect()
            ->route('vehicles.documents.index', $vehicle)
            ->with('success', 'Document updated successfully!');
    }

    public function destroy(Vehicle $vehicle, VehicleDocument $document)
    {
        $this->authorize('delete', $vehicle);

        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()
            ->route('vehicles.documents.index', $vehicle)
            ->with('success', 'Document deleted successfully!');
    }

    public function download(Vehicle $vehicle, VehicleDocument $document)
    {
        // $this->authorize('view', $vehicle);

        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File not found.');
        }

        return Storage::disk('public')->download($document->file_path, $document->title);
    }
}