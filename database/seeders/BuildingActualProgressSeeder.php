<?php

namespace Database\Seeders;

use App\Models\BuildingActivity;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingActualProgressSeeder extends Seeder
{
    private $chunkSize = 500; // Process building activities in chunks
    private $batchSize = 1000; // Insert records in batches
    private $minWeeks = 3;    // Minimum weeks of progress to generate
    private $maxWeeks = 6;    // Maximum weeks of progress to generate
    private $startDate;       // Project start date

    public function __construct()
    {
        $this->startDate = Carbon::now()->subMonths(6); // Project started 6 months ago
    }

    public function run()
    {
        $totalActivities = BuildingActivity::count();

        // Process in chunks for memory efficiency
        for ($offset = 0; $offset < $totalActivities; $offset += $this->chunkSize) {
            $buildingActivities = BuildingActivity::with(['building', 'activity'])
                ->skip($offset)
                ->take($this->chunkSize)
                ->get();

            $this->processActivities($buildingActivities);
        }
    }

    protected function processActivities($buildingActivities)
    {
        $allProgressData = [];

        foreach ($buildingActivities as $buildingActivity) {
            $weeks = rand($this->minWeeks, $this->maxWeeks);
            $totalWeightage = $buildingActivity->weightage;
            $weeklyIncrement = $totalWeightage / ($weeks + 1); // +1 to prevent 100% completion

            // Random start date within project duration (last 6 months)
            $currentDate = $this->startDate->copy()->addDays(rand(0, 180));
            $currentProgress = 0;

            for ($week = 1; $week <= $weeks; $week++) {
                $currentProgress = min($currentProgress + $weeklyIncrement, $totalWeightage * 0.95); // Cap at 95%

                $allProgressData[] = [
                    'building_id' => $buildingActivity->building_id,
                    'activity_id' => $buildingActivity->activity_id,
                    'progress_percentage' => $currentProgress,
                    'progress_date' => $currentDate->format('Y-m-d'),
                    'notes' => $this->generateProgressNote($week, $weeks, $currentProgress, $totalWeightage),
                    'created_at' => now(),
                    'updated_at' => now(),
                    'created_by' => 1,
                    'updated_by' => 1,
                ];

                $currentDate->addWeek();

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

    protected function generateProgressNote($currentWeek, $totalWeeks, $currentProgress, $totalWeightage)
    {
        $phrases = [
            "Week {$currentWeek} progress: {$currentProgress}% of {$totalWeightage} weightage",
            "Phase {$currentWeek} completed",
            "Achieved {$currentProgress}% progress this week",
            "Construction update: {$currentProgress}% done",
            "Work ongoing - {$currentProgress}% complete",
            "Quality inspection passed for this phase",
            "Materials installed for this stage",
            "Crew completed weekly targets",
            "Partial completion recorded",
            "Progress meeting notes updated"
        ];

        return $phrases[array_rand($phrases)];
    }

    protected function insertBatch($data)
    {
        DB::table('building_actual_progress')->insert($data);
    }
}
