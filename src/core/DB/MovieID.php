<?php


namespace DB;

class MovieID extends ID
{

    static public function getIdCol()
    {
        return 'movie_id';
    }
}