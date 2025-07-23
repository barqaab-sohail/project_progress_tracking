<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Building extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'building_no',
        'type',
        'location',
        'longitude',
        'latitude',
        'status',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'longitude' => 'float',
        'latitude' => 'float'
    ];

    // Relationships
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'building_activities')
            ->withPivot('weightage', 'sort_order', 'is_active')
            ->withTimestamps();
    }

    public function buildingActivities()
    {
        return $this->hasMany(BuildingActivity::class);
    }

    public function scheduleProgress()
    {
        return $this->hasMany(BuildingScheduleProgress::class);
    }

    public function actualProgress()
    {
        return $this->hasMany(BuildingActualProgress::class);
    }

    public function progressPhotos()
    {
        return $this->hasManyThrough(
            BuildingProgressPhoto::class,
            BuildingActualProgress::class,
            'building_id',
            'building_actual_progress_id'
        );
    }

    // public function aggregatedProgress()
    // {
    //     return $this->hasMany(BuildingProgressAggregated::class);
    // }

    public function aggregatedProgress()
    {
        return $this->hasMany('App\Models\BuildingProgressAggregated')
                    ->orderBy('progress_date');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Functions
    public function getTotalWeightage()
    {
        return $this->buildingActivities()->sum('weightage');
    }

    public function getCurrentProgress()
    {
        return $this->aggregatedProgress()
            ->orderByDesc('progress_date')
            ->first();
    }

    public function updateProgressAggregates($date = null)
    {
        $date = $date ?? now()->format('Y-m-d');

        // This would be called from the observer we created earlier
        // Implementation would be similar to the observer's updateAggregatedData method
    }
}
