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

namespace DanielSiepmann\Tracking\Tests\Functional\Domain\Extractors;

use DanielSiepmann\Tracking\Domain\Extractors\Bots;
use DanielSiepmann\Tracking\Domain\Extractors\Bots\CustomBotParser;
use DanielSiepmann\Tracking\Domain\Model\Extractor;
use DanielSiepmann\Tracking\Domain\Model\Pageview;
use DanielSiepmann\Tracking\Domain\Model\Recordview;
use DeviceDetector\DeviceDetector;
use Prophecy\PhpUnit\ProphecyTrait;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase as TestCase;

/**
 * @covers \DanielSiepmann\Tracking\Domain\Extractors\Bots
 */
class BotsTest extends TestCase
{
    use ProphecyTrait;

    protected $testExtensionsToLoad = [
        'typo3conf/ext/tracking',
    ];

    /**
     * @test
     * @dataProvider possibleUserStringWithBots
     * @testdox Bot $expectedBot is extracted from Pageview UserAgent string: $userAgent
     */
    public function returnsBotForPageview(string $userAgent, string $expectedBot): void
    {
        $model = $this->prophesize(Pageview::class);
        $model->getUserAgent()->willReturn($userAgent);

        $extractor = new Bots(new CustomBotParser());
        $tags = $extractor->extractTagFromPageview($model->reveal());

        self::assertCount(2, $tags);
        self::assertSame('bot', $tags[0]->getName());
        self::assertSame('yes', $tags[0]->getValue());
        self::assertSame('bot_name', $tags[1]->getName());
        self::assertSame($expectedBot, $tags[1]->getValue());
    }

    /**
     * @test
     * @dataProvider possibleUserStringWithBots
     * @testdox Bot $expectedBot is extracted from Recordview UserAgent string: $userAgent
     */
    public function returnsBotForRecordview(string $userAgent, string $expectedBot): void
    {
        $model = $this->prophesize(Recordview::class);
        $model->getUserAgent()->willReturn($userAgent);

        $extractor = new Bots(new CustomBotParser());
        $tags = $extractor->extractTagFromRecordview($model->reveal());

        self::assertCount(2, $tags);
        self::assertSame('bot', $tags[0]->getName());
        self::assertSame('yes', $tags[0]->getValue());
        self::assertSame('bot_name', $tags[1]->getName());
        self::assertSame($expectedBot, $tags[1]->getValue());
    }

    public function possibleUserStringWithBots(): array
    {
        return [
            0 => [
                'userAgent' => 'nettle (+https://www.nettle.sk)',
                'expectedBot' => 'Nettle',
            ],
            1 => [
                'userAgent' => 'MauiBot (crawler.feedback+wc@gmail.com)',
                'expectedBot' => 'Generic Bot',
            ],
            2 => [
                'userAgent' => 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)',
                'expectedBot' => 'Facebook External Hit',
            ],
            3 => [
                'userAgent' => 'Java/11.0.10',
                'expectedBot' => 'Java',
            ],
            4 => [
                'userAgent' => 'newspaper/0.2.8',
                'expectedBot' => 'Newspaper',
            ],
            5 => [
                'userAgent' => 'Tiny Tiny RSS/21.05-326850845 (http://tt-rss.org/)',
                'expectedBot' => 'Tiny Tiny RSS',
            ],
            6 => [
                'userAgent' => 'BacklinkCrawler (http://www.backlinktest.com/crawler.html)',
                'expectedBot' => 'BacklinkCrawler',
            ],
            7 => [
                'userAgent' => 'CCBot/2.0 (https://commoncrawl.org/faq/)',
                'expectedBot' => 'ccBot crawler',
            ],
            8 => [
                'userAgent' => 'Tiny Tiny RSS/21.04-e8f78181f (http://tt-rss.org/)',
                'expectedBot' => 'Tiny Tiny RSS',
            ],
            9 => [
                'userAgent' => 'ltx71 - (http://ltx71.com/)',
                'expectedBot' => 'LTX71',
            ],
            10 => [
                'userAgent' => 'FeedFetcher-Google; (+http://www.google.com/feedfetcher.html)',
                'expectedBot' => 'Googlebot',
            ],
            11 => [
                'userAgent' => 'colly - https://github.com/gocolly/colly',
                'expectedBot' => 'colly',
            ],
            12 => [
                'userAgent' => 'WordPress.com; https://serdargunes.wordpress.com',
                'expectedBot' => 'WordPress',
            ],
            13 => [
                'userAgent' => 'Tiny Tiny RSS/21.03-2f402d598 (http://tt-rss.org/)',
                'expectedBot' => 'Tiny Tiny RSS',
            ],
            14 => [
                'userAgent' => 'netEstate NE Crawler (+http://www.website-datenbank.de/)',
                'expectedBot' => 'netEstate',
            ],
            15 => [
                'userAgent' => 'python-requests/2.18.1',
                'expectedBot' => 'Python Requests',
            ],
            16 => [
                'userAgent' => 'PocketParser/2.0 (+https://getpocket.com/pocketparser_ua)',
                'expectedBot' => 'PocketParser',
            ],
            17 => [
                'userAgent' => 'Faraday v0.17.3',
                'expectedBot' => 'Faraday',
            ],
            18 => [
                'userAgent' => 'hgfAlphaXCrawl/0.1 (+https://www.fim.uni-passau.de/data-science/forschung/open-search)',
                'expectedBot' => 'Generic Bot',
            ],
            19 => [
                'userAgent' => 'Upflow/1.0',
                'expectedBot' => 'Upflow',
            ],
            20 => [
                'userAgent' => 'crusty/0.12.0',
                'expectedBot' => 'Crusty',
            ],
            21 => [
                'userAgent' => 'TelegramBot (like TwitterBot)',
                'expectedBot' => 'TelegramBot',
            ],
            22 => [
                'userAgent' => 'python-requests/2.25.1',
                'expectedBot' => 'Python Requests',
            ],
        ];
    }
}
