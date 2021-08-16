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

use DanielSiepmann\Tracking\Domain\Model\Pageview;
use DanielSiepmann\Tracking\Domain\Model\Recordview;

class OperatingSystem implements PageviewExtractor, RecordviewExtractor
{
    public function extractTagFromPageview(Pageview $pageview): array
    {
        return [new Tag('os', $this->getOperatingSystem($pageview->getUserAgent()))];
    }

    public function extractTagFromRecordview(Recordview $recordview): array
    {
        return [new Tag('os', $this->getOperatingSystem($recordview->getUserAgent()))];
    }

    private function getOperatingSystem(string $userAgent): string
    {
        if (mb_stripos($userAgent, 'Android') !== false) {
            return 'Android';
        }
        if (mb_stripos($userAgent, 'Windows') !== false) {
            return 'Windows';
        }
        if (mb_stripos($userAgent, 'Linux') !== false) {
            return 'Linux';
        }
        if (mb_stripos($userAgent, 'Macintosh') !== false) {
            return 'Macintosh';
        }
        if (mb_stripos($userAgent, 'CrOS') !== false) {
            return 'Google Chrome OS';
        }
        if (mb_stripos($userAgent, 'OpenBSD') !== false) {
            return 'OpenBSD';
        }
        if (
            mb_stripos($userAgent, 'iPad') !== false
            || mb_stripos($userAgent, 'iphone') !== false
        ) {
            return 'iOS';
        }

        return 'Unkown';
    }
}
