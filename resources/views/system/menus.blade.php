@php
    $authUser = auth()->user();
@endphp

@if($authUser && $authUser->user_type == 2)
    {{-- ===== Trainee Menu ===== --}}
    @php
        $menu['MyProgram'] = [
            'permission' => ['system.dashboard.trainer'],
            'url' => route('system.dashboard.trainer'),
            'icon' => '<i class="fa fa-dumbbell"></i>',
            'text' => __('My Workout Program'),
        ];
        $menu['MyNutritionPlan'] = [
            'permission' => ['system.nutrition.my-plan'],
            'url' => route('system.nutrition.my-plan'),
            'icon' => '<i class="fa fa-utensils"></i>',
            'text' => __('My Nutrition Plan'),
        ];
    @endphp

    @foreach ($menu as $onemenu)
        {!! generateMenu($onemenu) !!}
    @endforeach
@else
    {{-- ===== Admin / Moderator Menu ===== --}}
    @php
        $menu['Dashboard'] = [
            'permission' => ['system.dashboard'],
            'url' => route('system.dashboard'),
            'icon' => '<i class="fa fa-tachometer-alt"></i>',
            'text' => __('Dashboard'),
        ];

        $menu['Users'] = [
            'permission' => ['system.user.index','system.user.show','system.user.create','system.user.edit',
                'system.permission-group.index','system.permission-group.edit','system.permission-group.create',
            ],
            'icon' => '<i class="fa-users"></i>',
            'text' => __('Users'),
            'sub' => [
                [
                    'permission' => ['system.user.index', 'system.user.show', 'system.user.create','system.user.edit'],
                    'url' => route('system.user.index'),
                    'text' => __('All Users'),
                    'icon' => '<i class="fa-solid fa-user-group"></i>',
                ],
                [
                    'permission' => ['system.permission-group.index','system.permission-group.edit','system.permission-group.create',],
                    'url' => route('system.permission-group.index'),
                    'text' => __('Permission Groups'),
                    'icon' => '<i class="fa-solid fa-user-shield"></i>',
                ],

            ],
        ];

        $menu['Trainee'] = [
            'permission' => ['system.trainee.index','system.trainee.create','system.trainee.edit'],
            'icon' => '<i class="fa-solid fa-person-running"></i>',
            'text' => __('Trainees'),
            'sub' => [
                [
                    'permission' => ['system.trainee.index','system.trainee.create','system.trainee.edit'],
                    'url' => route('system.trainee.index'),
                    'text' => __('All Trainees'),
                    'icon' => '<i class="fa-solid fa-list-ul"></i>',
                ],
            ],
        ];

        $menu['Workout'] = [
            'permission' => ['system.workout.index','system.workout.create',
            'system.nutrition.index','system.nutrition.create'],
            'icon' => '<i class="fa-solid fa-dumbbell"></i>',
            'text' => __('Plans'),
            'sub' => [
                [
                    'permission' => ['system.workout.index','system.workout.create'],
                    'url' => route('system.workout.index'),
                    'text' => __('Workout Plans'),
                    'icon' => '<i class="fa-solid fa-clipboard-list"></i>',
                ],
                [
                    'permission' => ['system.nutrition.index','system.nutrition.create'],
                    'url' => route('system.nutrition.index'),
                    'text' => __('Nutrition Plans'),
                    'icon' => '<i class="fa-solid fa-apple-whole"></i>',
                ],
            ],
        ];

        $menu['Website'] = [
            'permission' => ['system.website.index','system.website.create'],
            'icon' => '<i class="fa-solid fa-Global"></i>',
            'text' => __('Website'),
            'sub' => [
                [
                    'permission' => ['system.website.index'],
                    'url' => route('system.website.index'),
                    'text' => __('website Pages'),
                    'icon' => '<i class="fa-solid fa-list-ul"></i>',
                ],
            ],
        ];

        $menu['Setting'] = [
            'permission' => ['system.activity-log.index','system.activity-log.show','system.auth-sessions.index',
                            'system.language.index','system.activate.index', 'system.setting.index'],
            'icon' => '<i class="fa-solid fa-gear"></i>',
            'text' => __('Settings'),
            'sub' => [
                [
                    'permission' => ['system.setting.index'],
                    'url' => route('system.setting.index'),
                    'icon' => '<i class="fa-solid fa-sliders"></i>',
                    'text' => __('System Settings'),
                ],
                [
                    'permission' => ['system.activity-log.index', 'system.activity-log.show'],
                    'url' => route('system.activity-log.index'),
                    'text' => __('Activity Log'),
                    'icon' => '<i class="fa-solid fa-clock-rotate-left"></i>',
                ],
                [
                    'permission' => ['system.auth-sessions.index'],
                    'url' => route('system.auth-sessions.index'),
                    'icon' => '<i class="fa-solid fa-shield-halved"></i>',
                    'text' => __('Auth Sessions'),
                ],
            ],
        ];
    @endphp

    @foreach ($menu as $onemenu)
        {!! generateMenu($onemenu) !!}
    @endforeach
@endif
