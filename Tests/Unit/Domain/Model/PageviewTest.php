<?php

declare(strict_types=1);

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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Pageview::class)]
class PageviewTest extends UnitTestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $language = self::createStub(SiteLanguage::class);

        $subject = new Pageview(
            0,
            $language,
            new DateTimeImmutable(),
            0,
            '',
            ''
        );

        self::assertInstanceOf(Pageview::class, $subject);
    }

    #[Test]
    public function returnsPageUid(): void
    {
        $language = self::createStub(SiteLanguage::class);

        $subject = new Pageview(
            500,
            $language,
            new DateTimeImmutable(),
            0,
            '',
            ''
        );

        self::assertSame(500, $subject->getPageUid());
    }

    #[Test]
    public function returnsLanguage(): void
    {
        $language = self::createStub(SiteLanguage::class);

        $subject = new Pageview(
            0,
            $language,
            new DateTimeImmutable(),
            0,
            '',
            ''
        );

        self::assertSame($language, $subject->getLanguage());
    }

    #[Test]
    public function returnsCrdate(): void
    {
        $language = self::createStub(SiteLanguage::class);
        $crdate = new DateTimeImmutable();

        $subject = new Pageview(
            0,
            $language,
            $crdate,
            0,
            '',
            ''
        );

        self::assertSame($crdate, $subject->getCrdate());
    }

    #[Test]
    public function returnsPageType(): void
    {
        $language = self::createStub(SiteLanguage::class);

        $subject = new Pageview(
            0,
            $language,
            new DateTimeImmutable(),
            999,
            '',
            ''
        );

        self::assertSame(999, $subject->getPageType());
    }

    #[Test]
    public function returnsUrl(): void
    {
        $language = self::createStub(SiteLanguage::class);

        $subject = new Pageview(
            0,
            $language,
            new DateTimeImmutable(),
            0,
            'https://example.com/path.html',
            ''
        );

        self::assertSame('https://example.com/path.html', $subject->getUrl());
    }

    #[Test]
    public function returnsUserAgent(): void
    {
        $language = self::createStub(SiteLanguage::class);

        $subject = new Pageview(
            0,
            $language,
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

    #[Test]
    public function returnsZeroAsDefaultUid(): void
    {
        $language = self::createStub(SiteLanguage::class);

        $subject = new Pageview(
            0,
            $language,
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

    #[Test]
    public function returnsSetAsUid(): void
    {
        $language = self::createStub(SiteLanguage::class);

        $subject = new Pageview(
            0,
            $language,
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

    #[Test]
    public function returnsOperatingSystem(): void
    {
        $language = self::createStub(SiteLanguage::class);

        $subject = new Pageview(
            0,
            $language,
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
