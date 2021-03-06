<?php

/**
 * This file is part of the Pathfinder package.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types = 1);

namespace bitExpert\Pathfinder\Matcher;

/**
 * Matcher which matches against a set of constants defined by class and pattern
 */
class ConstantSetMatcher extends ValueSetMatcher
{
    /**
     * Creates a new {@link \bitExpert\Pathfinder\Matcher\ConstantSetMatcher}.
     *
     * @param mixed $classIdentifier Class or object to get the constants from
     * @param string $pattern A simplified expression using * as placeholder
     */
    public function __construct($classIdentifier, string $pattern)
    {
        $regex = $this->transformPatternToRegEx($pattern);
        $values = $this->getConstantValues($classIdentifier, $regex);

        parent::__construct($values);
    }

    /**
     * Returns the constant values of the given class when its name matches
     * the given regex.
     *
     * @param string $classIdentifier
     * @param string $regex
     * @return array
     */
    protected function getConstantValues(string $classIdentifier, string $regex) : array
    {
        $reflectionClass = new \ReflectionClass($classIdentifier);
        $constants = $reflectionClass->getConstants();

        $validValues = array_filter($constants, function ($constantName) use ($regex) {
            return preg_match($regex, $constantName);
        }, ARRAY_FILTER_USE_KEY);

        return $validValues;
    }

    /**
     * Transforms a simplified pattern (* for multiple characters ? for one)
     * to a regular expression.
     *
     * @param string $pattern
     * @return string
     */
    protected function transformPatternToRegEx(string $pattern) : string
    {
        $pattern = str_replace('*', '.*', $pattern);
        $pattern = str_replace('?', '.', $pattern);
        return '/^' . $pattern . '$/';
    }
}
