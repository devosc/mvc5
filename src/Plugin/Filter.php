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
     * @var array|\Traversable
     */
    protected $filter;

    /**
     * @var string|null
     */
    protected $param;

    /**
     * @param $config
     * @param array|\Traversable $filter
     * @param array $args
     * @param string|null $param
     */
    function __construct($config, $filter = [], array $args = [], string $param = null)
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
     * @return string|null
     */
    function param()
    {
        return $this->param;
    }
}
