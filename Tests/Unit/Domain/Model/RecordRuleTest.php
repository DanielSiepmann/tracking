<?php

declare(strict_types=1);

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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(RecordRule::class)]
class RecordRuleTest extends UnitTestCase
{
    #[Test]
    public function canBeCreatedViaConstructor(): void
    {
        $subject = new RecordRule(
            '',
            '',
            ''
        );

        self::assertInstanceOf(RecordRule::class, $subject);
    }

    #[Test]
    public function canBeCreatedFromArray(): void
    {
        $subject = RecordRule::fromArray([
            'identifier' => '',
            'matches' => '',
            'recordUid' => '',
            'tableName' => '',
        ]);

        self::assertInstanceOf(RecordRule::class, $subject);
    }

    #[Test]
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

        self::assertCount(2, $result);
        self::assertInstanceOf(RecordRule::class, $result[0]);
        self::assertInstanceOf(RecordRule::class, $result[1]);
    }

    #[Test]
    public function returnsMatchExpression(): void
    {
        $subject = new RecordRule(
            'match expression',
            '',
            ''
        );

        self::assertSame('match expression', $subject->getMatchesExpression());
    }

    #[Test]
    public function returnsUidExpression(): void
    {
        $subject = new RecordRule(
            '',
            'match expression',
            ''
        );

        self::assertSame('match expression', $subject->getUidExpression());
    }

    #[Test]
    public function returnsTableName(): void
    {
        $subject = new RecordRule(
            '',
            '',
            'table_name'
        );

        self::assertSame('table_name', $subject->getTableName());
    }
}
