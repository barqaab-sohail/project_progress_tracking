@foreach($buildings as $building)
<tr>
    <td>{{ $building->building_no }}</td>
    <td>{{ $building->name }}</td>
    <td>{{ ucfirst($building->type) }}</td>
    <td>{{ $building->location }}</td>
    <td>
        <span class="badge badge-{{ $building->status == 'completed' ? 'success' : ($building->status == 'in_progress' ? 'primary' : 'secondary') }}">
            {{ ucfirst(str_replace('_', ' ', $building->status)) }}
        </span>
    </td>
    <td>
        @php
            $aggregated = $building->aggregatedProgress->first();
            $actual = $aggregated->actual_percentage ?? 0;
            $scheduled = $aggregated->scheduled_percentage ?? 0;
        @endphp
        <div class="progress-bar-container">
            <div class="progress-bar-scheduled" style="width: {{ $scheduled }}%"></div>
            <div class="progress-bar-actual" style="width: {{ $actual }}%"></div>
        </div>
        <small>Actual: {{ round($actual, 1) }}% | Scheduled: {{ round($scheduled, 1) }}%</small>
    </td>
    <td>
        <button class="btn btn-sm btn-info view-building-btn" data-id="{{ $building->id }}">
            <i class="fas fa-eye"></i> View
        </button>
    </td>
</tr>
@endforeach