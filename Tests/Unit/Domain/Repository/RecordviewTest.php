<?php

namespace DanielSiepmann\Tracking\Tests\Unit\Domain\Repository;

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

use DanielSiepmann\Tracking\Domain\Model\Recordview as Model;
use DanielSiepmann\Tracking\Domain\Repository\Recordview;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * @covers DanielSiepmann\Tracking\Domain\Repository\Recordview
 */
class RecordviewTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function modelCanBeAdded(): void
    {
        $connection = $this->prophesize(Connection::class);

        $dateTime = $this->prophesize(\DateTimeImmutable::class);
        $dateTime->format('U')->willReturn(1582660189);

        $language = $this->prophesize(SiteLanguage::class);
        $language->getLanguageId()->willReturn(2);

        $model = $this->prophesize(Model::class);
        $model->getPageUid()->willReturn(10);
        $model->getCrdate()->willReturn($dateTime->reveal());
        $model->getLanguage()->willReturn($language->reveal());
        $model->getUrl()->willReturn('https://example.com/path.html');
        $model->getUserAgent()->willReturn('Mozilla/5.0 (Windows NT 10.0) Gecko/20100101 Firefox/74.0');
        $model->getOperatingSystem()->willReturn('Linux');
        $model->getRecordUid()->willReturn(10);
        $model->getTableName()->willReturn('sys_category');

        $connection->insert(
            'tx_tracking_recordview',
            [
                'pid' => 10,
                'crdate' => 1582660189,
                'tstamp' => 1582660189,
                'sys_language_uid' => 2,
                'url' => 'https://example.com/path.html',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0) Gecko/20100101 Firefox/74.0',
                'operating_system' => 'Linux',
                'record_uid' => 10,
                'record_table_name' => 'sys_category',
                'record' => 'sys_category_10',
            ]
        )->willReturn(1)->shouldBeCalledTimes(1);

        $subject = new Recordview($connection->reveal());
        $subject->add($model->reveal());
    }
}
