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
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

class PageviewsPerOperatingSystem implements ChartDataProviderInterface
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

    public function getChartData(): array
    {
        list($labels, $data) = $this->getPageViewsPerPage();

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

    private function getPageViewsPerPage(): array
    {
        $labels = [];
        $data = [];

        $constraints = [
            $this->queryBuilder->expr()->gte(
                'tx_tracking_pageview.crdate',
                strtotime('-' . $this->demand->getDays() . ' day 0:00:00')
            ),
            $this->queryBuilder->expr()->neq(
                'operating_system',
                $this->queryBuilder->createNamedParameter('')
            ),
        ];

        $constraints = array_merge($constraints, $this->demand->getConstraints(
            $this->queryBuilder,
            'tx_tracking_pageview'
        ));

        $this->demand->addJoins($this->queryBuilder, 'tx_tracking_pageview');

        $result = $this->queryBuilder
            ->addSelect('tag.value as operating_system')
            ->addSelectLiteral(
                'count(' . $this->queryBuilder->quoteIdentifier('operating_system') . ') as total'
            )
            ->from('tx_tracking_pageview')
            ->leftJoin(
                'tx_tracking_pageview',
                'tx_tracking_tag',
                'tag',
                (string) $this->queryBuilder->expr()->andX(
                    $this->queryBuilder->expr()->eq('tx_tracking_pageview.uid', $this->queryBuilder->quoteIdentifier('tag.record_uid')),
                    $this->queryBuilder->expr()->eq('tag.name', $this->queryBuilder->createNamedParameter('os')),
                    $this->queryBuilder->expr()->eq('tag.record_table_name', $this->queryBuilder->createNamedParameter('tx_tracking_pageview'))
                )
            )
            ->where(...$constraints)
            ->groupBy('operating_system')
            ->orderBy('total', 'desc')
            ->addOrderBy('operating_system', 'asc')
            ->setMaxResults($this->demand->getMaxResults())
            ->execute()
            ->fetchAll();

        foreach ($result as $row) {
            if (is_array($row) === false) {
                continue;
            }

            $labels[] = mb_strimwidth($row['operating_system'], 0, 50, '…');
            $data[] = $row['total'];
        }

        return [
            $labels,
            $data,
        ];
    }
}
