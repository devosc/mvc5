<?php
/**
 *
 */

namespace Mvc5\Service;

use Mvc5\Exception;

final class Context
{
    /**
     * @var Service
     */
    protected static $service;

    /**
     * @param Service|null $service
     */
    function __construct(Service $service = null)
    {
        $service && $this->bind($service);
    }

    /**
     * @param Service $service
     * @return callable|Manager|Service
     */
    static function bind(Service $service)
    {
        isset(static::$service) &&
            Exception::runtime('Service already exists');

        return static::$service = $service;
    }

    /**
     * @return callable|Manager|Service
     */
    static function service()
    {
        return static::$service ?: Exception::runtime('Service does not exist');
    }

    /**
     * @param $name
     * @param array $args
     * @return mixed
     */
    static function __callStatic($name, array $args)
    {
        return static::service()->call($name, $args);
    }

    /**
     * @param Service $service
     */
    function __invoke(Service $service)
    {
        $this->bind($service);
    }
}
