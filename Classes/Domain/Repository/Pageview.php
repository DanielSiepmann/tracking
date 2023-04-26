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
use Generator;
use InvalidArgumentException;
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

    public function __construct(
        Connection $connection,
        Factory $factory
    ) {
        $this->connection = $connection;
        $this->factory = $factory;
    }

    public function countAll(): int
    {
        $result = $this->connection->createQueryBuilder()
            ->count('uid')
            ->from('tx_tracking_pageview')
            ->execute()
            ->fetchOne()
        ;

        if (is_numeric($result)) {
            return (int)$result;
        }

        return 0;
    }

    public function findAll(): Generator
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $pageViews = $queryBuilder->select('*')->from('tx_tracking_pageview')->execute();

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
            throw new InvalidArgumentException('Can not update pageview if uid is 0.', 1585770573);
        }

        $this->connection->update(
            'tx_tracking_pageview',
            $this->getFieldsFromModel($pageview),
            ['uid' => $pageview->getUid()]
        );
    }

    public function add(Model $pageview): void
    {
        $this->connection->insert(
            'tx_tracking_pageview',
            $this->getFieldsFromModel($pageview)
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
            'operating_system' => $pageview->getOperatingSystem(),
        ];
    }
}
