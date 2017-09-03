<?php
/**
 *
 */

namespace Mvc5\Plugin;

use Mvc5\Arg;
use Mvc5\Service\Service;

class Register
    extends Call
{
    /**
     * @param string $name
     * @param mixed $service
     * @param mixed $plugin
     */
    function __construct(string $name, $service, $plugin = null)
    {
        parent::__construct(
            [$this, 'register'], [new Link, [Arg::NAME => $name, Arg::SERVICE => $service, Arg::PLUGIN => $plugin]]
        );
    }

    /**
     * @param Service $plugins
     * @param array $config
     * @return callable|object|null
     */
    function register(Service $plugins, array $config)
    {
        $service = $plugins->plugin($config[Arg::SERVICE]);

        return $service[$config[Arg::NAME]] ?? (
            isset($config[Arg::PLUGIN]) ? $service[$config[Arg::NAME]] = $plugins->plugin($config[Arg::PLUGIN]) : null
        );
    }
}
