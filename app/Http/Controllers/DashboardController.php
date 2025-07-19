<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Building;
use Illuminate\Http\Request;
use App\Models\BuildingActualProgress;
use App\Models\BuildingProgressAggregated;

class DashboardController extends Controller
{

    public function getProgressDates()
    {
        $dates = BuildingActualProgress::selectRaw('DISTINCT DATE(progress_date) as date')
            ->orderBy('date', 'desc')
            ->get()
            ->pluck('date')
            ->map(function ($date) {
                return $date->format('Y-m-d');
            });

        return response()->json([
            'dates' => $dates
        ]);
    }

    public function getDashboardData(Request $request)
    {
        $date = $request->input('date');

        if (!$date) {
            return response()->json([
                'summary' => [
                    'actual_progress' => 0,
                    'scheduled_progress' => 0,
                    'remaining_progress' => 100
                ],
                'buildings' => []
            ]);
        }

        // Get buildings with progress data for selected date
        $buildings = Building::whereHas('actualProgress', function ($query) use ($date) {
            $query->whereDate('progress_date', $date);
        })
            ->with(['aggregatedProgress' => function ($query) use ($date) {
                $query->whereDate('progress_date', $date);
            }])
            ->get()
            ->map(function ($building) {
                $progress = $building->aggregatedProgress->first();
                return [
                    'building_no' => $building->building_no,
                    'name' => $building->name,
                    'type' => $building->type,
                    'actual_progress' => $progress ? $progress->actual_percentage : 0,
                    'scheduled_progress' => $progress ? $progress->scheduled_percentage : 0,
                ];
            });

        // Calculate summary statistics
        $actualAvg = $buildings->avg('actual_progress') ?? 0;
        $scheduledAvg = $buildings->avg('scheduled_progress') ?? 0;

        return response()->json([
            'summary' => [
                'actual_progress' => round($actualAvg, 2),
                'scheduled_progress' => round($scheduledAvg, 2),
                'remaining_progress' => round(100 - $actualAvg, 2)
            ],
            'buildings' => $buildings
        ]);
    }



    public function getBuildingsTable(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $status = $request->input('status', 'all');

        $buildings = Building::with(['aggregatedProgress' => function ($query) use ($date) {
            $query->where('progress_date', $date);
        }])
            ->when($status !== 'all', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->get();

        return view('partials.buildings_table_rows', [
            'buildings' => $buildings,
            'currentDate' => $date
        ])->render();
    }

    public function getBuildingDetails($id, Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $building = Building::with([
            'activities' => function ($query) {
                $query->with('activity')
                    ->orderBy('sort_order');
            },
            'actualProgress' => function ($query) use ($date) {
                $query->where('progress_date', '<=', $date)
                    ->with('photos');
            },
            'scheduleProgress' => function ($query) use ($date) {
                $query->where('progress_date', '<=', $date);
            }
        ])->findOrFail($id);

        // Calculate activity progress
        $activities = $building->activities->map(function ($activity) use ($date, $building) {
            $actualProgress = $building->actualProgress
                ->where('activity_id', $activity->activity_id)
                ->sortByDesc('progress_date')
                ->first();

            $scheduledProgress = $building->scheduleProgress
                ->where('activity_id', $activity->activity_id)
                ->sortByDesc('progress_date')
                ->first();

            $photos = $actualProgress->photos ?? collect();

            return [
                'activity' => $activity->activity,
                'weightage' => $activity->weightage,
                'actual_progress' => $actualProgress->progress_percentage ?? 0,
                'scheduled_progress' => $scheduledProgress->progress_percentage ?? 0,
                'photos' => $photos
            ];
        });

        $html = view('partials.building_details', [
            'building' => $building,
            'activities' => $activities,
            'currentDate' => $date
        ])->render();

        return response()->json([
            'building' => $building,
            'html' => $html
        ]);
    }
}
