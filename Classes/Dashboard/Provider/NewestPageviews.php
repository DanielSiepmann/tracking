<?php

namespace DanielSiepmann\Tracking\Dashboard\Provider;

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

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Dashboard\Widgets\Interfaces\ListDataProviderInterface;

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
    private $blackListedPages;

    public function __construct(
        QueryBuilder $queryBuilder,
        int $maxResults = 6,
        array $blackListedPages = []
    ) {
        $this->queryBuilder = $queryBuilder;
        $this->maxResults = $maxResults;
        $this->blackListedPages = $blackListedPages;
    }

    public function getItems(): array
    {
        $preparedItems = [];

        $constraints = [];
        if (count($this->blackListedPages)) {
            $constraints[] = $this->queryBuilder->expr()->notIn(
                'tx_tracking_pageview.pid',
                $this->queryBuilder->createNamedParameter(
                    $this->blackListedPages,
                    Connection::PARAM_INT_ARRAY
                )
            );
        }

        $this->queryBuilder
            ->select('url', 'user_agent')
            ->from('tx_tracking_pageview')
            ->orderBy('crdate', 'desc')
            ->setMaxResults($this->maxResults);

        if ($constraints !== []) {
            $this->queryBuilder->where(... $constraints);
        }

        $items = $this->queryBuilder->execute()->fetchAll();
        foreach ($items as $item) {
            $preparedItems[] = sprintf(
                '%s - %s',
                $item['url'],
                $item['user_agent']
            );
        }

        return $preparedItems;
    }
}
