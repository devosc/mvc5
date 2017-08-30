<?php
/**
 *
 */

namespace Mvc5\Resolver;

use Mvc5\Arg;
use Mvc5\Plugin\Gem\Args;
use Mvc5\Plugin\Gem\Call;
use Mvc5\Plugin\Gem\Calls;
use Mvc5\Plugin\Gem\Child;
use Mvc5\Plugin\Gem\Config;
use Mvc5\Plugin\Gem\Copy;
use Mvc5\Plugin\Gem\Factory;
use Mvc5\Plugin\Gem\FileInclude;
use Mvc5\Plugin\Gem\Filter;
use Mvc5\Plugin\Gem\Gem;
use Mvc5\Plugin\Gem\Invokable;
use Mvc5\Plugin\Gem\Invoke;
use Mvc5\Plugin\Gem\Link;
use Mvc5\Plugin\Gem\Param;
use Mvc5\Plugin\Gem\Plug;
use Mvc5\Plugin\Gem\Plugin;
use Mvc5\Plugin\Gem\Provide;
use Mvc5\Plugin\Gem\Scoped;
use Mvc5\Plugin\Gem\Shared;
use Mvc5\Plugin\Gem\SignalArgs;
use Mvc5\Plugin\Gem\Value;
use Mvc5\Resolvable;

trait Resolver
{
    /**
     *
     */
    use Build;
    use Container;
    use Generator;
    use Service;

    /**
     * @var callable
     */
    protected $provider;

    /**
     * @var object
     */
    protected $scope;

    /**
     * @param array|\ArrayAccess $config
     * @param callable|null $provider
     * @param bool|null|object $scope
     * @param bool $strict
     */
    function __construct($config = null, callable $provider = null, $scope = null, bool $strict = false)
    {
        $config && $this->config = $config;

        isset($config[Arg::CONTAINER])
            && $this->container = $config[Arg::CONTAINER];

        isset($config[Arg::EVENTS])
            && $this->events = $config[Arg::EVENTS];

        isset($config[Arg::SERVICES])
            && $this->services = $config[Arg::SERVICES];

        $provider && $this->provider = $this->resolve($provider);

        $scope && $this->scope = $this->resolve($scope);

        $strict && $this->strict = $strict;
    }

    /**
     * @param $args
     * @return array|callable|null|object|string
     */
    protected function args($args)
    {
        if (!$args) {
            return $args;
        }

        if (!is_array($args)) {
            return $this->resolve($args);
        }

        foreach($args as $index => $value) {
            $value instanceof Resolvable && $args[$index] = $this->resolve($value);
        }

        return $args;
    }

    /**
     * @param array $child
     * @param array $parent
     * @return array
     */
    protected function arguments(array $child, array $parent)
    {
        return !$parent ? $child : (
            !$child ? $parent : (is_string(key($child)) ? $child + $parent : array_merge($child, $parent))
        );
    }

    /**
     * @param \Closure $callback
     * @param object $object
     * @param bool $scoped
     * @return \Closure
     */
    protected function bind(\Closure $callback, $object, $scoped)
    {
        return \Closure::bind($callback, $object, $scoped ? $object : null);
    }

    /**
     * @param Child $config
     * @param array $args
     * @return array|callable|object|string
     */
    protected function child(Child $config, array $args = [])
    {
        return $this->provide($this->merge($this->parent($config->parent()), $config), $args);
    }

    /**
     * @param array|callable|null|object|string $value
     * @param array|\Traversable $filters
     * @param array $args
     * @param $param
     * @return mixed
     */
    protected function filter($value, $filters = [], array $args = [], string $param = null)
    {
        $result = $value;

        foreach($filters as $filter) {
            $value = $this->invoke(
                $this->callable($filter), $param ? [$param => $result] + $args : array_merge([$result], $args)
            );

            if (false === $value) {
                return $result;
            }

            if (null === $value) {
                return null;
            }

            $result = $value;
        }

        return $result;
    }

    /**
     * @param Filter $config
     * @param array $args
     * @return mixed
     */
    protected function filterable(Filter $config, array $args = [])
    {
        return $this->filter(
            $this->resolve($config->config()), $this->resolve($config->filter()), $args, $config->param()
        );
    }

