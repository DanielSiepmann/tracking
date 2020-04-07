<?php

namespace DanielSiepmann\Tracking\Tests\Unit\Dashboard\Provider;

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

use DanielSiepmann\Tracking\Dashboard\Provider\NewestPageviews;
use Doctrine\DBAL\Driver\Statement;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * @covers DanielSiepmann\Tracking\Dashboard\Provider\NewestPageviews
 */
class NewestPageviewsTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function defaultsToMaxResultsOf6(): void
    {
        $statement = $this->prophesize(Statement::class);
        $statement->fetchAll()->willReturn([]);

        $queryBuilder = $this->prophesize(QueryBuilder::class);
        $queryBuilder->select(Argument::cetera())->willReturn($queryBuilder->reveal());
        $queryBuilder->from(Argument::cetera())->willReturn($queryBuilder->reveal());
        $queryBuilder->orderBy(Argument::cetera())->willReturn($queryBuilder->reveal());
        $queryBuilder->setMaxResults(6)->willReturn($queryBuilder->reveal())->shouldBeCalled();
        $queryBuilder->execute()->willReturn($statement->reveal());

        $subject = new NewestPageviews($queryBuilder->reveal());
        $subject->getItems();
    }

    /**
     * @test
     */
    public function respectsMaxResults(): void
    {
        $statement = $this->prophesize(Statement::class);
        $statement->fetchAll()->willReturn([]);

        $queryBuilder = $this->prophesize(QueryBuilder::class);
        $queryBuilder->select(Argument::cetera())->willReturn($queryBuilder->reveal());
        $queryBuilder->from(Argument::cetera())->willReturn($queryBuilder->reveal());
        $queryBuilder->orderBy(Argument::cetera())->willReturn($queryBuilder->reveal());
        $queryBuilder->setMaxResults(1)->willReturn($queryBuilder->reveal())->shouldBeCalled();
        $queryBuilder->execute()->willReturn($statement->reveal());

        $subject = new NewestPageviews($queryBuilder->reveal(), 1);
        $subject->getItems();
    }

    /**
     * @test
     */
    public function defaultsToNoBlackListedPages(): void
    {
        $statement = $this->prophesize(Statement::class);
        $statement->fetchAll()->willReturn([]);

        $queryBuilder = $this->prophesize(QueryBuilder::class);
        $queryBuilder->select(Argument::cetera())->willReturn($queryBuilder->reveal());
        $queryBuilder->from(Argument::cetera())->willReturn($queryBuilder->reveal());
        $queryBuilder->orderBy(Argument::cetera())->willReturn($queryBuilder->reveal());
        $queryBuilder->setMaxResults(Argument::cetera())->willReturn($queryBuilder->reveal());
        $queryBuilder->execute()->willReturn($statement->reveal());

        $queryBuilder->where()->shouldNotBeCalled();

        $subject = new NewestPageviews($queryBuilder->reveal());
        $subject->getItems();
    }

    /**
     * @test
     */
    public function respectsBlackListedPages(): void
    {
        $expressionBuilder = $this->prophesize(ExpressionBuilder::class);
        $queryBuilder = $this->prophesize(QueryBuilder::class);
        $statement = $this->prophesize(Statement::class);

        $statement->fetchAll()->willReturn([]);

        $expressionBuilder->notIn(
            'tx_tracking_pageview.pid',
            '10, 11'
        )->willReturn('tx_tracking_pageview.pid NOT IN (10, 11)')->shouldBeCalled();
        $queryBuilder->createNamedParameter(
            [10, 11],
            Connection::PARAM_INT_ARRAY
        )->willReturn('10, 11')->shouldBeCalled();
        $queryBuilder->where(
            'tx_tracking_pageview.pid NOT IN (10, 11)'
        )->willReturn($queryBuilder->reveal())->shouldBeCalled();

        $queryBuilder->expr()->willReturn($expressionBuilder->reveal());
        $queryBuilder->select(Argument::cetera())->willReturn($queryBuilder->reveal());
        $queryBuilder->from(Argument::cetera())->willReturn($queryBuilder->reveal());
        $queryBuilder->orderBy(Argument::cetera())->willReturn($queryBuilder->reveal());
        $queryBuilder->setMaxResults(Argument::cetera())->willReturn($queryBuilder->reveal());
        $queryBuilder->execute()->willReturn($statement->reveal());

        $subject = new NewestPageviews($queryBuilder->reveal(), 6, [10, 11]);
        $subject->getItems();
    }

    /**
     * @test
     */
    public function runsQueryWithExpectedValues(): void
    {
        $statement = $this->prophesize(Statement::class);
        $statement->fetchAll()->willReturn([]);

        $queryBuilder = $this->prophesize(QueryBuilder::class);
        $queryBuilder->select('url', 'user_agent')->willReturn($queryBuilder->reveal())->shouldBeCalled();
        $queryBuilder->from('tx_tracking_pageview')->willReturn($queryBuilder->reveal())->shouldBeCalled();
        $queryBuilder->orderBy('crdate', 'desc')->willReturn($queryBuilder->reveal())->shouldBeCalled();
        $queryBuilder->setMaxResults(Argument::cetera())->willReturn($queryBuilder->reveal());
        $queryBuilder->execute()->willReturn($statement->reveal());

        $subject = new NewestPageviews($queryBuilder->reveal());
        $subject->getItems();
    }

    /**
     * @test
     */
    public function returnsSimpleList(): void
    {
        $statement = $this->prophesize(Statement::class);
        $statement->fetchAll()->willReturn([
            [
                'url' => 'https://example.com/path/file.html',
                'user_agent' => 'Mozilla/5.0 (user agent)',
            ],
            [
                'url' => 'https://example.com/path/file2.html',
                'user_agent' => 'Mozilla/5.0 (another user agent)',
            ],
        ]);

        $queryBuilder = $this->prophesize(QueryBuilder::class);
        $queryBuilder->select(Argument::cetera())->willReturn($queryBuilder->reveal());
        $queryBuilder->from(Argument::cetera())->willReturn($queryBuilder->reveal());
        $queryBuilder->orderBy(Argument::cetera())->willReturn($queryBuilder->reveal());
        $queryBuilder->setMaxResults(Argument::cetera())->willReturn($queryBuilder->reveal());
        $queryBuilder->execute()->willReturn($statement->reveal());

        $subject = new NewestPageviews($queryBuilder->reveal());
        static::assertSame(
            [
                'https://example.com/path/file.html - Mozilla/5.0 (user agent)',
                'https://example.com/path/file2.html - Mozilla/5.0 (another user agent)',
            ],
            $subject->getItems()
        );
    }
}
