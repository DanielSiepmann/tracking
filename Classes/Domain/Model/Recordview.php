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

namespace DanielSiepmann\Tracking\Domain\Model;

use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

class Recordview implements HasUserAgent
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
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $userAgent;

    /**
     * @var int
     */
    private $recordUid;

    /**
     * @var string
     */
    private $tableName;

    public function __construct(
        int $pageUid,
        SiteLanguage $language,
        \DateTimeImmutable $crdate,
        string $url,
        string $userAgent,
        int $recordUid,
        string $tableName
    ) {
        $this->pageUid = $pageUid;
        $this->language = $language;
        $this->crdate = $crdate;
        $this->url = $url;
        $this->userAgent = $userAgent;
        $this->recordUid = $recordUid;
        $this->tableName = $tableName;
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

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function getRecordUid(): int
    {
        return $this->recordUid;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getOperatingSystem(): string
    {
        return Extractor::getOperatingSystem($this);
    }
}
