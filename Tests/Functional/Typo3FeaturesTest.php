<?php

declare(strict_types=1);

namespace DanielSiepmann\Tracking\Tests\Functional;

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
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;

#[TestDox('This extension works with TYPO3 feature:')]
final class Typo3FeaturesTest extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/BackendUser.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Typo3FeaturesTest/PageWithRecords.php');
        $this->setUpBackendUser(1);
        $GLOBALS['LANG'] = $this->get(LanguageServiceFactory::class)->create('default');
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['LANG']);

        parent::tearDown();
    }

    #[TestDox('Copy pages. Tracking records will not be copied.')]
    #[Test]
    public function copyContainingRecords(): void
    {
        $dataHandler = $this->get(DataHandler::class);
        $dataHandler->start([], [
            'pages' => [
                1 => [
                    'copy' => 1,
                ],
            ],
        ]);
        $dataHandler->process_cmdmap();

        self::assertCount(0, $dataHandler->errorLog, 'Failed with errors: ' . implode(PHP_EOL, $dataHandler->errorLog));
        $this->assertCSVDataSet(
            __DIR__ . '/ExpectedResults/Typo3FeaturesTest/CopyPasteContainingRecords.csv'
        );
    }

    #[TestDox('Copy individual tables, but always exclude tracking tables.')]
    #[Test]
    public function copyCustomTablesViaDataHandlerV13(): void
    {
        $dataHandler = $this->get(DataHandler::class);

        // @phpstan-ignore function.alreadyNarrowedType (It is available in v13, but not v14)
        if (property_exists($dataHandler, 'copyWhichTables')) {
            self::markTestSkipped('Only available in TYPO3 v13.');
        }

        // @phpstan-ignore property.notFound (this code is only executed on v13 where it exists)
        $dataHandler->copyWhichTables = 'pages,tx_tracking_pageview,tx_tracking_recordview';
        $dataHandler->start([], [
            'pages' => [
                1 => [
                    'copy' => 1,
                ],
            ],
        ]);
        $dataHandler->process_cmdmap();

        self::assertCount(0, $dataHandler->errorLog, 'Failed with errors: ' . implode(PHP_EOL, $dataHandler->errorLog));
        $this->assertCSVDataSet(
            __DIR__ . '/ExpectedResults/Typo3FeaturesTest/CopyPasteContainingRecords.csv'
        );
    }

    #[TestDox('Copy individual tables, but always exclude tracking tables.')]
    #[Test]
    public function copyCustomTablesViaDataHandlerV14(): void
    {
        $dataHandler = $this->get(DataHandler::class);

        // @phpstan-ignore function.alreadyNarrowedType (It is available in v13, but not v14)
        if (property_exists($dataHandler, 'copyWhichTables') === false) {
            self::markTestSkipped('Only available in TYPO3 v14.');
        }

        $dataHandler->start([], [
            'pages' => [
                1 => [
                    'copy' => 1,
                ],
            ],
        ]);
        $dataHandler->process_cmdmap();

        self::assertCount(0, $dataHandler->errorLog, 'Failed with errors: ' . implode(PHP_EOL, $dataHandler->errorLog));
        $this->assertCSVDataSet(
            __DIR__ . '/ExpectedResults/Typo3FeaturesTest/CopyPasteContainingRecords.csv'
        );
    }
}
