<?php
/**
 *
 */

namespace Mvc5\Plugin\Gem;

interface Invoke
    extends Gem
{
    /**
     * @return array
     */
    function args() : array;

    /**
     * @return string|array
     */
    function config();
}
