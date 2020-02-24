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

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Dashboard\Widgets\AbstractBarChartWidget;

class PageViewsBar extends AbstractBarChartWidget
{
    protected $title = 'LLL:EXT:tracking/Resources/Private/Language/locallang.xlf:dashboard.widgets.pageViewsBar.title';

    protected $description = 'LLL:EXT:tracking/Resources/Private/Language/locallang.xlf:dashboard.widgets.pageViewsBar.description';

    protected $width = 2;

    protected $height = 4;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    public function __construct(string $identifier, QueryBuilder $queryBuilder)
    {
        parent::__construct($identifier);
        $this->queryBuilder = $queryBuilder;
        $this->identifier = $identifier;
    }

    protected function prepareChartData(): void
    {
        list($labels, $data) = $this->calculateDataForLastDays(31);

        $this->chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $this->getLanguageService()->sL(
                        'LLL:EXT:tracking/Resources/Private/Language/locallang.xlf:widgets.pageViewsBar.chart.dataSet.0'
                    ),
                    'backgroundColor' => $this->chartColors[0],
                    'border' => 0,
                    'data' => $data
                ]
            ]
        ];
    }

    protected function getPageViewsInPeriod(int $start, int $end): int
    {
        return (int)$this->queryBuilder
            ->count('*')
            ->from('tx_tracking_pageview')
            ->where(
                $this->queryBuilder->expr()->gte('tstamp', $start),
                $this->queryBuilder->expr()->lte('tstamp', $end),
                $this->queryBuilder->expr()->notIn(
                    'tx_tracking_pageview.pid',
                    $this->queryBuilder->createNamedParameter([
                        1,
                        11,
                        38,
                    ], Connection::PARAM_INT_ARRAY)
                )
            )
            ->execute()
            ->fetchColumn();
    }

    protected function calculateDataForLastDays(int $days): array
    {
        $labels = [];
        $data = [];

        $format = $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'] ?: 'Y-m-d';

        for ($daysBefore = $days; $daysBefore >= 0; $daysBefore--) {
            $labels[] = date($format, strtotime('-' . $daysBefore . ' day'));
            $startPeriod = strtotime('-' . $daysBefore . ' day 0:00:00');
            $endPeriod =  strtotime('-' . $daysBefore . ' day 23:59:59');

            $data[] = $this->getPageViewsInPeriod($startPeriod, $endPeriod);
        }

        return [
            $labels,
            $data,
        ];
    }
}
