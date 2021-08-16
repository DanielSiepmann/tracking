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
use Doctrine\DBAL\Statement;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\EndTimeRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\StartTimeRestriction;
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
     * @var Demand
     */
    private $demand;

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
        Demand $demand,
        array $recordTableLimitation = [],
        array $recordTypeLimitation = []
    ) {
        $this->pageRepository = $pageRepository;
        $this->queryBuilder = $queryBuilder;
        $this->demand = $demand;
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

    private function getRecordviewsRecords(): \Generator
    {
        $constraints = [
            $this->queryBuilder->expr()->gte(
                'tx_tracking_recordview.crdate',
                strtotime('-' . $this->demand->getDays() . ' day 0:00:00')
            )
        ];

        if (count($this->recordTableLimitation)) {
            $constraints[] = $this->queryBuilder->expr()->in(
                'tx_tracking_recordview.record_table_name',
                $this->queryBuilder->createNamedParameter(
                    $this->recordTableLimitation,
                    Connection::PARAM_STR_ARRAY
                )
            );
        }

        $constraints = array_merge($constraints, $this->demand->getConstraints(
            $this->queryBuilder,
            'tx_tracking_recordview'
        ));
        $this->demand->addJoins(
            $this->queryBuilder,
            'tx_tracking_recordview'
        );

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
            ->setMaxResults($this->demand->getMaxResults())
            ->execute();

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
        if (count($this->demand->getLanguageLimitation()) === 1 && $record !== null) {
            $record = $this->pageRepository->getRecordOverlay($table, $record, $this->demand->getLanguageLimitation()[0]);
        }

        if (is_array($record) === false) {
            return null;
        }

        return [
            'title' => strip_tags(BackendUtility::getRecordTitle($table, $record, true)),
            'type' => $record[$recordTypeField] ?? '',
        ];
    }
}
