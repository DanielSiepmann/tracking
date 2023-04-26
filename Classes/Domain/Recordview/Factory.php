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

namespace DanielSiepmann\Tracking\Domain\Recordview;

use DanielSiepmann\Tracking\Domain\ExpressionLanguage\Factory as ExpressionFactory;
use DanielSiepmann\Tracking\Domain\Model\RecordRule;
use DanielSiepmann\Tracking\Domain\Model\Recordview;
use DateTimeImmutable;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use UnexpectedValueException;

class Factory
{
    /**
     * @var ExpressionFactory
     */
    private $expressionFactory;

    public function __construct(
        ExpressionFactory $expressionFactory
    ) {
        $this->expressionFactory = $expressionFactory;
    }

    public function fromRequest(
        ServerRequestInterface $request,
        RecordRule $rule
    ): Recordview {
        $recordUid = $this->expressionFactory->create(
            $rule->getUidExpression(),
            ['request' => $request]
        )->evaluate();

        if (is_numeric($recordUid) === false) {
            throw new UnexpectedValueException(
                sprintf(
                    'Could not determine record uid based on expression: "%1$s", got type "%2$s".',
                    $rule->getUidExpression(),
                    gettype($recordUid)
                ),
                1637846881
            );
        }

        return new Recordview(
            self::getRouting($request)->getPageId(),
            self::getLanguage($request),
            new DateTimeImmutable(),
            (string)$request->getUri(),
            $request->getHeader('User-Agent')[0] ?? '',
            (int)$recordUid,
            $rule->getTableName()
        );
    }

    private static function getLanguage(ServerRequestInterface $request): SiteLanguage
    {
        $language = $request->getAttribute('language');

        if (!$language instanceof SiteLanguage) {
            throw new UnexpectedValueException('Could not fetch SiteLanguage from request attributes.', 1637847002);
        }

        return $language;
    }

    private static function getRouting(ServerRequestInterface $request): PageArguments
    {
        $routing = $request->getAttribute('routing');

        if (!$routing instanceof PageArguments) {
            throw new UnexpectedValueException('Could not fetch PageArguments from request attributes.', 1637847002);
        }

        return $routing;
    }
}
