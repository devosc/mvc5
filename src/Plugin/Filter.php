<?php
/**
 *
 */

namespace Mvc5\Plugin;

class Filter
    implements Gem\Filter
{
    /**
     *
     */
    use Config\Args;
    use Config\Config;

    /**
     * @var array
     */
    protected $filter;

    /**
     * @var string
     */
    protected $param;

    /**
     * @param $config
     * @param array|\Traversable $filter
     * @param array $args
     * @param string $param
     */
    function __construct($config, $filter = [], array $args = [], $param = null)
    {
        $this->args   = $args;
        $this->config = $config;
        $this->filter = $filter;
        $this->param  = $param;
    }

    /**
     * @return array|\Traversable
     */
    function filter()
    {
        return $this->filter;
    }

    /**
     * @return string
     */
    function param()
    {
        return $this->param;
    }
}
