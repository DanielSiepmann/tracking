<?php

declare(strict_types=1);

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

namespace DanielSiepmann\Tracking\Hooks;

use TYPO3\CMS\Core\DataHandling\DataHandler as Typo3DataHandler;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DataHandler
{
    public function processCmdmap_beforeStart(Typo3DataHandler $dataHandler): void
    {
        // @phpstan-ignore function.alreadyNarrowedType (It is available in v13, but not v14)
        if (property_exists($dataHandler, 'copyWhichTables')) {
            $this->preventCopyOfTrackingTablesV13($dataHandler);
            return;
        }

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

    private function preventCopyOfTrackingTablesV13(Typo3DataHandler $dataHandler): void
    {
        $copyWhichTables = array_keys($GLOBALS['TCA']);

        // @phpstan-ignore property.notFound (this code is only executed on v13 where it exists)
        if ($dataHandler->copyWhichTables !== '*') {
            // @phpstan-ignore property.notFound (this code is only executed on v13 where it exists)
            $copyWhichTables = $dataHandler->copyWhichTables;
            // @phpstan-ignore argument.type (this is a string in v13, and not executed in v14)
            $copyWhichTables = GeneralUtility::trimExplode(',', $copyWhichTables, true);
        }

        $copyWhichTables = array_filter(
            $copyWhichTables,
            static fn (int|string $tableName): bool => \str_starts_with((string) $tableName, 'tx_tracking_') === false
        );

        // @phpstan-ignore property.notFound (this code is only executed on v13 where it exists)
        $dataHandler->copyWhichTables = implode(',', $copyWhichTables);
    }

    private function preventCopyOfTrackingTables(Typo3DataHandler $dataHandler): void
    {
        // TODO: Find a way to prevent copy of tracking tables on page copy.
        // See upstream issue: https://forge.typo3.org/issues/108353
    }
}
