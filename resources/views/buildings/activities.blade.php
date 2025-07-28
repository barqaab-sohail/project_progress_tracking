@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $building->name }} - Activities Progress</h1>
    
    <!-- Date Picker -->
    <div class="mb-4">
        <label for="progress-date" class="form-label">Select Date:</label>
        <input type="date" id="progress-date" class="form-control" value="{{ $currentDate->format('Y-m-d') }}">
    </div>

    <!-- Activities Table -->
    <div class="card mb-4">
        <div class="card-header">
            <h2>Activities Progress</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Activity</th>
                            <th>Category</th>
                            <th>Weightage</th>
                            <th>Scheduled Dates</th>
                            <th>Progress (Scheduled vs Actual)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activities as $activity)
                        <tr>
                            <td>{{ $activity->name }}</td>
                            <td>{{ $activity->category ?? 'N/A' }}</td>
                            <td>{{ $activity->weightage }}%</td>
                            <td>
                                {{ $activity->scheduleProgress->schedule_start_date->format('M d, Y') }} - 
                                {{ $activity->scheduleProgress->schedule_completion_date->format('M d, Y') }}
                            </td>
                            <td>
                                @php
                                    $scheduled = $activity->getScheduledProgress($currentDate);
                                    $actual = $activity->getActualProgress($currentDate);
                                @endphp
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $scheduled }}%">
                                        Scheduled: {{ $scheduled }}%
                                    </div>
                                    <div class="progress-bar bg-success" style="width: {{ max(0, $actual - $scheduled) }}%">
                                        Actual: {{ $actual }}%
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

    <!-- Additional Charts -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Activities Progress Timeline</h3>
                </div>
                <div class="card-body">
                    <canvas id="timelineChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Progress by Category</h3>
                </div>
                <div class="card-body">
                    <canvas id="categoryProgressChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Date picker change event
    document.getElementById('progress-date').addEventListener('change', function() {
        window.location.href = "{{ route('buildings.activities', $building) }}?date=" + this.value;
    });

    // Timeline Chart
    new Chart(document.getElementById('timelineChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($activities->pluck('name')) !!},
            datasets: [
                {
                    label: 'Scheduled Progress',
                    data: {!! json_encode($activities->map->getScheduledProgress($currentDate)) !!},
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    fill: true
                },
                {
                    label: 'Actual Progress',
                    data: {!! json_encode($activities->map->getActualProgress($currentDate)) !!},
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    // Category Progress Chart
    @if($activities->whereNotNull('category')->count() > 0)
    new Chart(document.getElementById('categoryProgressChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($activities->groupBy('category')->keys()) !!},
            datasets: [
                {
                    label: 'Average Scheduled Progress',
                    data: {!! json_encode($activities->groupBy('category')->map(function($items) use ($currentDate) {
                        return $items->avg(function($activity) use ($currentDate) {
                            return $activity->getScheduledProgress($currentDate);
                        });
                    })) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                },
                {
                    label: 'Average Actual Progress',
                    data: {!! json_encode($activities->groupBy('category')->map(function($items) use ($currentDate) {
                        return $items->avg(function($activity) use ($currentDate) {
                            return $activity->getActualProgress($currentDate);
                        });
                    })) !!},
                    backgroundColor: 'rgba(75, 192, 192, 0.6)'
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
    @endif
</script>
@endpush
@endsection