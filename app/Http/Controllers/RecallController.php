<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Services\NhtsaRecallService;
use Illuminate\Http\Request;

class RecallController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = $request->user()
            ->vehicles()
            ->active()
            ->withCount([
                'recalls as open_recalls_count' => fn ($q) => $q->where('is_open', true)
            ])
            ->get();

        return view('recalls.index', compact('vehicles'));
    }

    public function check(Vehicle $vehicle)
    {
        
        // $this->authorize('view', $vehicle);

        app(NhtsaRecallService::class)->syncByVin($vehicle);

        $recalls = $vehicle->recalls()->latest()->get();

        return view('recalls.check', compact('vehicle', 'recalls'));
    }

    public function sync(Vehicle $vehicle)
    {
        // $this->authorize('view', $vehicle);

        $count = app(NhtsaRecallService::class)->syncByVin($vehicle);

        return back()->with(
            'success',
            $count ? "{$count} new recall(s) found." : 'No new recalls found.'
        );
    }
}
