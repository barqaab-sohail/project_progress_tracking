@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Building Progress Dashboard</h1>
    
    <!-- Date Picker -->
    <div class="mb-4">
        <label for="progress-date" class="form-label">Select Date:</label>
        <input type="date" id="progress-date" class="form-control" value="{{ $currentDate->format('Y-m-d') }}">
    </div>

    <!-- Buildings Table -->
    <div class="card mb-4">
        <div class="card-header">
            <h2>Buildings Progress</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Building</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Progress (Scheduled vs Actual)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($buildings as $building)
                        <tr>
                            <td>{{ $building->name }} ({{ $building->building_no }})</td>
                            <td>{{ ucfirst($building->type) }}</td>
                            <td>{{ str_replace('_', ' ', ucfirst($building->status)) }}</td>
                            <td>
                                @php
                                    $scheduled = $building->getScheduledProgress($currentDate);
                                    $actual = $building->getActualProgress($currentDate);
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
                            <td>
                                <a href="{{ route('buildings.activities', $building) }}" class="btn btn-sm btn-info">
                                    View Activities
                                </a>
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
                    <h3>Overall Progress Comparison</h3>
                </div>
                <div class="card-body">
                    <canvas id="progressComparisonChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Status Distribution</h3>
                </div>
                <div class="card-body">
                    <canvas id="statusDistributionChart" height="200"></canvas>
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
        window.location.href = "{{ route('buildings.progress') }}?date=" + this.value;
    });

    // Progress Comparison Chart
    new Chart(document.getElementById('progressComparisonChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($buildings->pluck('name')) !!},
            datasets: [
                {
                    label: 'Scheduled Progress',
                    data: {!! json_encode($buildings->map->getScheduledProgress($currentDate)) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                },
                {
                    label: 'Actual Progress',
                    data: {!! json_encode($buildings->map->getActualProgress($currentDate)) !!},
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

    // Status Distribution Chart
    new Chart(document.getElementById('statusDistributionChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode(['Planned', 'In Progress', 'Completed', 'On Hold']) !!},
            datasets: [{
                data: {!! json_encode([
                    $buildings->where('status', 'planned')->count(),
                    $buildings->where('status', 'in_progress')->count(),
                    $buildings->where('status', 'completed')->count(),
                    $buildings->where('status', 'on_hold')->count()
                ]) !!},
                backgroundColor: [
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(255, 99, 132, 0.6)'
                ]
            }]
        }
    });
</script>
@endpush
@endsection