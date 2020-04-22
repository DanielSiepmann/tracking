<?php
return [
    'ctrl' => [
        'label' => 'url',
        'label_alt' => 'crdate',
        'label_alt_force' => true,
        'sortby' => 'crdate DESC',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'languageField' => 'sys_language_uid',
        'title' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.pageview',
        'searchFields' => 'uid, url',
        'iconfile' => 'EXT:core/Resources/Public/Icons/T3Icons/apps/apps-pagetree-page-default.svg',
    ],
    'types' => [
        '0' => [
            'showitem' => 'sys_language_uid, pid, url, user_agent, type, crdate',
        ],
    ],
    'columns' => [
        'pid' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.pageview.pid',
            'config' => [
                'type' => 'select',
                'readOnly' => true,
                'renderType' => 'selectSingle',
                'foreign_table' => 'pages',
            ],
        ],
        'crdate' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.pageview.crdate',
            'config' => [
                'type' => 'input',
                'eval' => 'datetime',
            ],
        ],
        'sys_language_uid' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.pageview.sys_language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'items' => [
                    ['LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.pageview.sys_language.0', 0],
                ],
                'readOnly' => true,
            ]
        ],
        'user_agent' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.pageview.user_agent',
            'config' => [
                'type' => 'input',
                'readOnly' => true,
            ],
        ],
        'type' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.pageview.type',
            'config' => [
                'type' => 'input',
                'readOnly' => true,
                'eval' => 'int',
            ],
        ],
        'url' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.pageview.url',
            'config' => [
                'readOnly' => true,
                'type' => 'input',
                'size' => 50,
                'max' => 255,
            ],
        ],
    ],
];
