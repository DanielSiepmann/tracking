<?php

namespace DanielSiepmann\Tracking\Tests\Unit\Domain\Pageview;

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
use DanielSiepmann\Tracking\Domain\Pageview\Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * @covers DanielSiepmann\Tracking\Domain\Pageview\Factory
 */
class FactoryTest extends TestCase
{
    /**
     * @test
     */
    public function returnsPageview(): void
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

        $result = Factory::fromRequest($request->reveal());
        static::assertInstanceOf(Pageview::class, $result);
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

        $result = Factory::fromRequest($request->reveal());
        static::assertSame(
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

        $result = Factory::fromRequest($request->reveal());
        static::assertSame(
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

        $result = Factory::fromRequest($request->reveal());
        static::assertSame(
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

        $result = Factory::fromRequest($request->reveal());
        static::assertInstanceOf(\DateTimeImmutable::class, $result->getCrdate());
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

        $result = Factory::fromRequest($request->reveal());
        static::assertInstanceOf(SiteLanguage::class, $result->getLanguage());
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

        $result = Factory::fromRequest($request->reveal());
        static::assertSame(
            10,
            $result->getPageUid()
        );
    }
}
