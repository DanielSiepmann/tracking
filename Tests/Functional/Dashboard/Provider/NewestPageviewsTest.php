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

use DanielSiepmann\Tracking\Dashboard\Provider\NewestPageviews;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase as TestCase;

/**
 * @covers \DanielSiepmann\Tracking\Dashboard\Provider\NewestPageviews
 */
class NewestPageviewsTest extends TestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/tracking',
    ];

    /**
     * @test
     */
    public function returnsRecentSixPageviews(): void
    {
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview');
        for ($i = 1; $i <= 10; $i++) {
            $connection->insert('tx_tracking_pageview', [
                'pid' => $i,
                'url' => 'Url ' . $i,
                'user_agent' => 'User-Agent ' . $i,
                'crdate' => $i,
            ]);
        }

        $subject = new NewestPageviews(
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview')
        );

        self::assertSame([
            'Url 10 - User-Agent 10',
            'Url 9 - User-Agent 9',
            'Url 8 - User-Agent 8',
            'Url 7 - User-Agent 7',
            'Url 6 - User-Agent 6',
            'Url 5 - User-Agent 5',
        ], $subject->getItems());
    }

    /**
     * @test
     */
    public function respectsMaxResults(): void
    {
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview');
        for ($i = 1; $i <= 10; $i++) {
            $connection->insert('tx_tracking_pageview', [
                'pid' => $i,
                'url' => 'Url ' . $i,
                'user_agent' => 'User-Agent ' . $i,
                'crdate' => $i,
            ]);
        }

        $subject = new NewestPageviews(
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
            2
        );

        self::assertSame([
            'Url 10 - User-Agent 10',
            'Url 9 - User-Agent 9',
        ], $subject->getItems());
    }

    /**
     * @test
     */
    public function respectsPagesToExclude(): void
    {
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview');
        for ($i = 1; $i <= 10; $i++) {
            $connection->insert('tx_tracking_pageview', [
                'pid' => $i,
                'url' => 'Url ' . $i,
                'user_agent' => 'User-Agent ' . $i,
                'crdate' => $i,
            ]);
        }

        $subject = new NewestPageviews(
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
            6,
            [9]
        );

        self::assertSame([
            'Url 10 - User-Agent 10',
            'Url 8 - User-Agent 8',
            'Url 7 - User-Agent 7',
            'Url 6 - User-Agent 6',
            'Url 5 - User-Agent 5',
            'Url 4 - User-Agent 4',
        ], $subject->getItems());
    }

    /**
     * @test
     */
    public function respectsLimitToLanguages(): void
    {
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview');
        for ($i = 1; $i <= 10; $i++) {
            $connection->insert('tx_tracking_pageview', [
                'pid' => $i,
                'url' => 'Url ' . $i,
                'sys_language_uid' => $i % 2,
                'user_agent' => 'User-Agent ' . $i,
                'crdate' => $i,
            ]);
        }

        $subject = new NewestPageviews(
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
            6,
            [],
            [1]
        );

        self::assertSame([
            'Url 9 - User-Agent 9',
            'Url 7 - User-Agent 7',
            'Url 5 - User-Agent 5',
            'Url 3 - User-Agent 3',
            'Url 1 - User-Agent 1',
        ], $subject->getItems());
    }
}
