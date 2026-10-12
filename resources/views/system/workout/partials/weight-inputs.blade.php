{{--
    Multi-weight input group (max 4).
    Params:
      - $name    : base input name WITHOUT trailing [] (e.g. "exercises[ex_1][internal_weight]")
      - $weights : array of existing weights (optional)
--}}
@php
    $maxWeights = \App\Models\Exercise::MAX_WEIGHTS;
    $weightValues = array_slice(array_values(\App\Models\Exercise::normalizeWeights($weights ?? [])), 0, $maxWeights);
    if (empty($weightValues)) {
        $weightValues = [''];
    }
@endphp
<div class="weight-inputs" data-name="{{ $name }}[]" data-max="{{ $maxWeights }}">
    <div class="weight-inputs__list">
        @foreach($weightValues as $weightValue)
            <div class="weight-inputs__item">
                <input type="text" name="{{ $name }}[]" value="{{ $weightValue }}"
                       class="form-control form-control-solid text-center" placeholder="20kg" maxlength="20">
                <button type="button" class="weight-inputs__remove" title="@lang('Remove')" aria-label="@lang('Remove')">&times;</button>
            </div>
        @endforeach
    </div>
    <button type="button" class="btn btn-icon btn-sm btn-light-success weight-inputs__add"
            title="@lang('Add Weight')" @if(count($weightValues) >= $maxWeights) disabled @endif>
        <i class="fas fa-plus"></i>
    </button>
</div>
