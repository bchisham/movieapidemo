<?php


namespace DB;


class ID extends Target
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
        parent::__construct(static::COL_TYPE_INT, static::getIdCol());

    }

    static protected function getIdCol()
    {
        throw new \Exception('Not Implemented ');
    }

    public function getValue()
    {
        return $this->value;
    }

}