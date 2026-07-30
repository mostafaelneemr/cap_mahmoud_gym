@php
    $authUser = auth()->guard('user');
    $authTrainer = auth()->guard('trainee');
@endphp

@if($authTrainer)
    @php
        $menu['MyProgram'] = [
            'permission' => ['system.dashboard.trainer'],
            'url' => route('system.dashboard.trainer'),
            'icon' => '<i class="fa fa-dumbbell"></i>',
            'text' => __('My Workout Program'),
        ];
    @endphp

    @foreach ($menu as $onemenu)
        {!! generateMenu($onemenu) !!}
    @endforeach
@else
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
            'icon' => '<i class="fa fa-users"></i>',
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
            'permission' => ['system.workout.index','system.workout.create'],
            'icon' => '<i class="fa-solid fa-dumbbell"></i>',
            'text' => __('Workouts'),
            'sub' => [
                [
                    'permission' => ['system.workout.index','system.workout.create'],
                    'url' => route('system.workout.index'),
                    'text' => __('Workout Plans'),
                    'icon' => '<i class="fa-solid fa-clipboard-list"></i>',
                ],
            ],
        ];


        $menu['Setting'] = [

            'permission' => ['system.activity-log.index','system.activity-log.show','system.auth-sessions.index',
                            'system.language.index','system.activate.index','system.social-links.index','system.setting.index'],
            'icon' => '<i class="fa-solid fa-gear"></i>',
            'text' => __('Settings'),
            'sub' => [
                [
                    'permission'=> ['system.social-links.index'],
                    'url'=> route('system.social-links.index'),
                    'icon'=>'<i class="fa-solid fa-link"></i>',
                    'text'=> __('Social Links'),
                ],
                [
                    'permission'=> ['system.setting.index'],
                    'url'=> route('system.setting.index'),
                    'icon'=>'<i class="fa-solid fa-sliders"></i>',
                    'text'=> __('System Settings'),
                ],

//            [
//                'permission'=> ['system.language.index'],
//                'url'=> route('system.language.index'),
//                'icon'=>'<i class="fa-solid fa-language"></i>',
//                'text'=> __('Languages'),
//            ],

                [
                    'permission' => ['system.activity-log.index', 'system.activity-log.show'],
                    'url' => route('system.activity-log.index'),
                    'text' => __('Activity Log'),
                    'icon' => ' <i class="fa-solid fa-clock-rotate-left"></i>',
                ],

                [
                    'permission'=> ['system.auth-sessions.index'],
                    'url'=> route('system.auth-sessions.index'),
                    'icon'=>'<i class="fa-solid fa-shield-halved"></i>',
                    'text'=> __('Auth Sessions'),
                ],

            ],
        ];
    @endphp

    @foreach ($menu as $onemenu)
        {!! generateMenu($onemenu) !!}
    @endforeach
@endif

