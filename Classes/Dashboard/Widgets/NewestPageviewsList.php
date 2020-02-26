<?php

namespace DanielSiepmann\Tracking\Dashboard\Widgets;

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
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Dashboard\Widgets\AbstractListWidget;

class NewestPageviewsList extends AbstractListWidget
{
    protected $title = Extension::LANGUAGE_PATH . ':dashboard.widgets.newestPageviewsList.title';

    protected $description = Extension::LANGUAGE_PATH . ':dashboard.widgets.newestPageviewsList.description';

    protected $width = 2;

    protected $height = 4;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var \ArrayObject
     */
    private $settings;

    public function __construct(
        string $identifier,
        QueryBuilder $queryBuilder,
        \ArrayObject $settings
    ) {
        parent::__construct($identifier);

        $this->queryBuilder = $queryBuilder;
        $this->settings = $settings;
    }

    public function renderWidgetContent(): string
    {
        $this->generateItems();
        return parent::renderWidgetContent();
    }

    protected function generateItems(): void
    {
        $constraints = [];
        if (count($this->settings['blackListedPages'])) {
            $constraints[] = $this->queryBuilder->expr()->notIn(
                'tx_tracking_pageview.pid',
                $this->queryBuilder->createNamedParameter(
                    $this->settings['blackListedPages'],
                    Connection::PARAM_INT_ARRAY
                )
            );
        }

        $this->queryBuilder
            ->select('*')
            ->from('tx_tracking_pageview')
            ->orderBy('crdate desc')
            ->setMaxResults($this->settings['maxResults']);

        if ($constraints !== []) {
            $this->queryBuilder->where(... $constraints);
        }

        $items = $this->queryBuilder->execute()->fetchAll();
        foreach ($items as $item) {
            $this->items[] = [
                'link' => $item['url'],
                'title' => sprintf(
                    '%s - %s',
                    $item['url'],
                    $item['user_agent']
                ),
            ];
        }
    }
}
