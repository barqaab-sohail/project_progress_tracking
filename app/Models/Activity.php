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

    public function getScheduledProgress(Carbon $date)
    {
        $schedule = $this->scheduleProgress;
        if (!$schedule) return 0;

        $start = Carbon::parse($schedule->schedule_start_date);
        $end = Carbon::parse($schedule->schedule_completion_date);

        if ($date <= $start) return 0;
        if ($date >= $end) return 100;

        $totalDays = $start->diffInDays($end);
        $elapsedDays = $start->diffInDays($date);

        return min(100, ($elapsedDays / $totalDays) * 100);
    }

    public function getActualProgress(Carbon $date)
    {
        $latestProgress = $this->actualProgress()
            ->whereDate('progress_date', '<=', $date)
            ->orderBy('progress_date', 'desc')
            ->first();

        return $latestProgress ? $latestProgress->progress_percentage : 0;
    }
}
