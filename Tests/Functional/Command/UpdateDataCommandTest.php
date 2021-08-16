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

namespace DanielSiepmann\Tracking\Tests\Functional\Command;

use DanielSiepmann\Tracking\Command\UpdateDataCommand;
use DanielSiepmann\Tracking\Extension;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase as TestCase;

/**
 * @covers \DanielSiepmann\Tracking\Command\UpdateDataCommand
 */
class UpdateDataCommandTest extends TestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/tracking',
    ];

    protected $pathsToLinkInTestInstance = [
        'typo3conf/ext/tracking/Tests/Functional/Fixtures/sites' => 'typo3conf/sites',
    ];

    /**
     * @test
     */
    public function updatesAllEntriesWithMissingOperatingSystem(): void
    {
        $this->importDataSet('EXT:tracking/Tests/Functional/Fixtures/UpdateDataCommandTest/WithMissingOperatingSystem.xml');

        $subject = GeneralUtility::makeInstance(UpdateDataCommand::class);
        $tester = new CommandTester($subject);
        $tester->execute([], ['capture_stderr_separately' => true]);

        self::assertSame(0, $tester->getStatusCode());

        $records = $this->getAllRecords('tx_tracking_tag');
        self::assertCount(8, $records);
        self::assertSame([
            'uid'=> '1',
            'pid'=> '1',
            'tstamp'=> '1630649915',
            'crdate'=> '1630649915',
            'cruser_id'=> '0',
            'record_uid' => '1',
            'record_table_name' => 'tx_tracking_pageview',
            'name' => 'bot',
            'value' => 'no',
            'compatible_version' => Extension::getCompatibleVersionNow(),
        ], array_map('strval', $records[0]));
        self::assertSame([
            'uid'=> '2',
            'pid'=> '1',
            'tstamp'=> '1630649915',
            'crdate'=> '1630649915',
            'cruser_id'=> '0',
            'record_uid' => '1',
            'record_table_name' => 'tx_tracking_pageview',
            'name' => 'os',
            'value' => 'Linux',
            'compatible_version' => Extension::getCompatibleVersionNow(),
        ], array_map('strval', $records[1]));
        self::assertSame([
            'uid'=> '3',
            'pid'=> '1',
            'tstamp'=> '1630649916',
            'crdate'=> '1630649916',
            'cruser_id'=> '0',
            'record_uid' => '2',
            'record_table_name' => 'tx_tracking_pageview',
            'name' => 'bot',
            'value' => 'no',
            'compatible_version' => Extension::getCompatibleVersionNow(),
        ], array_map('strval', $records[2]));
        self::assertSame([
            'uid'=> '4',
            'pid'=> '1',
            'tstamp'=> '1630649916',
            'crdate'=> '1630649916',
            'cruser_id'=> '0',
            'record_uid' => '2',
            'record_table_name' => 'tx_tracking_pageview',
            'name' => 'os',
            'value' => 'Android',
            'compatible_version' => Extension::getCompatibleVersionNow(),
        ], array_map('strval', $records[3]));
        self::assertSame([
            'uid'=> '5',
            'pid'=> '1',
            'tstamp'=> '1630649915',
            'crdate'=> '1630649915',
            'cruser_id'=> '0',
            'record_uid' => '1',
            'record_table_name' => 'tx_tracking_recordview',
            'name' => 'bot',
            'value' => 'no',
            'compatible_version' => Extension::getCompatibleVersionNow(),
        ], array_map('strval', $records[4]));
        self::assertSame([
            'uid'=> '6',
            'pid'=> '1',
            'tstamp'=> '1630649915',
            'crdate'=> '1630649915',
            'cruser_id'=> '0',
            'record_uid' => '1',
            'record_table_name' => 'tx_tracking_recordview',
            'name' => 'os',
            'value' => 'Linux',
            'compatible_version' => Extension::getCompatibleVersionNow(),
        ], array_map('strval', $records[5]));
        self::assertSame([
            'uid'=> '7',
            'pid'=> '1',
            'tstamp'=> '1630649916',
            'crdate'=> '1630649916',
            'cruser_id'=> '0',
            'record_uid' => '2',
            'record_table_name' => 'tx_tracking_recordview',
            'name' => 'bot',
            'value' => 'no',
            'compatible_version' => Extension::getCompatibleVersionNow(),
        ], array_map('strval', $records[6]));
        self::assertSame([
            'uid'=> '8',
            'pid'=> '1',
            'tstamp'=> '1630649916',
            'crdate'=> '1630649916',
            'cruser_id'=> '0',
            'record_uid' => '2',
            'record_table_name' => 'tx_tracking_recordview',
            'name' => 'os',
            'value' => 'Android',
            'compatible_version' => Extension::getCompatibleVersionNow(),
        ], array_map('strval', $records[7]));
    }

    /**
     * @test
     */
    public function doesNotChangeExistingOperatingSystem(): void
    {
        $this->importDataSet('EXT:tracking/Tests/Functional/Fixtures/UpdateDataCommandTest/WithOperatingSystem.xml');

        $subject = GeneralUtility::makeInstance(UpdateDataCommand::class);
        $tester = new CommandTester($subject);
        $tester->execute([], ['capture_stderr_separately' => true]);

        self::assertSame(0, $tester->getStatusCode());

        $records = $this->getAllRecords('tx_tracking_tag');
        self::assertCount(4, $records);
        self::assertSame([
            'uid' => '3',
            'pid' => '1',
            'tstamp'=> '1630649915',
            'crdate'=> '1630649915',
            'cruser_id' => '0',
            'record_uid' => '1',
            'record_table_name' => 'tx_tracking_pageview',
            'name' => 'bot',
            'value' => 'no',
            'compatible_version' => Extension::getCompatibleVersionNow(),
        ], array_map('strval', $records[0]));
        self::assertSame([
            'uid' => '4',
            'pid' => '1',
            'tstamp'=> '1630649915',
            'crdate'=> '1630649915',
            'cruser_id' => '0',
            'record_uid' => '1',
            'record_table_name' => 'tx_tracking_pageview',
            'name' => 'os',
            'value' => 'Linux',
            'compatible_version' => Extension::getCompatibleVersionNow(),
        ], array_map('strval', $records[1]));
        self::assertSame([
            'uid' => '5',
            'pid' => '1',
            'tstamp'=> '1630649916',
            'crdate'=> '1630649916',
            'cruser_id' => '0',
            'record_uid' => '2',
            'record_table_name' => 'tx_tracking_pageview',
            'name' => 'bot',
            'value' => 'no',
            'compatible_version' => Extension::getCompatibleVersionNow(),
        ], array_map('strval', $records[2]));
        self::assertSame([
            'uid' => '6',
            'pid' => '1',
            'tstamp'=> '1630649916',
            'crdate'=> '1630649916',
            'cruser_id' => '0',
            'record_uid' => '2',
            'record_table_name' => 'tx_tracking_pageview',
            'name' => 'os',
            'value' => 'Android',
            'compatible_version' => Extension::getCompatibleVersionNow(),
        ], array_map('strval', $records[3]));
    }

    /**
     * @test
     */
    public function doesNothingIfNoRecordExists(): void
    {
        $this->importDataSet('EXT:tracking/Tests/Functional/Fixtures/Pages.xml');

        $subject = GeneralUtility::makeInstance(UpdateDataCommand::class);
        $tester = new CommandTester($subject);
        $tester->execute([], ['capture_stderr_separately' => true]);

        self::assertSame(0, $tester->getStatusCode());

        $records = $this->getAllRecords('tx_tracking_pageview');
        self::assertCount(0, $records);

        $records = $this->getAllRecords('tx_tracking_tag');
        self::assertCount(0, $records);
    }

    /**
     * @test
     */
    public function doesNothingIfAllRecordsAreCompatible(): void
    {
        $this->importDataSet('EXT:tracking/Tests/Functional/Fixtures/UpdateDataCommandTest/WithCompatibleVersion.xml');

        $subject = GeneralUtility::makeInstance(UpdateDataCommand::class);
        $tester = new CommandTester($subject);
        $tester->execute([], ['capture_stderr_separately' => true]);

        self::assertSame(0, $tester->getStatusCode());

        $records = $this->getAllRecords('tx_tracking_pageview');
        self::assertCount(1, $records);

        $records = $this->getAllRecords('tx_tracking_recordview');
        self::assertCount(1, $records);

        $records = $this->getAllRecords('tx_tracking_tag');
        self::assertCount(4, $records);
        foreach ($records as $record) {
            self::assertSame(1663773639, $record['crdate']);
        }
    }
}
