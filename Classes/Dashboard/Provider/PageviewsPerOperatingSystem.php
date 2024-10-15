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

use Exception;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

class PageviewsPerOperatingSystem implements ChartDataProviderInterface
{
    /**
     * @param int[] $languageLimitation
     */
    public function __construct(
        private readonly QueryBuilder $queryBuilder,
        private readonly int $days = 31,
        private readonly int $maxResults = 6,
        private readonly array $languageLimitation = []
    ) {
    }

    public function getChartData(): array
    {
        [$labels, $data] = $this->getPageViewsPerPage();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'backgroundColor' => WidgetApi::getDefaultChartColors(),
                    'data' => $data,
                ],
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
                strtotime('-' . $this->days . ' day 0:00:00')
            ),
            $this->queryBuilder->expr()->neq(
                'tx_tracking_pageview.operating_system',
                $this->queryBuilder->createNamedParameter('')
            ),
        ];

        if (count($this->languageLimitation)) {
            $constraints[] = $this->queryBuilder->expr()->in(
                'tx_tracking_pageview.sys_language_uid',
                $this->queryBuilder->createNamedParameter(
                    $this->languageLimitation,
                    Connection::PARAM_INT_ARRAY
                )
            );
        }

        $result = $this->queryBuilder
            ->selectLiteral('count(operating_system) as total')
            ->addSelect('operating_system')
            ->from('tx_tracking_pageview')
            ->where(...$constraints)
            ->groupBy('tx_tracking_pageview.operating_system')
            ->orderBy('total', 'desc')
            ->addOrderBy('operating_system', 'asc')
            ->setMaxResults($this->maxResults)
            ->executeQuery()
            ->fetchAllAssociative()
        ;

        foreach ($result as $row) {
            if (is_string($row['operating_system']) === false) {
                throw new Exception('operating_system of row was not string: ' . var_export($row['operating_system'], true), 1707326866);
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
