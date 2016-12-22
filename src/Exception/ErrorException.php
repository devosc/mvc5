<?php
/**
 *
 */

namespace Mvc5\Exception;

class ErrorException
    extends \ErrorException
    implements Throwable
{
    /**
     *
     */
    use Base;

    /**
     * @param $severity
     * @param $message
     * @param $file
     * @param $line
     * @return bool
     * @codeCoverageIgnore
     */
    static function handler($severity, $message, $file, $line)
    {
        if (!ini_get('display_errors') || in_array($severity, [E_DEPRECATED, E_USER_DEPRECATED])) {
            return false;
        }

        $success = true;
        while(ob_get_level() && $success) {
            $success = ob_end_clean();
        }

        http_response_code(500);

        $exception = new self($message, 0, $severity, $file, $line);

        include __DIR__ . '/../../view/exception.phtml';

        exit(70);
    }
}
