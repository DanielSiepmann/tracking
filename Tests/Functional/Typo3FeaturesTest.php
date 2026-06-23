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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
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

    #[DataProvider('possibleTypo3Doktypes')]
    #[TestDox('Can switch a page with tracking records to doktype $_dataName')]
    #[Test]
    public function allowRecordsonDoktypes(int $doktype): void
    {
        $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview')->insert('tx_tracking_pageview', [
            'pid' => '1',
            'url' => 'http://localhost/',
        ]);
        $this->getConnectionPool()->getConnectionForTable('tx_tracking_recordview')->insert('tx_tracking_recordview', [
            'pid' => '1',
            'url' => 'http://localhost/',
        ]);

        $dataHandler = $this->get(DataHandler::class);
        $dataHandler->start([
            'pages' => [
                1 => [
                    'doktype' => $doktype,
                ],
            ],
        ], []);
        $dataHandler->process_datamap();

        self::assertCount(0, $dataHandler->errorLog, 'Failed with errors: ' . implode(PHP_EOL, $dataHandler->errorLog));
    }

    public static function possibleTypo3Doktypes(): iterable
    {
        yield 'Default/Standard' => [
            'doktype' => PageRepository::DOKTYPE_DEFAULT,
        ];
        yield 'Link' => [
            'doktype' => PageRepository::DOKTYPE_LINK,
        ];
        yield 'Shortcut' => [
            'doktype' => PageRepository::DOKTYPE_SHORTCUT,
        ];
        yield 'BE User Section' => [
            'doktype' => PageRepository::DOKTYPE_BE_USER_SECTION,
        ];
        yield 'Mountpoint' => [
            'doktype' => PageRepository::DOKTYPE_MOUNTPOINT,
        ];
        yield 'Spacer' => [
            'doktype' => PageRepository::DOKTYPE_SPACER,
        ];
        yield 'Storage Folder' => [
            'doktype' => PageRepository::DOKTYPE_SYSFOLDER,
        ];
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
    public function copyCustomTablesViaDataHandler(): void
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
}
