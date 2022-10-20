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

namespace DanielSiepmann\Tracking\Dashboard\Provider;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;

class NewestPageviews implements ListDataProviderInterface
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var int
     */
    private $maxResults;

    /**
     * @var array
     */
    private $pagesToExclude;

    /**
     * @var array<int>
     */
    private $languageLimitation;

    public function __construct(
        QueryBuilder $queryBuilder,
        int $maxResults = 6,
        array $pagesToExclude = [],
        array $languageLimitation = []
    ) {
        $this->queryBuilder = $queryBuilder;
        $this->maxResults = $maxResults;
        $this->pagesToExclude = $pagesToExclude;
        $this->languageLimitation = $languageLimitation;
    }

    public function getItems(): array
    {
        $preparedItems = [];

        $constraints = [];
        if (count($this->pagesToExclude)) {
            $constraints[] = $this->queryBuilder->expr()->notIn(
                'tx_tracking_pageview.pid',
                $this->queryBuilder->createNamedParameter(
                    $this->pagesToExclude,
                    Connection::PARAM_INT_ARRAY
                )
            );
        }

        if (count($this->languageLimitation)) {
            $constraints[] = $this->queryBuilder->expr()->in(
                'tx_tracking_pageview.sys_language_uid',
                $this->queryBuilder->createNamedParameter(
                    $this->languageLimitation,
                    Connection::PARAM_INT_ARRAY
                )
            );
        }

        $this->queryBuilder
            ->select('url', 'user_agent')
            ->from('tx_tracking_pageview')
            ->orderBy('crdate', 'desc')
            ->addOrderBy('uid', 'desc')
            ->setMaxResults($this->maxResults)
        ;

        if ($constraints !== []) {
            $this->queryBuilder->where(...$constraints);
        }

        $items = $this->queryBuilder->execute()->fetchAll();
        foreach ($items as $item) {
            if (is_array($item) === false) {
                continue;
            }

            $preparedItems[] = sprintf(
                '%s - %s',
                $item['url'],
                $item['user_agent']
            );
        }

        return $preparedItems;
    }
}
