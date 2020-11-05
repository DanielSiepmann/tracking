<?php

namespace DanielSiepmann\Tracking\Tests\Unit\Domain\Model;

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

use DanielSiepmann\Tracking\Domain\Model\Extractor;
use DanielSiepmann\Tracking\Domain\Model\HasUserAgent;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers DanielSiepmann\Tracking\Domain\Model\Extractor
 */
class ExtractorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     * @dataProvider possibleUserStringWithOperatingSystems
     * @testdox Operating system $expectedOperatingSystem is extracted from UserAgent string: $userAgent
     */
    public function returnsOperatingSystem(string $userAgent, string $expectedOperatingSystem): void
    {
        $model = $this->prophesize(HasUserAgent::class);
        $model->getUserAgent()->willReturn($userAgent);

        static::assertSame(
            $expectedOperatingSystem,
            Extractor::getOperatingSystem($model->reveal())
        );
    }

    public function possibleUserStringWithOperatingSystems(): array
    {
        return [
            [
                'userAgent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
                'expectedOperatingSystem' => 'Linux',
            ],
            [
                'userAgent' => 'Dalvik/2.1.0 (Linux; U; Android 9; ONEPLUS A3003 Build/PKQ1.181203.001)',
                'expectedOperatingSystem' => 'Android',
            ],
            [
                'userAgent' => 'Apache-HttpClient/4.5.2 (Java/1.8.0_151)',
                'expectedOperatingSystem' => '',
            ],
            [
                'userAgent' => 'AwarioSmartBot/1.0 (+https://awario.com/bots.html; bots@awario.com)',
                'expectedOperatingSystem' => '',
            ],
            [
                'userAgent' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)',
                'expectedOperatingSystem' => 'Windows',
            ],
            [
                'userAgent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:73.0) Gecko/20100101 Firefox/73.0',
                'expectedOperatingSystem' => 'Macintosh',
            ],
            [
                'userAgent' => 'Mozilla/5.0 (X11; CrOS x86_64 12607.82.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.123 Safari/537.36',
                'expectedOperatingSystem' => 'Google Chrome OS',
            ],
            [
                'userAgent' => 'Mozilla/5.0 (X11; U; OpenBSD i386; en-US; rv:1.8.1.4) Gecko/20070704 Firefox/52.0',
                'expectedOperatingSystem' => 'OpenBSD',
            ],
            [
                'userAgent' => 'Mozilla/5.0 (iPad; CPU OS 13_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/80.0.3987.95 Mobile/15E148 Safari/604.1',
                'expectedOperatingSystem' => 'iOS',
            ],
            [
                'userAgent' => 'Mozilla/5.0 (iPhone; CPU OS 13_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) FxiOS/22.0  Mobile/15E148 Safari/605.1.15',
                'expectedOperatingSystem' => 'iOS',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider possibleUserStringWithBot
     * @testdox Bot $expectedBot is extracted from UserAgent string: $userAgent
     */
    public function returnsBot(string $userAgent, string $expectedBot): void
    {
        $model = $this->prophesize(HasUserAgent::class);
        $model->getUserAgent()->willReturn($userAgent);

        static::assertSame(
            $expectedBot,
            Extractor::getBot($model->reveal())
        );
    }

    public function possibleUserStringWithBot(): array
    {
        return [
            'No Bot' => [
                'userAgent' => '',
                'expectedBot' => '',
            ],
            // Software / social media
            'WhatsApp' => [
                'userAgent' => 'WhatsApp/2.20.47 A',
                'expectedBot' => 'whatsapp',
            ],
            'Mattermost' => [
                'userAgent' => 'mattermost-5.17.0',
                'expectedBot' => 'mattermost',
            ],
            'Slack' => [
                'userAgent' => 'Slackbot-LinkExpanding 1.0 (+https://api.slack.com/robots)',
                'expectedBot' => 'slack',
            ],
            'Mastodon' => [
                'userAgent' => 'http.rb/4.3.0 (Mastodon/3.1.3; +https://fosstodon.org/)',
                'expectedBot' => 'mastodon',
            ],
            'Twitter iPhone' => [
                'userAgent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/17D50 Twitter for iPhone/8.9',
                'expectedBot' => 'twitter',
            ],
            'Twitter Bot' => [
                'userAgent' => 'Twitterbot/1.0',
                'expectedBot' => 'twitter',
            ],
            'Twitter' => [
                'userAgent' => 'Twitter/8.2 CFNetwork/1121.2.2 Darwin/19.3.0',
                'expectedBot' => 'twitter',
            ],
            'Telegram' => [
                'userAgent' => 'TelegramBot (like TwitterBot)',
                'expectedBot' => 'telegram',
            ],
            // Searchmachine indexing
            'Googlebot' => [
                'userAgent' => 'Googlebot/2.1 (+http://www.google.com/bot.html)',
                'expectedBot' => 'google',
            ],
            'Bingbot' => [
                'userAgent' => 'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)',
                'expectedBot' => 'bing',
            ],
            'DuckDuckBot' => [
                'userAgent' => 'Mozilla/5.0 (Linux; Android 7.0) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/80.0.3987.132 Mobile Safari/537.36 DuckDuckGo/5',
                'expectedBot' => 'duckduckgo',
            ],
            'DuckDuckBot' => [
                'userAgent' => 'Mozilla/5.0 (compatible; DuckDuckGo-Favicons-Bot/1.0; +http://duckduckgo.com)',
                'expectedBot' => 'duckduckgo-favicon',
            ],
            // Aggregator
            'Feedly' => [
                'userAgent' => 'Feedly/1.0 (+http://www.feedly.com/fetcher.html; 1 subscribers; like FeedFetcher-Google)',
                'expectedBot' => 'feedly',
            ],
            'NextCloud-News' => [
                'userAgent' => 'NextCloud-News/1.0',
                'expectedBot' => 'nextcloud-news',
            ],
            'XING FeedReader' => [
                'userAgent' => 'XING FeedReader (xing.com)',
                'expectedBot' => 'xing-feedreader',
            ],
        ];
    }
}
