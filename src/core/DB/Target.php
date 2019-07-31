<?php


namespace DB;


class Target
{
    const COL_TYPE_INT = 'i';
    const COL_TYPE_STRING = 's';
    const COL_TYPE_DATE = 's';

    private $type;
    private $columnName;

    public function __construct($type, $columnName)
    {
        $this->type = $type;
        $this->columnName = $columnName;

    }

    public function getType()
    {
        return $this->type;
    }


    public function getColumnName()
    {
        return $this->columnName;
    }

}