<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BuildingActivity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'building_id',
        'activity_id',
        'weightage',
        'sort_order',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function scheduleProgress()
    {
        return $this->hasMany(BuildingScheduleProgress::class, 'activity_id', 'activity_id')
            ->where('building_id', $this->building_id);
    }

    public function actualProgress()
    {
        return $this->hasMany(BuildingActualProgress::class, 'activity_id', 'activity_id')
            ->where('building_id', $this->building_id);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByBuilding($query, $buildingId)
    {
        return $query->where('building_id', $buildingId);
    }

    // Functions
    public function getLatestProgress()
    {
        return $this->actualProgress()
            ->orderByDesc('progress_date')
            ->first();
    }

    public function getScheduledProgress($date)
    {
        return $this->scheduleProgress()
            ->where('progress_date', $date)
            ->first();
    }
}
