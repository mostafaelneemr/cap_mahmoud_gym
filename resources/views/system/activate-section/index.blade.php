@extends('system.layout')

@section('content')

    <h4 class="text-center text-muted mt-2">{{__('Activate Site Sections')}}</h4>
    <div class="row">

        <div class="col-lg-3 mb-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 h6 text-center">{{__('Testimonial Section')}}</h3>
                </div>
                <div class="form-check form-switch">
                    <label class="form-check-label col-lg-3 col-sm-12 text-right" for="testimonial_section">
                        <input type="checkbox" class="form-check-input" id="testimonial_section" data-switch="1"
                            onchange="updateSettings(this, 'testimonial_section')"
                            {{ active_section('testimonial_section') ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>

        {{--        --}}
        {{--        <div class="col-lg-3 mb-2">--}}
        {{--            <div class="card">--}}
        {{--                <div class="card-header">--}}
        {{--                    <h3 class="mb-0 h6 text-center">{{__('Blogs Section')}}</h3>--}}
        {{--                </div>--}}

        {{--                <div class="form-group row switch switch-outline switch-icon switch-primary text-center">--}}
        {{--                    <label class="col-form-label text-right col-lg-3 col-sm-12">--}}
        {{--                        <input data-switch="1" type="checkbox" onchange="updateSettings(this, 'blog_section')" <?php if (\App\Models\admin\Active_section::where('name', 'blog_section')->first()->value == 1) echo "checked"; ?>>--}}
        {{--                        <span class="slider round"></span>--}}
        {{--                    </label>--}}
        {{--                </div>--}}
        {{--            </div>--}}
        {{--        </div>--}}

    </div>

    {{--    <h4 class="text-center text-muted mt-2">{{__('Activate Pages ')}}</h4>--}}
    {{--    <div class="row">--}}

    {{--        <div class="col-lg-6 mb-2">--}}
    {{--            <div class="card">--}}
    {{--                <div class="card-header">--}}
    {{--                    <h3 class="mb-0 h6 text-center">{{__('About Page')}}</h3>--}}
    {{--                </div>--}}

    {{--                <div class="form-group row switch switch-outline switch-icon switch-success text-center">--}}
    {{--                    <label class="col-form-label text-right col-lg-3 col-sm-12">--}}
    {{--                        <input data-switch="1" type="checkbox" onchange="updateSettings(this, 'about_page')" <?php if (\App\Models\admin\Active_section::where('name', 'about_page')->first()->value == 1) echo "checked"; ?>>--}}
    {{--                        <span class="slider round"></span>--}}
    {{--                    </label>--}}
    {{--                </div>--}}
    {{--            </div>--}}
    {{--        </div>--}}

    {{--        <div class="col-lg-6 mb-2">--}}
    {{--            <div class="card">--}}
    {{--                <div class="card-header">--}}
    {{--                    <h3 class="mb-0 h6 text-center">{{__('service Page')}}</h3>--}}
    {{--                </div>--}}

    {{--                <div class="form-group row switch switch-outline switch-icon switch-success text-center">--}}
    {{--                    <label class="col-form-label text-right col-lg-3 col-sm-12">--}}
    {{--                        <input data-switch="1" type="checkbox" onchange="updateSettings(this, 'service_page')" <?php if (\App\Models\admin\Active_section::where('name', 'service_page')->first()->value == 1) echo "checked"; ?>>--}}
    {{--                        <span class="slider round"></span>--}}
    {{--                    </label>--}}
    {{--                </div>--}}
    {{--            </div>--}}
    {{--        </div>--}}

    {{--        <div class="col-lg-6 mb-2">--}}
    {{--            <div class="card">--}}
    {{--                <div class="card-header">--}}
    {{--                    <h3 class="mb-0 h6 text-center">{{__('Blog Page')}}</h3>--}}
    {{--                </div>--}}

    {{--                <div class="form-group row switch switch-outline switch-icon switch-success text-center">--}}
    {{--                    <label class="col-form-label text-right col-lg-3 col-sm-12">--}}
    {{--                        <input data-switch="1" type="checkbox" onchange="updateSettings(this, 'blog_page')" <?php if (\App\Models\admin\Active_section::where('name', 'blog_page')->first()->value == 1) echo "checked"; ?>>--}}
    {{--                        <span class="slider round"></span>--}}
    {{--                    </label>--}}
    {{--                </div>--}}
    {{--            </div>--}}
    {{--        </div>--}}

    {{--        <div class="col-lg-6 mb-2">--}}
    {{--            <div class="card">--}}
    {{--                <div class="card-header">--}}
    {{--                    <h3 class="mb-0 h6 text-center">{{__('Project Page')}}</h3>--}}
    {{--                </div>--}}

    {{--                <div class="form-group row switch switch-outline switch-icon switch-success text-center">--}}
    {{--                    <label class="col-form-label text-right col-lg-3 col-sm-12">--}}
    {{--                        <input data-switch="1" type="checkbox" onchange="updateSettings(this, 'project_page')" <?php if (\App\Models\admin\Active_section::where('name', 'project_page')->first()->value == 1) echo "checked"; ?>>--}}
    {{--                        <span class="slider round"></span>--}}
    {{--                    </label>--}}
    {{--                </div>--}}
    {{--            </div>--}}
    {{--        </div>--}}

    {{--        <div class="col-lg-6 mb-2">--}}
    {{--            <div class="card">--}}
    {{--                <div class="card-header">--}}
    {{--                    <h3 class="mb-0 h6 text-center">{{__('Career Page')}}</h3>--}}
    {{--                </div>--}}

    {{--                <div class="form-group row switch switch-outline switch-icon switch-success text-center">--}}
    {{--                    <label class="col-form-label text-right col-lg-3 col-sm-12">--}}
    {{--                        <input data-switch="1" type="checkbox" onchange="updateSettings(this, 'career_page')" <?php if (\App\Models\admin\Active_section::where('name', 'career_page')->first()->value == 1) echo "checked"; ?>>--}}
    {{--                        <span class="slider round"></span>--}}
    {{--                    </label>--}}
    {{--                </div>--}}
    {{--            </div>--}}
    {{--        </div>--}}

    {{--    </div>--}}

@endsection

@section('footer')

    <script type="text/javascript">
        $('.c-preloader').hide();

        function updateSettings(el, type) {
            console.log(el, type)
            var value = $(el).is(':checked') ? "active" : "inactive";

            var id = type;

            $.post('{{ route('system.activate.update', ['id' => ':id']) }}'.replace(':id', id), {
                _token: '{{ csrf_token() }}',
                name: type,
                value: value
            }, function (data) {
                if (value == 1) {
                    toastr.success(data.message, 'Success Active!', {"closeButton": true});
                } else {
                    toastr.warning(data.message, 'Success DeActive !', {"closeButton": true});
                }
            });
        }


    </script>

@endsection
