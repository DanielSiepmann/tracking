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

/**
 * API to extract further info out of an model.
 */
class Extractor
{
    public static function getOperatingSystem(HasUserAgent $model): string
    {
        $userAgent = $model->getUserAgent();

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

        return '';
    }

    public static function getBot(HasUserAgent $model): string
    {
        $userAgent = strtolower($model->getUserAgent());

        if (mb_stripos($userAgent, 'mastodon') !== false) {
            return 'mastodon';
        }
        if (mb_stripos($userAgent, 'slack') !== false) {
            return 'slack';
        }
        if (mb_stripos($userAgent, 'mattermost') !== false) {
            return 'mattermost';
        }
        if (mb_stripos($userAgent, 'whatsapp') !== false) {
            return 'whatsapp';
        }
        if (mb_stripos($userAgent, 'telegram') !== false) {
            return 'telegram';
        }
        if (mb_stripos($userAgent, 'twitter') !== false) {
            return 'twitter';
        }
        if (mb_stripos($userAgent, 'googlebot') !== false) {
            return 'google';
        }
        if (mb_stripos($userAgent, 'bingbot') !== false) {
            return 'bing';
        }
        if (mb_stripos($userAgent, 'dukduckgo') !== false) {
            return 'dukduckgo';
        }
        if (mb_stripos($userAgent, 'duckduckgo-favicons') !== false) {
            return 'duckduckgo-favicon';
        }
        if (mb_stripos($userAgent, 'feedly') !== false) {
            return 'feedly';
        }
        if (mb_stripos($userAgent, 'feedly') !== false) {
            return 'feedly';
        }
        if (mb_stripos($userAgent, 'nextcloud-news') !== false) {
            return 'nextcloud-news';
        }
        if (mb_stripos($userAgent, 'xing feedreader') !== false) {
            return 'xing-feedreader';
        }

        // TODO: Add Hook, or read config?

        return '';
    }
}
