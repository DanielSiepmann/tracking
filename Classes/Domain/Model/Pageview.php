<?php

namespace DanielSiepmann\Tracking\Domain\Model;

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

use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

class Pageview
{
    /**
     * @var int
     */
    private $pageUid;

    /**
     * @var SiteLanguage
     */
    private $language;

    /**
     * @var \DateTimeImmutable
     */
    private $crdate;

    /**
     * @var int
     */
    private $pageType;

    /**
     * @var string
     */
    private $url;

    public function __construct(
        int $pageUid,
        SiteLanguage $language,
        \DateTimeImmutable $crdate,
        int $pageType,
        string $url
    ) {
        $this->pageUid = $pageUid;
        $this->language = $language;
        $this->crdate = $crdate;
        $this->pageType = $pageType;
        $this->url = $url;
    }

    public function getPageUid(): int
    {
        return $this->pageUid;
    }

    public function getLanguage(): SiteLanguage
    {
        return $this->language;
    }

    public function getCrdate(): \DateTimeImmutable
    {
        return $this->crdate;
    }

    public function getPageType(): int
    {
        return $this->pageType;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
