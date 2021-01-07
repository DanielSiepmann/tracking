<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Tracking',
    'description' => 'Tracks page visits in TYPO3.',
    'category' => 'fe',
    'state' => 'alpha',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'author' => 'Daniel Siepmann',
    'author_email' => 'coding@daniel-siepmann.de',
    'author_company' => '',
    'version' => '1.0.1',
    'constraints' => [
        'depends' => [
            'core' => '',
        ],
        'conflicts' => [],
        'suggests' => [
            'dashboard' => '',
        ],
    ],
];