    /**
     * @param Gem $gem
     * @param array $args
     * @return mixed|callable
     */
    protected function gem(Gem $gem, array $args = [])
    {
        if ($gem instanceof Factory) {
            return $this->invoke($this->child($gem, $args));
        }

        if ($gem instanceof Calls) {
            return $this->hydrate($gem, $this->resolve($gem->name(), $args));
        }

        if ($gem instanceof Child) {
            return $this->child($gem, $args);
        }

        if ($gem instanceof Plugin) {
            return $this->provide($gem, $args);
        }

        if ($gem instanceof Shared) {
            return $this->shared($gem->name(), $gem->config());
        }

        if ($gem instanceof Param) {
            return $this->resolve($this->param($gem->name()), $args);
        }

        if ($gem instanceof Call) {
            return $this->call($this->resolve($gem->config()), $this->vars($args, $gem->args()));
        }

        if ($gem instanceof Args) {
            return $this->args($gem->config());
        }

        if ($gem instanceof Config) {
            return $this->config();
        }

        if ($gem instanceof Link) {
            return $this;
        }

        if ($gem instanceof Filter) {
            return $this->filterable($gem, $this->vars($args, $gem->args()));
        }

        if ($gem instanceof Plug) {
            return $this->configured($gem->name());
        }

        if ($gem instanceof Invoke) {
            return function(...$argv) use ($gem) {
                return $this->call(
                    $this->resolve($gem->config()), $this->vars($this->variadic($argv), $gem->args())
                );
            };
        }

        if ($gem instanceof Invokable) {
            return function(...$argv) use ($gem) {
                return $this->resolve($gem->config(), $this->vars($this->variadic($argv), $gem->args()));
            };
        }

        if ($gem instanceof FileInclude) {
            /** @var callable $include */
            $include = new class() {
                function __invoke($file) {
                    return include $file;
                }
            };

            return $include($this->resolve($gem->config()));
        }

        if ($gem instanceof Copy) {
            return clone $this->resolve($gem->config(), $args);
        }

        if ($gem instanceof Value) {
            return $gem->config();
        }

        if ($gem instanceof Scoped) {
            return $this->scoped($gem->closure(), $gem->scoped());
        }

        if ($gem instanceof Provide) {
            return ($this->provider() ?: new Unresolvable)($gem->config(), $this->vars($args, $gem->args()));
        }

        return Unresolvable::plugin($gem);
    }

    /**
     * @param Plugin $plugin
     * @param object $service
     * @return object
     */
    protected function hydrate(Plugin $plugin, $service)
    {
        foreach($plugin->calls() as $method => $args) {
            if (is_string($method)) {
                if (Arg::INDEX == $method[0]) {
                    $service[substr($method, 1)] = $this->resolve($args);
                    continue;
                }

                if (Arg::PROPERTY == $method[0]) {
                    $service->{substr($method, 1)} = $this->resolve($args);
                    continue;
                }

                $service->$method($this->resolve($args));
                continue;
            }

            if (is_array($args)) {
                $method = array_shift($args);
                $param  = $plugin->param();

                if (is_string($method) && Arg::PROPERTY == $method[0]) {
                    $param  = substr($method, 1);
                    $method = array_shift($args);
                }

                $this->invoke(
                    is_string($method) ? [$service, $method] : $this->callable($method),
                    ($param && (!$args || is_string(key($args))) ? [$param => $service] : []) + $this->args($args)
                );

                continue;
            }

            $this->resolve($args);
        }

        return $service;
    }

    /**
     * @param Plugin $parent
     * @param Plugin $child
     * @param null|string $name
     * @param array $config
     * @return Plugin
     */
    protected function merge(Plugin $parent, Plugin $child, string $name = null, array $config = [])
    {
        !$parent->name() &&
            $config[Arg::NAME] = $name ?? $this->resolve($child->name());

        $child->args() &&
            $config[Arg::ARGS] = is_string(key($child->args())) ? $child->args() + $parent->args() : $child->args();

        $child->calls() &&
            $config[Arg::CALLS] = $child->merge() ? array_merge($parent->calls(), $child->calls()) : $child->calls();

        $child->param() &&
            $config[Arg::PARAM] = $child->param();

        return $config ? $parent->with($config) : $parent;
    }

    /**
     * @param string $name
     * @return mixed
     */
    function param(string $name)
    {
        $name  = explode(Arg::CALL_SEPARATOR, $name);
        $value = $this->config()[array_shift($name)];

        foreach($name as $n) {
            $value = $value[$n];
        }

        return $value;
    }

    /**
     * @param $plugin
     * @return array|callable|Plugin|null|object|string
     */
    protected function parent(string $plugin)
    {
        return $this->configured($this->resolve($plugin));
    }

    /**
     * @param $plugin
     * @param array $args
     * @param callable|null $callback
     * @param null|string $previous
     * @return array|callable|null|object|string
     */
    function plugin($plugin, array $args = [], callable $callback = null, string $previous = null)
    {
        if (!$plugin) {
            return $plugin;
        }

        if (is_string($plugin)) {
            return $this->build(explode(Arg::SERVICE_SEPARATOR, $plugin), $args, $callback);
        }

        if (is_array($plugin)) {
            return $this->pluginArray(array_shift($plugin), $args + $this->args($plugin), $callback, $previous);
        }

        if ($plugin instanceof \Closure) {
            return $this->invoke($this->scoped($plugin), $args);
        }

        return $this->resolve($plugin, $args);
    }

