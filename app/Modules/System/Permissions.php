<?php

return [

    [
        'group_title' => __('Users'),
        'name' => __('Users'),
        'permissions' => [
            'view-all-user' => ['system.user.index', 'system.user.show', 'system.get-user-activity-log'],
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
        'name' => __('Trainee'),
        'permissions' => [
            'view-all-trainees' => ['system.trainee.index'],
            'show-trainee' => ['system.trainee.show', 'system.trainee.get-activity-log', 'system.trainee.get-auth-session', 'system.trainee.get-workout'],
            'create-trainee' => ['system.trainee.create', 'system.trainee.store'],
            'update-trainee' => ['system.trainee.edit', 'system.trainee.update', 'system.trainee.reset-plan'],
            'show-trainee-dashboard' => ['system.dashboard.trainer'],
        ]
    ],

    [
        'name' => __('Workout'),
        'permissions' => [
            'view-all-workouts' => ['system.workout.index'],
            'create-workout' => ['system.workout.create', 'system.workout.store'],
            'update-workout' => ['system.workout.edit', 'system.workout.update', 'system.workout.updateDay'],
            'delete-workout' => ['system.workout.destroy'],
        ]
    ],

    [
        'name' => __('Nutrition'),
        'permissions' => [
            'view-all-nutrition' => ['system.nutrition.index'],
            'create-nutrition' => ['system.nutrition.create', 'system.nutrition.store'],
            'show-nutrition' => ['system.nutrition.show'],
            'delete-nutrition' => ['system.nutrition.destroy'],
            'show-my-nutrition' => ['system.nutrition.my-plan'],
        ]
    ],

    [
        'group_title' => __('Setting'),
        'name' => __('Social Links'),
        'permissions' => [
            'view-links' => ['system.social-links.index'],
            'add-links' => ['system.social-links.create', 'system.social-links.store'],
            'update-links' => ['system.social-links.edit', 'system.social-links.update'],
            'delete-links' => ['system.social-links.destroy'],
        ]
    ],

    [
        'group_title' => __('Join US'),
        'name' => __('Join US'),
        'permissions' => [
            'view-join-us' => ['system.join-us.index'],
            'view-one-join-us' => ['system.join-us.show'],
            'update-join-us-status' => ['system.join-us.update-status'],
//            'delete-links' => ['system.social-links.destroy'],
        ]
    ],

    [
        'group_title' => __('Messages'),
        'name' => __('Messages'),
        'permissions' => [
            'view-messages' => ['system.message.index'],
            'update-message-status' => ['system.message.update-status'],
        ]
    ],

    [
        'name' => __('website'),
        'permissions' => [
            'view-website-pages' => [
                'system.website.index',
                'system.website.show',
            ],
            'manage-website-settings' => [
                'system.website.settings.update',
                'system.website.section.update',
            ],
            'manage-website-items' => [
                'system.website.items.store',
                'system.website.items.update',
                'system.website.items.destroy',
                'system.website.posts.store',
                'system.website.posts.update',
                'system.website.posts.destroy',
            ],
        ]
    ],

    [
        'name' => __('Setting'),
        'permissions' => [
            'view-setting' => ['system.setting.index', 'system.setting.update'],
        ]
    ],

    [
        'name' => __('Activate Sections'),
        'permissions' => [
            'view-activate-sections' => ['system.activate.index', 'system.activate.update'],
        ]
    ],

    [
        'name' => __('Activity Log'),
        'permissions' => [
            'view-activity-log' => ['system.activity-log.index', 'system.activity-log.show'],
            'view-log-viewer' => ['log-viewer.index'],
        ]
    ],

    [
        'name' => __('Auth Sessions'),
        'permissions' => [
            'view-auth-session' => ['system.auth-sessions.index', 'system.get-auth-session', 'system.auth-sessions.show'],
            'delete-auth-session' => ['system.auth-sessions.destroy'],
        ]
    ],

];
