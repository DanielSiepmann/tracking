<?php

declare(strict_types=1);

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
use DanielSiepmann\Tracking\Domain\Repository\Site;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site as SiteEntity;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Factory::class)]
class FactoryTest extends UnitTestCase
{
    #[Test]
    public function returnsPageviewFromRequest(): void
    {
        $routing = $this->createStub(PageArguments::class);
        $routing->method('getPageId')->willReturn(10);
        $routing->method('getPageType')->willReturn('0');

        $language = $this->createStub(SiteLanguage::class);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturnMap([
            ['routing', null, $routing],
            ['language', null, $language],
        ]);
        $request->method('getUri')->willReturn('');
        $request->method('getHeader')->willReturn([]);

        $subject = new Factory($this->createStub(Site::class));

        $result = $subject->fromRequest($request);
        self::assertInstanceOf(Pageview::class, $result);
    }

    #[Test]
    public function returnedPageviewContainsUserAgent(): void
    {
        $routing = $this->createStub(PageArguments::class);
        $routing->method('getPageId')->willReturn(10);
        $routing->method('getPageType')->willReturn('0');

        $language = $this->createStub(SiteLanguage::class);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturnMap([
            ['routing', null, $routing],
            ['language', null, $language],
        ]);
        $request->method('getUri')->willReturn('');
        $request->method('getHeader')->willReturn([
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0',
        ]);

        $subject = new Factory($this->createStub(Site::class));

        $result = $subject->fromRequest($request);
        self::assertSame(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0',
            $result->getUserAgent()
        );
    }

    #[Test]
    public function returnedPageviewContainsUri(): void
    {
        $routing = $this->createStub(PageArguments::class);
        $routing->method('getPageId')->willReturn(10);
        $routing->method('getPageType')->willReturn('0');

        $language = $this->createStub(SiteLanguage::class);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturnMap([
            ['routing', null, $routing],
            ['language', null, $language],
        ]);
        $request->method('getUri')->willReturn('https://example.com/path?query=params&some=more#anchor');
        $request->method('getHeader')->willReturn([]);

        $subject = new Factory($this->createStub(Site::class));

        $result = $subject->fromRequest($request);
        self::assertSame(
            'https://example.com/path?query=params&some=more#anchor',
            $result->getUrl()
        );
    }

    #[Test]
    public function returnedPageviewContainsPageType(): void
    {
        $routing = $this->createStub(PageArguments::class);
        $routing->method('getPageId')->willReturn(10);
        $routing->method('getPageType')->willReturn('50');

        $language = $this->createStub(SiteLanguage::class);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturnMap([
            ['routing', null, $routing],
            ['language', null, $language],
        ]);
        $request->method('getUri')->willReturn('');
        $request->method('getHeader')->willReturn([]);

        $subject = new Factory($this->createStub(Site::class));

        $result = $subject->fromRequest($request);
        self::assertSame(
            50,
            $result->getPageType()
        );
    }

    #[Test]
    public function returnedPageviewContainsDateTime(): void
    {
        $routing = $this->createStub(PageArguments::class);
        $routing->method('getPageId')->willReturn(10);
        $routing->method('getPageType')->willReturn('0');

        $language = $this->createStub(SiteLanguage::class);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturnMap([
            ['routing', null, $routing],
            ['language', null, $language],
        ]);
        $request->method('getUri')->willReturn('');
        $request->method('getHeader')->willReturn([]);

        $subject = new Factory($this->createStub(Site::class));

        $result = $subject->fromRequest($request);
        self::assertInstanceOf(DateTimeImmutable::class, $result->getCrdate());
    }

    #[Test]
    public function returnedPageviewContainsLanguage(): void
    {
        $routing = $this->createStub(PageArguments::class);
        $routing->method('getPageId')->willReturn(10);
        $routing->method('getPageType')->willReturn('0');

        $language = $this->createStub(SiteLanguage::class);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturnMap([
            ['routing', null, $routing],
            ['language', null, $language],
        ]);
        $request->method('getUri')->willReturn('');
        $request->method('getHeader')->willReturn([]);

        $subject = new Factory($this->createStub(Site::class));

        $result = $subject->fromRequest($request);
        self::assertInstanceOf(SiteLanguage::class, $result->getLanguage());
    }

    #[Test]
    public function returnedPageviewContainsPageId(): void
    {
        $routing = $this->createStub(PageArguments::class);
        $routing->method('getPageId')->willReturn(10);
        $routing->method('getPageType')->willReturn('0');

        $language = $this->createStub(SiteLanguage::class);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturnMap([
            ['routing', null, $routing],
            ['language', null, $language],
        ]);
        $request->method('getUri')->willReturn('');
        $request->method('getHeader')->willReturn([]);

        $subject = new Factory($this->createStub(Site::class));

        $result = $subject->fromRequest($request);
        self::assertSame(
            10,
            $result->getPageUid()
        );
    }

    #[Test]
    public function returnsPageviewFromDbRow(): void
    {
        $siteLanguage = $this->createStub(SiteLanguage::class);
        $site = $this->createStub(SiteEntity::class);
        $site->method('getLanguageById')->willReturn($siteLanguage);
        $siteRepository = $this->createStub(Site::class);
        $siteRepository->method('findByPageUid')->willReturn($site);

        $subject = new Factory($siteRepository);

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
        self::assertSame($siteLanguage, $result->getLanguage());
        self::assertSame('1533906435', $result->getCrdate()->format('U'));
        self::assertSame(0, $result->getPageType());
        self::assertSame('https://example.com/path', $result->getUrl());
        self::assertSame('Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36', $result->getUserAgent());
    }
}
