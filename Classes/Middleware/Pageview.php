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

use DanielSiepmann\Tracking\Domain\ExpressionLanguage\Factory as ExpressionFactory;
use DanielSiepmann\Tracking\Domain\Pageview\Factory;
use DanielSiepmann\Tracking\Domain\Repository\Pageview as Repository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Context\Context;

class Pageview implements MiddlewareInterface
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
     * @var Factory
     */
    private $factory;

    /**
     * @var ExpressionFactory
     */
    private $expressionFactory;

    /**
     * @var string
     */
    private $rule = '';

    public function __construct(
        Repository $repository,
        Context $context,
        Factory $factory,
        ExpressionFactory $expressionFactory,
        string $rule
    ) {
        $this->repository = $repository;
        $this->context = $context;
        $this->factory = $factory;
        $this->expressionFactory = $expressionFactory;
        $this->rule = $rule;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        if ($this->shouldTrack($request, $this->context)) {
            $this->repository->add($this->factory->fromRequest($request));
        }

        return $handler->handle($request);
    }

    private function shouldTrack(
        ServerRequestInterface $request,
        Context $context
    ): bool {
        return (bool) $this->expressionFactory->create(
            $this->rule,
            [
                'request' => $request,
                'context' => $context,
            ]
        )->evaluate();
    }
}
