    <?php

return [

    [
        'group_title' => __('Home'),
        'name' => __('Slider'),
        'permissions' => [
            'view-slider' => ['system.slider.index'],
            'create-slider' => ['system.slider.create', 'system.slider.store'],
            'update-slider' => ['system.slider.edit', 'system.slider.update']
        ]
    ],

    [
        'name' => __('Choose Item'),
        'permissions' => [
            'view-all-items' => ['system.choose-item.index'],
            'create-item' => ['system.choose-item.create', 'system.choose-item.store'],
            'update-item' => ['system.choose-item.edit', 'system.choose-item.update']
        ]
    ],

    [
        'name' => __('Testimonials'),
        'permissions' => [
            'view-all-testimonials' => ['system.testimonial.index'],
            'create-testimonial' => ['system.testimonial.create', 'system.testimonial.store'],
            'update-testimonial' => ['system.testimonial.edit', 'system.testimonial.update']
        ]
    ],

    [
        'group_title' => __('Users'),
        'name' => __('Users'),
        'permissions' => [
            'view-all-user' => ['system.user.index','system.user.show', 'system.get-user-activity-log'],
            'create-user' => ['system.user.create', 'system.user.store'],
            'update-user' => ['system.user.edit', 'system.user.update']
        ]
    ],


    [
        'name' => __('Permission Group'),
        'permissions' => [
            'view-all-permission-groups' => ['system.permission-group.index'],
            'create-permission-group' => ['system.permission-group.create', 'system.permission-group.store'],
            'update-permission-group' => ['system.permission-group.edit', 'system.permission-group.update']
        ]
    ],

    [
        'group_title' => __('Setting'),
        'name' => __('Activity Log'),
        'permissions' => [
            'view-activity-log'=>['system.activity-log.index','system.activity-log.show'],
            'view-log-viewer'=>[ 'log-viewer.index'],
            'git-version-control'=>[ 'system.git-version-control'],
            'view-shipping-log'=>['system.shipping-log.index','system.shipping-log.show'],

        ]
    ],

    [
        'name' => __('Language'),
        'permissions' => [
            'view-language' => ['system.language.index'],
            'create-language' => ['system.language.create','system.language.store'],
            'update-language' => ['system.language.edit','system.language.update'],

        ]
    ],

    [
        'name' => __('Setting'),
        'permissions' => [
            'view-setting'=>['system.setting.index','system.setting.update'],
        ]
    ],

    [
        'name' => __('Auth Sessions'),
        'permissions' => [
            'view-auth-session' => ['system.auth-sessions.index', 'system.get-auth-session', 'system.auth-sessions.show'],
            'delete-auth-session' => ['system.auth-sessions.destroy'],
            'view-log-viewer'=>[ 'log-viewer.index'],
        ]
    ],


];
