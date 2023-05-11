<?php

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

use Codappix\Typo3PhpDatasets\TestingFramework;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use UnexpectedValueException;

/**
 * @covers \DanielSiepmann\Tracking\Functional\CopyingPageWithRecordsWorks
 *
 * @testdox This extension works with TYPO3 feature:
 */
class Typo3FeaturesTest extends FunctionalTestCase
{
    use TestingFramework;

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/tracking',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/BackendUser.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Typo3FeaturesTest/PageWithRecords.php');
        $this->setUpBackendUser(1);
        $languageServiceFactory = $this->getContainer()->get(LanguageServiceFactory::class);
        if (!$languageServiceFactory instanceof LanguageServiceFactory) {
            throw new UnexpectedValueException('Did not retrieve LanguageServiceFactory.', 1637847250);
        }
        $GLOBALS['LANG'] = $languageServiceFactory->create('default');
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['LANG']);

        parent::tearDown();
    }

    /**
     * @test
     *
     * @testdox Copy pages. Tracking records will not be copied.
     */
    public function copyContainingRecords(): void
    {
        $dataHandler = new DataHandler();
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
            'EXT:tracking/Tests/Functional/ExpectedResults/Typo3FeaturesTest/CopyPasteContainingRecords.csv'
        );
    }

    /**
     * @test
     *
     * @testdox Copy individual tables, but always exclude tracking tables.
     */
    public function copyCustomTablesViaDataHandler(): void
    {
        $dataHandler = new DataHandler();
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
            'EXT:tracking/Tests/Functional/ExpectedResults/Typo3FeaturesTest/CopyPasteContainingRecords.csv'
        );
    }
}
