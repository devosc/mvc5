<?php
/**
 *
 */

namespace Mvc5\View\Template;

trait Path
{
    /**
     * @var array|\ArrayAccess
     */
    protected $paths = [];

    /**
     * @param string $name
     * @return string|null
     */
    protected function path(string $name)
    {
        return $this->paths[$name] ?? null;
    }
}
