<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BuildingScheduleProgress;
use App\Models\BuildingActualProgress;
use Carbon\Carbon;

class BuildingActualProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all scheduled activities
        $scheduledActivities = BuildingScheduleProgress::all();

        foreach ($scheduledActivities as $schedule) {
            $startDate = Carbon::parse($schedule->schedule_start_date);
            $endDate = Carbon::parse($schedule->schedule_completion_date);
            $daysDuration = $startDate->diffInDays($endDate);

            // Generate 10 progress points for each activity
            $progressPoints = 10;
            $progressIncrement = (100 - rand(40, 50)) / $progressPoints; // Start between 50-60% and go to 100%

            $currentProgress = rand(40, 50); // Start between 40-50%

            for ($i = 1; $i <= $progressPoints; $i++) {
                // Calculate progress date - spread out over the scheduled duration
                $progressDate = $startDate->copy()->addDays(($daysDuration / $progressPoints) * $i);

                // Ensure we don't go beyond 100%
                $currentProgress = min(100, $currentProgress + $progressIncrement);

                BuildingActualProgress::create([
                    'building_id' => $schedule->building_id,
                    'activity_id' => $schedule->activity_id,
                    'progress_percentage' => round($currentProgress, 2),
                    'progress_date' => $progressDate,
                    'notes' => $i === $progressPoints ? 'Activity completed' : 'Progress update',
                    'created_by' => 1, // Assuming user ID 1 exists
                    'updated_by' => 1,
                ]);
            }
        }
    }
}
