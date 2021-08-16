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

use DanielSiepmann\Tracking\Domain\Model\Pageview as Model;
use DanielSiepmann\Tracking\Domain\Pageview\Factory;
use DanielSiepmann\Tracking\Extension;
use TYPO3\CMS\Core\Database\Connection;

class Pageview
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
        $queryBuilder->from('tx_tracking_pageview');
        $queryBuilder->where($queryBuilder->expr()->neq('compatible_version', $queryBuilder->createNamedParameter(Extension::getCompatibleVersionNow())));
        $queryBuilder->setMaxResults(Extension::getMaximumRowsForUpdate());

        $pageViews = $queryBuilder->execute()->fetchColumn();
        if (is_numeric($pageViews) === false) {
            return 0;
        }

        if ($pageViews > Extension::getMaximumRowsForUpdate()) {
            return Extension::getMaximumRowsForUpdate();
        }
        return (int) $pageViews;
    }

    public function findLegacy(): \Generator
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select('*');
        $queryBuilder->from('tx_tracking_pageview');
        $queryBuilder->where($queryBuilder->expr()->neq('compatible_version', $queryBuilder->createNamedParameter(Extension::getCompatibleVersionNow())));
        $test = Extension::getCompatibleVersionNow();
        $queryBuilder->setMaxResults(Extension::getMaximumRowsForUpdate());

        $pageViews = $queryBuilder->execute();

        while ($pageView = $pageViews->fetch()) {
            if (is_array($pageView) === false) {
                continue;
            }

            yield $this->factory->fromDbRow($pageView);
        }
    }

    public function update(Model $pageview): void
    {
        if ($pageview->getUid() === 0) {
            throw new \InvalidArgumentException('Can not update pageview if uid is 0.', 1585770573);
        }

        $this->connection->update(
            'tx_tracking_pageview',
            $this->getFieldsFromModel($pageview),
            ['uid' => $pageview->getUid()]
        );

        $this->tagRepository->updateForPageview($pageview);
    }

    public function add(Model $pageview): void
    {
        $this->connection->insert(
            'tx_tracking_pageview',
            $this->getFieldsFromModel($pageview)
        );

        $this->tagRepository->addForPageview(
            $pageview,
            (int) $this->connection->lastInsertId('tx_tracking_pageview')
        );
    }

    private function getFieldsFromModel(Model $pageview): array
    {
        return [
            'pid' => $pageview->getPageUid(),
            'crdate' => $pageview->getCrdate()->format('U'),
            'tstamp' => $pageview->getCrdate()->format('U'),
            'type' => $pageview->getPageType(),
            'sys_language_uid' => $pageview->getLanguage()->getLanguageId(),
            'url' => $pageview->getUrl(),
            'user_agent' => $pageview->getUserAgent(),
            'compatible_version' => Extension::getCompatibleVersionNow(),
        ];
    }
}
