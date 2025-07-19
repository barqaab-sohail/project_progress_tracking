<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingActualProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'activity_id',
        'progress_percentage',
        'progress_date',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'progress_percentage' => 'float',
        'progress_date' => 'date'
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

    public function buildingActivity()
    {
        return $this->belongsTo(BuildingActivity::class, 'activity_id', 'activity_id')
            ->where('building_id', $this->building_id);
    }

    public function photos()
    {
        return $this->hasMany(BuildingProgressPhoto::class, 'building_actual_progress_id');
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

    public function scopeWithPhotos($query)
    {
        return $query->with('photos');
    }

    // Functions
    public function getWeightedProgress()
    {
        if ($this->buildingActivity) {
            return ($this->progress_percentage / 100) * $this->buildingActivity->weightage;
        }
        return 0;
    }

    public function addPhoto($path, $caption = null, $userId)
    {
        return $this->photos()->create([
            'photo_path' => $path,
            'caption' => $caption,
            'created_by' => $userId
        ]);
    }
}
