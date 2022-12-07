<?php

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
use DateTimeImmutable;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @covers \DanielSiepmann\Tracking\Domain\Recordview\Factory
 */
class FactoryTest extends FunctionalTestCase
{
    use ProphecyTrait;

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/tracking',
    ];

    /**
     * @test
     */
    public function returnsRecordviewFromRequest(): void
    {
        $rule = $this->prophesize(RecordRule::class);
        $rule->getUidExpression()->willReturn('request.getQueryParams()["category"]');
        $rule->getTableName()->willReturn('sys_category');

        $routing = $this->prophesize(PageArguments::class);
        $routing->getPageId()->willReturn(10);

        $language = $this->prophesize(SiteLanguage::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('routing')->willReturn($routing->reveal());
        $request->getAttribute('language')->willReturn($language->reveal());
        $request->getUri()->willReturn('');
        $request->getHeader('User-Agent')->willReturn([]);
        $request->getQueryParams()->willReturn([
            'category' => 10,
        ]);

        $subject = $this->get(Factory::class);

        $result = $subject->fromRequest($request->reveal(), $rule->reveal());
        self::assertInstanceOf(Recordview::class, $result);
    }

    /**
     * @test
     */
    public function returnedRecordviewContainsUserAgent(): void
    {
        $rule = $this->prophesize(RecordRule::class);
        $rule->getUidExpression()->willReturn('request.getQueryParams()["category"]');
        $rule->getTableName()->willReturn('sys_category');

        $routing = $this->prophesize(PageArguments::class);
        $routing->getPageId()->willReturn(10);

        $language = $this->prophesize(SiteLanguage::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('routing')->willReturn($routing->reveal());
        $request->getAttribute('language')->willReturn($language->reveal());
        $request->getUri()->willReturn('');
        $request->getHeader('User-Agent')->willReturn(['Some User Agent']);
        $request->getQueryParams()->willReturn([
            'category' => 10,
        ]);

        $subject = $this->get(Factory::class);

        $result = $subject->fromRequest($request->reveal(), $rule->reveal());
        self::assertSame('Some User Agent', $result->getUserAgent());
    }

    /**
     * @test
     */
    public function returnedRecordviewContainsUri(): void
    {
        $rule = $this->prophesize(RecordRule::class);
        $rule->getUidExpression()->willReturn('request.getQueryParams()["category"]');
        $rule->getTableName()->willReturn('sys_category');

        $routing = $this->prophesize(PageArguments::class);
        $routing->getPageId()->willReturn(10);

        $language = $this->prophesize(SiteLanguage::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('routing')->willReturn($routing->reveal());
        $request->getAttribute('language')->willReturn($language->reveal());
        $request->getUri()->willReturn('https://example.com');
        $request->getHeader('User-Agent')->willReturn(['']);
        $request->getQueryParams()->willReturn([
            'category' => 10,
        ]);

        $subject = $this->get(Factory::class);

        $result = $subject->fromRequest($request->reveal(), $rule->reveal());
        self::assertSame('https://example.com', $result->getUrl());
    }

    /**
     * @test
     */
    public function returnedRecordviewContainsDateTime(): void
    {
        $rule = $this->prophesize(RecordRule::class);
        $rule->getUidExpression()->willReturn('request.getQueryParams()["category"]');
        $rule->getTableName()->willReturn('sys_category');

        $routing = $this->prophesize(PageArguments::class);
        $routing->getPageId()->willReturn(10);

        $language = $this->prophesize(SiteLanguage::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('routing')->willReturn($routing->reveal());
        $request->getAttribute('language')->willReturn($language->reveal());
        $request->getUri()->willReturn('https://example.com');
        $request->getHeader('User-Agent')->willReturn(['']);
        $request->getQueryParams()->willReturn([
            'category' => 10,
        ]);

        $subject = $this->get(Factory::class);

        $result = $subject->fromRequest($request->reveal(), $rule->reveal());
        self::assertInstanceOf(DateTimeImmutable::class, $result->getCrdate());
    }

    /**
     * @test
     */
    public function returnedRecordviewContainsLanguage(): void
    {
        $rule = $this->prophesize(RecordRule::class);
        $rule->getUidExpression()->willReturn('request.getQueryParams()["category"]');
        $rule->getTableName()->willReturn('sys_category');

        $routing = $this->prophesize(PageArguments::class);
        $routing->getPageId()->willReturn(10);

        $language = $this->prophesize(SiteLanguage::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('routing')->willReturn($routing->reveal());
        $request->getAttribute('language')->willReturn($language->reveal());
        $request->getUri()->willReturn('https://example.com');
        $request->getHeader('User-Agent')->willReturn(['']);
        $request->getQueryParams()->willReturn([
            'category' => 10,
        ]);

        $subject = $this->get(Factory::class);

        $result = $subject->fromRequest($request->reveal(), $rule->reveal());
        self::assertSame($language->reveal(), $result->getLanguage());
    }

    /**
     * @test
     */
    public function returnedRecordviewContainsPageId(): void
    {
        $rule = $this->prophesize(RecordRule::class);
        $rule->getUidExpression()->willReturn('request.getQueryParams()["category"]');
        $rule->getTableName()->willReturn('sys_category');

        $routing = $this->prophesize(PageArguments::class);
        $routing->getPageId()->willReturn(10);

        $language = $this->prophesize(SiteLanguage::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('routing')->willReturn($routing->reveal());
        $request->getAttribute('language')->willReturn($language->reveal());
        $request->getUri()->willReturn('https://example.com');
        $request->getHeader('User-Agent')->willReturn(['']);
        $request->getQueryParams()->willReturn([
            'category' => 10,
        ]);

        $subject = $this->get(Factory::class);

        $result = $subject->fromRequest($request->reveal(), $rule->reveal());
        self::assertSame(10, $result->getPageUid());
    }

    /**
     * @test
     */
    public function returnedRecordviewContainsRecordUid(): void
    {
        $rule = $this->prophesize(RecordRule::class);
        $rule->getUidExpression()->willReturn('request.getQueryParams()["category"]');
        $rule->getTableName()->willReturn('sys_category');

        $routing = $this->prophesize(PageArguments::class);
        $routing->getPageId()->willReturn(10);

        $language = $this->prophesize(SiteLanguage::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('routing')->willReturn($routing->reveal());
        $request->getAttribute('language')->willReturn($language->reveal());
        $request->getUri()->willReturn('https://example.com');
        $request->getHeader('User-Agent')->willReturn(['']);
        $request->getQueryParams()->willReturn([
            'category' => 20,
        ]);

        $subject = $this->get(Factory::class);

        $result = $subject->fromRequest($request->reveal(), $rule->reveal());
        self::assertSame(20, $result->getRecordUid());
    }

    /**
     * @test
     */
    public function returnedRecordviewContainsTableName(): void
    {
        $rule = $this->prophesize(RecordRule::class);
        $rule->getUidExpression()->willReturn('request.getQueryParams()["category"]');
        $rule->getTableName()->willReturn('sys_category');

        $routing = $this->prophesize(PageArguments::class);
        $routing->getPageId()->willReturn(10);

        $language = $this->prophesize(SiteLanguage::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('routing')->willReturn($routing->reveal());
        $request->getAttribute('language')->willReturn($language->reveal());
        $request->getUri()->willReturn('https://example.com');
        $request->getHeader('User-Agent')->willReturn(['']);
        $request->getQueryParams()->willReturn([
            'category' => 20,
        ]);

        $subject = $this->get(Factory::class);

        $result = $subject->fromRequest($request->reveal(), $rule->reveal());
        self::assertSame('sys_category', $result->getTableName());
    }
}
