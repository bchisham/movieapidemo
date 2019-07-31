<?php


namespace entities;


use ArrayAccess;
use DB\ReadOnly;
use DB\ReadWrite;

abstract class Entity implements ArrayAccess
{
    private $container;
    private $changed;

    public function __construct()
    {
    }

    public function loadByID($ID)
    {
        $db = $this->getDBHandle();
        if ($db) {
            $db->getByID($ID);
            $this->loadByValue($db->getContainer());
        }
    }

    public function loadByValue($container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function __get($name)
    {
        if (isset($this->container[$name])) {
            return $this->container[$name];
        }
        return null;
    }

    public function __set($name, $value)
    {
        if (isset($this->container[$name]) && $this->container[$name] !== $value) {
            $this->changed[$name] = $value;
        }
        $this->container[$name] = $value;
    }

    /**
     * @return ReadWrite|false
     */
    abstract public function getDBHandle();

    public function offsetGet($offset)
    {
        return $this->container[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->container[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->container);
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }


}