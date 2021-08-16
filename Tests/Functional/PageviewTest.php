<?php

declare(strict_types=1);

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

namespace DanielSiepmann\Tracking\Tests\Functional;

use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequestContext;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase as TestCase;

/**
 * @testdox Pageviews are
 */
class PageviewTest extends TestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/tracking',
    ];

    protected $pathsToLinkInTestInstance = [
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
        self::assertSame(1, $records[0]['pid']);
        self::assertSame(1, $records[0]['uid']);
        self::assertSame('http://localhost/?id=1', $records[0]['url']);
        self::assertSame('Mozilla/5.0 (Macintosh; Intel Mac OS X x.y; rv:42.0) Gecko/20100101 Firefox/42.0', $records[0]['user_agent']);
        self::assertSame('0', (string)$records[0]['type']);

        $records = $this->getAllRecords('tx_tracking_tag');
        self::assertCount(2, $records);

        self::assertSame(1, $records[0]['pid']);
        self::assertSame(1, $records[0]['record_uid']);
        self::assertSame('tx_tracking_pageview', $records[0]['record_table_name']);
        self::assertSame('bot', $records[0]['name']);
        self::assertSame('no', $records[0]['value']);

        self::assertSame(1, $records[1]['pid']);
        self::assertSame(1, $records[1]['record_uid']);
        self::assertSame('tx_tracking_pageview', $records[1]['record_table_name']);
        self::assertSame('os', $records[1]['name']);
        self::assertSame('Macintosh', $records[1]['value']);
    }

    /**
     * @test
     */
    public function trackedWithBotResolvedToTags(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withHeader('User-Agent', 'Slackbot-LinkExpanding 1.0 (+https://api.slack.com/robots)');
        $response = $this->executeFrontendRequest($request);

        self::assertSame(200, $response->getStatusCode());

        $records = $this->getAllRecords('tx_tracking_pageview');
        self::assertCount(1, $records);
        self::assertSame(1, $records[0]['pid']);
        self::assertSame(1, $records[0]['uid']);
        self::assertSame('http://localhost/?id=1', $records[0]['url']);
        self::assertSame('Slackbot-LinkExpanding 1.0 (+https://api.slack.com/robots)', $records[0]['user_agent']);
        self::assertSame('0', (string)$records[0]['type']);

        $records = $this->getAllRecords('tx_tracking_tag');
        self::assertCount(3, $records);

        self::assertSame(1, $records[0]['pid']);
        self::assertSame(1, $records[0]['record_uid']);
        self::assertSame('tx_tracking_pageview', $records[0]['record_table_name']);
        self::assertSame('bot', $records[0]['name']);
        self::assertSame('yes', $records[0]['value']);

        self::assertSame(1, $records[1]['pid']);
        self::assertSame(1, $records[1]['record_uid']);
        self::assertSame('tx_tracking_pageview', $records[1]['record_table_name']);
        self::assertSame('bot_name', $records[1]['name']);
        self::assertSame('Slackbot', $records[1]['value']);

        self::assertSame(1, $records[2]['pid']);
        self::assertSame(1, $records[2]['record_uid']);
        self::assertSame('tx_tracking_pageview', $records[2]['record_table_name']);
        self::assertSame('os', $records[2]['name']);
        self::assertSame('Unkown', $records[2]['value']);
    }

    /**
     * @test
     */
    public function notTrackedWhenDisallowed(): void
    {
        $this->setUpBackendUserFromFixture(1);

        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $context = new InternalRequestContext();
        $context = $context->withBackendUserId(1);
        $response = $this->executeFrontendRequest($request, $context);

        self::assertSame(200, $response->getStatusCode());

        self::assertCount(0, $this->getAllRecords('tx_tracking_pageview'));
        self::assertCount(0, $this->getAllRecords('tx_tracking_tag'));
    }
}
