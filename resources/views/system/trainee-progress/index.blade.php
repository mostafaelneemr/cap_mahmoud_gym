@extends('system.layout')

@section('header')
<style>
    :root {
        --gym-neon-orange: #d97706;
        --gym-electric-blue: #009ef7;
        --gym-neon-lime: #FACC15;
    }

    .progress-hero-card {
        background: linear-gradient(135deg, #1e1e2d 0%, #2a2a3f 60%, #151521 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 2rem;
        color: #fff;
        box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.4);
    }

    .progress-card {
        background: var(--gp-bg-surface, #1e1e2d);
        border: 1px solid var(--gp-border, rgba(255, 255, 255, 0.08));
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .progress-card:hover {
        border-color: rgba(0, 158, 247, 0.3);
    }

    .photo-thumb-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        aspect-ratio: 1 / 1;
        background: #11111b;
        border: 2px solid rgba(255, 255, 255, 0.08);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .photo-thumb-wrapper:hover {
        transform: scale(1.03);
        border-color: var(--gym-electric-blue);
        box-shadow: 0 10px 25px rgba(0, 158, 247, 0.3);
    }

    .photo-thumb-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: pointer;
    }

    .photo-overlay-actions {
        position: absolute;
        top: 8px;
        right: 8px;
        display: flex;
        gap: 6px;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .photo-thumb-wrapper:hover .photo-overlay-actions {
        opacity: 1;
    }

    .preview-thumb-box {
        position: relative;
        width: 90px;
        height: 90px;
        border-radius: 10px;
        overflow: hidden;
        border: 2px solid var(--gym-electric-blue);
    }

    .preview-thumb-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .preview-thumb-box .remove-preview-btn {
        position: absolute;
        top: 2px;
        right: 2px;
        background: rgba(220, 53, 69, 0.85);
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .coach-feedback-box {
        background: rgba(0, 158, 247, 0.08);
        border-left: 4px solid var(--gym-electric-blue);
        border-radius: 8px;
        padding: 1rem 1.25rem;
    }

    [dir="rtl"] .coach-feedback-box {
        border-left: none;
        border-right: 4px solid var(--gym-electric-blue);
    }

    .comparison-column {
        background: rgba(255, 255, 255, 0.03);
        border: 1px dashed rgba(255, 255, 255, 0.12);
        border-radius: 16px;
        padding: 1.5rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    <!-- Header Hero Banner -->
    <div class="progress-hero-card mb-6">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h2 class="text-white fw-bold mb-1 d-flex align-items-center gap-2">
                    <span>📸</span> {{ __('Progress & Photo Gallery') }} ({{ __('صور التطور والمتابعة') }})
                </h2>
                <p class="text-gray-400 mb-0 fs-6">
                    {{ __('Track body physical transformations, weekly check-ins, and coach feedback for') }}:
                    <strong class="text-warning">{{ $trainee->user->name ?? $trainee->email }}</strong>
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-primary fw-bold d-flex align-items-center gap-2" data-bs-toggle="collapse" data-bs-target="#newCheckinCollapse">
                    <i class="fa fa-plus"></i>
                    <span>{{ __('Add New Check-in') }}</span>
                </button>
                <button class="btn btn-outline btn-outline-info text-info fw-bold d-flex align-items-center gap-2" data-bs-toggle="collapse" data-bs-target="#comparisonCollapse">
                    <i class="fa fa-columns"></i>
                    <span>{{ __('Compare Check-ins') }}</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Collapse: Add New Check-in Form -->
    <div class="collapse mb-6" id="newCheckinCollapse">
        <div class="progress-card p-5">
            <h4 class="fw-bold mb-4 text-primary d-flex align-items-center gap-2">
                <i class="fa fa-calendar-plus text-primary"></i>
                <span>{{ __('New Check-in Session') }}</span>
            </h4>

            <form action="{{ route('system.trainee-progress.store') }}" method="POST" enctype="multipart/form-data" id="checkinForm">
                @csrf
                <input type="hidden" name="trainee_id" value="{{ $trainee->id }}">

                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold required">{{ __('Check-in Date') }}</label>
                        <input type="date" name="checkin_date" class="form-control form-control-solid @error('checkin_date') is-invalid @enderror" value="{{ old('checkin_date', date('Y-m-d')) }}" required>
                        @error('checkin_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">{{ __('Current Weight (KG)') }}</label>
                        <input type="number" step="0.1" name="weight" class="form-control form-control-solid @error('weight') is-invalid @enderror" placeholder="e.g. 78.5" value="{{ old('weight') }}">
                        @error('weight') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">{{ __('Upload Photos') }} (jpeg, png, jpg, webp, max 5MB)</label>
                        <input type="file" name="photos[]" id="photosInput" class="form-control form-control-solid" multiple accept="image/jpeg,image/png,image/jpg,image/webp">
                    </div>

                    <div class="col-12">
                        <!-- Live Image Preview Container -->
                        <div id="imagePreviewContainer" class="d-flex flex-wrap gap-3 mt-2"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('Trainee Notes / Observations') }}</label>
                        <textarea name="notes" class="form-control form-control-solid" rows="3" placeholder="{{ __('How are you feeling? Any changes in energy or measurements?') }}">{{ old('notes') }}</textarea>
                    </div>

                    @if($authUser->user_type != 2)
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-info">{{ __('Coach Feedback / Instructions') }}</label>
                            <textarea name="coach_notes" class="form-control form-control-solid border-info" rows="3" placeholder="{{ __('Write your evaluation or instructions for this check-in...') }}">{{ old('coach_notes') }}</textarea>
                        </div>
                    @endif

                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-light me-2" data-bs-toggle="collapse" data-bs-target="#newCheckinCollapse">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-success fw-bold px-6" id="submitCheckinBtn">
                            <i class="fa fa-cloud-upload-alt me-1"></i> {{ __('Save Check-in & Upload') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Collapse: Side-by-Side Comparison Tool -->
    <div class="collapse mb-6" id="comparisonCollapse">
        <div class="progress-card p-5">
            <h4 class="fw-bold mb-3 text-info d-flex align-items-center gap-2">
                <i class="fa fa-columns text-info"></i>
                <span>{{ __('Side-by-Side Progress Comparison') }}</span>
            </h4>
            <p class="text-muted fs-7 mb-4">{{ __('Select two check-in dates below to compare body transformation photos side by side.') }}</p>

            <div class="row g-3 mb-4">
                <div class="col-md-5">
                    <label class="form-label fw-bold">{{ __('Session A (Older Check-in)') }}</label>
                    <select id="compareDate1" class="form-select form-select-solid">
                        <option value="">-- {{ __('Select Date A') }} --</option>
                        @foreach($checkins as $ck)
                            <option value="{{ $ck->checkin_date->format('Y-m-d') }}">
                                {{ $ck->checkin_date->format('Y-m-d') }} @if($ck->weight) ({{ $ck->weight }} KG) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5">
                    <label class="form-label fw-bold">{{ __('Session B (Newer Check-in)') }}</label>
                    <select id="compareDate2" class="form-select form-select-solid">
                        <option value="">-- {{ __('Select Date B') }} --</option>
                        @foreach($checkins as $ck)
                            <option value="{{ $ck->checkin_date->format('Y-m-d') }}">
                                {{ $ck->checkin_date->format('Y-m-d') }} @if($ck->weight) ({{ $ck->weight }} KG) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-info w-100 fw-bold" id="runCompareBtn">
                        <i class="fa fa-sync me-1"></i> {{ __('Compare') }}
                    </button>
                </div>
            </div>

            <!-- Comparison Result Container -->
            <div id="comparisonResult" class="row g-4 d-none">
                <div class="col-md-6">
                    <div class="comparison-column" id="colSessionA">
                        <h5 class="fw-bold text-warning border-bottom pb-2 mb-3" id="titleA">Session A</h5>
                        <div id="contentA"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="comparison-column" id="colSessionB">
                        <h5 class="fw-bold text-success border-bottom pb-2 mb-3" id="titleB">Session B</h5>
                        <div id="contentB"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History Timeline & Gallery Section -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
            <i class="fa fa-history text-warning"></i>
            <span>{{ __('Check-in History') }} ({{ $checkins->count() }})</span>
        </h3>
    </div>

    @if($checkins->isEmpty())
        <div class="progress-card p-8 text-center">
            <div class="mb-4">
                <i class="fa fa-camera-retro fs-3x text-muted opacity-50"></i>
            </div>
            <h4 class="text-white fw-bold">{{ __('No progress check-ins recorded yet') }}</h4>
            <p class="text-gray-400 max-w-500px mx-auto mb-4">
                {{ __('Upload weekly check-in photos and track body measurements over time.') }}
            </p>
            <button class="btn btn-primary fw-bold" data-bs-toggle="collapse" data-bs-target="#newCheckinCollapse">
                <i class="fa fa-plus me-1"></i> {{ __('Create First Check-in') }}
            </button>
        </div>
    @else
        <div class="row g-4">
            @foreach($checkins as $checkin)
                <div class="col-12">
                    <div class="progress-card p-5">
                        <div class="d-flex flex-wrap align-items-center justify-content-between border-bottom pb-4 mb-4 gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-primary fs-6 px-3 py-2 fw-bold rounded-pill">
                                    <i class="fa fa-calendar-alt me-1 text-white"></i> {{ $checkin->checkin_date->format('Y-m-d') }}
                                </span>

                                @if($checkin->weight)
                                    <span class="badge bg-light-warning text-warning fs-6 px-3 py-2 fw-bold border border-warning border-opacity-25 rounded-pill">
                                        ⚖️ {{ $checkin->weight }} KG
                                    </span>
                                @endif

                                <span class="text-muted fs-7">
                                    {{ __('Logged') }}: {{ $checkin->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                @if($authUser->user_type != 2)
                                    <button class="btn btn-sm btn-light-info fw-bold d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#coachNotesModal_{{ $checkin->id }}">
                                        <i class="fa fa-edit"></i>
                                        <span>{{ __('Edit Feedback') }}</span>
                                    </button>
                                @endif

                                <form action="{{ route('system.trainee-progress.destroy', $checkin->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this check-in session and all its photos?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light-danger fw-bold">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Notes & Feedback Area -->
                        <div class="row g-3 mb-4">
                            @if($checkin->notes)
                                <div class="col-md-6">
                                    <div class="bg-dark bg-opacity-30 rounded p-3 border border-white border-opacity-10">
                                        <div class="fw-bold text-gray-300 fs-7 mb-1">
                                            <i class="fa fa-comment-dots me-1 text-primary"></i> {{ __('Trainee Notes') }}:
                                        </div>
                                        <div class="text-white fs-6">{{ $checkin->notes }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($checkin->coach_notes)
                                <div class="{{ $checkin->notes ? 'col-md-6' : 'col-12' }}">
                                    <div class="coach-feedback-box">
                                        <div class="fw-bold text-info fs-7 mb-1 d-flex align-items-center justify-content-between">
                                            <span><i class="fa fa-user-ninja me-1 text-info"></i> {{ __('Coach Feedback') }}:</span>
                                            @if($checkin->coach)
                                                <small class="text-muted">({{ $checkin->coach->name }})</small>
                                            @endif
                                        </div>
                                        <div class="text-white fs-6 fw-semibold">{{ $checkin->coach_notes }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Photos Grid -->
                        @if($checkin->photos->count() > 0)
                            <div class="row g-3">
                                @foreach($checkin->photos as $photo)
                                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                        <div class="photo-thumb-wrapper">
                                            <img src="{{ $photo->photo_url }}" class="photo-thumb-img" alt="Progress Photo" data-bs-toggle="modal" data-bs-target="#lightboxModal" data-img-src="{{ $photo->photo_url }}" data-date="{{ $checkin->checkin_date->format('Y-m-d') }}">

                                            <div class="photo-overlay-actions">
                                                <form action="{{ route('system.trainee-progress.destroy-photo', $photo->id) }}" method="POST" onsubmit="return confirm('{{ __('Delete this photo?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-icon btn-sm rounded-circle p-0" style="width:26px; height:26px;">
                                                        <i class="fa fa-times fs-8"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-muted fs-7 italic">{{ __('No photos uploaded for this check-in session.') }}</div>
                        @endif
                    </div>
                </div>

                <!-- Modal for Editing Coach Notes -->
                @if($authUser->user_type != 2)
                    <div class="modal fade" id="coachNotesModal_{{ $checkin->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content bg-dark text-white border border-secondary">
                                <div class="modal-header border-secondary">
                                    <h5 class="modal-header-title text-info fw-bold">
                                        <i class="fa fa-user-ninja me-2"></i>{{ __('Coach Feedback for Check-in') }} ({{ $checkin->checkin_date->format('Y-m-d') }})
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('system.trainee-progress.update-notes', $checkin->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{ __('Coach Feedback / Notes') }}</label>
                                            <textarea name="coach_notes" class="form-control form-control-solid" rows="4" placeholder="{{ __('Provide guidelines or critique on trainee form/progress...') }}">{{ $checkin->coach_notes }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-secondary">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                        <button type="submit" class="btn btn-info fw-bold">{{ __('Save Feedback') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

</div>

<!-- Lightbox Modal -->
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-white border-0">
            <div class="modal-header border-0 pb-0">
                <span class="fw-bold text-warning" id="lightboxDateLabel"></span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3">
                <img id="lightboxImage" src="" class="img-fluid rounded shadow" style="max-height: 80vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Client-Side Image Preview
        const photosInput = document.getElementById('photosInput');
        const previewContainer = document.getElementById('imagePreviewContainer');

        if (photosInput && previewContainer) {
            photosInput.addEventListener('change', function (e) {
                previewContainer.innerHTML = '';
                const files = e.target.files;

                if (files) {
                    Array.from(files).forEach((file, index) => {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function (evt) {
                                const thumbBox = document.createElement('div');
                                thumbBox.className = 'preview-thumb-box';
                                thumbBox.innerHTML = `
                                    <img src="${evt.target.result}">
                                    <span class="preview-filename text-truncate d-block fs-8 text-center text-gray-300" style="max-width:90px;">${file.name}</span>
                                `;
                                previewContainer.appendChild(thumbBox);
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }
            });
        }

        // 2. Lightbox Modal Trigger
        const lightboxModal = document.getElementById('lightboxModal');
        if (lightboxModal) {
            lightboxModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const imgSrc = button.getAttribute('data-img-src');
                const date = button.getAttribute('data-date');

                document.getElementById('lightboxImage').src = imgSrc;
                document.getElementById('lightboxDateLabel').textContent = date ? '{{ __("Check-in Session Date") }}: ' + date : '';
            });
        }

        // 3. Side-by-Side Comparison AJAX
        const runCompareBtn = document.getElementById('runCompareBtn');
        if (runCompareBtn) {
            runCompareBtn.addEventListener('click', function () {
                const date1 = document.getElementById('compareDate1').value;
                const date2 = document.getElementById('compareDate2').value;
                const traineeId = "{{ $trainee->id }}";

                if (!date1 || !date2) {
                    alert("{{ __('Please select both Date A and Date B to compare.') }}");
                    return;
                }

                fetch(`{{ route('system.trainee-progress.compare') }}?trainee_id=${traineeId}&date1=${date1}&date2=${date2}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(response => {
                    if (response.status && response.data) {
                        const checkin1 = response.data.checkin1;
                        const checkin2 = response.data.checkin2;

                        renderComparisonColumn('colSessionA', 'titleA', 'contentA', checkin1, date1);
                        renderComparisonColumn('colSessionB', 'titleB', 'contentB', checkin2, date2);

                        document.getElementById('comparisonResult').classList.remove('d-none');
                    } else {
                        alert(response.message || 'Error fetching comparison data.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error connecting to server.');
                });
            });
        }

        function renderComparisonColumn(colId, titleId, contentId, checkin, dateLabel) {
            const titleEl = document.getElementById(titleId);
            const contentEl = document.getElementById(contentId);

            if (!checkin) {
                titleEl.textContent = dateLabel + ' (No Data)';
                contentEl.innerHTML = '<div class="text-muted italic">{{ __("No check-in record for this date.") }}</div>';
                return;
            }

            titleEl.textContent = checkin.checkin_date + (checkin.weight ? ` - (${checkin.weight} KG)` : '');

            let html = '';

            if (checkin.notes) {
                html += `<div class="mb-3"><strong class="text-warning fs-7">{{ __("Trainee Notes") }}:</strong> <p class="mb-0 text-white">${checkin.notes}</p></div>`;
            }

            if (checkin.coach_notes) {
                html += `<div class="mb-3"><strong class="text-info fs-7">{{ __("Coach Feedback") }}:</strong> <p class="mb-0 text-white">${checkin.coach_notes}</p></div>`;
            }

            if (checkin.photos && checkin.photos.length > 0) {
                html += '<div class="row g-2 mt-2">';
                checkin.photos.forEach(photo => {
                    html += `
                        <div class="col-6">
                            <div class="photo-thumb-wrapper">
                                <img src="${photo.photo_url}" class="photo-thumb-img" onclick="window.open('${photo.photo_url}', '_blank')">
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            } else {
                html += '<div class="text-muted fs-7">{{ __("No photos recorded.") }}</div>';
            }

            contentEl.innerHTML = html;
        }
    });
</script>
@endsection
