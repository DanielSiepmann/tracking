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

use DanielSiepmann\Tracking\Domain\Model\Pageview;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * @covers DanielSiepmann\Tracking\Domain\Model\Pageview
 */
class PageviewTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function canBeCreated(): void
    {
        $language = $this->prophesize(SiteLanguage::class);

        $subject = new Pageview(
            0,
            $language->reveal(),
            new \DateTimeImmutable(),
            0,
            '',
            ''
        );

        static::assertInstanceOf(Pageview::class, $subject);
    }

    /**
     * @test
     */
    public function returnsPageUid(): void
    {
        $language = $this->prophesize(SiteLanguage::class);

        $subject = new Pageview(
            500,
            $language->reveal(),
            new \DateTimeImmutable(),
            0,
            '',
            ''
        );

        static::assertSame(500, $subject->getPageUid());
    }

    /**
     * @test
     */
    public function returnsLanguage(): void
    {
        $language = $this->prophesize(SiteLanguage::class);

        $subject = new Pageview(
            0,
            $language->reveal(),
            new \DateTimeImmutable(),
            0,
            '',
            ''
        );

        static::assertSame($language->reveal(), $subject->getLanguage());
    }

    /**
     * @test
     */
    public function returnsCrdate(): void
    {
        $language = $this->prophesize(SiteLanguage::class);
        $crdate = new \DateTimeImmutable();

        $subject = new Pageview(
            0,
            $language->reveal(),
            $crdate,
            0,
            '',
            ''
        );

        static::assertSame($crdate, $subject->getCrdate());
    }

    /**
     * @test
     */
    public function returnsPageType(): void
    {
        $language = $this->prophesize(SiteLanguage::class);

        $subject = new Pageview(
            0,
            $language->reveal(),
            new \DateTimeImmutable(),
            999,
            '',
            ''
        );

        static::assertSame(999, $subject->getPageType());
    }

    /**
     * @test
     */
    public function returnsUrl(): void
    {
        $language = $this->prophesize(SiteLanguage::class);

        $subject = new Pageview(
            0,
            $language->reveal(),
            new \DateTimeImmutable(),
            0,
            'https://example.com/path.html',
            ''
        );

        static::assertSame('https://example.com/path.html', $subject->getUrl());
    }

    /**
     * @test
     */
    public function returnsUserAgent(): void
    {
        $language = $this->prophesize(SiteLanguage::class);

        $subject = new Pageview(
            0,
            $language->reveal(),
            new \DateTimeImmutable(),
            0,
            '',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0'
        );

        static::assertSame(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0',
            $subject->getUserAgent()
        );
    }

    /**
     * @test
     */
    public function returnsZeroAsDefaultUid(): void
    {
        $language = $this->prophesize(SiteLanguage::class);

        $subject = new Pageview(
            0,
            $language->reveal(),
            new \DateTimeImmutable(),
            0,
            '',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0'
        );

        static::assertSame(
            0,
            $subject->getUid()
        );
    }

    /**
     * @test
     */
    public function returnsSetAsUid(): void
    {
        $language = $this->prophesize(SiteLanguage::class);

        $subject = new Pageview(
            0,
            $language->reveal(),
            new \DateTimeImmutable(),
            0,
            '',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0',
            10
        );

        static::assertSame(
            10,
            $subject->getUid()
        );
    }

    /**
     * @test
     * @dataProvider possibleUserStringWithOperatingSystems
     * @testdox Operating system $expectedOperatingSystem is extracted from UserAgent string: $userAgent
     */
    public function returnsOperatingSystem(string $userAgent, string $expectedOperatingSystem): void
    {
        $language = $this->prophesize(SiteLanguage::class);

        $subject = new Pageview(
            0,
            $language->reveal(),
            new \DateTimeImmutable(),
            0,
            '',
            $userAgent
        );

        static::assertSame(
            $expectedOperatingSystem,
            $subject->getOperatingSystem()
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
}
