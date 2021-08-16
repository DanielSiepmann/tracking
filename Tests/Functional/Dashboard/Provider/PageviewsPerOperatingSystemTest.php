<?php

declare(strict_types=1);

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

namespace DanielSiepmann\Tracking\Tests\Functional\Dashboard\Provider;

use DanielSiepmann\Tracking\Dashboard\Provider\Demand;
use DanielSiepmann\Tracking\Dashboard\Provider\PageviewsPerOperatingSystem;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase as TestCase;

/**
 * @covers \DanielSiepmann\Tracking\Dashboard\Provider\PageviewsPerOperatingSystem
 */
class PageviewsPerOperatingSystemTest extends TestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/tracking',
    ];

    /**
     * @test
     */
    public function listsSixResultsForLast31DaysByDefault(): void
    {
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview');
            $connection->insert('tx_tracking_pageview', [
                'pid' => 1,
                'crdate' => time(),
            ]);
            $connection->insert('tx_tracking_tag', [
                'record_uid' => 1,
                'record_table_name' => 'tx_tracking_pageview',
                'name' => 'os',
                'value' => 'System ' . 1,
            ]);
        for ($i = 1; $i <= 10; $i++) {
            $connection->insert('tx_tracking_pageview', [
                'pid' => $i,
                'crdate' => time(),
            ]);
            $connection->insert('tx_tracking_tag', [
                'record_uid' => $i,
                'record_table_name' => 'tx_tracking_pageview',
                'name' => 'os',
                'value' => 'System ' . $i,
            ]);
        }

        $subject = new PageviewsPerOperatingSystem(
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
            new Demand()
        );

        $result = $subject->getChartData();
        self::assertSame([
            'System 1',
            'System 10',
            'System 2',
            'System 3',
            'System 4',
            'System 5',
        ], $result['labels']);
        self::assertSame([
            '2',
            '1',
            '1',
            '1',
            '1',
            '1',
        ], array_map('strval', $result['datasets'][0]['data']));
    }

    /**
     * @test
     */
    public function respectedOrdering(): void
    {
        $this->importDataSet('EXT:tracking/Tests/Functional/Fixtures/Pages.xml');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview');
        $connection->insert('tx_tracking_pageview', [
            'pid' => 1,
            'crdate' => time(),
        ]);
        $connection->insert('tx_tracking_tag', [
            'record_uid' => '1',
            'record_table_name' => 'tx_tracking_pageview',
            'name' => 'os',
            'value' => 'System 1',
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 2,
            'crdate' => time(),
        ]);
        $connection->insert('tx_tracking_tag', [
            'record_uid' => '2',
            'record_table_name' => 'tx_tracking_pageview',
            'name' => 'os',
            'value' => 'System 2',
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 3,
            'crdate' => time(),
        ]);
        $connection->insert('tx_tracking_tag', [
            'record_uid' => '3',
            'record_table_name' => 'tx_tracking_pageview',
            'name' => 'os',
            'value' => 'System 3',
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 3,
            'crdate' => time(),
        ]);
        $connection->insert('tx_tracking_tag', [
            'record_uid' => '4',
            'record_table_name' => 'tx_tracking_pageview',
            'name' => 'os',
            'value' => 'System 2',
        ]);

        $subject = new PageviewsPerOperatingSystem(
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
            new Demand()
        );

        $result = $subject->getChartData();
        self::assertSame([
            'System 2',
            'System 1',
            'System 3',
        ], $result['labels']);
        self::assertSame([
            '2',
            '1',
            '1',
        ], array_map('strval', $result['datasets'][0]['data']));
    }

    /**
     * @test
     */
    public function respectedNumberOfDays(): void
    {
        $this->importDataSet('EXT:tracking/Tests/Functional/Fixtures/Pages.xml');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview');
        $connection->insert('tx_tracking_pageview', [
            'pid' => 1,
            'crdate' => strtotime('-3 days'),
        ]);
        $connection->insert('tx_tracking_tag', [
            'record_uid' => '1',
            'record_table_name' => 'tx_tracking_pageview',
            'name' => 'os',
            'value' => 'System 1',
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 2,
            'crdate' => strtotime('-2 days'),
        ]);
        $connection->insert('tx_tracking_tag', [
            'record_uid' => '2',
            'record_table_name' => 'tx_tracking_pageview',
            'name' => 'os',
            'value' => 'System 2',
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 3,
            'crdate' => strtotime('-1 days'),
        ]);
        $connection->insert('tx_tracking_tag', [
            'record_uid' => '3',
            'record_table_name' => 'tx_tracking_pageview',
            'name' => 'os',
            'value' => 'System 3',
        ]);

        $subject = new PageviewsPerOperatingSystem(
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
            new Demand(2)
        );

        $result = $subject->getChartData();
        self::assertSame([
            'System 2',
            'System 3',
        ], $result['labels']);
        self::assertSame([
            '1',
            '1',
        ], array_map('strval', $result['datasets'][0]['data']));
    }

    /**
     * @test
     */
    public function respectedMaxResults(): void
    {
        $this->importDataSet('EXT:tracking/Tests/Functional/Fixtures/Pages.xml');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview');
        for ($i = 1; $i <= 10; $i++) {
            $connection->insert('tx_tracking_pageview', [
                'pid' => $i,
                'crdate' => time(),
            ]);
            $connection->insert('tx_tracking_tag', [
                'record_uid' => $i,
                'record_table_name' => 'tx_tracking_pageview',
                'name' => 'os',
                'value' => 'System ' . $i,
            ]);
        }

        $subject = new PageviewsPerOperatingSystem(
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
            new Demand(31, 4)
        );

        $result = $subject->getChartData();
        self::assertSame([
            'System 1',
            'System 10',
            'System 2',
            'System 3',
        ], $result['labels']);
        self::assertSame([
            '1',
            '1',
            '1',
            '1',
        ], array_map('strval', $result['datasets'][0]['data']));
    }

    /**
     * @test
     */
    public function respectsLimitToLanguages(): void
    {
        $this->importDataSet('EXT:tracking/Tests/Functional/Fixtures/Pages.xml');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview');
        for ($i = 1; $i <= 10; $i++) {
            $connection->insert('tx_tracking_pageview', [
                'pid' => $i,
                'sys_language_uid' => $i % 2,
                'crdate' => time(),
            ]);
            $connection->insert('tx_tracking_tag', [
                'record_uid' => $i,
                'record_table_name' => 'tx_tracking_pageview',
                'name' => 'os',
                'value' => 'System ' . $i,
            ]);
        }

        $subject = new PageviewsPerOperatingSystem(
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
            new Demand(31, 6, [], [1])
        );

        $result = $subject->getChartData();
        self::assertSame([
            'System 1',
            'System 3',
            'System 5',
            'System 7',
            'System 9',
        ], $result['labels']);
        self::assertSame([
            '1',
            '1',
            '1',
            '1',
            '1',
        ], array_map('strval', $result['datasets'][0]['data']));
    }

    // TODO: Add tests for new feature regarding tags
}
