@extends('system.layout')

@section('content')
    @include('system.message.filter')
    @include('system.datatable')
@endsection

@section('footer')

    <script>
        $(document).ready(function(){
            $('#message_read').val('no').change();
        })

        function submitForm(){
            $('#filterForm').submit();
        }
    </script>
@endsection
