<?php

namespace DanielSiepmann\Tracking\Domain\Model;

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

class RecordRule
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $matches;

    /**
     * @var string
     */
    private $recordUid;

    /**
     * @var string
     */
    private $tableName;

    public function __construct(
        string $identifier,
        string $matches,
        string $recordUid,
        string $tableName
    ) {
        $this->identifier = $identifier;
        $this->matches = $matches;
        $this->recordUid = $recordUid;
        $this->tableName = $tableName;
    }

    public static function fromArray(array $config): self
    {
        return new RecordRule(
            $config['identifier'],
            $config['matches'],
            $config['recordUid'],
            $config['tableName']
        );
    }

    public static function multipleFromArray(array $configs): array
    {
        $rules = [];
        foreach ($configs as $identifier => $config) {
            $rules[] = static::fromArray(array_merge(
                [
                    'identifier' => $identifier,
                ],
                $config
            ));
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
