<?php

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

use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequestContext;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase as TestCase;

/**
 * @testdox Pageviews are
 *
 * @coversNothing
 */
class PageviewTest extends TestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/tracking',
    ];

    protected array $pathsToLinkInTestInstance = [
        'typo3conf/ext/tracking/Tests/Functional/Fixtures/sites' => 'typo3conf/sites',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importDataSet('EXT:tracking/Tests/Functional/Fixtures/Pages.xml');
        $this->setUpFrontendRootPage(1, [
            'EXT:tracking/Tests/Functional/Fixtures/Rendering.typoscript',
        ]);
    }

    /**
     * @test
     */
    public function trackedWhenAllowed(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withHeader('User-Agent', 'Mozilla/5.0 (Macintosh; Intel Mac OS X x.y; rv:42.0) Gecko/20100101 Firefox/42.0');
        $response = $this->executeFrontendRequest($request);

        self::assertSame(200, $response->getStatusCode());

        $records = $this->getAllRecords('tx_tracking_pageview');
        self::assertCount(1, $records);
        self::assertSame('1', (string)$records[0]['pid']);
        self::assertSame('1', (string)$records[0]['uid']);
        self::assertSame('http://localhost/?id=1', $records[0]['url']);
        self::assertSame('Mozilla/5.0 (Macintosh; Intel Mac OS X x.y; rv:42.0) Gecko/20100101 Firefox/42.0', $records[0]['user_agent']);
        self::assertSame('Macintosh', $records[0]['operating_system']);
        self::assertSame('0', (string)$records[0]['type']);
    }

    /**
     * @test
     */
    public function notTrackedWhenDisallowed(): void
    {
        $this->importDataSet('EXT:tracking/Tests/Functional/Fixtures/BackendUser.xml');
        $this->setUpBackendUser(1);

        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $context = new InternalRequestContext();
        $context = $context->withBackendUserId(1);
        $response = $this->executeFrontendRequest($request, $context);

        self::assertSame(200, $response->getStatusCode());

        $records = $this->getAllRecords('tx_tracking_pageview');
        self::assertCount(0, $records);
    }

    /**
     * @test
     *
     * @dataProvider possibleDeniedUserAgents
     */
    public function preventsTrackingOfUserAgents(string $userAgent): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withHeader('User-Agent', $userAgent);
        $response = $this->executeFrontendRequest($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertCount(0, $this->getAllRecords('tx_tracking_pageview'));
    }

    public static function possibleDeniedUserAgents(): array
    {
        return [
            'Uptime-Kuma' => [
                'userAgent' => 'Uptime-Kuma/1.21.2',
            ],
        ];
    }
}
