<?php

declare(strict_types=1);

/*
 * Copyright (C) 2024 Daniel Siepmann <daniel.siepmann@codappix.com>
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

use TYPO3\CMS\Core\Site\Entity\Site as SiteEntity;
use TYPO3\CMS\Core\Site\SiteFinder;

class Site
{
    public function __construct(
        private readonly SiteFinder $siteFinder
    ) {
    }

    public function findByPageUid(int $pageUid): SiteEntity
    {
        return $this->siteFinder->getSiteByPageId($pageUid);
    }
}
