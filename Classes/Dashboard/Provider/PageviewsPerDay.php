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

    /**
     * @var array<int>
     */
    private $languageLimitation;

    public function __construct(
        LanguageService $languageService,
        QueryBuilder $queryBuilder,
        int $days = 31,
        array $pagesToExclude = [],
        array $languageLimitation = [],
        string $dateFormat = 'Y-m-d'
    ) {
        $this->languageService = $languageService;
        $this->queryBuilder = $queryBuilder;
        $this->days = $days;
        $this->pagesToExclude = $pagesToExclude;
        $this->languageLimitation = $languageLimitation;
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
            $label = date($this->dateFormat, (int) strtotime('-' . $daysBefore . ' day'));
            $labels[$label] = $label;
            $data[$label] = 0;
        }

        $start = (int) strtotime('-' . $this->days . ' day 0:00:00');
        $end = (int) strtotime('tomorrow midnight');


        foreach ($this->getPageviewsInPeriod($start, $end) as $day) {
            $data[$day['label']] = (int) $day['count'];
        }

        return [
            array_values($labels),
            array_values($data),
        ];
    }

    private function getPageviewsInPeriod(int $start, int $end): array
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
            ->addSelectLiteral('COUNT(*) as "count"')
            ->from('tx_tracking_pageview')
            ->where(...$constraints)
            ->groupBy('label')
            ->orderBy('label', 'ASC')
            ;

        if ($this->queryBuilder->getConnection()->getDatabasePlatform()->getName() === 'sqlite') {
            $this->queryBuilder->addSelectLiteral('date(crdate, "unixepoch") as "label"');
        } else {
            $this->queryBuilder->addSelectLiteral('FROM_UNIXTIME(crdate, "%Y-%m-%d") as "label"');
        }

        return $this->queryBuilder->execute()->fetchAll();
    }
}
