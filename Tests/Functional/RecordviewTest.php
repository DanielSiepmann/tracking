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

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;

#[TestDox('Recordviews are')]
#[CoversNothing]
final class RecordviewTest extends AbstractFunctionalTestCase
{
    protected array $pathsToLinkInTestInstance = [
        'typo3conf/ext/tracking/Tests/Functional/Fixtures/sites' => 'typo3conf/sites',
    ];

    protected array $configurationToUseInTestInstance = [
        'FE' => [
            'cacheHash' => [
                'enforceValidation' => false,
            ],
        ],
    ];

    protected function setUp(): void
    {
        $this->testExtensionsToLoad[] = 'typo3conf/ext/tracking/Tests/Functional/Fixtures/Extensions/recordview';
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Pages.php');
        $this->setUpFrontendRootPage(1, [
            'EXT:tracking/Tests/Functional/Fixtures/Rendering.typoscript',
        ]);
    }

    #[Test]
    public function trackedWhenAllowed(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('topic_id', 1);
        $request = $request->withHeader('User-Agent', 'Mozilla/5.0 (Macintosh; Intel Mac OS X x.y; rv:42.0) Gecko/20100101 Firefox/42.0');
        $response = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $response->getStatusCode());

        $records = $this->getAllRecords('tx_tracking_recordview');
        self::assertCount(1, $records);
        self::assertSame('1', (string) $records[0]['pid']);
        self::assertSame('1', (string) $records[0]['uid']);
        self::assertSame('http://localhost/?id=1&topic_id=1', $records[0]['url']);
        self::assertSame('Mozilla/5.0 (Macintosh; Intel Mac OS X x.y; rv:42.0) Gecko/20100101 Firefox/42.0', $records[0]['user_agent']);
        self::assertSame('Macintosh', $records[0]['operating_system']);
        self::assertSame('sys_category_1', $records[0]['record']);
        self::assertSame('1', (string) $records[0]['record_uid']);
        self::assertSame('sys_category', $records[0]['record_table_name']);
    }

    #[Test]
    public function notTrackedWhenNotDetected(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $response = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $response->getStatusCode());

        $records = $this->getAllRecords('tx_tracking_recordview');
        self::assertCount(0, $records);
    }
}
