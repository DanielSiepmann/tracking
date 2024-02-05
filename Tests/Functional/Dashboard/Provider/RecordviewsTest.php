<?php

declare(strict_types=1);

namespace DanielSiepmann\Tracking\Tests\Functional\Dashboard\Provider;

/*
 * Copyright (C) 2020 Daniel Siepmann <coding@daniel-siepmann.de>
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

use DanielSiepmann\Tracking\Dashboard\Provider\Recordviews;
use DanielSiepmann\Tracking\LanguageAspectFactory;
use DanielSiepmann\Tracking\Tests\Functional\AbstractFunctionalTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;

#[CoversClass(Recordviews::class)]
final class RecordviewsTest extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/BackendUser.php');
        $GLOBALS['BE_USER'] = $this->setUpBackendUser(1);
        $GLOBALS['LANG'] = $this->get(LanguageServiceFactory::class)->create('default');
    }

    protected function tearDown(): void
    {
        unset(
            $GLOBALS['BE_USER'],
            $GLOBALS['LANG']
        );
        parent::tearDown();
    }

    #[Test]
    public function listsSixResultsForLast31DaysByDefault(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/SysCategories.php');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_recordview');
        for ($i = 1; $i <= 10; $i++) {
            $connection->insert('tx_tracking_recordview', [
                'crdate' => time(),
                'record' => 'sys_category_' . $i,
                'record_uid' => $i,
                'record_table_name' => 'sys_category',
            ]);
        }

        $subject = new Recordviews(
            $this->get(PageRepository::class),
            $this->getConnectionPool()->getQueryBuilderForTable('tx_tracking_recordview'),
            new LanguageAspectFactory()
        );

        $result = $subject->getChartData();
        self::assertSame([
            'Category 10',
            'Category 9',
            'Category 8',
            'Category 7',
            'Category 6',
            'Category 5',
        ], $result['labels']);
        self::assertCount(6, $result['datasets'][0]['data']);
    }

    #[Test]
    public function respectedOrdering(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/SysCategories.php');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_recordview');
        $connection->insert('tx_tracking_recordview', [
            'crdate' => time(),
            'record' => 'sys_category_1',
            'record_uid' => 1,
            'record_table_name' => 'sys_category',
        ]);
        $connection->insert('tx_tracking_recordview', [
            'crdate' => time(),
            'record' => 'sys_category_2',
            'record_uid' => 2,
            'record_table_name' => 'sys_category',
        ]);
        $connection->insert('tx_tracking_recordview', [
            'crdate' => time(),
            'record' => 'sys_category_3',
            'record_uid' => 3,
            'record_table_name' => 'sys_category',
        ]);
        $connection->insert('tx_tracking_recordview', [
            'crdate' => time(),
            'record' => 'sys_category_2',
            'record_uid' => 2,
            'record_table_name' => 'sys_category',
        ]);

        $subject = new Recordviews(
            $this->get(PageRepository::class),
            $this->getConnectionPool()->getQueryBuilderForTable('tx_tracking_recordview'),
            new LanguageAspectFactory(),
            2
        );

        $result = $subject->getChartData();
        self::assertSame([
            'Category 2',
            'Category 3',
            'Category 1',
        ], $result['labels']);
        self::assertCount(3, $result['datasets'][0]['data']);
    }

    #[Test]
    public function respectedNumberOfDays(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/SysCategories.php');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_recordview');
        $connection->insert('tx_tracking_recordview', [
            'crdate' => strtotime('-3 days'),
            'record' => 'sys_category_1',
            'record_uid' => 1,
            'record_table_name' => 'sys_category',
        ]);
        $connection->insert('tx_tracking_recordview', [
            'crdate' => strtotime('-2 days'),
            'record' => 'sys_category_2',
            'record_uid' => 2,
            'record_table_name' => 'sys_category',
        ]);
        $connection->insert('tx_tracking_recordview', [
            'crdate' => strtotime('-1 day'),
            'record' => 'sys_category_3',
            'record_uid' => 3,
            'record_table_name' => 'sys_category',
        ]);

        $subject = new Recordviews(
            $this->get(PageRepository::class),
            $this->getConnectionPool()->getQueryBuilderForTable('tx_tracking_recordview'),
            new LanguageAspectFactory(),
            2
        );

        $result = $subject->getChartData();
        self::assertSame([
            'Category 3',
            'Category 2',
        ], $result['labels']);
        self::assertCount(2, $result['datasets'][0]['data']);
    }

    #[Test]
    public function respectedMaxResults(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/SysCategories.php');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_recordview');
        for ($i = 1; $i <= 10; $i++) {
            $connection->insert('tx_tracking_recordview', [
                'crdate' => time(),
                'record' => 'sys_category_' . $i,
                'record_uid' => $i,
                'record_table_name' => 'sys_category',
            ]);
        }

        $subject = new Recordviews(
            $this->get(PageRepository::class),
            $this->getConnectionPool()->getQueryBuilderForTable('tx_tracking_recordview'),
            new LanguageAspectFactory(),
            31,
            2
        );

        $result = $subject->getChartData();
        self::assertSame([
            'Category 10',
            'Category 9',
        ], $result['labels']);
        self::assertCount(2, $result['datasets'][0]['data']);
    }

    #[Test]
    public function respectedExcludedPages(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/SysCategories.php');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_recordview');
        for ($i = 1; $i <= 10; $i++) {
            $connection->insert('tx_tracking_recordview', [
                'pid' => $i,
                'crdate' => time(),
                'record' => 'sys_category_' . $i,
                'record_uid' => $i,
                'record_table_name' => 'sys_category',
            ]);
        }

        $subject = new Recordviews(
            $this->get(PageRepository::class),
            $this->getConnectionPool()->getQueryBuilderForTable('tx_tracking_recordview'),
            new LanguageAspectFactory(),
            31,
            6,
            [1, 2, 3, 4, 5]
        );

        $result = $subject->getChartData();
        self::assertSame([
            'Category 10',
            'Category 9',
            'Category 8',
            'Category 7',
            'Category 6',
        ], $result['labels']);
        self::assertCount(5, $result['datasets'][0]['data']);
    }

    #[Test]
    public function respectLimitesTables(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/SysCategories.php');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_recordview');
        for ($i = 1; $i <= 3; $i++) {
            $connection->insert('tx_tracking_recordview', [
                'crdate' => time(),
                'record' => 'sys_category_' . $i,
                'record_uid' => $i,
                'record_table_name' => 'sys_category',
            ]);
        }
        $connection->insert('tx_tracking_recordview', [
            'crdate' => time(),
            'record' => 'tt_content_1',
            'record_uid' => 1,
            'record_table_name' => 'tt_content',
        ]);

        $subject = new Recordviews(
            $this->get(PageRepository::class),
            $this->getConnectionPool()->getQueryBuilderForTable('tx_tracking_recordview'),
            new LanguageAspectFactory(),
            31,
            6,
            [],
            [],
            ['sys_category']
        );

        $result = $subject->getChartData();
        self::assertSame([
            'Category 3',
            'Category 2',
            'Category 1',
        ], $result['labels']);
        self::assertCount(3, $result['datasets'][0]['data']);
    }

    #[Test]
    public function respectsLimitedTypes(): void
    {
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_recordview');
        for ($i = 1; $i <= 3; $i++) {
            $connection->insert('tx_tracking_recordview', [
                'crdate' => time(),
                'record' => 'tt_content_' . $i,
                'record_uid' => $i,
                'record_table_name' => 'tt_content',
            ]);
            $connection->insert('tt_content', [
                'uid' => $i,
                'CType' => $i,
                'header' => 'Content element ' . $i,
            ]);
        }

        $subject = new Recordviews(
            $this->get(PageRepository::class),
            $this->getConnectionPool()->getQueryBuilderForTable('tx_tracking_recordview'),
            new LanguageAspectFactory(),
            31,
            6,
            [],
            [],
            [],
            ['1', 2]
        );

        $result = $subject->getChartData();
        self::assertSame([
            'Content element 2',
            'Content element 1',
        ], $result['labels']);
        self::assertCount(2, $result['datasets'][0]['data']);
    }

    #[Test]
    public function localizedRecordTitlesIfLimitedToSingleLanguage(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/SysCategories.php');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_recordview');
        $connection->insert('tx_tracking_recordview', [
            'crdate' => time(),
            'sys_language_uid' => 0,
            'record' => 'sys_category_1',
            'record_uid' => 1,
            'record_table_name' => 'sys_category',
        ]);
        $connection->insert('tx_tracking_recordview', [
            'crdate' => time(),
            'sys_language_uid' => 1,
            'record' => 'sys_category_1',
            'record_uid' => 1,
            'record_table_name' => 'sys_category',
        ]);
        $connection->insert('tx_tracking_recordview', [
            'crdate' => time(),
            'sys_language_uid' => 1,
            'record' => 'sys_category_2',
            'record_uid' => 2,
            'record_table_name' => 'sys_category',
        ]);

        $subject = new Recordviews(
            $this->get(PageRepository::class),
            $this->getConnectionPool()->getQueryBuilderForTable('tx_tracking_recordview'),
            new LanguageAspectFactory(),
            31,
            6,
            [],
            [1],
            [],
            []
        );

        $result = $subject->getChartData();
        self::assertSame([
            'Category 2',
            'Kategorie 1',
        ], $result['labels']);
        self::assertCount(2, $result['datasets'][0]['data']);
    }

    #[Test]
    public function defaultLanguageTitleIsUsedIfMultipleLanguagesAreAllowed(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/SysCategories.php');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_recordview');
        $connection->insert('tx_tracking_recordview', [
            'crdate' => time(),
            'sys_language_uid' => 0,
            'record' => 'sys_category_1',
            'record_uid' => 1,
            'record_table_name' => 'sys_category',
        ]);
        $connection->insert('tx_tracking_recordview', [
            'crdate' => time(),
            'sys_language_uid' => 1,
            'record' => 'sys_category_1',
            'record_uid' => 1,
            'record_table_name' => 'sys_category',
        ]);
        $connection->insert('tx_tracking_recordview', [
            'crdate' => time(),
            'sys_language_uid' => 1,
            'record' => 'sys_category_2',
            'record_uid' => 2,
            'record_table_name' => 'sys_category',
        ]);

        $subject = new Recordviews(
            $this->get(PageRepository::class),
            $this->getConnectionPool()->getQueryBuilderForTable('tx_tracking_recordview'),
            new LanguageAspectFactory(),
            31,
            6,
            [],
            [1, 0],
            [],
            []
        );

        $result = $subject->getChartData();
        self::assertSame([
            'Category 1',
            'Category 2',
        ], $result['labels']);
        self::assertCount(2, $result['datasets'][0]['data']);
    }
}
