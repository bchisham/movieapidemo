<?php


namespace entities;


use DB\Performers;
use DB\ReadWrite;

class Performer extends Entity
{
    public function getDBHandle()
    {
        static $db = null;
        if (!isset($db)) {
            $db = new Performers();
        }
    }
}