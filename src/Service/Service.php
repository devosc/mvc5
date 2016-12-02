<?php
/**
 *
 */

namespace Mvc5\Service;

interface Service
{
    /**
     * @param array|callable|object|string $config
     * @param array $args
     * @param callable $callback
     * @return callable|mixed|null|object
     */
    function call($config, array $args = [], callable $callback = null);

    /**
     * @param string $name
     * @return mixed
     */
    function param($name);

    /**
     * @param string $name
     * @param callable $callback
     * @param array $args
     * @return callable|mixed|null|object
     */
    function plugin($name, array $args = [], callable $callback = null);

    /**
     * @param string $name
     * @param $config
     * @return callable|mixed|null|object
     */
    function shared($name, $config = null);

    /**
     * @param array|object|string|\Traversable $event
     * @param array $args
     * @param callable $callback
     * @return mixed|null
     */
    function trigger($event, array $args = [], callable $callback = null);
}
