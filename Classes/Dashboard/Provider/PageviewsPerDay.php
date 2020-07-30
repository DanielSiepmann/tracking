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
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

class PageviewsPerDay implements ChartDataProviderInterface
{
    /**
     * @var LanguageService
     */
    private $languageService;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var int
     */
    private $days;

    /**
     * @var array<int>
     */
    private $pagesToExclude;

    /**
     * @var string
     */
    private $dateFormat;

    public function __construct(
        LanguageService $languageService,
        QueryBuilder $queryBuilder,
        int $days = 31,
        array $pagesToExclude = [],
        string $dateFormat = 'Y-m-d'
    ) {
        $this->languageService = $languageService;
        $this->queryBuilder = $queryBuilder;
        $this->days = $days;
        $this->pagesToExclude = $pagesToExclude;
        $this->dateFormat = $dateFormat;
    }

    public function getChartData(): array
    {
        list($labels, $data) = $this->calculateData();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $this->languageService->sL(
                        Extension::LANGUAGE_PATH . 'widgets.pageViewsBar.chart.dataSet.0'
                    ),
                    'backgroundColor' => WidgetApi::getDefaultChartColors()[0],
                    'border' => 0,
                    'data' => $data
                ]
            ]
        ];
    }

    private function calculateData(): array
    {
        $labels = [];
        $data = [];

        for ($daysBefore = $this->days; $daysBefore >= 0; $daysBefore--) {
            $timeForLabel = strtotime('-' . $daysBefore . ' day');
            $startPeriod = strtotime('-' . $daysBefore . ' day 0:00:00');
            $endPeriod =  strtotime('-' . $daysBefore . ' day 23:59:59');

            $labels[] = date($this->dateFormat, $timeForLabel);
            $data[] = $this->getPageviewsInPeriod($startPeriod, $endPeriod);
        }

        return [
            $labels,
            $data,
        ];
    }

    private function getPageviewsInPeriod(int $start, int $end): int
    {
        $constraints = [
            $this->queryBuilder->expr()->gte('crdate', $start),
            $this->queryBuilder->expr()->lte('crdate', $end),
        ];

        if (count($this->pagesToExclude)) {
            $constraints[] = $this->queryBuilder->expr()->notIn(
                'tx_tracking_pageview.pid',
                $this->queryBuilder->createNamedParameter(
                    $this->pagesToExclude,
                    Connection::PARAM_INT_ARRAY
                )
            );
        }

        return (int)$this->queryBuilder
            ->count('*')
            ->from('tx_tracking_pageview')
            ->where(... $constraints)
            ->execute()
            ->fetchColumn();
    }
}
