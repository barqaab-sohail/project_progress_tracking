<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\BuildingScheduleProgress;
use App\Models\BuildingActualProgress;
use Carbon\Carbon;

class BuildingProgressController extends Controller
{
    public function index($date = null)
    {
        $currentDate = $date ? Carbon::parse($date) : Carbon::today();

        $buildings = Building::with(['scheduleProgress', 'actualProgress' => function ($q) use ($currentDate) {
            $q->whereDate('progress_date', '<=', $currentDate)
                ->orderBy('progress_date', 'desc');
        }])->get();

        return view('buildings.progress', compact('buildings', 'currentDate'));
    }

    public function activities(Building $building, $date = null)
    {
        $currentDate = $date ? Carbon::parse($date) : Carbon::today();

        $activities = $building->activities()->with(['scheduleProgress', 'actualProgress' => function ($q) use ($currentDate) {
            $q->whereDate('progress_date', '<=', $currentDate)
                ->orderBy('progress_date', 'desc');
        }])->get();

        return view('buildings.activities', compact('building', 'activities', 'currentDate'));
    }
}
