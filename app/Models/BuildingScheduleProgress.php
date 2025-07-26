<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BuildingScheduleProgress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'building_id',
        'activity_id',
        'progress_percentage',
        'schedule_start_date',
        'schedule_completion_date',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'progress_percentage' => 'float',
        'schedule_start_date' => 'date',
        'schedule_completion_date' => 'date'
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
    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function buildings()
    {
        return $this->hasMany(Building::class, 'id', 'building_id');
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function buildingActivity()
    {
        return $this->belongsTo(BuildingActivity::class, 'activity_id', 'activity_id')
            ->where('building_id', $this->building_id);
    }

    public function buildingActivities()
    {
        return $this->hasMany(BuildingActivity::class, 'building_id', 'building_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeByBuilding($query, $buildingId)
    {
        return $query->where('building_id', $buildingId);
    }

    public function scopeByActivity($query, $activityId)
    {
        return $query->where('activity_id', $activityId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('progress_date', $date);
    }

    // Functions
    public function getWeightedProgress()
    {
        if ($this->buildingActivity) {
            return ($this->progress_percentage / 100) * $this->buildingActivity->weightage;
        }
        return 0;
    }
}
