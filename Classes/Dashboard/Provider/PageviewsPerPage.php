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
    private $pagesToExclude;

    /**
     * @var array<int>
     */
    private $languageLimitation;

    public function __construct(
        QueryBuilder $queryBuilder,
        PageRepository $pageRepository,
        int $days = 31,
        int $maxResults = 6,
        array $pagesToExclude = [],
        array $languageLimitation = []
    ) {
        $this->queryBuilder = $queryBuilder;
        $this->pageRepository = $pageRepository;
        $this->days = $days;
        $this->maxResults = $maxResults;
        $this->pagesToExclude = $pagesToExclude;
        $this->languageLimitation = $languageLimitation;
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

        $result = $this->queryBuilder
            ->selectLiteral('count(tx_tracking_pageview.pid) as total')
            ->addSelect('pages.uid', 'tx_tracking_pageview.sys_language_uid')
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
            ->addOrderBy('tx_tracking_pageview.uid', 'desc')
            ->setMaxResults($this->maxResults)
            ->execute()
            ->fetchAll();

        foreach ($result as $row) {
            $labels[] = $this->getRecordTitle($row['uid'], $row['sys_language_uid']);
            $data[] = $row['total'];
        }

        return [
            $labels,
            $data,
        ];
    }

    private function getRecordTitle(int $uid, int $sysLanguageUid): string
    {
        $record = BackendUtility::getRecord('pages', $uid);
        if ($sysLanguageUid > 0 && count($this->languageLimitation) === 1 && $record !== null) {
            $record = $this->pageRepository->getRecordOverlay('pages', $record, $sysLanguageUid);
        }

        return strip_tags(BackendUtility::getRecordTitle('pages', $record, true));
    }
}
