<?php
/**
 *
 */

namespace Mvc5\Config;

trait Config
{
    /**
     *
     */
    use ArrayAccess;

    /**
     * @var array|Configuration
     */
    protected $config = [];

    /**
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->config);
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return is_array($this->config) ? current($this->config) : $this->config->current();
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->config[$name] ?? null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->config[$name]);
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return is_array($this->config) ? key($this->config) : $this->config->key();
    }

    /**
     *
     */
    public function next()
    {
        is_array($this->config) ? next($this->config) : $this->config->next();
    }

    /**
     * @param string $name
     * @return void
     */
    public function remove($name)
    {
        unset($this->config[$name]);
    }

    /**
     *
     */
    public function rewind()
    {
        is_array($this->config) ? reset($this->config) : $this->config->rewind();
    }

    /**
     * @param string $name
     * @param mixed $config
     * @return mixed $config
     */
    public function set($name, $config)
    {
        return $this->config[$name] = $config;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return is_array($this->config) ? null !== $this->key() : $this->config->valid();
    }
}
