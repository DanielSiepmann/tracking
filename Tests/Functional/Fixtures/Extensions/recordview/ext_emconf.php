<?php

declare(strict_types=1);

$EM_CONF[$_EXTKEY] = [
    'title' => 'TESTING: Tracking recordview',
    'description' => 'Used by functional tests',
    'category' => 'fe',
    'state' => 'stable',
    'author' => 'Daniel Siepmann',
    'author_email' => 'coding@daniel-siepmann.de',
    'author_company' => '',
    'version' => '1.1.2',
    'constraints' => [
        'depends' => [
            'core' => '',
            'tracking' => '',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
