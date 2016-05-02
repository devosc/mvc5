<?php
/**
 *
 */

namespace Mvc5\Route\Definition;

/**
 * Portions copyright (c) 2013 Ben Scholzen 'DASPRiD'. (http://github.com/DASPRiD/Dash)
 * under the Simplified BSD License (http://opensource.org/licenses/BSD-2-Clause).
 */
trait Params
{
    /**
     * @param array $tokens
     * @return array
     */
    protected function params(array $tokens)
    {
        $index = 1;
        $map   = [];

        foreach($tokens as $token) {
            'param' == $token[Dash::TYPE] && $map['param' . $index++] = $token[Dash::NAME];
        }

        return $map;
    }
}
