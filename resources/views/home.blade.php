@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="my-4">Building Progress Overview</h2>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Buildings Progress</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="buildingsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Building Name</th>
                            <th>Building No</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Progress (Scheduled vs Actual)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($buildings as $building)
                        @php
                            $latestProgress = $building->aggregatedProgress->last();
                            $scheduledPercentage = $latestProgress ? $latestProgress->scheduled_percentage : 0;
                            $actualPercentage = $latestProgress ? $latestProgress->actual_percentage : 0;
                            $variance = $actualPercentage - $scheduledPercentage;
                        @endphp
                        <tr>
                            <td>{{ $building->name }}</td>
                            <td>{{ $building->building_no }}</td>
                            <td>{{ $building->location }}</td>
                            <td>
                                <span class="badge badge-{{ 
                                    $building->status == 'completed' ? 'success' : 
                                    ($building->status == 'in_progress' ? 'primary' : 
                                    ($building->status == 'on_hold' ? 'warning' : 'secondary')) 
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $building->status)) }}
                                </span>
                            </td>
                            <td>
                                <div class="progress-container">
                                    <!-- Thick outer bar for scheduled progress -->
                                    <div class="progress progress-thick" style="height: 20px;">
                                        <div class="progress-bar bg-secondary" 
                                             role="progressbar" 
                                             style="width: {{ $scheduledPercentage }}%" 
                                             aria-valuenow="{{ $scheduledPercentage }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    
                                    <!-- Thin inner bar for actual progress -->
                                    <div class="progress progress-thin" style="height: 8px; margin-top: -14px; margin-bottom: 6px;">
                                        <div class="progress-bar {{ $variance >= 0 ? 'bg-success' : 'bg-danger' }}" 
                                             role="progressbar" 
                                             style="width: {{ $actualPercentage }}%" 
                                             aria-valuenow="{{ $actualPercentage }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    
                                    <!-- Progress labels -->
                                    <div class="progress-labels d-flex justify-content-between">
                                        <span class="text-secondary">
                                            <i class="fas fa-calendar-alt"></i> Scheduled: {{ number_format($scheduledPercentage, 1) }}%
                                        </span>
                                        <span class="{{ $variance >= 0 ? 'text-success' : 'text-danger' }}">
                                            <i class="fas fa-chart-line"></i> Actual: {{ number_format($actualPercentage, 1) }}%
                                        </span>
                                        <span class="{{ $variance >= 0 ? 'text-success' : 'text-danger' }}">
                                            <i class="fas fa-balance-scale"></i> {{ number_format(abs($variance), 1) }}%
                                        </span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .progress-container {
        position: relative;
        margin-bottom: 20px;
    }
    
    .progress-thick {
        height: 20px;
        border-radius: 10px;
        background-color: #f0f0f0;
    }
    
    .progress-thin {
        height: 8px;
        border-radius: 4px;
        background-color: transparent;
        position: relative;
        top: -14px;
    }
    
    .progress-labels {
        font-size: 0.8rem;
        margin-top: -8px;
    }
    
    .progress-labels i {
        margin-right: 4px;
    }
</style>
@endsection