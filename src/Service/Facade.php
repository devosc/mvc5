<?php
/**
 *
 */

namespace Mvc5\Service;

trait Facade
{
    /**
     * @param callable|mixed $plugin
     * @param array $args
     * @param callable|null $callback
     * @return mixed
     * @throws \Throwable
     */
    protected static function call($plugin, array $args = [], callable $callback = null)
    {
        return static::service()->call($plugin, $args, $callback);
    }

    /**
     * @param array|string $name
     * @return mixed
     * @throws \Throwable
     */
    protected static function param($name)
    {
        return static::service()->param($name);
    }

    /**
     * @param string|mixed $plugin
     * @param array $args
     * @param callable|null $callback
     * @return mixed
     * @throws \Throwable
     */
    protected static function plugin($plugin, array $args = [], callable $callback = null)
    {
        return static::service()->plugin($plugin, $args, $callback);
    }

    /**
     * @return callable|Manager|Service
     * @throws \Throwable
     */
    protected static function service() : Service
    {
        return Context::service();
    }

    /**
     * @param array|string $name
     * @param mixed $config
     * @return mixed
     * @throws \Throwable
     */
    protected static function shared($name, $config = null)
    {
        return static::service()->shared($name, $config);
    }

    /**
     * @param array|object|string|\Traversable $event
     * @param array $args
     * @param callable|null $callback
     * @return mixed
     * @throws \Throwable
     */
    protected static function trigger($event, array $args = [], callable $callback = null)
    {
        return static::service()->trigger($event, $args, $callback);
    }
}
