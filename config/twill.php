<?php

return [


    'dashboard' => [
        'modules' => [
            App\Models\Page::class => [ // Using FQN of the model
                'name' => 'Pages',
                'count' => true,
                'create' => true,
                'activity' => true,
                'draft' => true,
                'search' => true,
            ],

        ],
        'auth_activity_log' => [
            'login' => true,
            'logout' => true
        ]
    ],


    'block_editor' => [
        'use_twill_blocks' => [],
        'blocks' => [
            'text' => [
                'title' => 'Text',
                'icon' => 'text',
                'component' => 'a17-block-text',
                'translatable' => true,
            ],
        ],
        'crops' => [
            'highlight' => [
                'desktop' => [
                    [
                        'name' => 'desktop',
                        'ratio' => 16 / 9,
                    ],
                ],
                'mobile' => [
                    [
                        'name' => 'mobile',
                        'ratio' => 1,
                    ],
                ],
            ],
        ],
    ],
];
