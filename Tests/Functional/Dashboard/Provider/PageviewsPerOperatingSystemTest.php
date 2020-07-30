<?php

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

use DanielSiepmann\Tracking\Dashboard\Provider\PageviewsPerOperatingSystem;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase as TestCase;

/**
 * @covers DanielSiepmann\Tracking\Dashboard\Provider\PageviewsPerOperatingSystem
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
        for ($i = 1; $i <= 10; $i++) {
            $connection->insert('tx_tracking_pageview', [
                'pid' => $i,
                'operating_system' => 'System ' . $i,
                'crdate' => time(),
            ]);
        }

        $subject = new PageviewsPerOperatingSystem(
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview')
        );

        $result = $subject->getChartData();
        static::assertSame([
            'System 1',
            'System 10',
            'System 2',
            'System 3',
            'System 4',
            'System 5',
        ], $result['labels']);
        static::assertCount(6, $result['datasets'][0]['data']);
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
            'operating_system' => 'System 1',
            'crdate' => time(),
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 2,
            'operating_system' => 'System 2',
            'crdate' => time(),
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 3,
            'operating_system' => 'System 3',
            'crdate' => time(),
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 2,
            'operating_system' => 'System 2',
            'crdate' => time(),
        ]);

        $subject = new PageviewsPerOperatingSystem(
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview')
        );

        $result = $subject->getChartData();
        static::assertSame([
            'System 2',
            'System 1',
            'System 3',
        ], $result['labels']);
        static::assertCount(3, $result['datasets'][0]['data']);
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
            'operating_system' => 'System 1',
            'crdate' => strtotime('-3 days'),
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 2,
            'operating_system' => 'System 2',
            'crdate' => strtotime('-2 days'),
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 3,
            'operating_system' => 'System 3',
            'crdate' => strtotime('-1 days'),
        ]);

        $subject = new PageviewsPerOperatingSystem(
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
            2
        );

        $result = $subject->getChartData();
        static::assertSame([
            'System 2',
            'System 3',
        ], $result['labels']);
        static::assertCount(2, $result['datasets'][0]['data']);
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
                'operating_system' => 'System ' . $i,
                'crdate' => time(),
            ]);
        }

        $subject = new PageviewsPerOperatingSystem(
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
            31,
            4
        );

        $result = $subject->getChartData();
        static::assertSame([
            'System 1',
            'System 10',
            'System 2',
            'System 3',
        ], $result['labels']);
        static::assertCount(4, $result['datasets'][0]['data']);
    }
}
