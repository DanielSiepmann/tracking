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

namespace DanielSiepmann\Tracking\Domain\Repository;

use DanielSiepmann\Tracking\Domain\Model\Recordview as Model;
use DanielSiepmann\Tracking\Domain\Recordview\Factory;
use DanielSiepmann\Tracking\Extension;
use TYPO3\CMS\Core\Database\Connection;

// TODO: Move common code to API class.
// Call API Class with table name

class Recordview
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var Tag
     */
    private $tagRepository;

    public function __construct(
        Connection $connection,
        Factory $factory,
        Tag $tagRepository
    ) {
        $this->connection = $connection;
        $this->factory = $factory;
        $this->tagRepository = $tagRepository;
    }

    public function findLegacyCount(): int
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->count('*');
        $queryBuilder->from('tx_tracking_recordview');
        $queryBuilder->where($queryBuilder->expr()->neq('compatible_version', $queryBuilder->createNamedParameter(Extension::getCompatibleVersionNow())));
        $queryBuilder->setMaxResults(Extension::getMaximumRowsForUpdate());

        $recordviews = $queryBuilder->execute()->fetchColumn();
        if (is_numeric($recordviews) === false) {
            return 0;
        }

        if ($recordviews > Extension::getMaximumRowsForUpdate()) {
            return Extension::getMaximumRowsForUpdate();
        }
        return (int) $recordviews;
    }

    public function findLegacy(): \Generator
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select('*');
        $queryBuilder->from('tx_tracking_recordview');
        $queryBuilder->where($queryBuilder->expr()->neq('compatible_version', $queryBuilder->createNamedParameter(Extension::getCompatibleVersionNow())));
        $queryBuilder->setMaxResults(Extension::getMaximumRowsForUpdate());

        $recordviews = $queryBuilder->execute();

        while ($pageView = $recordviews->fetch()) {
            if (is_array($pageView) === false) {
                continue;
            }

            yield $this->factory->fromDbRow($pageView);
        }
    }

    public function update(Model $model): void
    {
        if ($model->getUid() === 0) {
            throw new \InvalidArgumentException('Can not update recordview if uid is 0.', 1585770573);
        }

        $this->connection->update(
            'tx_tracking_recordview',
            $this->getFieldsFromModel($model),
            ['uid' => $model->getUid()]
        );

        $this->tagRepository->updateForRecordview($model);
    }

    public function add(Model $recordview): void
    {
        $this->connection->insert(
            'tx_tracking_recordview',
            $this->getFieldsFromModel($recordview)
        );

        $this->tagRepository->addForRecordview(
            $recordview,
            (int) $this->connection->lastInsertId('tx_tracking_recordview')
        );
    }

    private function getFieldsFromModel(Model $recordview): array
    {
        return [
            'pid' => $recordview->getPageUid(),
            'crdate' => $recordview->getCrdate()->format('U'),
            'tstamp' => $recordview->getCrdate()->format('U'),
            'sys_language_uid' => $recordview->getLanguage()->getLanguageId(),
            'url' => $recordview->getUrl(),
            'user_agent' => $recordview->getUserAgent(),
            'record_uid' => $recordview->getRecordUid(),
            'record_table_name' => $recordview->getTableName(),
            'record' => $recordview->getTableName() . '_' . $recordview->getRecordUid(),
            'compatible_version' => Extension::getCompatibleVersionNow(),
        ];
    }
}
