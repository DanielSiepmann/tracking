<?php

declare(strict_types=1);

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
use DanielSiepmann\Tracking\Domain\Model\Pageview as Model;
use DanielSiepmann\Tracking\Domain\Pageview\Factory;
use DanielSiepmann\Tracking\Domain\Repository\Pageview;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Pageview::class)]
class PageviewTest extends UnitTestCase
{
    #[Test]
    public function modelCanBeAdded(): void
    {
        $connection = $this->createMock(Connection::class);
        $factory = $this->createMock(Factory::class);

        $dateTime = $this->createStub(DateTimeImmutable::class);
        $dateTime->method('format')->willReturn('1582660189');

        $language = $this->createStub(SiteLanguage::class);
        $language->method('getLanguageId')->willReturn(2);

        $model = $this->createStub(Model::class);
        $model->method('getPageUid')->willReturn(10);
        $model->method('getCrdate')->willReturn($dateTime);
        $model->method('getPageType')->willReturn(999);
        $model->method('getLanguage')->willReturn($language);
        $model->method('getUrl')->willReturn('https://example.com/path.html');
        $model->method('getUserAgent')->willReturn('Mozilla/5.0 (Windows NT 10.0) Gecko/20100101 Firefox/74.0');
        $model->method('getOperatingSystem')->willReturn('Linux');

        $connection
            ->expects(self::once())
            ->method('insert')
            ->with(
                'tx_tracking_pageview',
                [
                    'pid' => 10,
                    'crdate' => 1582660189,
                    'tstamp' => 1582660189,
                    'type' => 999,
                    'sys_language_uid' => 2,
                    'url' => 'https://example.com/path.html',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0) Gecko/20100101 Firefox/74.0',
                    'operating_system' => 'Linux',
                ]
            )
            ->willReturn(1)
        ;

        $subject = new Pageview($connection, $factory);
        $subject->add($model);
    }

    #[Test]
    public function throwsExceptionIfModelToUodateHasNoUid(): void
    {
        $connection = $this->createMock(Connection::class);
        $factory = $this->createMock(Factory::class);

        $model = $this->createStub(Model::class);
        $model->method('getUid')->willReturn(0);

        $subject = new Pageview($connection, $factory);
        $this->expectExceptionMessage('Can not update pageview if uid is 0.');
        $subject->update($model);
    }

    #[Test]
    public function modelCanBeUpdated(): void
    {
        $connection = $this->createMock(Connection::class);
        $factory = $this->createMock(Factory::class);

        $dateTime = $this->createStub(DateTimeImmutable::class);
        $dateTime->method('format')->willReturn('1582660189');

        $language = $this->createStub(SiteLanguage::class);
        $language->method('getLanguageId')->willReturn(2);

        $model = $this->createStub(Model::class);
        $model->method('getUid')->willReturn(1);
        $model->method('getPageUid')->willReturn(10);
        $model->method('getCrdate')->willReturn($dateTime);
        $model->method('getPageType')->willReturn(999);
        $model->method('getLanguage')->willReturn($language);
        $model->method('getUrl')->willReturn('https://example.com/path.html');
        $model->method('getUserAgent')->willReturn('Mozilla/5.0 (Windows NT 10.0) Gecko/20100101 Firefox/74.0');
        $model->method('getOperatingSystem')->willReturn('Linux');

        $connection
            ->expects(self::once())
            ->method('update')
            ->with(
                'tx_tracking_pageview',
                [
                    'pid' => 10,
                    'crdate' => 1582660189,
                    'tstamp' => 1582660189,
                    'type' => 999,
                    'sys_language_uid' => 2,
                    'url' => 'https://example.com/path.html',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0) Gecko/20100101 Firefox/74.0',
                    'operating_system' => 'Linux',
                ],
                [
                    'uid' => 1,
                ]
            )
            ->willReturn(1)
        ;

        $subject = new Pageview($connection, $factory);
        $subject->update($model);
    }
}
