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

class Registry
{
    /**
     * @var PageviewExtractor[]
     */
    protected $pageviewExtractors = [];

    /**
     * @var RecordviewExtractor[]
     */
    protected $recordviewExtractors = [];

    public function addPageviewExtractor(PageviewExtractor $extractor): void
    {
        $this->pageviewExtractors[] = $extractor;
    }

    public function addRecordviewExtractor(RecordviewExtractor $extractor): void
    {
        $this->recordviewExtractors[] = $extractor;
    }

    /**
     * @return Tag[]
     */
    public function getTagsForPageview(Pageview $pageview): array
    {
        $tags = [];
        foreach ($this->pageviewExtractors as $extractor) {
            $tags = array_merge($tags, $extractor->extractTagFromPageview($pageview));
        }
        return $tags;
    }

    /**
     * @return Tag[]
     */
    public function getTagsForRecordview(Recordview $recordview): array
    {
        $tags = [];
        foreach ($this->recordviewExtractors as $extractor) {
            $tags = array_merge($tags, $extractor->extractTagFromRecordview($recordview));
        }
        return $tags;
    }
}
