<?php

declare(strict_types=1);

/*
 * Copyright (C) 2021 Daniel Siepmann <coding@daniel-siepmann.de>
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

namespace DanielSiepmann\Tracking\Dashboard\Provider;

use DanielSiepmann\Tracking\Dashboard\Provider\Demand\Tag;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

class Demand
{
    /**
     * @var int
     */
    private $days;

    /**
     * @var int
     */
    private $maxResults;

    /**
     * @var int[]
     */
    private $pagesToExclude;

    /**
     * @var int[]
     */
    private $languageLimitation;

    /**
     * @var Tag[]
     */
    private $tagConstraints;

    /**
     * @param int[] $pagesToExclude
     * @param int[] $languageLimitation
     * @param Tag[] $tagConstraints
     */
    public function __construct(
        int $days = 31,
        int $maxResults = 6,
        array $pagesToExclude = [],
        array $languageLimitation = [],
        array $tagConstraints = []
    ) {
        $this->days = $days;
        $this->maxResults = $maxResults;
        $this->pagesToExclude = array_map('intval', $pagesToExclude);
        $this->languageLimitation = array_map('intval', $languageLimitation);
        $this->tagConstraints = $tagConstraints;
    }

    public function getDays(): int
    {
        return $this->days;
    }

    public function getMaxResults(): int
    {
        return $this->maxResults;
    }

    /**
     * @return int[]
     */
    public function getPagesToExclude(): array
    {
        return $this->pagesToExclude;
    }

    /**
     * @return int[]
     */
    public function getLanguageLimitation(): array
    {
        return $this->languageLimitation;
    }

    /**
     * @return Tag[]
     */
    public function getTagConstraints(): array
    {
        return $this->tagConstraints;
    }

    public function addJoins(
        QueryBuilder $queryBuilder,
        string $tableName
    ): void {
        if ($this->getTagConstraints() !== []) {
            $queryBuilder->leftJoin(
                $tableName,
                'tx_tracking_tag',
                'tx_tracking_tag',
                (string) $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq(
                        'tx_tracking_tag.record_table_name',
                        $queryBuilder->createNamedParameter($tableName)
                    ),
                    $queryBuilder->expr()->eq(
                        'tx_tracking_tag.record_uid',
                        $queryBuilder->quoteIdentifier($tableName . '.uid')
                    )
                )
            );
        }
    }

    /**
     * @return string[]
     */
    public function getConstraints(
        QueryBuilder $queryBuilder,
        string $tableName
    ): array {
        $constraints = [];

        if ($this->getPagesToExclude() !== []) {
            $constraints[] = (string) $queryBuilder->expr()->notIn(
                $tableName . '.pid',
                $queryBuilder->createNamedParameter(
                    $this->getPagesToExclude(),
                    Connection::PARAM_INT_ARRAY
                )
            );
        }

        if ($this->getLanguageLimitation() !== []) {
            $constraints[] = (string) $queryBuilder->expr()->in(
                $tableName . '.sys_language_uid',
                $queryBuilder->createNamedParameter(
                    $this->getLanguageLimitation(),
                    Connection::PARAM_INT_ARRAY
                )
            );
        }

        foreach ($this->getTagConstraints() as $tagConstraint) {
            $constraints[] = (string) $queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq(
                    'tx_tracking_tag.name',
                    $queryBuilder->createNamedParameter(
                        $tagConstraint->getName()
                    )
                ),
                $queryBuilder->expr()->eq(
                    'tx_tracking_tag.value',
                    $queryBuilder->createNamedParameter(
                        $tagConstraint->getValue()
                    )
                )
            );
        }

        return $constraints;
    }
}
