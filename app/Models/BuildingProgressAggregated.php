<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingProgressAggregated extends Model
{
    use HasFactory;

    protected $table = 'building_progress_aggregated';

    protected $fillable = [
        'building_id',
        'progress_date',
        'scheduled_total_weightage',
        'scheduled_completed_weightage',
        'scheduled_percentage',
        'actual_total_weightage',
        'actual_completed_weightage',
        'actual_percentage',
        'variance'
    ];

    protected $casts = [
        'progress_date' => 'date',
        'scheduled_total_weightage' => 'float',
        'scheduled_completed_weightage' => 'float',
        'scheduled_percentage' => 'float',
        'actual_total_weightage' => 'float',
        'actual_completed_weightage' => 'float',
        'actual_percentage' => 'float',
        'variance' => 'float'
    ];

    // Relationships
    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    // Scopes
    public function scopeByBuilding($query, $buildingId)
    {
        return $query->where('building_id', $buildingId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('progress_date', $date);
    }

    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('progress_date');
    }

    // Functions
    public function isOnSchedule()
    {
        return $this->variance >= 0;
    }

    public function getProgressStatus()
    {
        if ($this->variance >= 5) {
            return 'ahead';
        } elseif ($this->variance <= -5) {
            return 'behind';
        } else {
            return 'on_track';
        }
    }
}
