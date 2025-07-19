<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\BuildingActivity;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingActualProgressSeeder extends Seeder
{
    private $chunkSize = 200; // Process buildings in smaller chunks
    private $batchSize = 1000; // Insert records in batches
    private $maxMonths = 3; // Data for last 3 months only
    private $targetProgressRange = [15, 20]; // 15-20% total progress

    public function run()
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subMonths($this->maxMonths)->startOfWeek();

        Building::chunk($this->chunkSize, function ($buildings) use ($startDate, $endDate) {
            $allProgressData = [];

            foreach ($buildings as $building) {
                $buildingActivities = BuildingActivity::where('building_id', $building->id)
                    ->with('activity')
                    ->get();

                // Determine building's actual progress percentage (15-20%)
                $buildingTotalProgress = rand(
                    $this->targetProgressRange[0] * 100,
                    $this->targetProgressRange[1] * 100
                ) / 100;

                foreach ($buildingActivities as $buildingActivity) {
                    $progressData = $this->generateActivityProgress(
                        $building,
                        $buildingActivity,
                        $startDate,
                        $endDate,
                        $buildingTotalProgress
                    );
                    $allProgressData = array_merge($allProgressData, $progressData);

                    // Insert in batches
                    if (count($allProgressData) >= $this->batchSize) {
                        $this->insertBatch($allProgressData);
                        $allProgressData = [];
                    }
                }
            }

            // Insert remaining records
            if (!empty($allProgressData)) {
                $this->insertBatch($allProgressData);
            }
        });
    }

    protected function generateActivityProgress($building, $buildingActivity, $startDate, $endDate, $buildingTotalProgress)
    {
        $progressData = [];
        $weightage = $buildingActivity->weightage;

        // Calculate target progress for this activity
        $activityTargetProgress = ($weightage / 100) * $buildingTotalProgress;

        // Get scheduled progress dates for this activity
        $scheduledWeeks = DB::table('building_schedule_progress')
            ->where('building_id', $building->id)
            ->where('activity_id', $buildingActivity->activity_id)
            ->whereBetween('progress_date', [$startDate, $endDate])
            ->orderBy('progress_date')
            ->pluck('progress_date')
            ->unique();

        if ($scheduledWeeks->isEmpty()) {
            // If no scheduled weeks, create some realistic progress
            $weeksCount = min(12, rand(4, 12)); // 4-12 weeks of progress
            $progressPerWeek = $activityTargetProgress / $weeksCount;

            $currentDate = $startDate->copy();
            $currentProgress = 0;

            for ($week = 1; $week <= $weeksCount; $week++) {
                $progress = ($week === $weeksCount)
                    ? $activityTargetProgress
                    : min($currentProgress + $progressPerWeek, $activityTargetProgress);

                $progressData[] = [
                    'building_id' => $building->id,
                    'activity_id' => $buildingActivity->activity_id,
                    'progress_percentage' => $progress,
                    'progress_date' => $currentDate->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                    'created_by' => 1,
                    'updated_by' => 1,
                ];

                $currentProgress = $progress;
                $currentDate->addWeek();
            }
        } else {
            // Follow scheduled weeks but with actual progress
            $weeksCount = $scheduledWeeks->count();
            $progressPerWeek = $activityTargetProgress / $weeksCount;
            $currentProgress = 0;

            foreach ($scheduledWeeks as $weekDate) {
                $progress = ($weekDate === $scheduledWeeks->last())
                    ? $activityTargetProgress
                    : min($currentProgress + $progressPerWeek, $activityTargetProgress);

                $progressData[] = [
                    'building_id' => $building->id,
                    'activity_id' => $buildingActivity->activity_id,
                    'progress_percentage' => $progress,
                    'progress_date' => $weekDate,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'created_by' => 1,
                    'updated_by' => 1,
                ];

                $currentProgress = $progress;
            }
        }

        return $progressData;
    }

    protected function insertBatch($data)
    {
        DB::table('building_actual_progress')->insert($data);
    }
}
