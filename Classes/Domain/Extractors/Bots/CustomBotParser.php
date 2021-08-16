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

namespace DanielSiepmann\Tracking\Domain\Extractors\Bots;

use DeviceDetector\Parser\Bot;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CustomBotParser extends Bot
{
    protected $parserName = 'customBots';

    /**
     * @var string
     */
    protected $dirName = '';

    public function __construct()
    {
        parent::__construct();

        $fixtureFile = GeneralUtility::getFileAbsFileName('EXT:tracking/Configuration/Bots.yaml');
        $this->fixtureFile = basename($fixtureFile);
        $this->dirName = dirname($fixtureFile);
    }

    protected function getRegexesDirectory(): string
    {
        return $this->dirName;
    }

    public function parse(): ?array
    {
        return parent::parse();
    }
}
