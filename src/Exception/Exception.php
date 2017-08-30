<?php
/**
 *
 */

namespace Mvc5\Exception;

use Mvc5\Arg;

trait Exception
{
    /**
     * @param \Throwable $exception
     * @param int $offset
     * @return \Throwable
     */
    protected static function backtrace(\Throwable $exception, int $offset = 2)
    {
        return static::origin(
            $exception, $offset, $offset && ($origin = $exception->getTrace()[$offset]) ? $origin : []);
    }

    /**
     * @param string $exception
     * @param array $args
     * @param int $offset
     * @return \Throwable
     */
    protected static function create(string $exception, array $args = [], int $offset = 2)
    {
        return static::backtrace(static::instance($exception, $args), $offset);
    }

    /**
     * @param string $exception
     * @param array $args
     * @return \Throwable
     */
    protected static function instance(string $exception, array $args = []) : \Throwable
    {
        return new $exception(...$args);
    }

    /**
     * @param \Throwable $exception
     * @param int $offset
     * @param array $origin
     * @return \Throwable
     */
    protected static function origin(\Throwable $exception, int $offset = 1, array $origin = [])
    {
        if (!$exception instanceof \Error) {
            if (!empty($origin[Arg::FILE])) {
                $exception->file = $origin[Arg::FILE];
                $exception->line = $origin[Arg::LINE];
            }
            $offset && $exception->offset = $offset;
            return $exception;
        }

        return (new class extends \Error {
            function __invoke(\Throwable $error, int $offset, array $origin) {
                if (!empty($origin[Arg::FILE])) {
                    $error->file = $origin[Arg::FILE];
                    $error->line = $origin[Arg::LINE];
                }
                $offset && $error->offset = $offset;
                return $error;
            }
        })($exception, $offset, $origin);
    }

    /**
     * @param $exception
     * @return mixed
     * @throws \Throwable
     */
    static function raise(\Throwable $exception)
    {
        throw $exception;
    }
}
