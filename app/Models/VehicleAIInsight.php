<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleAIInsight extends Model
{

        protected $table = 'vehicle_ai_insights';

        protected $fillable = [
        'vehicle_id',
        'summary',
        'known_issues',
        'maintenance_tips',
        'owner_tips',
        'cost_expectations',
        'peace_of_mind_score',
        'generated_at',
        'mileage_at_generation'
    ];

    protected $casts = [
        'known_issues' => 'array',
        'maintenance_tips' => 'array',
        'owner_tips' => 'array',
        'cost_expectations' => 'array',
        'generated_at' => 'datetime'
    ];
}
