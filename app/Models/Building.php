<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    // Relationships


    public function buildingActivities()
    {
        return $this->hasMany(BuildingActivity::class);
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



    public function scheduleProgress()
    {
        return $this->hasMany(BuildingScheduleProgress::class);
    }

    public function actualProgress()
    {
        return $this->hasMany(BuildingActualProgress::class);
    }
}
