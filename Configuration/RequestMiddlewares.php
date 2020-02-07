<?php

return [
    'frontend' => [
        'tracking-pageview' => [
            'target' => \DanielSiepmann\Tracking\Middleware\Pageview::class,
            'before' => [
                'typo3/cms-frontend/content-length-headers',
            ],
            'after' => [
                'typo3/cms-frontend/shortcut-and-mountpoint-redirect',
            ],
        ],
    ],
];