    /**
     * @param $plugin
     * @param array $args
     * @param callable|null $callback
     * @param null|string $previous
     * @return array|callable|null|object|string
     */
    protected function pluginArray($plugin, array $args = [], callable $callback = null, string $previous = null)
    {
        return $previous && $previous === $plugin ?
            $this->callback($plugin, true, $args, $callback) : $this->plugin($plugin, $args, $callback);
    }

    /**
     * @param Plugin $plugin
     * @param array $args
     * @return callable|null|object
     */
    protected function provide(Plugin $plugin, array $args = [])
    {
        $name   = $this->resolve($plugin->name());
        $parent = $this->configured($name);

        $args && is_string(key($args)) && $plugin->args() && $args += $this->args($plugin->args());

        !$args && $args = $this->args($plugin->args());

        if (!$parent) {
            return $this->hydrate($plugin, $this->combine(explode(Arg::SERVICE_SEPARATOR, $name), $args));
        }

        if (!$parent instanceof Plugin) {
            return $this->hydrate(
                $plugin, $name === $parent ? $this->make($name, $args) : $this->plugin($this->resolve($parent), $args)
            );
        }

        if ($name === $parent->name()) {
            return $this->hydrate($plugin, $this->make($name, $args));
        }

        return $this->provide($this->merge($parent, $plugin, $name), $args);
    }

    /**
     * @return callable
     */
    protected function provider()
    {
        return $this->provider;
    }

    /**
     * @param $plugin
     * @param array $args
     * @param callable|null $callback
     * @param int $c
     * @return array|callable|Plugin|null|object|Resolvable|string
     */
    protected function resolvable($plugin, array $args = [], callable $callback = null, int $c = 0)
    {
        return !$plugin instanceof Resolvable ? $plugin : (
            $c > Arg::MAX_RECURSION ? Unresolvable::plugin($plugin) :
                $this->resolvable($this->solve($plugin, $args, $callback), $args, $callback, ++$c)
        );
    }

    /**
     * @param $plugin
     * @param array $args
     * @return array|callable|Plugin|null|object|Resolvable|string
     */
    protected function resolve($plugin, array $args = [])
    {
        return $this->resolvable($plugin, $args);
    }

    /**
     * @param $plugin
     * @param array $args
     * @return callable|mixed|null|object
     */
    protected function resolver($plugin, array $args = [])
    {
        return $this->call($this->provider() ?: Arg::SERVICE_RESOLVER, [$plugin, $args]);
    }

    /**
     * @param object $scope
     * @return object
     */
    function scope($scope = null)
    {
        return null !== $scope ? $this->scope = $scope : $this->scope;
    }

    /**
     * @param \Closure $callback
     * @param bool $scoped
     * @return \Closure
     */
    protected function scoped(\Closure $callback, bool $scoped = false)
    {
        return $this->scope ? $this->bind($callback, $this->scope === true ? $this : $this->scope, $scoped) : $callback;
    }

    /**
     * @param $plugin
     * @param array $args
     * @param callable|null $callback
     * @return mixed|callable
     */
    protected function solve($plugin, array $args = [], callable $callback = null)
    {
        return $plugin instanceof Gem ? $this->gem($plugin, $args) : (
            $callback ? $callback($plugin, $args) : $this->resolver($plugin, $args)
        );
    }

    /**
     * @return string
     */
    function serialize()
    {
        return serialize([$this->config, $this->events, $this->provider, $this->scope, $this->services, $this->strict]);
    }

    /**
     * @param string $serialized
     */
    function unserialize($serialized)
    {
        list(
            $this->config, $this->events, $this->provider, $this->scope, $this->services, $this->strict
        ) = unserialize($serialized);
    }

    /**
     * @param array $args
     * @return array
     */
    protected function variadic(array $args)
    {
        return $args && $args[0] instanceof SignalArgs ? $args[0]->args() : $args;
    }

    /**
     * @param array $child
     * @param array $parent
     * @return array
     */
    protected function vars(array $child = [], array $parent = [])
    {
        return $this->arguments($child, $this->args($parent));
    }

    /**
     *
     */
    function __clone()
    {
        is_object($this->config) &&
            $this->config = clone $this->config;

        if (is_object($this->container)) {
            $this->container = clone $this->container;

            if (isset($this->config[Arg::CONTAINER])) {
                $this->config[Arg::CONTAINER] = $this->container;
            }
        }

        if (is_object($this->events)) {
            $this->events = clone $this->events;

            if (isset($this->config[Arg::EVENTS])) {
                $this->config[Arg::EVENTS] = $this->events;
            }
        }

        if (is_object($this->services)) {
            $this->services = clone $this->services;

            if (isset($this->config[Arg::SERVICES])) {
                $this->config[Arg::SERVICES] = $this->services;
            }
        }

        is_object($this->scope) &&
            $this->scope = clone $this->scope;
    }

    /**
     * @param $plugin
     * @param array $args
     * @return array|callable|null|object|string
     */
    function __invoke($plugin, array $args = [])
    {
        return $this->plugin($plugin, $args, $this->provider() ?? function(){});
    }
}
