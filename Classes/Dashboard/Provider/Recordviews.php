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
use Doctrine\DBAL\Statement;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\EndTimeRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\StartTimeRestriction;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

class Recordviews implements ChartDataProviderInterface
{
    /**
     * @var ConnectionPool
     */
    private $connectionPool;

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
     * @var array
     */
    private $recordTableLimitation;

    /**
     * @var array
     */
    private $recordTypeLimitation;

    public function __construct(
        ConnectionPool $connectionPool,
        QueryBuilder $queryBuilder,
        int $days = 31,
        int $maxResults = 6,
        array $pagesToExclude = [],
        array $recordTableLimitation = [],
        array $recordTypeLimitation = []
    ) {
        $this->connectionPool = $connectionPool;
        $this->queryBuilder = $queryBuilder;
        $this->days = $days;
        $this->pagesToExclude = $pagesToExclude;
        $this->maxResults = $maxResults;
        $this->recordTableLimitation = $recordTableLimitation;
        $this->recordTypeLimitation = $recordTypeLimitation;
    }

    public function getChartData(): array
    {
        list($labels, $data) = $this->getRecordviews();

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

    private function getRecordviews(): array
    {
        $labels = [];
        $data = [];

        foreach ($this->getRecordviewsRecords() as $recordview) {
            $record = $this->getRecord($recordview['record_uid'], $recordview['record_table_name']);

            if (
                $this->recordTypeLimitation !== []
                && in_array($record['type'], $this->recordTypeLimitation) === false
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

    private function getRecordviewsRecords(): \Generator
    {
        $constraints = [
            $this->queryBuilder->expr()->gte(
                'tx_tracking_recordview.crdate',
                strtotime('-' . $this->days . ' day 0:00:00')
            ),
            $this->queryBuilder->expr()->lte(
                'tx_tracking_recordview.crdate',
                time()
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
            ->selectLiteral('count(record) as total')
            ->addSelect('record_uid', 'record_table_name')
            ->from('tx_tracking_recordview')
            ->where(... $constraints)
            ->groupBy('record')
            ->orderBy('total', 'desc')
            ->setMaxResults($this->maxResults)
            ->execute();

        while ($row = $result->fetch()) {
            yield $row;
        }
    }

    private function getRecord(int $recordUid, string $recordTable): array
    {
        $titlefield = $GLOBALS['TCA'][$recordTable]['ctrl']['label'];
        $recordTypeField = $GLOBALS['TCA'][$recordTable]['ctrl']['type'] ?? '';

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($recordTable);

        $queryBuilder->getRestrictions()
            ->removeByType(StartTimeRestriction::class)
            ->removeByType(EndTimeRestriction::class)
            ->removeByType(HiddenRestriction::class)
            ;

        $queryBuilder->select($titlefield)
            ->from($recordTable)
            ->where('uid = ' . $recordUid);

        if ($recordTypeField !== '') {
            $queryBuilder->addSelect($recordTypeField);
        }

        $record = $queryBuilder->execute()->fetch();

        return [
            'title' => $record[$titlefield],
            'type' => $record[$recordTypeField] ?? '',
        ];
    }
}
