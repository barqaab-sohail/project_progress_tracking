<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\BuildingProgressAggregated;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingProgressAggregatedSeeder extends Seeder
{
    private $batchSize = 500; // Process buildings in batches

    public function run()
    {
        // Disable foreign key checks for faster inserts
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        BuildingProgressAggregated::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Building::chunk($this->batchSize, function ($buildings) {
            $aggregatedData = [];
            
            foreach ($buildings as $building) {
                // Get all progress dates for this building in one query
                $dates = DB::table('building_actual_progress')
                    ->where('building_id', $building->id)
                    ->select('progress_date as date')
                    ->union(
                        DB::table('building_schedule_progress')
                            ->where('building_id', $building->id)
                            ->select('schedule_completion_date as date')
                    )
                    ->distinct()
                    ->orderBy('date')
                    ->pluck('date');

                // Preload all relevant data for this building
                $buildingActivities = DB::table('building_activities')
                    ->where('building_id', $building->id)
                    ->get()
                    ->keyBy('activity_id');

                $totalWeightage = $buildingActivities->sum('weightage');

                foreach ($dates as $date) {
                    $aggregatedData[] = $this->calculateAggregatedProgress(
                        $building->id,
                        $date,
                        $buildingActivities,
                        $totalWeightage
                    );

                    // Insert in batches
                    if (count($aggregatedData) >= $this->batchSize) {
                        BuildingProgressAggregated::insert($aggregatedData);
                        $aggregatedData = [];
                    }
                }
            }

            // Insert remaining records
            if (!empty($aggregatedData)) {
                BuildingProgressAggregated::insert($aggregatedData);
            }
        });
    }

    protected function calculateAggregatedProgress($buildingId, $date, $buildingActivities, $totalWeightage)
    {
        // Calculate scheduled progress (single query)
        $scheduledCompleted = DB::table('building_schedule_progress')
            ->where('building_id', $buildingId)
            ->where('schedule_completion_date', '<=', $date)
            ->whereIn('activity_id', $buildingActivities->keys())
            ->count();

        $scheduledWeightage = $buildingActivities->slice(0, $scheduledCompleted)
            ->sum('weightage');

        // Calculate actual progress (single query)
        $actualProgress = DB::table('building_actual_progress')
            ->select('activity_id', DB::raw('MAX(progress_percentage) as progress'))
            ->where('building_id', $buildingId)
            ->where('progress_date', '<=', $date)
            ->groupBy('activity_id')
            ->get();

        $actualWeightage = $actualProgress->sum(function ($item) use ($buildingActivities) {
            return ($item->progress / 100) * ($buildingActivities[$item->activity_id]->weightage ?? 0);
        });

        return [
            'building_id' => $buildingId,
            'progress_date' => $date,
            'scheduled_total_weightage' => $totalWeightage,
            'scheduled_completed_weightage' => $scheduledWeightage,
            'scheduled_percentage' => $totalWeightage > 0 ? ($scheduledWeightage / $totalWeightage) * 100 : 0,
            'actual_total_weightage' => $totalWeightage,
            'actual_completed_weightage' => $actualWeightage,
            'actual_percentage' => $totalWeightage > 0 ? ($actualWeightage / $totalWeightage) * 100 : 0,
            'variance' => ($totalWeightage > 0 ? ($actualWeightage / $totalWeightage) * 100 : 0) - 
                          ($totalWeightage > 0 ? ($scheduledWeightage / $totalWeightage) * 100 : 0),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}