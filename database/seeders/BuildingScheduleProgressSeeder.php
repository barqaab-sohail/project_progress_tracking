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
    private $projectDurationMonths = 12; // Total project duration in months

    public function run()
    {
        $totalBuildings = Building::count();

        // Process in chunks for memory efficiency
        for ($offset = 0; $offset < $totalBuildings; $offset += $this->chunkSize) {
            $buildings = Building::skip($offset)
                ->take($this->chunkSize)
                ->get();

            $this->processBuildings($buildings);
        }
    }

    protected function processBuildings($buildings)
    {
        $allScheduleData = [];

        foreach ($buildings as $building) {
            $buildingActivities = BuildingActivity::where('building_id', $building->id)
                ->with('activity')
                ->orderBy('sort_order') // Ensure activities are processed in order
                ->get();

            $totalActivities = count($buildingActivities);
            $currentActivity = 0;

            foreach ($buildingActivities as $buildingActivity) {
                $currentActivity++;
                // Distribute activities evenly over the 12-month period
                $monthsToAdd = (int) round(($this->projectDurationMonths * $currentActivity) / $totalActivities);
                $completionDate = Carbon::now()->addMonths($monthsToAdd);

                $scheduleData = [
                    'building_id' => $building->id,
                    'activity_id' => $buildingActivity->activity_id,
                    'schedule_completion_date' => $completionDate->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                    'created_by' => 1,
                    'updated_by' => 1,
                ];

                $allScheduleData[] = $scheduleData;

                // Insert in batches
                if (count($allScheduleData) >= $this->batchSize) {
                    $this->insertBatch($allScheduleData);
                    $allScheduleData = [];
                }
            }
        }

        // Insert remaining records
        if (!empty($allScheduleData)) {
            $this->insertBatch($allScheduleData);
        }
    }

    protected function insertBatch($data)
    {
        DB::table('building_schedule_progress')->insert($data);
    }
}
