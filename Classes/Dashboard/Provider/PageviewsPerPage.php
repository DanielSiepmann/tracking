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

use DanielSiepmann\Tracking\Extension;
use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

class PageviewsPerPage implements ChartDataProviderInterface
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var PageRepository
     */
    private $pageRepository;

    /**
     * @var Demand
     */
    private $demand;

    public function __construct(
        QueryBuilder $queryBuilder,
        PageRepository $pageRepository,
        Demand $demand
    ) {
        $this->queryBuilder = $queryBuilder;
        $this->pageRepository = $pageRepository;
        $this->demand = $demand;
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
            (string) $this->queryBuilder->expr()->gte(
                'tx_tracking_pageview.crdate',
                strtotime('-' . $this->demand->getDays() . ' day 0:00:00')
            ),
        ];
        $constraints = array_merge($constraints, $this->demand->getConstraints(
            $this->queryBuilder,
            'tx_tracking_pageview'
        ));
        $this->demand->addJoins(
            $this->queryBuilder,
            'tx_tracking_pageview'
        );

        $result = $this->queryBuilder
            ->selectLiteral(
                $this->queryBuilder->expr()->count('tx_tracking_pageview.pid', 'total'),
                $this->queryBuilder->expr()->max('tx_tracking_pageview.uid', 'latest')
            )
            ->addSelect('tx_tracking_pageview.pid')
            ->from('tx_tracking_pageview')
            ->where(...$constraints)
            ->groupBy('tx_tracking_pageview.pid')
            ->orderBy('total', 'desc')
            ->addOrderBy('latest', 'desc')
            ->setMaxResults($this->demand->getMaxResults())
            ->execute()
            ->fetchAll();

        foreach ($result as $row) {
            if (is_array($row) === false) {
                continue;
            }

            $labels[] = $this->getRecordTitle((int) $row['pid']);
            $data[] = $row['total'];
        }

        return [
            $labels,
            $data,
        ];
    }

    private function getRecordTitle(int $uid): string
    {
        $record = BackendUtility::getRecord('pages', $uid);
        if (count($this->demand->getLanguageLimitation()) === 1 && $record !== null) {
            $record = $this->pageRepository->getRecordOverlay('pages', $record, $this->demand->getLanguageLimitation()[0]);
        }

        if (is_array($record) === false) {
            return 'Unkown';
        }

        return strip_tags(BackendUtility::getRecordTitle('pages', $record, true));
    }
}
