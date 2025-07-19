<?php

namespace App\Observers;

use App\Models\BuildingActualProgress;
use App\Models\BuildingProgressAggregated;
use App\Models\BuildingScheduleProgress;

class ProgressObserver
{
    public function updated($progress)
    {
        $this->updateAggregatedData($progress->building_id, $progress->progress_date);
    }

    public function created($progress)
    {
        $this->updateAggregatedData($progress->building_id, $progress->progress_date);
    }

    public function deleted($progress)
    {
        $this->updateAggregatedData($progress->building_id, $progress->progress_date);
    }

    protected function updateAggregatedData($buildingId, $date)
    {
        // Calculate scheduled progress
        $scheduled = BuildingScheduleProgress::where('building_id', $buildingId)
            ->where('progress_date', $date)
            ->join('building_activities', 'building_schedule_progress.activity_id', '=', 'building_activities.activity_id')
            ->selectRaw('SUM(building_activities.weightage) as total_weightage,
                        SUM(building_activities.weightage * building_schedule_progress.progress_percentage / 100) as completed_weightage')
            ->first();

        // Calculate actual progress
        $actual = BuildingActualProgress::where('building_id', $buildingId)
            ->where('progress_date', $date)
            ->join('building_activities', 'building_actual_progress.activity_id', '=', 'building_activities.activity_id')
            ->selectRaw('SUM(building_activities.weightage) as total_weightage,
                        SUM(building_activities.weightage * building_actual_progress.progress_percentage / 100) as completed_weightage')
            ->first();

        // Update or create aggregated record
        BuildingProgressAggregated::updateOrCreate(
            ['building_id' => $buildingId, 'progress_date' => $date],
            [
                'scheduled_total_weightage' => $scheduled->total_weightage ?? 0,
                'scheduled_completed_weightage' => $scheduled->completed_weightage ?? 0,
                'scheduled_percentage' => $scheduled->total_weightage ? ($scheduled->completed_weightage / $scheduled->total_weightage * 100) : 0,
                'actual_total_weightage' => $actual->total_weightage ?? 0,
                'actual_completed_weightage' => $actual->completed_weightage ?? 0,
                'actual_percentage' => $actual->total_weightage ? ($actual->completed_weightage / $actual->total_weightage * 100) : 0,
                'variance' => ($actual->total_weightage ? ($actual->completed_weightage / $actual->total_weightage * 100) : 0) -
                    ($scheduled->total_weightage ? ($scheduled->completed_weightage / $scheduled->total_weightage * 100) : 0)
            ]
        );
    }
}
