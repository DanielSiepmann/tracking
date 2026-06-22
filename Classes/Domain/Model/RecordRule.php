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

namespace DanielSiepmann\Tracking\Domain\Model;

use InvalidArgumentException;

class RecordRule
{
    public function __construct(
        private readonly string $matches,
        private readonly string $recordUid,
        private readonly string $tableName
    ) {
    }

    public static function fromArray(array $config): self
    {
        if (
            is_string($config['matches']) === false
            || is_string($config['recordUid']) === false
            || is_string($config['tableName']) === false
        ) {
            throw new InvalidArgumentException('Expected all three keys to be strings.', 1772793074);
        }

        return new self(
            $config['matches'],
            $config['recordUid'],
            $config['tableName']
        );
    }

    /**
     * @param array[] $configs
     *
     * @return array<RecordRule>
     */
    public static function multipleFromArray(array $configs): array
    {
        $rules = [];
        foreach ($configs as $config) {
            $rules[] = static::fromArray($config);
        }
        return $rules;
    }

    public function getMatchesExpression(): string
    {
        return $this->matches;
    }

    public function getUidExpression(): string
    {
        return $this->recordUid;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }
}
