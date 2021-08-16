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

namespace DanielSiepmann\Tracking\Tests\Unit\Domain\Pageview;

use DanielSiepmann\Tracking\Domain\Model\Pageview;
use DanielSiepmann\Tracking\Domain\Pageview\Factory;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophet;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase as TestCase;

/**
 * @covers \DanielSiepmann\Tracking\Domain\Pageview\Factory
 */
class FactoryTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function returnsPageviewFromRequest(): void
    {
        $routing = $this->prophesize(PageArguments::class);
        $routing->getPageId()->willReturn(10);
        $routing->getPageType()->willReturn(0);

        $language = $this->prophesize(SiteLanguage::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('routing')->willReturn($routing->reveal());
        $request->getAttribute('language')->willReturn($language->reveal());
        $request->getUri()->willReturn('');
        $request->getHeader('User-Agent')->willReturn([]);

        $subject = new Factory($this->prophesize(SiteFinder::class)->reveal());

        $result = $subject->fromRequest($request->reveal());
        self::assertInstanceOf(Pageview::class, $result);
    }

    /**
     * @test
     */
    public function returnedPageviewContainsUserAgent(): void
    {
        $routing = $this->prophesize(PageArguments::class);
        $routing->getPageId()->willReturn(10);
        $routing->getPageType()->willReturn(0);

        $language = $this->prophesize(SiteLanguage::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('routing')->willReturn($routing->reveal());
        $request->getAttribute('language')->willReturn($language->reveal());
        $request->getUri()->willReturn('');
        $request->getHeader('User-Agent')->willReturn([
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0'
        ]);

        $subject = new Factory($this->prophesize(SiteFinder::class)->reveal());

        $result = $subject->fromRequest($request->reveal());
        self::assertSame(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0',
            $result->getUserAgent()
        );
    }

    /**
     * @test
     */
    public function returnedPageviewContainsUri(): void
    {
        $routing = $this->prophesize(PageArguments::class);
        $routing->getPageId()->willReturn(10);
        $routing->getPageType()->willReturn(0);

        $language = $this->prophesize(SiteLanguage::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('routing')->willReturn($routing->reveal());
        $request->getAttribute('language')->willReturn($language->reveal());
        $request->getUri()->willReturn('https://example.com/path?query=params&some=more#anchor');
        $request->getHeader('User-Agent')->willReturn([]);

        $subject = new Factory($this->prophesize(SiteFinder::class)->reveal());

        $result = $subject->fromRequest($request->reveal());
        self::assertSame(
            'https://example.com/path?query=params&some=more#anchor',
            $result->getUrl()
        );
    }

    /**
     * @test
     */
    public function returnedPageviewContainsPageType(): void
    {
        $routing = $this->prophesize(PageArguments::class);
        $routing->getPageId()->willReturn(10);
        $routing->getPageType()->willReturn(50);

        $language = $this->prophesize(SiteLanguage::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('routing')->willReturn($routing->reveal());
        $request->getAttribute('language')->willReturn($language->reveal());
        $request->getUri()->willReturn('');
        $request->getHeader('User-Agent')->willReturn([]);

        $subject = new Factory($this->prophesize(SiteFinder::class)->reveal());

        $result = $subject->fromRequest($request->reveal());
        self::assertSame(
            50,
            $result->getPageType()
        );
    }

    /**
     * @test
     */
    public function returnedPageviewContainsDateTime(): void
    {
        $routing = $this->prophesize(PageArguments::class);
        $routing->getPageId()->willReturn(10);
        $routing->getPageType()->willReturn(0);

        $language = $this->prophesize(SiteLanguage::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('routing')->willReturn($routing->reveal());
        $request->getAttribute('language')->willReturn($language->reveal());
        $request->getUri()->willReturn('');
        $request->getHeader('User-Agent')->willReturn([]);

        $subject = new Factory($this->prophesize(SiteFinder::class)->reveal());

        $result = $subject->fromRequest($request->reveal());
        self::assertInstanceOf(\DateTimeImmutable::class, $result->getCrdate());
    }

    /**
     * @test
     */
    public function returnedPageviewContainsLanguage(): void
    {
        $routing = $this->prophesize(PageArguments::class);
        $routing->getPageId()->willReturn(10);
        $routing->getPageType()->willReturn(0);

        $language = $this->prophesize(SiteLanguage::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('routing')->willReturn($routing->reveal());
        $request->getAttribute('language')->willReturn($language->reveal());
        $request->getUri()->willReturn('');
        $request->getHeader('User-Agent')->willReturn([]);

        $subject = new Factory($this->prophesize(SiteFinder::class)->reveal());

        $result = $subject->fromRequest($request->reveal());
        self::assertInstanceOf(SiteLanguage::class, $result->getLanguage());
    }

    /**
     * @test
     */
    public function returnedPageviewContainsPageId(): void
    {
        $routing = $this->prophesize(PageArguments::class);
        $routing->getPageId()->willReturn(10);
        $routing->getPageType()->willReturn(0);

        $language = $this->prophesize(SiteLanguage::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('routing')->willReturn($routing->reveal());
        $request->getAttribute('language')->willReturn($language->reveal());
        $request->getUri()->willReturn('');
        $request->getHeader('User-Agent')->willReturn([]);

        $subject = new Factory($this->prophesize(SiteFinder::class)->reveal());

        $result = $subject->fromRequest($request->reveal());
        self::assertSame(
            10,
            $result->getPageUid()
        );
    }

    /**
     * @test
     */
    public function returnsPageviewFromDbRow(): void
    {
        $siteLanguage = $this->prophesize(SiteLanguage::class);
        $site = $this->prophesize(Site::class);
        $site->getLanguageById(0)->willReturn($siteLanguage->reveal());
        $siteFinder = $this->prophesize(SiteFinder::class);
        $siteFinder->getSiteByPageId(2)->willReturn($site->reveal());

        $subject = new Factory($siteFinder->reveal());

        $result = $subject->fromDbRow([
            'uid' => 1,
            'pid' => 2,
            'sys_language_uid' => 0,
            'crdate' => 1533906435,
            'type' => 0,
            'url' => 'https://example.com/path',
            'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
        ]);

        self::assertInstanceOf(Pageview::class, $result);
        self::assertSame(1, $result->getUid());
        self::assertSame(2, $result->getPageUid());
        self::assertSame($siteLanguage->reveal(), $result->getLanguage());
        self::assertSame('1533906435', $result->getCrdate()->format('U'));
        self::assertSame(0, $result->getPageType());
        self::assertSame('https://example.com/path', $result->getUrl());
        self::assertSame('Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36', $result->getUserAgent());
    }
}
