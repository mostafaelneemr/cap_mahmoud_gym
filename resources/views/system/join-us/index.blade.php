@extends('system.layout')

@section('content')

    {{-- ── Status Summary Widgets ────────────────────────────────────────── --}}
    <div class="row g-5 mb-7">

        <div class="col-6 col-sm-3">
            <div class="card card-flush border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center py-5">
                    <span class="fs-2x fw-bolder text-primary">{{ $counts['new'] }}</span>
                    <span class="fw-semibold text-gray-500 mt-1">{{ __('New') }}</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-sm-3">
            <div class="card card-flush border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center py-5">
                    <span class="fs-2x fw-bolder text-warning">{{ $counts['contacted'] }}</span>
                    <span class="fw-semibold text-gray-500 mt-1">{{ __('Contacted') }}</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-sm-3">
            <div class="card card-flush border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center py-5">
                    <span class="fs-2x fw-bolder text-success">{{ $counts['converted'] }}</span>
                    <span class="fw-semibold text-gray-500 mt-1">{{ __('Converted') }}</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-sm-3">
            <div class="card card-flush border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center py-5">
                    <span class="fs-2x fw-bolder text-danger">{{ $counts['rejected'] }}</span>
                    <span class="fw-semibold text-gray-500 mt-1">{{ __('Rejected') }}</span>
                </div>
            </div>
        </div>

    </div>

    {!! Form::open(['id' => 'filterForm', 'onsubmit' => 'filterFunction("'.$datatableURL.'","'.$datatableVar.'",$(this));return false;']) !!}
    <div class="card mb-7">
        <div class="card-body py-5 d-flex flex-wrap align-items-center gap-3">

            <label class="fw-semibold text-gray-700 me-2">{{ __('Filter by Status') }}:</label>

            @foreach(['all' => __('All'), 'new' => __('New'), 'contacted' => __('Contacted'), 'converted' => __('Converted'), 'rejected' => __('Rejected')] as $val => $label)
                <button type="submit" name="status" value="{{ $val }}"
                        class="btn btn-sm {{ $val === 'new' ? 'btn-primary' : 'btn-light-secondary' }}">
                    {{ $label }}
                </button>
            @endforeach

            <button type="reset" class="btn btn-sm btn-warning ms-auto" onclick="resetForm()">
                <i class="fa-solid fa-rotate-left me-1"></i>{{ __('Reset') }}
            </button>

        </div>
    </div>
    {!! Form::close() !!}

    @include('system.datatable')

@endsection
