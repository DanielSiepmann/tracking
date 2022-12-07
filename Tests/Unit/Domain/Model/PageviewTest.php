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
use DateTimeImmutable;
use Prophecy\PhpUnit\ProphecyTrait;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase as TestCase;

/**
 * @covers \DanielSiepmann\Tracking\Domain\Model\Pageview
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
            new DateTimeImmutable(),
            0,
            '',
            ''
        );

        self::assertInstanceOf(Pageview::class, $subject);
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
            new DateTimeImmutable(),
            0,
            '',
            ''
        );

        self::assertSame(500, $subject->getPageUid());
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
            new DateTimeImmutable(),
            0,
            '',
            ''
        );

        self::assertSame($language->reveal(), $subject->getLanguage());
    }

    /**
     * @test
     */
    public function returnsCrdate(): void
    {
        $language = $this->prophesize(SiteLanguage::class);
        $crdate = new DateTimeImmutable();

        $subject = new Pageview(
            0,
            $language->reveal(),
            $crdate,
            0,
            '',
            ''
        );

        self::assertSame($crdate, $subject->getCrdate());
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
            new DateTimeImmutable(),
            999,
            '',
            ''
        );

        self::assertSame(999, $subject->getPageType());
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
            new DateTimeImmutable(),
            0,
            'https://example.com/path.html',
            ''
        );

        self::assertSame('https://example.com/path.html', $subject->getUrl());
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
            new DateTimeImmutable(),
            0,
            '',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0'
        );

        self::assertSame(
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
            new DateTimeImmutable(),
            0,
            '',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0'
        );

        self::assertSame(
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
            new DateTimeImmutable(),
            0,
            '',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0',
            10
        );

        self::assertSame(
            10,
            $subject->getUid()
        );
    }

    /**
     * @test
     */
    public function returnsOperatingSystem(): void
    {
        $language = $this->prophesize(SiteLanguage::class);

        $subject = new Pageview(
            0,
            $language->reveal(),
            new DateTimeImmutable(),
            0,
            '',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36'
        );

        self::assertSame(
            'Linux',
            $subject->getOperatingSystem()
        );
    }
}
