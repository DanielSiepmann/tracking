<?php

namespace DanielSiepmann\Tracking\Tests\Unit\Middleware;

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

use DanielSiepmann\Tracking\Domain\Model\Pageview as Model;
use DanielSiepmann\Tracking\Domain\Repository\Pageview as Repository;
use DanielSiepmann\Tracking\Middleware\Pageview;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase as TestCase;

/**
 * @covers DanielSiepmann\Tracking\Middleware\Pageview
 */
class PageviewTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function doesNotAddRequestIfRuleDoesNotApply(): void
    {
        $repository = $this->prophesize(Repository::class);
        $context = $this->prophesize(Context::class);
        $rule = 'false';

        $request = $this->prophesize(ServerRequestInterface::class);
        $response = $this->prophesize(ResponseInterface::class);
        $handler = $this->prophesize(RequestHandlerInterface::class);

        $handler->handle($request->reveal())->willReturn($response->reveal());
        $repository->add()->shouldNotBeCalled();

        $subject = new Pageview($repository->reveal(), $context->reveal(), $rule);
        $result = $subject->process($request->reveal(), $handler->reveal());

        static::assertInstanceOf(ResponseInterface::class, $result);
    }

    /**
     * @test
     */
    public function addsPageviewToRepository(): void
    {
        $repository = $this->prophesize(Repository::class);
        $context = $this->prophesize(Context::class);
        $rule = 'true';

        $routing = $this->prophesize(PageArguments::class);
        $routing->getPageId()->willReturn(10);
        $routing->getPageType()->willReturn(0);

        $language = $this->prophesize(SiteLanguage::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('routing')->willReturn($routing->reveal());
        $request->getAttribute('language')->willReturn($language->reveal());
        $request->getUri()->willReturn('');
        $request->getHeader('User-Agent')->willReturn([]);

        $response = $this->prophesize(ResponseInterface::class);
        $handler = $this->prophesize(RequestHandlerInterface::class);

        $handler->handle($request->reveal())->willReturn($response->reveal());
        $repository->add(Argument::type(Model::class))->shouldBeCalledtimes(1);

        $subject = new Pageview($repository->reveal(), $context->reveal(), $rule);
        $result = $subject->process($request->reveal(), $handler->reveal());

        static::assertInstanceOf(ResponseInterface::class, $result);
    }
}
