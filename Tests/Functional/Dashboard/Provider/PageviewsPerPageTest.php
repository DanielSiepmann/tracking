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

use DanielSiepmann\Tracking\Dashboard\Provider\PageviewsPerPage;
use DanielSiepmann\Tracking\Tests\Functional\AbstractFunctionalTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;

#[CoversClass(PageviewsPerPage::class)]
final class PageviewsPerPageTest extends AbstractFunctionalTestCase
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
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/Pages.php');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview');
        for ($i = 1; $i <= 10; $i++) {
            $connection->insert('tx_tracking_pageview', [
                'pid' => $i,
                'crdate' => time(),
            ]);
        }

        $subject = new PageviewsPerPage(
            $this->get(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
            $this->get(PageRepository::class)
        );

        $result = $subject->getChartData();
        self::assertSame([
            'Page 10',
            'Page 9',
            'Page 8',
            'Page 7',
            'Page 6',
            'Page 5',
        ], $result['labels']);
        self::assertCount(6, $result['datasets'][0]['data']);
    }

    #[Test]
    public function respectedOrdering(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/Pages.php');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview');
        $connection->insert('tx_tracking_pageview', [
            'pid' => 1,
            'crdate' => time(),
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 2,
            'crdate' => time(),
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 3,
            'crdate' => time(),
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 2,
            'crdate' => time(),
        ]);

        $subject = new PageviewsPerPage(
            $this->get(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
            $this->get(PageRepository::class)
        );

        $result = $subject->getChartData();
        self::assertSame([
            'Page 2',
            'Page 3',
            'Page 1',
        ], $result['labels']);
        self::assertCount(3, $result['datasets'][0]['data']);
    }

    #[Test]
    public function respectedNumberOfDays(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/Pages.php');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview');
        $connection->insert('tx_tracking_pageview', [
            'pid' => 1,
            'crdate' => strtotime('-3 days'),
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 2,
            'crdate' => strtotime('-2 days'),
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 3,
            'crdate' => strtotime('-1 days'),
        ]);

        $subject = new PageviewsPerPage(
            $this->get(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
            $this->get(PageRepository::class),
            2
        );

        $result = $subject->getChartData();
        self::assertSame([
            'Page 3',
            'Page 2',
        ], $result['labels']);
        self::assertCount(2, $result['datasets'][0]['data']);
    }

    #[Test]
    public function respectedMaxResults(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/Pages.php');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview');
        for ($i = 1; $i <= 10; $i++) {
            $connection->insert('tx_tracking_pageview', [
                'pid' => $i,
                'crdate' => time(),
            ]);
        }

        $subject = new PageviewsPerPage(
            $this->get(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
            $this->get(PageRepository::class),
            31,
            4
        );

        $result = $subject->getChartData();
        self::assertSame([
            'Page 10',
            'Page 9',
            'Page 8',
            'Page 7',
        ], $result['labels']);
        self::assertCount(4, $result['datasets'][0]['data']);
    }

    #[Test]
    public function respectedExcludedPages(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/Pages.php');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview');
        for ($i = 1; $i <= 10; $i++) {
            $connection->insert('tx_tracking_pageview', [
                'pid' => $i,
                'crdate' => time(),
            ]);
        }

        $subject = new PageviewsPerPage(
            $this->get(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
            $this->get(PageRepository::class),
            31,
            6,
            [1, 2, 3, 4, 5, 6]
        );

        $result = $subject->getChartData();
        self::assertSame([
            'Page 10',
            'Page 9',
            'Page 8',
            'Page 7',
        ], $result['labels']);
        self::assertCount(4, $result['datasets'][0]['data']);
    }

    #[Test]
    public function localizedRecordTitlesIfLimitedToSingleLanguage(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/Pages.php');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview');
        $connection->insert('tx_tracking_pageview', [
            'pid' => 1,
            'crdate' => time(),
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 1,
            'sys_language_uid' => 1,
            'crdate' => time(),
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 2,
            'crdate' => time(),
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 2,
            'sys_language_uid' => 1,
            'crdate' => time(),
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 3,
            'crdate' => time(),
        ]);

        $subject = new PageviewsPerPage(
            $this->get(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
            $this->get(PageRepository::class),
            31,
            6,
            [],
            [1]
        );

        $result = $subject->getChartData();
        self::assertSame([
            'Page 2',
            'Seite 1',
        ], $result['labels']);
        self::assertCount(2, $result['datasets'][0]['data']);
    }

    #[Test]
    public function defaultLanguageTitleIsUsedIfMultipleLanguagesAreAllowed(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/Pages.php');
        $connection = $this->getConnectionPool()->getConnectionForTable('tx_tracking_pageview');
        $connection->insert('tx_tracking_pageview', [
            'pid' => 1,
            'crdate' => time(),
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 1,
            'sys_language_uid' => 1,
            'crdate' => time(),
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 2,
            'crdate' => time(),
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 2,
            'sys_language_uid' => 1,
            'crdate' => time(),
        ]);
        $connection->insert('tx_tracking_pageview', [
            'pid' => 3,
            'crdate' => time(),
        ]);

        $subject = new PageviewsPerPage(
            $this->get(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview'),
            $this->get(PageRepository::class),
            31,
            6,
            [],
            [1, 0]
        );

        $result = $subject->getChartData();
        self::assertSame([
            'Page 2',
            'Page 1',
            'Page 3',
        ], $result['labels']);
        self::assertCount(3, $result['datasets'][0]['data']);
    }
}
