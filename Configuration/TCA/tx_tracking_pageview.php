<?php

return [
    'ctrl' => [
        'label' => 'url',
        'label_alt' => 'crdate',
        'label_alt_force' => true,
        'default_sortby' => 'crdate DESC',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'title' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.pageview',
        'searchFields' => 'uid, url',
        'iconfile' => 'EXT:tracking/Resources/Public/Icons/Record/Pageview.svg',
    ],
    'types' => [
        '0' => [
            'showitem' => 'sys_language_uid, pid, url, user_agent, tags, type, crdate',
        ],
    ],
    'columns' => [
        'pid' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.pageview.pid',
            'config' => [
                // TYPO3 v10 does no longer allow to resolve PID relations, e.g. via select or group
                // This will break internal PID handling.
                'type' => 'input',
                'readOnly' => true,
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
        'tags' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.pageview.tags',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_tracking_tag',
                'foreign_field' => 'record_uid',
                'foreign_table_field' => 'record_table_name',
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
            ],
        ],
    ],
];
