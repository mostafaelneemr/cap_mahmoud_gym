@if (in_array($status, ['enable', 'active', '1','true','yes']))
    <i class="ki-duotone ki-check-square text-success fs-1">
        <span title=" {{ __('active') }}" class="path1"></span><span class="path2"></span></i>
@elseif (in_array($status, ['disable', 'inactive', '0','false','no']))
    <i title=" {{ __('in-active') }}" class="ki-duotone ki-cross-square text-danger fs-1" style="display: inline">
        <span class="path1"></span><span class="path2"></span></i>
@else
    --
@endif
