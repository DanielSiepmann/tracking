<?php

$tca = [
    'ctrl' => [
        'label' => 'url',
        'label_alt' => 'crdate',
        'label_alt_force' => true,
        'default_sortby' => 'crdate DESC',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'title' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.pageview',
        'iconfile' => 'EXT:tracking/Resources/Public/Icons/Record/Pageview.svg',
    ],
    'types' => [
        '0' => [
            'showitem' => 'sys_language_uid, pid, url, user_agent, operating_system, type, crdate',
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
                'type' => 'datetime',
            ],
        ],
        'sys_language_uid' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.pageview.sys_language',
            'config' => ['type' => 'language'],
        ],
        'user_agent' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.pageview.user_agent',
            'config' => [
                'type' => 'input',
                'readOnly' => true,
            ],
        ],
        'operating_system' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.pageview.operating_system',
            'config' => [
                'type' => 'input',
                'readOnly' => true,
            ],
        ],
        'type' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.pageview.type',
            'config' => [
                'type' => 'number',
                'readOnly' => true,
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

if ((new \TYPO3\CMS\Core\Information\Typo3Version())->getMajorVersion() < 12) {
    $tca['ctrl']['cruser_id'] = 'cruser_id';

    $tca['columns']['crdate']['config']['type'] = 'input';
    $tca['columns']['crdate']['config']['renderType'] = 'inputDateTime';
    $tca['columns']['crdate']['config']['eval'] = 'datetime';

    $tca['columns']['type']['config']['type'] = 'input';
    $tca['columns']['type']['config']['eval'] = 'int';
}

return $tca;
