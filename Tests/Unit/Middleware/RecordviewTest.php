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

use DanielSiepmann\Tracking\Domain\Model\Recordview as Model;
use DanielSiepmann\Tracking\Domain\Repository\Recordview as Repository;
use DanielSiepmann\Tracking\Middleware\Recordview;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * @covers DanielSiepmann\Tracking\Middleware\Recordview
 */
class RecordviewTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function proceedsWithNextHandlerIfNoRuleIsConfigured(): void
    {
        $repository = $this->prophesize(Repository::class);
        $repository->add()->shouldNotBeCalled();
        $context = $this->prophesize(Context::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $response = $this->prophesize(ResponseInterface::class);
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle($request->reveal())->willReturn($response);

        $subject = new Recordview($repository->reveal(), $context->reveal(), []);
        $result = $subject->process($request->reveal(), $handler->reveal());

        static::assertInstanceOf(ResponseInterface::class, $result);
    }

    /**
     * @test
     */
    public function doesntAddViewIfRuleDoesNotMatchRequest(): void
    {
        $repository = $this->prophesize(Repository::class);
        $repository->add()->shouldNotBeCalled();
        $context = $this->prophesize(Context::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getQueryParams()->willReturn([]);
        $response = $this->prophesize(ResponseInterface::class);
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle($request->reveal())->willReturn($response);

        $subject = new Recordview($repository->reveal(), $context->reveal(), [
            'topic' => [
                'matches' => 'request.getQueryParams()["topic_id"] > 0',
                'recordUid' => '',
                'tableName' => '',
            ],
        ]);
        $result = $subject->process($request->reveal(), $handler->reveal());

        static::assertInstanceOf(ResponseInterface::class, $result);
    }

    /**
     * @test
     */
    public function addsSingleViewIfRuleMatches(): void
    {
        $repository = $this->prophesize(Repository::class);
        $repository->add(Argument::that(function (Model $recordview) {
            return $recordview->getPageUid() === 10
                && $recordview->getRecordUid() === 10
                && $recordview->getTableName() === 'topics'
                ;
        }))->shouldBeCalled();
        $context = $this->prophesize(Context::class);

        $routing = $this->prophesize(PageArguments::class);
        $routing->getPageId()->willReturn(10);

        $language = $this->prophesize(SiteLanguage::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('routing')->willReturn($routing->reveal());
        $request->getAttribute('language')->willReturn($language->reveal());
        $request->getUri()->willReturn('');
        $request->getHeader('User-Agent')->willReturn([]);
        $request->getQueryParams()->willReturn([
            'topic_id' => '10',
        ]);
        $response = $this->prophesize(ResponseInterface::class);
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle($request->reveal())->willReturn($response);

        $subject = new Recordview($repository->reveal(), $context->reveal(), [
            'topic' => [
                'matches' => 'request.getQueryParams()["topic_id"] > 0',
                'recordUid' => 'request.getQueryParams()["topic_id"]',
                'tableName' => 'topics',
            ],
        ]);
        $result = $subject->process($request->reveal(), $handler->reveal());

        static::assertInstanceOf(ResponseInterface::class, $result);
    }

    /**
     * @test
     */
    public function canAddMultipleViewsIfMultipleRulesApply(): void
    {
        $repository = $this->prophesize(Repository::class);
        $repository->add(Argument::that(function (Model $recordview) {
            return $recordview->getPageUid() === 10
                && $recordview->getRecordUid() === 10
                && $recordview->getTableName() === 'topics'
                ;
        }))->shouldBeCalled();
        $repository->add(Argument::that(function (Model $recordview) {
            return $recordview->getPageUid() === 10
                && $recordview->getRecordUid() === 20
                && $recordview->getTableName() === 'news'
                ;
        }))->shouldBeCalled();
        $context = $this->prophesize(Context::class);

        $routing = $this->prophesize(PageArguments::class);
        $routing->getPageId()->willReturn(10);

        $language = $this->prophesize(SiteLanguage::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('routing')->willReturn($routing->reveal());
        $request->getAttribute('language')->willReturn($language->reveal());
        $request->getUri()->willReturn('');
        $request->getHeader('User-Agent')->willReturn([]);
        $request->getQueryParams()->willReturn([
            'topic_id' => '10',
            'news' => '20',
        ]);
        $response = $this->prophesize(ResponseInterface::class);
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle($request->reveal())->willReturn($response);

        $subject = new Recordview($repository->reveal(), $context->reveal(), [
            'topic' => [
                'matches' => 'request.getQueryParams()["topic_id"] > 0',
                'recordUid' => 'request.getQueryParams()["topic_id"]',
                'tableName' => 'topics',
            ],
            'news' => [
                'matches' => 'request.getQueryParams()["news"] > 0',
                'recordUid' => 'request.getQueryParams()["news"]',
                'tableName' => 'news',
            ],
        ]);
        $result = $subject->process($request->reveal(), $handler->reveal());

        static::assertInstanceOf(ResponseInterface::class, $result);
    }
}
