<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }

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
        return $this->hasMany(BuildingScheduleProgress::class, 'activity_id');
    }

    public function actualProgress()
    {
        return $this->hasMany(BuildingActualProgress::class, 'activity_id');
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


    public function buildingScheduleProgress()
    {
        return $this->hasMany(BuildingScheduleProgress::class, 'activity_id');
    }

    public function buildingActualProgress()
    {
        return $this->hasMany(BuildingActualProgress::class, 'activity_id');
    }
}
