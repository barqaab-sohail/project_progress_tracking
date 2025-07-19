<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'description',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Functions
    public function getTotalWeightage($buildingId)
    {
        return $this->buildingActivities()
            ->where('building_id', $buildingId)
            ->sum('weightage');
    }
}
