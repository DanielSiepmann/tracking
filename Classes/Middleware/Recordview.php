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

namespace DanielSiepmann\Tracking\Middleware;

use DanielSiepmann\Tracking\Domain\Model\RecordRule;
use DanielSiepmann\Tracking\Domain\Recordview\Factory;
use DanielSiepmann\Tracking\Domain\Repository\Recordview as Repository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use TYPO3\CMS\Core\Context\Context;

class Recordview implements MiddlewareInterface
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var array<RecordRule>
     */
    private $rules = [];

    public function __construct(
        Repository $repository,
        Context $context,
        array $rules
    ) {
        $this->repository = $repository;
        $this->context = $context;

        $this->rules = RecordRule::multipleFromArray($rules);
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        foreach ($this->rules as $rule) {
            if ($this->shouldTrack($request, $this->context, $rule)) {
                $this->repository->add(Factory::fromRequest($request, $rule));
            }
        }

        return $handler->handle($request);
    }

    private function shouldTrack(
        ServerRequestInterface $request,
        Context $context,
        RecordRule $rule
    ): bool {
        // Need silent, as expression language doens't provide a way to check for array keys
        return @(bool) (new ExpressionLanguage())->evaluate(
            $rule->getMatchesExpression(),
            [
                'request' => $request,
                'context' => $context,
            ]
        );
    }
}
