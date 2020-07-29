<?php

return [
    'frontend' => [
        'danielsiepmann/tracking/pageview' => [
            'target' => \DanielSiepmann\Tracking\Middleware\Pageview::class,
            'before' => [
                'typo3/cms-frontend/content-length-headers',
            ],
            'after' => [
                'typo3/cms-frontend/shortcut-and-mountpoint-redirect',
            ],
        ],
        'danielsiepmann/tracking/recordview' => [
            'target' => \DanielSiepmann\Tracking\Middleware\Recordview::class,
            'before' => [
                'typo3/cms-frontend/content-length-headers',
            ],
            'after' => [
                'typo3/cms-frontend/shortcut-and-mountpoint-redirect',
            ],
        ],
    ],
];
