<?php
/**
 *
 */

namespace Mvc5\Response\Send;

use Mvc5\Arg;
use Mvc5\Http\Response;
use Mvc5\Response\Emitter;

trait Send
{
    /**
     * @param Response $response
     */
    protected function body(Response $response)
    {
        $this->emit($response->body());
    }

    /**
     * @param Response $response
     * @return array|mixed
     */
    protected function cookies(Response $response)
    {
        return $response[Arg::COOKIES] ?? [];
    }

    /**
     * @param \Closure|Emitter|string $body
     */
    protected function emit($body)
    {
        $body instanceof Emitter ? $body->emit() : ($body instanceof \Closure ? $body() : print($body));
    }

    /**
     * @param Response $response
     * @return void
     */
    protected function headers(Response $response)
    {
        if (headers_sent()) {
            return;
        }

        foreach($response->headers() as $name => $header) {
            header($name . ': ' . implode(', ', (array) $header));
        }

        foreach($this->cookies($response) as $cookie) {
            setcookie(...array_values($cookie));
        }

        $statusLine = sprintf('HTTP/%s %s %s', $response->version(), $response->status(), $response->reason());

        header($statusLine, true, (int) $response->status());
    }

    /**
     * @param Response $response
     * @return Response
     */
    protected function send(Response $response)
    {
        $this->headers($response);
        $this->body($response);
        return $response;
    }

    /**
     * @param Response $response
     * @return Response
     */
    function __invoke(Response $response)
    {
        return $this->send($response);
    }
}
