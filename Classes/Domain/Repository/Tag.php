<?php

declare(strict_types=1);

/*
 * Copyright (C) 2021 Daniel Siepmann <coding@daniel-siepmann.de>
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

use DanielSiepmann\Tracking\Domain\Extractors\Registry;
use DanielSiepmann\Tracking\Domain\Extractors\Tag as Model;
use DanielSiepmann\Tracking\Domain\Model\Pageview;
use DanielSiepmann\Tracking\Domain\Model\Recordview;
use DanielSiepmann\Tracking\Extension;
use TYPO3\CMS\Core\Database\Connection;

class Tag
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Registry
     */
    private $extractorRegistry;

    public function __construct(
        Connection $connection,
        Registry $extractorRegistry
    ) {
        $this->connection = $connection;
        $this->extractorRegistry = $extractorRegistry;
    }

    public function addForPageview(
        Pageview $pageview,
        int $recordUid
    ): void {
        foreach ($this->extractorRegistry->getTagsForPageview($pageview) as $tag) {
            $this->connection->insert(
                'tx_tracking_tag',
                [
                    'pid' => $pageview->getPageUid(),
                    'crdate' => $pageview->getCrdate()->format('U'),
                    'tstamp' => $pageview->getCrdate()->format('U'),
                    'record_uid' => $recordUid,
                    'record_table_name' => 'tx_tracking_pageview',
                    'name' => $tag->getName(),
                    'value' => $tag->getValue(),
                ]
            );
        }
    }

    public function updateForPageview(
        Pageview $pageview
    ): void {
        $this->connection->delete(
            'tx_tracking_tag',
            [
                'record_uid' => $pageview->getUid(),
                'record_table_name' => 'tx_tracking_pageview',
            ]
        );
        $this->addForPageview($pageview, $pageview->getUid());
    }

    public function addForRecordview(
        Recordview $recordview,
        int $recordUid
    ): void {
        foreach ($this->extractorRegistry->getTagsForRecordview($recordview) as $tag) {
            $this->connection->insert(
                'tx_tracking_tag',
                [
                    'pid' => $recordview->getPageUid(),
                    'crdate' => $recordview->getCrdate()->format('U'),
                    'tstamp' => $recordview->getCrdate()->format('U'),
                    'record_uid' => $recordUid,
                    'record_table_name' => 'tx_tracking_recordview',
                    'name' => $tag->getName(),
                    'value' => $tag->getValue(),
                    'compatible_version' => Extension::getCompatibleVersionNow(),
                ]
            );
        }
    }

    public function updateForRecordview(
        Recordview $recordview
    ): void {
        $this->connection->delete(
            'tx_tracking_tag',
            [
                'record_uid' => $recordview->getUid(),
                'record_table_name' => 'tx_tracking_recordview',
            ]
        );
        $this->addForRecordview($recordview, $recordview->getUid());
    }
}
