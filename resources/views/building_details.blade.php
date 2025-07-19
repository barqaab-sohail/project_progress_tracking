<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Building Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Building No:</strong> {{ $building->building_no }}</p>
                        <p><strong>Type:</strong> {{ ucfirst($building->type) }}</p>
                        <p><strong>Location:</strong> {{ $building->location }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> 
                            <span class="badge badge-{{ $building->status == 'completed' ? 'success' : ($building->status == 'in_progress' ? 'primary' : 'secondary') }}">
                                {{ ucfirst(str_replace('_', ' ', $building->status)) }}
                            </span>
                        </p>
                        <p><strong>Coordinates:</strong> {{ $building->latitude }}, {{ $building->longitude }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>Progress Photos ({{ $currentDate }})</h5>
            </div>
            <div class="card-body">
                @php
                    $allPhotos = collect();
                    foreach ($activities as $activity) {
                        $allPhotos = $allPhotos->merge($activity['photos']);
                    }
                @endphp

                @if($allPhotos->count() > 0)
                    <div class="d-flex flex-wrap">
                        @foreach($allPhotos as $photo)
                            <img src="{{ asset('storage/' . $photo->photo_path) }}" 
                                 class="building-photo-thumbnail"
                                 data-toggle="modal" data-target="#photoModal"
                                 data-photo="{{ asset('storage/' . $photo->photo_path) }}"
                                 data-caption="{{ $photo->caption }}">
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No photos available for this date.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Activities Progress ({{ $currentDate }})</h5>
            </div>
            <div class="card-body">
                @foreach($activities as $activity)
                    <div class="activity-progress-row">
                        <div class="d-flex justify-content-between">
                            <span class="activity-name">{{ $activity['activity']->name }}</span>
                            <span>{{ round($activity['actual_progress'], 1) }}% / {{ round($activity['scheduled_progress'], 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-info" role="progressbar" 
                                 style="width: {{ $activity['scheduled_progress'] }}%" 
                                 aria-valuenow="{{ $activity['scheduled_progress'] }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $activity['actual_progress'] }}%" 
                                 aria-valuenow="{{ $activity['actual_progress'] }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                        <small>Weightage: {{ $activity['weightage'] }}%</small>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Photo Modal -->
<div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Progress Photo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalPhoto" src="" class="img-fluid">
                <p id="modalCaption" class="mt-2"></p>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle photo click
    $('.building-photo-thumbnail').click(function() {
        $('#modalPhoto').attr('src', $(this).data('photo'));
        $('#modalCaption').text($(this).data('caption'));
    });

    // Handle date change in modal
    $('#building-date-selector').change(function() {
        const buildingId = {{ $building->id }};
        const date = $(this).val();
        
        $.ajax({
            url: '/building-details/' + buildingId,
            method: 'GET',
            data: { date: date },
            success: function(response) {
                $('#building-details-content').html(response.html);
            },
            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });
    });
});
</script>