<?php


namespace DB;


class PerformerID extends ID
{
    static public function getIdCol()
    {
        return 'performer_id';
    }
}