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

use DanielSiepmann\Tracking\Extension;
use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\Interfaces\ChartDataProviderInterface;

class PageviewsPerPage implements ChartDataProviderInterface
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var int
     */
    private $days;

    /**
     * @var int
     */
    private $maxResults;

    /**
     * @var array<int>
     */
    private $blackListedPages;

    public function __construct(
        QueryBuilder $queryBuilder,
        int $days = 31,
        int $maxResults = 6,
        array $blackListedPages = []
    ) {
        $this->queryBuilder = $queryBuilder;
        $this->days = $days;
        $this->blackListedPages = $blackListedPages;
        $this->maxResults = $maxResults;
    }

    public function getChartData(): array
    {
        list($labels, $data) = $this->getPageviewsPerPage();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'backgroundColor' => WidgetApi::getDefaultChartColors(),
                    'data' => $data,
                ]
            ],
        ];
    }

    private function getPageviewsPerPage(): array
    {
        $labels = [];
        $data = [];

        $constraints = [
            $this->queryBuilder->expr()->gte(
                'tx_tracking_pageview.crdate',
                strtotime('-' . $this->days . ' day 0:00:00')
            ),
            $this->queryBuilder->expr()->lte(
                'tx_tracking_pageview.crdate',
                time()
            ),
        ];
        if (count($this->blackListedPages)) {
            $constraints[] = $this->queryBuilder->expr()->notIn(
                'tx_tracking_pageview.pid',
                $this->queryBuilder->createNamedParameter(
                    $this->blackListedPages,
                    Connection::PARAM_INT_ARRAY
                )
            );
        }

        $result = $this->queryBuilder
            ->selectLiteral('count(tx_tracking_pageview.pid) as total')
            ->addSelect('pages.title', 'pages.uid')
            ->from('tx_tracking_pageview')
            ->leftJoin(
                'tx_tracking_pageview',
                'pages',
                'pages',
                $this->queryBuilder->expr()->eq(
                    'tx_tracking_pageview.pid',
                    $this->queryBuilder->quoteIdentifier('pages.uid')
                )
            )
            ->where(... $constraints)
            ->groupBy('tx_tracking_pageview.pid')
            ->orderBy('total', 'desc')
            ->setMaxResults($this->maxResults)
            ->execute()
            ->fetchAll();

        foreach ($result as $row) {
            $labels[] = mb_strimwidth($row['title'], 0, 50, '…') . ' [' . $row['uid'] . ']';
            $data[] = $row['total'];
        }

        return [
            $labels,
            $data,
        ];
    }
}
