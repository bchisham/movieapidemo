<?php


namespace Validator;


abstract class Validator
{
    abstract public function validate();

    static public function isAlnum($rawField)
    {
        return preg_match('#^[a-zA-Z0-9]+$#', $rawField) == 1;
    }

    static public function maxLength($rawField, $max)
    {
        return strlen($rawField) <= $max;
    }

    static public function isInteger($rawField)
    {
        return preg_match('#^-?\d+$#', $rawField);
    }

    static public function isIsoDate($rawField)
    {
        return preg_match('^\d{4,4}-\d{2,2}-\d{2,2}$', $rawField) == 1;
    }
}