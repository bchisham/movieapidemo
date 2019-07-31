<?php


namespace entities;


use DB\PredicateSet;
use DB\ReadWrite;

abstract class EntityList implements \ArrayAccess
{
    private $container;
    private $entityType;

    public function __construct($entityType)
    {
        $this->entityType = $entityType;
        $this->container = [];
    }

    public function loadByPredicateSet(PredicateSet $predicateSet)
    {
        /** @var Entity $entity */
        $entity = new $this->entityType();
        $db = $entity->getDBHandle();

        $this->loadByResultSet($db);
    }

    protected function loadByResultSet($results)
    {
        if (empty($results)) {
            return;
        }
        foreach ($results as $result) {
            $this->container[] = $this->createEntity($result);
        }
    }

    protected function createEntity($container)
    {
        /** @var Entity $entity */
        $entity = new $this->entityType();
        $entity->loadByValue($container);
        return $entity;
    }


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