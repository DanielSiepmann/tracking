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

namespace DanielSiepmann\Tracking\Domain\ExpressionLanguage;

use DanielSiepmann\Tracking\Domain\Model\Expression;
use DanielSiepmann\Tracking\Domain\Model\SymfonyExpression;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\Exception\MissingArrayPathException;

class SymfonyExpressionLanguage implements Factory
{
    public function create(
        string $expression,
        array $variables
    ): Expression {
        $language = new ExpressionLanguage();
        $this->registerTraverseFunction($language);

        return new SymfonyExpression(
            $expression,
            $variables,
            $language
        );
    }

    /**
     * Taken from TYPO3 Core, see: https://docs.typo3.org/m/typo3/reference-typoscript/11.5/en-us/Conditions/Index.html#traverse
     */
    private function registerTraverseFunction(ExpressionLanguage $language): void
    {
        $language->register(
            'traverse',
            static function () {
                // Not implemented, we only use the evaluator
            },
            static function ($arguments, $array, $path) {
                if (!is_array($array) || !is_string($path) || $path === '') {
                    return '';
                }
                try {
                    return ArrayUtility::getValueByPath($array, $path);
                } catch (MissingArrayPathException $e) {
                    return '';
                }
            }
        );
    }
}
