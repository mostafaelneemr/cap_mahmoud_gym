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
            'system.departments.index','system.departments.edit','system.departments.create',
        ],
        'icon' => '<i class="fa fa-users"></i>',
        'text' => __('Users'),
        'sub' => [
            [
                'permission' => ['system.user.index', 'system.user.show', 'system.user.create','system.user.edit'],
                'url' => route('system.user.index'),
                'text' => __('View'),
                'icon' => '<i class="fa fa-eye"></i>',
            ],
            [
                'permission' => ['system.permission-group.index','system.permission-group.edit',
                                    'system.permission-group.create',
                ],
                'url' => route('system.permission-group.index'),
                'text' => __('Permission Group'),
                'icon' => '<i class="fa fa-lock"></i>',
            ],

        ],
    ];

    $menu['Home'] = [
        'permission' => ['system.slider.index','system.slider.create','system.slider.edit',
                        'system.choose-item.index','system.choose-item.create','system.choose-item.edit',
                        'system.testimonial.index','system.testimonial.create','system.testimonial.edit',
                        'system.blog.index','system.blog.create','system.blog.edit'],
        'icon' => '<i class="fa fa-cog"></i>',
        'text' => __('Home'),
        'sub' => [
            [
                'permission' => ['system.slider.index','system.slider.create','system.slider.edit'],
                'url' => route('system.slider.index'),
                'text' => __('Slider'),
                'icon' => ' <i class="fa fa-solid fa-magnifying-glass"></i>',
            ],

            [
                'permission' => ['system.choose-item.index','system.choose-item.create','system.choose-item.edit'],
                'url' => route('system.choose-item.index'),
                'text' => __('Items'),
                'icon' => ' <i class="fa fa-solid fa-magnifying-glass"></i>',
            ],

            [
                'permission' => ['system.testimonial.index','system.testimonial.create','system.testimonial.edit'],
                'url' => route('system.testimonial.index'),
                'text' => __('Testimonials'),
                'icon' => ' <i class="fa fa-solid fa-magnifying-glass"></i>',
            ],
            [
                'permission' => ['system.blog.index','system.blog.create','system.blog.edit'],
                'url' => route('system.blog.index'),
                'text' => __('Blogs'),
                'icon' => ' <i class="fa fa-solid fa-magnifying-glass"></i>',
            ],
        ],
    ];


    $menu['Setting'] = [

        'permission' => ['system.activity-log.index','system.activity-log.show','system.auth-sessions.index',
                        'system.language.index'],
        'icon' => '<i class="fa fa-cog"></i>',
        'text' => __('Setting'),
        'sub' => [

            [
                'permission'=> ['system.setting.index'],
                'url'=> route('system.setting.index'),
                'icon'=>'<i class="fa fa-user-tie"></i>',
                'text'=> __('setting'),
            ],

            [
                'permission'=> ['system.language.index'],
                'url'=> route('system.language.index'),
                'icon'=>'<i class="fa fa-user-tie"></i>',
                'text'=> __('Languages'),
            ],

            [
                'permission' => ['system.activity-log.index', 'system.activity-log.show'],
                'url' => route('system.activity-log.index'),
                'text' => __('Activity Log'),
                'icon' => ' <i class="fa fa-solid fa-magnifying-glass"></i>',
            ],

            [
                'permission'=> ['system.auth-sessions.index'],
                'url'=> route('system.auth-sessions.index'),
                'icon'=>'<i class="fa fa-user-tie"></i>',
                'text'=> __('Auth Sessions'),
            ],

        ],
    ];
@endphp

@foreach ($menu as $onemenu)
    {!! generateMenu($onemenu) !!}
@endforeach
