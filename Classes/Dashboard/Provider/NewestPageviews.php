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
     * @var Demand
     */
    private $demand;

    public function __construct(
        QueryBuilder $queryBuilder,
        Demand $demand
    ) {
        $this->queryBuilder = $queryBuilder;
        $this->demand = $demand;
    }

    public function getItems(): array
    {
        $preparedItems = [];

        $constraints = $this->demand->getConstraints(
            $this->queryBuilder,
            'tx_tracking_pageview'
        );

        $this->demand->addJoins($this->queryBuilder, 'tx_tracking_pageview');

        $this->queryBuilder
            ->select('url', 'user_agent')
            ->from('tx_tracking_pageview')
            ->orderBy('crdate', 'desc')
            ->addOrderBy('uid', 'desc')
            ->setMaxResults($this->demand->getMaxResults());

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
