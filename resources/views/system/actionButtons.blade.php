<div class="d-flex align-items-center gap-2 gap-lg-3">

        @foreach($actionButtons as $button)

               {{$button}}

        @endforeach

    @if(isset($model) && isset($model_id) && userCan('system.activity-log.index'))
    {{ datatable_menu_log( route('system.activity-log.index',[
    'subject_type'=>$model,
    'subject_id'=>$model_id
]) )  }}

        @endif
</div>
