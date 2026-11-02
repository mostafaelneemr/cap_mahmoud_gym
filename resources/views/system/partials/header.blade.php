<!--begin::Header-->
<div id="kt_app_header" class="app-header d-flex flex-column flex-stack">
    <!--begin::Header main-->
    <div class="d-flex flex-stack flex-grow-1" style="padding: 10px 0;
        @if(env('APP_ENV') == 'local') background-color: #FACC15; @elseif(env('APP_ENV') == 'test') background-color: #FACC15; @endif ">
        <div class="app-header-logo d-flex align-items-center ps-lg-12" id="kt_app_header_logo">
            <!--begin::Sidebar toggle-->
            <div id="kt_app_sidebar_toggle"
                class="app-sidebar-toggle btn btn-sm btn-icon bg-body btn-color-gray-500 btn-active-color-primary w-30px h-30px ms-n2 me-4 d-none d-lg-flex"
                data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
                data-kt-toggle-name="app-sidebar-minimize">
                <i class="ki-outline ki-abstract-14 fs-3 mt-1"></i>
            </div>
            <!--end::Sidebar toggle-->
            <!--begin::Sidebar mobile toggle-->
            <div class="btn btn-icon btn-active-color-primary w-35px h-35px ms-3 me-2 d-flex d-lg-none"
                id="kt_app_sidebar_mobile_toggle">
                <i class="ki-outline ki-abstract-14 fs-2"></i>
            </div>
            <!--end::Sidebar mobile toggle-->
            <!--begin::Logo-->

            <a href="{{route('system.dashboard')}}" class="brand-logo">
                <img alt="Logo" src="{{asset('logo/logo-white.jpeg')}}" class="theme-light-show" style="width:auto;height: 40px" />
                <img alt="Logo" src="{{asset('logo/logo-dark.jpg')}}" class="theme-dark-show" style="width:auto;height: 40px" />
            </a>
            <!--end::Logo-->
        </div>
        <!--begin::Navbar-->
        <div class="app-navbar flex-grow-1 justify-content-end" id="kt_app_header_navbar">
            <div class="app-navbar-item d-flex align-items-stretch flex-lg-grow-1">
                <!--begin::Search-->
                <div id="kt_header_search" class="header-search d-flex align-items-center w-lg-350px"
                    data-kt-search-keypress="true" data-kt-search-min-length="2" data-kt-search-enter="enter"
                    data-kt-search-layout="menu" data-kt-search-responsive="true" data-kt-menu-trigger="auto"
                    data-kt-menu-permanent="true" data-kt-menu-placement="bottom-start">
                    <!--begin::Tablet and mobile search toggle-->
                    <div data-kt-search-element="toggle"
                        class="search-toggle-mobile d-flex d-lg-none align-items-center">
                        <div class="d-flex">
                            <i class="ki-outline ki-magnifier fs-1 fs-1"></i>
                        </div>
                    </div>
                    <!--end::Tablet and mobile search toggle-->
                    <!--begin::Menu-->
                    <div data-kt-search-element="content"
                        class="menu menu-sub menu-sub-dropdown py-7 px-7 overflow-hidden w-300px w-md-350px">
                        <!--begin::Wrapper-->
                        <div data-kt-search-element="wrapper">
                            <div class="" data-kt-search-element="main">
                                <!--begin::Heading-->
                                <div class="d-flex flex-stack fw-semibold mb-4">
                                    <!--begin::Label-->
                                    <span class="text-muted fs-6 me-2">{{__('Quick Search')}}</span>
                                    <!--end::Label-->

                                </div>
                                <!--end::Heading-->
                                <!--begin::Items-->
                                <div class="scroll-y mh-200px mh-lg-325px">
                                    <div class="d-flex flex-wrap justify-content-between">
                                        <div class="mb-5 col-5">
                                            <input type="number" autofocus id="quick_search_id"
                                                oninput="$('#quick_search_name').val('')"
                                                class="form-control form-control-sm form-control-solid @if(lang() =='ar') search_id @endif"
                                                placeholder="{{__('ID')}}" name="quick_search_id" style="" />
                                        </div>
                                        <div class="mb-5 col-6">
                                            <input type="text" id="quick_search_name"
                                                oninput="$('#quick_search_id').val('')"
                                                class="form-control form-control-sm form-control-solid @if(lang() =='ar') search_id @endif"
                                                placeholder="{{__('Reference ID')}}" name="quick_search_name" />
                                        </div>
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="mb-5">
                                        <!--begin::Radio group-->
                                        <div class="nav-group nav-group-fluid">
                                            <!--begin::Option-->
                                            <label>
                                                <input type="radio" class="btn-check" name="quick_search_type"
                                                    value="trainee" checked="checked" />
                                                <span
                                                    class="btn btn-sm btn-color-muted btn-active btn-active-primary">{{__('Trainee')}}</span>
                                            </label>
                                            <label>
                                                <input type="radio" class="btn-check" name="quick_search_type"
                                                    value="user" />
                                                <span
                                                    class="btn btn-sm btn-color-muted btn-active btn-active-primary">{{__('User')}}</span>
                                            </label>
                                            <!--end::Option-->

                                        </div>
                                        <!--end::Radio group-->
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <a onclick="QuickSearch()"
                                            class="btn btn-sm fw-bold btn-primary">{{__('Search')}}</a>
                                    </div>
                                </div>
                                <!--end::Items-->
                            </div>
                        </div>
                    </div>
                    <!--end::Menu-->


                </div>
                <!--end::Search-->
            </div>
            {{-- icon Global To Site --}}
            <div class="topbar-item">
                <div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1">
                    <span class="svg-icon svg-icon-xl svg-icon-primary">
                        <a href="#" target="_blank">
                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="9" />
                                        <path
                                            d="M11.7357634,20.9961946 C6.88740052,20.8563914 3,16.8821712 3,12 C3,11.9168367 3.00112797,11.8339369 3.00336944,11.751315 C3.66233009,11.8143341 4.85636818,11.9573854 4.91262842,12.4204038 C4.9904938,13.0609191 4.91262842,13.8615942 5.45804656,14.101772 C6.00346469,14.3419498 6.15931561,13.1409372 6.6267482,13.4612567 C7.09418079,13.7815761 8.34086797,14.0899175 8.34086797,14.6562185 C8.34086797,15.222396 8.10715168,16.1034596 8.34086797,16.2636193 C8.57458427,16.423779 9.5089688,17.54465 9.50920913,17.7048097 C9.50956962,17.8649694 9.83857487,18.6793513 9.74040201,18.9906563 C9.65905192,19.2487394 9.24857641,20.0501554 8.85059781,20.4145589 C9.75315358,20.7620621 10.7235846,20.9657742 11.7357634,20.9960544 L11.7357634,20.9961946 Z M8.28272988,3.80112099 C9.4158415,3.28656421 10.6744554,3 12,3 C15.5114513,3 18.5532143,5.01097452 20.0364482,7.94408274 C20.069657,8.72412177 20.0638332,9.39135321 20.2361262,9.6327358 C21.1131932,10.8600506 18.0995147,11.7043158 18.5573343,13.5605384 C18.7589671,14.3794892 16.5527814,14.1196773 16.0139722,14.886394 C15.4748026,15.6527403 14.1574598,15.137809 13.8520064,14.9904917 C13.546553,14.8431744 12.3766497,15.3341497 12.4789081,14.4995164 C12.5805657,13.664636 13.2922889,13.6156126 14.0555619,13.2719546 C14.8184743,12.928667 15.9189236,11.7871741 15.3781918,11.6380045 C12.8323064,10.9362407 11.963771,8.47852395 11.963771,8.47852395 C11.8110443,8.44901109 11.8493762,6.74109366 11.1883616,6.69207022 C10.5267462,6.64279981 10.170464,6.88841096 9.20435656,6.69207022 C8.23764828,6.49572949 8.44144409,5.85743687 8.2887174,4.48255778 C8.25453994,4.17415686 8.25619136,3.95717082 8.28272988,3.80112099 Z M20.9991771,11.8770357 C20.9997251,11.9179585 21,11.9589471 21,12 C21,16.9406923 17.0188468,20.9515364 12.0895088,20.9995641 C16.970233,20.9503326 20.9337111,16.888438 20.9991771,11.8770357 Z"
                                            fill="#000000" opacity="0.3" />
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                        </a>
                    </span>
                </div>
            </div>


                        <!--begin::Notifications-->
            <div class="app-navbar-item ms-1 ms-lg-3" id="kt_header_notification_item">
                <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px w-md-40px h-md-40px position-relative"
                    data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                    data-kt-menu-attach="parent"
                    data-kt-menu-placement="bottom-end"
                    id="kt_header_notification_toggle">
                    <i class="ki-outline ki-notification-status fs-2"></i>
                    <span id="notification-badge" class="position-absolute top-0 start-100 translate-middle badge badge-circle badge-danger fs-8 fw-bold d-none" style="min-width: 18px; height: 18px; padding: 2px 4px;">0</span>
                </div>

                <!--begin::Menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px shadow-sm" data-kt-menu="true" id="kt_menu_notifications">
                    <!--begin::Heading-->
                    <div class="d-flex flex-stack bgi-no-repeat bgi-size-cover rounded-top p-4 bg-primary text-white">
                        <h3 class="text-white fw-semibold mb-0 fs-5 d-flex align-items-center">
                            <i class="ki-outline ki-notification-status fs-4 me-2 text-white"></i>
                            {{ __('Notifications') }}
                            <span id="notification-header-count" class="badge badge-sm badge-light-warning ms-2 fs-8">0</span>
                        </h3>
                        <button type="button" id="btn-mark-all-read" class="btn btn-sm btn-link text-white text-hover-white p-0 fs-8 fw-semibold" style="text-decoration: underline;">
                            {{ __('Mark all as read') }}
                        </button>
                    </div>
                    <!--end::Heading-->

                    <!--begin::Items-->
                    <div id="notification-dropdown-list" class="scroll-y mh-325px my-2 px-4 py-2">
                        <div class="text-center py-6 text-muted fs-7" id="notification-loading">
                            <span class="spinner-border spinner-border-sm text-primary me-2"></span> {{ __('Loading...') }}
                        </div>
                    </div>
                    <!--end::Items-->

                    <!--begin::View more-->
                    @if(auth()->user() && auth()->user()->user_type != 2)
                    <div class="py-3 text-center border-top">
                        <a href="{{ route('system.notifications.index') }}" class="btn btn-color-gray-600 btn-active-color-primary fs-7 fw-semibold">
                            {{ __('View all notifications') }}
                            <i class="ki-outline ki-arrow-right fs-6 ms-1"></i>
                        </a>
                    </div>
                    @endif
                    <!--end::View more-->
                </div>
                <!--end::Menu-->
            </div>
            <!--end::Notifications-->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdownUrl = "{{ route('system.notifications.dropdown') }}";
    const readAllUrl = "{{ route('system.notifications.read-all') }}";
    const csrfToken = "{{ csrf_token() }}";
    const badgeEl = document.getElementById('notification-badge');
    const headerCountEl = document.getElementById('notification-header-count');
    const listEl = document.getElementById('notification-dropdown-list');
    const markAllBtn = document.getElementById('btn-mark-all-read');

    function updateBadge(count) {
        if (!badgeEl || !headerCountEl) return;
        headerCountEl.textContent = count;
        if (count > 0) {
            badgeEl.textContent = count > 99 ? '99+' : count;
            badgeEl.classList.remove('d-none');
        } else {
            badgeEl.textContent = '0';
            badgeEl.classList.add('d-none');
        }
    }

    function renderNotifications(items, unreadCount) {
        updateBadge(unreadCount);
        if (!listEl) return;

        if (!items || items.length === 0) {
            listEl.innerHTML = `
                <div class="text-center py-8 text-muted">
                    <i class="ki-outline ki-notification fs-2x mb-2 text-gray-400 d-block"></i>
                    <div class="fs-7 fw-semibold">{{ __('No notifications yet') }}</div>
                </div>`;
            return;
        }

        let html = '';
        items.forEach(item => {
            const unreadClass = !item.is_read ? 'bg-light-primary border-primary' : 'bg-hover-light';
            const dot = !item.is_read ? '<span class="badge badge-dot badge-primary me-2"></span>' : '';
            const actionUrl = item.action_url ? item.action_url : item.read_url;

            html += `
                <a href="${actionUrl}" class="d-flex align-items-center p-3 mb-2 rounded border border-dashed text-dark text-decoration-none ${unreadClass}">
                    <div class="symbol symbol-35px me-3">
                        <span class="symbol-label bg-light-primary text-primary">
                            <i class="ki-outline ${item.icon || 'ki-notification-status'} fs-3"></i>
                        </span>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 min-w-0">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="fw-bold fs-7 text-truncate">${item.title}</span>
                            ${dot}
                        </div>
                        <div class="text-muted fs-8 text-truncate mb-1">${item.message}</div>
                        <span class="text-gray-400 fs-9">${item.created_at_human}</span>
                    </div>
                </a>
            `;
        });
        listEl.innerHTML = html;
    }

    function fetchNotifications() {
        fetch(dropdownUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                renderNotifications(data.notifications, data.unread_count);
            }
        })
        .catch(err => console.error('Error fetching notifications:', err));
    }

    if (markAllBtn) {
        markAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            fetch(readAllUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    updateBadge(0);
                    fetchNotifications();
                }
            })
            .catch(err => console.error('Error marking notifications as read:', err));
        });
    }

    fetchNotifications();
    setInterval(fetchNotifications, 60000);
});
</script>

            <div class="app-navbar-item ms-2 ms-lg-6" id="kt_header_user_menu_toggle">
                <!--begin::Menu wrapper-->
                <div class="cursor-pointer symbol symbol-circle symbol-30px symbol-lg-45px"
                    data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
                    data-kt-menu-placement="bottom-end">
                    <img src="assets/media/avatars/blank.png" alt="user" />
                </div>
                <!--begin::User account menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
                    data-kt-menu="true">
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <div class="menu-content d-flex align-items-center px-3">
                            <!--begin::Avatar-->
                            <div class="symbol symbol-50px me-5">
                                <img alt="Logo" src="assets/media/avatars/blank.png" />
                            </div>
                            <!--end::Avatar-->
                            <!--begin::Username-->
                            <div class="d-flex flex-column">
                                <div class="fw-bold d-flex align-items-center fs-5">{{auth()->user()->name}}</div>
                                <div class="fw-bold d-flex align-items-center fs-5"><span
                                        class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2">{{auth()->user()->permission_group?->name ?? ''}}</span>
                                </div>

                                <a class="fw-semibold text-muted text-hover-primary fs-7">{{auth()->user()->email}}</a>
                            </div>
                            <!--end::Username-->
                        </div>
                    </div>
                    <!--end::Menu item-->

                    <!--begin::Menu item-->
                    <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                        data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                        <a href="#" class="menu-link px-5">
                            <span class="menu-title position-relative">{{__('Mode')}}
                                <span class="ms-5 position-absolute translate-middle-y top-50 end-0">
                                    <i class="ki-outline ki-night-day theme-light-show fs-2"></i>
                                    <i class="ki-outline ki-moon theme-dark-show fs-2"></i>
                                </span></span>
                        </a>
                        <!--begin::Menu-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px"
                            data-kt-menu="true" data-kt-element="theme-mode-menu">
                            <!--begin::Menu item-->
                            <div class="menu-item px-3 my-0">
                                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                                    <span class="menu-icon" data-kt-element="icon">
                                        <i class="ki-outline ki-night-day fs-2"></i>
                                    </span>
                                    <span class="menu-title">{{__('Light')}}</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-3 my-0">
                                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                                    <span class="menu-icon" data-kt-element="icon">
                                        <i class="ki-outline ki-moon fs-2"></i>
                                    </span>
                                    <span class="menu-title">{{__('Dark')}}</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                        </div>
                        <!--end::Menu-->
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                        data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                        <a href="#" class="menu-link px-5">
                            <span class="menu-title position-relative">{{__('Language')}}
                                <span
                                    class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">
                                    @if(lang() == 'ar') عربي @else English @endif

                                    <img class="w-15px h-15px rounded-1 ms-2" @if(lang()=='ar' )
                                        src="assets/media/misc/saudi-arabia.svg" @else
                                        src="assets/media/misc/united-states.svg" @endif alt="" />

                                </span></span>
                        </a>
                        <!--begin::Menu sub-->
                        <div class="menu-sub menu-sub-dropdown w-175px py-4">


                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <a href="{{url()->current()}}?lang=ar&backByLanguage=true"
                                    class="menu-link d-flex px-5 @if(lang() == 'ar') active @endif">
                                    <span class="symbol symbol-20px me-4">
                                        <img class="rounded-1" src="assets/media/misc/saudi-arabia.svg" alt="" />
                                    </span>عربي</a>
                            </div>
                            <!--end::Menu item-->

                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <a href="{{url()->current()}}?lang=en-gb&backByLanguage=true"
                                    class="menu-link d-flex px-5 @if(lang() == 'en-gb') active @endif">
                                    <span class="symbol symbol-20px me-4">
                                        <img class="rounded-1" src="assets/media/misc/united-states.svg" alt="" />
                                    </span>English</a>
                            </div>
                            <!--end::Menu item-->

                        </div>
                        <!--end::Menu sub-->
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    @if(auth()->user()->user_type == 1 || auth()->user()->user_type == null)
                    <div class="menu-item px-5 my-1">
                        <a href="{{route('system.user.show-profile')}}" class="menu-link px-5">{{__('Profile')}}</a>
                    </div>

                    <div class="menu-item px-5 my-1">
                        <a href="{{route('system.user.user-sessions')}}" class="menu-link px-5">{{__('Auth Sessions')}}</a>
                    </div>
                    @endif
                    <!--end::Menu item-->

                </div>
                <!--end::User account menu-->
                <!--end::Menu wrapper-->
            </div>
            <!--end::User menu-->
            <!--begin::Action-->
            <div class="app-navbar-item ms-2 ms-lg-6 me-lg-6">
                <!--begin::Link-->
                <a href="{{route('logout')}}" title="{{__('Logout')}}"
                    class="btn btn-icon btn-custom btn-color-gray-600 btn-active-color-primary w-35px h-35px w-md-40px h-md-40px">
                    <i class="ki-outline ki-exit-{{ lang() == 'ar'?'left':'right'  }} fs-1"></i>
                </a>
                <!--end::Link-->
            </div>
            <!--end::Action-->

        </div>
        <!--end::Navbar-->
    </div>
    <!--end::Header main-->
    {{--
    <!--begin::Separator-->--}}
    {{-- <div class="app-header-separator"></div>--}}
    {{--
    <!--end::Separator-->--}}
</div>
<!--end::Header-->
