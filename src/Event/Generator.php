<?php
/**
 *
 */

namespace Mvc5\Event;

use Iterator;
use Mvc5\Signal;
use Throwable;

use function Mvc5\Iterator\{ next, rewind };

trait Generator
{
    /**
     * @param mixed $listener
     * @return callable
     */
    protected abstract function callable($listener) : callable;

    /**
     * @param array|Event|Iterator|object|string $event
     * @param callable $listener
     * @param array $args
     * @param callable|null $callback
     * @return mixed
     * @throws Throwable
     */
    protected function emit($event, callable $listener, array $args = [], callable $callback = null)
    {
        return $event instanceof Event ? $event($listener, $args, $callback) : Signal::emit($listener, $args, $callback);
    }

    /**
     * @param array|Event|Iterator|object|string $event
     * @param array $args
     * @param callable|null $callback
     * @return mixed
     * @throws Throwable
     */
    protected function generate($event, array $args = [], callable $callback = null)
    {
        return $this->iterate(null, $event, rewind($this->iterator($event, $args)), $args, $callback);
    }

    /**
     * @param mixed $result
     * @param array|Event|Iterator|object|string $event
     * @param Iterator $iterator
     * @param array $args
     * @param callable|null $callback
     * @return mixed
     * @throws Throwable
     */
    protected function iterate($result, $event, Iterator $iterator, array $args, callable $callback = null)
    {
        return $this->stopped($event, $iterator) ? $result : $this->iterate(
            $this->emit($event, $this->callable($iterator->current()), $args, $callback), $event, next($iterator), $args, $callback
        );
    }

    /**
     * @param Event|object|string $event
     * @param array $args
     * @return Iterator
     * @throws Throwable
     */
    abstract protected function iterator($event, array $args = []) : Iterator;

    /**
     * @param Event|mixed $event
     * @param Iterator $iterator
     * @return bool
     */
    protected function stopped($event, Iterator $iterator) : bool
    {
        return ($event instanceof Event && $event->stopped()) || !$iterator->valid();
    }
}
