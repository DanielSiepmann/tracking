<?php

declare(strict_types=1);

use DanielSiepmann\Tracking\Middleware\Pageview;
use DanielSiepmann\Tracking\Middleware\Recordview;

return [
    'frontend' => [
        'danielsiepmann/tracking/pageview' => [
            'target' => Pageview::class,
            'before' => [
                'typo3/cms-frontend/content-length-headers',
            ],
            'after' => [
                'typo3/cms-frontend/shortcut-and-mountpoint-redirect',
            ],
        ],
        'danielsiepmann/tracking/recordview' => [
            'target' => Recordview::class,
            'before' => [
                'typo3/cms-frontend/content-length-headers',
            ],
            'after' => [
                'typo3/cms-frontend/shortcut-and-mountpoint-redirect',
            ],
        ],
    ],
];
