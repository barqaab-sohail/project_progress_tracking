<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingProgressPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_actual_progress_id',
        'photo_path',
        'caption',
        'created_by'
    ];

    // Relationships
    public function progress()
    {
        return $this->belongsTo(BuildingActualProgress::class, 'building_actual_progress_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeByProgress($query, $progressId)
    {
        return $query->where('building_actual_progress_id', $progressId);
    }

    // Functions
    public function getPhotoUrl()
    {
        return asset('storage/' . $this->photo_path);
    }
}
