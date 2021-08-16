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

namespace DanielSiepmann\Tracking\Domain\Extractors;

use DanielSiepmann\Tracking\Domain\Extractors\Bots\CustomBotParser;
use DanielSiepmann\Tracking\Domain\Model\Pageview;
use DanielSiepmann\Tracking\Domain\Model\Recordview;
use DeviceDetector\DeviceDetector;

class Bots implements PageviewExtractor, RecordviewExtractor
{
    /**
     * @var CustomBotParser
     */
    private $customBotParser;

    public function __construct(
        CustomBotParser $customBotParser
    ) {
        $this->customBotParser = $customBotParser;
    }

    public function extractTagFromPageview(Pageview $pageview): array
    {
        return $this->getTagsForUserAgent($pageview->getUserAgent());
    }

    public function extractTagFromRecordview(Recordview $recordview): array
    {
        return $this->getTagsForUserAgent($recordview->getUserAgent());
    }

    /**
     * @return Tag[]
     */
    private function getTagsForUserAgent(string $userAgent): array
    {
        $botNameTag = new Tag('bot_name', $this->getBotName($userAgent));

        if ($botNameTag->getValue() !== '') {
            return [
                new Tag('bot', 'yes'),
                $botNameTag,
            ];
        }
        return [new Tag('bot', 'no')];
    }

    private function getBotName(string $userAgent): string
    {
        $deviceDetector = new DeviceDetector();
        $deviceDetector->addBotParser($this->customBotParser);
        $deviceDetector->setUserAgent($userAgent);
        $deviceDetector->parse();

        if ($deviceDetector->isBot() === false) {
            return '';
        }

        $bot = $deviceDetector->getBot();
        if (is_array($bot) === false) {
            return '';
        }

        return $bot['name'] ?? '';
    }
}
