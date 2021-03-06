<?php
/**
 *
 */

namespace Mvc5\Route\Dispatch;

use Mvc5\Http\Error;
use Mvc5\Http\Error\NotFound;
use Mvc5\Http\Request;
use Mvc5\Route\Route;

use function is_string;

use const Mvc5\{ CHILDREN, ERROR, NAME, MATCHED, PARENT, ROUTE, SEPARATOR };

trait Router
{
    /**
     * @var callable
     */
    protected $generator;

    /**
     * @var callable
     */
    protected $match;

    /**
     * @var iterable
     */
    protected iterable $routes;

    /**
     * @param callable $match
     * @param callable $generator
     * @param iterable $routes
     */
    function __construct(callable $match, callable $generator, iterable $routes)
    {
        $this->generator = $generator;
        $this->match = $match;
        $this->routes = $routes;
    }

    /**
     * @param Route $route
     * @param Route|null $parent
     * @return Route
     */
    protected function child(Route $route, Route $parent = null) : Route
    {
        return $route->with(PARENT, $parent);
    }

    /**
     * @param array|Route $route
     * @return Route
     */
    protected function definition($route) : Route
    {
        return $route instanceof Route ? $route : ($this->generator)($route);
    }

    /**
     * @param Request $request
     * @return Request|mixed
     */
    protected function dispatch(Request $request)
    {
        return $this->result($request, $this->traverse($this->routes, $request));
    }

    /**
     * @param Route $route
     * @param int|string $name
     * @return string
     */
    protected function key(Route $route, $name) : string
    {
        return is_string($name) ? $name : (string) $route->name();
    }

    /**
     * @param Route $route
     * @param Request $request
     * @return Request|mixed
     */
    protected function match(Route $route, Request $request)
    {
        return ($this->match)($route, $request);
    }

    /**
     * @param string $name
     * @param string|null $parent
     * @return string
     */
    protected function name(string $name, string $parent = null) : string
    {
        return !$parent ? $name : $parent . SEPARATOR . $name;
    }

    /**
     * @param Request $request
     * @param $result
     * @return Request|mixed
     */
    protected function result(Request $request, $result)
    {
        return $result instanceof Error ? $request->with(ERROR, $result) : $result;
    }

    /**
     * @param Route $route
     * @param Request $request
     * @return Request|mixed
     */
    protected function route(Route $route, Request $request)
    {
        return $this->solve($this->match($route, $request));
    }

    /**
     * @param Request|mixed $request
     * @return Request|mixed
     */
    protected function solve($request)
    {
        return !$request instanceof Request || true === $request[MATCHED] ? $request :
            $this->traverse($request[ROUTE][CHILDREN] ?? [], $request, $request[ROUTE]);
    }

    /**
     * @param Route $route
     * @param Request $request
     * @param int|string $name
     * @return Request|mixed
     */
    protected function step(Route $route, Request $request, $name)
    {
        return $this->route(
            $route->with(NAME, $this->name($this->key($route, $name), $request[NAME])), $request
        );
    }

    /**
     * @param iterable $routes
     * @param Request $request
     * @param Route|null $parent
     * @return Request|mixed
     */
    protected function traverse(iterable $routes, Request $request, Route $parent = null)
    {
        foreach($routes as $name => $route) {
            if ($match = $this->step($this->child($this->definition($route), $parent), $request, $name)) {
                return $match;
            }
        }

        return new NotFound;
    }

    /**
     * @param Request $request
     * @return Request|mixed
     */
    function __invoke(Request $request)
    {
        return $this->dispatch($request);
    }
}
