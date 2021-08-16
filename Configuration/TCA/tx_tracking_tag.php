<?php

return [
    'ctrl' => [
        'label' => 'name',
        'label_alt' => 'value',
        'label_alt_force' => true,
        'default_sortby' => 'crdate DESC',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'hideTable' => true,
        'title' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.tag',
    ],
    'types' => [
        '0' => [
            'showitem' => 'pid, record_uid, record_table_name, name, value',
        ],
    ],
    'columns' => [
        'pid' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.tag.pid',
            'config' => [
                // TYPO3 v10 does no longer allow to resolve PID relations, e.g. via select or group
                // This will break internal PID handling.
                'type' => 'input',
                'readOnly' => true,
            ],
        ],
        'record_uid' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.tag.record_uid',
            'config' => [
                'type' => 'input',
                'eval' => 'int',
                'readOnly' => true,
            ],
        ],
        'record_table_name' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.tag.record_table_name',
            'config' => [
                'type' => 'input',
                'readOnly' => true,
            ],
        ],
        'crdate' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.tag.crdate',
            'config' => [
                'type' => 'input',
                'eval' => 'datetime',
            ],
        ],
        'name' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.tag.name',
            'config' => [
                'type' => 'input',
                'readOnly' => true,
            ],
        ],
        'value' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.tag.value',
            'config' => [
                'type' => 'input',
                'readOnly' => true,
            ],
        ],
    ],
];
