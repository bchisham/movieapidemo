<?php


namespace DB;


class PredicateSet
{

    const TYPE_AND = 'AND';
    const TYPE_OR = 'OR';

    private $setType;
    /** @var Predicate[] $predicates */
    private $predicates;
    private $bindTypes;
    private $values;

    public function __construct($predicates, $setType = self::TYPE_AND)
    {
        $this->predicates = $predicates;
        $this->bindTypes = '';
        $this->values = [];
        foreach ($this->predicates as $predicate) {
            $this->bindTypes .= $predicate->getBindType();
            $this->values[] = $predicate->getValue();
        }
        $this->setType = $setType;
    }

    public function getBindTypes()
    {
        return $this->bindTypes;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function getAsSQL()
    {
        $result = [];
        foreach ($this->predicates as $predicate) {
            $result[] = $predicate->getAsSQL();
        }
        return implode(' ' . $this->setType . ' ', $result);
    }

}