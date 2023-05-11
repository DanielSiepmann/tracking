<?php

$tca = [
    'ctrl' => [
        'label' => 'record',
        'label_alt' => 'crdate',
        'label_alt_force' => true,
        'default_sortby' => 'crdate DESC',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'title' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.recordview',
        'iconfile' => 'EXT:tracking/Resources/Public/Icons/Record/Recordview.svg',
    ],
    'types' => [
        '0' => [
            'showitem' => 'sys_language_uid, pid, record, url, user_agent, operating_system, crdate',
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
                'type' => 'inputDateTime',
            ],
        ],
        'sys_language_uid' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.recordview.sys_language',
            'config' => ['type' => 'language'],
        ],
        'user_agent' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.recordview.user_agent',
            'config' => [
                'type' => 'input',
                'readOnly' => true,
            ],
        ],
        'operating_system' => [
            'label' => 'LLL:EXT:tracking/Resources/Private/Language/locallang_tca.xlf:table.recordview.operating_system',
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
                'maxitems' => 1,
                'minitems' => 1,
                'size' => 1,
            ],
        ],
    ],
];

if ((new \TYPO3\CMS\Core\Information\Typo3Version())->getMajorVersion() < 12) {
    $tca['ctrl']['cruser_id'] = 'cruser_id';

    $tca['columns']['crdate']['config']['type'] = 'input';
    $tca['columns']['crdate']['config']['renderType'] = 'inputDateTime';
    $tca['columns']['crdate']['config']['eval'] = 'datetime';
}

return $tca;
