<?php

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

use DanielSiepmann\Tracking\Domain\Model\RecordRule;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase as TestCase;

/**
 * @covers DanielSiepmann\Tracking\Domain\Model\RecordRule
 */
class RecordRuleTest extends TestCase
{
    /**
     * @test
     */
    public function canBeCreatedViaConstructor(): void
    {
        $subject = new RecordRule(
            '',
            '',
            '',
            ''
        );

        static::assertInstanceOf(RecordRule::class, $subject);
    }

    /**
     * @test
     */
    public function canBeCreatedFromArray(): void
    {
        $subject = RecordRule::fromArray([
            'identifier' => '',
            'matches' => '',
            'recordUid' => '',
            'tableName' => '',
        ]);

        static::assertInstanceOf(RecordRule::class, $subject);
    }

    /**
     * @test
     */
    public function multipleCanBeCratedFromArray(): void
    {
        $result = RecordRule::multipleFromArray([
            'identifier1' => [
                'matches' => '',
                'recordUid' => '',
                'tableName' => '',
            ],
            'identifier2' => [
                'matches' => '',
                'recordUid' => '',
                'tableName' => '',
            ],
        ]);

        static::assertCount(2, $result);
        static::assertInstanceOf(RecordRule::class, $result[0]);
        static::assertInstanceOf(RecordRule::class, $result[1]);
    }

    /**
     * @test
     */
    public function returnsMatchExpression(): void
    {
        $subject = new RecordRule(
            '',
            'match expression',
            '',
            ''
        );

        static::assertSame('match expression', $subject->getMatchesExpression());
    }

    /**
     * @test
     */
    public function returnsUidExpression(): void
    {
        $subject = new RecordRule(
            '',
            '',
            'match expression',
            ''
        );

        static::assertSame('match expression', $subject->getUidExpression());
    }

    /**
     * @test
     */
    public function returnsTableName(): void
    {
        $subject = new RecordRule(
            '',
            '',
            '',
            'table_name'
        );

        static::assertSame('table_name', $subject->getTableName());
    }
}
