@php
    if (!isset($array['url'])) {
        $array['url'] = '#';
    }

    if (!isset($array['icon'])) {
        $array['icon'] = null;
    }

    if (!isset($array['class'])) {
        $array['class'] = null;
    }

    if (!isset($array['aClass'])) {
        $array['aClass'] = null;
    }

    if (!empty($array['permission'])) {
        if (is_array($array['permission'])) {
            $oneTrue = false;
            foreach ($array['permission'] as $key => $value) {
                if (userCan($value)) {
                    $oneTrue = true;
                    break;
                }
            }

            if (!$oneTrue) {
                return false;
            }
        } else {
            if (!userCan($array['permission'])) {
                return false;
            }
        }
    }

    if (isset($array['permission'])) {
        if (!userCan($array['permission'])) {
            return false;
        }
    }

    $should_open = '';
    if (isset($array['permission']) && MenuRoute($array['permission'])) {
        $array['class'] .= ' active';
        $should_open = 'hover show';
    }
@endphp
@if (isset($array['sub']))
    <div data-kt-menu-trigger="click" class="menu-item here {!! $should_open !!} menu-accordion">


        <!--begin:Menu link-->
        <span class="menu-link ">
            <span class="menu-icon">
                {!! $array['icon'] !!}
            </span>
            <span class="menu-title"> {!! trans($array['text']) !!}</span>
            <span class="menu-arrow"></span>
        </span>
        <!--end:Menu link-->

        <!--begin:Menu sub-->
        <div class="menu-sub menu-sub-accordion">

            <!--begin:Menu item-->
            <div data-kt-menu-trigger="click" class="menu-item here {!! $should_open !!} menu-accordion">
                @if (isset($array['sub']) && !empty($array['sub']))

                    @foreach ($array['sub'] as $key => $value)
                        @include('system.partials.divs.menu', ['array' => $value])
                    @endforeach
                    @endif
            </div>
        </div>
    </div>

<!--begin:Menu link-->
@else
@php
    $styling = url()->current() == $array['url'] ? 'style="color: #3E97FF;"' : '';
@endphp
@if (empty($array['icon']))
@endif
<div class="menu-item">
    <!--begin::Menu link-->
    <a class="menu-link {!! $array['class'] !!}" href="{!! $array['url'] !!}">
        <span class="menu-icon">{!! $array['icon'] !!}</span>
        <!--begin::Title-->
        <span class="menu-title text-gray-700 fw-bold fs-6">
            {!! trans($array['text']) !!} </span>
        <!--end::Title-->
    </a>
    <!--end::Menu link-->
</div>
@endif
