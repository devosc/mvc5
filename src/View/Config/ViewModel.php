<?php
/**
 *
 */

namespace Mvc5\View\Config;

use Mvc5\Arg;
use Mvc5\Service\Service;
use Mvc5\Service\Plugin;
use Mvc5\Template\Config\TemplateModel;

trait ViewModel
{
    /**
     *
     */
    use Plugin;
    use TemplateModel;

    /**
     * @param array|string|null $template
     * @param array $vars
     * @param Service|null $service
     */
    function __construct($template = null, array $vars = [], Service $service = null)
    {
        $this->config = (is_array($template) ? $template + $vars : $vars) + array_filter([
            Arg::TEMPLATE_MODEL => is_string($template) ? $template :
                (defined('static::TEMPLATE') ? constant('static::TEMPLATE') : null)
        ]);

        $this->service = $service;
    }

    /**
     * @return Service|null
     */
    function service()
    {
        return $this->service;
    }

    /**
     * @param Service $service
     * @return self|mixed
     */
    function withService(Service $service)
    {
        $new = clone $this;
        $new->service = $service;
        return $new;
    }

    /**
     * @param string $name
     * @param array $args
     * @return mixed
     */
    function __call(string $name, array $args = [])
    {
        return $this->service->call($name, $args);
    }
}
