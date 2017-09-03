<?php
/**
 *
 */

namespace Mvc5\View;

use Mvc5\Service\Service;
use Mvc5\Template\TemplateModel;

interface ViewModel
    extends TemplateModel
{
    /**
     * @return Service|null
     */
    function service();

    /**
     * @param Service $service
     * @return self|mixed
     */
    function withService(Service $service);

    /**
     * @param string $name
     * @param array $args
     * @return mixed
     */
    function __call(string $name, array $args = []);
}
