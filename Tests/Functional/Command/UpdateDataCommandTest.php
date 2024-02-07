<?php

declare(strict_types=1);

namespace DanielSiepmann\Tracking\Tests\Functional\Command;

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

use DanielSiepmann\Tracking\Command\UpdateDataCommand;
use DanielSiepmann\Tracking\Tests\Functional\AbstractFunctionalTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[CoversClass(UpdateDataCommand::class)]
final class UpdateDataCommandTest extends AbstractFunctionalTestCase
{
    protected array $pathsToLinkInTestInstance = [
        'typo3conf/ext/tracking/Tests/Functional/Fixtures/sites' => 'typo3conf/sites',
    ];

    #[Test]
    public function updatesAllEntriesWithMissingOperatingSystem(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../Fixtures/UpdateDataCommandTest/PageviewsWithMissingOperatingSystem.php');

        $subject = GeneralUtility::makeInstance(UpdateDataCommand::class);
        $tester = new CommandTester($subject);
        $tester->execute([], ['capture_stderr_separately' => true]);

        self::assertSame(0, $tester->getStatusCode());

        $records = $this->getAllRecords('tx_tracking_pageview');
        self::assertCount(2, $records);
        self::assertSame('Linux', $records[0]['operating_system']);
        self::assertSame('Android', $records[1]['operating_system']);
    }

    #[Test]
    public function doesNotChangeExistingOperatingSystem(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../Fixtures/UpdateDataCommandTest/PageviewsWithOperatingSystem.php');

        $subject = GeneralUtility::makeInstance(UpdateDataCommand::class);
        $tester = new CommandTester($subject);
        $tester->execute([], ['capture_stderr_separately' => true]);

        self::assertSame(0, $tester->getStatusCode());

        $records = $this->getAllRecords('tx_tracking_pageview');
        self::assertCount(2, $records);
        self::assertSame('Linux', $records[0]['operating_system']);
        self::assertSame('Android', $records[1]['operating_system']);
    }

    #[Test]
    public function doesNothingIfNoRecordExists(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../Fixtures/Pages.php');

        $subject = GeneralUtility::makeInstance(UpdateDataCommand::class);
        $tester = new CommandTester($subject);
        $tester->execute([], ['capture_stderr_separately' => true]);

        self::assertSame(0, $tester->getStatusCode());

        $records = $this->getAllRecords('tx_tracking_pageview');
        self::assertCount(0, $records);
    }
}
