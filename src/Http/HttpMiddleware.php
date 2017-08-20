<?php
/**
 *
 */

namespace Mvc5\Http;

use Mvc5\Service\Middleware;

class HttpMiddleware
{
    /**
     *
     */
    use Middleware;

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed|Response
     */
    function __invoke($request, $response)
    {
        return $this->call($this->rewind(), [$request, $response]);
    }
}
