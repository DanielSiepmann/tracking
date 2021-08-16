<?php

return [
    'ctrl' => [
        'label' => 'record',
        'label_alt' => 'crdate',
        'label_alt_force' => true,
        'default_sortby' => 'crdate DESC',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'title' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.recordview',
        'searchFields' => 'uid, url',
        'iconfile' => 'EXT:tracking/Resources/Public/Icons/Record/Recordview.svg',
    ],
    'types' => [
        '0' => [
            'showitem' => 'sys_language_uid, pid, record, url, user_agent, tags, crdate',
        ],
    ],
    'columns' => [
        'pid' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.recordview.pid',
            'config' => [
                // TYPO3 v10 does no longer allow to resolve PID relations, e.g. via select or group
                // This will break internal PID handling.
                'type' => 'input',
                'readOnly' => true,
            ],
        ],
        'crdate' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.recordview.crdate',
            'config' => [
                'type' => 'input',
                'eval' => 'datetime',
            ],
        ],
        'sys_language_uid' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.recordview.sys_language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'items' => [
                    ['LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.recordview.sys_language.0', 0],
                ],
                'readOnly' => true,
            ]
        ],
        'user_agent' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.recordview.user_agent',
            'config' => [
                'type' => 'input',
                'readOnly' => true,
            ],
        ],
        'url' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.recordview.url',
            'config' => [
                'readOnly' => true,
                'type' => 'input',
                'size' => 50,
            ],
        ],
        'record' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.recordview.record',
            'config' => [
                'type' => 'group',
                'allowed' => 'tt_content,sys_category,pages',
                'internal_type' => 'db',
                'maxitems' => 1,
                'minitems' => 1,
                'size' => 1,
            ],
        ],
        'tags' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.recordview.tags',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_tracking_tag',
                'foreign_field' => 'record_uid',
                'foreign_table_field' => 'record_table_name',
                'readOnly' => true,
            ],
        ],
    ],
];
