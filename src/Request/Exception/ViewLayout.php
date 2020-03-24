<?php
/**
 *
 */

namespace Mvc5\Request\Exception;

use Throwable;

use const Mvc5\EXCEPTION;

class ViewLayout
    extends \Mvc5\ViewLayout
    implements ExceptionLayout
{
    /**
     *
     */
    use Config\ExceptionLayout;

    /**
     *
     */
    const TEMPLATE = 'exception';

    /**
     * @param Throwable $exception
     * @param string $template
     */
    function __construct(Throwable $exception, string $template = null)
    {
        parent::__construct($template, [EXCEPTION => $exception]);
    }
}
