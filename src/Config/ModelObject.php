<?php
/**
 *
 */

namespace Mvc5\Config;

use Mvc5\ArrayObject;

trait ModelObject
{
    /**
     * @var Model
     */
    protected Model $config;

    /**
     * @param array|Model $config
     */
    function __construct($config = [])
    {
        $this->config = $config instanceof Model ? $config : new ArrayObject((array) $config);
    }
}