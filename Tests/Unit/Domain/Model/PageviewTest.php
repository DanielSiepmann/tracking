<?php

namespace DanielSiepmann\Tracking\Unit\Domain\Model;

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
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * @covers DanielSiepmann\Tracking\Domain\Model\Pageview
 */
class PageviewTest extends TestCase
{
    /**
     * @test
     */
    public function canBeCreated()
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
    public function returnsPageUid()
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
    public function returnsLanguage()
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
    public function returnsCrdate()
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
    public function returnsPageType()
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
    public function returnsUrl()
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
    public function returnsUserAgent()
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
}
