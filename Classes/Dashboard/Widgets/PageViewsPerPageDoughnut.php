<?php

namespace DanielSiepmann\Tracking\Dashboard\Widgets;

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

use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\Widgets\AbstractDoughnutChartWidget;

class PageViewsPerPageDoughnut extends AbstractDoughnutChartWidget
{
    protected $title = 'LLL:EXT:tracking/Resources/Private/Language/locallang.xlf:dashboard.widgets.pageViewsPerPageDoughnut.title';

    protected $description = 'LLL:EXT:tracking/Resources/Private/Language/locallang.xlf:dashboard.widgets.pageViewsPerPageDoughnut.description';

    protected function prepareChartData(): void
    {
        list($labels, $data) = $this->getPageViewsPerPage(31);

        $this->chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'backgroundColor' => $this->chartColors,
                    'data' => $data,
                ]
            ],
        ];
    }

    private function getPageViewsPerPage(int $period): array
    {
        $labels = [];
        $data = [];
        $queryBuilder = $this->getQueryBuilder();

        $result = $queryBuilder
            ->selectLiteral('count(tx_tracking_pageview.pid) as total')
            ->addSelect('pages.title', 'pages.uid')
            ->from('tx_tracking_pageview')
            ->leftJoin(
                'tx_tracking_pageview',
                'pages',
                'pages',
                $queryBuilder->expr()->eq('tx_tracking_pageview.pid', $queryBuilder->quoteIdentifier('pages.uid'))
            )
            ->where(
                $queryBuilder->expr()->notIn(
                    'tx_tracking_pageview.pid',
                    $queryBuilder->createNamedParameter([
                        1,
                        11,
                        38,
                    ], Connection::PARAM_INT_ARRAY)
                )
            )
            ->groupBy('tx_tracking_pageview.pid')
            ->orderBy('total', 'desc')
            ->setMaxResults(6) // Because 6 colors are defined
            ->execute()
            ->fetchAll();

        foreach ($result as $row) {
            $labels[] = $row['title'] . ' [' . $row['uid'] . ']';
            $data[] = $row['total'];
        }

        return [
            $labels,
            $data,
        ];
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_tracking_pageview');
    }
}
