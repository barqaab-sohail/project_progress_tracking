<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\BuildingActivity;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingScheduleProgressSeeder extends Seeder
{
    private $chunkSize = 500; // Process buildings in chunks
    private $batchSize = 1000; // Insert records in batches

    public function run()
    {
        $startDate = Carbon::now()->startOfWeek();
        $totalBuildings = Building::count();

        // Process in chunks for memory efficiency
        for ($offset = 0; $offset < $totalBuildings; $offset += $this->chunkSize) {
            $buildings = Building::skip($offset)
                ->take($this->chunkSize)
                ->get();

            $this->processBuildings($buildings, $startDate);
        }
    }

    protected function processBuildings($buildings, $startDate)
    {
        $allProgressData = [];

        foreach ($buildings as $building) {
            $buildingActivities = BuildingActivity::where('building_id', $building->id)
                ->with('activity')
                ->get();

            foreach ($buildingActivities as $buildingActivity) {
                $progressData = $this->generateActivityProgress(
                    $building,
                    $buildingActivity,
                    $startDate
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
    }

    protected function generateActivityProgress($building, $buildingActivity, $startDate)
    {
        $progressData = [];
        $weightage = $buildingActivity->weightage;
        $activityDurationWeeks = rand(4, 52);
        $progressIncrement = $weightage / $activityDurationWeeks;
        $currentProgress = 0;
        $currentDate = $startDate->copy();

        for ($week = 1; $week <= $activityDurationWeeks; $week++) {
            $progress = ($week === $activityDurationWeeks)
                ? $weightage
                : min($currentProgress + $progressIncrement, $weightage);

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

        return $progressData;
    }

    protected function insertBatch($data)
    {
        DB::table('building_schedule_progress')->insert($data);
    }
}
