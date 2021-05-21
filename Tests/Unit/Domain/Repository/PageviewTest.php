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

use DanielSiepmann\Tracking\Domain\Model\Pageview as Model;
use DanielSiepmann\Tracking\Domain\Pageview\Factory;
use DanielSiepmann\Tracking\Domain\Repository\Pageview;
use Doctrine\DBAL\Statement;
use Prophecy\PhpUnit\ProphecyTrait;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase as TestCase;

/**
 * @covers DanielSiepmann\Tracking\Domain\Repository\Pageview
 */
class PageviewTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function modelCanBeAdded(): void
    {
        $connection = $this->prophesize(Connection::class);
        $factory = $this->prophesize(Factory::class);

        $dateTime = $this->prophesize(\DateTimeImmutable::class);
        $dateTime->format('U')->willReturn(1582660189);

        $language = $this->prophesize(SiteLanguage::class);
        $language->getLanguageId()->willReturn(2);

        $model = $this->prophesize(Model::class);
        $model->getPageUid()->willReturn(10);
        $model->getCrdate()->willReturn($dateTime->reveal());
        $model->getPageType()->willReturn(999);
        $model->getLanguage()->willReturn($language->reveal());
        $model->getUrl()->willReturn('https://example.com/path.html');
        $model->getUserAgent()->willReturn('Mozilla/5.0 (Windows NT 10.0) Gecko/20100101 Firefox/74.0');
        $model->getOperatingSystem()->willReturn('Linux');

        $connection->insert(
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
        )->willReturn(1)->shouldBeCalledTimes(1);

        $subject = new Pageview($connection->reveal(), $factory->reveal());
        $subject->add($model->reveal());
    }

    /**
     * @test
     */
    public function throwsExceptionIfModelToUodateHasNoUid(): void
    {
        $connection = $this->prophesize(Connection::class);
        $factory = $this->prophesize(Factory::class);

        $model = $this->prophesize(Model::class);
        $model->getUid()->willReturn(0);

        $subject = new Pageview($connection->reveal(), $factory->reveal());
        $this->expectExceptionMessage('Can not update pageview if uid is 0.');
        $subject->update($model->reveal());
    }

    /**
     * @test
     */
    public function modelCanBeUpdated(): void
    {
        $connection = $this->prophesize(Connection::class);
        $factory = $this->prophesize(Factory::class);

        $dateTime = $this->prophesize(\DateTimeImmutable::class);
        $dateTime->format('U')->willReturn(1582660189);

        $language = $this->prophesize(SiteLanguage::class);
        $language->getLanguageId()->willReturn(2);

        $model = $this->prophesize(Model::class);
        $model->getUid()->willReturn(1);
        $model->getPageUid()->willReturn(10);
        $model->getCrdate()->willReturn($dateTime->reveal());
        $model->getPageType()->willReturn(999);
        $model->getLanguage()->willReturn($language->reveal());
        $model->getUrl()->willReturn('https://example.com/path.html');
        $model->getUserAgent()->willReturn('Mozilla/5.0 (Windows NT 10.0) Gecko/20100101 Firefox/74.0');
        $model->getOperatingSystem()->willReturn('Linux');

        $connection->update(
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
                'uid' => 1
            ]
        )->willReturn(1)->shouldBeCalledTimes(1);

        $subject = new Pageview($connection->reveal(), $factory->reveal());
        $subject->update($model->reveal());
    }

    /**
     * @test
     */
    public function returnsACountOfAllModels(): void
    {
        $statement = $this->prophesize(Statement::class);
        $statement->fetchColumn()->willReturn(10);

        $queryBuilder = $this->prophesize(QueryBuilder::class);
        $queryBuilder->count('uid')->willReturn($queryBuilder->reveal());
        $queryBuilder->from('tx_tracking_pageview')->willReturn($queryBuilder->reveal());
        $queryBuilder->execute()->willReturn($statement->reveal());

        $connection = $this->prophesize(Connection::class);
        $connection->createQueryBuilder()->willReturn($queryBuilder->reveal());

        $factory = $this->prophesize(Factory::class);

        $subject = new Pageview($connection->reveal(), $factory->reveal());
        static::assertSame(10, $subject->countAll());
    }

    /**
     * @test
     */
    public function returnsAllModells(): void
    {
        $statement = $this->prophesize(Statement::class);
        $statement->fetch()->willReturn(
            [
                'pid' => '10',
                'crdate' => '1595948372',
                'type' => '0',
                'sys_language_uid' => '0',
                'url' => 'https://example.com/path/file.html',
                'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
            ],
            [
                'pid' => '9',
                'crdate' => '1595948376',
                'type' => '0',
                'sys_language_uid' => '0',
                'url' => 'https://example.com/path/file.html',
                'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
            ],
            false
        );

        $queryBuilder = $this->prophesize(QueryBuilder::class);
        $queryBuilder->select('*')->willReturn($queryBuilder->reveal());
        $queryBuilder->from('tx_tracking_pageview')->willReturn($queryBuilder->reveal());
        $queryBuilder->execute()->willReturn($statement->reveal());

        $connection = $this->prophesize(Connection::class);
        $connection->createQueryBuilder()->willReturn($queryBuilder->reveal());

        $model1 = $this->prophesize(Model::class);
        $model1->getPageUid()->willReturn(10);
        $model2 = $this->prophesize(Model::class);
        $model2->getPageUid()->willReturn(9);

        $factory = $this->prophesize(Factory::class);
        $factory->fromDbRow([
            'pid' => '10',
            'crdate' => '1595948372',
            'type' => '0',
            'sys_language_uid' => '0',
            'url' => 'https://example.com/path/file.html',
            'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
        ])->willReturn($model1->reveal());
        $factory->fromDbRow([
            'pid' => '9',
            'crdate' => '1595948376',
            'type' => '0',
            'sys_language_uid' => '0',
            'url' => 'https://example.com/path/file.html',
            'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
        ])->willReturn($model2->reveal());

        $subject = new Pageview($connection->reveal(), $factory->reveal());
        static::assertCount(2, $subject->findAll());

        $pageUid = 10;
        foreach ($subject->findAll() as $model) {
            static::assertSame($pageUid, $model->getPageUid());
            --$pageUid;
        }
    }
}
