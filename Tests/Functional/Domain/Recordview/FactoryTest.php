<?php

declare(strict_types=1);

namespace DanielSiepmann\Tracking\Tests\Unit\Domain\Recordview;

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

use DanielSiepmann\Tracking\Domain\Model\RecordRule;
use DanielSiepmann\Tracking\Domain\Model\Recordview;
use DanielSiepmann\Tracking\Domain\Recordview\Factory;
use DanielSiepmann\Tracking\Tests\Functional\AbstractFunctionalTestCase;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

#[CoversClass(Factory::class)]
final class FactoryTest extends AbstractFunctionalTestCase
{
    #[Test]
    public function returnsRecordviewFromRequest(): void
    {
        $rule = self::createStub(RecordRule::class);
        $rule->method('getUidExpression')->willReturn('request.getQueryParams()["category"]');
        $rule->method('getTableName')->willReturn('sys_category');

        $routing = self::createStub(PageArguments::class);
        $routing->method('getPageId')->willReturn(10);

        $language = self::createStub(SiteLanguage::class);

        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturnMap([
            ['routing', null, $routing],
            ['language', null, $language],
        ]);
        $request->method('getUri')->willReturn('');
        $request->method('getHeader')->willReturn([]);
        $request->method('getQueryParams')->willReturn([
            'category' => 10,
        ]);

        $subject = $this->get(Factory::class);

        $result = $subject->fromRequest($request, $rule);
        self::assertInstanceOf(Recordview::class, $result);
    }

    #[Test]
    public function returnedRecordviewContainsUserAgent(): void
    {
        $rule = self::createStub(RecordRule::class);
        $rule->method('getUidExpression')->willReturn('request.getQueryParams()["category"]');
        $rule->method('getTableName')->willReturn('sys_category');

        $routing = self::createStub(PageArguments::class);
        $routing->method('getPageId')->willReturn(10);

        $language = self::createStub(SiteLanguage::class);

        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturnMap([
            ['routing', null, $routing],
            ['language', null, $language],
        ]);
        $request->method('getUri')->willReturn('');
        $request->method('getHeader')->willReturn(['Some User Agent']);
        $request->method('getQueryParams')->willReturn([
            'category' => 10,
        ]);

        $subject = $this->get(Factory::class);

        $result = $subject->fromRequest($request, $rule);
        self::assertSame('Some User Agent', $result->getUserAgent());
    }

    #[Test]
    public function returnedRecordviewContainsUri(): void
    {
        $rule = self::createStub(RecordRule::class);
        $rule->method('getUidExpression')->willReturn('request.getQueryParams()["category"]');
        $rule->method('getTableName')->willReturn('sys_category');

        $routing = self::createStub(PageArguments::class);
        $routing->method('getPageId')->willReturn(10);

        $language = self::createStub(SiteLanguage::class);

        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturnMap([
            ['routing', null, $routing],
            ['language', null, $language],
        ]);
        $request->method('getUri')->willReturn('https://example.com');
        $request->method('getHeader')->willReturn(['']);
        $request->method('getQueryParams')->willReturn([
            'category' => 10,
        ]);

        $subject = $this->get(Factory::class);

        $result = $subject->fromRequest($request, $rule);
        self::assertSame('https://example.com', $result->getUrl());
    }

    #[Test]
    public function returnedRecordviewContainsDateTime(): void
    {
        $rule = self::createStub(RecordRule::class);
        $rule->method('getUidExpression')->willReturn('request.getQueryParams()["category"]');
        $rule->method('getTableName')->willReturn('sys_category');

        $routing = self::createStub(PageArguments::class);
        $routing->method('getPageId')->willReturn(10);

        $language = self::createStub(SiteLanguage::class);

        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturnMap([
            ['routing', null, $routing],
            ['language', null, $language],
        ]);
        $request->method('getUri')->willReturn('https://example.com');
        $request->method('getHeader')->willReturn(['']);
        $request->method('getQueryParams')->willReturn([
            'category' => 10,
        ]);

        $subject = $this->get(Factory::class);

        $result = $subject->fromRequest($request, $rule);
        self::assertInstanceOf(DateTimeImmutable::class, $result->getCrdate());
    }

    #[Test]
    public function returnedRecordviewContainsLanguage(): void
    {
        $rule = self::createStub(RecordRule::class);
        $rule->method('getUidExpression')->willReturn('request.getQueryParams()["category"]');
        $rule->method('getTableName')->willReturn('sys_category');

        $routing = self::createStub(PageArguments::class);
        $routing->method('getPageId')->willReturn(10);

        $language = self::createStub(SiteLanguage::class);

        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturnMap([
            ['routing', null, $routing],
            ['language', null, $language],
        ]);
        $request->method('getUri')->willReturn('https://example.com');
        $request->method('getHeader')->willReturn(['']);
        $request->method('getQueryParams')->willReturn([
            'category' => 10,
        ]);

        $subject = $this->get(Factory::class);

        $result = $subject->fromRequest($request, $rule);
        self::assertSame($language, $result->getLanguage());
    }

    #[Test]
    public function returnedRecordviewContainsPageId(): void
    {
        $rule = self::createStub(RecordRule::class);
        $rule->method('getUidExpression')->willReturn('request.getQueryParams()["category"]');
        $rule->method('getTableName')->willReturn('sys_category');

        $routing = self::createStub(PageArguments::class);
        $routing->method('getPageId')->willReturn(10);

        $language = self::createStub(SiteLanguage::class);

        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturnMap([
            ['routing', null, $routing],
            ['language', null, $language],
        ]);
        $request->method('getUri')->willReturn('https://example.com');
        $request->method('getHeader')->willReturn(['']);
        $request->method('getQueryParams')->willReturn([
            'category' => 10,
        ]);

        $subject = $this->get(Factory::class);

        $result = $subject->fromRequest($request, $rule);
        self::assertSame(10, $result->getPageUid());
    }

    #[Test]
    public function returnedRecordviewContainsRecordUid(): void
    {
        $rule = self::createStub(RecordRule::class);
        $rule->method('getUidExpression')->willReturn('request.getQueryParams()["category"]');
        $rule->method('getTableName')->willReturn('sys_category');

        $routing = self::createStub(PageArguments::class);
        $routing->method('getPageId')->willReturn(10);

        $language = self::createStub(SiteLanguage::class);

        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturnMap([
            ['routing', null, $routing],
            ['language', null, $language],
        ]);
        $request->method('getUri')->willReturn('https://example.com');
        $request->method('getHeader')->willReturn(['']);
        $request->method('getQueryParams')->willReturn([
            'category' => 20,
        ]);

        $subject = $this->get(Factory::class);

        $result = $subject->fromRequest($request, $rule);
        self::assertSame(20, $result->getRecordUid());
    }

    #[Test]
    public function returnedRecordviewContainsTableName(): void
    {
        $rule = self::createStub(RecordRule::class);
        $rule->method('getUidExpression')->willReturn('request.getQueryParams()["category"]');
        $rule->method('getTableName')->willReturn('sys_category');

        $routing = self::createStub(PageArguments::class);
        $routing->method('getPageId')->willReturn(10);

        $language = self::createStub(SiteLanguage::class);

        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturnMap([
            ['routing', null, $routing],
            ['language', null, $language],
        ]);
        $request->method('getUri')->willReturn('https://example.com');
        $request->method('getHeader')->willReturn(['']);
        $request->method('getQueryParams')->willReturn([
            'category' => 20,
        ]);

        $subject = $this->get(Factory::class);

        $result = $subject->fromRequest($request, $rule);
        self::assertSame('sys_category', $result->getTableName());
    }
}
