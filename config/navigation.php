<?php

return [
    'default' => [
        ['route' => 'dashboard',           'label' => 'Dashboard'],
        ['route' => 'learner.my-learning', 'label' => 'My Learning'],
    ],

    'role' => [
        'admin' => [
            ['route' => 'admin.dashboard',             'label' => 'Dashboard'],
            ['route' => 'admin.courses.index',         'label' => 'Courses'],
            ['route' => 'admin.users.faculty.index',   'label' => 'Faculty'],
            ['route' => 'admin.users.managers.index',  'label' => 'Managers'],
            ['route' => 'admin.users.admins.index',    'label' => 'Admins'],
            ['route' => 'admin.organizations.index',   'label' => 'Organizations'],
            ['route' => 'admin.departments.index',     'label' => 'Departments'],
            ['route' => 'admin.question-bank.index',   'label' => 'Question Bank'],
            ['route' => 'admin.media-library.index',   'label' => 'Media Library'],
            ['route' => 'admin.reports.index',         'label' => 'Reports'],
            ['route' => 'admin.announcements.index',   'label' => 'Announcements'],
            ['route' => 'admin.settings.index',        'label' => 'Settings'],
        ],

        'manager' => [
            ['route' => 'manager.dashboard',           'label' => 'Dashboard'],
            ['route' => 'manager.faculty.index',       'label' => 'Faculty'],
            ['route' => 'manager.courses.assigned',    'label' => 'Assigned Courses'],
            ['route' => 'manager.assign.faculty',      'label' => 'Assign Faculty'],
            ['route' => 'manager.assign.courses',      'label' => 'Assign Courses'],
            ['route' => 'manager.reports.index',       'label' => 'Reports'],
            ['route' => 'manager.announcements.index', 'label' => 'Announcements'],
        ],

        'learner' => [
            ['route' => 'dashboard',                   'label' => 'Dashboard'],
            ['route' => 'learner.my-learning',         'label' => 'My Learning'],
            ['route' => 'learner.achievements',        'label' => 'Achievements'],
            ['route' => 'learner.certificates',        'label' => 'Certificates'],
            ['route' => 'learner.notifications',       'label' => 'Notifications'],
        ],
    ],
];
