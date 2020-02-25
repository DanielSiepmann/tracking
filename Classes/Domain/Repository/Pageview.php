<?php

namespace DanielSiepmann\Tracking\Domain\Repository;

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

use DanielSiepmann\Tracking\Domain\Model\Pageview as Model;
use TYPO3\CMS\Core\Database\Connection;

class Pageview
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function add(Model $pageview)
    {
        $this->connection->insert(
            'tx_tracking_pageview',
            [
                'pid' => $pageview->getPageUid(),
                'crdate' => $pageview->getCrdate()->format('U'),
                'tstamp' => $pageview->getCrdate()->format('U'),
                'type' => $pageview->getPageType(),
                'sys_language_uid' => $pageview->getLanguage()->getLanguageId(),
                'url' => $pageview->getUrl(),
                'user_agent' => $pageview->getUserAgent(),
            ]
        );
    }
}
