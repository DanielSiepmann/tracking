<?php

namespace DanielSiepmann\Tracking\Hooks;

/*
 * Copyright (C) 2021 Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

use TYPO3\CMS\Core\DataHandling\DataHandler as Typo3DataHandler;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

class DataHandler
{
    public function processCmdmap_beforeStart(Typo3DataHandler $dataHandler): void
    {
        $this->preventCopyOfTrackingTables($dataHandler);
    }

    public static function register(): void
    {
        ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TYPO3_CONF_VARS'], [
            'SC_OPTIONS' => [
                't3lib/class.t3lib_tcemain.php' => [
                    'processCmdmapClass' => [
                        'tracking' => self::class,
                    ],
                ],
            ],
        ]);
    }

    private function preventCopyOfTrackingTables(Typo3DataHandler $dataHandler): void
    {
        $copyWhichTables = $dataHandler->compileAdminTables();

        if ($dataHandler->copyWhichTables !== '*') {
            $copyWhichTables = GeneralUtility::trimExplode(',', $dataHandler->copyWhichTables, true);
        }

        $copyWhichTables = array_filter($copyWhichTables, function (string $tableName) {
            return StringUtility::beginsWith($tableName, 'tx_tracking_') === false;
        });

        $dataHandler->copyWhichTables = implode(',', $copyWhichTables);
    }
}
