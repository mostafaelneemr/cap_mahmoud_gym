@extends('system.layout')

@section('content')
    @include('system.datatable')

    <div class="modal fade" id="modal-details" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-body" id="show_data">

                </div>
                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
                </div>

            </div>
        </div>
    </div>

@endsection
