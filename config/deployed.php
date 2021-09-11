<?php

return [
    'links' => [
        'Changelog' => 'https://github.com/vendor/app/blob/v{appVersion}/CHANGELOG.md',
        'Visit Site' => '{appUrl}',
    ],

    'logo' => asset('img/logo.png'),

    'slack' => [
        'webhook' => env('DEPLOYED_SLACK_WEBHOOK')
    ]
];
