@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="row">
        <div class="col-md-12">
            <div class="card dashboard-header-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">{{ __('Construction Progress Dashboard') }}</h3>
                    <div class="date-selector">
                        <select id="progress-date" class="form-control">
                            <option value="">Select a progress date</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Indicators -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card summary-card bg-success text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Actual Progress</h5>
                    <h2 id="actual-progress" class="display-4">0%</h2>
                    <div class="progress mt-2" style="height: 10px;">
                        <div id="actual-bar" class="progress-bar bg-white" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card summary-card bg-info text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Scheduled Progress</h5>
                    <h2 id="scheduled-progress" class="display-4">0%</h2>
                    <div class="progress mt-2" style="height: 10px;">
                        <div id="scheduled-bar" class="progress-bar bg-white" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card summary-card bg-warning text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Remaining Progress</h5>
                    <h2 id="remaining-progress" class="display-4">100%</h2>
                    <div class="progress mt-2" style="height: 10px;">
                        <div id="remaining-bar" class="progress-bar bg-white" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Buildings Table -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Buildings Progress</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="buildings-table" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Building No</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Progress</th>
                                    <th>Variance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No data available - please select a date</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .dashboard-container {
        background-color: #f8f9fa;
        padding: 20px;
    }
    .dashboard-header-card {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
    }
    .summary-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .progress-comparison {
        height: 20px;
        background-color: #e9ecef;
        border-radius: 5px;
        position: relative;
    }
    .progress-scheduled {
        background-color: #17a2b8;
        height: 100%;
        border-radius: 5px;
    }
    .progress-actual {
        background-color: #28a745;
        height: 100%;
        border-radius: 5px;
        position: absolute;
        top: 0;
        left: 0;
    }
    .variance-positive {
        color: #28a745;
        font-weight: bold;
    }
    .variance-negative {
        color: #dc3545;
        font-weight: bold;
    }
    .variance-neutral {
        color: #6c757d;
        font-weight: bold;
    }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize with empty state
    resetDashboard();

    // Load available dates with actual progress
    loadAvailableDates();

    // Date change handler
    $('#progress-date').change(function() {
        const date = $(this).val();
        if (date) {
            loadDashboardData(date);
        } else {
            resetDashboard();
        }
    });

    function loadAvailableDates() {
        $.ajax({
            url: '/api/progress-dates',
            method: 'GET',
            success: function(response) {
                const select = $('#progress-date');
                select.empty().append('<option value="">Select a progress date</option>');
                response.dates.forEach(date => {
                    select.append(`<option value="${date}">${date}</option>`);
                });
            },
            error: function(xhr) {
                console.error('Error loading dates:', xhr.responseText);
            }
        });
    }

    function loadDashboardData(date) {
        $.ajax({
            url: '/api/dashboard-data',
            method: 'GET',
            data: { date: date },
            success: function(response) {
                updateProgressIndicators(response.summary);
                updateBuildingsTable(response.buildings);
            },
            error: function(xhr) {
                console.error('Error loading data:', xhr.responseText);
                resetDashboard();
            }
        });
    }

    function updateProgressIndicators(summary) {
        $('#actual-progress').text(summary.actual_progress.toFixed(1) + '%');
        $('#scheduled-progress').text(summary.scheduled_progress.toFixed(1) + '%');
        $('#remaining-progress').text(summary.remaining_progress.toFixed(1) + '%');
        
        $('#actual-bar').css('width', summary.actual_progress + '%');
        $('#scheduled-bar').css('width', summary.scheduled_progress + '%');
        $('#remaining-bar').css('width', summary.remaining_progress + '%');
    }

    function updateBuildingsTable(buildings) {
        const tbody = $('#buildings-table tbody');
        
        if (buildings.length === 0) {
            tbody.html('<tr><td colspan="5" class="text-center text-muted">No buildings with progress data for selected date</td></tr>');
            return;
        }

        let rows = '';
        buildings.forEach(building => {
            const variance = building.actual_progress - building.scheduled_progress;
            let varianceClass = 'variance-neutral';
            let varianceIcon = '';
            
            if (variance > 1) { // More than 1% difference
                varianceClass = 'variance-positive';
                varianceIcon = '<i class="fas fa-arrow-up"></i>';
            } else if (variance < -1) {
                varianceClass = 'variance-negative';
                varianceIcon = '<i class="fas fa-arrow-down"></i>';
            }

            rows += `
                <tr>
                    <td>${building.building_no}</td>
                    <td>${building.name}</td>
                    <td>${building.type.charAt(0).toUpperCase() + building.type.slice(1)}</td>
                    <td>
                        <div class="progress-comparison">
                            <div class="progress-scheduled" style="width: ${building.scheduled_progress}%"></div>
                            <div class="progress-actual" style="width: ${building.actual_progress}%"></div>
                        </div>
                        <small>Actual: ${building.actual_progress.toFixed(1)}% | Scheduled: ${building.scheduled_progress.toFixed(1)}%</small>
                    </td>
                    <td class="${varianceClass}">
                        ${varianceIcon} ${Math.abs(variance).toFixed(1)}%
                    </td>
                </tr>
            `;
        });

        tbody.html(rows);
    }

    function resetDashboard() {
        $('#actual-progress').text('0%');
        $('#scheduled-progress').text('0%');
        $('#remaining-progress').text('100%');
        
        $('#actual-bar').css('width', '0%');
        $('#scheduled-bar').css('width', '0%');
        $('#remaining-bar').css('width', '100%');
        
        $('#buildings-table tbody').html(`
            <tr>
                <td colspan="5" class="text-center text-muted">No data available - please select a date</td>
            </tr>
        `);
    }
});
</script>
@endsection