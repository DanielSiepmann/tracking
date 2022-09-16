<?php

declare(strict_types=1);

/*
 * Copyright (C) 2022 Daniel Siepmann <coding@daniel-siepmann.de>
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

namespace DanielSiepmann\Tracking\Domain\Model;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class SymfonyExpression implements Expression
{
    /**
     * @var string
     */
    private $expression;

    /**
     * @var array
     */
    private $values;

    /**
     * @var ExpressionLanguage
     */
    private $symfonyExpression;

    public function __construct(
        string $expression,
        array $values,
        ExpressionLanguage $symfonyExpression
    ) {
        $this->expression = $expression;
        $this->values = $values;
        $this->symfonyExpression = $symfonyExpression;
    }

    public function evaluate()
    {
        return $this->symfonyExpression->evaluate(
            $this->expression,
            $this->values
        );
    }
}
