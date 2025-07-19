<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Activity;
use App\Models\BuildingActivity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingActivitySeeder extends Seeder
{
    // Define category weightages (these should sum to 100)
    private $categoryWeightages = [
        'Foundation Works' => 15,
        'Structure' => 25, // Combined for ground and first floor structure
        'Masonry' => 15,
        'Roofing' => 10,
        'Finishing' => 20,
        'MEP' => 10,
        'External' => 5
    ];

    public function run()
    {
        // Disable foreign key checks for performance
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        BuildingActivity::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $activities = Activity::all()->groupBy('category');
        $buildings = Building::all();

        foreach ($buildings as $building) {
            $this->attachActivitiesToBuilding($building, $activities);
        }
    }

    protected function attachActivitiesToBuilding($building, $groupedActivities)
    {
        $sortOrder = 1;
        $activitiesToAttach = [];

        foreach ($groupedActivities as $category => $activities) {
            $categoryWeight = $this->categoryWeightages[$category] ?? 0;
            $activityCount = count($activities);
            $baseWeight = $activityCount > 0 ? $categoryWeight / $activityCount : 0;

            foreach ($activities as $activity) {
                $activitiesToAttach[] = [
                    'building_id' => $building->id,
                    'activity_id' => $activity->id,
                    'weightage' => $baseWeight,
                    'sort_order' => $sortOrder++,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'created_by' => 1,
                    'updated_by' => 1,
                ];
            }
        }

        // Insert all activities at once for better performance
        DB::table('building_activities')->insert($activitiesToAttach);

        // Now adjust to ensure exact 100% total
        $this->normalizeWeightages($building->id);
    }

    protected function normalizeWeightages($buildingId)
    {
        // Get the current total weightage
        $totalWeightage = BuildingActivity::where('building_id', $buildingId)->sum('weightage');

        if ($totalWeightage != 100) {
            // Calculate adjustment factor
            $adjustmentFactor = 100 / $totalWeightage;

            // Update all weightages for this building
            BuildingActivity::where('building_id', $buildingId)
                ->update([
                    'weightage' => DB::raw("weightage * $adjustmentFactor")
                ]);
        }
    }
}
