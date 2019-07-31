<?php


namespace DB;


class Predicate
{
    const OP_EQ = '=';
    const OP_GT = '>';
    const OP_LT = '<';
    const OP_GE = '>=';
    const OP_LE = '<=';
    const OP_LK = 'LIKE';

    private $target;
    private $operation;
    private $value;

    public function __construct(Target $target, $operation, $value)
    {
        $this->target = $target;
        $this->operation = $operation;
        $this->value = $value;
    }

    public function getBindType()
    {
        return $this->target->getType();
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getAsSQL()
    {
        return ' ' . $this->target->getColumnName() . ' ' . $this->operation . ' ? ';
    }
}