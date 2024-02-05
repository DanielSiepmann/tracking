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

use DanielSiepmann\Tracking\Dashboard\Provider\PageviewsPerDay;
use DanielSiepmann\Tracking\Tests\Functional\AbstractFunctionalTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[CoversClass(PageviewsPerDay::class)]
final class PageviewsPerDayTest extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['LANG'] = $this->get(LanguageServiceFactory::class)->create('default');
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['LANG']);
        parent::tearDown();
    }

    #[Test]
    public function listsResultsForLast31DaysByDefault(): void
    {
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview');
        for ($i = 1; $i <= 10; $i++) {
            $connection->insert('tx_tracking_pageview', [
                'pid' => $i,
                'crdate' => strtotime('-' . $i . ' days'),
            ]);
        }

        $subject = new PageviewsPerDay(
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
        );

        $result = $subject->getChartData();
        self::assertCount(32, $result['labels']);
        self::assertCount(32, $result['datasets'][0]['data']);
    }

    #[Test]
    public function respectedNumberOfDays(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/Pages.php');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview');
        for ($i = 1; $i <= 10; $i++) {
            $connection->insert('tx_tracking_pageview', [
                'pid' => $i,
                'crdate' => strtotime('-' . $i . ' days'),
            ]);
        }

        $subject = new PageviewsPerDay(
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
            3
        );

        $result = $subject->getChartData();
        self::assertCount(4, $result['labels']);
        self::assertSame([
            1,
            1,
            1,
            0,
        ], $result['datasets'][0]['data']);
    }

    #[Test]
    public function respectedExcludedPages(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/Pages.php');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview');
        for ($i = 1; $i <= 10; $i++) {
            $connection->insert('tx_tracking_pageview', [
                'pid' => $i,
                'crdate' => strtotime('-' . $i . ' days'),
            ]);
        }

        $subject = new PageviewsPerDay(
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
            3,
            [2]
        );

        $result = $subject->getChartData();
        self::assertCount(4, $result['labels']);
        self::assertSame([
            1,
            0,
            1,
            0,
        ], $result['datasets'][0]['data']);
    }

    #[Test]
    public function respectedDateFormat(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/Pages.php');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview');

        $subject = new PageviewsPerDay(
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
            1,
            [],
            [],
            'd.m.Y'
        );

        $result = $subject->getChartData();
        self::assertSame([
            date('d.m.Y', strtotime('-1 day')),
            date('d.m.Y'),
        ], $result['labels']);
        self::assertCount(2, $result['datasets'][0]['data']);
    }

    #[Test]
    public function respectsLimitToLanguages(): void
    {
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview');
        for ($i = 1; $i <= 10; $i++) {
            $connection->insert('tx_tracking_pageview', [
                'pid' => $i,
                'crdate' => strtotime('-' . $i . ' days'),
                'sys_language_uid' => $i % 2,
            ]);
        }

        $subject = new PageviewsPerDay(
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
            11,
            [],
            [1]
        );

        $result = $subject->getChartData();
        self::assertSame([
            0 => 0,
            1 => 0,
            2 => 1,
            3 => 0,
            4 => 1,
            5 => 0,
            6 => 1,
            7 => 0,
            8 => 1,
            9 => 0,
            10 => 1,
            11 => 0,
        ], $result['datasets'][0]['data']);
    }
}
