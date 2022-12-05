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

use Generator;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

class Recordviews implements ChartDataProviderInterface
{
    /**
     * @var PageRepository
     */
    private $pageRepository;

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
    private $pagesToExclude;

    /**
     * @var array<int>
     */
    private $languageLimitation;

    /**
     * @var array
     */
    private $recordTableLimitation;

    /**
     * @var array
     */
    private $recordTypeLimitation;

    public function __construct(
        PageRepository $pageRepository,
        QueryBuilder $queryBuilder,
        int $days = 31,
        int $maxResults = 6,
        array $pagesToExclude = [],
        array $languageLimitation = [],
        array $recordTableLimitation = [],
        array $recordTypeLimitation = []
    ) {
        $this->pageRepository = $pageRepository;
        $this->queryBuilder = $queryBuilder;
        $this->days = $days;
        $this->pagesToExclude = $pagesToExclude;
        $this->languageLimitation = $languageLimitation;
        $this->maxResults = $maxResults;
        $this->recordTableLimitation = $recordTableLimitation;
        $this->recordTypeLimitation = $recordTypeLimitation;
    }

    public function getChartData(): array
    {
        [$labels, $data] = $this->getRecordviews();

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

    private function getRecordviews(): array
    {
        $labels = [];
        $data = [];

        foreach ($this->getRecordviewsRecords() as $recordview) {
            if (is_numeric($recordview['record_uid']) === false) {
                continue;
            }
            $record = $this->getRecord(
                (int) $recordview['record_uid'],
                $recordview['record_table_name']
            );

            if (
                $record === null
                || (
                    $this->recordTypeLimitation !== []
                    && in_array($record['type'], $this->recordTypeLimitation) === false
                )
            ) {
                continue;
            }

            $labels[] = mb_strimwidth($record['title'], 0, 25, '…');
            $data[] = $recordview['total'];
        }

        return [
            $labels,
            $data,
        ];
    }

    private function getRecordviewsRecords(): Generator
    {
        $constraints = [
            $this->queryBuilder->expr()->gte(
                'tx_tracking_recordview.crdate',
                strtotime('-' . $this->days . ' day 0:00:00')
            ),
        ];

        if (count($this->pagesToExclude)) {
            $constraints[] = $this->queryBuilder->expr()->notIn(
                'tx_tracking_recordview.pid',
                $this->queryBuilder->createNamedParameter(
                    $this->pagesToExclude,
                    Connection::PARAM_INT_ARRAY
                )
            );
        }

        if (count($this->languageLimitation)) {
            $constraints[] = $this->queryBuilder->expr()->in(
                'tx_tracking_recordview.sys_language_uid',
                $this->queryBuilder->createNamedParameter(
                    $this->languageLimitation,
                    Connection::PARAM_INT_ARRAY
                )
            );
        }

        if (count($this->recordTableLimitation)) {
            $constraints[] = $this->queryBuilder->expr()->in(
                'tx_tracking_recordview.record_table_name',
                $this->queryBuilder->createNamedParameter(
                    $this->recordTableLimitation,
                    Connection::PARAM_STR_ARRAY
                )
            );
        }

        $result = $this->queryBuilder
            ->selectLiteral(
                $this->queryBuilder->expr()->count('record', 'total'),
                $this->queryBuilder->expr()->max('uid', 'latest')
            )
            ->addSelect('record_uid', 'record_table_name')
            ->from('tx_tracking_recordview')
            ->where(...$constraints)
            ->groupBy('record', 'record_uid', 'record_table_name')
            ->orderBy('total', 'desc')
            ->addOrderBy('latest', 'desc')
            ->setMaxResults($this->maxResults)
            ->execute()
        ;

        while ($row = $result->fetch()) {
            yield $row;
        }
    }

    private function getRecord(
        int $uid,
        string $table
    ): ?array {
        $recordTypeField = $GLOBALS['TCA'][$table]['ctrl']['type'] ?? '';

        $record = BackendUtility::getRecord($table, $uid);
        if (count($this->languageLimitation) === 1 && $record !== null) {
            $record = $this->pageRepository->getRecordOverlay(
                $table,
                $record,
                $this->createLanguageAspect($this->languageLimitation[0])
            );
        }

        if (is_array($record) === false) {
            return null;
        }

        return [
            'title' => strip_tags(BackendUtility::getRecordTitle($table, $record, true)),
            'type' => $record[$recordTypeField] ?? '',
        ];
    }

    private function createLanguageAspect(int $languageUid): LanguageAspect
    {
        return new LanguageAspect(
            $languageUid,
            null,
            LanguageAspect::OVERLAYS_MIXED
        );
    }
}
